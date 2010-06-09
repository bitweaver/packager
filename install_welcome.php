<?php
/**
 * @version $Header$
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

// assign next step in installation process
$gBitSmarty->assign( 'next_step', $step + 1 );
$gBitSmarty->assign( 'gInstall', $gInstall );
?>
