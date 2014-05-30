<h1>Preliminary checks</h1>
<p>
	These checks are supposed to help you determine if the settings on your
	server allow the bitweaver package manager to do it's thing.  We have a
	script to help you set the permissions if they should not be set correctly
	or you can try to adjust them yourself.
	<br />
	If you wish to know more about the package manager, please visit the online help at:
	<a href="http://doc.bitweaver.org/wiki/index.php?page=PackagerPackage">PackagerPackage</a>
</p>

{form}
	<input type="hidden" name="step" value="{$next_step}" />

	<h2>Tests</h2>
	{if $smarty.request.perform_checks}
		<table class="table data">
			<caption>Progress Report</caption>
			<tr>
				<th></th>
				<th>Check</th>
				<th>Message</th>
			</tr>
			{foreach from=$pp item=check key=action}
				<tr class="{cycle values="odd,even" advance=0}">
					{if $check.result == 'error'}
						{assign var=error value=1}
					{elseif $check.result == 'warning'}
						{assign var=warning value=1}
					{/if}
					<td rowspan="2">{biticon iname=large/dialog-`$check.result` iexplain="Error"}</td>
					<td>{$action|capitalize}</td>
					<td>{$check.note}</td>
				</tr>
				<tr class="{cycle}">
					<td colspan="2">
						<code>{$check.code}</code>
						{if $check.resolve && $check.code}<br />{/if}
						{$check.resolve}
					</td>
				</tr>
			{/foreach}
		</table>

		{if $error}
			<p class="error">Something seems to have gone wrong. Please visit our online documentation to check on how to proceed.</p>
		{elseif $warning}
			<p class="warning">There seems to have been some minor problems. You can try ignoring this or you can visit our online documentation to see if there is help regarding this issue.</p>
		{else}
			<p class="success">Everything seems to have gone well. Using the package manager will probably work well.</p>
		{/if}

		<div class="form-group submit">
			{if $error}
				<input type="submit" class="btn btn-default" name="perform_checks" value="Repeat Tests" />
			{/if}
			<input type="submit" class="btn btn-default" name="advance" value="Continue" />
		</div>
	{else}
		<table class="table data">
			<caption>Progress Report</caption>
			<tr>
				<th></th>
				<th>Check</th>
				<th>Message</th>
			</tr>
			{foreach from=$pp item=check key=action}
				<tr class="{cycle values="odd,even" advance=0}">
					<td>{biticon iname=large/help-contents iexplain="Not checked yet"}</td>
					<td>{$action|capitalize}</td>
					<td>{$check.note}</td>
				</tr>
			{/foreach}
		</table>

		<div class="form-group submit">
			<input type="submit" class="btn btn-default" name="perform_checks" value="Perform Tests" />
		</div>
	{/if}
{/form}
