<h1>Upgrade Packages</h1>

{form}
	<input type="hidden" name="step" value="{$next_step}" />

	{foreach from=$upgradeList item=upgrade key=packager_id}
	<h3>{$upgrade.package|capitalize}</h3>
		{foreach from=$failedcommands.$packager_id item=commands}
			{foreach from=$commands.errors item=command key=idx}
				<p class="error"><strong>{$command}</strong>: <br/>{$commands.failedcommands.$idx}</p>
			{/foreach}
		{foreachelse}
			<p class="success">All database operations competed successfully</p>
		{/foreach}
	{/foreach}

	<div class="submit">
		<input type="submit" name="verify" value="Verify Install" />
	</div>
{/form}
