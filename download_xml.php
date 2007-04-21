<?php
require_once( "../bit_setup_inc.php" );
$gBitSystem->verifyPackage( 'packager' );
// we need to find a way to verify that the computer that is downloading is a bitweaver install
//$gBitSystem->verifyPermission( 'p_packager_download_package' );

require_once( PACKAGER_PKG_PATH."Packager.php" );

if( empty( $_REQUEST['table'] )) {
	$gBitSystem->fatalError( 'Without a table, we do not know what file you want to download.' );
} else {
	$gPackager = new Packager();

	$fileHash['source_file']   = $gPackager->getXmlFilepath( $_REQUEST['table'] );
	if( is_readable( $fileHash['source_file'] )) {
		$fileHash['last_modified'] = filemtime( $fileHash['source_file'] );
		$fileHash['mime_type']     = 'text/xml';
		$fileHash['filename']      = $_REQUEST['table'].'.xml';

		require_once( PACKAGER_PKG_PATH.'download_inc.php' );
	} else {
		$gBitSystem->fatalError( 'The requested file could not be found.' );
	}
}
?>
