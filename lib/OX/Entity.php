<?php
require_once (OX_LIB . '/Plugin.php');

class OX_Entity extends OX_Plugin
{
	var $name; //Name of this ad
	var $id; // ID of the ad
	var $active = true; //whether this ad can display
	
	var $p; //$p holds Ad properties (e.g. dimensions etc.) - acessible through $this->get(''); see $this->get_network_property('') for default merged
	
	//Global start up functions for all network classes	
	function OX_Entity()
	{
	}
	
	function register_plugin(&$engine)
	{
		$engine->add_action('ad_type', get_class($this));
	}
	
	function get_property($key)
	{
		$properties = $this->p;
		return isset($properties[$key]) ? $properties[$key] : '';
	}
	
	function get_properties()
	{
		return $this->p;
	}
	
	function set_property($key, $value)
	{
		if (is_null($value)) {
			unset($this->p[$key]);
		} else {
			$this->p[$key] = $value;
		}
		
		if ($key == 'adformat' && $value !== 'custom') {
			if (empty($value)) {
				$width = '';
				$height = '';
			} else {
				list($width, $height) = preg_split('/[x]/', $value);
			}
			$this->set_property('width', $width);
			$this->set_property('height', $height);
		}
	}
	
	function reset_properties()
	{
		$this->p = $this->get_default_properties();
	}
	
	function get_default_properties()
	{
		return array();
	}
	
	/**
	 * Is this ad able to be displayed given the context, user, etc.?
	 */
	function is_available()
	{
		return true;
	}
	
	function add_revision()
	{
		$revisions = $this->get_property('revisions');
		
		// Get the user login information
		global $user_login;
		get_currentuserinfo();
		
		// If there is no revisions, use my own revisions
		if (!is_array($revisions)) {
			$revisions = array();
		}
		
		// Deal with revisions
		$r = array();
		$now = mktime();
		$r[$now] = $user_login;
		
		// Get rid of revisions more than 30 days old
		if (!empty($revisions)) {
			foreach ($revisions as $ts => $user) {
				$days = (strtotime($now) - strtotime($ts)) / 86400 + 1;
				if ($days <= 30) {
					$r[$ts] = $user;
				}
			}
		}
		
		krsort($r);
		
		$this->set_property('revisions', $r);
	}
	
	function to_array()
	{
		$aEntity = $this->p;
		$aEntity['name'] = $this->name;
		$aEntity['id'] = $this->id;
		$aEntity['active'] = $this->active;
		
		return $aEntity;
	}
	
	function to_object($properties = null, $class = 'OX_Entity')
	{
		$entity = new $class;
		
		if (!empty($properties)) {
			$names = array('name', 'id', 'active');
			
			if (isset($properties['name'])) {
				$entity->name = $properties['name'];
			}
			if (isset($properties['id'])) {
				$entity->id = $properties['id'];
			}
			if (isset($properties['active'])) {
				$entity->active = $properties['active'];
			}
			
			foreach ($properties as $name => $value) {
				if (!in_array($name, $names)) {
					$entity->p[$name] = $value;
				}
			}
		}
		
		return $entity;
	}
}
?>
