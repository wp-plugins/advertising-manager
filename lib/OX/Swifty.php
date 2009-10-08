<?php
require_once(OX_LIB . '/Ad.php');
require_once(OX_LIB . '/Dal.php');
require_once(OX_LIB . '/Html.php');
require_once(OX_LIB . '/Network.php');
require_once(OX_LIB . '/Zone.php');

class OX_Swifty
{
	var $dal;
	var $counter;
	var $actions;
	
	function OX_Swifty($dalClass = null)
	{
		// Functions here are initialisation only - plugins have not been loaded (so we cannot initialise data)
		$this->counter = array();
		$this->actions = array();
		
		// Load all Swifty plugins
		OX_Tools::load_plugins(OX_LIB . '/Plugins', $this);
		
		// Load the data access layer
		$this->dal = is_null($dalClass) ? new OX_Dal() : new $dalClass();
		
		// Sync with OpenX
		$this->sync();
	}
	
	function add_action($key, $value)
	{
		$actions = !empty($this->actions[$key]) ? $this->actions[$key] : array();
		$actions[] = $value;
		
		$this->actions[$key] = $actions;
	}
	
	function get_action($key)
	{
		return $this->actions[$key];
	}
	
	function get_setting($key)
	{
		return $this->dal->select_setting($key);
	}
	
	function set_setting($key, $value)
	{
		return $this->dal->update_setting($key, $value);
	}
	
	function add_ad(&$ad)
	{
		$ad->add_revision();
		return $this->dal->insert_ad($ad);
	}
	function add_ads(&$ads)
	{
		$aads = array();
		foreach ($ads as $ad) {
			$aads[] = $this->add_ad($ad);
		}
		return $aads;
	}
	
	function remove_ad($id)
	{
		return $this->dal->delete_ad($id);
	}
	
	function remove_ads($ids)
	{
		$aads = array();
		foreach ($ids as $id) {
			$aads[] = $this->remove_ad($id);
		}
		return $aads;
	}
	
	function get_ads($ids = null)
	{
		if (empty($ids)) {
			return $this->dal->select_ads();
		} else {
			$aads = array();
			foreach ($ids as $id) {
				$aads[$id] = $this->get_ad($id);
			}
			return $aads;
		}
	}
	
	function get_ad($id)
	{
		return $this->dal->select_ad($id);
	}
	
	function set_ad(&$ad)
	{
		$ad->add_revision();
		return $this->dal->update_ad($ad);
	}
	
	function set_ad_active(&$ad, $active)
	{
		if ($ad->active != $active) {
			$ad->active = $active;
			return $this->set_ad($ad);
		}
		
		return $ad;
	}
	
	function copy_ad($id)
	{
		$ad = $this->dal->select_ad($id);
		if ($ad) {
			$ad = version_compare(phpversion(), '5.0') < 0 ? $ad : clone($ad); // Hack to deal with PHP 4/5 incompatiblity with cloning
			$ad->add_revision();
			return $this->dal->insert_ad($ad);
		}
		
		return false;
	}
	
	function add_zone(&$zone)
	{
		$zone->add_revision();
		return $this->dal->insert_zone($zone);
	}
	
	function remove_zone($id)
	{
		return $this->dal->delete_zone($id);
	}
	
	function get_zones()
	{
		return $this->dal->select_zones();
	}
	
	function get_zone($id)
	{
		return $this->dal->select_zone($id);
	}
	
	function set_zone(&$zone)
	{
		$zone->add_revision();
		return $this->dal->update_zone($zone);
	}
	
	function copy_zone($id)
	{
		$zone = $this->dal->select_zone($id);
		if ($zone) {
			$zone = version_compare(phpversion(), '5.0') < 0 ? $zone : clone($zone); // Hack to deal with PHP 4/5 incompatiblity with cloning
			$zone->add_revision();
			return $this->dal->insert_zone($zone);
		}
		
		return false;
	}
	
	function add_network(&$network)
	{
		$network->add_revision();
		return $this->dal->insert_network($network);
	}
	function add_networks(&$networks)
	{
		$new_networks = array();
		foreach ($networks as $network) {
			$new_networks[] = $this->add_network($network);
		}
		return $new_networks;
	}
	
	function remove_network($id)
	{
		return $this->dal->delete_network($id);
	}
	
