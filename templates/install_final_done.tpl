<h1>Upgrade Complete</h1>

{form action="`$smarty.const.BIT_ROOT_URL`index.php" legend="Upgrade has been completed sucessfully"}
	<p class="success">
		{booticon iname="icon-ok"  ipackage="icons"  iexplain=success}
		Your system is ready for use now.
	</p>

	<p>
		Please report any problems you had with the package manager to the
		bitweaver development team. This package is still in its infancy and
		will advance as development on it continues.
	</p>

	<div class="control-group submit">
		<input type="submit" class="btn" size="20" value="Enter Your bitweaver Site" />
	</div>
{/form}
