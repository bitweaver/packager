<table class="data">
	<caption>Available Packages</caption>
	<tr>
		<th>{smartlink step=$step ititle="Package" isort="package" idefault=1} ({smartlink step=$step ititle="Type" isort="package_type"})</th>
		<th>{smartlink step=$step ititle="License" isort="license_id"}</th>
		<th>{smartlink step=$step ititle="Service" isort="is_service"}</th>
		{if !$gPackager->isServer()}
			<th>Installed Version</th>
		{/if}
		<th>{smartlink step=$step ititle="Latest Release" isort="release_date"}</th>
		{if $smarty.const.BIT_INSTALL|defined}
			<th>Upgrade</th>
		{/if}
	</tr>
	{foreach from=$packageList item=package}
		<tr class="{cycle values="odd,even" advance=0}">
			<td>
				<a href="{$package.display_url}">{$package.package|capitalize}</a>
				<br />
				{assign var=type_id value=$package.type_id}
				<small>({$gPackager->mTypes.$type_id.title})</small>
			</td>
			<td>
				{assign var=license_id value=$package.license_id}
				<a href="{$gPackager->mLicenses.$license_id.license_url}">{$gPackager->mLicenses.$license_id.title}</a>
			</td>
			<td>{if $package.is_service == 'y'}{booticon iname="icon-ok"  ipackage=icons  iexplain="Service"}{/if}</td>
			{if !$gPackager->isServer()}
				<td>{$package.installed_version.version} {$package.installed_version.status}</td>
			{/if}
			<td>
				{if $package.latest_version.packager_id}
					<a href="{$package.latest_version.display_url}">{$package.latest_version.version} {$package.latest_version.status}</a>
					{if $package.latest_version.is_security_release == 'y'}{booticon iname="icon-warning-sign"   iexplain="Security Release"}{/if}<br />
					<small>{$package.latest_version.release_date|bit_short_date}</small>
				{/if}
			</td>
			{if $smarty.const.BIT_INSTALL|defined}
				<td class="actionicon">
					{if $package.is_uptodate}
						{booticon iname="icon-ok"  ipackage=icons  iexplain="Installed"}
					{elseif $package.is_upgradable}
						<input type="checkbox" name="upgrades[]" value="{$package.latest_version.packager_id}" />
					{elseif $package.latest_version.packager_id}
						TODO:<br />install link
					{/if}
				</td>
			{/if}
		</tr>

		<tr class="{cycle}">
			<td colspan="6">{$package.description}</td>
		</tr>
	{foreachelse}
		<tr>
			<td class="norecords" colspan="6">{tr}No Records found{/tr}</td>
		</tr>
		{if $gPackager->isServer() and $gBitUser->hasPermission('p_packager_edit_package')}
			<tr>
				<td class="norecords" colspan="6"><a href="{$smarty.const.PACKAGER_PKG_URL}edit_package.php">{tr}Create Package{/tr}</a></td>
			</tr>
		{/if}
	{/foreach}
</table>
