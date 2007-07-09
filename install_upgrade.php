<?php
// redirect if something is wrong
if( empty( $_SESSION['upgrades'] )) {
	header( "Location: ".$_SERVER['PHP_SELF']."?noselection=1&step=".--$step );
	die;
}
$_REQUEST['upgrades'] = $_SESSION['upgrades'];

$versionList = $gInstall->getList( $listHash );
$upgradeList = array();
foreach( $_REQUEST['upgrades'] as $packager_id ) {
	$upgradeList[$packager_id] = $versionList[$packager_id];
	$gInstall->mPackagerId = $packager_id;
	$upgradeList[$packager_id]['schema_files'] = $gInstall->getSchemaFiles();
}

// here we go with the upgrade
if( !empty( $_REQUEST['upgrade'] )) {
	if( !empty( $gDebug ) || !empty( $_REQUEST['debug'] ) ) {
		$gBitInstaller->debug();
	}

	// cycle though packages that need upgrading
	foreach( $upgradeList as $upgrade ) {
		$updateVersion = TRUE;
		// cycle through individual schema files and include them
		foreach( $upgrade['schema_files'] as $schema ) {
			$schemaUpgrades = $gInstall->getSchemaUpgrades( $schema );
			if( !empty( $schemaUpgrades )) {
				// pass the upgrades hash on to the installer
				foreach( $schemaUpgrades as $up ) {
					$gBitSystem->mUpgrades[$upgrade['package']] = $up;
					if( $err = $gBitInstaller->upgradePackage( $upgrade['package'] )) {
						$failedcommands[$upgrade['packager_id']][] = $err;
						$updateVersion = FALSE;
					}
				}
			}
		}

		// only update the installed version of this package in the database if everything went well
		if( $updateVersion ) {
			$gInstall->updateVersion( $upgrade );
		}
	}

	$gBitSmarty->assign( 'failedcommands', $failedcommands );
	$gBitSmarty->assign( 'next_step', $step + 1 );
	$app = "_done";
} else {
	$gBitSmarty->assign( 'next_step', $step );
}

$gBitSmarty->assign( 'upgradeList', $upgradeList );
$gBitSmarty->assign( 'gInstall', $gInstall );
?>
