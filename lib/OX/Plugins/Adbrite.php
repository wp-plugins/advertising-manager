<?php
require_once(OX_LIB . '/Network.php');

class OX_Plugin_Adbrite extends OX_Network
{
	var $network_name = 'AdBrite';
	var $url = 'http://www.adbrite.com';
	
	function OX_Plugin_Adbrite($network = null)
	{
		$this->OX_Network($network);
		$this->name = 'adgridwork';  // Short name which is the prefix for the default name of ads
	}
	
	/**
	 * This function is called statically from the ad engine.  Use this function to put any hooks in the ad engine that you want to use.
	 */
	function register_plugin(&$engine)
	{
		$engine->add_action('ad_network', get_class($this));
	}
	
	function get_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '250x250',
			'color-bg' => 'FFFFFF',
			'color-border' => 'CCCCCC',
			'color-text' => '000000',
			'color-title' => '0000FF',
			'color-link' => '008000',
			'height' => '250',
			'slot' => '',
			'width' => '250',
		);
		
		return $properties + parent::get_property_defaults();
	}
	
	function get_ad_formats()
	{
		return array(all => array('728x90', '468x60', '120x600', '160x600', '300x250'));
	}
	
	function is_tag_detected($code)
	{
		
		return ((strpos($code,'<!-- Begin: AdBrite -->') !== false) ||
			(strpos($code,'src="http://ads.adbrite.com') !== false) ||
			(strpos($code,'<!-- End: AdBrite -->') !== false)
		);

	}
	
	function import($code, &$ad)
	{
		if (preg_match("/var AdBrite_Title_Color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-title', $matches[1]);
			$code = str_replace("var AdBrite_Title_Color = '{$matches[1]}'", "var AdBrite_Title_Color = '{{color-title}}'", $code);
		}
		if (preg_match("/var AdBrite_Text_Color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-text', $matches[1]);
			$code = str_replace("var AdBrite_Text_Color = '{$matches[1]}'", "var AdBrite_Text_Color = '{{color-text}}'", $code);
		}
		if (preg_match("/var AdBrite_Background_Color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-bg', $matches[1]);
			$code = str_replace("var AdBrite_Background_Color = '{$matches[1]}'", "var AdBrite_Background_Color = '{{color-bg}}'", $code);
		}
		if (preg_match("/var AdBrite_Border_Color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-border', $matches[1]);
			$code = str_replace("var AdBrite_Border_Color = '{$matches[1]}'", "var AdBrite_Border_Color = '{{color-border}}'", $code);
		}
		if (preg_match("/var AdBrite_URL_Color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-link', $matches[1]);
			$code = str_replace("var AdBrite_URL_Color = '{$matches[1]}'", "var AdBrite_URL_Color = '{{color-link}}'", $code);
		}
		
		if (preg_match("/zs=(\w*)/", $code, $matches) != 0) {
			$ad->set_property('account-id', $matches[1]);
			$code = str_replace("zs={$matches[1]}", "zs={{account-id}}", $code);
		}
		if (preg_match("/sid=(\w*)/", $code, $matches) != 0) {
			$ad->set_property('slot', $matches[1]);
			$code = str_replace("sid={$matches[1]}", "sid={{slot}}", $code);
			$code = str_replace("opid={$matches[1]}", "sid={{slot}}", $code);
		}
		
		return parent::import($code, $ad);
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

<!-- Begin: AdBrite, Generated: 2009-04-30 10:04:42  -->
<script type="text/javascript">
var AdBrite_Title_Color = '0000FF';
var AdBrite_Text_Color = '000000';
var AdBrite_Background_Color = 'FFFFFF';
var AdBrite_Border_Color = 'CCCCCC';
var AdBrite_URL_Color = '008000';
try{var AdBrite_Iframe=window.top!=window.self?2:1;var AdBrite_Referrer=document.referrer==''?document.location:document.referrer;AdBrite_Referrer=encodeURIComponent(AdBrite_Referrer);}catch(e){var AdBrite_Iframe='';var AdBrite_Referrer='';}
</script>
<script type="text/javascript">document.write(String.fromCharCode(60,83,67,82,73,80,84));document.write(' src="http://ads.adbrite.com/mb/text_group.php?sid=1151693&zs=3330305f323530&ifr='+AdBrite_Iframe+'&ref='+AdBrite_Referrer+'" type="text/javascript">');document.write(String.fromCharCode(60,47,83,67,82,73,80,84,62));</script>
<div><a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=1151693&afsid=1" style="font-weight:bold;font-family:Arial;font-size:13px;">Your Ad Here</a></div>
<!-- End: AdBrite -->

REFERRAL TAG
<a href="http://www.adbrite.com/mb/landing_both.php?spid=118090&afb=468x60-2">
<img src="http://files.adbrite.com/mb/images/468x60-2.gif" border="0"></a>
*/
?>