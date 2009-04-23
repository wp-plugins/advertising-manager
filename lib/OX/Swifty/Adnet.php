<?php
class OX_Adnet
{
	var $name; //Name of this ad
	var $id; // ID of the ad
	var $p; //$p holds Ad properties (e.g. dimensions etc.) - acessible through $this->get(''); see $this->get_default('') for default merged
	var $active; //whether this ad can display
	
	static $defaults;
	static $revisions;
	static $shortName = 'Ad';
	static $url = '';
	static $networkName = '';
	
	//Global start up functions for all network classes	
	function OX_Adnet()
	{
		global $advman_engine;
		
		$this->active = true;
		$this->name = OX_Tools::generate_name(self::$shortName);
	}
	
	function getNetworkName()
	{
		return self::$networkName;
	}
	
	function getNetwork()
	{
		return get_class($this);
	}
	
	/**
	 * Returns a property setting
	 * @param $key the property name
	 * @param $default whether to return the default setting if the property is not set
	 */
	function get($key, $default = false)
	{
		// Return just the property
		if (!$default) {
			return isset($this->p[$key]) ? $this->p[$key] : '';
		}
		
		// Return the default
		if (!isset($this->p[$key]) || $this->p[$key] == '') {
			return $this->get_default($key);
		}
		
		// Return the property
		return $this->p[$key];
	}
	
	function get_default($key)
	{
		$defaults = self::$defaults;
		return isset($defaults[$key]) ? $defaults[$key] : '';
	}
	
	/**
	 * Returns the given property
	 * @param $key the property that is to be set
	 * @param $value the value to set the property to.  If the value is null, the property will be deleted.
	 * @param $default if true, the default will be set.  Otherwise, the property will be set.
	 */
	function set($key, $value, $default = false)
	{
		$properties = $default ? self::$defaults : $this->p;
		
		$v = isset($properties[$key]) ? $properties[$key] : null;
		
		if (is_null($value)) {
			unset($properties[$key]);
		} else {
			$properties[$key] = $value;
		}
		
		if ($default) {
			self::$defaults = $properties;
		} else {
			$this->p = $properties;
		}
		
		if ($key == 'adformat' && $key !== 'custom') {
			list($width, $height, $null) = split('[x]', $value);
			$this->set('width', $width, $default);
			$this->set('height', $height, $default);
		}
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
		
		// Filter by counter
		$counter = $this->get('counter', true);
		if (!empty($counter) && ($advman_engine->counter['network'][$this->network] >= $counter)) {
			return false;
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
	
	function render_ad($search = array(), $replace = array())
	{
		$search[] = '{{random}}';
		$replace[] = mt_rand();
		$search[] = '{{timestamp}}';
		$replace[] = time();
		
		$properties = $this->get_default_properties();
		foreach ($properties as $property => $default) {
			$search[] = '{{' . $property . '}}';
			$replace[] = $this->get($property, true);
		}
		$code  = $this->get('html-before', true);
		$code .= $this->get('code');
		$code .= $this->get('html-after', true);
		
		return str_replace($search, $replace, $code);
//		return $this->get('code');
	}
	
	function get_default_properties()
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
	
	function import_detect_network($code)
	{
		return false;
	}
	
	function import_settings($code)
	{
		$this->set('code', $code);
	}
	
	function get_preview_url()
	{
		return get_bloginfo('wpurl') . '/wp-admin/edit.php?page=advman-manage&advman-ad-id=' . $this->id;
	}

	function get_last_edit($default = false)
	{
		$revisions = $default ? self::$revisions : $this->get('revisions');
		
		$last_user = __('Unknown', 'advman');
		$last_timestamp = 0;
		
		if (!empty($revisions)) {
			foreach($revisions as $t => $u) {
				$last_user = $u;
				$last_timestamp = $t;
				break; // just get first one - the array is sorted by reverse date
			}
		}
		
		return array($last_user, $last_timestamp);
	}
	
	function add_revision($default = false)
	{
		if ($default) {
			$revisions = self::$revisions;
		} else {
			$revisions = $this->get('revisions');
		}
		
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
		
		if ($default) {
			self::$revisions = $r;
		} else {
			$this->set('revisions', $r);
		}
	}
}

?>
