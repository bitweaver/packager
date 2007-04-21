<?php
require_once( "../bit_setup_inc.php" );
require_once( PACKAGER_PKG_PATH."Packager.php" );
require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );
require_once( PACKAGER_PKG_PATH."lookup_package.php" );
require_once( PACKAGER_PKG_PATH."form_options_inc.php" );

$gBitSystem->verifyPackage( 'packager' );
$gBitSystem->verifyPermission( 'p_packager_edit_package' );

$listHash = array(
	'user_id' => $gBitUser->mUserId,
);
$packageList = $gPackager->getList( $listHash );
$gBitSmarty->assign( 'packageList', $packageList );

$feedback = array();

if( !empty( $_REQUEST['process_package'] ) && !empty( $_FILES['package_upload']['tmp_name'] )) {
	$storeHash = $_REQUEST;
	$storeHash['upload'] = $_FILES['package_upload'];

	if( $gVersions->store( $storeHash )) {
		$gPackager->mPackage = $_REQUEST['package'];
		bit_redirect( $gPackager->getDisplayUrl() );
	} else {
		$feedback['error'] = $gVersions->mErrors;
	}
} elseif( !empty( $_REQUEST['process_package'] )) {
	// form was submitted but no file uploaded
	$feedback['error'] = tra( 'You did not provide us with a package.' );
}

$gBitSmarty->assign( 'feedback', $feedback );
$gBitSystem->display( 'bitpackage:packager/upload.tpl', tra( 'Upload Package' ));
?>
