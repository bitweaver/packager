{strip}
<div class="display packager">
	{if $gPackager->isOwner()}
		<div class="floaticon">
			{smartlink ititle="Edit Package Details" ifile="edit_package.php" booticon="icon-edit" package=$gPackager->mInfo.package}
			{smartlink ititle="Remove Package" ifile="edit_package.php" booticon="icon-trash" remove=$gPackager->mInfo.package}
		</div>
	{/if}

	<div class="header">
		<h1>{tr}Package{/tr}: {$gPackager->mPackage|capitalize}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		{if $gPackager->mInfo}
			<div class="form-group">
				{formlabel label="Package"}
				{forminput}
					{$gPackager->mInfo.package|escape}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Package Type"}
				{forminput}
					{assign var=type_id value=$gPackager->mInfo.type_id}
					{$gPackager->mTypes.$type_id.title}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Package description"}
				{forminput}
					{$gPackager->mInfo.description|escape}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Online Documentation"}
				{forminput}
					<a {if !$gPackager->isServer()}class="external"{/if} href="{$gPackager->mInfo.documentation_url}">{$gPackager->mPackage|capitalize}Package</a>
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="License"}
				{forminput}
					{assign var=license_id value=$gPackager->mInfo.license_id}
					<a href="{$gPackager->mLicenses.$license_id.license_url}">{$gPackager->mLicenses.$license_id.title}</a>
				{/forminput}
			</div>

			{if $gPackager->mInfo.is_service == 'y'}
				<div class="form-group">
					{formlabel label="Service"}
					{forminput}
						{booticon iname="icon-ok"  ipackage=icons  iexplain="Is Service"}
					{/forminput}
				</div>
			{/if}

			<h2>{tr}Package Versions{/tr}</h2>

			{if $gPackager->isOwner()}
				{smartlink ititle="Upload new version" ifile="upload.php" package=$gPackager->mPackage}
			{/if}

			{if $versionList}
				<table class="table data">
					<caption>{tr}Available Versions{/tr}</caption>
					<tr>
						<th style="width:1%"> </th>
						{*<th style="width:1%"> </th>*}
						<th>{smartlink package=$gPackager->mPackage ititle="Release Date" isort="release_date" idefault=1}</th>
						<th>{smartlink package=$gPackager->mPackage ititle="Version" isort="version"}</th>
						<th>{smartlink package=$gPackager->mPackage ititle="Filesize" isort="file_size"}</th>
						<th>{tr}Actions{/tr}</th>
					</tr>
					{foreach from=$versionList item=version}
						<tr class="{cycle values="odd,even"}">
							<td>{if $version.is_security_release == 'y'}{booticon iname="icon-warning-sign"  ipackage="icons"  iexplain="Security Release"}{/if}</td>
							{*<td>{if !$gPackager->isServer() && $version.is_downloaded}{booticon iname="icon-ok"  ipackage=icons  iexplain="On Server"}{/if}</td>*}
							<td>{$version.release_date|bit_short_date}</td>
							<td><a href="{$version.display_url}">{$version.version} <em>{$version.status}</em></a></td>
							<td style="text-align:right">{$version.file_size|display_bytes}</td>
							<td class="actionicon">
								<a href="{$version.package_url}">{booticon iname="icon-cloud-download"   ipackage="icons" iexplain="Download"}</a>
								{if $gPackager->isOwner()}
									{smartlink ititle="Edit Version" ifile="edit_version.php" packager_id=$version.packager_id booticon="icon-edit"}
								{/if}
							</td>
						</tr>
					{/foreach}
				</table>
			{else}
				<p class="norecords">{tr}No versions for this package have been uploaded yet{/tr}</p>
			{/if}
		{/if}
	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}
