<?php
require_once(OX_LIB . '/Network.php');	

class OX_Plugin_Adpinion extends OX_Network
{
	function OX_Plugin_Adpinion()
	{
		$this->OX_Network();
		$this->name = 'Adpinion';
		$this->short_name = 'adpinion';
	}
	
	/**
	 * This function is called statically from the ad engine.  Use this function to put any hooks in the ad engine that you want to use.
	 */
	function register_plugin(&$engine)
	{
		$engine->add_action('ad_network', get_class());
	}
	
	function substitute_fields($ad, $search = array(), $replace = array())
	{
		if($ad->get('width', true) > $ad->get('height', true)) {
			$xwidth=18;
			$xheight=17;
		} else {
			$xwidth=0;
			$xheight=35;
		}
	
		$search[] = '{{xwidth}}';
		$search[] = '{{xheight}}';
		$replace[] = $ad->get('width', true) + $xwidth;
		$replace[] = $ad->get('height', true) + $xheight;
		
		return parent::substitue_fields($ad, $search, $replace);
	}
	
	function get_default_properties()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '728x90',
			'height'=> '90',
			'width' => '728',
		);
		
		return $properties + parent::get_default_properties();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('728x90', '468x60', '120x600', '160x600', '300x250'));
	}
	
	function import($code)
	{
		$ad = false;
		
		if ( preg_match('/src="http:\/\/www.adpinion.com\/app\//', $code, $matches) !== 0 ) {
			
			$ad = OX_Ad::to_object();
			$ad->network_type = get_class();
			
			$width = '';
			$height = '';
			if (preg_match("/website=(\w*)/", $code, $matches) != 0) {
				$ad->set_property('account-id', $matches[1]);
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
				$ad->set_property('width', $width);
			}
			if ($height != '') {
				$ad->set_property('height', $height);
			}
			if (($width != '') && ($height != '')) {
				$ad->set_property('adformat', $width . 'x' . $height);
			}
			
			$ad->set_property('code', $code);
		}
		
		return $ad;
	}
}
/*
<iframe src="http://www.adpinion.com/app/adpinion_frame?website=133599&amp;width=468&amp;height=60" id="adframe" style="width:486px;height:60px;" scrolling="no" frameborder="0">
*/
?>