	function remove_networks($ids)
	{
		$networks = array();
		foreach ($ids as $id) {
			$networks[] = $this->remove_network($id);
		}
		return $networks;
	}
	
	function get_networks($ids = null)
	{
		if (empty($ids)) {
			return $this->dal->select_networks();
		} else {
			$networks = array();
			foreach ($ids as $id) {
				$networks[$id] = $this->get_network($id);
			}
			return $networks;
		}
	}
	
	function get_network($id)
	{
		return $this->dal->select_network($id);
	}
	
	function set_network(&$network)
	{
		$network->add_revision();
		return $this->dal->update_network($network);
	}
	
	function copy_network($id)
	{
		$network = $this->dal->select_network($id);
		if ($network) {
			$network = version_compare(phpversion(), '5.0') < 0 ? $network : clone($network); // Hack to deal with PHP 4/5 incompatiblity with cloning
			$network->add_revision();
			return $this->dal->insert_network($network);
		}
		
		return false;
	}
	
	function import_ad_tag($tag)
	{
		global $advman_engine;
		
		$imported = false;
		$ad = OX_Ad::to_object();
		
		if (!empty($tag)) {
			$network_types = $this->get_action('ad_network');
			foreach ($network_types as $network_type) {
				if (call_user_func(array($network_type, 'import'), $tag, $ad)) {
					$imported = true;
					break; //leave the foreach loop
				}
			}
		}
		
		// Not a pre-defined network - we will make it HTML code...
		if (!$imported) {
			OX_Network_Html::import($tag, $ad);
		}
		
		return $this->add_ad($ad);
	}
	
	function choose_ad($name = null)
	{
		global $advman_engine;
		
		if (empty($name)) {
			$name = $this->get_setting('default-ad');
		}
		if (!empty($name)) {
			// Find the ads which match the name
			$ads = $advman_engine->get_ads();
			$totalWeight = 0;
			$validAds = array();
			foreach ($ads as $id => $ad) {
				if ( ($ad->name == $name) && ($ad->is_available()) ) {
					$weight = $ad->get('weight');
					if ($weight > 0) {
						$validAds[] = $ad;
						$totalWeight += $weight;
					}
				}
			}
			// Pick the ad
			// Generate a number between 0 and 1
			$rnd = (mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX);
			// Loop through ads until the selected one is chosen
			$wt = 0;
			foreach ($validAds as $ad) {
				$wt += $ad->get('weight');
				if ( ($wt / $totalWeight) > $rnd) {
					// Update the counters for this ad
					$this->update_counters($ad);
					// Display the ad
					return $ad;
				}
			}
		}
	}
	
	function update_counters($ad)
	{
		if (!empty($ad)) {
			if (empty($this->counter['id'][$ad->id])) {
				$this->counter['id'][$ad->id] = 1;
			} else {
				$this->counter['id'][$ad->id]++;
			}
			
			if (empty($this->counter['network'][strtolower(get_class($ad))])) {
				$this->counter['network'][strtolower(get_class($ad))] = 1;
			} else {
				$this->counter['network'][strtolower(get_class($ad))]++;
			}
		}
	}
	/**
	 * This function synchornises with the central server.  This will be used to pass ad deals to publishers if publisher choose to accept
	 */
	function sync()
	{
		$sync = $this->get_setting('openx-sync');
		if ($sync) {
			$timestamp = $this->get_setting('last-sync');
//			$timestamp = 1235710700; //FOR TESTING
			$now = mktime(0,0,0);
			if (empty($timestamp) || ($now - $timestamp > 0) ) {
				$this->set_setting('last-sync', $now);
				
				$params = array(
					'p' => $this->get_setting('product-name'),
					'i' => $this->get_setting('publisher-id'),
					'v' => $this->get_setting('version'),
					'w' => $this->get_setting('host-version'),
					'e' => $this->get_setting('admin-email'),
					's' => $this->get_setting('website-url'),
				);
				
				$id = base64_encode(serialize($params));
	
				$url = 'http://code.openx.org/sync.php?id=' . $id;
//				$url = 'http://localhost:8888/wordpress.27/wp-content/plugins/advertising-manager/sync.php?XDEBUG_SESSION_START=' . time() . '&id=' . $id;
				$data = @file_get_contents($url);
			}
		}
	}
}
?>