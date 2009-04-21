<?php
require_once ADVMAN_LIB . '/OX/Swifty/Dal.php';

class OX_Dal_Wordpress extends OX_Swifty_Dal
{
	var $data;
	
	function OX_Dal_Wordpress()
	{
		$this->data = $this->loadData();
		$this->_verifyData();
	}
	
	function loadData($key = 'plugin_adsensem')
	{
		return get_option($key);
	}
	
	function saveData($data = null, $key = 'plugin_adsensem')
	{
		if (is_null($data)) {
			$data = $this->data;
		}
		update_option($key, $data);
	}
	
	function getKey($key)
	{
		return $this->data[$key];
	}
	function setKey($key, $value)
	{
		$this->data[$key] = $value;
	}
	function getAds()
	{
		return $this->getKey('ads');
	}
	
	function getSetting($name)
	{
		$settings = $this->getKey('settings');
		
		if (isset($settings[$name])) {
			return $settings[$name];
		}
		
		switch ($name) {
			case 'last-sync': return $this->getKey('last-sync');
			case 'product-name': return 'advman';
			case 'publisher-id': return $this->getKey('uuid');
			case 'product-version': return ADVMAN_VERSION;
			case 'host-version': global $wp_version; return $wp_version;
			case 'admin-email': return get_option('admin_email');
			case 'user-login':
				global $user_login;
				if (function_exists('get_currentuserinfo')) {
					get_currentuserinfo();
					return $user_login;
				}
				return '';
			case 'website-url': return get_option('siteurl');
		}
		
		return false;
	}
	
	function setSetting($name, $value)
	{
		switch ($name) {
			case 'last-sync':
				$this->setKey('last-sync', $value);
				break;
			case 'product-name':
			case 'publisher-id':
			case 'product-version':
			case 'host-version':
			case 'admin-email':
			case 'user-login':
			case 'website-url':
				return false; // all of these settings are read only
			default:
				$this->data['settings'][$name] = $value;
				break;
		}
		
		$this->saveData();
	}

	function _verifyData()
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
			
			$this->saveData($data);
		}
		
		if (version_compare($data['version'], ADVMAN_VERSION, '<')) {
			include_once('WordpressUpgrade.php');
			
			//Backup cycle
			$backup = $this->loadData('plugin_adsensem_backup');
			$version = OX_Tools::major_version($data['version']);
			$backup[$version] = $data;
			$this->saveData($backup, 'plugin_adsensem_backup');
			
			$upgrade = new OX_Dal_WordpressUpgrade();
			$data = $upgrade->upgrade($data);
			$this->saveData($data);
		}
	}
}
?>