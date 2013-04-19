{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=packager">{tr}Packager{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.PACKAGER_PKG_URL}install.php">{tr}Update System{/tr}</a></li>
</ul>

