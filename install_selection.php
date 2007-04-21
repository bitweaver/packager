<?php
$gBitSystem->verifyPackage( 'packager' );
require_once( PACKAGER_PKG_PATH."Packager.php" );
$gPackager = new Packager();

$feedback = array();

// display error when sent back here from the next page
if( !empty( $_REQUEST['noselection'] )) {
	$feedback['warning'] = "You need to select at least one package to upgrade.";
}

$listHash = $_REQUEST;
$packageList = $gPackager->getList( $listHash );
$gBitSmarty->assign( 'packageList', $packageList );
$gBitSmarty->assign( 'gPackager', $gPackager );
$gBitSmarty->assign( 'feedback', $feedback );
$gBitSmarty->assign( 'next_step', $step + 1 );
?>
