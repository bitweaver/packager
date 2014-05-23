<h1>Database Update</h1>
{form legend="Update your local Database"}
	<input type="hidden" name="step" value="{$next_step}" />

	{formfeedback hash=$feedback}

	<p>
		Before you can continue, you need to download an up-to-date version of the
		available packages. I will try to connect to the bitweaver server, download
		the database data and insert it into your server database.
	</p>

	{if $smarty.request.db_download}
		<p>
			You can confirm that the files are present and do not contain
			malicious code by inspecting them here. Once you press the button
			below, the data in these xml files will be inserted into your
			database.
		</p>

		<h3>The following files were found on your server</h3>
		<ul>
			{foreach from=$xmlFiles key=table item=file}
				<li>
					{if $xmlFiles.$table}
						{booticon iname="icon-ok"   iexplain=OK} <a href="{$gInstall->getXmlUrl($table)}">{$table}.xml</a>
						{assign var=ok value=1}
					{else}
						{biticon iname=dialog-error iexplain=Missing} {$table}.xml
					{/if}
				</li>
			{/foreach}
		</ul>

		{if !$ok}
			{formfeedback error="There are no xml files on your server. We can not proceed with the package installation process unless you know that you have a valid database."}
		{/if}

		<div class="submit">
			<input type="submit" class="btn btn-default" name="skip" value="Skip Database Update" />
			<input type="submit" class="btn btn-default" name="db_update" value="Update Database" />
		</div>

		<div class="control-group">
			{forminput}
				<label><input type="checkbox" name="debug" value="true" /> Debug mode</label>
				{formhelp note="This will display SQL statements."}
			{/forminput}
		</div>
	{else}
		<div class="submit">
			<input type="submit" class="btn btn-default" name="skip" value="Skip Database Update" />
			<input type="submit" class="btn btn-default" name="db_download" value="Download Database" />
		</div>
	{/if}
{/form}
