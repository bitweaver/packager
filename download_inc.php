<?php
// Check to see if the file actually exists
if( is_readable( $fileHash['source_file'] )) {
	// if we have PEAR HTTP/Download installed, we make use of it since it allows download resume and download manager access
	// read the docs if you want to enable download throttling and the like
	if( @include_once( 'HTTP/Download.php' )) {
		$dl = new HTTP_Download();
		$dl->setLastModified( $fileHash['last_modified'] );
		$dl->setFile( $fileHash['source_file'] );
		$dl->setContentDisposition( HTTP_DOWNLOAD_ATTACHMENT, $fileHash['filename'] );
		$dl->setContentType( $fileHash['mime_type'] );
		$res = $dl->send();

		if( PEAR::isError( $res )) {
			$gBitSystem->fatalError( $res->getMessage() );
		}
	} else {
		header( "Cache Control: " );
		header( "Accept-Ranges: bytes" );
		header( "Content-type: ".$fileHash['mime_type'] );
		header( "Content-Disposition: attachment; filename=".$fileHash['filename'] );
		header( "Last-Modified: ".gmdate( "D, d M Y H:i:s", $fileHash['last_modified'] )." GMT", true, 200 );
		header( "Content-Length: ".filesize( $fileHash['source_file'] ));
		header( "Content-Transfer-Encoding: binary" );
		header( "Connection: close" );
		readfile( $fileHash['source_file'] );
	}
}
die;
?>
