<?php
$feedback = array();

if( !empty( $_REQUEST['skip'] )) {
	bit_redirect( PACKAGER_PKG_URL.'install.php?step='.++$step );
}

if( !empty( $_REQUEST['db_download'] )) {
	if( !$gInstall->fetchRemoteXmlFiles() ) {
		$feedback['error'] = $gInstall->mErrors;
	} else {
		$feedback['success'] = 'All required database table data was downloaded and stored in:<br />'.dirname( $gInstall->getXmlFilepath( $gInstall->mTables[0] ) );
	}

	$tables   = $gInstall->mTables;
	$tables[] = 'kernel_config';
	foreach( $tables as $table ) {
		$xmlFiles[$table] = is_file( $gInstall->getXmlFilepath( $table ));
	}
	$gBitSmarty->assign( 'xmlFiles', $xmlFiles );
}

if( !empty( $_REQUEST['db_update'] )) {
	if( !$gInstall->packagerXmlToDb() ) {
		$feedback['error'] = $gInstall->mErrors;
	}
	$app = "_done";
	$gBitSmarty->assign( 'next_step', $step + 1 );
} else {
	$gBitSmarty->assign( 'next_step', $step );
}

$gBitSmarty->assign( 'feedback', $feedback );
$gBitSmarty->assign( 'gInstall', $gInstall );
?>
