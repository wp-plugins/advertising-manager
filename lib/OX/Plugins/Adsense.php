<?php
require_once(OX_LIB . '/Network.php');

class OX_Plugin_Adsense extends OX_Network
{
	function OX_Plugin_Adsense()
	{
		$this->OX_Network();
		$this->name = 'Google Adsense';
		$this->short_name = 'adsense';
	}
	
	/**
	 * This function is called statically from the ad engine.  Use this function to put any hooks in the ad engine that you want to use.
	 */
	function register_plugin(&$engine)
	{
		$engine->add_action('ad_network', get_class());
	}
	
	function get_default_properties()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '728x90',
			'adtype' => 'all',
			'counter' => '3',
			'height' => '728',
			'partner' => '',
//			'password' => '',
			'slot' => '',
//			'username' => '',
			'width' => '90',
		);
		return $properties + parent::get_default_properties();
	}
	
	function get_ad_formats()
	{
		$text = array('728x90', '468x60', '234x60', '125x125', '120x600', '160x600', '180x150', '120x240', '200x200', '250x250', '300x250', '336x280');
		$image = array('728x90', '468x60', '120x600', '160x600', '200x200', '250x250', '300x250', '336x280');
		$video = array('728x90', '120x600', '160x600', '200x200', '250x250', '300x250', '336x280');
		$link = array('120x90#4', '120x90#5', '160x90#4', '160x90#5', '180x90#4', '180x90#5', '200x90#4', '200x90#5', '468x15#4', '468x15#5');
		
		return array('all' => $text + $image + $video, 'text' => $text, 'image' => $image, 'video' => $video, 'link' => $link);
	}
	
	function import($code)
	{
		$ad = false;
		
		if (strpos($code,'google_ad_client') !== false) {
			
			$ad = OX_Ad::to_object();
			$ad->network_type = get_class();
		
			// Account ID
			if (preg_match('/google_ad_client( *)=( *)"(.*)"/', $code, $matches) != 0) {
				$ad->set_property('account-id', $matches[3]);
				$code = str_replace("google_ad_client{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_client{$matches[1]}={$matches[2]}\"{{account-id}}\"", $code);
			}
			
			// Partner ID
			if (preg_match('/google_ad_host( *)=( *)"(.*)"/', $code, $matches) != 0) {
				$ad->set_property('partner', $matches[3]);
				$code = str_replace("google_ad_host{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_host{$matches[1]}={$matches[2]}\"{{partner}}\"", $code);
			}
			
			// Slot ID
			if (preg_match('/google_ad_slot( *)=( *)"(.*)"/', $code, $matches) != 0) {
				$ad->set_property('slot', $matches[3]);
				$ad->set_property('adtype', 'all');
				$code = str_replace("google_ad_slot{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "google_ad_slot{$matches[1]}={$matches[2]}\"{{slot}}\"", $code);
			}
			
			// Width / Height
			$width = '';
			$height = '';
			if (preg_match('/google_ad_width( *)=( *)(\d*);/', $code, $matches) != 0) {
				$width = $matches[3]; 
				if ($width != '') {
					$ad->set_property('width', $width);
				}
				$code = str_replace("google_ad_width{$matches[1]}={$matches[2]}{$width}", "google_ad_width{$matches[1]}={$matches[2]}{{width}}", $code);
			}
			if (preg_match('/google_ad_height( *)=( *)(\d*);/', $code, $matches) != 0) {
				$height = $matches[3];
				if ($height != '') {
					$ad->set_property('height', $height);
				}
				$code = str_replace("google_ad_height{$matches[1]}={$matches[2]}{$height}", "google_ad_height{$matches[1]}={$matches[2]}{{height}}", $code);
			}
			if (($width != '') && ($height != '')) {
				$ad->set_property('adformat', $width . 'x' . $height);
				$ad->set_property('adtype', 'all');
			}
		
			$ad->set_property('code', $code);
		}
		
		return $ad;
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

// REFERRAL
<script type="text/javascript"><!--
google_ad_client = "pub-8424176087324892";
google_ad_width = 728;
google_ad_height = 90;
google_ad_format = "728x90_as";
google_cpa_choice = "CAEaCGcn-3m_pqJvUBRQDlAIUEJQL1AFUEdQDVAEUBI";
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
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