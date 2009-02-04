<?php
if(!ADSENSEM_VERSION) {die();}

class Template_Settings
{
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		global $_adsensem_networks;
?>			<div class="wrap">
				<h2>Advertising Settings</h2>
<form method="post" action="">
<table class="form-table">
<tr valign="top">
<td>
<table style="">
<tr valign="top">
<th scope="row">Optimisation</th>
<td>
	<fieldset>
		<legend class="hidden">Optimisation</legend>
		<label for="adsensem-openx-market">
			<input name="adsensem-openx-market" type="checkbox" id="adsensem-openx-market" value="1"  checked="checked" />
			Optimise ads on OpenX Market by default
		</label>
		<br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<label for="adsensem-openx-market-cpm">
			Default eCPM:
			<input name="adsensem-openx-market-cpm" type="text" id="adsensem-openx-market-cpm" value="0.20" size="5"  />
		</label>
		<br /><br />
	</fieldset>
	<span style="font-size:x-small;color:gray;">Display options determine where on your website your ads will appear.</span>
</td>
</tr>
</table>
</td>
</tr>
<tr valign="top">
<td><span style="font-size:x-small;color:gray;">Display options determine where on your website your ads will appear.</span></td>
</tr>
</table>
<!--
<tr valign="top">
<th scope="row">Optimisation</th>
<td>
	<fieldset>
		<legend class="hidden">Optimisation</legend>
		<label for="adsensem-openx-market">
			<input name="adsensem-openx-market" type="checkbox" id="adsensem-openx-market" value="1"  checked="checked" />
			Optimise ads on OpenX Market by default
		</label>
		<br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<label for="adsensem-openx-market-cpm">
			Default eCPM:
			<input name="adsensem-openx-market-cpm" type="text" id="adsensem-openx-market-cpm" value="0.20" size="5"  />
		</label>
		<br /><br />
	</fieldset>
	<span style="font-size:x-small;color:gray;">Display options determine where on your website your ads will appear.</span>
</td>
</tr>
</table>
-->
</form>
<?php
	}
}
?>