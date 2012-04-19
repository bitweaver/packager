<?php
$tables = array (
	'packager_types' => "
		type_id I8 PRIMARY NOTNULL,
		title C(100)
	",

	'packager_licenses' => "
		license_id I8 PRIMARY NOTNULL,
		title C(64),
		license_url C(250)
	",

	'packager_packages' => "
		package C(64) PRIMARY NOTNULL,
		user_id I8 NOTNULL,
		bwo_content_id I8 NOTNULL,
		license_id I8,
		type_id I8,
		description X,
		is_service C(1) NOTNULL DEFAULT 'n'
	",

	'packager_versions' => "
		packager_id I8 PRIMARY,
		downloads I8,
		package C(64) NOTNULL,
		md5_hash C(32) NOTNULL,
		file_size I4 NOTNULL,
		status C(64),
		version C(64) NOTNULL,
		release_date I8 NOTNULL,
		is_security_release C(1) NOTNULL DEFAULT 'n'
		CONSTRAINT '
			, CONSTRAINT `packager_package_ref` FOREIGN KEY ( `package` ) REFERENCES `".BIT_DB_PREFIX."packager_packages`( `package` )'
	",

	'packager_changelogs' => "
		packager_id I8 NOTNULL,
		log_date I8,
		flag C(1) NOTNULL DEFAULT '+',
		change_log X
		CONSTRAINT '
			, CONSTRAINT `packager_packager_id_ref` FOREIGN KEY ( `packager_id` ) REFERENCES `".BIT_DB_PREFIX."packager_versions`( `packager_id` )'
	",

	'packager_dependencies' => "
		packager_id I8 NOTNULL,
		dependency C(64) NOTNULL,
		min_version C(64),
		max_version C(64)
		CONSTRAINT '
			, CONSTRAINT `packager_dependencies_packager_id_ref` FOREIGN KEY ( `packager_id` ) REFERENCES `".BIT_DB_PREFIX."packager_versions`( `packager_id` )'
	",

	'packager_installed' => "
		packager_id I8 NOTNULL PRIMARY,
		download_date I8,
		download_message X,
		install_date I8,
		install_message X
		CONSTRAINT '
			, CONSTRAINT `packager_installed_packager_id_ref` FOREIGN KEY ( `packager_id` ) REFERENCES `".BIT_DB_PREFIX."packager_versions`( `packager_id` )'
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( PACKAGER_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageVersion( PACKAGER_PKG_NAME, '0.0.0.dev' );

$gBitInstaller->registerPackageInfo( PACKAGER_PKG_NAME, array(
	'description' => "A powerful way to manage bitweaver packages.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
));

// Sequences
$gBitInstaller->registerSchemaSequences( PACKAGER_PKG_NAME, array (
	'packager_licenses_id_seq' => array( 'start' => 1 ),
	'packager_types_id_seq' => array( 'start' => 1 ),
	'packager_id_seq' => array( 'start' => 1 ),
));

// Indeces
$gBitInstaller->registerSchemaIndexes( PACKAGER_PKG_NAME, array (
	'packager_package_id_idx' => array( 'table' => 'packager_installed', 'cols' => 'packager_id', 'opts' => array( 'UNIQUE' ) ),
));

// Default Preferences
$gBitInstaller->registerPreferences( PACKAGER_PKG_NAME, array(
));

// Default UserPermissions
$gBitInstaller->registerUserPermissions( PACKAGER_PKG_NAME, array(
	array( 'p_packager_download_package', 'Can download packages', 'basic', PACKAGER_PKG_NAME ),
	array( 'p_packager_edit_package', 'Can upload and edit packages', 'registered', PACKAGER_PKG_NAME ),
));

// Default Database entries
$gBitInstaller->registerSchemaDefault( PACKAGER_PKG_NAME, array(
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-1, 'Core Package')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-2, 'Content in General')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-3, 'Meta Data')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-4, 'Categorisation or Tagging')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-5, 'Search')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-6, 'Navigation')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-7, 'Ratings')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-8, 'Personalisation')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-9, 'Package Maintenance')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-10, 'News or Blogs')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-11, 'Maps and Navigation')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-12, 'Shop')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-13, 'Forums')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-14, 'Newsletters or Messaging')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-15, 'Calendar or Events')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-16, 'Project Management')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-17, 'File Management')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-18, 'Image Management')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_types` (`type_id`,`title`) VALUES (-19, 'Wiki')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_licenses` (`license_id`,`title`, `license_url`) VALUES (-1, 'GPL', 'http://www.gnu.org/licenses/gpl.html')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_licenses` (`license_id`,`title`, `license_url`) VALUES (-2, 'LGPL', 'http://www.gnu.org/licenses/lgpl.html')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_licenses` (`license_id`,`title`, `license_url`) VALUES (-3, 'CC3 Attribution 3.0', 'http://creativecommons.org/licenses/by/3.0/')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_licenses` (`license_id`,`title`, `license_url`) VALUES (-4, 'CC3 Attribution-ShareAlike 3.0', 'http://creativecommons.org/licenses/by-sa/3.0/')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_licenses` (`license_id`,`title`, `license_url`) VALUES (-5, 'CC3 Attribution-NoDerivs 3.0', 'http://creativecommons.org/licenses/by-nd/3.0/')",
	"INSERT INTO `".BIT_DB_PREFIX."packager_licenses` (`license_id`,`title`, `license_url`) VALUES (-6, 'CC3 Attribution-NonCommercial 3.0', 'http://creativecommons.org/licenses/by-nc/3.0/')",
));
?>
