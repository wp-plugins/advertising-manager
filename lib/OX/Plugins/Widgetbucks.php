<?php
require_once(OX_LIB . '/Network.php');	

class OX_Plugin_Widgetbucks extends OX_Network
{
	function OX_Plugin_Widgetbucks()
	{
		$this->OX_Network();
		$this->name = 'WidgetBucks';
		$this->short_name = 'wb';
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
			'slot' => '',
		);
		return $properties + parent::get_default_properties();
	}
	
	function get_ad_colors()
	{
		return array('border', 'title', 'bg', 'text');
	}
	
	function import($code)
	{
		$ad = false;
		
		if ( preg_match('/(\w*)\.widgetbucks.com\/script\/(\w*).js\?uid=(\w*)/', $code, $matches) ) {
		
			$ad = OX_Ad::to_object();
			$ad->network_type = get_class();
		
			if (preg_match('/(\w*)\.widgetbucks.com\/script\/(\w*).js\?uid=(\w*)/', $code, $matches)!=0) { 
				$ad->set_property('slot', $matches[3]);
				$code = str_replace("{$matches[1]}.widgetbucks.com/script/{$matches[2]}.js?uid={$matches[3]}", "{$matches[1]}.widgetbucks.com/script/{$matches[2]}.js?uid={{slot}}", $code);
			}
			
			$ad->set_property('code', $code);
		}
		
		return $ad;
	}
}
/*
<!-- START CUSTOM WIDGETBUCKS CODE -->
<div><script src="http://api.widgetbucks.com/script/ads.js?uid=CAcM7be51gG5tPg9"></script></div>
<!-- END CUSTOM WIDGETBUCKS CODE -->
*/
?>
