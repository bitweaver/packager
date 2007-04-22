{strip}
<ul>
	<li><a class="item" href="{$smarty.const.PACKAGER_PKG_URL}index.php">{biticon ipackage="icons" iname="format-justify-fill" iexplain="List Packages" iforce="icon_text"}</a></li>
	{if $gBitUser->hasPermission( 'p_packager_edit_package' )}
		<li><a class="item" href="{$smarty.const.PACKAGER_PKG_URL}edit_package.php">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Create new Package" iforce="icon_text"}</a></li>
	{/if}
</ul>
{/strip}
