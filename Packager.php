<?php
require_once( PACKAGER_PKG_PATH."PackagerBase.php" );

/**
 * Packager 
 * 
 * @uses BitBase
 */
class Packager extends PackagerBase {
	// currently loaded package
	var $mPackage;
	// list of available licenses
	var $mLicenses = array();
	// list of available types
	var $mTypes = array();

	/**
	 * Initiate class
	 * 
	 * @return void
	 */
	function Packager( $pPackage = NULL ) {
		PackagerBase::PackagerBase();
		$this->mPackage = $pPackage;
		$this->loadLicenses();
		$this->loadTypes();
	}

	/**
	 * load 
	 * 
	 * @param array $pPackage 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function load() {
		if( $this->isValid() ) {
			$whereSql = "WHERE `package`=?";
			$bindVars[] = $this->mPackage;

			if( $aux = $this->mDb->getRow( "SELECT pkgp.* FROM `".BIT_DB_PREFIX."packager_packages` pkgp $whereSql", $bindVars )) {
				$this->mInfo = $aux;
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['documentation_url'] = $this->getDocumentionUrl( $aux['package'] );
			}
		}

		return( count( $this->mInfo ) );
	}

	/**
	 * store 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function store( $pParamHash ) {
		if( $this->verify( $pParamHash )) {
			$table = BIT_DB_PREFIX."packager_packages";

			// mInfo is only populated during an update
			if( !empty( $this->mInfo['package'] )) {
				$this->mDb->associateUpdate( $table, $pParamHash['store'], array( 'package' => $pParamHash['store']['package'] ));
			} else {
				$this->mDb->associateInsert( $table, $pParamHash['store'] );
			}
			$this->mPackage = $pParamHash['store']['package'];

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

		// make sure we are fully loaded
		if( $this->isValid() ) {
			$this->load();
		}

		if( !empty( $pParamHash['package'] )) {
			$pParamHash['package'] = preg_replace( '/[^a-z0-9_-]/', '', strtolower( $pParamHash['package'] ));
		}

		if( !empty( $pParamHash['package'] )) {
			$pParamHash['store']['package'] = $pParamHash['package'];
		} else {
			$this->mErrors['package'] = tra( 'You need to provide a valid package name.' );
		}

		if( !empty( $pParamHash['type_id'] )) {
			$pParamHash['store']['type_id'] = $pParamHash['type_id'];
		} elseif( !empty( $pParamHash['type_new_title'] )) {
			$pParamHash['store']['type_id'] = $this->storeType( $pParamHash );
		}

		if( !empty( $pParamHash['license_id'] )) {
			$pParamHash['store']['license_id'] = $pParamHash['license_id'];
		} elseif( !empty( $pParamHash['license_new_title'] ) && !empty( $pParamHash['license_new_url'] )) {
			$pParamHash['store']['license_id'] = $this->storeLicense( $pParamHash );
		}

		if( empty( $pParamHash['store']['license_id'] )) {
			$this->mErrors['license'] = tra( 'You need to select a license.' );
		}

		$pParamHash['store']['user_id']      = $gBitUser->mUserId;
		$pParamHash['store']['description']  = ( !empty( $pParamHash['description'] ) ? strip_tags( $pParamHash['description'] ) : NULL );
		$pParamHash['store']['is_service']   = ( !empty( $pParamHash['is_service'] ) ? 'y' : 'n' );

		if( $wikiPage = $this->getDocumentionPage( $pParamHash['store']['package'] )) {
			$pParamHash['store']['bwo_content_id'] = $wikiPage[0]['content_id'];
		} else {
			$this->mErrors['wiki_page'] = tra( 'There was a problem trying to create the documentation page.' );
		}

		return( count( $this->mErrors ) == 0 );
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
			$pListHash['sort_mode'] = 'release_date_desc';
		}

		if( !empty( $pListHash['user_id'] )) {
			$whereSql .= empty( $whereSql ) ? ' WHERE ' : ' AND ';
			$whereSql .= ' pkgp.`user_id`=? ';
			$bindVars[] = $pListHash['user_id'];
		}

		LibertyContent::prepGetList( $pListHash );

		$query = "
			SELECT pkgp.*, (
				SELECT MAX( pkgv.`release_date` )
				FROM `".BIT_DB_PREFIX."packager_versions` pkgv
				WHERE pkgv.`package`=pkgp.`package`
			) AS release_date
			FROM `".BIT_DB_PREFIX."packager_packages` pkgp
			$whereSql ORDER BY ".$this->mDb->convertSortmode( $pListHash['sort_mode'] );
		$result = $this->mDb->query( $query, $bindVars );

		while( $aux = $result->fetchRow() ) {
			$aux['display_url']       = $this->getDisplayUrlFromHash( $aux );
			$aux['latest_version']    = $this->getLatestVersion( $aux['package'] );
			$aux['installed_version'] = $this->getInstalledVersion( $aux['package'] );
			$aux['is_cvs']            = ( $this->versionCompare( $aux['latest_version'], $aux['installed_version'] ) === -1 );
			$aux['is_uptodate']       = ( $this->versionCompare( $aux['latest_version'], $aux['installed_version'] ) === 0 );
			$aux['is_upgradable']     = ( $this->versionCompare( $aux['latest_version'], $aux['installed_version'] ) === 1 );
			$ret[]                    = $aux;
		}

		$pListHash['cant'] = count( $ret );
		LibertyContent::postGetList( $pListHash );
		return $ret;
	}

	/* ======================================= Types ======================================= */
	/**
	 * loadType 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function loadTypes() {
		$this->mTypes = $this->mDb->getAssoc( "SELECT `type_id` AS hash_key, * FROM `".BIT_DB_PREFIX."packager_types`" );
	}

	/**
	 * storeType 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function storeType( $pParamHash ) {
		$ret = FALSE;
		if( $this->verifyType( $pParamHash )) {
			// if we have an entry with the same name, we update it
			$table = BIT_DB_PREFIX."packager_types";
			if( $type_id = $this->mDb->getOne( "SELECT `type_id` FROM `$table` WHERE `title`=?", array( $pParamHash['type_new_title'] ))) {
				$this->mDb->associateUpdate( $table, $pParamHash['type_store'], array( 'type_id' => $type_id ));
			} else {
				$ret = $pParamHash['type_store']['type_id'] = $this->mDb->GenID( 'packager_type_id_seq' );
				$this->mDb->associateInsert( $table, $pParamHash['type_store'] );
			}
		}
		return $ret;
	}

	/**
	 * verifyType 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function verifyType( &$pParamHash ) {
		if( !empty( $pParamHash['type_new_title'] )) {
			$pParamHash['type_store']['title'] = substr( $pParamHash['type_new_title'], 0, 100 );
		} else {
			$this->mErrors['type_title'] = tra( 'You need to provede a title for the new type.' );
		}

		return( count( $this->mErrors ) == 0 );
	}

	/* ======================================= Licences ======================================= */
	/**
	 * loadLicenses 
	 * 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function loadLicenses() {
		$this->mLicenses = $this->mDb->getAssoc( "SELECT `license_id` AS hash_key, * FROM `".BIT_DB_PREFIX."packager_licenses`" );
	}

	/**
	 * storeLicense 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function storeLicense( $pParamHash ) {
		$ret = FALSE;
		if( $this->verifyLicense( $pParamHash )) {
			// if we have an entry with the same name, we update it
			$table = BIT_DB_PREFIX."packager_licenses";
			if( $license_id = $this->mDb->getOne( "SELECT `license_id` FROM `$table` WHERE `title`=?", array( $pParamHash['license_new_title'] ))) {
				$this->mDb->associateUpdate( $table, $pParamHash['license_store'], array( 'license_id' => $license_id ));
			} else {
				$ret = $pParamHash['license_store']['license_id'] = $this->mDb->GenID( 'packager_license_id_seq' );
				$this->mDb->associateInsert( $table, $pParamHash['license_store'] );
			}
		}
		return $ret;
	}

	/**
	 * verifyLicense 
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function verifyLicense( &$pParamHash ) {
		if( !empty( $pParamHash['license_new_title'] )) {
			$pParamHash['license_store']['title'] = substr( $pParamHash['license_new_title'], 0, 64 );
		} else {
			$this->mErrors['license_title'] = tra( 'You need to provede a title for the new license.' );
		}

		if( !empty( $pParamHash['license_new_url'] )) {
			$pParamHash['license_store']['license_url'] = $pParamHash['license_new_url'];
		} else {
			$this->mErrors['license_title'] = tra( 'You need to provede a URL for the new license.' );
		}

		return( count( $this->mErrors ) == 0 );
	}

	/* ======================================= Documentation ======================================= */
	/**
	 * see if the documentation page exists, if not create it and return the appropriate content_id
	 * 
	 * @param array $pPackage 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getDocumentionPage( $pPackage = NULL ) {
		global $gBitUser, $gBitSystem;
		$ret = FALSE;

		if( empty( $pPackage ) && $this->isValid() ) {
			$pPackage = $this->mPackage;
		}

		if( !empty( $pPackage )) {
			$page = ucfirst( $pPackage )."Package";
			require_once( LIBERTY_PKG_PATH."LibertyContent.php" );
			$lc = new LibertyContent();
			$ret = $lc->pageExists( $page );
			if( $gBitSystem->isPackageActive( 'wiki' ) && !$ret && $gBitUser->hasPermission( 'p_wiki_edit_page' )) {
				require_once( WIKI_PKG_PATH."BitPage.php" );
				$wp = new BitPage();
				$create = array(
					'title'            => $page,
					'creator_user_id'  => $gBitUser->mUserId,
					'modifier_user_id' => $gBitUser->mUserId,
					'edit'             => "This page contains information about $pPackage",
				);

				// get some rudimentary page details
				if( $wp->store( $create )) {
					$ret = $lc->pageExists( $page );
				}
			}
		}
		return $ret;
	}

	/**
	 * see if the documentation page exists, if not create it and return the appropriate content_id
	 * 
	 * @param array $pPackage 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getDocumentionUrl( $pPackage = NULL ) {
		$ret = FALSE;

		if( empty( $pPackage ) && $this->isValid() ) {
			$pPackage = $this->mPackage;
		}

		if( !empty( $pPackage ) && $page = $this->getDocumentionPage( $pPackage )) {
			$ret = "http://www.bitweaver.org/wiki/".$page[0]['title'];
		}
		return $ret;
	}

	/**
	 * Returns Request URL to a piece of content
	 */
	public function getDisplayUrl() {
		$ret = NULL;
		if( !empty( $this ) && $this->isValid() ) {
			$ret = self::getDisplayUrlFromHash( array( 'package' => $this->mPackage ) );
		}
		return $ret;
	}

