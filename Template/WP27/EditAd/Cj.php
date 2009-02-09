<?php
require_once(ADS_PATH . '/Template/WP27/EditAd.php');

class Template_EditAd_Cj extends Template_EditAd
{
	function Template_EditAd_Cj()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Account
		add_meta_box('adsensem_account', __('Account Details', 'adsensem'), array(get_class($this), 'displaySectionAccount'), 'adsensem', 'advanced', 'high');
		// Link Options
		add_meta_box('adsensem_linkoptions', __('Link Options', 'adsensem'), array(get_class($this), 'displaySectionLinkOptions'), 'adsensem', 'advanced', 'high');
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="adsensem-slot">Account ID:</label></td>
	<td><input type="text" name="adsensem-account-id" style="width:200px" id="adsensem-account-id" value="<?php echo $ad->p['account-id']; ?>" /></td>
</tr>
<tr>
	<td><label for="adsensem-slot">Slot ID:</label></td>
	<td><input type="text" name="adsensem-slot" style="width:200px" id="adsensem-slot" value="<?php echo $ad->p['slot']; ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;">The Account ID is your ID for your Commission Junction account.  The Slot ID is the ID of this specific ad slot.</span>
<?php
	}
	
	function displaySectionLinkOptions($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td class="adsensem-label"><label for="adsensem-slot">Alt Text:</label></td>
	<td><input type="text" name="adsensem-alt-text" style="width:300px" id="adsensem-alt-text" value="<?php echo $ad->p['alt-text']; ?>" /></td>
</tr>
<tr>
	<td class="adsensem-label"><label for="adsensem-new-window">New Window:</label></td>
	<td>
		<select name="adsensem-new-window" id="adsensem-new-window">
			<option value=""> <?php _e('Use Default', 'advman'); ?></option>
			<option<?php echo ($ad->p['new-window'] == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
			<option<?php echo ($ad->p['new-window'] == 'no' ? ' selected="selected"' : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
		</select>
			<img class="default_note" title="<?php _e('[Default]', 'advman') . ' ' . $ad->d('new-window'); ?>">
	</td>
</tr>
<tr>
	<td class="adsensem-label"><label for="adsensem-status">Status Text:</label></td>
	<td><input type="text" name="adsensem-status" style="width:300px" id="adsensem-status" value="<?php echo $ad->p['status']; ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;">The Alt Text displays in place of the ad in some cases.  The status sets the browser toolbar message when the cursor is hovering over the ad.  New Window will open a new browser window when the ad is clicked.</span>
<?php
	}
	
	function displaySectionFormat($ad)
	{
?>	<table id="adsensem-settings-ad_format">
	<tr id="adsensem-form-adformat">
		<td class="adsensem_label"><label for="adsensem-adformat">Format:</label></td>
		<td>
			<select name="adsensem-adformat" id="adsensem-adformat" onchange="adsensem_form_update(this);">
				<optgroup id="adsensem-optgroup-default" label="Default">
					<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->p['adformat'] == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($ad->p['adformat'] == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($ad->p['adformat'] == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
					<option<?php echo ($ad->p['adformat'] == '150x50' ? ' selected="selected"' : ''); ?> value="150x50"> 150 x 50 Half Banner</option>
					<option<?php echo ($ad->p['adformat'] == '120x90' ? ' selected="selected"' : ''); ?> value="120x90"> 120 x 90 Button</option>
					<option<?php echo ($ad->p['adformat'] == '120x60' ? ' selected="selected"' : ''); ?> value="120x60"> 120 x 60 Button</option>
					<option<?php echo ($ad->p['adformat'] == '83x31' ? ' selected="selected"' : ''); ?> value="83x31"> 83 x 31 Micro Bar</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->p['adformat'] == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($ad->p['adformat'] == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($ad->p['adformat'] == '240x400' ? ' selected="selected"' : ''); ?> value="240x400"> 240 x 400 Vertical Rectangle</option>
					<option<?php echo ($ad->p['adformat'] == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($ad->p['adformat'] == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($ad->p['adformat'] == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($ad->p['adformat'] == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($ad->p['adformat'] == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> 200 x 200 Small Square</option>
					<option<?php echo ($ad->p['adformat'] == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
					<option<?php echo ($ad->p['adformat'] == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-custom" label="Custom">
					<option<?php echo ($ad->p['adformat'] == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php _e('[Default]', 'advman') . ' ' . $ad->d('adformat'); ?>">
		</td>
	</tr>
	<tr id="adsensem-settings-custom">
		<td class="adsensem_label"><label for="adsensem-width">Dimensions:</label></td>
		<td>
			<input name="adsensem-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo ($ad->p['width']); ?>" /> x
			<input name="adsensem-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo ($ad->p['height']); ?>" /> px
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes. If your ad size is not one of the standard sizes, select 'Custom' and fill in your size.</span>
<?php
	}
}
?>