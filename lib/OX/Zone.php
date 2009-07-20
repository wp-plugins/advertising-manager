<?php
require_once (OX_LIB . '/Entity.php');

class OX_Zone extends OX_Entity
{
	function OX_Zone()
	{
		$this->OX_Entity();
	}
	
	function get_default_properties()
	{
		global $advman_engine;
		
		return array (
			'adformat' => '728x90',
			'counter' => '',
			'height' => '90',
			'html-after' => '',
			'html-before' => '',
			'notes' => '',
			'openx-market' => $advman_engine->get_setting('openx-market'),
			'openx-market-cpm' => $advman_engine->get_setting('openx-market-cpm'),
			'width' => '728',
		);
	}
	
	/**
	 * Is this ad able to be displayed given the context, user, etc.?
	 */
	function is_available()
	{
		return true;
	}
	
	function to_array()
	{
		return parent::to_array();
	}
	
	function to_object($properties = null, $class = 'OX_Zone')
	{
		return parent::to_object($properties, $class);
	}
}
?>
