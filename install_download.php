<?php
$feedback = array();
$progressReport = array();

// redirect if something is wrong
if( empty( $_REQUEST['upgrades'] )) {
	header( "Location: ".$_SERVER['SCRIPT_NAME']."?noselection=1&step=".--$step );
	die;
} elseif( !empty( $_REQUEST['continue'] )) {
	// we transfer manually since the user can decide when he wants to move on and we're storing the upgrade list in the session
	header( "Location: ".$_SERVER['SCRIPT_NAME']."?step=".++$step );
	die;
}

// transfer the list of files we need to process into our session
$_SESSION['upgrades'] = $_REQUEST['upgrades'];

$versionList = $gInstall->getList( $listHash );
$upgradeList = array();
foreach( $_REQUEST['upgrades'] as $packager_id ) {
	$upgradeList[$packager_id] = $versionList[$packager_id];
}
$gBitSmarty->assign( 'upgradeList', $upgradeList );

// this is where we start the actual downloads
if( !empty( $_REQUEST['download'] )) {
	$stages = array(
		'download'      => 'Download package',
		'write'         => 'Write package to your server',
		'filecheck'     => 'Check the file integrity',
		'extract'       => 'Extract the file',
		'version'       => 'Compare the version of the downloaded file to the one you are using',
		'backup'        => 'Back up the original package',
		'move'          => 'Move the extracted package to the place where the previous version was',
		'final_version' => 'Check version again after having shifted around all the files',
	);

	foreach( $_REQUEST['upgrades'] as $packager_id ) {
		$progressReport = &$upgradeList;
		if( !$gInstall->prepareVersionForInstall( $packager_id, !empty( $_REQUEST['ignore_versions'] ))) {
			$progressReport[$packager_id]['error'] = $gInstall->mErrors;
			$gBitSmarty->assign( 'errors', $errors = TRUE );
		}
	}
	$gBitSmarty->assign( 'stages', $stages );
}

$gBitSmarty->assign( 'gInstall', $gInstall );
$gBitSmarty->assign( 'feedback', $feedback );
$gBitSmarty->assign( 'progressReport', $progressReport );
$gBitSmarty->assign( 'next_step', $step );
?>
