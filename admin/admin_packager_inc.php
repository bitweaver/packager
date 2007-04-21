<?php
// $Header: /cvsroot/bitweaver/_bit_packager/admin/admin_packager_inc.php,v 1.1 2007/04/21 14:20:13 squareing Exp $

// any setting preceded by packager_rem_ will be copied to the client when they update.
// we could copy settings like: pretty urls and the like to the client
$packagerSettings = array(
	'packager_rem_host' => array(
		'label' => 'Hostname',
		'note'  => 'Set this to your package repository host, such as www.bitweaver.org. Leave blank to access packages hosted on www.bitweaver.org.',
		'type'  => 'text',
	),
);
$gBitSmarty->assign( 'packagerSettings', $packagerSettings );

if( !empty( $_REQUEST['packager_settings'] ) ) {
	foreach( $packagerSettings as $item => $data ) {
		if( $data['type'] == 'checkbox' ) {
			simple_set_toggle( $item, PACKAGER_PKG_NAME );
		} elseif( $data['type'] == 'numeric' ) {
			simple_set_int( $item, PACKAGER_PKG_NAME );
		} else {
			$gBitSystem->storeConfig( $item, ( !empty( $_REQUEST[$item] ) ? $_REQUEST[$item] : NULL ), PACKAGER_PKG_NAME );
		}
	}

	$copySettings = array(
		'packager_rem_pretty_urls' => 'pretty_urls',
	);

	foreach( $copySettings as $packager => $kernel ) {
		$gBitSystem->storeConfig( $packager, $gBitSystem->getConfig( $kernel ));
	}
}
?>