	/**
	 * getDisplayUrlFromHash
	 * 
	 * @param array $pParamHash 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getDisplayUrlFromHash( $pParamHash = NULL ) {
		global $gBitSystem;

		$ret = FALSE;

		if( empty( $pParamHash['package'] ) && $this->isValid() ) {
			$pParamHash['package'] = $this->mPackage;
		}

		if( !empty( $pParamHash['package'] )) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) || $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ) {
				$rewrite_tag = $gBitSystem->isFeatureActive( 'pretty_urls_extended' ) ? 'view/' : '';
				$ret = PACKAGER_PKG_URL.$rewrite_tag."package/".urlencode( $pParamHash['package'] );
			} else {
				$ret = PACKAGER_PKG_URL.'view_package.php?package='.urlencode( $pParamHash['package'] );
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
	function isValid() {
		return( !empty( $this->mPackage ));
	}

	function expunge( $pPackage = NULL ) {
		if( !empty( $pPackage )) {
			$this->mPackage = $pPackage;
		}

		if( $this->isValid() ) {
			$this->load();

			$this->mDb->StartTrans();
			// first we get all related versions
			$query = "SELECT `packager_id` FROM `".BIT_DB_PREFIX."packager_versions` WHERE `package` = ?";
			$ids = $this->mDb->getCol( $query, array( $this->mPackage ));
			foreach( $ids as $id ) {
				$version = new PackagerVersions( $id );
				$version->load();
				$version->expunge();
			}

			if( !$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."packager_packages` WHERE `package` = ?", array( $this->mPackage ))) {
				$this->mErrors['delete'] = tra( 'The data could not be removed from the database.' );
			}

			if( count( $this->mErrors ) == 0 ) {
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		$this->postStore();
		return( count( $this->mErrors ) == 0 );
	}
}
?>
