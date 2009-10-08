<?php
require_once(OX_LIB . '/Network.php');	

class OX_Plugin_Adgridwork extends OX_Network
{
	var $network_name = 'AdGridWork';
	var $url = 'http://www.adgridwork.com';
	
	function OX_Plugin_Adgridwork($network = null)
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
	
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'color-bg' 	=> 'FFFFFF',
			'color-border'=> '646360',
			'color-link' => 'FF0000',
			'color-text'	=> '646360',
			'color-title'	=> '000000',
			'slot' => '',
		);
		
		return $properties + parent::get_network_property_defaults();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('800x90', '728x90', '600x90', '468x60', '400x90', '234x60', '200x90', '120x600', '160x600', '200x360', '200x270', '336x280', '300x250', '250x250', '200x180', '180x150'));
	}
	
	function is_tag_detected($code)
	{
		
		return ((strpos($code,'www.adgridwork.com') !== false) ||
			(strpos($code,'www.mediagridwork.com/mx.js') !== false)
		);

	}
		
	function import($code, &$ad)
	{
		if (preg_match("/www\.adgridwork\.com\/\?r=(\d*)/", $code, $matches)) {
			$ad->set_property('account-id', $matches[1]);
			$code = str_replace("www.adgridwork.com/?r={$matches[1]}", "www.adgridwork.com/?r={{account-id}}", $code);
		}
		
		if (preg_match('/var sid = \'(\w*)\'/', $code, $matches)) {
			$ad->set_property('slot', $matches[1]);
			$code = str_replace("var sid = '{$matches[1]}'", "var sid = '{{slot}}'", $code);
		}
		
		if (preg_match('/style=\"color: #(\w*);/', $code, $matches)) {
			$ad->set_property('color-link', $matches[1]);
			$code = str_replace("style=\"color: #{$matches[1]};", "style=\"color: #{{color-link}};", $code);
		}
		
		if (preg_match("/var title_color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-title', $matches[1]);
			$code = str_replace("var title_color = '{$matches[1]}'", "var title_color = '{{color-title}}'", $code);
		}
		
		if (preg_match("/var description_color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-text', $matches[1]);
			$code = str_replace("var description_color = '{$matches[1]}'", "var description_color = '{{color-text}}'", $code);
		}
		
		if (preg_match("/var link_color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-url', $matches[1]);
			$code = str_replace("var link_color = '{$matches[1]}'", "var link_color = '{{color-link}}'", $code);
		}
		
		if (preg_match("/var background_color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-bg', $matches[1]);
			$code = str_replace("var background_color = '{$matches[1]}'", "var background_color = '{{color-bg}}'", $code);
		}
		
		if (preg_match("/var border_color = '(\w*)'/", $code, $matches)) {
			$ad->set_property('color-border', $matches[1]);
			$code = str_replace("var border_color = '{$matches[1]}'", "var border_color = '{{color-border}}'", $code);
		}
		
		return parent::import($code, $ad);
	}
}
/*
<a href="http://www.adgridwork.com/?r=18501" style="color: #ff3333; font-size: 14px" target="_blank">Free Advertising</a>
<script type="text/javascript">
var sid = '12';
var title_color = '333399';
var description_color = '00ff00';
var link_color = 'ff0000';
var background_color = 'ffff00';
var border_color = '999999';
</script><script type="text/javascript" src="http://www.mediagridwork.com/mx.js"></script>
*/
?>
