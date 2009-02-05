<?php
require_once(ADS_PATH . '/Template/WP27/EditAd.php');

class Template_EditAd_Shoppingads extends Template_EditAd
{
	function Template_EditAd_Shoppingads()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('adsensem_colors', __('Colors', 'adsensem'), array(get_class($this), 'displaySectionColors'), 'adsensem', 'normal');
		// Link Options
		add_meta_box('adsensem_style', __('Style', 'adsensem'), array(get_class($this), 'displaySectionStyle'), 'adsensem', 'normal');
		// Account
		add_meta_box('adsensem_account', __('Account Details', 'adsensem'), array(get_class($this), 'displaySectionAccount'), 'adsensem', 'advanced', 'high');
		// Campaign
		add_meta_box('adsensem_campaign', __('Campaign', 'adsensem'), array(get_class($this), 'displaySectionCampaign'), 'adsensem', 'advanced');
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="adsensem-account-id">Account ID:</label>
	<input type="text" name="adsensem-account-id" style="width:200px" id="adsensem-account-id" value="<?php echo $ad->account_id(); ?>" />
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;">Enter the Account ID which corresponds to this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.</span>
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
					<option value=""> Use Default</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->p['adformat'] == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($ad->p['adformat'] == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($ad->p['adformat'] == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->p['adformat'] == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($ad->p['adformat'] == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($ad->p['adformat'] == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($ad->p['adformat'] == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($ad->p['adformat'] == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($ad->p['adformat'] == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($ad->p['adformat'] == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
					<option<?php echo ($ad->p['adformat'] == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="[Default] <?php echo $ad->d('adformat'); ?>">
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
	<td class="adsensem-label"><label for="adsensem-attitude">Attitude:</label></td>
	<td>
		<select name="adsensem-attitude" id="adsensem-attitude">
			<option value=""> Use Default</option>
			<option<?php echo ($ad->p['attitude'] == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> Yes</option>
			<option<?php echo ($ad->p['attitude'] == 'no' ? ' selected="selected"' : ''); ?> value="no"> No</option>
		</select>
			<img class="default_note" title="[Default] <?php echo $ad->d('attitude'); ?>">
	</td>
</tr>
<tr>
	<td class="adsensem-label"><label for="adsensem-new-window">New Window:</label></td>
	<td>
		<select name="adsensem-new-window" id="adsensem-new-window">
			<option value=""> Use Default</option>
			<option<?php echo ($ad->p['new-window'] == 'yes' ? ' selected="selected"' : ''); ?> value="yes"> Yes</option>
			<option<?php echo ($ad->p['new-window'] == 'no' ? ' selected="selected"' : ''); ?> value="no"> No</option>
		</select>
			<img class="default_note" title="[Default] <?php echo $ad->d('new-window'); ?>">
	</td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;">Enter the Slot ID which corresponds to this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.</span>
<?php
	}
	
	function displaySectionColors($ad)
	{
?>	<table id="adsensem-settings-colors" width="100%">
	<tr>
		<td>
			<table>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-border">Border:</label></td>
				<td>#<input name="adsensem-color-border" onChange="adsensem_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->p['color-border']; ?>" /></td>
				<td><img class="default_note" title="[Default] <?php echo $ad->d('color-border'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-description">Description:</label></td>
				<td>#<input name="adsensem-color-description" onChange="adsensem_update_color(this,'ad-color-description','description');" size="6" value="<?php echo $ad->p['color-description']; ?>" /></td>
				<td><img class="default_note" title="[Default] <?php echo $ad->d('color-description'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-bg">Background:</label></td>
				<td>#<input name="adsensem-color-bg" onChange="adsensem_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->p['color-bg']; ?>" /></td>
				<td><img class="default_note" title="[Default] <?php echo $ad->d('color-bg'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-price">Price:</label></td>
				<td>#<input name="adsensem-color-price" onChange="adsensem_update_color(this,'ad-color-price','price');" size="6" value="<?php echo $ad->p['color-price']; ?>" /></td>
				<td><img class="default_note" title="[Default] <?php echo $ad->d('color-price'); ?>"></td>
			</tr>
			<tr>
				<td class="adsensem_label"><label for="adsensem-color-footer">Footer:</label></td>
				<td>#<input name="adsensem-color-footer" onChange="adsensem_update_color(this,'ad-color-footer','footer');" size="6" value="<?php echo $ad->p['color-footer']; ?>" /></td>
				<td><img class="default_note" title="[Default] <?php echo $ad->d('color-footer'); ?>"></td>
			</tr>
			</table>
		</td>
		<td>
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->pd('color-bg'), ENT_QUOTES); ?>;">
			<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->pd('color-border'), ENT_QUOTES); ?>" class="linkunit-wrapper">
			<img src="<?php echo get_bloginfo('wpurl') . '/wp-content/plugins/advertising-manager/shoppingads.png'?>" style="width:60%">
			<div id="ad-color-description" style="color: #<?php echo htmlspecialchars($ad->pd('color-desciption'), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
				<b><u>Description of Product</u></b><br /></div>
			<div id="ad-color-price" style="color: #<?php echo htmlspecialchars($ad->pd('color-price'), ENT_QUOTES); ?>; padding: 2px;" class="text">
				Current Bid: $5.00<br /></div>
			<div id="ad-color-footer" style="color: #<?php echo htmlspecialchars($ad->pd('color-footer'), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
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
	<td class="adsensem-label"><label for="adsensem-campaign">Camapign:</label></td>
	<td><input type="text" name="adsensem-campaign" style="width:200px" id="adsensem-campaign" value="<?php echo $ad->p['campaign']; ?>" /></td>
</tr>
<tr>
	<td class="adsensem-label"><label for="adsensem-keywords">Keywords:</label></td>
	<td><input type="text" name="adsensem-keywords" style="width:200px" id="adsensem-keywords" value="<?php echo $ad->p['keywords']; ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;">This field corresponds to the campaign and keywords associated with this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.</span>
<?php
	}
}
?>