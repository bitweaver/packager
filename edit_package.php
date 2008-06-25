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

if( !empty( $_REQUEST['package'] ) && empty( $_REQUEST['process_package'] ) && empty( $gPackager->mInfo )) {
	$gBitSystem->fatalError( tra( 'The package you are looking for can not be found.' ));
} elseif( !empty( $gPackager->mInfo ) && !$gPackager->isOwner() ) {
	$gBitSystem->fatalError( tra( 'Only the package owner may edit this package.' ));
} elseif( !empty( $_REQUEST['process_package'] )) {
	if( $gPackager->store( $_REQUEST )) {
		$gPackager->load();
		bit_redirect( $gPackager->getDisplayUrl() );
	} else {
		$gBitSmarty->assign( 'editInfo', $_REQUEST );
		$feedback['error'] = $gPackager->mErrors;
	}
} elseif( !empty( $_REQUEST['remove'] )) {
	if( !empty( $_REQUEST['confirm'] )) {
		if( $gPackager->expunge( $_REQUEST['remove'] )) {
			bit_redirect( PACKAGER_PKG_URL );
		} else {
			$feedback['error'] = $gPackager->mErrors;
		}
	}

	$gBitSystem->setBrowserTitle( 'Confirm removal of '.$_REQUEST['remove'] );
	$formHash['remove'] = $_REQUEST['remove'];
	$msgHash = array(
		'label' => 'Remove Package and all Versions',
		'confirm_item' => $_REQUEST['remove'],
		'warning' => 'This will remove the package, all associated versions and their files. This cannot be undone!',
	);
	$gBitSystem->confirmDialog( $formHash, $msgHash );
}

$gBitSmarty->assign( 'feedback', $feedback );
if( empty( $_REQUEST['process_package'] )) {
	$gBitSmarty->assign( 'editInfo', $gPackager->mInfo );
}
$gBitSystem->display( 'bitpackage:packager/edit_package.tpl', tra( 'Edit Package Details' ), array( 'display_mode' => 'edit' ));
?>
