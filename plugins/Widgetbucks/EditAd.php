<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Widgetbucks extends Template_EditAd
{
	function Template_EditAd_Widgetbucks()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Account
		add_meta_box('advman_account', __('Account Details', 'advman'), array(get_class($this), 'displaySectionAccount'), 'advman', 'advanced', 'high');
		// Remove Format Meta box
		remove_meta_box('advman_format', 'advman', 'default');
	}
	
	function displaySectionAccount($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="advman-slot"><?php _e('Slot ID:'); ?></label>
	<input type="text" name="advman-slot" style="width:200px" id="advman-slot" value="<?php echo $ad->get_property('slot'); ?>" />
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Enter the Slot ID which corresponds to this ad.  This should automatically be filled in when you import your tag.  If copying ads, you will need to enter this manually.', 'advman'); ?></span>
<?php
	}
}
?>