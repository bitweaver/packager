<?php
define( 'PACKAGER_VERSION_REGEX', '(\d+\.\d+\.\d+)[\.-]?(dev|alpha|beta|pl)?' );

/**
 * Packager
 *
 * @uses BitBase
 */
class PackagerBase extends BitBase {
	// set of tables used in packager. these are needed for xml data transfer
	var $mTables = array();

	// set the host where the packages are uploaded to
	var $mHost = 'www.bitweaver.org';

	// special settings transferred from remote host
	var $mConfig = array();

	/**
	 * Initiate class
	 *
	 * @return void
	 */
	function PackagerBase() {
		global $gBitSystem;

		BitBase::BitBase();

		// table order is important due to constraints
		$this->mTables = array(
			'packager_packages',
			'packager_versions',
			'packager_licenses',
			'packager_types',
			'packager_changelogs',
			'packager_dependencies',
		);

		$this->mHost = $gBitSystem->getConfig( 'packager_rem_host', 'www.bitweaver.org' );
	}


	// ================================== DB <--> XML ==================================
	/**
	 * tableToXml
	 *
	 * @param array $pTable
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function tableToXml( $pTable, $pQuery = NULL, $pBindvars = array() ) {
		if( !empty( $pTable )) {
			require_once( PACKAGER_PKG_PATH."libs/class.xml.php" );
			$this->mXml = new XMLFILE( "1.0", "UTF-8" );

			if( empty( $pQuery )) {
				$pQuery = "SELECT * FROM `".BIT_DB_PREFIX."$pTable`";
			}
			$rs = $this->mDb->query( $pQuery, $pBindvars );

			$this->mXml->create_root();
			$this->mXml->roottag->name = "ROOT";

			while( !$rs->EOF ) {
				$this->mXml->roottag->add_subtag( "ROW", array() );
				$tag = &$this->mXml->roottag->curtag;

				for( $i = 0; $i < $rs->_numOfFields ; $i++ ) {
					list( $field, $value ) = each( $rs->fields );
					$tag->add_subtag( $field );
					$tag->curtag->cdata = $value;
				}

				$rs->moveNext();
			}

			$file = $this->getXmlFilepath( $pTable );
			$xml_file = fopen( $file, "w" );
			$this->mXml->write_file_handle( $xml_file );
		}
	}

	/**
	 * xmlToDatabase
	 *
	 * @param array $pXmlFile
	 * @param array $pTable
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function xmlToDatabase( $pTable ) {
		if( !empty( $pTable )) {
			require_once( PACKAGER_PKG_PATH."libs/class.xml.php" );
			$this->mXml = new XMLFILE( "1.0", "UTF-8" );

			$file = $this->getXmlFilepath( $pTable );
			$xml_file = fopen( $file, "r" );
			$this->mXml->read_file_handle( $xml_file );

			$numRows = $this->mXml->roottag->num_subtags();

			for( $i = 0; $i < $numRows; $i++ ) {
				$arrFields = null;
				$arrValues = null;

				$row = $this->mXml->roottag->tags[$i];
				$numFields = $row->num_subtags();

				for( $ii = 0; $ii < $numFields; $ii++ ) {
					$field = $row->tags[$ii];
					$storeHash[strtolower( $field->name )] = !empty( $field->cdata ) ? $field->cdata : NULL;
				}

				if( !$result = $this->mDb->associateInsert( BIT_DB_PREFIX.$pTable, $storeHash )) {
					$this->mErrors[$pTable] = 'Failed to insert data into '.$pTable;
				}
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * getXmlFilepath
	 *
	 * @param array $pTable
	 * @access public
	 * @return path to xml file
	 */
	function getXmlFilepath( $pTable ) {
		return $this->getStoragePath( "xml" ).$pTable.".xml";
	}

	/**
	 * get the url to the local xml file
	 * 
	 * @param array $pTable 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getXmlUrl( $pTable, $pRemote = FALSE ) {
		global $gBitSystem;
		if( $pRemote ) {
			if( $gBitSystem->isFeatureActive( 'packager_rem_pretty_urls' )) {
				return "http://".$this->mHost.'/packager/xml/'.$pTable;
			} else {
				return "http://".$this->mHost.'/packager/download_xml.php?table='.$pTable;
			}
		} else {
			return $this->getStorageUrl( 'xml' ).$pTable.'.xml';
		}
	}

	/**
	 * packagerDbToXml
	 *
	 * @access public
	 * @return void
	 */
	function packagerDbToXml() {
		foreach( $this->mTables as $table ) {
			$this->tableToXml( $table );
		}
		$this->tableToXml( 'kernel_config', "SELECT * FROM `".BIT_DB_PREFIX."kernel_config` WHERE `package`=? AND `config_name` LIKE ?", array( 'packager', 'packager_rem_%' ));
	}

