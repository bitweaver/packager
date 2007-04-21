<?php
$licenseTypes[] = 'Add new license below';
foreach( $gPackager->mLicenses as $license ) {
	$licenseTypes[$license['license_id']] = $license['title'];
}
$gBitSmarty->assign( 'licenseTypes', $licenseTypes );

$packageTypes = array(
	''                          => 'Other',
	'Core Package'              => 'Core Package',
	'Content in General'        => 'Content in General',
	'Meta Data'                 => 'Meta Data',
	'Categorisation or Tagging' => 'Categorisation or Tagging',
	'Search'                    => 'Search',
	'Navigation'                => 'Navigation',
	'Ratings'                   => 'Ratings',
	'Personalisation'           => 'Personalisation',
	'News or Blogs'             => 'News or Blogs',
	'Maps and Navigation'       => 'Maps and Navigation',
	'Shop'                      => 'Shop',
	'Forums'                    => 'Forums',
	'Newsletters or Messaging'  => 'Newsletters or Messaging',
	'Calendar or Events'        => 'Calendar or Events',
	'Project Management'        => 'Project Management',
	'File Management'           => 'File Management',
	'Image Management'          => 'Image Management',
	'Wiki'                      => 'Wiki',
);
ksort( $packageTypes );
$gBitSmarty->assign( 'packageTypes', $packageTypes );

$statuses = array(
	''       => '',
	'dev'    => 'dev',
	'alpha'  => 'alpha',
	'beta'   => 'beta',
	'stable' => 'stable',
);
$gBitSmarty->assign( 'statuses', $statuses );
?>
