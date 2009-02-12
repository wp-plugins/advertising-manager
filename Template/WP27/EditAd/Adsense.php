<?php
require_once(ADVMAN_PATH . '/Template/WP27/EditAd.php');

class Template_EditAd_Adsense extends Template_EditAd
{
	function Template_EditAd_Adsense()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('adsensem_colors', __('Colors', 'adsensem'), array(get_class($this), 'displaySectionColors'), 'adsensem', 'normal');
		// Account
		add_meta_box('adsensem_account', __('Account Details', 'adsensem'), array(get_class($this), 'displaySectionAccount'), 'adsensem', 'advanced', 'high');
	}
	
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		
		$id = $target;
		$ad = $_adsensem['ads'][$id];
		$type = $ad->get('adtype');
		if ($type == 'ref_image') {
			// Remove Color Meta box
			remove_meta_box('adsensem_colors', 'adsensem', 'normal');
		}
		
		parent::display($target);
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="adsensem-slot"><?php _e('Account ID:'); ?></label></td>
	<td><input type="text" name="adsensem-account-id" style="width:200px" id="adsensem-account-id" value="<?php echo $ad->get('account-id'); ?>" /></td>
</tr>
<tr>
	<td><label for="adsensem-slot"><?php _e('Partner ID:'); ?></label></td>
	<td><input type="text" name="adsensem-partner" style="width:200px" id="adsensem-partner" value="<?php echo $ad->get('partner'); ?>" /></td>
</tr>
<tr>
	<td><label for="adsensem-slot"><?php _e('Slot ID:'); ?></label></td>
	<td><input type="text" name="adsensem-slot" style="width:200px" id="adsensem-slot" value="<?php echo $ad->get('slot'); ?>" /></td>
