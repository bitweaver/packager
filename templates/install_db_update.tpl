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

		<ul>
			{foreach from=$gInstall->mTables item=file}
				<li><a href="{$gInstall->getXmlUrl($file)}">{$file}.xml</a></li>
			{/foreach}
			<li><a href="{$gInstall->getXmlUrl('kernel_config')}">kernel_config.xml</a></li>
		</ul>

		<div class="submit">
			<input type="submit" name="skip" value="Skip Database Update" />
			<input type="submit" name="db_update" value="Update Database" />
		</div>
	{else}
		<div class="submit">
			<input type="submit" name="skip" value="Skip Database Update" />
			<input type="submit" name="db_download" value="Download Database" />
		</div>
	{/if}
{/form}
