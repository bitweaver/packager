<?php
require_once( "../bit_setup_inc.php" );
$gBitSystem->verifyPackage( 'packager' );
// we need to find a way to verify that the computer that is downloading is a bitweaver install
//$gBitSystem->verifyPermission( 'p_packager_download_package' );

require_once( PACKAGER_PKG_PATH."Packager.php" );
require_once( PACKAGER_PKG_PATH."PackagerVersions.php" );
require_once( PACKAGER_PKG_PATH."lookup_package.php" );

if( empty( $_REQUEST['packager_id'] )) {
	$gBitSystem->fatalError( 'Without an id, we do not know what file you want to download.' );
} elseif( !empty( $gVersions->mInfo )) {
	$fileHash = $gVersions->mInfo;
	if( is_readable( $fileHash['source_file'] )) {
		require_once( PACKAGER_PKG_PATH.'download_inc.php' );
		$gVersions->addHit();
		exit();
	} else {
		$gBitSystem->fatalError( 'The requested file could not be found.' );
	}
}
?>
