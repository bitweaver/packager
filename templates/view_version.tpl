{strip}
<div class="display packager">
	<div class="header">
		<h1>{tr}View Package Version{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		<div class="form-group">
			{formlabel label="Package"}
			{forminput}
				<a href="{$gVersions->mInfo.package_display_url}">{$gVersions->mInfo.package}</a>
			{/forminput}
		</div>

		<div class="form-group">
			{formlabel label="Version"}
			{forminput}
				{$gVersions->mInfo.version}
			{/forminput}
		</div>

		{if $gVersions->mInfo.is_security_release == 'y'}
			<div class="form-group">
				{formlabel label="Security Release"}
				{forminput}
					{booticon iname="icon-warning-sign"  ipackage=icons  iexplain="Security Release"}
				{/forminput}
			</div>
		{/if}

		<div class="form-group">
			{formlabel label="Actions"}
			{forminput}
				<a href="{$gVersions->mInfo.package_url}">{biticon iname=large/go-down ipackage=icons iexplain="Download"}</a>
				{if $gVersions->isOwner()}
					<a href="{$smarty.const.PACKAGER_PKG_URL}edit_version.php?packager_id={$gVersions->mPackagerId}">{biticon iname=large/accessories-text-editor ipackage=icons iexplain="Edit"}</a>
				{/if}
				{formhelp note=""}
			{/forminput}
		</div>

		{if $gVersions->mInfo.changelog}
			<div class="form-group">
				{formlabel label="Changelog"}
				{forminput}
					{booticon iname="icon-plus-sign"  ipackage=icons  iexplain="Feature added" iforce=icon_text}
					&nbsp; &nbsp; &nbsp; &nbsp;
					{biticon ipackage=icons iname="emblem-important" iexplain="Bug Squished" iforce=icon_text}
					&nbsp; &nbsp; &nbsp; &nbsp;
					{booticon iname="icon-minus-sign"  ipackage=icons  iexplain="Feature removed" iforce=icon_text}
					<ul class="data">
						{foreach from=$gVersions->mInfo.changelog item=log}
							<li class="item">
								{if $log.flag == "!"}
									{biticon ipackage=icons iname="emblem-important" iexplain="Bug Squished"}
								{elseif $log.flag == "+"}
									{booticon iname="icon-plus-sign"  ipackage=icons  iexplain="Feature added"}
								{elseif $log.flag == "-"}
									{booticon iname="icon-minus-sign"  ipackage=icons  iexplain="Feature removed"}
								{/if}

								&nbsp; <code>{$log.log_date|bit_date_format:"%Y-%m-%d"}</code> &nbsp; &bull; &nbsp; {$log.change_log|escape}
							</li>
						{/foreach}
					</ul>
				{/forminput}
			</div>
		{/if}

		{if $gVersions->mInfo.dependencies}
			<div class="form-group">
				{formlabel label="Dependencies"}
				{forminput}
					{* we really need some sort of nested dependency tree here *}
					<ul class="data">
						{foreach from=$gVersions->mInfo.dependencies item=dep}
							<li class="item">{$dep.dependency} {$dep.min_version} {$dep.max_version}</li>
						{/foreach}
					</ul>
				{/forminput}
			</div>
		{/if}
	</div><!-- end .body -->
</div><!-- end .packager -->
{/strip}
