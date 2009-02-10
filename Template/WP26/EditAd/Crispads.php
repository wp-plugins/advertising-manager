<?php
require_once(ADS_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Crispads extends Template_EditAd
{
	function Template_EditAd_Crispads()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Account
		add_meta_box('adsensem_account', __('Account Details', 'adsensem'), array(get_class($this), 'displaySectionAccount'), 'adsensem', 'advanced', 'high');
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="adsensem-slot">Slot ID:</label></td>
	<td><input type="text" name="adsensem-slot" style="width:200px" id="adsensem-slot" value="<?php echo $ad->get('slot'); ?>" /></td>
</tr>
<tr>
	<td><label for="adsensem-slot">Identifier:</label></td>
	<td><input type="text" name="adsensem-slot" style="width:200px" id="adsensem-identifier" value="<?php echo $ad->get('identifier'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Enter the Slot ID which corresponds to this ad.  The identifier uniquely identifies the slot.  This information should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionFormat($ad)
	{
		$format = $ad->get('adformat');
		
?>	<table id="adsensem-settings-ad_format">
	<tr id="adsensem-form-adformat">
		<td class="adsensem_label"><label for="adsensem-adformat">Format:</label></td>
		<td>
			<select name="adsensem-adformat" id="adsensem-adformat" onchange="adsensem_form_update(this);">
<?php if ($mode != 'edit_network'): ?>
				<optgroup id="adsensem-optgroup-default" label="Default">
					<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				</optgroup>
<?php endif; ?>
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($format == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($format == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
					<option<?php echo ($format == '150x50' ? ' selected="selected"' : ''); ?> value="150x50"> 150 x 50 Half Banner</option>
					<option<?php echo ($format == '120x90' ? ' selected="selected"' : ''); ?> value="120x90"> 120 x 90 Button</option>
					<option<?php echo ($format == '120x60' ? ' selected="selected"' : ''); ?> value="120x60"> 120 x 60 Button</option>
					<option<?php echo ($format == '83x31' ? ' selected="selected"' : ''); ?> value="83x31"> 83 x 31 Micro Bar</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($format == '240x400' ? ' selected="selected"' : ''); ?> value="240x400"> 240 x 400 Vertical Rectangle</option>
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
				<optgroup id="adsensem-optgroup-custom" label="Custom">
					<option<?php echo ($format == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
				</optgroup>
			</select>
		</td>
		<td>
<?php if ($mode != 'edit_network'): ?>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>">
<?php endif; ?>
		</td>
	</tr>
	<tr id="adsensem-settings-custom">
		<td class="adsensem_label"><label for="adsensem-width">Dimensions:</label></td>
		<td>
			<input name="adsensem-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('width')); ?>" /> x
			<input name="adsensem-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('height')); ?>" /> px
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes. If your ad size is not one of the standard sizes, select 'Custom' and fill in your size.</span>
<?php
	}
}
?>