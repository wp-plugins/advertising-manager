<?php
require_once(OX_SWIFTY_PATH . '/Adnet.php');	
/*
$_advman_networks['OX_Adnet_Widgetbucks']	= array(
		'www-create' => 'http://www.widgetbucks.com/widget.page?action=call&widgetID=',
		'www-signup' => 'http://www.widgetbucks.com/home.page?referrer=468034'
		);
*/
class OX_Swifty_Plugins_Widgetbucks extends OX_Adnet
{
	var $mnemonic = 'Widgetbucks';
	var $network_name = 'WidgetBucks';
	var $url = 'http://www.widgetbucks.com';
	
	function OX_Swifty_Plugins_Widgetbucks()
	{
		$this->OX_Adnet();
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
			'slot' => '',
		);
		return $properties + parent::get_network_property_defaults();
	}
	
	function import_detect_network($code)
	{
		return (preg_match('/(\w*)\.widgetbucks.com\/script\/(\w*).js\?uid=(\w*)/', $code, $matches));
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match('/(\w*)\.widgetbucks.com\/script\/(\w*).js\?uid=(\w*)/', $code, $matches)!=0) { 
			$this->set_property('slot', $matches[3]);
			$code = str_replace("{$matches[1]}.widgetbucks.com/script/{$matches[2]}.js?uid={$matches[3]}", "{$matches[1]}.widgetbucks.com/script/{$matches[2]}.js?uid={{slot}}", $code);
		}
		
		$this->set_property('code', $code);
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
