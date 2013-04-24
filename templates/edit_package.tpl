{strip}
<div class="edit packager">
	<div class="header">
		<h1>{tr}Create / Edit Package{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Edit Package"}
			{formfeedback hash=$feedback}

			<p class="help">{tr}Here you can describe the package you wish to upload. Please provide accurate information that users who are looking for new packages know what to expect when installing this particular package.{/tr}</p>

			<div class="control-group">
				{formlabel label="Package" for="package"}
				{forminput}
					{if $editInfo.package}
						{$editInfo.package|escape}
						<input type="hidden" name="package" value="{$editInfo.package|escape}" />
					{else}
						<input type="text" name="package" id="package" size="30" value="{$editInfo.package|escape}" />{required}
						{formhelp note="Please insert the name of the package. Please bear in mind that the name will be reformatted to contain only alphanumeric characters."}
					{/if}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="Package description" for="description"}
				{forminput}
					<textarea rows="5" cols="50" name="description" id="description">{$editInfo.description|escape}</textarea>
					{formhelp note="Enter a description of the package you are uploading. This should contain a short summary of what the package does and what it provides."}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="Package Type" for="type_id"}
				{forminput}
					{html_options options=$packageTypes values=$packageTypes name=type_id id=type_id selected=$editInfo.type_id}{required}
					{formhelp note="Please select the package type that best fits your needs. If there is no such type, please enter one below."}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="License" for="license_id"}
				{forminput}
					{html_options options=$licenseTypes values=$licenseTypes name=license_id id=license_id selected=$editInfo.license_id}{required}
					{formhelp note='Please select the license that best fits your needs. If there is no such license, please enter one below. Need some <a href="http://creativecommons.org/license/">help finding the correct license</a>?'}
				{/forminput}
			</div>

			<div class="control-group">
				<label class="checkbox">
					<input type="checkbox" name="is_service" id="is_service" {if $editInfo.is_service == 'y'}checked="checked"{/if} />Service
					{formhelp note="If your package is a service, please check this box."}
				</label>
			</div>

			<div class="submit">
				<input type="submit" class="btn" name="process_package" value="{tr}Store Package Details{/tr}" />
			</div>

			<h3>{tr}Enter new Package type if needed{/tr}</h3>
			<p class="help">{tr}If there is no package type that fits your needs, please enter the details here.{/tr}</p>

			<div class="control-group">
				{formlabel label="Package Type" for="type_new_title"}
				{forminput}
					<input type="text" name="type_new_title" id="type_new_title" size="30" />
				{/forminput}
			</div>

			<h3>{tr}Enter new License if needed{/tr}</h3>
			<p class="help">{tr}If there is no license that fits your needs, please enter the details here.{/tr}</p>

			<div class="control-group">
				{formlabel label="License Title" for="license_new_title"}
				{forminput}
					<input type="text" name="license_new_title" id="license_new_title" size="30" />
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="License URL" for="license_new_url"}
				{forminput}
					<input type="text" name="license_new_url" id="license_new_url" size="50" />
				{/forminput}
			</div>

			{required legend=1}
		{/form}
	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}
