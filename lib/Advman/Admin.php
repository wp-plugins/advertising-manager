<?php
require_once (ADVMAN_LIB . '/Tools.php');

class Advman_Admin
{
	/**
	 * Initialise menu items, notices, etc.
	 */
	function init()
	{
		global $wp_version;
		
		add_action('admin_print_scripts', array('Advman_Admin', 'add_scripts'));
		
		if (version_compare($wp_version,"2.7-alpha", '>')) {
			add_object_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-manage', array('Advman_Admin','process'));
			add_submenu_page('advman-manage', __('Edit Ads', 'advman'), __('Edit', 'advman'), 8, 'advman-manage', array('Advman_Admin','process'));
			add_submenu_page('advman-manage', __('Create New Ad', 'advman'), __('Create New', 'advman'), 8, 'advman-create', array('Advman_Admin','create'));
			add_options_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-settings', array('Advman_Admin','settings'));
		} else {
			add_menu_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-manage', array('Advman_Admin','process'));
			add_submenu_page('advman-manage', __('Edit Ads', 'advman'), __('Edit', 'advman'), 8, 'advman-manage', array('Advman_Admin','process'));
			add_submenu_page('advman-manage', __('Create New Ad', 'advman'), __('Create New', 'advman'), 8, 'advman-create', array('Advman_Admin','create'));
			add_options_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-settings', array('Advman_Admin','settings'));
		}
		add_action('admin_notices', array('Advman_Admin','display_notices'), 1 );
		
		/* PRE-OUTPUT PROCESSING - e.g. NOTICEs (upgrade-adsense-deluxe) */
		$mode = OX_Tools::sanitize($_POST['advman-mode'], 'key');
		if ($mode == 'notice') {
			$action = OX_Tools::sanitize($_POST['advman-action'], 'key');
			$yes = OX_Tools::sanitize($_POST['advman-notice-confirm-yes'], 'key');
			switch ($action) {
				case 'upgrade adsense-deluxe':
					if ($yes) {
						require_once(ADVMAN_LIB . '/Upgrade/Wordpress.php');
						Advman_Upgrade::adsense_deluxe_to_3_0();
					}
					$admin->remove_notice('upgrade adsense-deluxe');
					break;	
				case 'optimise':
					advman_admin::_set_auto_optimise(!empty($yes));
					$admin->remove_notice('optimise');
					break;
				case 'activate advertising-manager':
					$admin->remove_notice('activate advertising-manager');
					break;
			}
		}
	}
	
	function add_notice($action,$text,$confirm=false)
	{
		global $advman_engine;
		
		$notices = $advman_engine->getSetting('notices');
		$notices[$action]['text'] = $text;
		$notices[$action]['confirm'] = $confirm;
		$advman_engine->setSetting('notices', $notices);
	}
	
	function remove_notice($action)
	{
		global $advman_engine;

		$notices = $advman_engine->getSetting('notices');
		if (!empty($notices[$action])) {
			unset($notices[$action]);
			$advman_engine->setSetting('notices', $notices);
		}
	}
	
