<?php
require_once ADVMAN_LIB . '/Tools.php';
require_once OX_LIB . '/Dal.php';

class Advman_Dal extends OX_Dal
{
	var $data;
	
	function Advman_Dal()
	{
		$this->data = $this->_load_data();
	}
	
	function _load_data()
	{
		$save = false;
		$data = get_option('plugin_advman');
		if (!empty($data)) {
			if (version_compare($data['settings']['version'], ADVMAN_VERSION, '<')) {
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
			$data['zones'] = array();
			$data['settings'] = array();
			$data['settings']['next_ad_id'] = 1;
			$data['settings']['next_zone_id'] = 1;
			$data['settings']['default-ad'] = '';
			$data['settings']['version'] = ADVMAN_VERSION;
			$data['settings']['openx-sync'] = true;
			$data['settings']['publisher-id'] = md5(uniqid('', true));
			$save = true;
		}
		
		if ($save) {
			update_option('plugin_advman', $data);
		}
		
		$this->_map_objects($data);
		
		return $data;
	}
	
	function _map_arrays(&$data)
	{
		foreach (array('ads','networks','zones') as $name) {
			$aEntities = array();
			foreach ($data[$name] as $id => $oEntity) {
				$aEntities[$id] = $oEntity->to_array();
			}
			$data[$name] = $aEntities;
		}
	}
	function _map_objects(&$data)
	{
		foreach (array('ads','networks','zones') as $name) {
			$oEntities = array();
			foreach ($data[$name] as $id => $aEntity) {
				switch ($name) {
					case 'ads' : $oEntity = OX_Ad::to_object($aEntity); break;
					case 'networks' : $oEntity = OX_Network::to_object($aEntity); break;
					case 'zones' : $oEntity = OX_Zone::to_object($aEntity); break;
				}
				$oEntities[$id] = $oEntity;
			}
			$data[$name] = $oEntities;
		}
	}
	
	function _update_data($data = null, $key = 'plugin_advman')
	{
		if (is_null($data)) {
			$data = $this->data;
		}
		
		$this->_map_arrays($data);
	
		update_option($key, $data);
	}
	
	function select_setting($key)
	{
		switch ($key) {
			case 'admin-email':
				return get_option('admin_email');
			case 'host-version':
				global $wp_version;
				return $wp_version;
			case 'product-name':
				global $wpmu_version;
				return !empty($wpmu_version) ? 'Wordpress MU' : 'Wordpress'; 
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
			case 'product-name':
			case 'host-version':
			case 'admin-email':
			case 'user-login':
			case 'website-url':
				return false; // all of these settings are read only
		}
		$this->data['settings'][$key] = $value;
		$this->_update_data();
		return true;
	}
	
	function insert_ad($ad)
	{
		$id = $this->data['settings']['next_ad_id'];
		$this->data['settings']['next_ad_id'] = $id+1;
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
	
	function insert_network($network)
	{
		$this->data['networks'][strtolower(get_class($network))] = $network->to_array();
		$this->_update_data();
		return $ad;
	}
	
	function delete_network($id)
	{
		unset($this->data['networks'][$id]);
		$this->_update_data();
	}
	
	function select_network($id)
	{
		return $this->data['networks'][$id];
	}
	
	function select_networks()
	{
		return $this->data['networks'];
	}
	
	function update_network($network)
	{
		$this->data['networks'][strtolower(get_class($network))] = $network->to_array();
		$this->_update_data();
	}
	
	function insert_zone($zone)
	{
		$id = empty($this->data['settings']['next_zone_id']) ? 1 : $this->data['settings']['next_zone_id'];
		$this->data['settings']['next_zone_id'] = $id + 1;
		$zone->id = $id;
		$this->data['zones'][$id] = $zone;
		OX_Tools::sort($this->data['zones']);
		$this->_update_data();
		return $zone;
	}
	
	function delete_zone($id)
	{
		unset($this->data['zones'][$id]);
		$this->_update_data();
	}
	
	function select_zone($id)
	{
		return $this->data['zones'][$id];
	}
	
	function select_zones()
	{
		return $this->data['zones'];
	}
	
	function update_zone($zone)
	{
		$id = $zone->id;
		$this->data['zones'][$id] = $zone;
		$this->_update_data();
		return $id;
	}
}
?>