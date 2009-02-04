<?php
require_once(ADS_PATH . '/Template/WP27/EditNetwork.php');

class Template_EditNetwork_Adroll extends Template_EditNetwork
{
	function Template_EditNetwork_Adroll()
	{
		// Call parent first!
		parent::Template_EditNetwork();
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
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->d('adformat') == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($ad->d('adformat') == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
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
}
?>