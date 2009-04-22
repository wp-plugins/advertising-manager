<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Cj extends Template_EditAd
{
	function Template_EditAd_Cj()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Account
		add_meta_box('advman_account', __('Account Details', 'advman'), array(get_class($this), 'displaySectionAccount'), 'advman', 'advanced', 'high');
		// Link Options
		add_meta_box('advman_linkoptions', __('Link Options', 'advman'), array(get_class($this), 'displaySectionLinkOptions'), 'advman', 'advanced', 'high');
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="advman-slot"><?php _e('Account ID:'); ?></label></td>
	<td><input type="text" name="advman-account-id" style="width:200px" id="advman-account-id" value="<?php echo $ad->get('account-id'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-slot"><?php _e('Slot ID:'); ?></label></td>
	<td><input type="text" name="advman-slot" style="width:200px" id="advman-slot" value="<?php echo $ad->get('slot'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php printf(__('The Account ID is your ID for your %s account.', 'advman'), $ad->networkName); ?> <?php _e('The Slot ID is the ID of this specific ad slot.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionLinkOptions($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td class="advman-label"><label for="advman-slot"><?php _e('Alt Text:'); ?></label></td>
	<td><input type="text" name="advman-alt-text" style="width:300px" id="advman-alt-text" value="<?php echo $ad->get('alt-text'); ?>" /></td>
</tr>
<tr>
	<td class="advman-label"><label for="advman-new-window"><?php _e('New Window:'); ?></label></td>
	<td>
		<select name="advman-new-window" id="advman-new-window">
			<option value=""> <?php _e('Use Default', 'advman'); ?></option>
			<option<?php echo ($ad->get('new-window') == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
			<option<?php echo ($ad->get('new-window') == 'no' ? ' selected="selected"' : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
		</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('new-window'); ?>">
	</td>
</tr>
<tr>
	<td class="advman-label"><label for="advman-status"><?php _e('Status Text:'); ?></label></td>
	<td><input type="text" name="advman-status" style="width:300px" id="advman-status" value="<?php echo $ad->get('status'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('The Alt Text displays in place of the ad in some cases.  The status sets the browser toolbar message when the cursor is hovering over the ad.  New Window will open a new browser window when the ad is clicked.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionFormat($ad)
	{
		$format = $ad->get('adformat');
		
?>	<table id="advman-settings-ad_format">
	<tr id="advman-form-adformat">
		<td class="advman_label"><label for="advman-adformat"><?php _e('Format:'); ?></label></td>
		<td>
			<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-default" label="Default">
					<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> <?php _e('728 x 90 Leaderboard', 'advman'); ?></option>
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> <?php _e('468 x 60 Banner', 'advman'); ?></option>
					<option<?php echo ($format == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> <?php _e('234 x 60 Half Banner', 'advman'); ?></option>
					<option<?php echo ($format == '150x50' ? ' selected="selected"' : ''); ?> value="150x50"> 150 x 50 Half Banner</option>
					<option<?php echo ($format == '120x90' ? ' selected="selected"' : ''); ?> value="120x90"> 120 x 90 Button</option>
					<option<?php echo ($format == '120x60' ? ' selected="selected"' : ''); ?> value="120x60"> 120 x 60 Button</option>
					<option<?php echo ($format == '83x31' ? ' selected="selected"' : ''); ?> value="83x31"> 83 x 31 Micro Bar</option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> <?php _e('120 x 600 Skyscraper', 'advman'); ?></option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> <?php _e('160 x 600 Wide Skyscraper', 'advman'); ?></option>
					<option<?php echo ($format == '240x400' ? ' selected="selected"' : ''); ?> value="240x400"> 240 x 400 Vertical Rectangle</option>
					<option<?php echo ($format == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> <?php _e('120 x 240 Vertical Banner', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($format == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> <?php _e('336 x 280 Large Rectangle', 'advman'); ?></option>
					<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> <?php _e('300 x 250 Medium Rectangle', 'advman'); ?></option>
					<option<?php echo ($format == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> <?php _e('250 x 250 Square', 'advman'); ?></option>
					<option<?php echo ($format == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> <?php _e('200 x 200 Small Square', 'advman'); ?></option>
					<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> <?php _e('180 x 150 Small Rectangle'); ?></option>
					<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> <?php _e('125 x 125 Button', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-custom" label="Custom">
					<option<?php echo ($format == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>">
		</td>
	</tr>
	<tr id="advman-settings-custom">
		<td class="advman_label"><label for="advman-width"><?php _e('Dimensions:'); ?></label></td>
		<td>
			<input name="advman-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('width')); ?>" /> x
			<input name="advman-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('height')); ?>" /> px
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes. If your ad size is not one of the standard sizes, select Custom and fill in your size.', 'advman'); ?></span>
<?php
	}
}
?>