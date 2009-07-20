<?php
require_once(OX_LIB . '/Network.php');	

class OX_Plugin_Widgetbucks extends OX_Network
{
	var $network_name = 'WidgetBucks';
	var $url = 'http://www.widgetbucks.com';
	
	function OX_Plugin_Widgetbucks($network = null)
	{
		$this->OX_Network($network);
		$this->name = 'widgetbucks';  // Short name which is the prefix for the default name of ads
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
			'slot' => '',
		);
		return $properties + parent::get_network_property_defaults();
	}
	
	function get_ad_colors()
	{
		return array('border', 'title', 'bg', 'text');
	}
	
	function is_tag_detected($code)
	{
		return (preg_match('/(\w*)\.widgetbucks.com\/script\/(\w*).js\?uid=(\w*)/', $code, $matches));
	}
		
	function import($code, &$ad)
	{
		if (preg_match('/(\w*)\.widgetbucks.com\/script\/(\w*).js\?uid=(\w*)/', $code, $matches)!=0) { 
			$ad->set_property('slot', $matches[3]);
			$code = str_replace("{$matches[1]}.widgetbucks.com/script/{$matches[2]}.js?uid={$matches[3]}", "{$matches[1]}.widgetbucks.com/script/{$matches[2]}.js?uid={{slot}}", $code);
		}
		
		return parent::import($code, $ad);
	}

	function _form_settings_help()
	{
?><tr><td><p>Configuration is available through the <a href="http://www.widgetbucks.com/" target="_blank">WidgetBucks site</a>. 
Account maintenance links:</p>
<ul>
<li><a href="http://www.widgetbucks.com/myWidgets.page?action=call" target="_blank">My Widgets</a><br />
		View, manage and create widgets.</li>
<li><a href="http://www.widgetbucks.com/myBucks.page?action=call" target="_blank">My Bucks</a><br />
		View your account balance and payment schedule.</li>
<li><a href="https://www.widgetbucks.com/mySettings.page?action=call" target="_blank">My Settings</a><br />
		Change account details and other global settings.</li>
</ul>
</td></tr><?php
	}
}
/*
<!-- START CUSTOM WIDGETBUCKS CODE -->
<div><script src="http://api.widgetbucks.com/script/ads.js?uid=CAcM7be51gG5tPg9"></script></div>
<!-- END CUSTOM WIDGETBUCKS CODE -->
*/
?>
