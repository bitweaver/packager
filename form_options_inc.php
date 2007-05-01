<?php
$packageTypes[] = 'Add new package type below';
foreach( $gPackager->mTypes as $type ) {
	$packageTypes[$type['type_id']] = $type['title'];
}
$gBitSmarty->assign( 'packageTypes', $packageTypes );

$licenseTypes[] = 'Add new license below';
foreach( $gPackager->mLicenses as $license ) {
	$licenseTypes[$license['license_id']] = $license['title'];
}
$gBitSmarty->assign( 'licenseTypes', $licenseTypes );

$statuses = array(
	''       => '',
	'dev'    => 'dev',
	'alpha'  => 'alpha',
	'beta'   => 'beta',
	'stable' => 'stable',
);
$gBitSmarty->assign( 'statuses', $statuses );
?>
