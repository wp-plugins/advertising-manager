<?php
require_once(OX_LIB . '/Ad.php');	

class OX_Plugin_Adsense extends OX_Ad
{
	var $network = 'adsense';
	var $network_name = 'Google Adsense';
	var $url = 'http://www.google.com/adsense';
	
	function OX_Plugin_Adsense($aAd = null)
	{
		$this->OX_Ad($aAd);
	}
	
	/**
	 * This function is called statically from the ad engine.  Use this function to put any hooks in the ad engine that you want to use.
	 */
	function register_plugin($engine)
	{
		$engine->addAction('ad_network', get_class());
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '728x90',
			'counter' => '3',
			'height' => '728',
			'partner' => '',
			'slot' => '',
			'width' => '90',
		);
		return $properties + parent::get_network_property_defaults();
	}
	
	function import_detect_network($code)
	{
		return (strpos($code,'google_ad_client') !== false);
	}

	function import_settings($code)
	{
		// Account ID
		if (preg_match('/google_ad_client( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set_property('account-id', $matches[3]);
			$code = str_replace("google_ad_client{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_client{$matches[1]}={$matches[2]}\"{{account-id}}\"", $code);
		}
		
		// Partner ID
		if (preg_match('/google_ad_host( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set_property('partner', $matches[3]);
			$code = str_replace("google_ad_host{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_host{$matches[1]}={$matches[2]}\"{{partner}}\"", $code);
		}
		
		// Slot ID
		if (preg_match('/google_ad_slot( *)=( *)"(.*)"/', $code, $matches) != 0) {
			$this->set_property('slot', $matches[3]);
			$code = str_replace("google_ad_slot{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_slot{$matches[1]}={$matches[2]}\"{{slot}}\"", $code);
		}
		
		// Width / Height
		$width = '';
		$height = '';
		if (preg_match('/google_ad_width( *)=( *)(\d*);/', $code, $matches) != 0) {
			$width = $matches[3]; 
			if ($width != '') {
				$this->set_property('width', $width);
			}
			$code = str_replace("google_ad_width{$matches[1]}={$matches[2]}{$width}", "google_ad_width{$matches[1]}={$matches[2]}{{width}}", $code);
		}
		if (preg_match('/google_ad_height( *)=( *)(\d*);/', $code, $matches) != 0) {
			$height = $matches[3];
			if ($height != '') {
				$this->set_property('height', $height);
			}
			$code = str_replace("google_ad_height{$matches[1]}={$matches[2]}{$height}", "google_ad_height{$matches[1]}={$matches[2]}{{height}}", $code);
		}
		if (($width != '') && ($height != '')) {
			$this->set_property('adformat', $width . 'x' . $height);
		}
		
		parent::import_settings($code);
	}
	
	function save_settings()
	{
		// Save settings to parent first!
		parent::save_settings();
		
		//Override adformat saving already
		switch($this->get_property('adtype')){
			case 'slot' :
			case 'ad' :
				$this->set_property('adformat', OX_Tools::sanitize($_POST['advman-adformat'], 'format'));
				break;
			case 'link' :
				$this->set_property('adformat', OX_Tools::sanitize($_POST['advman-linkformat'], 'format'));
				break;
			case 'ref_image' :
				$this->set_property('adformat', OX_Tools::sanitize($_POST['advman-referralformat'], 'format'));
				break;
			default :
				$this->set_property('adformat', '');
		 }

		 list($width, $height, $null) = split('[x]', $this->get_property('adformat'));
		 $this->set_property('width', $width);
		 $this->set_property('height', $height);
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


a:6:{s:3:"ads";a:1:{s:7:"adsense";O:19:"Ad_AdSense_Referral":3:{s:5:"title";s:0:"";s:1:"p";a:23:{s:11:"html-before";s:0:"";s:10:"html-after";s:0:"";s:9:"show-home";s:0:"";s:9:"show-post";s:0:"";s:9:"show-page";s:0:"";s:12:"show-archive";s:0:"";s:11:"show-search";s:0:"";s:8:"adformat";s:0:"";s:4:"code";N;s:5:"notes";s:0:"";s:6:"height";N;s:5:"width";s:0:"";s:7:"channel";s:7:"CHANNEL";s:6:"adtype";s:0:"";s:12:"color-border";s:0:"";s:11:"color-title";s:0:"";s:8:"color-bg";s:0:"";s:10:"color-text";s:0:"";s:10:"color-link";s:0:"";s:7:"uistyle";s:0:"";s:7:"product";s:0:"";s:8:"referral";s:0:"";s:4:"slot";s:8:"Referral";}s:4:"name";s:7:"adsense";}}s:8:"defaults";a:1:{s:18:"ad_adsense_classic";a:19:{s:9:"show-home";s:3:"yes";s:9:"show-post";s:3:"yes";s:9:"show-page";s:3:"yes";s:12:"show-archive";s:3:"yes";s:11:"show-search";s:3:"yes";s:11:"html-before";s:0:"";s:10:"html-after";s:0:"";s:12:"color-border";s:6:"FFFFFF";s:11:"color-title";s:6:"0000FF";s:8:"color-bg";s:6:"FFFFFF";s:10:"color-text";s:6:"000000";s:10:"color-link";s:6:"008000";s:7:"channel";s:0:"";s:7:"uistyle";s:0:"";s:4:"slot";s:0:"";s:8:"adformat";s:7:"250x250";s:6:"adtype";s:10:"text_image";s:10:"linkformat";s:6:"120x90";s:8:"linktype";s:10:"_0ads_al_s";}}s:11:"account-ids";a:1:{s:10:"ad_adsense";s:16:"4156398908232320";}s:7:"be-nice";i:3;s:7:"version";s:6:"3.2.13";s:10:"default-ad";s:7:"adsense";}

a:6:{s:3:"ads";a:1:{s:4:"ad-1";O:10:"Ad_AdSense":3:{s:5:"title";s:0:"";s:1:"p";a:14:{s:11:"html-before";s:0:"";s:10:"html-after";s:0:"";s:9:"show-home";N;s:9:"show-post";N;s:9:"show-page";N;s:12:"show-archive";N;s:11:"show-search";N;s:8:"adformat";s:6:"728x90";s:4:"code";s:357:"<script type=\"text/javascript\"><!--
google_ad_client = \"pub-8134107512753547\";
google_ad_host = \"pub-1599271086004685\";
// Leaderboard
google_ad_slot = \"3141793269\";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script 
src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\" type=\"text/javascript\">
</script>
";s:5:"notes";N;s:6:"height";s:2:"90";s:5:"width";s:3:"728";s:4:"slot";s:10:"3141793269";s:6:"adtype";s:2:"ad";}s:4:"name";s:4:"ad-1";}}s:8:"defaults";a:1:{s:10:"ad_adsense";a:8:{s:9:"show-home";s:3:"yes";s:9:"show-post";s:3:"yes";s:9:"show-page";s:3:"yes";s:12:"show-archive";s:3:"yes";s:11:"show-search";s:3:"yes";s:11:"html-before";s:0:"";s:10:"html-after";s:0:"";s:4:"slot";s:0:"";}}s:11:"account-ids";a:1:{s:10:"ad_adsense";s:16:"8134107512753547";}s:7:"be-nice";i:3;s:7:"version";s:6:"3.2.13";s:10:"default-ad";s:4:"ad-1";}

*/
?>