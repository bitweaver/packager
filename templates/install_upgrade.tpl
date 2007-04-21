<h1>Upgrade Packages</h1>

{form}
	<input type="hidden" name="step" value="{$next_step}" />

	{formfeedback hash=$feedback}

	<p>The following packages will be upgraded</p>
	<ul>
		{foreach from=$upgradeList item=upgrade}
			<li>
				<input type="hidden" value="{$upgrade.packager_id}" name="upgrades[]" />
				{$upgrade.package}<br />
				{assign var=installed value=$gInstall->getInstalledVersion($upgrade.package)}
				{$installed.version} {$installed.status} &raquo; {$upgrade.version} {$upgrade.status}
				{if $upgrade.schema_files}
					<p class="warning">
						The following files are going to be included in the upgrade process:<br />
						{foreach from=$upgrade.schema_files item=schema}
							{$schema}<br />
						{/foreach}
					</p>
				{else}
					<p class="success">
						No schema changes since your last upgrade. The upgrade of this package is complete.
					</p>
				{/if}
			</li>
		{/foreach}
	</ul>

	<div class="submit">
		<input type="submit" name="upgrade" value="Perform Upgrade" />
	</div>

	<div class="row">
		{forminput}
			<label><input type="checkbox" name="debug" value="true" /> Debug mode</label>
			{formhelp note="This will display SQL statements."}
		{/forminput}
	</div>
{/form}
