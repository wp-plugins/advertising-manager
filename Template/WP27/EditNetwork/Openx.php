<?php
require_once(ADVMAN_PATH . '/Template/WP27/EditNetwork.php');

class Template_EditNetwork_Openx extends Template_EditNetwork
{
	function Template_EditAd_Openx()
	{
		// Call parent first!
		parent::Template_EditNetwork();
		// Remove Format Meta box
		remove_meta_box('advman_format', 'advman', 'normal');
	}
}
?>