	function save_properties(&$ad, $default = false)
	{
		global $advman_engine;
		
		// Set the ad properties (if not setting default properties)
		if (!$default) {
			if (isset($_POST['advman-name'])) {
				$ad->name = OX_Tools::sanitize($_POST['advman-name'], 'key');
			}
			
			if (isset($_POST['advman-active'])) {
				$ad->active = ($_POST['advman-active'] == 'yes');
			}
		}
		
		$properties = $ad->get_network_property_defaults();
		if (!empty($properties)) {
			foreach ($properties as $property => $d) {
				if (isset($_POST["advman-{$property}"])) {
					$value = OX_Tools::sanitize($_POST["advman-{$property}"]);
					if ($default) {
						$ad->set_network_property($property, $value);
					} else {
						$ad->set_property($property, $value, $default);
					}
				}
			}
		}
		
		$ad->add_revision($default);
	}
	
	
	/**
	 * Process input from the Admin UI.  Called staticly from the Wordpress form screen.
	 */
	function process()
	{
		global $advman_engine;
		
		$filter = null;
		$mode = OX_Tools::sanitize($_POST['advman-mode'], 'key');
		$action = OX_Tools::sanitize($_POST['advman-action'], 'key');
		$target = OX_Tools::sanitize($_POST['advman-target'], 'key');
		$targets = OX_Tools::sanitize($_POST['advman-targets'], 'key');
		
		// For operations on a single ad
		if (is_numeric($target)) {
			$id = intval($target);
			$ad = $advman_engine->getAd($id);
		}
		
		// For operations on multiple ads
		if (is_array($targets)) {
			$ids = array();
			$ads = array();
			foreach ($targets as $target) {
				$i = intval($target);
				$ids[] = $i;
				$ads[] = $advman_engine->getAd($i);
			}
		}
		
		switch ($action) {
			
			case 'activate' :
				if (!$ad->active) {
					$ad->active = true;
					$advman_engine->setAd($ad);
				}
				break;
			
			case 'clear' :
				break;
			
			case 'copy' :
				if (!empty($ad)) {
					$ad = $advman_engine->copyAd($ad->id);
				}
				if (!empty($ads)) {
					foreach ($ads as $ad) {
						$advman_engine->copyAd($ad->id);
					}
				}
				break;
			
			case 'deactivate' :
				if ($ad->active) {
					$ad->active = false;
					$advman_engine->setAd($ad);
				}
				break;
			
			case 'default' :
				$advman_engine->setKey('default-ad', $ad->name);
				break;
			
			case 'delete' :
				if (!empty($ad)) {
					$ad = $advman_engine->deleteAd($ad->id);
				}
				if (!empty($ads)) {
					foreach ($ads as $ad) {
						$advman_engine->deleteAd($ad->id);
					}
				}
				$mode = !empty($_advman['ads']) ? 'list_ads' : 'create_ad';
				break;
			
			case 'edit' :
				$mode = !empty($id) ? 'edit_ad' : 'edit_network';
				break;
			
			case 'filter' :
				$filter_active = OX_Tools::sanitize($_POST['advman-filter-active'], 'key');
				$filter_network = OX_Tools::sanitize($_POST['advman-filter-network'], 'key');
				if (!empty($filter_active)) {
					$filter['active'] = $filter_active;
				}
				if (!empty($filter_network)) {
					$filter['network'] = $filter_network;
				}
				break;
			
			case 'import' :
				$tag = OX_Tools::sanitize($_POST['advman-code']);
				$ad = $advman_engine->importAdTag($tag);
				$mode = 'edit_ad';
				break;
			
			case 'list' :
				$mode = 'list_ads';
				break;
			
			case 'apply' :
			case 'save' :
				if ($mode == 'edit_ad') {
					Advman_Admin::save_properties($ad);
					$advman_engine->setAd($ad);
				} elseif ($mode == 'edit_network') {
					$ad = new $target;
					Advman_Admin::save_properties($ad, true);
					$advman_engine->setAd($ad);
				} elseif ($mode == 'settings') {
					Advman_Admin::save_settings();
				}
			
				if ($action == 'save' && $mode != 'settings') {
					$mode = 'list_ads';
				}
				break;
			
			case 'settings' :
				$mode = 'settings';
				break;
			
			case 'cancel' :
			default :
				$ads = $advman_engine->getAds();
				$mode = !empty($ads) ? 'list_ads' : 'create_ad';
				break;
		}
		
		switch ($mode) {
			case 'list_ads' :
				$template = Advman_Tools::get_template('List');
				$template->display();
				break;
			
			case 'create_ad' :
				Advman_Admin::create();
				break;
			
			case 'edit_ad' :
				$template = Advman_Tools::get_template('Edit_Ad', $ad);
				$template->display($ad);
				break;
			
			case 'edit_network' :
				$template = Advman_Tools::get_template('Edit_Network', $ad);
				$template->display($target);
				break;
			
			case 'settings' :
				$template = Advman_Tools::get_template('Settings');
				$template->display();
				break;
		}
	}
	
	/**
	 * Display notices in the Admin UI.  Called staticly from the Wordpress 'admin_notices' hook.
	 */
	function display_notices()
	{
		global $advman_engine;
		
		$notices = $advman_engine->getSetting('notices');
		if (!empty($notices)) {
			$template = OX_Tools::get_template('Notice');
			$template->display($notices);
		}
		
	}
	
	/**
	 * This function is called from the Wordpress Ads menu
	 */
	function create()
	{
		$template = Advman_Tools::get_template('Create');
		$template->display();
	}
	
	/**
	 * This function is called from the Wordpress Settings menu
	 */
	function settings()
	{
		
		// Get our options and see if we're handling a form submission.
		$action = OX_Tools::sanitize($_POST['advman-action'], 'key');
		if ($action == 'save') {
			global $advman_engine;
			$advman_engine->saveSettings($settings);
//			advman_admin::_save_settings();
		}
		$template = Advman_Tools::get_template('Settings');
		$template->display();
	}

	function add_scripts()
	{
		wp_enqueue_script('prototype'); //do we need this?
		wp_enqueue_script('postbox'); //do we need this?
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-swifty', ADVMAN_URL . '/scripts/jQuery.swifty.js', array('jquery'));
		wp_enqueue_script('advman', ADVMAN_URL . '/scripts/advertising-manager.js');
		$page = !empty($_GET['page']) ? $_GET['page'] : '';
		if ($page == 'advman-manage') {
			echo "<link type='text/css' rel='stylesheet' href='" . ADVMAN_URL . "/advertising-manager.css' />";
		}
	}
}
?>