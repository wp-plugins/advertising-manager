<?php
require_once(OX_SWIFTY_PATH . '/Adnet.php');	
/*
$_advman_networks['OX_Adnet_Adify'] = array(
	'www-create' => 'http://www.adify.com',
	'www-signup'	=>	'http://www.adify.com',
);
*/
class OX_Adnet_Adify extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Adify';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = 'http://www.adify.com';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'Adify';
	
	function OX_Adnet_Adify()
	{
		$this->OX_Adnet();
	}
		
	function get_default_properties()
	{
		$properties = array(
			'adformat' => '250x250',
			'color-bg' 	=> 'FFFFFF',
			'color-border'=> 'FFFFFF',
			'color-text'	=> '000000',
			'color-title'	=> '0000FF',
			'height' => '250',
			'slot' => '',
			'width' => '250',
		);
		
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code)
	{
		return (strpos($code,'sr_adspace_id') !== false);
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		// Account ID
		if (preg_match('/sr_adspace_id( *)=( *)(.\d)/', $code, $matches) != 0) {
			$this->set('account-id', $matches[3]);
			$code = str_replace("sr_adspace_id{$matches[1]}={$matches[2]}{$matches[3]}", "sr_adspace_id{$matches[1]}={$matches[2]}{{account-id}}", $code);
			$code = str_replace("azId={$matches[3]}", "azId={{account-id}}", $code);
			$code = str_replace("ID #{$matches[3]}", "ID #{{account-id}}", $code);
		}
		
		// Width / Height
		$width = '';
		$height = '';
		if (preg_match('/sr_adspace_width( *)=( *)(\d*);/', $code, $matches) != 0) {
			$width = $matches[3]; 
			if ($width != '') {
				$this->set('width', $width);
			}
			$code = str_replace("sr_adspace_width{$matches[1]}={$matches[2]}{$width}", "sr_adspace_width{$matches[1]}={$matches[2]}{{width}}", $code);
		}
		if (preg_match('/google_ad_height( *)=( *)(\d*);/', $code, $matches) != 0) {
			$height = $matches[3];
			if ($height != '') {
				$this->set('height', $height);
			}
			$code = str_replace("sr_adspace_height{$matches[1]}={$matches[2]}{$height}", "sr_adspace_height{$matches[1]}={$matches[2]}{{height}}", $code);
		}
		if (($width != '') && ($height != '')) {
			$this->set('adformat', $width . 'x' . $height);
		}
	}
}
/*
<!-- Begin Adify tag for "Sidebar" Ad Space (160x160) ID #6471907 -->
<script type="text/javascript">
   sr_adspace_id = 6471907;
   sr_adspace_width = 160;
   sr_adspace_height = 160;
   sr_adspace_type = "graphic";
   sr_ad_new_window = true;
   
</script>
<script type="text/javascript" src="http://ad.afy11.net/srad.js?azId=6471907">
</script>
<!-- End Adify tag for "Sidebar" Ad Space (160x160) ID #6471907 -->*/
?>
