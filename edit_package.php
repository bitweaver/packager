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

if( !empty( $gPackager->mInfo ) && !$gPackager->isOwner() ) {
	$gBitSystem->fatalError( 'Only the package owner may edit this package.' );
} elseif( !empty( $_REQUEST['process_package'] )) {
	if( $gPackager->store( $_REQUEST )) {
		$gPackager->load();
		bit_redirect( $gPackager->getDisplayUrl() );
	} else {
		$feedback['error'] = $gPackager->mErrors;
	}
}

$gBitSmarty->assign( 'feedback', $feedback );
$gBitSmarty->assign( 'editInfo', $gPackager->mInfo );
$gBitSystem->display( 'bitpackage:packager/edit_package.tpl', tra( 'Edit Package Details' ));
?>
