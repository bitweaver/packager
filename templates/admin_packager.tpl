{strip}
{form}
	<input type="hidden" name="page" value="{$page}" />

	{legend legend="Packager Settings"}
		<p class="help">
			{tr}Here you specify a package host other than www.bitweaver.org. If you want to set this up as an alternative package host to www.bitweaver.org, you and the client machines all need to set this to the same host.{/tr}
		</p>

		{foreach from=$packagerSettings key=feature item=output}
			<div class="control-group">
				{formlabel label=$output.label for=$feature}
				{forminput}
					{if $output.type == 'checkbox'}
						{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
					{elseif $output.type == 'gallery'}
						{foreach from=$galleryList item=gallery}
							<label><input type="radio" name="{$feature}" value="{$gallery.content_id}" {if $gBitSystem->getConfig($feature) == $gallery.content_id}checked="checked"{/if} /> {$gallery.title|escape}</label><br />
						{foreachelse}
							<p class="norecords">
								{tr}No Galleries Found{/tr}.<br />
								{tr}Please create a gallery and then return to this page to select it.{/tr}
							</p>
						{/foreach}
					{else}
						<input type='text' name="{$feature}" id="{$feature}" size="{if $output.type == 'text'}40{else}5{/if}" value="{$gBitSystem->getConfig($feature)|escape}" /> {$output.unit}
					{/if}
					{formhelp note=$output.note page=$output.page}
				{/forminput}
			</div>
		{/foreach}
	{/legend}

	<div class="control-group submit">
		<input type="submit" class="btn btn-default" name="packager_settings" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
