<?php
if(!ADVMAN_VERSION) {die();}

class Template_Settings
{
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_advman;
		global $_advman_networks;
		
		$action = isset($_POST['advman-action']) ? $_POST['advman-action'] : '';
		$oxMarket = !empty($_advman['settings']['openx-market']) ? $_advman['settings']['openx-market'] : false;
		$oxUpdates = !empty($_advman['settings']['openx-sync']) ? $_advman['settings']['openx-sync'] : false;
		$oxCpm = !empty($_advman['settings']['openx-market-cpm']) ? $_advman['settings']['openx-market-cpm'] : '0.20';
		
?><div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php _e('Ad Settings', 'advman'); ?></h2>

<?php if ($action == 'save') : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
<?php endif; ?>

<form action="" method="post" id="advman-form" enctype="multipart/form-data">
<input type="hidden" name="advman-mode" id="advman-mode" value="settings" />
<input type="hidden" name="advman-action" id="advman-action" value="save" />
<input type="hidden" name="advman-target" id="advman-target" />

<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="advman-openx-market"><?php _e('Optimization', 'advman'); ?></label>
	</th>
	<td>
		<fieldset>
			<legend class="hidden"><?php _e('Optimization', 'advman'); ?></legend>
			<label for="users_can_register"><input name="advman-openx-market" type="checkbox" id="advman-openx-market" value="1"<?php echo $oxMarket ? ' checked="checked"' : ''; ?> /> <?php _e('Optimize ads on OpenX Market by default', 'advman'); ?></label>
		</fieldset>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Default floor price:', 'advman'); ?> <input type="text" name="advman-openx-market-cpm" value="<?php echo $oxCpm; ?>" class="small-text" /><br />
		<span class="setting-description"><?php _e('By enabling the OpenX Market, an alternative ad may show if it will make you more money than the existing ad.  The floor price is the eCPM (revenue per 1000 ads) that your ad network pays.', 'advman'); ?></span>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Updates', 'advman'); ?></th>
	<td>
		<fieldset>
			<legend class="hidden"><?php _e('Updates', 'advman'); ?></legend>
			<label for="users_can_register"><input name="advman-openx-sync" type="checkbox" id="advman-openx-sync" value="1"<?php echo $oxUpdates ? ' checked="checked"' : ''; ?> /> <?php _e('Check for updates', 'advman'); ?></label>
		</fieldset>
		<span class="setting-description"><?php _e('Checking for updates will keep you informed of not only updates, but of any offers from advertisers who want to buy your ad space.', 'advman'); ?></span>
	</td>
</tr>
</tbody>
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