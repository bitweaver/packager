<?php
require_once( "../bit_setup_inc.php" );
require_once( PACKAGER_PKG_PATH."Packager.php" );
require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );

$gBitSystem->verifyPackage( 'packager' );

$gPackager = new Packager();
$gPackager->verifyServer();

$listHash = $_REQUEST;
$packageList = $gPackager->getList( $listHash );
$gBitSmarty->assign( 'packageList', $packageList );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
$gBitSmarty->assign( 'gPackager', $gPackager );
$gBitSystem->display( 'bitpackage:packager/list_packages.tpl', tra( 'Package List' ), array( 'display_mode' => 'display' ));
?>
