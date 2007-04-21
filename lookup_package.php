<?php
if( !empty( $_REQUEST['package'] )) {
	$gPackager = new Packager( $_REQUEST['package'] );
	$gPackager->load();
} else {
	$gPackager = new Packager();
}

if( @BitBase::verifyId( $_REQUEST['packager_id'] )) {
	$gVersions = new PackagerVersions( $_REQUEST['packager_id'] );
	$gVersions->load();
} else {
	$gVersions = new PackagerVersions();
}

$gBitSmarty->assign_by_ref( 'gPackager', $gPackager );
$gBitSmarty->assign_by_ref( 'gVersions', $gVersions );
?>
