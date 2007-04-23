<?php
if( $gInstall->isServer() ) {
	$gBitSystem->fatalError( tra( 'Only packager clients can use the package manager to install software.' ));
}
$gBitSmarty->assign_by_ref( 'gInstall', $gInstall );

// we'll just use the themes package to play with.
$testdir  = $gInstall->getInstallPath( 'themes' );
$backup   = $gInstall->getStoragePath( 'backups' ).'_dummy';
$tempfile = $gInstall->getStoragePath( 'packages' ).'temp.zip';

$pp['download']['note'] = 'Try to download a file from www.bitweaver.org.';
$pp['write']['note']    = 'Try to write the downloaded file to your server.';
$pp['extract']['note']  = 'Try to extract the downloaded zip file on your server.';
$pp['move']['note']     = 'Try to move one of the installed packages to a backup location.<br />We will test this with the <em>Themes</em> package.';
$pp['replace']['note']  = 'Try to move the extracted files to your bitweaver root directy.';

// assume that something went wrong if we didn't explicitly spcify that it didn't.
foreach( $pp as $key => $item ) {
	$pp[$key]['result'] = 'error';
}

if( !empty( $_REQUEST['perform_checks'] )) {
	// download
	if( $content = bit_http_request( "http://www.bitweaver.org/storage/test.zip" )) {
		$pp['download']['result'] = 'ok';

		// write
		if( $handle = fopen( $tempfile, 'w' )) {
			fwrite( $handle, $content );
			fclose( $handle );
			$pp['write']['result'] = 'ok';

			// extract archive
			$fileHash = array (
				'tmp_name' => $tempfile,
				'type'     => 'application/zip',
				'name'     => 'temp.zip'
			);
			if( $extracted = liberty_process_archive( $fileHash )) {
				if( is_file( $extracted."/test.txt" )) {
					$pp['extract']['result'] = 'ok';
					$dummyfile = BIT_ROOT_PATH."___bitdummy.txt";
					if( rename( $extracted."/test.txt", $dummyfile )) {
						$pp['replace']['result'] = 'ok';
						unlink( $dummyfile );
					}
				}
				unlink_r( $extracted );
			}

			// remove the testfile
			unlink( $tempfile );
		}
	}

	// move
	if( @rename( $testdir, $backup )) {
		rename( $backup, $testdir );
		$pp['move']['result'] = 'ok';
	}

	// we are ready to advance a stage if the user wants to
	$gBitSmarty->assign( 'next_step', $step + 1 );
	$app = "_done";
} else {
	$gBitSmarty->assign( 'next_step', $step );
}

$gBitSmarty->assign( 'pp', $pp );
?>
