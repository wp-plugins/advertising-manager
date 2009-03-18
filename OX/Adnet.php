<?php
if(!ADVMAN_VERSION) {die();}

class OX_Adnet
{
	var $name; //Name of this ad
	var $id; // ID of the ad
	var $title; //Used in widget displays only
	var $p; //$p holds Ad properties (e.g. dimensions etc.) - acessible through $this->get(''); see $this->get_default('') for default merged
	var $active; //whether this ad can display

	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Ad';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = '';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $network = '';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = '';
	
	//Global start up functions for all network classes	
	function OX_Adnet()
	{
		global $_advman;
		
		$this->network = get_class($this);
		
		// Set defaults if they are not already set
		if (!is_array($_advman['defaults'][$this->network])) {
			$this->reset_defaults();
			update_option('plugin_adsensem', $_advman);
		}
		
		$this->name = '';
		$this->title = '';
		$this->active = true;
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
	
	/**
	 * Returns the default for the given property
	 * @param $key the property in which to retrieve the default
	 */
	function get_default($key)
	{
		global $_advman;
		
		if (isset($_advman['defaults'][$this->network][$key])) {
			return $_advman['defaults'][$this->network][$key];
		} else {
			return '';
		}
	}
	
	/**
	 * Returns the default for the given property
	 * @param $key the property in which to retrieve the default
	 */
	function set_default($key, $value)
	{
		global $_advman;
		
		if (is_null($value)) {
			unset($_advman['defaults'][$this->network][$key]);
		} else {
			$_advman['defaults'][$this->network][$key] = $value;
		}
	}
	
	/**
	 * Sets a given property to a value
	 * @param $key the property in which to set the value
	 * @param $value the value in which to set the property.
	 */
	function set($key, $value)
	{
		if (!empty($key)) {
			if (!is_null($value)) {
				$this->p[$key] = $value;
			} else {
				unset($this->p[$key]);
			}
		}
	}
	
	/* Returns current setting, without defaults */
	function p($key)
	{
		return isset($this->p[$key]) ? $this->p[$key] : '';
	}
	
	function is_available()
	{
		global $post;
		global $_advman_counter;
		
		// Filter by active
		if (!$this->active) {
			return false;
		}
		
		// Filter by counter
		$counter = $this->get_default('counter');
		if (!empty($counter) && ($_advman_counter['network'][$this->network] >= $counter)) {
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
	
	function get_ad()
	{
		global $_advman;
		
		$search = array();
		$replace = array();
		
		$code = $this->get('html-before', true);
		$code.=$this->render_ad($search, $replace);
		$code .= $this->get('html-after', true);
		
		return $code;
	}

	function render_ad($search = array(), $replace = array())
	{
		$search[] = '{{random}}';
		$replace[] = mt_rand();
		
		$properties = $this->get_default_properties();
		foreach ($properties as $property => $default) {
			$search[] = '{{' . $property . '}}';
			$replace[] = $this->get($property, true);
		}
		
		return str_replace($search, $replace, $this->get('code'));
//		return $this->get('code');
	}
		
	function reset_defaults()
	{
		global $_advman;
		$_advman['defaults'][$this->network] = $this->get_default_properties();
	}	
	
	function get_default_properties()
	{
		return array (
			'account-id' => '',
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
	
	function save_defaults()
	{
		global $_advman;
		
		$properties = $this->get_default_properties();
		if (!empty($properties)) {
			foreach ($properties as $property => $default) {
				if (isset($_POST['advman-' . $property])) {
					$value = stripslashes($_POST['advman-' . $property]);
					$this->set_default($property, $value);
				}
			}
		}

		// Set width and height for non-custom formats
		$format = $this->get_default('adformat');
		if (!empty($format) && ($format !== 'custom')) {
			list($width, $height, $null) = split('[x]', $format);
			$this->set_default('width', $width);
			$this->set_default('height', $height);
		}
		
		// add an item to the audit trail
		$revisions = !empty($_advman['defaults'][$this->network]['revisions']) ? $_advman['defaults'][$this->network]['revisions'] : array();
		$revisions = OX_Tools::add_revision($revisions);
		$_advman['defaults'][$this->network]['revisions'] = $revisions;
	}
	
	function save_settings()
	{
		global $_advman;	
		
		if (isset($_POST['advman-name'])) {
			$this->name = $_POST['advman-name'];
		}
		
		if (isset($_POST['advman-active'])) {
			$this->active = ($_POST['advman-active'] == 'yes');
		}
		
		// Save some standard properties
		$properties = $this->get_default_properties();
		if (!empty($properties)) {
			foreach ($properties as $property => $default) {
				if (isset($_POST['advman-' . $property])) {
					$this->set($property, stripslashes($_POST['advman-' . $property]));
				}
			}
		}
		
		// Set width and height for non-custom formats
		$format = $this->get('adformat');
		if (!empty($format) && ($format !== 'custom')) {
			list($width, $height, $null) = split('[x]', $format);
			$this->set('width', $width);
			$this->set('height', $height);
		}
		
		// add an item to the audit trail
		$this->add_revision();
	}
	
	function add_revision()
	{
		$revisions = $this->get('revisions');
		$this->set('revisions', OX_Tools::add_revision($revisions));
	}
	
	//Convert defined ads into a simple list for outputting as alternates. Maybe limit types by network (once multiple networks supported)
	function get_alternate_ads()
	{
		global $_advman;
		$compat=array();
		foreach($_advman['ads'] as $oname => $oad){
			if( ($this->network !== $oad->network ) && ($this->get('width', true)==$oad->get('width', true)) && ($this->get('height', true)==$oad->get('height', true)) ){ $compat[$oname]=$oname; }
		}
		return $compat;
	}
	
	function import_detect_network($code)
	{
		return false;
	}
	
	function import_settings($code)
	{
		$this->set('code', $code);
	}
	
	function get_name_url()
	{
		return get_bloginfo('wpurl') . '/wp-admin/edit.php?page=advman-manage&advman-ad-name=' . $this->name;
	}
	
	function get_preview_url()
	{
		return get_bloginfo('wpurl') . '/wp-admin/edit.php?page=advman-manage&advman-ad-id=' . $this->id;
	}
}

?>
