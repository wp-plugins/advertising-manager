<?php
require_once (OX_LIB . '/Entity.php');

class OX_Ad extends OX_Entity
{
	var $network_type = 'OX_Ad_Html';
	
	function OX_Ad()
	{
		$this->OX_Entity();
	}
	
	function register_plugin(&$engine)
	{
		$engine->add_action('ad_network', get_class($this));
	}
	
	/**
	 * Returns a property setting
	 * @param $key the property name
	 */
	function get($key)
	{
		$property = $this->get_property($key);
		if ($property != '') {
			return $property;
		}
		
		$network = $this->get_network();
		return $network->get_property($key);
	}
	
	function get_network()
	{
		global $advman_engine;
		
		$type = $this->network_type;
		$network = $advman_engine->get_network($type);
		
		if (empty($network)) {
			$properties = $this->get_property_defaults();
			$properties['class'] = $type;
			$network = OX_Network::to_object($properties);
			$advman_engine->set_network($network);
		}
		
		return $network;
	}
	
	function get_network_property($key)
	{
		$network = $this->get_network();
		return $network->get_property($key);
	}
	
	function set_network_property($key, $value)
	{
		$network = $this->get_network();
		return $network->set_property($key, $value);
	}
	
	function get_default_properties()
	{
		$network = $this->get_network();
		return $network->get_properties();
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
		$authors = $this->get('show-author', true);
		if (!empty($authors)) {
			if (!in_array($post->post_author, $authors)) {
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
		$pageTypes = $this->get_property('show-pagetype');
		if (!empty($pageTypes)) {
			if (is_home() && !in_array('home', $pageTypes)) {
				return false;
			}
			if (is_single() && !in_array('post', $pageTypes)) {
				return false;
			}
			if (is_page() && !in_array('page', $pageTypes)) {
				return false;
			}
			if (is_archive() && !in_array('archive', $pageTypes)) {
				return false;
			}
			if (is_search() && !in_array('search', $pageTypes)) {
				return false;
			}
		}
		
		return true;
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
	
	function to_array()
	{
		$aAd = parent::to_array();
		$aAd['network_type'] = $this->network_type;
		
		return $aAd;
	}
	
	function to_object($properties = null, $class = 'OX_Ad')
	{
		$ad = parent::to_object($properties, $class);
		
		if (!empty($properties)) {
			if (isset($properties['network_type'])) {
				$ad->network_type = $properties['network_type'];
				unset($ad->p['network_type']);
			}
		}
		
		return $ad;
	}
}
?>
