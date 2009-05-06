<?php
require_once(OX_LIB . '/Ad.php');
require_once(OX_LIB . '/Dal.php');

class OX_Swifty
{
	var $dal;
	var $ad_networks;
	var $counter;
	var $actions;
	
	function OX_Swifty($dalClass = null)
	{
		// Functions here are initialisation only - plugins have not been loaded (so we cannot initialise data)
		$this->counter = array();
		$this->ad_networks = array();
		$this->actions = array();
		
		// Load all Swifty plugins
		OX_Tools::load_plugins(OX_LIB . '/Plugins', $this);
		
		// Load the data access layer
		$this->dal = is_null($dalClass) ? new OX_Dal() : new $dalClass();
		
		// Sync with OpenX
		$this->sync();
	}
	
	function addAction($key, $value)
	{
		$actions = $this->actions[$key];
		if (empty($actions)) {
			$actions = array();
		}
		$actions[] = $value;
		
		$this->actions[$key] = $actions;
	}
	
	function getAction($key)
	{
		return $this->actions[$key];
	}
	
	function getSetting($key)
	{
		return $this->dal->select_setting($key);
	}
	
	function setSetting($key)
	{
		return $this->dal->update_setting($key);
	}
	
	function insertAd($ad)
	{
		return $this->dal->insert_ad($ad);
	}
	
	function deleteAd($id)
	{
		return $this->dal->delete_ad($id);
	}
	
	function getAds()
	{
		return $this->dal->select_ads();
	}
	
	function getAd($id)
	{
		return $this->dal->select_ad($id);
	}
	
	function setAd($ad)
	{
		return $this->dal->update_ad($ad);
	}
	
	function copyAd($id)
	{
		$ad = $this->dal->select_ad($id);
		if ($ad) {
			$ad = version_compare(phpversion(), '5.0') < 0 ? $ad : clone($ad); // Hack to deal with PHP 4/5 incompatiblity with cloning
			return $this->dal->insert_ad($ad);
		}
		
		return false;
	}
	
	function setAdNetwork($ad)
	{
		return $this->dal->update_ad_network($ad);
	}
	
	function importAdTag($tag)
	{
		global $advman_engine;
		
		$imported = false;
		if (!empty($tag)) {
			$networks = $this->getAction('ad_network');
			foreach ($networks as $network) {
				if (call_user_func(array($network, 'import_detect_network'), $tag)) {
					$ad = new $network;
					$ad->import_settings($tag);
					$imported = true;
					break; //leave the foreach loop
				}
			}
		}
		
		// Not a pre-defined network - we will make it HTML code...
		if (!$imported) {
			$ad=new OX_Ad_Html();
			$ad->import_settings($tag);
		}
		
		$ad = $this->insertAd($ad);
		
		return $ad;
	}
	
	function setAdActive($id, $active)
	{
		$ad = $this->dal->select_ad($id);
		if ($active != $ad->active) {
			$ad->active = $active;
			$ad->add_revision();
			$this->dal->update_ad($ad);
			return true;
		}
		
		return false;
	}
		
	function selectAd($name = null)
	{
		global $advman_engine;
		
		if (empty($name)) {
			$name = $this->getSetting('default-ad');
		}
		if (!empty($name)) {
			// Find the ads which match the name
			$ads = $advman_engine->getAds();
			$totalWeight = 0;
			$validAds = array();
			foreach ($ads as $id => $ad) {
				if ( ($ad->name == $name) && ($ad->is_available()) ) {
					$validAds[] = $ad;
					$totalWeight += $ad->get('weight');
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
			
			if (empty($this->counter['network'][$ad->network])) {
				$this->counter['network'][$ad->network] = 1;
			} else {
				$this->counter['network'][$ad->network]++;
			}
		}
	}
	/**
	 * This function synchornises with the central server.  This will be used to pass ad deals to publishers if publisher choose to accept
	 */
	function sync()
	{
		$sync = $this->dal->select_setting('openx-sync');
		if ($sync) {
			$timestamp = $this->dal->select_setting('last-sync');
//			$timestamp = 1235710700; //FOR TESTING
			$now = mktime(0,0,0);
			if (empty($timestamp) || ($now - $timestamp > 0) ) {
				$this->dal->update_setting('last-sync', $now);
				
				$params = array(
					'p' => $this->dal->select_setting('product-name'),
					'i' => $this->dal->select_setting('publisher-id'),
					'v' => $this->dal->select_setting('product-version'),
					'w' => $this->dal->select_setting('host-version'),
					'e' => $this->dal->select_setting('admin-email'),
					'u' => $this->dal->select_setting('user-login'),
					's' => $this->dal->select_setting('website-url'),
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