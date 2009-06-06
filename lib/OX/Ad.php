<?php
require_once (OX_LIB . '/Plugin.php');

class OX_Ad extends OX_Plugin
{
	var $name; //Name of this ad
	var $id; // ID of the ad
	var $active; //whether this ad can display
	
	var $p; //$p holds Ad properties (e.g. dimensions etc.) - acessible through $this->get(''); see $this->get_network_property('') for default merged
	var $np; //$np holds Network properties - defaults, network settings, etc.
	
	//Global start up functions for all network classes	
	function OX_Ad()
	{
	}
	
	function register_plugin(&$engine)
	{
		$engine->addAction('ad_network', get_class($this));
	}
	
	/**
	 * Returns a property setting
	 * @param $key the property name
	 */
	function get($key)
	{
		$property = $this->get_property($key);
		return $property == '' ? $this->get_network_property($key) : $property;
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
	
	function set_network_property($key, $value)
	{
		$properties = $this->np;
		$this->_set($properties, $key, $value);
		$this->np = $properties;
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
	
	function reset_network_properties()
	{
		$this->np = $this->get_network_property_defaults();
	}
	
	function get_network_property_defaults()
	{
		return array (
			'adformat' => '728x90',
			'code' => '',
			'counter' => '',
			'height' => '90',
			'html-after' => '',
			'html-before' => '',
			'notes' => '',
			'openx-market' => 'yes',
			'openx-market-cpm' => '0.20',
			'show-archive' => 'yes',
			'show-author' => 'all',
			'show-home' => 'yes',
			'show-page' => 'yes',
			'show-post' => 'yes',
			'show-search' => 'yes',
			'weight' => '1',
			'width' => '728',
		);
	}
	
	/**
	 * Is this ad able to be displayed given the context, user, etc.?
	 */
	function is_available()
	{
		global $advman_engine;
		global $post;
		
		// Filter by active
		if (!$this->active) {
			return false;
		}
		
		// Filter by network counter
		$counter = $this->get_network_property('counter');
		if (!empty($counter)) {
			if ($advman_engine->counter['network'][get_class($this)] >= $counter) {
				return false;
			}
		}
		// Filter by ad counter
		$counter = $this->get_property('counter');
		if (!empty($counter)) {
			if ($advman_engine->counter['id'][$this->id] >= $counter) {
				return false;
			}
		}
		
		// Filter by author
		$author = $this->get('show-author', true);
		if (!empty($author) && ($author != 'all')) {
			if ($post->post_author != $this->get('show-author')) {
				return false;
			}
		}
/*		
		// Filter by category
		$cat = $this->get('show-category', true);
		$cat = '1';
		if (!empty($cat) && ($cat != 'all')) {
			$categories = get_the_category();
			$found = false;
			foreach ($categories as $category) {
				if ($category->cat_ID == $cat) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				return false;
			}
		}
*/		
		//Extend this to include all ad-specific checks, so it can used to filter adzone groups in future.
		return (
			( ($this->get('show-home', true) == 'yes') && is_home() ) ||
			( ($this->get('show-post', true) == 'yes') && is_single() ) ||
			( ($this->get('show-page', true) == 'yes') && is_page() ) ||
			( ($this->get('show-archive', true) == 'yes') && is_archive() ) ||
			( ($this->get('show-search', true) == 'yes') && is_search() )
		);
	}
	
	function display($codeonly = false, $search = array(), $replace = array())
	{
		$search[] = '{{random}}';
		$replace[] = mt_rand();
		$search[] = '{{timestamp}}';
		$replace[] = time();
		
		$properties = $this->get_network_property_defaults();
		foreach ($properties as $property => $value) {
			$search[] = '{{' . $property . '}}';
			$replace[] = $this->get($property);
		}
		
		$code = $codeonly ? $this->get('code') : ($this->get('html-before') . $this->get('code') . $this->get('html-after'));
		
		return str_replace($search, $replace, $code);
//		return $this->get('code');
	}
	
	function import_detect_network($code)
	{
		return false;
	}
	
	function import_settings($code)
	{
		$this->set_property('code', $code);
	}
	
	function get_preview_url()
	{
		return get_bloginfo('wpurl') . '/wp-admin/edit.php?page=advman-manage&advman-ad-id=' . $this->id;
	}

	function get_ad_formats()
	{
		return array('all' => array('custom', '728x90', '468x60', '120x600', '160x600', '300x250', '125x125'));
	}
	function get_ad_colors()
	{
		return false;
	}
	function add_revision($network = false)
	{
		$revisions = $network ? $this->get_network_property('revisions') : $this->get_property('revisions');
		
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
		
		if ($network) {
			$this->set_network_property('revisions', $r);
		} else {
			$this->set_property('revisions', $r);
		}
	}
	
	function to_array()
	{
		$aAd = $this->p;
		$aAd['name'] = $this->name;
		$aAd['id'] = $this->id;
		$aAd['active'] = $this->active;
		
		return $aAd;
	}
}

?>
