<?php
require_once(OX_SWIFTY_PATH . '/Ad.php');	
/*
$_advman_networks['OX_Swifty_Ad
_Chitika'] = array(
	'www-create' => 'https://chitika.com/affiliate/plcode.php',
	'www-signup'	=>	'http://chitika.com/publishers.php?refid=switzer',														 
 );
*/
class OX_Swifty_Plugins_Chitika extends OX_Swifty_Ad
{
	var $mnemonic = 'Chitika';
	var $network_name = 'Chitika';
	var $url = 'http://www.chitika.com';
	
	function OX_Swifty_Plugins_Chitika()
	{
		$this->OX_Swifty_Ad();
	}
		
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '728x90',
			'alt-url' => '',
			'channel' => '',
			'color-bg' => '',
			'color-border'=> '',
			'color-link'=> '',
			'color-text' => '',
			'color-title' => '',
			'font-text'	=> '',
			'font-title' => '',
			'height' => '90',
			'width' => '728',
		);
		
		return $properties + parent::get_network_property_defaults();
	}
	
	function import_detect_network($code)
	{
		
		return ( (strpos($code,'chitika')!==false) &&
				(strpos($code,'ch_client') !==false)
		);

	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);

		$s = '([\s]*)'; // search for a space character
		$q = "([\\'\\\"]{1})"; // search for a quote character
		
		if (preg_match("/ch_client{$s}={$s}{$q}(.*){$q};/", $code, $matches) != 0) {
			$this->set_property('account-id', $matches[4]);
			$code = str_replace("ch_client{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_client{$matches[1]}={$matches[2]}{$matches[3]}{{account-id}}{$matches[5]};", $code);
		}

		if (preg_match("/ch_color_bg{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('color-bg', $matches[4]);
			$code = str_replace("ch_color_bg{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_color_bg{$matches[1]}={$matches[2]}{$matches[3]}{{color-bg}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_color_border{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('color-border', $matches[4]);
			$code = str_replace("ch_color_border{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_color_border{$matches[1]}={$matches[2]}{$matches[3]}{{color-border}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_color_title{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('color-title', $matches[4]);
			$code = str_replace("ch_color_title{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_color_title{$matches[1]}={$matches[2]}{$matches[3]}{{color-title}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_color_site_link{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('color-link', $matches[4]);
			$code = str_replace("ch_color_site_link{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_color_site_link{$matches[1]}={$matches[2]}{$matches[3]}{{color-link}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_color_text{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('color-text', $matches[4]);
			$code = str_replace("ch_color_text{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_color_text{$matches[1]}={$matches[2]}{$matches[3]}{{color-text}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_font_title{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('font-title', $matches[4]);
			$code = str_replace("ch_font_title{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_font_title{$matches[1]}={$matches[2]}{$matches[3]}{{font-title}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_font_text{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('font-text', $matches[4]);
			$code = str_replace("ch_font_text{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_font_text{$matches[1]}={$matches[2]}{$matches[3]}{{font-text}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_sid{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('channel', $matches[4]);
			$code = str_replace("ch_sid{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_sid{$matches[1]}={$matches[2]}{$matches[3]}{{channel}}{$matches[5]};", $code);
		}
		
		if (preg_match("/ch_alternate_ad_url{$s}={$s}{$q}(.*){$q};/", $code, $matches)) {
			$this->set_property('alt-url', $matches[4]);
			$code = str_replace("ch_alternate_ad_url{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]};", "ch_alternate_ad_url{$matches[1]}={$matches[2]}{$matches[3]}{{alt-url}}{$matches[5]};", $code);
		}
		
		$width = '';
		$height = '';
		if (preg_match("/ch_width{$s}={$s}(\d*);/", $code, $matches) != 0) {
			$width = $matches[3];
			$this->set_property('width', $width);
			$code = str_replace("ch_width{$matches[1]}={$matches[2]}{$matches[3]};", "ch_width{$matches[1]}={$matches[2]}{{width}};", $code);
		}

		if (preg_match("/ch_height{$s}={$s}(\d*);/", $code, $matches) != 0) {
			$height = $matches[3];
			$this->set_property('height', $height);
			$code = str_replace("ch_height{$matches[1]}={$matches[2]}{$matches[3]};", "ch_height{$matches[1]}={$matches[2]}{{height}};", $code);
		}
		
		if (($width != '') && ($height != '')) {
			$this->set_property('adformat', $width . 'x' . $height);
		}

		$this->set_property('code', $code);
	}
	
	function get_preview_url()
	{
		$url = parent::get_preview_url();
		return $url . '#chitikatest=mortgage';
	}
}
/*
<!-- You will NOT be able to see the ad on your site! This unit is hidden on your page, and will only display to your search engine traffic (from US and CA). To preview, paste the code up on your site, then add #chitikatest=mortgage to the end of your URL in your browser's address bar.  Example:  www.yourwebsite.com#chitikatest=mortgage. This will show you what the ad would look like to a user who is interested in "mortgages." -->
<script type="text/javascript"><!--
ch_client = "switzer";
ch_type = "mpu";
ch_width = 728;
ch_height = 90;
ch_color_bg = "FFFF00";
ch_color_border = "FFFF00";
ch_color_title = "FF00FF";
ch_color_site_link = "FF00FF";
ch_color_text = "00FF00";
ch_non_contextual = 4;
ch_vertical ="premium";
ch_font_title = "Comic Sans MS";
ch_font_text = "Comic Sans MS";
ch_sid = "CHANNEL";
ch_alternate_ad_url = "http://www.switzer.org/ad.html";
var ch_queries = new Array( );
var ch_selected=Math.floor((Math.random()*ch_queries.length));
if ( ch_selected < ch_queries.length ) {
ch_query = ch_queries[ch_selected];
}
//--></script>
<script  src="http://scripts.chitika.net/eminimalls/amm.js" type="text/javascript">
</script>
*/
?>
