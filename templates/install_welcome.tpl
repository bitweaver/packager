<h1>bitweaver Package Manager</h1>

{form legend="Begin the update process"}
	<input type="hidden" name="step" value="{$next_step}" />

	<p>
		Welcome to the new and improved bitweaver package manager. Using this
		package manager will allow you to download packages from our central
		repository and apply the install or upgrade process easily.
	</p>
	<p>
		Initial steps before beginning the actual upgrade stages.
	</p>
	<p class="warning">
		<strong>Make a Backup</strong><br />
		You should have a spare dump of your database before you run this. (Of
		course, you already have a nightly cron job making nightly backups and
		scp'ing them to another host? right? right.)
	</p>
	<p class="warning">
		<strong>Do a Trial Run first</strong><br />
		You should run a trial upgrade on an offline server, personal machine,
		etc. before you do this on your live site.
	</p>
	{if $max_execution_time}
		<p class="warning">
			<strong>Upgrades can take a long time</strong><br />
			We tried to override the max_execution_time setting in your php.ini
			to ensure enough time but on some systems this does not work. If
			you get a blank page with a non-functional site as a result, the
			execution time might be the reason.<br />
			The value we are trying to set max_execution_time to is 86400.
			However, your value of {$max_execution_time} cannot be overridden
			on your system. If you run into problems with the upgrade process
			and you think this might be problem, please consult the
			<a class="external" href="http://us2.php.net/manual/en/ref.info.php#ini.max-execution-time">php manual</a>
			on how to change the value.
		</p>
	{/if}
	{if $dbWarning}
		<p class="warning">
			{$dbWarning}
		</p>
	{/if}
	<p>
		We have done our best to make sure all situations are handled. However,
		your install might have the one case we haven't run into yet.
	</p>


	<div class="row submit">
		<input type="submit" name="fSubmitWelcome" value="{$warningSubmit|default:"Let the Packaging begin!"}" />
	</div>
{/form}
