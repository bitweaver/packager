{strip}
<h1>Available Packages</h1>

{form id=form}
	<input type="hidden" name="step" value="{$next_step}" />

	{formfeedback hash=$feedback}

	<div class="row">
		{forminput}
			<script type="text/javascript">/* <![CDATA[ */
				document.write("<label><input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form.id,'upgrades[]','switcher')\" /> Batch (de)select all available Upgrades</label>");
			/* ]]> */</script>
		{/forminput}
	</div>

	{include file=bitpackage:packager/list_packages_inc.tpl}

	<div class="submit">
		<input type="submit" name="selection" value="Submit Selection" />
	</div>
{/form}
{/strip}
