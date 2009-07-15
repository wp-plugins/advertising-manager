<?php
require_once (OX_LIB . '/Plugin.php');

class OX_Zone extends OX_Plugin
{
	var $name; //Name of this zone
	var $id; // ID of the zone
	var $active; //whether this zone can display
	
	var $p; //$p holds Zone properties (e.g. dimensions etc.)
	
	//Global start up functions for all network classes	
	function OX_Zone()
	{
	}
	
	function register_plugin(&$engine)
	{
		$engine->addAction('zone', get_class($this));
	}
	
	function get_property($key)
	{
		$properties = $this->p;
		return isset($properties[$key]) ? $properties[$key] : '';
	}
	
	function get_network_property($key)
	{
		$properties = $this->np;
		return isset($properties[$key]) ? $properties[$key] : '';
	}
	
	/**
	 * Returns the given property
	 * @param $key the property that is to be set
	 * @param $value the value to set the property to.  If the value is null, the property will be deleted.
	 * @param $default if true, the default will be set.  Otherwise, the property will be set.
	 */
	function set_property($key, $value)
	{
		$properties = $this->p;
		$this->_set($properties, $key, $value);
		$this->p = $properties;
	}
	
	function _set(&$properties, $key, $value)
	{
		if (is_null($value)) {
			unset($properties[$key]);
		} else {
			$properties[$key] = $value;
		}
		
		if ($key == 'adformat' && $value !== 'custom') {
			if (empty($value)) {
				$width = '';
				$height = '';
			} else {
				list($width, $height) = split('[x]', $value);
			}
			$this->_set($properties, 'width', $width);
			$this->_set($properties, 'height', $height);
		}
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
		$aZone = $this->p;
		$aZone['name'] = $this->name;
		$aZone['id'] = $this->id;
		$aZone['active'] = $this->active;
		
		return $aZone;
	}
}

?>