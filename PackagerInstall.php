<?php
require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );

/**
 * Packager 
 * 
 * @uses BitBase
 */
class PackagerInstall extends PackagerVersions {
	// package id
	var $mPackagerId;

	/**
	 * Initiate class
	 * 
	 * @return void
	 */
	function PackagerVersions( $pPackagerId = NULL ) {
		PackagerVersions::PackagerVersions();
		$this->mPackagerId = $pPackagerId;
	}

	/**
	 * storeLog 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 * TODO: not sure if using serialize is the best way to go here, but makes sense for now. easy retrieval and should be easy to apply to the results table
	 */
	function storeDownloadLog( $pLogHash ) {
		global $gBitSystem;
		if( $this->isValid() ) {
			$storeHash = array(
				'packager_id' => $this->mPackagerId,
				'download_date' => $gBitSystem->mServerTimestamp->getUTCTime(),
				'download_message' => serialize( $this->mErrors ),
			);
			return $this->storeLog( $storeHash );
		}
	}

	function storeInstallLog( $pLogHash ) {
		global $gBitSystem;
		if( $this->isValid() ) {
			$storeHash = array(
				'packager_id' => $this->mPackagerId,
				'download_date' => $gBitSystem->mServerTimestamp->getUTCTime(),
				'download_message' => serialize( $pLogHash ),
			);
			return $this->storeLog( $storeHash );
		}
	}

	function storeLog( $pStoreHash ) {
		if( $this->isValid() ) {
			$table = BIT_DB_PREFIX.'packager_installed';
			if( $this->mDb->getOne( "SELECT `packager_id` FROM `$table` WHERE `packager_id`=?", array( $this->mPackagerId ))) {
				$this->mDb->associateUpdate( $table, $storeHash, array( 'packager_id' => $this->mPackagerId ));
			} else {
				$this->mDb->associateInsert( $table, $storeHash );
			}
		}
		return TRUE;
	}

	function getInstallPath( $pPackage = NULL ) {
		global $gBitSystem;
		if( empty( $pPackage ) && $this->isValid() ) {
			$pPackage = $this->mInfo['package'];
		}

		if( !empty( $gBitSystem->mPackages[$pPackage] )) {
			$ret = $gBitSystem->mPackages[$pPackage]['path'];
		}

		if( empty( $ret )) {
			$ret = BIT_ROOT_PATH.$pPackage;
		}
		return $ret;
	}

	function checkDependencies( $pPackage ) {
		// TODO: return a hash of versions that need to be fulfilled
	}

	function getSchemaUpgrades( $pFile ) {
		$ret = array();
		$newVersion       = $this->getVersionFromFile( $this->getInstallPath( $this->mInfo['package'] ).'admin/schema_inc.php' );
		$installedVersion = $this->getInstalledVersion( $this->mInfo['package'] );
		require_once( $pFile );
		foreach( array_keys( $schemaUpgrades ) as $version ) {
			if( preg_match( "/^".PACKAGER_VERSION_REGEX."$/", $version ) && $this->versionCompare( $installedVersion, $version ) === -1 && $this->versionCompare( $newVersion, $version ) !== -1 ) {
				$ret[$version] = $schemaUpgrades[$version];
			}
		}
		uksort( $ret, 'array_version_compare' );
		return $ret;
	}

	function updateVersion( $pParamHash ) {
		global $gBitSystem;
		if( !empty( $pParamHash['version'] ) && !empty( $pParamHash['package'] )) {
			$version = $pParamHash['version'].( !empty( $pParamHash['status'] ) ? ".".$pParamHash['status'] : '' );
			$gBitSystem->storeVersion( $pParamHash['package'], $version );
		}
		return TRUE;
	}

	function getSchemaFiles() {
		$ret = array();
		if( $this->isValid( TRUE )) {
			$newVersion = $this->getVersionFromFile( $this->getInstallPath( $this->mInfo['package'] ).'admin/schema_inc.php' );
			$adminPath  = $this->getInstallPath().'admin/';

			if( $handle = opendir( $adminPath )) {
				while( FALSE !== ( $file = readdir( $handle ))) {
					if( preg_match( "/upgrade_(\d+)_inc\.php/", $file, $matches )) {
						$fileVersion = $matches[1];
						if( $this->versionCompare( $newVersion, $fileVersion ) !== -1 ) {
							$ret['file'] = $adminPath.$file;
						}
					}
				}
				closedir( $handle );
				// ensure that we're including them in the right order
				usort( $ret, 'schemafile_version_compare' );
			}
		}
		return $ret;
	}

	function fetchRemotePackage( $pPackagerId = NULL ) {
		if( $this->isValid( TRUE ) ) {
			// determine if we need to download the file at all
			if( !$this->isDownloaded() ) {
				if( $content = bit_http_request( $this->mInfo['package_url'] )) {
					//echo( $content);
					if( $handle = fopen( $this->getPackageFilepath(), 'w' )) {
						fwrite( $handle, $content );
						fclose( $handle );
					} else {
						$this->mErrors['write'] = tra( 'There was a problem trying to write the file to your server.' );
					}
				} else {
					$this->mErrors['download'] = tra( 'There was a problem trying to download the file from the server.' );
				}
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	function prepareVersionForInstall( $pPackagerId = NULL, $pIgnoreVersion = FALSE ) {
		if( @BitBase::verifyId( $pPackagerId )) {
			$this->mPackagerId = $pPackagerId;
		}

		if( $this->isValid( TRUE ) ) {
			if( $this->fetchRemotePackage() ) {
				// shorthand
				$installPath = $this->getInstallPath( $this->mInfo['package'] );
				$backup      = $this->getStoragePath( 'backups' ).$this->mInfo['package'].'-'.mktime();

				if( $pIgnoreVersion || $this->versionCompare( $this->getVersionFromFile( $installPath.'admin/schema_inc.php' ), $this->mInfo ) === -1 ) {
					// only continue if file is present and valid
					if( $this->isDownloaded() ) {
						// extract archive
						$fileHash = array (
							'tmp_name' => $this->getPackageFilepath(),
							'type'     => 'application/zip',
							'name'     => 'temp.zip'
						);
						if( $ext = liberty_process_archive( $fileHash )) {
							if( is_dir( $extracted = $ext.'/'.$this->mInfo['package'] )) {
								if( is_dir( $installPath )) {
									if( rename( $installPath, $backup )) {
										if( !rename( $extracted, $installPath )) {
											$this->mErrors['move'] = tra( 'There was a problem moving the extracted package to its new position.' );
										}
									} else {
										$this->mErrors['backup'] = tra( 'There was a problem moving the original package to the backup location.' );
									}
								} else {
									if( !rename( $extracted, $installPath )) {
										$this->mErrors['move'] = tra( 'There was a problem moving the extracted package to its new position.' );
									}
								}
								// remove unnecessary files
								unlink_r( $ext );
							} else {
								$this->mErrors['extract'] = tra( 'There was a problem extracting the downloaded package.' );
							}
						}
					} else {
						$this->mErrors['filecheck'] = tra( 'The file could not be located on your server.' );
					}
				} else {
					$this->mErrors['version'] = tra( 'The version in your bitweaver root directory is either higher or equal to the version you wish to install. Only upgrades are possible.' );
				}
			}

			if( empty( $this->mErrors ) && $this->versionCompare( $this->getVersionFromFile( $installPath.'admin/schema_inc.php' ), $this->mInfo ) !== 0 ) {
				$this->mErrors['final_version'] = tra( 'Despite a successful download and extraction, there is a problem with the reported version of the package.' );
			}
		}

		return( count( $this->mErrors ) == 0 );
	}
}
?>
