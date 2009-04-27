<?php
require_once(OX_SWIFTY_PATH . '/Adnet.php');	
/*
$_advman_networks['OX_Adnet_Adbrite'] = array(
	'www-create' => 'http://www.adbrite.com/zones/commerce/purchase.php?product_id_array=22',
	'www-signup'	=>	'http://www.adbrite.com/mb/landing_both.php?spid=51549&afb=120x60-1-blue',														 
 );
*/
class OX_Adnet_Adbrite extends OX_Adnet
{
	var $mnemonic = 'Adbrite';
	var $network_name = 'AdBrite';
	var $url = 'http://www.adbrite.com';
	
	function OX_Adnet_Adbrite()
	{
		$this->OX_Adnet();
	}
		
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '250x250',
			'color-bg' => 'FFFFFF',
			'color-border' => 'FFFFFF',
			'color-text' => '000000',
			'color-title' => '0000FF',
			'height' => '250',
			'slot' => '',
			'width' => '250',
		);
		
		return $properties + parent::get_network_property_defaults();
	}
	
	function get_ad_formats()
	{
		return array('728x90', '468x60', '120x600', '160x600', '300x250');
	}
	
	function get_ad_colors()
	{
		return array('border', 'title', 'bg', 'text');
	}
	
	function import_detect_network($code)
	{
		
		return ( (strpos($code,'<!-- Begin: AdBrite -->')!==false) ||
				(strpos($code,'src="http://ads.adbrite.com')!==false) ||
				(strpos($code,'<!-- End: AdBrite -->')!==false)
		);

	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);

		if (preg_match("/var AdBrite_Title_Color = '(\w*)'/", $code, $matches)) {
			$this->set_property('color-title', $matches[1]);
			$code = str_replace("var AdBrite_Title_Color = '{$matches[1]}'", "var AdBrite_Title_Color = '{{color-title}}'", $code);
		}
		if (preg_match("/var AdBrite_Text_Color = '(\w*)'/", $code, $matches)) {
			$this->set_property('color-text', $matches[1]);
			$code = str_replace("var AdBrite_Text_Color = '{$matches[1]}'", "var AdBrite_Text_Color = '{{color-text}}'", $code);
		}
		if (preg_match("/var AdBrite_Background_Color = '(\w*)'/", $code, $matches)) {
			$this->set_property('color-bg', $matches[1]);
			$code = str_replace("var AdBrite_Background_Color = '{$matches[1]}'", "var AdBrite_Background_Color = '{{color-bg}}'", $code);
		}
		if (preg_match("/var AdBrite_Border_Color = '(\w*)'/", $code, $matches)) {
			$this->set_property('color-border', $matches[1]);
			$code = str_replace("var AdBrite_Border_Color = '{$matches[1]}'", "var AdBrite_Border_Color = '{{color-border}}'", $code);
		}
		
		if (preg_match("/zs=(\w*)/", $code, $matches) != 0) {
			$this->set_property('account-id', $matches[1]);
			$code = str_replace("zs={$matches[1]}", "zs={{account-id}}", $code);
		}
		if (preg_match("/sid=(\w*)/", $code, $matches) != 0) {
			$this->set_property('slot', $matches[1]);
			$code = str_replace("sid={$matches[1]}", "sid={{slot}}", $code);
			$code = str_replace("opid={$matches[1]}", "sid={{slot}}", $code);
		}
		
		$this->set_property('code', $code);
	}
}
/*
<!-- Begin: AdBrite -->
<script type="text/javascript">
   var AdBrite_Title_Color = '0000FF';
   var AdBrite_Text_Color = '000000';
   var AdBrite_Background_Color = 'FFFFFF';
   var AdBrite_Border_Color = 'FFFFFF';
</script>
<script src="http://ads.adbrite.com/mb/text_group.php?sid=426554&zs=3132305f363030" type="text/javascript"></script>
<div><a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=426554&afsid=1" style="font-weight:bold;font-family:Arial;font-size:13px;">Your Ad Here</a></div>
<!-- End: AdBrite -->
*/
?>