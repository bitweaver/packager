{strip}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.PACKAGER_PKG_URL}index.php">{booticon iname="icon-list" ipackage="icons" iexplain="List Packages" iforce="icon_text"}</a></li>
	{if $gBitUser->hasPermission( 'p_packager_edit_package' )}
		<li><a class="item" href="{$smarty.const.PACKAGER_PKG_URL}edit_package.php">{booticon iname="icon-edit" ipackage="icons" iexplain="Create new Package" iforce="icon_text"}</a></li>
	{/if}
</ul>
{/strip}
