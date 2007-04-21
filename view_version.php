<?php
require_once( "../bit_setup_inc.php" );
require_once( PACKAGER_PKG_PATH."Packager.php" );
require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );
require_once( PACKAGER_PKG_PATH."lookup_package.php" );
require_once( PACKAGER_PKG_PATH."form_options_inc.php" );

$gBitSystem->verifyPackage( 'packager' );
//$gBitSystem->verifyPermission( 'p_packager_edit_package' );

// some hashes that we can use smarty functions for dropdowns and the like
$feedback = array();
$gBitSmarty->assign( 'editInfo', $gVersions->mInfo );

$gPackager->packagerDbToXml();

if( !empty( $_REQUEST['process_package'] )) {
	$storeHash = $_REQUEST;

	if( $gVersions->store( $storeHash )) {
		$gPackager->mPackage = $_REQUEST['package'];
		bit_redirect( $gPackager->getDisplayUrl() );
	} else {
		$feedback['error'] = $gVersions->mErrors;
	}
}

$gBitSmarty->assign( 'feedback', $feedback );
$gBitSystem->display( 'bitpackage:packager/view_version.tpl', tra( 'Upload Package' ));
?>
