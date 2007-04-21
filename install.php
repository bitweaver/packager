<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_packager/install.php,v 1.1 2007/04/21 14:20:11 squareing Exp $
 * @package install
 * @subpackage upgrade
 */

/**
 * required setup
 */
// make sure the installer is accessible. some users might have change perms or removed it entirely.
if( !@include( '../install/install_inc.php' )) {
	require_once( '../bit_setup_inc.php' );
	$gBitSystem->fatalError( 'Please ensure that the installer package is accessible and that the installer is located at' ).": ".BIT_ROOT_URL."install/";
}

require_once( '../install/install_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );
$gBitSystem->verifyPackage( 'packager' );

// this variable will be appended to the template file called - useful for displaying messages after data input
$app = '';

// work out where in the installation process we are
if( !isset( $_REQUEST['step'] ) ) {
	$_REQUEST['step'] = 0;
}
$step = $_REQUEST['step'];

// updating $install_file name
$i = 0;
$install_file[$i]['file'] = 'welcome';
$install_file[$i]['name'] = 'Welcome';
$i++;
$install_file[$i]['file'] = 'tests';
$install_file[$i]['name'] = 'Preliminary tests';
$i++;
$install_file[$i]['file'] = 'db_update';
$install_file[$i]['name'] = 'Databse Update';
$i++;
$install_file[$i]['file'] = 'selection';
$install_file[$i]['name'] = 'Package Selection';
$i++;
$install_file[$i]['file'] = 'download';
$install_file[$i]['name'] = 'Package download';
$i++;
$install_file[$i]['file'] = 'upgrade';
$install_file[$i]['name'] = 'Package Upgrade';
$i++;
$install_file[$i]['file'] = 'verification';
$install_file[$i]['name'] = 'Database Verification';
$i++;
$install_file[$i]['file'] = 'final';
$install_file[$i]['name'] = 'Upgrade Complete';

// finally we are ready to include the actual php file
include_once( 'install_'.$install_file[$step]['file'].'.php' );

// this is used in the menu
$_SESSION['first_install'] = TRUE;
$install_file = set_menu( $install_file, $step );

$gBitSmarty->assign( 'menu_path', PACKAGER_PKG_URL );
$gBitSmarty->assign( 'menu_file', 'install.php' );
$gBitSmarty->assign( 'section', 'Packager' );

$gBitSmarty->assign( 'install_file', PACKAGER_PKG_PATH."templates/install_".$install_file[$step]['file'].$app.".tpl" );
$gBitInstaller->display( INSTALL_PKG_PATH.'templates/install.tpl', $install_file[$step]['name'] );

?>
