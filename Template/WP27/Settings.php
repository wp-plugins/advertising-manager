<?php
if(!ADVMAN_VERSION) {die();}

class Template_Settings
{
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		global $_adsensem_networks;
		
		$action = isset($_POST['adsensem-action']) ? $_POST['adsensem-action'] : '';
?><div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php _e('Ad Settings', 'advman'); ?></h2>

<?php if ($action == 'save') : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
<?php endif; ?>

<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
<input type="hidden" name="adsensem-mode" id="adsensem-mode" value="settings" />
<input type="hidden" name="adsensem-action" id="adsensem-action" value="save" />
<input type="hidden" name="adsensem-action-target" id="adsensem-action-target" />

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Optimization', 'advman'); ?></th>
<td> <fieldset><legend class="hidden"><?php _e('Optimization', 'advman'); ?></legend><label for="users_can_register">
<input name="adsensem-openx-market" type="checkbox" id="adsensem-openx-market" value="1"<?php echo ($_adsensem['settings']['openx-market']) ? ' checked="checked"' : ''; ?> />
<?php _e('Optimize ads on OpenX Market by default', 'advman'); ?></label>
</fieldset>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Default floor price:', 'advman'); ?> <input type="text" name="adsensem-openx-market-cpm" value="<?php echo $_adsensem['settings']['openx-market-cpm']; ?>" class="small-text" /><br />
<span class="setting-description"><?php _e('By enabling the OpenX Market, an alternative ad may show if it will make you more money than the existing ad.  The floor price is the eCPM (revenue per 1000 ads) that your ad network pays.', 'advman'); ?></span>
</td>
</tr>
</table>


<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', 'advman'); ?>" />
</p>
</form>

</div>

<?php
	}
}
?>