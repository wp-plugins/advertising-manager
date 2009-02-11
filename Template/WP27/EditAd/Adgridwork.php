<?php
require_once(ADS_PATH . '/Template/WP27/EditAd.php');

class Template_EditAd_Adgridwork extends Template_EditAd
{
	function Template_EditAd_Adgridwork()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('adsensem_colors', __('Colors', 'adsensem'), array(get_class($this), 'displaySectionColors'), 'adsensem', 'normal');
		// Account
		add_meta_box('adsensem_account', __('Account Details', 'adsensem'), array(get_class($this), 'displaySectionAccount'), 'adsensem', 'advanced', 'high');
	}
	
	function displaySectionFormat($ad)
	{
		$format = $ad->get('adformat');
		
?>	<table id="adsensem-settings-ad_format">
	<tr id="adsensem-form-adformat">
		<td class="adsensem_label"><label for="adsensem-adformat"><?php _e('Format:'); ?></label></td>
		<td>
			<select name="adsensem-adformat" id="adsensem-adformat" onchange="adsensem_form_update(this);">
				<optgroup id="adsensem-optgroup-default" label="Default">
					<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '800x90' ? ' selected="selected"' : ''); ?> value="800x90"> 800 x 90 Large Leaderboard</option>
					<option<?php echo ($format == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($format == '600x90' ? ' selected="selected"' : ''); ?> value="600x90"> 600 x 90 Small Leaderboard</option>
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($format == '400x90' ? ' selected="selected"' : ''); ?> value="400x90"> 400 x 90 Tall Banner</option>
					<option<?php echo ($format == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
					<option<?php echo ($format == '200x90' ? ' selected="selected"' : ''); ?> value="200x90"> 200 x 90 Tall Half Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($format == '200x360' ? ' selected="selected"' : ''); ?> value="200x360"> 200 x 360 Wide Half Banner</option>
					<option<?php echo ($format == '200x270' ? ' selected="selected"' : ''); ?> value="200x270"> 200 x 270 Wide Short Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($format == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($format == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($format == '200x180' ? ' selected="selected"' : ''); ?> value="200x180"> 200 x 180 Small Rectangle</option>
					<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
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
?>	<table id="adsensem-settings-colors" width="100%">
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
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->get('color-bg', true), ENT_QUOTES); ?>;">
			<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->get('color-border', true), ENT_QUOTES); ?>" class="linkunit-wrapper">
			<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->get('color-title', true), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
				<b><u>Linked Title</u></b><br /></div>
			<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->get('color-text', true), ENT_QUOTES); ?>; padding: 2px;" class="text">
				Advertiser's ad text here<br /></div>
			<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($ad->get('color-link', true), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
				www.advertiser-url.com<br /></div>
			<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
				&nbsp;<u>Ads by <?php echo $ad->networkName; ?></u></div>
			</div>			
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the ad format sizes supported by Adgridwork.  Enter multiple channels separated by '+' signs.</span>
<?php
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="adsensem-slot"><?php _e('Slot ID:'); ?></label>
	<input type="text" name="adsensem-slot" style="width:200px" id="adsensem-slot" value="<?php echo $ad->get('slot'); ?>" />
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Enter the Slot ID which corresponds to this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
<?php
	}
}
?>