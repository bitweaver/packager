{strip}
<div class="display packager">
	{if $gVersions->isOwner()}
		<div class="floaticon">
			{smartlink ititle="Edit Package Details" ifile="edit_version.php" ibiticon="icons/accessories-text-editor" packager_id=$gVersions->mPackagerId}
		</div>
	{/if}

	<div class="header">
		<h1>{tr}View Package Version{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		<div class="row">
			{formlabel label="Package"}
			{forminput}
				<a href="{$gVersions->mInfo.package_display_url}">{$gVersions->mInfo.package}</a>
			{/forminput}
		</div>

		<div class="row">
			{formlabel label="Version"}
			{forminput}
				{$gVersions->mInfo.version}
			{/forminput}
		</div>

		{if $gVersions->mInfo.is_security_release == 'y'}
			<div class="row">
				{formlabel label="Security Release"}
				{forminput}
					{biticon ipackage=icons iname=dialog-warning iexplain="Security Release"}
				{/forminput}
			</div>
		{/if}

		<div class="row">
			{formlabel label="Actions"}
			{forminput}
				<a href="{$gVersions->mInfo.package_url}">{biticon iname=large/go-down ipackage=icons iexplain="Download"}</a>
				{formhelp note=""}
			{/forminput}
		</div>

		{if $gVersions->mInfo.changelog}
			<div class="row">
				{formlabel label="Changelog"}
				{forminput}
					{biticon ipackage=icons iname="list-add" iexplain="Feature added" iforce=icon_text}
					&nbsp; &nbsp; &nbsp; &nbsp;
					{biticon ipackage=icons iname="emblem-important" iexplain="Bug Squished" iforce=icon_text}
					&nbsp; &nbsp; &nbsp; &nbsp;
					{biticon ipackage=icons iname="list-remove" iexplain="Feature removed" iforce=icon_text}
					<ul class="data">
						{foreach from=$gVersions->mInfo.changelog item=log}
							<li class="item">
								{if $log.flag == "!"}
									{biticon ipackage=icons iname="emblem-important" iexplain="Bug Squished"}
								{elseif $log.flag == "+"}
									{biticon ipackage=icons iname="list-add" iexplain="Feature added"}
								{elseif $log.flag == "-"}
									{biticon ipackage=icons iname="list-remove" iexplain="Feature removed"}
								{/if}

								&nbsp; <code>{$log.log_date|bit_date_format:"%Y-%m-%d"}</code> &nbsp; &bull; &nbsp; {$log.change_log|escape}
							</li>
						{/foreach}
					</ul>
				{/forminput}
			</div>
		{/if}

		{if $gVersions->mInfo.requirements}
			<div class="row">
				{formlabel label="Requirements"}
				{forminput}
					<ul class="data">
						{foreach from=$package.requirements item=requirement}
							<li class="item">{$requirement.required_package} {$requirement.min_version} {$requirement.max_version}</li>
						{/foreach}
					</ul>
				{/forminput}
			</div>
		{/if}
	</div><!-- end .body -->
</div><!-- end .packager -->
{/strip}
