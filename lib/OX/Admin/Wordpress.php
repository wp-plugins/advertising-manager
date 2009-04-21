<?php
require_once (ADVMAN_LIB . '/OX/Tools.php');

class OX_Admin_Wordpress
{
	/**
	 * Initialise menu items, notices, etc.
	 */
	function init()
	{
		global $wp_version;
		
		add_action('admin_print_scripts', array('advman_admin', 'add_scripts'));
//		add_action('admin_head', array('advman_admin','add_header_script'));
//		add_action('admin_footer', array('advman_admin','admin_callback_editor'));
		
		if (version_compare($wp_version,"2.7-alpha", '>')) {
			add_object_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-manage', array('OX_Admin_Wordpress','process'));
			add_submenu_page('advman-manage', __('Edit Ads', 'advman'), __('Edit', 'advman'), 8, 'advman-manage', array('OX_Admin_Wordpress','process'));
			add_submenu_page('advman-manage', __('Create New Ad', 'advman'), __('Create New', 'advman'), 8, 'advman-create', array('OX_Admin_Wordpress','create'));
			add_options_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-settings', array('OX_Admin_Wordpress','settings'));
		} else {
			add_menu_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-manage', array('OX_Admin_Wordpress','process'));
			add_submenu_page('advman-manage', __('Edit Ads', 'advman'), __('Edit', 'advman'), 8, 'advman-manage', array('OX_Admin_Wordpress','process'));
			add_submenu_page('advman-manage', __('Create New Ad', 'advman'), __('Create New', 'advman'), 8, 'advman-create', array('OX_Admin_Wordpress','create'));
			add_options_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-settings', array('OX_Admin_Wordpress','settings'));
		}
		add_action('admin_notices', array('OX_Admin_Wordpress','display_notices'), 1 );
		
		/* PRE-OUTPUT PROCESSING - e.g. NOTICEs (upgrade-adsense-deluxe) */
		$mode = OX_Tools::get_post_key('advman-mode');
		if ($mode == 'notice') {
			$action = OX_Tools::get_post_key('advman-action');
			$yes = OX_Tools::get_post_key('advman-notice-confirm-yes');
			switch ($action) {
				case 'upgrade adsense-deluxe':
					if ($yes) {
						require_once(ADVMAN_LIB . '/Upgrade/Wordpress.php');
						OX_Upgrade_Wordpress::adsense_deluxe_to_3_0();
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
		
		$notices = $advman_engine->getKey('notices');
		$notices[$action]['text'] = $text;
		$notices[$action]['confirm'] = $confirm;
		$advman_engine->setKey('notices', $notices);
	}
	
	function remove_notice($action)
	{
		global $advman_engine;

		$notices = $advman_engine->getKey('notices');
		if (!empty($notices[$action])) {
			unset($notices[$action]);
			$advman_engine->setKey('notices', $notices);
		}
	}
	
	/**
	 * Process input from the Admin UI.  Called staticly from the Wordpress form screen.
	 */
	function process()
	{
		global $advman_engine;
		
		$mode = OX_Tools::get_post_key('advman-mode');
		$action = OX_Tools::get_post_key('advman-action');
		$target = OX_Tools::get_post_key('advman-target');
		$targets = OX_Tools::get_post_key('advman-targets');
		
		$id = intval($target);
		$filter = null;
		
		switch ($action) {
			
			case 'activate' :
				$advman_engine->setAdActive($id, true);
				break;
			
			case 'apply' :
				$properties = OX_Admin_Wordpress::getProperties();
				if ($mode == 'edit_ad') {
					$ad = $advman_engine->saveAd($id, $properties);
					$mode = 'edit_ad';
				} elseif ($mode == 'edit_network') {
					$network = $advman_engine->saveAdNetwork($target, $properties);
					$mode = 'edit_network';
				}
				break;
			
			case 'cancel' :
				$ads = $advman_engine->getAds();
				$mode = !empty($ads) ? 'list_ads' : 'create_ad';
				break;
			
			case 'clear' :
				break;
			
			case 'copy' :
				if (!empty($id)) {
					$ad = $advman_engine->copyAd($id);
				} elseif (!empty($targets)) {
					foreach ($targets as $target) {
						$id = intval($target);
						$advman_engine->copyAd($id);
					}
				}
				break;
			
			case 'deactivate' :
				$advman_engine->setAdActive($id, false);
				break;
			
			case 'default' :
				advman_admin::_set_default($target);
				break;
			
			case 'delete' :
				if (!empty($id)) {
					advman_admin::_delete_ad($id);
				} elseif (!empty($targets)) {
					foreach ($targets as $target) {
						advman_admin::_delete_ad($target);
					}
				}
				$mode = !empty($_advman['ads']) ? 'list_ads' : 'create_ad';
				break;
			
			case 'edit' :
				$mode = !empty($id) ? 'edit_ad' : 'edit_network';
				break;
			
			case 'filter' :
				$filter_active = OX_Tools::get_post_key('advman-filter-active');
				$filter_network = OX_Tools::get_post_key('advman-filter-network');
				if (!empty($filter_active)) {
					$filter['active'] = $filter_active;
				}
				if (!empty($filter_network)) {
					$filter['network'] = $filter_network;
				}
				break;
			
			case 'import' :
				$tag = OX_Tools::get_post_field('advman-code');
				$tag = stripslashes($tag);
				$id = $advman_engine->importAdTag($tag);
				$mode = 'edit_ad';
				break;
			
			case 'list' :
				$mode = 'list_ads';
				break;
			
			case 'save' :
				if ($mode == 'settings') {
					advman_admin::_save_settings();
				} else {
					if (is_numeric($target)) {
						advman_admin::_save_ad($target);
					} else {
						advman_admin::_save_network($target);
					}
					$mode = 'list_ads';
				}
				break;
			
			case 'settings' :
				$mode = 'settings';
				break;
			
			default :
				$ads = $advman_engine->getAds();
				$mode = !empty($ads) ? 'list_ads' : 'create_ad';
				break;
		}
		
		switch ($mode) {
			case 'list_ads' :
				$template = OX_Tools::get_template('ListAds');
				$template->display();
				break;
			
			case 'create_ad' :
				OX_Admin_Wordpress::create();
				break;
			
			case 'edit_ad' :
				$ad = $_advman['ads'][$target];
				$template = OX_Tools::get_template('EditAd', $ad);
				$template->display($target);
				break;
			
			case 'edit_network' :
				$ad = new $target;
				$template = OX_Tools::get_template('EditNetwork', $ad);
				$template->display($target);
				break;
			
			case 'settings' :
				$template = OX_Tools::get_template('Settings');
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
		
		$notices = $advman_engine->getKey('notices');
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
		$template = OX_Tools::get_template('CreateAd');
		$template->display();
	}
	
	/**
	 * This function is called from the Wordpress Settings menu
	 */
	function settings()
	{
		
		// Get our options and see if we're handling a form submission.
		$action = OX_Tools::get_post_key('advman-action');
		if ($action == 'save') {
			global $advman_engine;
			$advman_engine->saveSettings($settings);
//			advman_admin::_save_settings();
		}
		$template = OX_Tools::get_template('Settings');
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