<?php
require_once(ADVMAN_PATH . '/Template/WP27/EditAd.php');

class Template_EditAd_Shoppingads extends Template_EditAd
{
	function Template_EditAd_Shoppingads()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('advman_colors', __('Colors', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'normal');
		// Link Options
		add_meta_box('advman_style', __('Style', 'advman'), array(get_class($this), 'displaySectionStyle'), 'advman', 'normal');
		// Account
		add_meta_box('advman_account', __('Account Details', 'advman'), array(get_class($this), 'displaySectionAccount'), 'advman', 'advanced', 'high');
		// Campaign
		add_meta_box('advman_campaign', __('Campaign', 'advman'), array(get_class($this), 'displaySectionCampaign'), 'advman', 'advanced');
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="advman-account-id"><?php _e('Account ID:'); ?></label>
	<input type="text" name="advman-account-id" style="width:200px" id="advman-account-id" value="<?php echo $ad->get('account-id'); ?>" />
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Enter the Account ID which corresponds to this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
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
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> <?php _e('120 x 600 Skyscraper', 'advman'); ?></option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> <?php _e('160 x 600 Wide Skyscraper', 'advman'); ?></option>
					<option<?php echo ($format == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> <?php _e('120 x 240 Vertical Banner', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($format == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> <?php _e('336 x 280 Large Rectangle', 'advman'); ?></option>
					<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> <?php _e('300 x 250 Medium Rectangle', 'advman'); ?></option>
					<option<?php echo ($format == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> <?php _e('250 x 250 Square', 'advman'); ?></option>
					<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> <?php _e('180 x 150 Small Rectangle'); ?></option>
					<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> <?php _e('125 x 125 Button', 'advman'); ?></option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>">
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionStyle($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td class="advman-label"><label for="advman-attitude"><?php _e('Attitude:'); ?></label></td>
	<td>
		<select name="advman-attitude" id="advman-attitude">
			<option value=""> <?php _e('Use Default', 'advman'); ?></option>
			<option<?php echo ($ad->get('attitude') == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
			<option<?php echo ($ad->get('attitude') == 'no' ? ' selected="selected"' : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
		</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('attitude'); ?>">
	</td>
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
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Enter the Slot ID which corresponds to this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionColors($ad)
	{
?>	<table id="advman-settings-colors" width="100%">
	<tr>
		<td>
			<table>
			<tr>
				<td class="advman_label"><label for="advman-color-border"><?php _e('Border:'); ?></label></td>
				<td>#<input name="advman-color-border" onChange="advman_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get('color-border'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-border'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
				<td>#<input name="advman-color-bg" onChange="advman_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get('color-bg'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-bg'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-title"><?php _e('Title:'); ?></label></td>
				<td>#<input name="advman-color-title" onChange="advman_update_color(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get('color-title'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-title'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-price"><?php _e('Text:'); ?></label></td>
				<td>#<input name="advman-color-text" onChange="advman_update_color(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get('color-text'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-text'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-footer"><?php _e('Link:'); ?></label></td>
				<td>#<input name="advman-color-link" onChange="advman_update_color(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get('color-link'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-link'); ?>"></td>
			</tr>
			</table>
		</td>
		<td>
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->get('color-bg', true), ENT_QUOTES); ?>;">
			<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->get('color-border', true), ENT_QUOTES); ?>" class="linkunit-wrapper">
			<img src="<?php echo get_bloginfo('wpurl') . '/wp-content/plugins/advertising-manager/shoppingads.png'?>" style="width:60%">
			<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->get('color-title', true), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
				<b><u>Description of Product</u></b><br /></div>
			<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->get('color-text', true), ENT_QUOTES); ?>; padding: 2px;" class="text">
				Current Bid: $5.00<br /></div>
			<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($ad->get('color-link', true), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
				&nbsp;<span style="text-decoration:underline"><?php printf(__('Ads by %s', 'advman'), $ad->networkName); ?></span></div>
			</div>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionCampaign($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td class="advman-label"><label for="advman-campaign"><?php _e('Camapign:'); ?></label></td>
	<td><input type="text" name="advman-campaign" style="width:200px" id="advman-campaign" value="<?php echo $ad->get('campaign'); ?>" /></td>
</tr>
<tr>
	<td class="advman-label"><label for="advman-keywords"><?php _e('Keywords:'); ?></label></td>
	<td><input type="text" name="advman-keywords" style="width:200px" id="advman-keywords" value="<?php echo $ad->get('keywords'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('This field corresponds to the campaign and keywords associated with this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
<?php
	}
}
?>