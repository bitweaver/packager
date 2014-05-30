<div class="edit packager">
	<div class="header">
		<h1>{tr}Edit Version{/tr}</h1>
	</div>

	<div class="body">
		{form enctype="multipart/form-data" legend="Edit Version"}
			{formfeedback hash=$feedback}

			<div class="form-group">
				{formlabel label="Package" for="package"}
				{forminput}
					<input type="hidden" name="package" value="{$editInfo.package}" />
					<input type="hidden" name="packager_id" value="{$editInfo.packager_id}" />
					{$editInfo.package}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Version" for="version"}
				{forminput}
					<input type="hidden" name="version" value="{$editInfo.version}" />
					{$editInfo.version}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Security Release" for="is_security_release"}
				{forminput}
					<input type="checkbox" name="is_security_release" id="is_security_release" {if $editInfo.is_security_release == 'y'}checked="checked"{/if} />
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Changelog" for="changelog"}
				{forminput}
					<textarea rows="10" cols="50" name="changelog" id="changelog">
{foreach from=$editInfo.changelog item=log}{$log.flag} {$log.log_date|bit_date_format:"%Y-%m-%d"} {$log.change_log|escape}
{/foreach}</textarea>
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Dependencies" for="dependencies"}
				{forminput}
					<textarea rows="3" cols="50" name="dependencies" id="dependencies">
						{foreach from=$package.dependencies item=dep}
							{$dep.dependency} {$dep.min_version} {$dep.max_version}
						{/foreach}
					</textarea>
					{formhelp note="Please enter any specific package dependencies here. Each dependency on a new line using the format: package min_version max_version"}
				{/forminput}
			</div>

			<div class="submit">
				<input type="submit" class="btn btn-default" name="process_package" value="{tr}Apply Changes{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .___ -->
