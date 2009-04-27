<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Adpinion extends Template_EditAd
{
	function Template_EditAd_Adpinion()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Account
		add_meta_box('advman_account', __('Account Details', 'advman'), array(get_class($this), 'displaySectionAccount'), 'advman', 'advanced', 'high');
	}
	
	function displaySectionFormat($ad)
	{
		$format = $ad->get_property('adformat');
		
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
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> <?php _e('120 x 600 Skyscraper', 'advman'); ?></option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> <?php _e('160 x 600 Wide Skyscraper', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> <?php _e('300 x 250 Medium Rectangle', 'advman'); ?></option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('adformat'); ?>">
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="advman-slot"><?php _e('Account ID:'); ?></label></td>
	<td><input type="text" name="advman-account-id" style="width:200px" id="advman-account-id" value="<?php echo $ad->get_property('account-id'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php printf(__('The Account ID is your ID for your %s account.', 'advman'), $ad->network_name); ?></span>
<?php
	}
}
?>