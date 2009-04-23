<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Ypn extends Template_EditAd
{
	function Template_EditAd_Ypn()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('advman_colors', __('Ad Appearance', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'default');
		// Account
		add_meta_box('advman_account', __('Account Details', 'advman'), array(get_class($this), 'displaySectionAccount'), 'advman', 'advanced', 'high');
	}
	
	function displaySectionColors($ad)
	{
?><table id="advman-settings-colors" width="100%">
<tr>
	<td>
		<table>
		<tr>
			<td class="advman_label"><label for="advman-color-border"><?php _e('Border:'); ?></label></td>
			<td>#<input name="advman-color-border" onChange="advman_update_ad(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get('color-border'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-border'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-title"><?php _e('Title:'); ?></label></td>
			<td>#<input name="advman-color-title" onChange="advman_update_ad(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get('color-title'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-title'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
			<td>#<input name="advman-color-bg" onChange="advman_update_ad(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get('color-bg'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-bg'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-text"><?php _e('Text:'); ?></label></td>
			<td>#<input name="advman-color-text" onChange="advman_update_ad(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get('color-text'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-text'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-link"><?php _e('URL:'); ?></label></td>
			<td>#<input name="advman-color-link" onChange="advman_update_ad(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get('color-link'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-link'); ?>"></td>
		</tr>
		</table>
	</td>
	<td>
		<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->get('color-bg', true), ENT_QUOTES); ?>;">
		<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->get('color-border', true), ENT_QUOTES); ?>" class="linkunit-wrapper">
		<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->get('color-title', true), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
			<b><u><?php _e('Linked Title', 'advman'); ?></u></b><br /></div>
		<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->get('color-text', true), ENT_QUOTES); ?>; padding: 2px;" class="text">
			<?php _e('Advertiser\'s ad text here', 'advman'); ?><br /></div>
		<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($ad->get('color-link', true), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
			<?php _e('www.advertiser-url.com', 'advman'); ?><br /></div>
		<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
			&nbsp;<u><?php printf(__('Ads by %s', 'advman'), $ad->getNetworkName()); ?></u></div>
		</div>
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
	<td><label for="advman-account-id"><?php _e('Account ID:'); ?></label></td>
	<td><input type="text" name="advman-account-id" style="width:200px" id="advman-account-id" value="<?php echo $ad->get('account-id'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-channel"><?php _e('Channel:'); ?></label></td>
	<td><input type="text" name="advman-channel" style="width:200px" id="advman-channel" value="<?php echo $ad->get('channel'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Enter the Slot ID which corresponds to this ad.  The identifier uniquely identifies the slot.  This information should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
<?php
	}
	
}
?>