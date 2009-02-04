<?php
require_once(ADS_PATH . '/Template/WP26/EditNetwork.php');

class Template_EditNetwork_Ypn extends Template_EditNetwork
{
	function Template_EditNetwork_Ypn()
	{
		// Call parent first!
		parent::Template_EditNetwork();
		// Colors
		add_meta_box('adsensem_colors', __('Colors', 'adsensem'), array(get_class($this), 'displaySectionColors'), 'adsensem', 'normal');
	}
	function displaySectionColors($ad)
	{
?><table id="adsensem-settings-colors" width="100%">
<tr>
	<td>
		<table>
		<tr>
			<td class="adsensem_label"><label for="adsensem-color-border">Border:</label></td>
			<td>#<input name="adsensem-color-border" onChange="adsensem_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->d('color-border'); ?>" /></td>
			<td><img class="default_note" title="[Default] <?php echo $ad->d('color-border'); ?>"></td>
		</tr>
		<tr>
			<td class="adsensem_label"><label for="adsensem-color-title">Title:</label></td>
			<td>#<input name="adsensem-color-title" onChange="adsensem_update_color(this,'ad-color-title','title');" size="6" value="<?php echo $ad->d('color-title'); ?>" /></td>
			<td><img class="default_note" title="[Default] <?php echo $ad->d('color-title'); ?>"></td>
		</tr>
		<tr>
			<td class="adsensem_label"><label for="adsensem-color-bg">Background:</label></td>
			<td>#<input name="adsensem-color-bg" onChange="adsensem_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->d('color-bg'); ?>" /></td>
			<td><img class="default_note" title="[Default] <?php echo $ad->d('color-bg'); ?>"></td>
		</tr>
		<tr>
			<td class="adsensem_label"><label for="adsensem-color-text">Text:</label></td>
			<td>#<input name="adsensem-color-text" onChange="adsensem_update_color(this,'ad-color-text','text');" size="6" value="<?php echo $ad->d('color-text'); ?>" /></td>
			<td><img class="default_note" title="[Default] <?php echo $ad->d('color-text'); ?>"></td>
		</tr>
		<tr>
			<td class="adsensem_label"><label for="adsensem-color-link">URL:</label></td>
			<td>#<input name="adsensem-color-link" onChange="adsensem_update_color(this,'ad-color-link','link');" size="6" value="<?php echo $ad->d('color-link'); ?>" /></td>
			<td><img class="default_note" title="[Default] <?php echo $ad->d('color-link'); ?>"></td>
		</tr>
		</table>
	</td>
	<td>
		<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->d('color-bg'), ENT_QUOTES); ?>;">
		<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->d('color-border'), ENT_QUOTES); ?>" class="linkunit-wrapper">
		<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->d('color-title'), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
			<b><u>Linked Title</u></b><br /></div>
		<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->d('color-text'), ENT_QUOTES); ?>; padding: 2px;" class="text">
			Advertiser's ad text here<br /></div>
		<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($ad->d('color-link'), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
			www.advertiser-url.com<br /></div>
		<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
			&nbsp;<u>Ads by <?php echo $ad->networkName; ?></u></div>
		</div>
	</td>
</tr>
</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the ad format sizes supported by <?php echo $ad->networkName; ?>.</span>
<?php
	}
}
?>