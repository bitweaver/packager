<h1>Database is up to date</h1>
{form legend="Database is up to date"}
	<input type="hidden" name="step" value="{$next_step}" />
	{if $feedback.error}
		<p class="error">There were some problems during data insertion.</p>
	{else}
		<p class="success">The new data has been inserted into your database.</p>
	{/if}
	{formfeedback hash=$feedback}

	<div class="submit">
		<input type="submit" class="btn btn-default" name="continue" value="Continue to Package Selection" />
	</div>
{/form}
