<?php
$tables = array (
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
		package_type C(250),
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

	'packager_requirements' => "
		packager_id I8 NOTNULL,
		required_package C(64) NOTNULL,
		min_version C(64),
		max_version C(64)
		CONSTRAINT '
			, CONSTRAINT `packager_packager_id_ref` FOREIGN KEY ( `packager_id` ) REFERENCES `".BIT_DB_PREFIX."packager_versions`( `packager_id` )'
	",

	'packager_installed' => "
		packager_id I8 NOTNULL PRIMARY,
		download_date I8,
		download_message X,
		install_date I8,
		install_message X
		CONSTRAINT '
			, CONSTRAINT `packager_packager_id_ref` FOREIGN KEY ( `packager_id` ) REFERENCES `".BIT_DB_PREFIX."packager_versions`( `packager_id` )'
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( PACKAGER_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( PACKAGER_PKG_NAME, array(
	'description' => "A powerful way to manage bitweaver packages.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
));

// Sequences
$gBitInstaller->registerSchemaSequences( PACKAGER_PKG_NAME, array (
	'packager_licenses_id_seq' => array( 'start' => 1 ),
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
?>
