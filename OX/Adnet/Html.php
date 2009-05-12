<?php
if(!ADVMAN_VERSION){die();}
require_once(ADVMAN_PATH . '/OX/Adnet.php');	

$_advman_networks['OX_Adnet_Html']  = array(
	'www-signup'		=>	'',
);

class OX_Adnet_Html extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Html';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'HTML Code';
	
	function OX_Adnet_Html()
	{
		$this->OX_Adnet();
	}
	
	function get_default_properties()
	{
		$properties = array(
		);
		return $properties + parent::get_default_properties();
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
			$this->set('width', $width);
		}
		if ($height != '') {
			$this->set('height', $height);
		}
		if (($width != '') && ($height != '')) {
			$this->set('adformat', $width . 'x' . $height); //Only set if both width and height present
		}
	}
}
?>