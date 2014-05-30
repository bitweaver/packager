<?php


if( empty( $argv[1] ) ||  $argv[1] == '--help' || $argv[1] == '-h' || $argv[1] == '?' || !file_exists( $argv[1] ) ) {
echo "This script will update all your templates to use the new set of bootstrap
icons used in bitweaver R4. To run this script please copy it to your theme
directory and execute it with: \n\n";
	print " php ".$argv[0]." path/to/package\n\n";
	die;
}

$PATH = $argv[1];

/*
echo "The script will continue in 5 seconds - hit <ctrl-c> to abort.";
echo '.5'; sleep( 1 );
echo '.4'; sleep( 1 );
echo '.3'; sleep( 1 );
echo '.2'; sleep( 1 );
echo '.1...'; sleep( 1 );
echo "====== Executing substitutions ======\n------ Doing Biticons to Booticons Now ------\n";
*/

# we should make sure that ipackage comes before iname - this is true in bitweaver but who knows in custom templates...
#find . -name "*.tpl" -exec perl -i -wpe ([^}]*?)iname="?(\w+)"?([^}]*?)ipackage="?(\w+)"?/{biticon$1ipackage="$4"$3iname="$2" 

// Icon mappings and conversions
 
$iconMap = array( 
	"accessories-text-editor" => "icon-edit",
	"applications-internet" => "icon-globe",
	"assume-user" => "icon-user-md",
	"bookmark-new" => "icon-bookmark",
	"appointment-new" => "icon-time",
  "camera-photo" => "icon-camera",
	"dialog-ok" => "icon-ok",
	"dialog-warning" => "icon-warning-sign",
	"document-new" => "icon-file",
	"document-properties" => "icon-file",
	"document-open" => "icon-folder-open",
	"document-print" => "icon-print",
	"document-save" => "icon-save",
	"drive-harddisk" => "icon-hdd",
	"edit-delete" => "icon-trash",
	"edit-find" => "icon-search",
	"edit-copy" => "icon-copy",
	"emblem-mail" => "icon-envelope",
	"emblem-photos" => "icon-picture",
	"emblem-readonly" => "icon-lock",
  "emblem-shared" => "icon-key",
	"emblem-symbolic-link" => "icon-circle-arrow-right",
	"emblem-system" => "icon-cogs",
	"folder" => "icon-folder-close",
	"format-justify-fill" => "icon-list",
	"folder-new" => "icon-folder-close",
	"folder-remote" => "icon-sitemap",
	"go-home" => "icon-home",
	"go-down" => "icon-cloud-download",
	"go-up" => "icon-cloud-upload",
	"go-previous" => "icon-arrow-left",
	"go-right" => "icon-arrow-right",
	"list-add" => "icon-plus-sign",
	"list-remove" => "icon-minus-sign",
	"mail-attachment" => "icon-paperclip",
	"mail-forward" => "icon-envelope",
	"mail-reply-sender" => "icon-envelope-alt",
	"preferences-desktop-locale" => "icon-flag",
	"preferences-desktop-sound" => "icon-volume-up",
	"preferences-desktop" => "icon-group",
	"system-users" => "icon-group",
	"user-online" => "icon-user",
	"view-sort-ascending" => "icon-sort",
	"view-refresh" => "icon-recycle",
	"weather-clear" => "icon-asterisk",
	"window-close" => "icon-remove",
	"x-office-calendar" => "icon-calendar",
);

foreach( $iconMap AS $biticon=>$booticon ) {
	print "$PATH $biticon => $booticon\n";
//	`find $PATH -name "*.tpl" -exec perl -i -wpe 's/{biticon\b([^}]*?)iname="?\b$biticon"?\s/{booticon iname="$booticon" $1 /g' {} \;`;
//	`find $PATH -name "*.tpl" -exec perl -i -wpe 's#{smartlink\b([^}]*?)ibiticon="[^/]*/$biticon"#{smartlink$1booticon="$booticon"#g' {} \;`;
}

// CSS changes
$execs = array( 
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/class=\"row\"/class=\"control-group\"/g' {} \;",
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/class=\"row\"/class=\"control-group\"/g' {} \;",
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/class=\"row submit/class=\"control-group submit/g' {} \;",
//"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/input type=\"submit\"/input type=\"submit\" class=\"btn\"/g' {} \;",
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/table class=\"data/table class=\"table/g' {} \;",
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/p class=\"success\"/p class=\"alert alert-success\"/g' {} \;",
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/p class=\"warning\"/p class=\"alert alert-warning\"/g' {} \;",
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/p class=\"error\"/p class=\"alert alert-danger\"/g' {} \;",
"find $PATH -name \"*.*p*\" -exec perl -i -wpe 's/p class=\"alert alert-warning\"/p class=\"alert alert-block\"/g' {} \;",
"find $PATH/modules/*php -exec perl -i -wpe \"s/gBitSmarty->assign[^\\\\(]*\\\\([ ]*('[^']*'),([^;]*);/_template->tpl_vars[\\$1] = new Smarty_variable(\\$2;/g\" {} \;",
);

foreach( $execs as $exec ) {
	print "$exec\n";
	exec( $exec );
}
