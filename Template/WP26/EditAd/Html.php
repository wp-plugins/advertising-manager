<?php
require_once(ADS_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Html extends Template_EditAd
{
	function Template_EditAd_Html()
	{
		// Call parent first!
		parent::Template_EditAd();
	}
	
	function displaySectionCode($ad)
	{
?><div style="font-size:small;">
	<label for="html_before">HTML Code Before</label><br />
	<textarea rows="1" cols="60" name="adsensem-html-before" id="adsensem-html-before" onfocus="this.select();"><?php echo $ad->p['html-before']; ?></textarea><img class="default_note" title="[Default] <?php echo $ad->d('html-before'); ?>"><br />
<?php if ($mode != 'edit_network'): ?>
	<label for="ad_code">Ad Code</label><br />
	<textarea rows="6" cols="60" name="adsensem-code" id="adsensem-code" onfocus="this.select();"><?php echo $ad->p['code']; ?></textarea><br />
<?php endif; ?>
	<label for="html_after">HTML Code After</label><br />
	<textarea rows="1" cols="60" name="adsensem-html-after" id="adsensem-html-after" onfocus="this.select();"><?php echo $ad->p['html-after']; ?></textarea><img class="default_note" title="[Default] <?php echo $ad->d('html-after'); ?>"><br />
</div>
<br />
<span style="font-size:x-small;color:gray;">Place the ad code that you received from your ad network in the 'Code' section.  If you would like to display HTML code either before or after the ad, place it in the 'HTML Before' or 'HTML After' box.</span>
<?php
	}
}
?>