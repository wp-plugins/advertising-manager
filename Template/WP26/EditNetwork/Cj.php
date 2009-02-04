<?php
require_once(ADS_PATH . '/Template/WP26/EditNetwork.php');

class Template_EditNetwork_Cj extends Template_EditNetwork
{
	function Template_EditNetwork_Cj()
	{
		// Call parent first!
		parent::Template_EditNetwork();
		// Link Options
		add_meta_box('adsensem_linkoptions', __('Link Options', 'adsensem'), array(get_class($this), 'displaySectionLinkOptions'), 'adsensem', 'advanced');
		// Colors
		add_meta_box('adsensem_colors', __('Colors', 'adsensem'), array(get_class($this), 'displaySectionColors'), 'adsensem', 'normal');
	}
	
	function displaySectionLinkOptions($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td class="adsensem-label"><label for="adsensem-slot">Alt Text:</label></td>
	<td><input type="text" name="adsensem-alt-text" style="width:300px" id="adsensem-alt-text" value="<?php echo $ad->d('alt-text'); ?>" /></td>
</tr>
<tr>
	<td class="adsensem-label"><label for="adsensem-new-window">New Window:</label></td>
	<td>
		<select name="adsensem-new-window" id="adsensem-new-window">
			<option<?php echo ($ad->d('new-window') == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> Yes</option>
			<option<?php echo ($ad->d('new-window') == 'no' ? ' selected="selected"' : ''); ?> value="no"> No</option>
		</select>
	</td>
</tr>
<tr>
	<td class="adsensem-label"><label for="adsensem-status">Status Text:</label></td>
	<td><input type="text" name="adsensem-status" style="width:300px" id="adsensem-status" value="<?php echo $ad->d('status'); ?>" /></td>
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
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->d('adformat') == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($ad->d('adformat') == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($ad->d('adformat') == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
					<option<?php echo ($ad->d('adformat') == '150x50' ? ' selected="selected"' : ''); ?> value="150x50"> 150 x 50 Half Banner</option>
					<option<?php echo ($ad->d('adformat') == '120x90' ? ' selected="selected"' : ''); ?> value="120x90"> 120 x 90 Button</option>
					<option<?php echo ($ad->d('adformat') == '120x60' ? ' selected="selected"' : ''); ?> value="120x60"> 120 x 60 Button</option>
					<option<?php echo ($ad->d('adformat') == '83x31' ? ' selected="selected"' : ''); ?> value="83x31"> 83 x 31 Micro Bar</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->d('adformat') == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($ad->d('adformat') == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($ad->d('adformat') == '240x400' ? ' selected="selected"' : ''); ?> value="240x400"> 240 x 400 Vertical Rectangle</option>
					<option<?php echo ($ad->d('adformat') == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($ad->d('adformat') == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($ad->d('adformat') == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($ad->d('adformat') == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($ad->d('adformat') == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> 200 x 200 Small Square</option>
					<option<?php echo ($ad->d('adformat') == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
					<option<?php echo ($ad->d('adformat') == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-custom" label="Custom">
					<option<?php echo ($ad->d('adformat') == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
				</optgroup>
			</select>
		</td>
		<td>
		</td>
	</tr>
	<tr id="adsensem-settings-custom">
		<td class="adsensem_label"><label for="adsensem-width">Dimensions:</label></td>
		<td>
			<input name="adsensem-width" size="5" title="Custom width for this unit." value="<?php echo ($ad->d('width')); ?>" /> x
			<input name="adsensem-height" size="5" title="Custom width for this unit." value="<?php echo ($ad->d('height')); ?>" /> px
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes. If your ad size is not one of the standard sizes, select 'Custom' and fill in your size.</span>
<?php
	}
}
?>