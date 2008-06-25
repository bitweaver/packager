<?php
require_once( "../bit_setup_inc.php" );
require_once( PACKAGER_PKG_PATH."Packager.php" );
require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );
require_once( PACKAGER_PKG_PATH."lookup_package.php" );
require_once( PACKAGER_PKG_PATH."form_options_inc.php" );

$gPackager->verifyServer();

$gBitSystem->verifyPackage( 'packager' );
$gBitSystem->verifyPermission( 'p_packager_edit_package' );

$feedback = array();
if( !empty( $_REQUEST['process_package'] )) {
	$storeHash = $_REQUEST;

	if( $gVersions->store( $storeHash )) {
		bit_redirect( $gVersions->getDisplayUrl() );
	} else {
		$feedback['error'] = $gVersions->mErrors;
		$gBitSystem->assign( 'editInfo', $_REQUEST );
	}
}

$gBitSmarty->assign( 'feedback', $feedback );
$gBitSmarty->assign( 'editInfo', $gVersions->mInfo );
$gBitSystem->display( 'bitpackage:packager/edit_version.tpl', tra( 'Edit Package' ), array( 'display_mode' => 'edit' ));
?>
