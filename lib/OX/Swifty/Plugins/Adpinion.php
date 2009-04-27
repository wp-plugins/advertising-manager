<?php
require_once(OX_SWIFTY_PATH . '/Adnet.php');	
/*
$_advman_networks['OX_Adnet_Adpinion'] = array(
	'www-create'	=>	'http://www.adpinion.com/',
	'www-signup'		=>	'http://www.adpinion.com/',
);
*/
/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class OX_Adnet_Adpinion extends OX_Adnet
{
	var $mnemonic = 'Adpinion';
	var $network_name = 'Adpinion';
	var $url = 'http://www.adpinion.com';
	
	function OX_Adnet_Adpinion()
	{
		$this->OX_Adnet();
	}
	
	function display($search, $replace)
	{
		if($this->get('width', true) > $this->get('height', true)) {
			$xwidth=18;
			$xheight=17;
		} else {
			$xwidth=0;
			$xheight=35;
		}
	
		$search[] = '{{xwidth}}';
		$search[] = '{{xheight}}';
		$replace[] = $this->get('width', true) + $xwidth;
		$replace[] = $this->get('height', true) + $xheight;
		
		return parent::display($search, $replace);
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '728x90',
			'height'=> '90',
			'width' => '728',
		);
		
		return $properties + parent::get_network_property_defaults();
	}
	
	function import_detect_network($code)
	{
		return ( preg_match('/src="http:\/\/www.adpinion.com\/app\//', $code, $matches) !==0);
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		$width = '';
		$height = '';
		if (preg_match("/website=(\w*)/", $code, $matches) != 0) {
			$this->set_property('account-id', $matches[1]);
			$code = str_replace("website={$matches[1]}", "website={{account-id}}'", $code);
		}
		if (preg_match("/width=(\w*)/", $code, $matches) != 0) {
			$width = $matches[1];
			$code = str_replace("width={$matches[1]}", "width={{width}}'", $code);
		}
		if (preg_match("/height=(\w*)/", $code, $matches) != 0) {
			$height = $matches[1];
			$code = str_replace("height={$matches[1]}", "height={{height}}'", $code);
		}
		if (preg_match("/style=\"width:(\d*)px;height:(\d*)px/", $code, $matches) != 0) {
			$code = str_replace("style=\"width:{$matches[1]}px;height:{$matches[2]}px", "style=\"width:{{xwidth}}px;height:{{xheight}}px", $code);
		}
		
		if ($width != '') {
			$this->set_property('width', $width);
		}
		if ($height != '') {
			$this->set_property('height', $height);
		}
		if (($width != '') && ($height != '')) {
			$this->set_property('adformat', $width . 'x' . $height);
		}
		
		$this->set_property('code', $code);
	}
}
/*
<iframe src="http://www.adpinion.com/app/adpinion_frame?website=133599&amp;width=468&amp;height=60" id="adframe" style="width:486px;height:60px;" scrolling="no" frameborder="0">
*/
?>
