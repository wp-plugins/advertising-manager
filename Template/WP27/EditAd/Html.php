<?php
require_once(ADS_PATH . '/Template/WP27/EditAd.php');

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
	<label for="html_before"><?php _e('HTML Code Before'); ?></label><br />
	<textarea rows="1" cols="60" name="adsensem-html-before" id="adsensem-html-before" onfocus="this.select();"><?php echo $ad->get('html-before'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('html-before'); ?>"><br /><br />
	<label for="ad_code"><?php _e('Ad Code'); ?></label><br />
	<textarea rows="6" cols="60" name="adsensem-code" id="adsensem-code" onfocus="this.select();"><?php echo $ad->get('code'); ?></textarea><br /><br />
	<label for="html_after"><?php _e('HTML Code After'); ?></label><br />
	<textarea rows="1" cols="60" name="adsensem-html-after" id="adsensem-html-after" onfocus="this.select();"><?php echo $ad->get('html-after'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('html-after'); ?>"><br /><br />
</div>
<br />
<span style="font-size:x-small;color:gray;">Place the ad code that you received from your ad network in the 'Code' section.  If you would like to display HTML code either before or after the ad, place it in the 'HTML Before' or 'HTML After' box.</span>
<?php
	}
}
?>