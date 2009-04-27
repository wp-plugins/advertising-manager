<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Adsense extends Template_EditAd
{
	function Template_EditAd_Adsense()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('advman_colors', __('Ad Appearance', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'default');
		// Account
		add_meta_box('advman_account', __('Account Details', 'advman'), array(get_class($this), 'displaySectionAccount'), 'advman', 'advanced', 'high');
	}
	
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_advman;
		
		$id = $target;
		$ad = $_advman['ads'][$id];
		$type = $ad->get_property('adtype');
		if ($type == 'ref_image') {
			// Remove Color Meta box
			remove_meta_box('advman_colors', 'advman', 'default');
		}
		
		parent::display($target);
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="advman-slot"><?php _e('Account ID:'); ?></label></td>
	<td><input type="text" name="advman-account-id" style="width:200px" id="advman-account-id" value="<?php echo $ad->get_property('account-id'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-slot"><?php _e('Partner ID:'); ?></label></td>
	<td><input type="text" name="advman-partner" style="width:200px" id="advman-partner" value="<?php echo $ad->get_property('partner'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-slot"><?php _e('Slot ID:'); ?></label></td>
	<td><input type="text" name="advman-slot" style="width:200px" id="advman-slot" value="<?php echo $ad->get_property('slot'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-slot"><?php _e('Channel:'); ?></label></td>
	<td><input type="text" name="advman-channel" style="width:200px" id="advman-channel" value="<?php echo $ad->get_property('channel'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php printf(__('The Account ID is your ID for your %s account.', 'advman'), $ad->network_name); ?> <?php _e('The Partner ID is the ID for a partner revenue sharing account, usually your blog hosting provider.  Note that a Partner ID does not necessarily mean that your partner is sharing revenues.  Google Adsense will notify you if this is the case.', 'advman'); ?> <?php _e('The Slot ID is the ID of this specific ad slot.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionFormat($ad)
	{
		$type = $ad->get_property('adtype');
		$format = $ad->get_property('adformat');
		
?>	<table id="advman-settings-ad_format">
<?php if ($type == 'slot') : ?>
		<input type="hidden" name="advman-adtype" value="slot">
<?php else : ?>
	<tr id="advman-form-adtype">
		<td class="advman_label"><label for="advman-adtype"><?php _e('Ad Type:'); ?></label></td>
		<td>
			<select name="advman-adtype" id="advman-adtype" onchange="advman_form_update(this);">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($type == 'ad' ? ' selected="selected"' : ''); ?> value="ad"> <?php _e('Ad Unit', 'advman'); ?></option>
				<option<?php echo ($type == 'link' ? ' selected="selected"' : ''); ?> value="link"> <?php _e('Link Unit', 'advman'); ?></option>
				<option<?php echo ($type == 'ref_text' ? ' selected="selected"' : ''); ?> value="ref_text"> <?php _e('Text Referral', 'advman'); ?></option>
				<option<?php echo ($type == 'ref_image' ? ' selected="selected"' : ''); ?> value="ref_image"> <?php _e('Image Referral', 'advman'); ?></option>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('adtype'); ?>">
		</td>
	</tr>
<?php endif; ?>
	<tr id="advman-form-ad-format"<?php echo (($type == 'ad' || $type == 'slot') ? '' : ' style="display:none"'); ?>>
		<td class="advman_label"><label for="advman-adformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-Default" label="Default">
					<option selected value=""> <?php _e('Use Default', 'advman'); ?></option>
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
					<option<?php echo ($format == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> <?php _e('200 x 200 Small Square', 'advman'); ?></option>
					<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> <?php _e('180 x 150 Small Rectangle'); ?></option>
					<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> <?php _e('125 x 125 Button', 'advman'); ?></option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('adformat'); ?>">
		</td>
	</tr>
	<tr id="advman-form-link-format"<?php echo (($type == 'link') ? '' : ' style="display:none"'); ?>>
		<td class="advman_label"><label for="advman-linkformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="advman-linkformat" id="advman-linkformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-Default" label="Default">
					<option selected value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '728x15' ? ' selected="selected"' : ''); ?> value="728x15"> 728 x 15</option>
					<option<?php echo ($format == '468x15' ? ' selected="selected"' : ''); ?> value="468x15"> 468 x 15</option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($format == '200x90' ? ' selected="selected"' : ''); ?> value="200x90"> 200 x 90</option>
					<option<?php echo ($format == '180x90' ? ' selected="selected"' : ''); ?> value="180x90"> 180 x 90</option>
					<option<?php echo ($format == '160x90' ? ' selected="selected"' : ''); ?> value="160x90"> 160 x 90</option>
					<option<?php echo ($format == '120x90' ? ' selected="selected"' : ''); ?> value="120x90"> 120 x 90</option>
				</optgroup>
			</select>
		</td>
	</tr>
	<tr id="advman-form-ref_image-format"<?php echo (($type == 'ref_image') ? '' : ' style="display:none"'); ?>>
		<td class="advman_label"><label for="advman-referralformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="advman-referralformat" id="advman-referralformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-Default" label="Default">
					<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '110x32' ? ' selected="selected"' : ''); ?> value="110x32"> 110 x 32</option>
					<option<?php echo ($format == '120x60' ? ' selected="selected"' : ''); ?> value="120x60"> 120 x 60</option>
					<option<?php echo ($format == '180x60' ? ' selected="selected"' : ''); ?> value="180x60"> 180 x 60</option>
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60</option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125</option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240</option>
				</optgroup>
			</select>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionColors($ad)
	{
?>
<?php if ($ad->get_property('adtype') == 'slot') :
			global $_advman_networks;
?>	<p class="advman-label">Colors must be modified within <a href="<?php echo $ad->get_network_property('url'); ?>" target="_new">Google Adsense</a> for this tag type.</p>
<?php else: ?>
	<table id="advman-settings-colors" width="100%">
	<tr>
		<td>
			<table>
			<tr>
				<td class="advman_label"><label for="advman-color-border"><?php _e('Border:'); ?></label></td>
				<td>#<input name="advman-color-border" onChange="advman_update_ad(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get_property('color-border'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('color-border'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-title"><?php _e('Title:'); ?></label></td>
				<td>#<input name="advman-color-title" onChange="advman_update_ad(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get_property('color-title'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('color-title'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
				<td>#<input name="advman-color-bg" onChange="advman_update_ad(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get_property('color-bg'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('color-bg'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-text"><?php _e('Text:'); ?></label></td>
				<td>#<input name="advman-color-text" onChange="advman_update_ad(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get_property('color-text'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('color-text'); ?>"></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-link"><?php _e('URL:'); ?></label></td>
				<td>#<input name="advman-color-link" onChange="advman_update_ad(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get_property('color-link'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('color-link'); ?>"></td>
			</tr>
			</table>
		</td>
		<td>
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo ($ad->get_property('color-bg')) ? $ad->get('color-bg') : 'FFFFFF'; ?>;">
				<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo ($ad->get_property('color-border')) ? $ad->get('color-border') : 'FF0000'; ?>" class="linkunit-wrapper">
					<div id="ad-color-title" style="color: #<?php echo ($ad->get_property('color-title')) ? $ad->get('color-title') : '00FFFF'; ?>; font: 11px verdana, arial, sans-serif; padding: 2px;"><b><u><?php _e('Linked Title', 'advman'); ?></u></b><br /></div>
					<div id="ad-color-text" style="color: #<?php echo ($ad->get_property('color-text')) ? $ad->get('color-text') : '000000'; ?>; padding: 2px;" class="text"><?php _e('Advertiser\'s ad text here', 'advman'); ?><br /></div>
					<div id="ad-color-link" style="color: #<?php echo ($ad->get_property('color-link')) ? $ad->get('color-link') : '008000'; ?>; font: 10px verdana, arial, sans-serif; padding: 2px;"><?php _e('www.advertiser-url.com', 'advman'); ?><br /></div>
					<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">&nbsp;<u><?php printf(__('Ads by %s', 'advman'), $ad->network_name); ?></u></div>
				</div>
			</div>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Enter the color of each part of the ad.  Colors must be expressed as RGB values.</span>
<?php endif; ?>
<?php
	}
}
?>