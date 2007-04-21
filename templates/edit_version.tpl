<div class="edit packager">
	<div class="header">
		<h1>{tr}Edit Version{/tr}</h1>
	</div>

	<div class="body">
		{form enctype="multipart/form-data" legend="Edit Package"}
			{formfeedback hash=$feedback}

			<div class="row">
				{formlabel label="Package" for="package"}
				{forminput}
					<input type="hidden" name="package" value="{$editInfo.package}" />
					<input type="hidden" name="packager_id" value="{$editInfo.packager_id}" />
					{$editInfo.package}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Version" for="version"}
				{forminput}
					<input type="hidden" name="version" value="{$editInfo.version}" />
					{$editInfo.version}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Security Release" for="is_security_release"}
				{forminput}
					<input type="checkbox" name="is_security_release" id="is_security_release" {if $editInfo.is_security_release == 'y'}checked="checked"{/if} />
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Changelog" for="changelog"}
				{forminput}
					<textarea rows="10" cols="50" name="changelog" id="changelog">
{foreach from=$editInfo.changelog item=log}{$log.flag} {$log.log_date|bit_date_format:"%Y-%m-%d"} {$log.change_log|escape}
{/foreach}</textarea>
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Requirements" for="requirements"}
				{forminput}
					<textarea rows="3" cols="50" name="requirements" id="requirements">
						{foreach from=$package.requirements item=requirement}
							{$requirement.required_package} {$requirement.min_version} {$requirement.max_version}
						{/foreach}
					</textarea>
					{formhelp note="Please enter any specific package requirements here. Each requirement on a new line using the format: package min_version max_version"}
				{/forminput}
			</div>

			<div class="submit">
				<input type="submit" name="process_package" value="{tr}Apply Changes{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .___ -->
