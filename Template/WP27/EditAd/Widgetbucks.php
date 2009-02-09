<?php
require_once(ADS_PATH . '/Template/WP27/EditAd.php');

class Template_EditAd_Widgetbucks extends Template_EditAd
{
	function Template_EditAd_Widgetbucks()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Account
		add_meta_box('adsensem_account', __('Account Details', 'adsensem'), array(get_class($this), 'displaySectionAccount'), 'adsensem', 'advanced', 'high');
		// Remove Format Meta box
		remove_meta_box('adsensem_format', 'adsensem', 'normal');
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="adsensem-account-id">Account ID:</label>
	<input type="text" name="adsensem-account-id" style="width:200px" id="adsensem-account-id" value="<?php echo $ad->get('account-id'); ?>" />
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;">Enter the Account ID which corresponds to this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.</span>
<?php
	}
}
?>