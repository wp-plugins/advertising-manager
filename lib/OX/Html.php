<?php
require_once(OX_LIB . '/Network.php');

class OX_Plugin_Html extends OX_Network
{
	function OX_Plugin_Html()
	{
		$this->OX_Network();
		$this->name = 'HTML';
		$this->short_name = 'html';
	}
	
	function import($code, &$ad)
	{
		$ad = OX_Ad::to_object();
		$ad->network_type = get_class();
		
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
		
		$ad->set_property('code', $code);
		
		return $ad;
	}
}
?>
