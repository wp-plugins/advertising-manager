<?php
require_once(OX_SWIFTY_PATH . '/Adnet.php');	
/*
$_advman_networks['OX_Adnet_Adsense'] = array(
		'ico'		=>	'http://www.google.com/favicon.ico',
		'www-create' => 'https://www.google.com/adsense/adsense-products',
		'www-signup'		=>	'https://www.google.com/adsense/',
		'display' => false,
		'limit-ads' => 9
		);
*/
class OX_Adnet_Adsense extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	static $shortName = 'Adsense';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	static $url = 'http://www.google.com/adsense';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	static $networkName = 'Google Adsense';
	
	function OX_Adnet_Adsense()
	{
		$this->OX_Adnet();
	}
	
	function get_default_properties()
	{
		$properties = array(
			'account-id' => '',
			'adtype' => 'slot',
			'adformat' => '728x90',
			'channel' => '',
			'color-bg' => 'FFFFFF',
			'color-border'=> '646360',
			'color-link' => 'FF0000',
			'color-text' => '646360',
			'color-title' => '000000',
			'counter' => '3',
			'height' => '728',
			'partner' => '',
			'slot' => '',
			'width' => '90',
		);
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code)
	{
		return (strpos($code,'google_ad_client') !== false);
	}

	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		// Account ID
		if (preg_match('/google_ad_client( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('account-id', $matches[3]);
			$code = str_replace("google_ad_client{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_client{$matches[1]}={$matches[2]}\"{{account-id}}\"", $code);
		}
		
		// Channel
		if (preg_match('/google_ad_channel( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('channel', $matches[3]);
			$code = str_replace("google_ad_channel{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_channel{$matches[1]}={$matches[2]}\"{{channel}}\"", $code);
		}
		
		// Partner ID
		if (preg_match('/google_ad_host( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('partner', $matches[3]);
			$code = str_replace("google_ad_host{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_host{$matches[1]}={$matches[2]}\"{{partner}}\"", $code);
		}
		
		// Slot ID
		$adtype = 'ad';
		if (preg_match('/google_ad_slot( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('slot', $matches[3]);
			$adtype = 'slot'; // 'Slot tag types'
			$code = str_replace("google_ad_slot{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_slot{$matches[1]}={$matches[2]}\"{{slot}}\"", $code);
		}
		
		// Color Border
		if (preg_match('/google_color_border( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('color-border', $matches[3]);
			$code = str_replace("google_color_border{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_color_border{$matches[1]}={$matches[2]}\"{{color-border}}\"", $code);
		}
		
		// Color Background
		if (preg_match('/google_color_bg( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('color-bg', $matches[3]);
			$code = str_replace("google_color_bg{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_color_bg{$matches[1]}={$matches[2]}\"{{color-bg}}\"", $code);
		}
		
		// Color Title
		if (preg_match('/google_color_link( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('color-title', $matches[3]);
			$code = str_replace("google_color_link{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_color_link{$matches[1]}={$matches[2]}\"{{color-title}}\"", $code);
		}
		
		// Color Text
		if (preg_match('/google_color_text( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('color-text', $matches[3]);
			$code = str_replace("google_color_text{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_color_text{$matches[1]}={$matches[2]}\"{{color-text}}\"", $code);
		}

		// Color URL
		if (preg_match('/google_color_url( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set('color-link', $matches[3]);
			$code = str_replace("google_color_url{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_color_url{$matches[1]}={$matches[2]}\"{{color-link}}\"", $code);
		}

		// Width / Height
		$width = '';
		$height = '';
		if (preg_match('/google_ad_width( *)=( *)(\d*);/', $code, $matches) != 0) {
			$width = $matches[3]; 
			if ($width != '') {
				$this->set('width', $width);
			}
			$code = str_replace("google_ad_width{$matches[1]}={$matches[2]}{$width}", "google_ad_width{$matches[1]}={$matches[2]}{{width}}", $code);
		}
		if (preg_match('/google_ad_height( *)=( *)(\d*);/', $code, $matches) != 0) {
			$height = $matches[3];
			if ($height != '') {
				$this->set('height', $height);
			}
			$code = str_replace("google_ad_height{$matches[1]}={$matches[2]}{$height}", "google_ad_height{$matches[1]}={$matches[2]}{{height}}", $code);
		}
		if (($width != '') && ($height != '')) {
			$this->set('adformat', $width . 'x' . $height);
		}
		
		$format = $this->get('adformat'); //passthru
		if (preg_match('/google_cpa_choice = ""/', $code, $matches) != 0) {
			//Referral unit
			if (preg_match('/google_ad_output = "textlink";/', $code, $matches) != 0) {
				$this->set('adtype', 'ref_text');
			} else {
				$this->set('adtype', 'ref_image');
				$this->set('referralformat', $format);
			}
		} else {
			$linkformats = array('728x15', '468x15', '200x90', '180x90', '160x90', '120x90');
			
			if (array_search($format, $linkformats) === false) {
				$this->set('adtype', $adtype);
			} else {
				$this->set('adtype', 'link');
				$this->set('linkformat', OX_Tools::sanitize($_POST['advman-adformat'], 'format')); //passthru
			}
		}
		
		$this->set('code', $code);
	}
	
	function save_settings()
	{
		// Save settings to parent first!
		parent::save_settings();
		
		//Override adformat saving already
		switch($this->get('adtype')){
			case 'slot' :
			case 'ad' :
				$this->set('adformat', OX_Tools::sanitize($_POST['advman-adformat'], 'format'));
				break;
			case 'link' :
				$this->set('adformat', OX_Tools::sanitize($_POST['advman-linkformat'], 'format'));
				break;
			case 'ref_image' :
				$this->set('adformat', OX_Tools::sanitize($_POST['advman-referralformat'], 'format'));
				break;
			default :
				$this->set('adformat', '');
		 }

		 list($width, $height, $null) = split('[x]', $this->get('adformat'));
		 $this->set('width', $width);
		 $this->set('height', $height);
	}

	function _form_settings_stats()
	{
?><tr><td><p><a href="https://www.google.com/adsense/report/overview">Statistics and earnings</a></p></td></tr><?php
	}
}
/*
 // SLOT SYSTEM AD 
<script type="text/javascript"><!--
google_ad_client = "pub-8134107512753547";
google_ad_host = "pub-1599271086004685";
// Leaderboard
google_ad_slot = "3141793269";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script 
src="http://pagead2.googlesyndication.com/pagead/show_ads.js" type="text/javascript">
</script>

// OLD AD FORMAT
<script type="text/javascript"><!--
google_ad_client = "pub-4156398908232320";
google_ad_width = 300;
google_ad_height = 250;
google_ad_format = "300x250_as";
google_ad_type = "text_image";
//2007-02-02: HCT Forum Square
google_ad_channel = "0219533365";
google_color_border = "FFFFFF";
google_color_bg = "FFFFFF";
google_color_link = "003399";
google_color_text = "000000";
google_color_url = "000000";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
*/
?>