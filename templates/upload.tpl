{strip}
<div class="edit packager">
	<div class="header">
		<h1>{tr}Upload Version{/tr}</h1>
	</div>

	<div class="body">
		{if $packageList}
			{form enctype="multipart/form-data" legend="Upload Package"}
				{formfeedback hash=$feedback}
				<p class="help">{tr}This is where you can upload new packages and allow other users to easily download and install your work using the packager. Please fill in the information below as accurately as possible since that will allow other users decide on what packages they want to install more easily. If you are unsure on how to fill in any of the form fields or for a more detailed explanation of the individual points, please visit the <a href="http://www.bitweaver.org/wiki/PackagerPackage">PackagerPackage</a> webpage.{/tr}</p>

				<div class="row">
					{formlabel label="Package" for="package"}
					{forminput}
						<select name="package" id="package">
							{foreach from=$packageList item=package}
								<option value="{$package.package}" {if $smarty.request.package == $package.package}selected="selected"{/if}>{$package.package}</option>
							{/foreach}
						</select>
						{formhelp note="Please choose the package you are uploading a new package for. If you have not created a package entry yet, please do so now." link="packager/edit_package.php/Create Package"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Upload Package" for="package_upload"}
					{forminput}
						<input type="file" name="package_upload" id="package_upload" />{required}
						{formhelp note="Please pick the package you want to upload. Please only upload archives of the type: .tar.gz, .tar.bz2, .zip or .rar"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Security Release" for="is_security_release"}
					{forminput}
						<input type="checkbox" name="is_security_release" id="is_security_release" {if $smarty.request.is_security_release}checked="checked"{/if} />
						{formhelp note="Please check this if this is a security release. This will warn other users that they should update to at least this version to avoid security issues."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Changelog" for="changelog"}
					{forminput}
						<textarea rows="10" cols="50" name="changelog" id="changelog">{$smarty.request.changelog|escape}</textarea>
						{formhelp note="If you have a changelog copy it into the textbox. Please use the following format:<br />+ New Feature<br />- Feature Removed<br />! Bug Fixed."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Dependencies" for="dependencies"}
					{forminput}
						<textarea rows="5" cols="50" name="dependencies" id="dependencies">{$smarty.request.dependencies|escape}</textarea>
						{formhelp note="Please enter any specific package dependencies here. Each dependency on a new line using the format:<br />package min_version max_version"}
					{/forminput}
				</div>

				<div class="submit">
					<input type="submit" name="process_package" value="{tr}Upload Package{/tr}" />
				</div>

				{required legend=1}
			{/form}
		{else}
			<p class="warning">
				{tr}You don't seem to have provided any package details yet. Please create a package first.{/tr}
				<br />
				{smartlink ititle="Create Package" ifile="edit_package.php"}
			</p>
		{/if}
	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}