</tr>
<tr>
	<td><label for="adsensem-slot"><?php _e('Channel:'); ?></label></td>
	<td><input type="text" name="adsensem-channel" style="width:200px" id="adsensem-channel" value="<?php echo $ad->get('channel'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('The Account ID is your ID for your Google Adsense account.  The Partner ID is the ID for a partner revenue sharing account, usually your blog hosting provider.  Note that a Partner ID does not necessarily mean that your partner is sharing revenues.  Google Adsense will notify you if this is the case.  The Slot ID is the ID of this specific ad slot.  All of these items should be filled in upon import.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionFormat($ad)
	{
		$type = $ad->get('adtype');
		$format = $ad->get('adformat');
		
?>	<table id="adsensem-settings-ad_format">
<?php if ($type == 'slot') : ?>
		<input type="hidden" name="adsensem-adtype" value="slot">
<?php else : ?>
	<tr id="adsensem-form-adtype">
		<td class="adsensem_label"><label for="adsensem-adtype"><?php _e('Ad Type:'); ?></label></td>
		<td>
			<select name="adsensem-adtype" id="adsensem-adtype" onchange="adsensem_form_update(this);">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($type == 'ad' ? ' selected="selected"' : ''); ?> value="ad"> Ad Unit</option>
				<option<?php echo ($type == 'link' ? ' selected="selected"' : ''); ?> value="link"> Link Unit</option>
				<option<?php echo ($type == 'ref_text' ? ' selected="selected"' : ''); ?> value="ref_text"> Text Referral</option>
				<option<?php echo ($type == 'ref_image' ? ' selected="selected"' : ''); ?> value="ref_image"> Image Referral</option>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adtype'); ?>">
		</td>
	</tr>
<?php endif; ?>
	<tr id="adsensem-form-ad-format"<?php echo (($type == 'ad' || $type == 'slot') ? '' : ' style="display:none"'); ?>>
		<td class="adsensem_label"><label for="adsensem-adformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="adsensem-adformat" id="adsensem-adformat" onchange="adsensem_form_update(this);">
				<optgroup id="adsensem-optgroup-Default" label="Default">
					<option selected value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($format == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($format == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($format == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($format == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($format == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> 200 x 200 Small Square</option>
					<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
					<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>">
		</td>
	</tr>
	<tr id="adsensem-form-link-format"<?php echo (($type == 'link') ? '' : ' style="display:none"'); ?>>
		<td class="adsensem_label"><label for="adsensem-linkformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="adsensem-linkformat" id="adsensem-linkformat" onchange="adsensem_form_update(this);">
				<optgroup id="adsensem-optgroup-Default" label="Default">
					<option selected value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '728x15' ? ' selected="selected"' : ''); ?> value="728x15"> 728 x 15</option>
					<option<?php echo ($format == '468x15' ? ' selected="selected"' : ''); ?> value="468x15"> 468 x 15</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($format == '200x90' ? ' selected="selected"' : ''); ?> value="200x90"> 200 x 90</option>
					<option<?php echo ($format == '180x90' ? ' selected="selected"' : ''); ?> value="180x90"> 180 x 90</option>
					<option<?php echo ($format == '160x90' ? ' selected="selected"' : ''); ?> value="160x90"> 160 x 90</option>
					<option<?php echo ($format == '120x90' ? ' selected="selected"' : ''); ?> value="120x90"> 120 x 90</option>
				</optgroup>
			</select>
		</td>
	</tr>
	<tr id="adsensem-form-ref_image-format"<?php echo (($type == 'ref_image') ? '' : ' style="display:none"'); ?>>
		<td class="adsensem_label"><label for="adsensem-referralformat"><a href="https://www.google.com/adsense/adformats" target="_new"><?php _e('Format'); ?></a>:</label></td>
		<td>
			<select name="adsensem-referralformat" id="adsensem-referralformat" onchange="adsensem_form_update(this);">
				<optgroup id="adsensem-optgroup-Default" label="Default">
					<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '110x32' ? ' selected="selected"' : ''); ?> value="110x32"> 110 x 32</option>
					<option<?php echo ($format == '120x60' ? ' selected="selected"' : ''); ?> value="120x60"> 120 x 60</option>
					<option<?php echo ($format == '180x60' ? ' selected="selected"' : ''); ?> value="180x60"> 180 x 60</option>
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240</option>
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
?>
<?php if ($ad->get('adtype') == 'slot') :
			global $_adsensem_networks;
?>	<p class="adsensem-label">Colors must be modified within <a href="<?php echo $ad->url; ?>" target="_new">Google Adsense</a> for this tag type.</p>
<?php else: ?>
	<table id="adsensem-settings-colors" width="100%">
	<tr>
		<td>
			<table>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-border"><?php _e('Border:'); ?></label></td>
				<td>#<input name="adsensem-color-border" onChange="adsensem_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get('color-border'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-border'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-title"><?php _e('Title:'); ?></label></td>
				<td>#<input name="adsensem-color-title" onChange="adsensem_update_color(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get('color-title'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-title'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-bg"><?php _e('Background:'); ?></label></td>
				<td>#<input name="adsensem-color-bg" onChange="adsensem_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get('color-bg'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-bg'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-text"><?php _e('Text:'); ?></label></td>
				<td>#<input name="adsensem-color-text" onChange="adsensem_update_color(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get('color-text'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-text'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-link"><?php _e('URL:'); ?></label></td>
				<td>#<input name="adsensem-color-link" onChange="adsensem_update_color(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get('color-link'); ?>" /></td>
				<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-link'); ?>"></td>
			</tr>
			</table>
		</td>
		<td>
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo ($ad->get('color-bg')) ? $ad->get('color-bg') : 'FFFFFF'; ?>;">
				<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo ($ad->get('color-border')) ? $ad->get('color-border') : 'FF0000'; ?>" class="linkunit-wrapper">
					<div id="ad-color-title" style="color: #<?php echo ($ad->get('color-title')) ? $ad->get('color-title') : '00FFFF'; ?>; font: 11px verdana, arial, sans-serif; padding: 2px;"><b><u>Linked Title</u></b><br /></div>
					<div id="ad-color-text" style="color: #<?php echo ($ad->get('color-text')) ? $ad->get('color-text') : '000000'; ?>; padding: 2px;" class="text">Advertiser's ad text here<br /></div>
					<div id="ad-color-link" style="color: #<?php echo ($ad->get('color-link')) ? $ad->get('color-link') : '008000'; ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">www.advertiser-url.com<br /></div>
					<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">&nbsp;<u>Ads by Google Adsense</u></div>
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