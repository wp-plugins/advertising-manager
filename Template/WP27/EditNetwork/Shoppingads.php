<?php
require_once(ADVMAN_PATH . '/Template/WP27/EditNetwork.php');

class Template_EditNetwork_Shoppingads extends Template_EditNetwork
{
	function Template_EditNetwork_Shoppingads()
	{
		// Call parent first!
		parent::Template_EditNetwork();
		// Colors
		add_meta_box('advman_colors', __('Colors', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'normal');
		// Link Options
		add_meta_box('advman_style', __('Style', 'advman'), array(get_class($this), 'displaySectionStyle'), 'advman', 'normal');
		// Campaign
		add_meta_box('advman_campaign', __('Campaign', 'advman'), array(get_class($this), 'displaySectionCampaign'), 'advman', 'advanced');
	}
	
	function displaySectionFormat($ad)
	{
?>	<table id="advman-settings-ad_format">
	<tr id="advman-form-adformat">
		<td class="advman_label"><label for="advman-adformat"><?php _e('Format:'); ?></label></td>
		<td>
			<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->get_default('adformat') == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($ad->get_default('adformat') == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($ad->get_default('adformat') == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->get_default('adformat') == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($ad->get_default('adformat') == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($ad->get_default('adformat') == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($ad->get_default('adformat') == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($ad->get_default('adformat') == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
				</optgroup>
			</select>
		</td>
		<td>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes.</span>
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
			<option<?php echo ($ad->get_default('attitude') == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
			<option<?php echo ($ad->get_default('attitude') == 'no' ? ' selected="selected"' : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
		</select>
	</td>
</tr>
<tr>
	<td class="advman-label"><label for="advman-new-window"><?php _e('New Window:'); ?></label></td>
	<td>
		<select name="advman-new-window" id="advman-new-window">
			<option<?php echo ($ad->get_default('new-window') == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
			<option<?php echo ($ad->get_default('new-window') == 'no' ? ' selected="selected"' : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
		</select>
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
				<td>#<input name="advman-color-border" onChange="advman_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get_default('color-border'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
				<td>#<input name="advman-color-bg" onChange="advman_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get_default('color-bg'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-text"><?php _e('Title:'); ?></label></td>
				<td>#<input name="advman-color-title" onChange="advman_update_color(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get_default('color-title'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-price"><?php _e('Text:'); ?></label></td>
				<td>#<input name="advman-color-text" onChange="advman_update_color(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get_default('color-text'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-link"><?php _e('Link:'); ?></label></td>
				<td>#<input name="advman-color-link" onChange="advman_update_color(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get_default('color-link'); ?>" /></td>
			</tr>
			</table>
		</td>
		<td>
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->get_default('color-bg'), ENT_QUOTES); ?>;">
			<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->get_default('color-border'), ENT_QUOTES); ?>" class="linkunit-wrapper">
			<img src="<?php echo get_bloginfo('wpurl') . '/wp-content/plugins/advertising-manager/shoppingads.png'?>" style="width:60%">
			<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->get_default('color-title'), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
				<b><u>Description of Product</u></b><br /></div>
			<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->get_default('color-text'), ENT_QUOTES); ?>; padding: 2px;" class="text">
				Current Bid: $5.00<br /></div>
			<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($ad->get_default('color-link'), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
				&nbsp;<span style="text-decoration:underline">Ads by <?php echo $ad->networkName; ?></span></div>
			</div>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the ad format sizes supported by Google Adsense.  Enter multiple channels separated by '+' signs.</span>
<?php
	}
	
	function displaySectionCampaign($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td class="advman-label"><label for="advman-campaign"><?php _e('Camapign:'); ?></label></td>
	<td><input type="text" name="advman-campaign" style="width:200px" id="advman-campaign" value="<?php echo $ad->get_default('campaign'); ?>" /></td>
</tr>
<tr>
	<td class="advman-label"><label for="advman-keywords"><?php _e('Keywords:'); ?></label></td>
	<td><input type="text" name="advman-keywords" style="width:200px" id="advman-keywords" value="<?php echo $ad->get_default('keywords'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('This field corresponds to the campaign and keywords associated with this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
<?php
	}
}
?>