<?php
require_once(OX_LIB . '/Network.php');

class OX_Network_Html extends OX_Network
{
	var $network_name = 'HTML';
	
	function OX_Network_Html($network = null)
	{
		$this->OX_Network($network);
	}
	
	function get_network_property_defaults()
	{
		$properties = array();
		return $properties + parent::get_network_property_defaults();
	}
	
	function import($code, &$ad)
	{
		//Attempt to find html width/height strings
		$width = '';
		$height = '';
		if(preg_match('/width="(\w*)"/', $code, $matches)!=0) {
			$width = $matches[1];
		}
		if(preg_match('/height="(\w*)"/', $code, $matches)!=0) {
			$height = $matches[1];
		}
		if ($width != '') {
			$ad->set_property('width', $width);
		}
		if ($height != '') {
			$ad->set_property('height', $height);
		}
		if (($width != '') && ($height != '')) {
			$ad->set_property('adformat', $width . 'x' . $height); //Only set if both width and height present
		}
		
		return parent::import($code, $ad);
	}
}
?>
