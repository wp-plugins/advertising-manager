<?php
require_once(ADVMAN_PATH . '/Template/WP27/EditAd.php');

class Template_EditAd_Adroll extends Template_EditAd
{
	function Template_EditAd_Adroll()
	{
		// Call parent first!
		parent::Template_EditAd();
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
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes.</span>
<?php
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="adsensem-account-id"><?php _e('Account ID:'); ?></label></td>
	<td><input type="text" name="adsensem-account-id" style="width:200px" id="adsensem-account-id" value="<?php echo $ad->get('account-id'); ?>" /></td>
</tr>
<tr>
	<td><label for="adsensem-slot"><?php _e('Slot ID:'); ?></label></td>
	<td><input type="text" name="adsensem-slot" style="width:200px" id="adsensem-slot" value="<?php echo $ad->get('slot'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('The Account ID is your ID for your Adroll account.  The Slot ID is the ID of this specific ad slot.', 'advman'); ?></span>
<?php
	}
}
?>