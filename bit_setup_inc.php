<?php
/**
 * @author   xing <xing@synapse.plus.com>
 * @version  $Revision: 1.1 $
 * @package  Treasury
 * @subpackage functions
 */
global $gBitSystem, $gBitUser, $gBitSmarty;

$registerHash = array(
	'package_name' => 'packager',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'packager' ) ) {
	if( $gBitUser->isAdmin() ) {
		$menuHash = array(
			'package_name'  => PACKAGER_PKG_NAME,
			'index_url'     => PACKAGER_PKG_URL.'index.php',
			'menu_template' => 'bitpackage:packager/menu_packager.tpl',
		);
		$gBitSystem->registerAppMenu( $menuHash );
	}
}
?>
