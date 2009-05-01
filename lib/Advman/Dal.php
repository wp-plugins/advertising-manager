<?php
require_once OX_LIB . '/Dal.php';

class Advman_Dal extends OX_Dal
{
	var $data;
	
	function Advman_Dal()
	{
		$this->data = $this->_select_data();
		$this->_verify_data();
	}
	function _select_data($key = 'plugin_adsensem')
	{
		return get_option($key);
	}
	
	function _update_data($data = null, $key = 'plugin_adsensem')
	{
		if (is_null($data)) {
			$data = $this->data;
		}
		update_option($key, $data);
	}
	
	function _verify_data()
	{
		$data = $this->data;
		
		// If there is no data (or no readable data), create an initial array
		if (empty($data['version']) || !is_array($data['ads'])) {
			// Build an initial array
			$data = array();
			$data['ads'] = array();
			$data['next_ad_id'] = 1;
			$data['default-ad'] = '';
			$data['version'] = ADVMAN_VERSION;
			$data['settings']['openx-sync'] = true;
			$data['uuid'] = $viewerId = md5(uniqid('', true));
			
			// If there is no Advertising Manager data, check to see if we can import from Adsense Deluxe
			$deluxe = get_option('acmetech_adsensedeluxe');
			if (is_array($deluxe)) {
				advman::add_notice('upgrade adsense-deluxe',__('<strong>Advertising Manager</strong> has detected a previous installation of <strong>Adsense Deluxe</strong>. Import settings?'),'yn');
			}
			
			$this->_update_data($data);
		}
		
		if (version_compare($data['version'], ADVMAN_VERSION, '<')) {
			include_once('WordpressUpgrade.php');
			
			//Backup cycle
			$backup = $this->_getData('plugin_adsensem_backup');
			$backup[$data['version']] = $data;
			$this->_setData($backup, 'plugin_adsensem_backup');
			
			$upgrade = new Advman_Upgrade();
			$data = $upgrade->upgrade($data);
			$this->_setData($data);
		}
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
}
?>