<?php
require_once( PACKAGER_PKG_PATH."PackagerBase.php" );
require_once( PACKAGER_PKG_PATH."Packager.php" );

/**
 * Packager 
 * 
 * @uses BitBase
 */
class PackagerVersions extends PackagerBase {
	// package id
	var $mPackagerId;

	/**
	 * Initiate class
	 * 
	 * @return void
	 */
	function PackagerVersions( $pPackagerId = NULL ) {
		PackagerBase::PackagerBase();
		$this->mPackagerId = $pPackagerId;
	}

	/**
	 * load a package
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( @BitBase::verifyId( $this->mPackagerId )) {
			$selectSql = $joinSql = $orderSql = $whereSql = "";
			$bindVars = $ret = array();

			$whereSql = "WHERE pkgv.`packager_id`=?";
			$bindVars[] = $this->mPackagerId;

			$query = "
				SELECT pkgv.*, pkgp.*, pkgi.`download_date`, pkgi.`install_date`
				FROM `".BIT_DB_PREFIX."packager_versions` pkgv
					INNER JOIN `".BIT_DB_PREFIX."packager_packages` pkgp ON( pkgv.`package`=pkgp.`package` )
					LEFT OUTER JOIN `".BIT_DB_PREFIX."packager_installed` pkgi ON( pkgv.`packager_id`=pkgi.`packager_id` )
				$whereSql";
			if( $aux = $this->mDb->getRow( $query, $bindVars )) {
				$this->mInfo                        = $aux;
				$this->mInfo['filename']            = $this->getPackageFilename( $this->mInfo );
				$this->mInfo['mime_type']           = 'application/zip';
				$this->mInfo['last_modified']       = $this->mInfo['release_date'];
				$this->mInfo['source_file']         = $this->getPackageFilepath( $this->mInfo );
				$this->mInfo['package_display_url'] = Packager::getDisplayUrl( $aux );
				$this->mInfo['display_url']         = $this->getDisplayUrl( $aux );
				$this->mInfo['package_url']         = $this->getPackageUrl( $aux );
				$this->mInfo['changelog']           = $this->getChangelog();
				$this->mInfo['dependencies']        = $this->getDependencies();
			}
		}

		return( count( $this->mInfo ) );
	}

	/**
	 * add an entry about an installed package to the database
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function store( $pParamHash ) {
		if( $this->verify( $pParamHash )) {
			$table = BIT_DB_PREFIX."packager_versions";
			if( $this->isValid() ) {
				$this->mDb->associateUpdate( $table, $pParamHash['store'], array( 'packager_id' => $this->mPackagerId ));
			} else {
				$this->mPackagerId = $pParamHash['store']['packager_id'] = $this->mDb->GenID( 'packager_licenses_id_seq' );
				$this->mDb->associateInsert( $table, $pParamHash['store'] );
			}

			if( !empty( $pParamHash['dependencies'] )) {
				if( !$this->storeDependencies( $pParamHash['dependencies'] )) {
					$this->mErrors['dependencies'] = tra( 'There was a problem storing the dependencies.' );
				}
			}

			if( !empty( $pParamHash['changelog'] )) {
				if( !$this->storeChangelog( $pParamHash['changelog'] )) {
					$this->mErrors['changelog'] = tra( 'There was a problem storing the changelog.' );
				}
			}

			$this->postStore();
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * verify 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function verify( &$pParamHash ) {
		global $gBitSystem, $gBitUser;

		// we only allow very few changes during an update
		if( $this->isValid() ) {
			$pParamHash['store']['is_security_release'] = ( !empty( $pParamHash['is_security_release'] ) ? 'y' : 'n' );
		} else {
			// check package entry against database
			if( $this->mDb->getOne( "SELECT `package` FROM `".BIT_DB_PREFIX."packager_packages` WHERE `package`=?", array( $pParamHash['package'] ))) {
				$pParamHash['store']['package'] = $pParamHash['package'];
			} else {
				$this->mErrors['package'] = tra( 'The package you provided does not seem to exist in our database. Please create an appropriate entry first.' );
			}

			$pParamHash['store']['release_date']        = $gBitSystem->mServerTimestamp->getUTCTime();
			$pParamHash['store']['is_security_release'] = ( !empty( $pParamHash['is_security_release'] ) ? 'y' : 'n' );

			// since this is not an update we require an upload
			$this->storeUpload( $pParamHash );
		}

		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * this will verify that the uploaded file is compatible with bitweaver and will create an archive named in a standard manner.
	 * it will return the path to the new archive in $pParamHash['archive']
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function storeUpload( &$pParamHash ) {
		// we now extract the new version of the package and perform some simple checks to see if everything is in order.
		if( !empty( $pParamHash['upload'] ) && $extracted = liberty_process_archive( $pParamHash['upload'] )) {
			// check to see if there is a dir named the same as the package
			if( is_dir( $extracted."/".$pParamHash['store']['package'] )) {
				// check for a set of files
				$fileChecks = array( 'bit_setup_inc.php', 'admin/schema_inc.php' );
				foreach( $fileChecks as $file ) {
					if( !is_file( $extracted."/".$pParamHash['store']['package']."/".$file )) {
						$this->mErrors['missing_file'] = tra( 'The archive you uploaded is missing at least one required file.' );
					}
				}

				if( empty( $this->mErrors )) {
					$schemafile = $extracted."/".$pParamHash['store']['package']."/admin/schema_inc.php";
					if( $new = $this->getVersionFromFile( $schemafile )) {
						$pParamHash['store'] = array_merge( $pParamHash['store'], $new );
						// we know that version and package are set. now we need to make sure the version provided is higher than the latest one in the database
						$latest = $this->getLatestVersion( $pParamHash['store']['package'] );
						if( !empty( $latest ) && $this->versionCompare( $new, $latest ) !== 1 ) {
							$this->mErrors['version'] = tra( 'The version number you provided is lower or equal to the one provided in the database. You can not upload older versions of any given package.' );
						} else {
							// now that we're sure that everyting is in order, we can start removig stuff.
							$this->unlinkDebris( $extracted."/".$pParamHash['store']['package'] );
						}
					} else {
						$this->mErrors['version'] = tra( 'You did not provide a valid version using registerPackageVersion() in your schema_inc.php file.' );
					}
				}
			} else {
				$this->mErrors['package_dir'] = tra( "The archive you uploaded does not contain a directory with the same name as your package" ).": ".$pParamHash['store']['package'];
			}
		} else {
			$this->mErrors['move'] = tra( 'I could not extract the file you uploaded. Please make sure the archive is valid. Also please use a common archive format such as .zip, .rar or .tar.gz.' );
		}

		// if the package has passed verification, we create a new standard zip archive
		if( empty( $this->mErrors )) {
			// get current working dir
			$cwd = getcwd();
			// change to new working dir
			chdir( $extracted );
			// create new zip archive
			$archive = $pParamHash['store']['package'].".zip";
			$shellResult = shell_exec( "zip -r \"$archive\" \"{$pParamHash['store']['package']}\"" );
			// change back to original working dir
			chdir( $cwd );

			// we can now go on to do normal stuff again.
			if( !empty( $shellResult ) && is_file( $extracted.'/'.$archive )) {
				$pParamHash['file']['extracted']  = $extracted;
				$pParamHash['file']['archive']    = $extracted."/".$archive;
				$pParamHash['store']['file_size'] = filesize( $pParamHash['file']['archive'] );
				$pParamHash['store']['md5_hash']  = md5_file( $pParamHash['file']['archive'] );

				// we can use the package icon to add a bit of colour to the package page
				$extensions = array( 'jpg', 'gif', 'png' );
				foreach( $extensions as $ext ) {
					$icon = $extracted."/".$pParamHash['store']['package']."/icons/pkg_".$pParamHash['store']['package'].".".$ext;
					if( is_file( $icon )) {
						$pParamHash['file']['icon'] = $icon;
					}
				}

				// move the archive accross and remove the extracted files
				if( !rename( $pParamHash['file']['archive'], $this->getPackageFilepath( $pParamHash['store'] ))) {
					$this->mErrors['move'] = tra( 'I could not move the uplaoaded file to its destination.' );
				} else {
					if( !empty( $pParamHash['file']['icon'] )) {
						rename( $pParamHash['file']['icon'], $this->getStoragePath( "packages" ).$pParamHash['store']['package']."-icon.png" );
					}
				}
				unlink_r( $pParamHash['file']['extracted'] );
			} else {
				$this->mErrors['archive'] = tra( 'I could not create an archive from the file you uploaded.' );
			}
		}

		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * recursively remove any unwanted debris from the uploaded package
	 * 
	 * @param array $pDir 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function unlinkDebris( $pDir ) {
		if( is_dir( $pDir ) && $handle = opendir( $pDir )) {
			// fix dir if no trailing slash
			if( !preg_match( "!/$!", $pDir )) {
				$pDir .= '/';
			}

			while( FALSE !== ( $file = readdir( $handle ))) {
				if( $file != '.' && $file != '..' ) {
					if( preg_match( "/^CVS$/", $file )) {
						unlink_r( $pDir.$file );
					} elseif( preg_match( "!^\..*\.swp$!", $file )) {
						unlink( $pDir.$file );
					} elseif( is_dir( $file )) {
						$this->unlinkDebris( $pDir.$file );
					}
				}
			}
		}
	}

	/**
	 * get a list of available packages
	 * 
	 * @param array $pListHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getList( &$pListHash ) {
		global $gBitSystem;

		$ret = $bindVars = array();
		$selectSql = $joinSql = $orderSql = $whereSql = '';

		// disable pagination for now
		$pListHash['max_records'] = -1;
		if( empty( $pListHash['sort_mode'] )) {
			$pListHash['sort_mode'] = 'version_desc';
		}

		if( !empty( $pListHash['package'] )) {
			$whereSql .= empty( $whereSql ) ? ' WHERE ' : ' AND ';
			$whereSql .= ' pkgv.`package`=? ';
			$bindVars[] = $pListHash['package'];
		}

		LibertyContent::prepGetList( $pListHash );

		$query = "
			SELECT pkgv.*, pkgp.*, pkgi.`download_date`, pkgi.`install_date`
			FROM `".BIT_DB_PREFIX."packager_versions` pkgv
				INNER JOIN `".BIT_DB_PREFIX."packager_packages` pkgp ON( pkgv.`package`=pkgp.`package` )
				LEFT OUTER JOIN `".BIT_DB_PREFIX."packager_installed` pkgi ON( pkgv.`packager_id`=pkgi.`packager_id` )
			$whereSql ORDER BY ".$this->mDb->convertSortmode( $pListHash['sort_mode'] );
		$result = $this->mDb->query( $query, $bindVars );

		while( $aux = $result->fetchRow() ) {
			$aux['display_url']       = $this->getDisplayUrl( $aux );
			$aux['package_url']       = $this->getPackageUrl( $aux );
			$ret[$aux['packager_id']] = $aux;
		}

		$pListHash['cant'] = count( $ret );
		LibertyContent::postGetList( $pListHash );
		return $ret;
	}

	/**
	 * getDisplayUrl 
	 * 
	 * @param array $pMixed 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getDisplayUrl( $pMixed = NULL ) {
		global $gBitSystem;

		$ret = FALSE;

		if( empty( $pMixed['packager_id'] ) && $this->isValid() ) {
			$pMixed['packager_id'] = $this->mPackagerId;
		}

		if( !empty( $pMixed['packager_id'] )) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ) {
				$rewrite_tag = $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ? 'view/' : '';
				$ret = PACKAGER_PKG_URL.$rewrite_tag."version/".$pMixed['packager_id'];
			} else {
				$ret = PACKAGER_PKG_URL.'view_version.php?packager_id='.$pMixed['packager_id'];
			}
		}
		return $ret;
	}

	// ================================== Changelogs ==================================
	/**
	 * getChangelog 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getChangelog() {
		if( $this->isValid() ) {
			$query = "SELECT pkgc.* FROM `".BIT_DB_PREFIX."packager_changelogs` pkgc WHERE pkgc.`packager_id`=? ORDER BY ".$this->mDb->convertSortmode( 'flag_desc' ).", ".$this->mDb->convertSortmode( 'log_date_desc' );
			return( $this->mDb->getAll( $query, array( $this->mPackagerId )));
		}
	}

	/**
	 * storeChangelog 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function storeChangelog( $pString ) {
		if( $this->isValid() && $changeLogs = $this->parseChangelog( $pString )) {
			$table = BIT_DB_PREFIX."packager_changelogs";
			// first we remove all old entries for this version
			$this->mDb->query( "DELETE FROM `$table` WHERE `packager_id`=?", array( $this->mPackagerId ));
			foreach( $changeLogs as $log ) {
				$log['packager_id'] = $this->mPackagerId;
				$this->mDb->associateInsert( $table, $log );
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * parseChangelog 
	 * 
	 * @param array $pString 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function parseChangelog( $pString ) {
		$ret = array();
		if( !empty( $pString )) {
			$lines = explode( "\n", $pString );
			foreach( $lines as $line ) {
				// check for our log where format is: <flag> YYYY-MM-DD log message - date is optional
				if( preg_match( "/^([!+-])\s+((\d{4})-(\d{2})-(\d{2})\s+)?(.*)$/", $line, $matches )) {
					$ret[] = array(
						'flag' => $matches[1],
						'log_date' => ( !empty( $matches[2] ) ? mktime( 0, 0, 0, $matches[4], $matches[5], $matches[3] ) : mktime() ),
						'change_log' => trim( $matches[6] ),
					);
				}
			}
		}
		return $ret;
	}


	// ================================== Dependencies ==================================
	/**
	 * getDependencies 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getDependencies() {
		if( $this->isValid() ) {
			$query = "SELECT pkgr.* FROM `".BIT_DB_PREFIX."packager_dependencies` pkgr WHERE pkgr.`packager_id`=? ORDER BY ".$this->mDb->convertSortmode( 'dependency_asc' );
			return( $this->mDb->getAll( $query, array( $this->mPackagerId )));
		}
	}

	/**
	 * storeDependencies 
	 * 
	 * @param mixed $pParamHash
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 * @TODO
	 */
	function storeDependencies( $pString ) {
		if( $this->isValid() && $dependencies = $this->parseDependencies( $pString )) {
			$table = BIT_DB_PREFIX."packager_dependencies";
			// first we remove all old entries for this version
			$this->mDb->query( "DELETE FROM `$table` WHERE `packager_id`=?", array( $this->mPackagerId ));
			foreach( $dependencies as $req ) {
				$req['packager_id'] = $this->mPackagerId;
				$this->mDb->associateInsert( $table, $req );
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * verifyDependencies 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function parseDependencies( $pString ) {
		$ret = FALSE;
		if( !empty( $pString ) && is_string( $pString )) {
			$dependencies = explode( "\n", $pString );
			foreach( $dependencies as $dependency ) {
				$hash = explode( ' ', preg_replace( "/\s+/", " ", trim( $dependency )));
				if( !empty( $hash[0] ) && preg_match( "/^".PACKAGER_VERSION_REGEX."$/", $hash[1] )) {
					if( !empty( $hash[2] ) && preg_match( "/^".PACKAGER_VERSION_REGEX."$/", $hash[2] ) && version_compare( $hash[1], $hash[2] ) == 1 ) {
						// max_version is smaller than min_version
					} else {
						$ret[] = array(
							'dependency'  => trim( strtolower( $hash[0] )),
							'min_version' => $hash[1],
							'max_version' => ( !empty( $hash[2] ) && preg_match( "/^".PACKAGER_VERSION_REGEX."$/", $hash[2] )) ? $hash[2] : NULL,
						);
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * isValid 
	 * 
	 * @access public
	 * @return TRUE if $this->mPackagerId is set, FALSE on failure
	 */
	function isValid( $pLoad = FALSE ) {
		if( @BitBase::verifyId( $this->mPackagerId )) {
			// make sure we are up to date and fully loaded
			if( $pLoad && ( empty( $this->mInfo['packager_id'] ) || $this->mPackagerId != $this->mInfo['packager_id'] )) {
				$this->load();
			}
			return TRUE;
		}
	}

	/**
	 * add a download hit when someone downloads a package 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function addHit() {
		if( $this->isServer() && $this->isValid() ) {
			$query = "UPDATE `".BIT_DB_PREFIX."packager_versions` SET `downloads`=`downloads`+1 WHERE `packager_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mPackagerId ) );
			$affected_rows = $this->mDb->Affected_Rows();
			if( !$affected_rows ) {
				$query = "UPDATE `".BIT_DB_PREFIX."packager_versions` SET `downloads`=? WHERE `packager_id` = ?";
				$result = $this->mDb->query( $query, array( 1, $this->mContentId ) );
			}
		}
		return TRUE;
	}

	function expunge() {
		if( $this->isValid( TRUE )) {
			$this->mDb->StartTrans();
			$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."packager_changelogs` WHERE `packager_id`=?", array( $this->mPackagerId ));
			$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."packager_dependencies` WHERE `packager_id`=?", array( $this->mPackagerId ));
			if( $this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."packager_versions` WHERE `packager_id` = ?", array( $this->mPackagerId ))) {
				$this->mDb->CompleteTrans();
				@unlink( $this->getPackageFilepath() );
			} else {
				$this->mDb->RollbackTrans();
				$this->mErrors['delete'] = tra( 'The data could not be removed from the database.' );
			}
		}
		$this->postStore();
		return( count( $this->mErrors ) == 0 );
	}
}
?>
