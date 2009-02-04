<?php
require_once(ADS_PATH . '/Template/WP26/EditNetwork.php');

class Template_EditNetwork_Openx extends Template_EditNetwork
{
	function Template_EditAd_Openx()
	{
		// Call parent first!
		parent::Template_EditNetwork();
		// Remove Format Meta box
		remove_meta_box('adsensem_format', 'adsensem', 'normal');
	}
}
?>