	/**
	 * packagerXmlToDb 
	 * 
	 * @access public
	 * @return void
	 */
	function packagerXmlToDb() {
		// we need to reverse tables order. they are in the right order for insertion
		foreach( array_reverse( $this->mTables ) as $table ) {
			// before we can insert all the juicy data, we need to remove all the old data first
			$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."$table`" );
		}

		foreach( $this->mTables as $table ) {
			// before we can insert all the juicy data, we need to remove all the old data first
			$this->xmlToDatabase( $table );
		}
		// now we remove any outdated remote packager settings as set by packager_rem_% settings
		$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."kernel_config` WHERE `config_name` LIKE ?", array( 'packager_rem_%' ));
		$this->xmlToDatabase( 'kernel_config' );
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * This will fetch all XML files from the host
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function fetchRemoteXmlFiles() {
		if( !$this->isServer() ) {
			// kernel_config is not in the list of packager tables
			$tables   = $this->mTables;
			$tables[] = 'kernel_config';
			foreach( $tables as $table ) {
				$xmlFile = $this->getXmlFilepath( $table );
				// make sure we only update when local files are older than a day
				if(( !is_file( $xmlFile ) || is_file( $xmlFile ) && ( mktime() - filemtime( $xmlFile ) > 86400 ))) {
					$xml = bit_http_request( $this->getXmlUrl( $table, TRUE ));
					if( !preg_match( "/404 Not Found/", $xml ) && !preg_match( "/DOCTYPE html PUBLIC/", $xml )) {
						if( $handle = fopen( $xmlFile, 'w' )) {
							fwrite( $handle, $xml );
							fclose( $handle );
						} else {
							$this->mErrors['write'] = tra( 'There was a problem trying to write the files to your server.' );
						}
					} else {
						$this->mErrors['download'] = tra( 'There was a problem trying to download the files from the server.' );
					}
				} else {
					$this->mErrors['download'] = tra( 'The files are up to date and were not updated again.' );
				}
			}
		} else {
			$this->mErrors['download'] = tra( 'You can not run the database upgrader on a package server.' );
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * get the most recent version of a given package
	 * 
	 * @param array $pPackage 
	 * @access public
	 * @return latest version number of given package
	 */
	function getLatestVersion( $pPackage ) {
		if( !empty( $pPackage )) {
			require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );
			$query = "SELECT * FROM `".BIT_DB_PREFIX."packager_versions` WHERE `package`=? ORDER BY ".$this->mDb->convertSortmode( 'release_date_desc' );
			if( $ret = $this->mDb->getRow( $query, array( $pPackage ))) {
				$ret['display_url'] = PackagerVersions::getDisplayUrlFromHash( $ret );
				$ret['package_url'] = PackagerVersions::getPackageUrl( $ret );
			}
			return $ret;
		}
	}



	// ================================== File related ==================================
	function getStorageUrl( $pSubDir = NULL ) {
		return LibertyAttachable::getStorageUrl( $pSubDir, NULL, PACKAGER_PKG_NAME );
	}

	function getStoragePath( $pSubDir = NULL ) {
		return LibertyAttachable::getStoragePath( $pSubDir, NULL, PACKAGER_PKG_NAME );
	}

	/**
	 * this will confirm that the file is present and that the file matches the md5 hash created when the file was compressed
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function isDownloaded( $pParamHash = NULL ) {
		$ret = FALSE;
		if( empty( $pParamHash ) && $this->isValid() ) {
			$pParamHash = $this->mInfo;
		}

		if( !empty( $pParamHash ) && is_array( $pParamHash )) {
			$file = $this->getPackageFilepath( $pParamHash );
			if( !empty( $file ) && is_file( $file ) && md5_file( $file ) == $pParamHash['md5_hash'] ) {
				$ret = TRUE;
			}
		}
		return $ret;
	}

	function getPackageFilepath( $pParamHash = NULL ) {
		return $this->getStoragePath( 'packages' ).$this->getPackageFilename( $pParamHash );
	}

	function getPackageFilename( $pParamHash = NULL ) {
		if( empty( $pParamHash ) && $this->isValid() ) {
			$pParamHash = $this->mInfo;
		}

		if( !empty( $pParamHash['package'] ) && !empty( $pParamHash['version'] )) {
			return $pParamHash['package'].'-'.$pParamHash['version'].( !empty( $pParamHash['status'] ) ? '.'.$pParamHash['status'] : '' ).'.zip';
		}
		return FALSE;
	}

	/**
	 * get the url to download the package from the remote server
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getPackageUrl( $pParamHash = NULL ) {
		if( empty( $pParamHash ) && $this->isValid() ) {
			$pParamHash = $this->mInfo;
		}

		if( !empty( $pParamHash['packager_id'] )) {
			global $gBitSystem;
			if( $gBitSystem->isFeatureActive( 'packager_rem_pretty_urls' )) {
				return "http://".$this->mHost.PACKAGER_PKG_URL."download/".$pParamHash['packager_id'];
			} else {
				return "http://".$this->mHost.PACKAGER_PKG_URL."download.php?packager_id=".$pParamHash['packager_id'];
			}
		}
	}

	/**
	 * use a regular expression to extract the package version from a given file 
	 * 
	 * @param array $pFile 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getVersionFromFile( $pFile ) {
		$ret = array();
		if( is_file( $pFile )) {
			if( $handle = fopen( $pFile, 'r' )) {
				$content = fread( $handle, filesize( $pFile ));
				fclose( $handle );
				if( preg_match( "/.*registerPackageVersion.*".PACKAGER_VERSION_REGEX.".*/", $content, $matches )) {
					$ret['version'] = $matches[1];
					$ret['status']  = !empty( $matches[2] ) ? $matches[2] : NULL;
				}
			}
		}
		return $ret;
	}

	/**
	 * get the version as stored in the database
	 * 
	 * @param array $pPackage 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getInstalledVersion( $pPackage ) {
		global $gBitSystem;
		$ret = FALSE;
		if( !empty( $gBitSystem->mPackages[$pPackage] )) {
			if( !empty( $gBitSystem->mPackages[$pPackage]['installed'] )) {
				$version = $gBitSystem->getVersion( $pPackage, '0.0.0' );
				if( preg_match( "/".PACKAGER_VERSION_REGEX."/", $version, $matches )) {
					$ret['version'] = $matches[1];
					$ret['status']  = !empty( $matches[2] ) ? $matches[2] : NULL;
				}
			}
		}
		return $ret;
	}

	/**
	 * versionCompare 
	 * 
	 * @param mixed $pOne array or string containing version and status information
	 * @param mixed $pTwo same as $pOne
	 * @access public
	 * @return comparative result of version_compare()
	 */
	function versionCompare( $pOne, $pTwo ) {
		if( is_array( $pOne ) && !empty( $pOne['version'] ) && !isset( $pOne['status'] )) {
			$pOne = $pOne['version'].$pOne['status'];
		}

		if( empty( $pOne )) {
			$pOne = '0.0.0';
		}

		if( is_array( $pTwo ) && !empty( $pTwo['version'] ) && !isset( $pTwo['status'] )) {
			$pTwo = $pTwo['version'].$pTwo['status'];
		}

		if( empty( $pTwo )) {
			$pTwo = '0.0.0';
		}

		return( version_compare( $pOne, $pTwo ));
	}



	// ================================== Odds and Ends ==================================
	/**
	 * check to see if the user who is viewing / editing a given item is the owner of the item.
	 * editing stuff is only possible on the server, so this will also perform an isServer() check
	 *
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function isOwner() {
		global $gBitUser;
		if( $this->isServer() && $this->isValid() ) {
			return( $gBitUser->isAdmin() || $this->mInfo['user_id'] == $gBitUser->mUserId );
		}
	}

	/**
	 * isServer 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function isServer() {
		return( preg_match( '!^'.preg_quote( $this->mHost, '!' ).'$!i', $_SERVER['SERVER_NAME'] ));
	}

	/**
	 * postStore 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function postStore() {
		if( $this->isServer() ) {
			$this->packagerDbToXml();
		}
	}

	function verifyServer() {
		global $gBitSystem;
		if( !$this->isServer() ) {
			$gBitSystem->fatalError( tra( 'You can only view this page on a host server.' ));
		}
	}
}

/**
 * custom sorting for schema files that they are included in the correct order when performing upgrades
 * 
 * @param array $a 
 * @param array $b 
 * @access public
 */
function schemafile_version_compare( $a, $b ) {
	preg_match( "/".PACKAGER_VERSION_REGEX."/", $a, $am );
	preg_match( "/".PACKAGER_VERSION_REGEX."/", $b, $bm );
	$av = $am[1].( !empty( $am[2] ) ? $am[2] : '' );
	$bv = $bm[1].( !empty( $bm[2] ) ? $bm[2] : '' );
	return PackagerBase::versionCompare( $av, $bv );
}

function array_version_compare( $a, $b ) {
	return PackagerBase::versionCompare( $a, $b );
}
?>
