<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_packager/install_welcome.php,v 1.1 2007/04/21 14:20:11 squareing Exp $
 * @package install
 * @subpackage upgrade
 */

if( preg_match( '/mysql/', $gBitDbType )) {
	$gBitSmarty->assign( 'dbWarning', 'MySQL 4.1 or greater is required to run the installer. bitweaver will support MySQL 3.23 and above, however, the upgrade process currently uses "sub-selects" which are only supported in MySQL 4.1 and higher and all other real databases.' );
	$gBitSmarty->assign( 'warningSubmit', 'Click if MySQL 4.1 is installed' );
}

ini_set( "max_execution_time", "86400" );
if( ini_get( "max_execution_time" ) != 86400 ) {
	$gBitSmarty->assign( 'max_execution_time', ini_get( "max_execution_time" ) );
}

require_once( PACKAGER_PKG_PATH."PackagerInstall.php" );
$gInstall = new PackagerInstall( @BitBase::verifyId( $_REQUEST['packager_id'] ) ? $_REQUEST['packager_id'] : NULL );
if( @BitBase::verifyId( $_REQUEST['packager_id'] )) {
	if( !$gInstall->isServer() ) {
		$gInstall->load();
	} else {
		$gBitSystem->fatalError( 'Only packager clients can use the package manager to install software.' );
	}
}

// assign next step in installation process
$gBitSmarty->assign( 'next_step', $step + 1 );
$gBitSmarty->assign( 'gInstall', $gInstall );
?>
