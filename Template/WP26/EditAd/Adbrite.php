<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Adbrite extends Template_EditAd
{
	function Template_EditAd_Adbrite()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('advman_colors', __('Colors', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'normal');
		// Account
		add_meta_box('advman_account', __('Account Details', 'advman'), array(get_class($this), 'displaySectionAccount'), 'advman', 'advanced', 'high');
	}
	
	function displaySectionFormat($ad)
	{
		$format = $ad->get('adformat');
		
?><table id="advman-settings-ad_format">
	<tr id="advman-form-adformat">
		<td class="advman_label"><label for="advman-adformat"><?php _e('Format:'); ?></label></td>
		<td>
			<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-default" label="Default">
					<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>">
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes.</span>
<?php
	}
	
	function displaySectionColors($ad)
	{
?><table id="advman-settings-colors" width="100%">
<tr>
	<td>
		<table>
		<tr>
			<td class="advman_label"><label for="advman-color-border"><?php _e('Border:'); ?></label></td>
			<td>#<input name="advman-color-border" onChange="advman_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get('color-border'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-border'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-title"><?php _e('Title:'); ?></label></td>
			<td>#<input name="advman-color-title" onChange="advman_update_color(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get('color-title'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-title'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
			<td>#<input name="advman-color-bg" onChange="advman_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get('color-bg'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-bg'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-text"><?php _e('Text:'); ?></label></td>
			<td>#<input name="advman-color-text" onChange="advman_update_color(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get('color-text'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-text'); ?>"></td>
		</tr>
		</table>
	</td>
	<td>
		<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->get('color-bg', true), ENT_QUOTES); ?>;">
		<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->get('color-border', true), ENT_QUOTES); ?>" class="linkunit-wrapper">
		<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->get('color-title', true), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
			<b><u>Linked Title</u></b><br /></div>
		<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->get('color-text', true), ENT_QUOTES); ?>; padding: 2px;" class="text">
			Advertiser's ad text here<br /></div>
		<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
			&nbsp;<u>Ads by <?php echo $ad->networkName; ?></u></div>
		</div>
	</td>
</tr>
</table>
<br />
<span style="font-size:x-small;color:gray;"><?php echo __('Select one of the ad format sizes supported by', 'advman') . ' ' . $ad->networkName; ?>.</span>
<?php
	}
	
	function displaySectionAccount($ad)
	{
?><table>
<tr>
	<td><label for="advman-slot"><?php _e('Account ID:', 'advman'); ?></label></td>
	<td><input type="text" name="advman-account-id" style="width:200px" id="advman-account-id" value="<?php echo $ad->get('account-id'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-slot"><?php _e('Slot ID:', 'advman'); ?></label></td>
	<td><input type="text" name="advman-slot" style="width:200px" id="advman-slot" value="<?php echo $ad->get('slot'); ?>" /></td>
</tr>
</table>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('The Account ID is your ID for your Adbrite account.  The Slot ID is the ID of this specific ad slot.', 'advman'); ?></span>
<?php
	}
}
?>