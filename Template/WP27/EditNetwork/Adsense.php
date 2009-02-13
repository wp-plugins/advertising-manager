<?php
require_once(ADVMAN_PATH . '/Template/WP27/EditNetwork.php');

class Template_EditNetwork_Adsense extends Template_EditNetwork
{
	function Template_EditNetwork_Adsense()
	{
		// Call parent first!
		parent::Template_EditNetwork();
		// Colors
		add_meta_box('advman_colors', __('Colors', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'normal');
	}
	
	function displaySectionFormat($ad)
	{
?>	<table id="advman-settings-ad_format">
<?php
		if ($ad->get_default('adtype') == 'slot') {
?>			<input type="hidden" name="advman-adtype" value="slot">
<?php
		} else {
?>
	<tr id="advman-form-adtype">
		<td class="advman_label"><label for="advman-adtype"><?php _e('Ad Type:'); ?></label></td>
		<td>
			<select name="advman-adtype" id="advman-adtype" onchange="advman_form_update(this);">
				<option<?php echo ($type == 'ad' ? ' selected="selected"' : ''); ?> value="ad"> <?php _e('Ad Unit', 'advman'); ?></option>
				<option<?php echo ($type == 'link' ? ' selected="selected"' : ''); ?> value="link"> <?php _e('Link Unit', 'advman'); ?></option>
				<option<?php echo ($type == 'ref_text' ? ' selected="selected"' : ''); ?> value="ref_text"> <?php _e('Text Referral', 'advman'); ?></option>
				<option<?php echo ($type == 'ref_image' ? ' selected="selected"' : ''); ?> value="ref_image"> <?php _e('Image Referral', 'advman'); ?></option>
			</select>
		</td>
		<td>
		</td>
	</tr>
<?php
		}
?>	<tr id="advman-form-ad-format"<?php echo (($ad->get_default('adtype') == 'ad' || $ad->get_default('adtype') == 'slot') ? '' : ' style="display:none"'); ?>>
		<td class="advman_label"><label for="advman-adformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->get_default('adformat') == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> <?php _e('728 x 90 Leaderboard', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> <?php _e('468 x 60 Banner', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> <?php _e('234 x 60 Half Banner', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->get_default('adformat') == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> <?php _e('120 x 600 Skyscraper', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> <?php _e('160 x 600 Wide Skyscraper', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> <?php _e('120 x 240 Vertical Banner', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($ad->get_default('adformat') == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> <?php _e('336 x 280 Large Rectangle', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> <?php _e('300 x 250 Medium Rectangle', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> <?php _e('250 x 250 Square', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> <?php _e('200 x 200 Small Square', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> <?php _e('180 x 150 Small Rectangle'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> <?php _e('125 x 125 Button', 'advman'); ?></option>
				</optgroup>
			</select>
		</td>
		<td>
		</td>
	</tr>
	<tr id="advman-form-link-format"<?php echo (($ad->get_default('adtype') == 'link') ? '' : ' style="display:none"'); ?>>
		<td class="advman_label"><label for="advman-linkformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="advman-linkformat" id="advman-linkformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->get_default('adformat') == '728x15' ? ' selected="selected"' : ''); ?> value="728x15"> <?php _e('728 x 15', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '468x15' ? ' selected="selected"' : ''); ?> value="468x15"> <?php _e('468 x 15', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($ad->get_default('adformat') == '200x90' ? ' selected="selected"' : ''); ?> value="200x90"> <?php _e('200 x 90', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '180x90' ? ' selected="selected"' : ''); ?> value="180x90"> <?php _e('180 x 90', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '160x90' ? ' selected="selected"' : ''); ?> value="160x90"> <?php _e('160 x 90', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '120x90' ? ' selected="selected"' : ''); ?> value="120x90"> <?php _e('120 x 90', 'advman'); ?></option>
				</optgroup>
			</select>
		</td>
	</tr>
	<tr id="advman-form-ref_image-format"<?php echo (($ad->get_default('adtype') == 'ref_image') ? '' : ' style="display:none"'); ?>>
		<td class="advman_label"><label for="advman-referralformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="advman-referralformat" id="advman-referralformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->get_default('adformat') == '110x32' ? ' selected="selected"' : ''); ?> value="110x32"> <?php _e('110 x 32', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '120x60' ? ' selected="selected"' : ''); ?> value="120x60"> <?php _e('120 x 60', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '180x60' ? ' selected="selected"' : ''); ?> value="180x60"> <?php _e('180 x 60', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> <?php _e('468 x 60', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($ad->get_default('adformat') == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> <?php _e('125 x 125', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->get_default('adformat') == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> <?php _e('120 x 240', 'advman'); ?></option>
				</optgroup>
			</select>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the ad format sizes supported by Google Adsense.</span>
<?php
	}
	
	function displaySectionColors($ad)
	{
		if ($ad->get_default('adtype') == 'slot') {
			global $_advman_networks;
?>	<p class="advman-label">Colors must be modified within <a href="<?php echo $ad->url; ?>" target="_new">Google Adsense</a> for this tag type.</p>
<?php
		} else {
?>	<table id="advman-settings-colors" width="100%">
	<tr>
		<td>
			<table>
			<tr>
				<td class="advman_label"><label for="advman-color-border"><?php _e('Border:'); ?></label></td>
				<td>#<input name="advman-color-border" onChange="advman_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get_default('color-border'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-title"><?php _e('Title:'); ?></label></td>
				<td>#<input name="advman-color-title" onChange="advman_update_color(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get_default('color-title'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
				<td>#<input name="advman-color-bg" onChange="advman_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get_default('color-bg'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-text"><?php _e('Text:'); ?></label></td>
				<td>#<input name="advman-color-text" onChange="advman_update_color(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get_default('color-text'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-link"><?php _e('URL:'); ?></label></td>
				<td>#<input name="advman-color-link" onChange="advman_update_color(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get_default('color-link'); ?>" /></td>
			</tr>
			</table>
		</td>
		<td>
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo ($ad->get_default('color-bg')) ? $ad->get_default('color-bg') : 'FFFFFF'; ?>;">
				<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo ($ad->get_default('color-border')) ? $ad->get_default('color-border') : 'FF0000'; ?>" class="linkunit-wrapper">
					<div id="ad-color-title" style="color: #<?php echo ($ad->get_default('color-title')) ? $ad->get_default('color-title') : '00FFFF'; ?>; font: 11px verdana, arial, sans-serif; padding: 2px;"><b><u>Linked Title</u></b><br /></div>
					<div id="ad-color-text" style="color: #<?php echo ($ad->get_default('color-text')) ? $ad->get_default('color-text') : '000000'; ?>; padding: 2px;" class="text">Advertiser's ad text here<br /></div>
					<div id="ad-color-link" style="color: #<?php echo ($ad->get_default('color-link')) ? $ad->get_default('color-link') : '008000'; ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">www.advertiser-url.com<br /></div>
					<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">&nbsp;<u>Ads by Google Adsense</u></div>
				</div>
			</div>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Enter the color of each part of the ad.  Colors must be expressed as RGB values.</span>
<?php
		}
	}
}
?>