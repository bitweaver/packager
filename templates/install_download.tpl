<h1>Download Packages</h1>

{form}
	{formfeedback hash=$feedback}

	<input type="hidden" name="step" value="{$next_step}" />

	<p class="warning">
		If any of the
		<a href="{$smarty.const.INSTALL_PKG_URL}packager.php?step=1">preliminary tests</a>
		failed, this might result in an unpredicatable outcome.  Please make
		sure the preliminary tests work without errors.
	</p>
	<p>The following packages will be downloaded from the central server extracted and then placed in your bitweaver directory</p>

	<ul>
		{foreach from=$upgradeList item=upgrade}
			<li>
				<input type="hidden" value="{$upgrade.packager_id}" name="upgrades[]" />
				{$upgrade.package}<br />
				{assign var=installed value=$gInstall->getInstalledVersion($upgrade.package)}
				{$installed.version} {$installed.status} &raquo; {$upgrade.version} {$upgrade.status}
			</li>
		{/foreach}
	</ul>

	{if $progressReport}
		<h2>Progress Report</h2>
		<p>These are the stages involved in downloading and preparing the packages for the upgrade<p>
		<ol>
			{foreach from=$stages key=key item=stage}
			<li>{$key|capitalize}: {$stage}</li>
			{/foreach}
		</ol>

		<p>This is the report and what errors occurred at what stage<p>
		<ul>
			{foreach from=$progressReport item=report key=stage}
				<li>
					<strong>{$report.package}</strong><br />
					{if $report.error}
						{formfeedback error=$report.error}
					{else}
						{formfeedback success="Everything seems to have gone well."}
					{/if}
				</li>
			{/foreach}
		</ul>
	{/if}

	{if $errors}
		<div class="form-group">
			{forminput label="checkbox"}
				<input type="checkbox" name="ignore_versions" id="ignore_versions" />Ignore local versions
				{formhelp note="If you check this, it will ignore the versions of all local files. Only check this if you know what you are doing."}
			{/forminput}
		</div>
	{/if}

	<div class="submit">
		{if $errors}
			{if $errors}
				<p class="warning">Due to the errors mentioned above, we do not recommend that you continue. If you know what you are doing or you just want to give it a shot, you can continue at your own risk.</p>
			{/if}
			<input type="submit" class="btn btn-default" name="download" value="Repeat download" />
			<input type="submit" class="btn btn-default" name="continue" value="Continue" />
		{elseif $progressReport}
			<input type="submit" class="btn btn-default" name="continue" value="Continue" />
		{else}
			<input type="submit" class="btn btn-default" name="download" value="Begin Download" />
		{/if}
	</div>
{/form}
