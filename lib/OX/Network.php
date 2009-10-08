<?php
require_once (OX_LIB . '/Entity.php');

class OX_Network extends OX_Entity
{
	var $network_type;
	
	function OX_Network()
	{
		$this->OX_Entity();
	}
	
	function register_plugin(&$engine)
	{
		$engine->add_action('ad_network', get_class($this));
	}
	
	function get_default_properties()
	{
		global $advman_engine;
		
		return array (
			'adformat' => '728x90',
			'code' => '',
			'counter' => '',
			'height' => '90',
			'html-after' => '',
			'html-before' => '',
			'notes' => '',
			'openx-market' => $advman_engine->get_setting('openx-market'),
			'openx-market-cpm' => $advman_engine->get_setting('openx-market-cpm'),
			'show-pagetype' => array('archive','home','page','post','search'),
			'show-author' => '',
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
	
	function is_tag_detected($code)
	{
		return false;
	}
	
	function import($code, &$ad)
	{
		$ad->set_property('code', $code);
		$ad->network_type = get_class($this);
	}
	
	function get_ad_formats()
	{
		return array('all' => array('custom', '728x90', '468x60', '120x600', '160x600', '300x250', '125x125'));
	}
	
	function get_ad_colors()
	{
		return false;
	}
	
	function substitute_fields($ad, $search = array(), $replace = array())
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
		
		$code = $codeonly ? $ad->get('code') : ($ad->get('html-before') . $ad->get('code') . $ad->get('html-after'));
		return str_replace($search, $replace, $code);
	}
	
	function to_array()
	{
		$aNetwork = parent::to_array();
		$aNetwork['class'] = get_class($this);
		
		return $aNetwork;
	}
	
	function to_object($properties = null, $class = 'OX_Network')
	{
		$network = parent::to_object($properties, $class);
		
		if (!empty($properties)) {
			if (isset($properties['network_type'])) {
				$network->network_type = $properties['network_type'];
				unset($network->p['network_type']);
			}
		}
		
		return $network;
	}
}
?>
