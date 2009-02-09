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
		global $_adsensem;
		
		$this->network = get_class($this);
		
		// Set defaults if they are not already set
		if (!is_array($_adsensem['defaults'][$this->network])) {
			$this->reset_defaults();
			update_option('plugin_adsensem', $_adsensem);
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
		global $_adsensem;
		
		if (isset($_adsensem['defaults'][$this->network][$key])) {
			return $_adsensem['defaults'][$this->network][$key];
		} else {
			return '';
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
		// Filter by active
		if (!$this->active) {
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
		global $_adsensem;
		
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
	}
		
	function customiseSection($mode, $section)
	{
		return false;
	}
	
	function displaySection($mode, $section)
	{
		return;
	}
	
	function displayBeforeSection($mode, $section)
	{
		return;
	}
	
	function displayAfterSection($mode, $section)
	{
		return;
	}
	
	function reset_defaults()
	{
		global $_adsensem;
		$_adsensem['defaults'][$this->network] = $this->get_default_properties();
	}	
	
	function get_default_properties()
	{
		return array (
			'account-id' => '',
			'adformat' => '728x90',
			'code' => '',
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
		global $_adsensem;
		
		$properties = $this->get_default_properties();
		if (!empty($properties)) {
			foreach ($properties as $property => $default) {
				if (isset($_POST['adsensem-' . $property])) {
					$_adsensem['defaults'][$this->network][$property] = stripslashes($_POST['adsensem-' . $property]);
				}
			}
		}
		
		// add an item to the audit trail
		$revisions = OX_Tools::add_revision($_adsensem['defaults'][$this->network]['revisions']);
		$_adsensem['defaults'][$this->network]['revisions'] = $revisions;
	}
	
	function save_settings()
	{
		global $_adsensem;	
		
		if (isset($_POST['adsensem-name'])) {
			$this->name = $_POST['adsensem-name'];
		}
		
		if (isset($_POST['adsensem-active'])) {
			$this->active = ($_POST['adsensem-active'] == 'yes');
		}
		
		// Save some standard properties
		$properties = $this->get_default_properties();
		if (!empty($properties)) {
			foreach ($properties as $property => $default) {
				if (isset($_POST['adsensem-' . $property])) {
					$this->set($property, stripslashes($_POST['adsensem-' . $property]));
				}
			}
		}
		
		// Set width and height for non-custom formats
		$format = $this->get('adformat');
		if ($format !== 'custom') {
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
		global $_adsensem;
		$compat=array();
		foreach($_adsensem['ads'] as $oname => $oad){
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
}

?>
