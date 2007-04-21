<?php
require_once( "../bit_setup_inc.php" );
require_once( PACKAGER_PKG_PATH."Packager.php" );
require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );
require_once( PACKAGER_PKG_PATH."lookup_package.php" );
require_once( PACKAGER_PKG_PATH."form_options_inc.php" );

$gBitSystem->verifyPackage( 'packager' );
//$gBitSystem->verifyPermission( 'p_packager_edit_package' );
$feedback = array();

// check that we're ready to roll
if( empty( $_REQUEST['package'] )) {
	bit_redirect( PACKAGER_PKG_URL );
} elseif( empty( $gPackager->mInfo )) {
	$feedback['error'] = tra( 'The package you are looking for does not seem to exist' );
}

$listHash = $_REQUEST;
$listHash['package'] = $gPackager->mPackage;
$versionList = $gVersions->getList( $listHash );
$gBitSmarty->assign( 'versionList', $versionList );
$gBitSmarty->assign( 'feedback', $feedback );
$gBitSystem->display( 'bitpackage:packager/view_package.tpl', tra( 'View Package Details' ));
?>
