<?php
require_once OX_LIB . '/Dal.php';

class Advman_Dal extends OX_Dal
{
	var $data;
	
	function Advman_Dal($engine)
	{
		$this->data = $this->_load_data($engine);
	}
	
	function _load_data()
	{
		$save = false;
		$data = get_option('plugin_advman');
		if (!empty($data)) {
			if (version_compare($data['version'], ADVMAN_VERSION, '<')) {
				include_once(ADVMAN_LIB . '/Upgrade.php');
				Advman_Upgrade::upgrade_advman($data);
				$save = true;
			}
		} else {
			$data = get_option('plugin_adsensem');
			if (!empty($data)) {
				include_once(ADVMAN_LIB . '/Upgrade.php');
				Advman_Upgrade::upgrade_adsensem($data);
				$save = true;
			}
		}
		if (empty($data)) {
			$data['ads'] = array();
			$data['networks'] = array();
			$data['settings'] = array();
			$data['settings']['next_ad_id'] = 1;
			$data['settings']['default-ad'] = '';
			$data['settings']['version'] = ADVMAN_VERSION;
			$data['settings']['openx-sync'] = true;
			$data['settings']['pub-id'] = md5(uniqid('', true));
			$save = true;
		}
		
		if ($save) {
			update_option('plugin_advman', $data);
		}
		
		_map_objects($data);
		return $data;
	}
	
	function _map_arrays(&$data)
	{
		$aAds = array();
		foreach ($data['ads'] as $id => $oAd) {
			$aAds[$id][$n] = $oAd->p;
		}
		$data['ads'] = $aAds;
	}
	function _map_objects(&$data)
	{
		$oAds = array();
		foreach ($data['ads'] as $id => $aAd) {
			$class = $aAd['class'];
			$oAds[$id] = new $class($aAd);
		}
		$oNetworks = array();
		foreach ($data['networks'] as $key => $aNetwork) {
			$class = $aNetwork['class'];
			$oNetworks[$key] = new $class($aNetwork);
		}
		$data['ads'] = $oAds;
		$data['networks'] = $oNetworks;
	}
	function _update_data($data = null, $key = 'plugin_adsensem')
	{
		if (is_null($data)) {
			$data = $this->data;
		}
		
		// Move the ad classes into arrays for storage.
		$aAds = array();
		foreach ($data['ads'] as $id => $ad) {
			$aAd = $ad->p;
			$aAd['id'] = $ad->id;
			$aAd['name'] = $ad->name;
			$aAd['active'] = $ad->active;
			$aAd['network'] = $ad->network;
			$aAds[$id] = $aAd;
		}
		
		// Move the network classes into arrays for storage
		$aNws = array();
		foreach ($data['networks'] as $id => $nw) {
			$aNw = $nw->p;
			$aNw['id'] = $ad->id;
			$aNw['name'] = $ad->name;
			$aNws[$id] = $aAd;
		}
		
		$newData = array('ads' => $aAds, 'networks' => $aNws, 'settings' => $data['settings']);
	
		update_option($key, $newData);
	}
	
	function select_setting($key)
	{
		switch ($key) {
			case 'account-ids' :
			case 'default-ad' :
			case 'defaults' :
			case 'last-sync' :
			case 'notices' :
			case 'publisher-id' :
				return $this->data[$key];
			case 'admin-email':
				return get_option('admin_email');
			case 'host-version':
				global $wp_version;
				return $wp_version;
			case 'product-name':
				return 'advman';
			case 'product-version':
				return ADVMAN_VERSION;
			case 'user-login':
				global $user_login;
				if (function_exists('get_currentuserinfo')) {
					get_currentuserinfo();
					return $user_login;
				}
				return '';
			case 'website-url':
				return get_option('siteurl');
		}
		return $this->data['settings'][$key];
	}
	
	function update_setting($key, $value)
	{
		switch ($key) {
			case 'account-ids':
			case 'defualts':
			case 'last-sync':
			case 'notices' :
				$this->data[$key] = $value;
				$this->_update_data();
				return true;
			case 'product-name':
			case 'publisher-id':
			case 'product-version':
			case 'host-version':
			case 'admin-email':
			case 'user-login':
			case 'website-url':
				return false; // all of these settings are read only
		}
		$this->data['settings'][$key] = $value;
		$this->_update_data();
	}
	
	function insert_ad($ad)
	{
		$id = $this->data['next_ad_id'];
		$this->data['next_ad_id'] = $id+1;
		$ad->id = $id;
		$this->data['ads'][$id] = $ad;
		OX_Tools::sort($this->data['ads']);
		$this->_update_data();
		return $ad;
	}
	
	function delete_ad($id)
	{
		unset($this->data['ads'][$id]);
		$this->_update_data();
	}
	
	function select_ad($id)
	{
		return $this->data['ads'][$id];
	}
	
	function select_ads()
	{
		return $this->data['ads'];
	}
	
	function update_ad($ad)
	{
		$id = $ad->id;
		$this->data['ads'][$id] = $ad;
		$this->_update_data();
		return $id;
	}
	
	function update_ad_network($ad)
	{
		$this->data['defaults'][get_class($ad)] = $ad->get_network_properties();
		$this->_update_data();
	}
}
?>