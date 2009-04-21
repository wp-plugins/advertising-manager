<?php

@define('OX_SWIFTY_PATH', dirname(__FILE__) . '/Swifty');
require_once(OX_SWIFTY_PATH . '/Adnet.php');

class OX_Swifty
{
	var $dal;
	var $ad_networks;
	var $counter;
	var $actions;
	
	function OX_Swifty($dal)
	{
		$this->dal = $dal;
		$this->counter = array();
		$this->ad_networks = array();
		$this->actions = array();
		
		// Sync with openx
		$this->sync();
	}
	
	function addAction($key, $data)
	{
		$action = $this->actions[$key];
		if (empty($action)) {
			$action = array();
		}
		$action[] = $data;
		
		$this->actions[$key] = $action;
	}
	function getAction($action)
	{
		return $this->actions[$action];
	}
	function getKey($key)
	{
		return $this->dal->getKey($key);
	}
	function setKey($key, $value)
	{
		return $this->dal->setKey($key, $value);
	}
	function getVersion()
	{
		return $this->dal->getVersion();
	}
	function addAd($ad)
	{
		return $this->dal->addAd($ad);
	}
	function saveAd($id, $properties)
	{
		$ad = $this->dal->getAd($id);
		
		if ($ad) {
			$ad->saveSettings($properties);
			return $this->dal->setAd($id, $ad);
		}
		
		return false;
	}
	
	function saveAdNetwork($id, $network)
	{
		$network = $this->dal->getAdNetwork($id);
		
		if ($network) {
			$network->saveSettings($properties);
			return $this->dal->setAdNetwork($id, $network);
		}
		
		return false;
	}
	function copyAd($id)
	{
		$ad = $this->dal->getAd($id);
		
		if ($ad) {
			return $this->dal->insertAd($ad);
		}
		
		return false;
	}
	
	function getAds()
	{
		return $this->dal->getAds();
	}
	
	function deleteAd($id)
	{
		return $this->dal->deleteAd($id);
	}
	
	function importAdTag($tag)
	{
		global $advman_engine;
		
		$imported = false;
		if (!empty($tag)) {
			$networks = $this->getAction('register_ad_network');
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
			$ad=new OX_Adnet_Html();
			$ad->import_settings($tag);
		}
		
		$id = $this->addAd($ad);
		
		return $id;
	}
	
	function setAdActive($id, $active)
	{
		global $_advman;
		
		//Set selected advert as active
		$id = advman_admin::validate_id($id);
		
		if ($_advman['ads'][$id]->active != $active) {
			$_advman['ads'][$id]->active = $active;
			$_advman['ads'][$id]->add_revision();
			update_option('plugin_adsensem', $_advman);
		}
	}
		
	function setZoneDefault($zoneId, $adId)
	{
		global $_advman;
		
		//Set selected advert as active
		$id = advman_admin::validate_id($id);
		
		$name = $_advman['ads'][$id]->name;
		if ($name != $_advman['default-ad']) {
			$_advman['default-ad'] = $name;
			update_option('plugin_adsensem', $_advman);
		}
	}
	
	// turn on/off optimisation across openx for all ads
	function _set_auto_optimise($active)
	{
		global $_advman;
		
		$market = ($active) ? 'yes' : 'no';
		foreach ($_advman['ads'] as $id => $ad) {
			$_advman['ads'][$id]->set('openx-market', $market);
		}
		foreach ($_advman['defaults'] as $network => $settings) {
			$_advman['defaults'][$network]['openx-market'] = $market;
		}
		update_option('plugin_adsensem', $_advman);
	}
		
	function selectAd($name)
	{
		global $_advman;
		
		// Find the ads which match the name
		$ads = array();
		$totalWeight = 0;
		foreach ($_advman['ads'] as $id => $ad) {
			if ( ($ad->name == $name) && ($ad->is_available()) ) {
				$ads[] = $ad;
				$totalWeight += $ad->get('weight', true);
			}
		}
		// Pick the ad
		// Generate a number between 0 and 1
		$rnd = (mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX);
		// Loop through ads until the selected one is chosen
		$wt = 0;
		foreach ($ads as $ad) {
			$wt += $ad->get('weight', true);
			if ( ($wt / $totalWeight) > $rnd) {
				// Display the ad
				return $ad;
			}
		}
	}
	function update_counters($ad)
	{
		global $_advman_counter;
		
		if (!empty($ad)) {
			if (empty($_advman_counter['id'][$ad->id])) {
				$_advman_counter['id'][$ad->id] = 1;
			} else {
				$_advman_counter['id'][$ad->id]++;
			}
			
			if (empty($_advman_counter['network'][$ad->network])) {
				$_advman_counter['network'][$ad->network] = 1;
			} else {
				$_advman_counter['network'][$ad->network]++;
			}
		}
	}
	/**
	 * This function synchornises with the central server.  This will be used to pass ad deals to publishers if publisher choose to accept
	 */
	function sync()
	{
		$sync = $this->dal->getSetting('openx-sync');
		if ($sync) {
			$timestamp = $this->dal->getSetting('last-sync');
//			$timestamp = 1235710700; //FOR TESTING
			$now = mktime(0,0,0);
			if (empty($timestamp) || ($now - $timestamp > 0) ) {
				$this->dal->setSetting('last-sync', $now);
				
				$params = array(
					'p' => $this->dal->getSetting('product-name'),
					'i' => $this->dal->getSetting('publisher-id'),
					'v' => $this->dal->getSetting('product-version'),
					'w' => $this->dal->getSetting('host-version'),
					'e' => $this->dal->getSetting('admin-email'),
					'u' => $this->dal->getSetting('user-login'),
					's' => $this->dal->getSetting('website-url'),
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