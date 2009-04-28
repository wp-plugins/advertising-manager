<?php
require_once(OX_SWIFTY_PATH . '/Ad.php');	
/*
$_advman_networks['OX_Swifty_Ad
_Html']  = array(
	'www-signup'		=>	'',
);
*/
class OX_Swifty_Ad
_Html extends OX_Swifty_Ad

{
	function OX_Swifty_Ad
_Html()
	{
		$this->set_network_property('mnemonic', 'Html');
		$this->set_network_property('name', 'HTML Code');

		$this->OX_Swifty_Ad
();
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
		);
		return $properties + parent::get_network_property_defaults();
	}
	
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
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
			$this->set_property('width', $width);
		}
		if ($height != '') {
			$this->set_property('height', $height);
		}
		if (($width != '') && ($height != '')) {
			$this->set_property('adformat', $width . 'x' . $height); //Only set if both width and height present
		}
	}
}
?>
