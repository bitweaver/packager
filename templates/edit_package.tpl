{strip}
<div class="edit packager">
	<div class="header">
		<h1>{tr}Create / Edit Package{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Edit Package"}
			{formfeedback hash=$feedback}

			<p class="help">{tr}Here you can describe the package you wish to upload. Please provide accurate information that users who are looking for new packages know what to expect when installing this particular package.{/tr}</p>

			<div class="row">
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

			<div class="row">
				{formlabel label="Package Type" for="package_type"}
				{forminput}
					{html_options options=$packageTypes values=$packageTypes name=package_type selected=$editInfo.package_type}
					{formhelp note="Pick the type that best describes this package."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Package description" for="description"}
				{forminput}
					<textarea rows="5" cols="50" name="description" id="description">{$editInfo.description|escape}</textarea>
					{formhelp note="Enter a description of the package you are uploading. This should contain a short summary of what the package does and what it provides."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="License" for="license"}
				{forminput}
					{html_options options=$licenseTypes values=$licenseTypes name=license_id selected=$editInfo.license_id}{requred}
					{formhelp note="Please select the license that best fits your needs. If there is no such license, please enter one below."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Service" for="is_service"}
				{forminput}
					<input type="checkbox" name="is_service" id="is_service" {if $editInfo.is_service}checked="checked"{/if} />
					{formhelp note="If your package is a service, please check this box."}
				{/forminput}
			</div>

			<div class="submit">
				<input type="submit" name="process_package" value="{tr}Store Package Details{/tr}" />
			</div>

			<h3>{tr}Enter new License if needed{/tr}</h3>
			<p class="help">{tr}If there is no license that fits your needs, please enter the details here.{/tr}</p>

			<div class="row">
				{formlabel label="License Title" for="license_new_title"}
				{forminput}
					<input type="text" name="license_new_title" id="license_new_title" size="10" />
				{/forminput}
			</div>

			<div class="row">
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
