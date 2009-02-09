<?php
if(!ADVMAN_VERSION) {die();}

class OX_Adnet
{
	var $name; //Name of this ad
	var $id; // ID of the ad
	var $title; //Used in widget displays only
	var $p; //$p holds Ad properties (e.g. dimensions etc.) - acessible through $this->p[''] and $this->p(''); see $this->pd('') for default merged
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
		
		if (!is_array($_adsensem['defaults'][$this->network])) {
			$this->reset_defaults();
			update_option('plugin_adsensem', $_adsensem);
		}
		
		$this->p = array();
		$this->name = '';
		$this->title = '';
		$this->active = true;
	}
	
	/* Returns current setting, without defaults */
	function p($key)
	{
		return $this->p[$key];
	}
	
	/* Returns current default for this network */
	function d($key){
		global $_adsensem;
		return $_adsensem['defaults'][$this->network][$key];
	}
	
	/* Returns current setting, merged with defaults */
	function pd($key)
	{
		global $_adsensem;

		$value = $this->p($key);
		if (empty($value)) {
			$value = $this->d($key);
		}
		return $value;
	}
			
	function is_available()
	{
		global $post;
		// Filter by active
		if (!$this->active) {
			return false;
		}
		// Filter by author
		$author = $this->pd('show-author');
		if (!empty($author) && ($author != 'all')) {
			if ($post->post_author != $this->p['show-author']) {
				return false;
			}
		}
/*		
		// Filter by category
		$cat = $this->pd('show-category');
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
			( ($this->pd('show-home') == 'yes') && is_home() ) ||
			( ($this->pd('show-post') == 'yes') && is_single() ) ||
			( ($this->pd('show-page') == 'yes') && is_page() ) ||
			( ($this->pd('show-archive') == 'yes') && is_archive() ) ||
			( ($this->pd('show-search') == 'yes') && is_search() )
		);
	}
	
	function get_ad()
	{
		global $_adsensem;
		
		$search = array();
		$replace = array();
		
		$code = $this->pd('html-before');
		$code.=$this->render_ad($search, $replace);
		$code .= $this->pd('html-after');
		
		return $code;
	}

	function render_ad($search = array(), $replace = array())
	{
		$search[] = '{{random}}';
		$replace[] = mt_rand();
		
		$properties = $this->get_default_properties();
		foreach ($properties as $property => $default) {
			$search[] = '{{' . $property . '}}';
			$replace[] = $this->pd($property);
		}
		
		return str_replace($search, $replace, $this->p['code']);
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
		$_adsensem['defaults'][$this->network]['revisions'] = $this->add_revision($_adsensem['defaults'][$this->network]['revisions']);
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
					$this->p[$property]=stripslashes($_POST['adsensem-' . $property]);
				}
			}
		}
		
		// Set width and height for non-custom formats
		if ($this->p['adformat'] !== 'custom') {
			list($this->p['width'],$this->p['height'],$null) = split('[x]',$this->p('adformat'));
		}
		
		// add an item to the audit trail
		$this->add_revision();
	}
	
	function add_revision($revisions = null)
	{
		// If there is no revisions, use my own revisions
		if (empty($revisions)) {
			$revisions = !empty($this->p['revisions']) ? $this->p['revisions'] : array();
		}
		// Deal with revisions
		$r = array();
		$now = mktime();
		$r[$now] = get_current_user();
		
		if (!empty($revisions)) {
			foreach ($revisions as $ts => $user) {
				$days = (strtotime($now) - strtotime($ts)) / 86400 + 1;
				if ($days <= 30) {
					$r[$ts] = $user;
				}
			}
		}
		krsort($r);
		return $r;
	}
	
	//Convert defined ads into a simple list for outputting as alternates. Maybe limit types by network (once multiple networks supported)
	function get_alternate_ads()
	{
		global $_adsensem;
		$compat=array();
		foreach($_adsensem['ads'] as $oname => $oad){
			if( ($this->network !== $oad->network ) && ($this->pd('width')==$oad->pd('width')) && ($this->pd('height')==$oad->pd('height')) ){ $compat[$oname]=$oname; }
		}
		return $compat;
	}
	
	function import_detect_network($code)
	{
		return false;
	}
	
	function import_settings($code)
	{
		$this->p['code'] = $code;
	}
}

?>
