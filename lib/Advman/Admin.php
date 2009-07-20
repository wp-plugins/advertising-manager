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
		
		if (version_compare($wp_version,"2.7-alpha", '>')) {
			add_object_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-manage-ads', array('Advman_Admin','process'));
			add_submenu_page('advman-manage-ads', __('Manage Ads', 'advman'), __('Manage Ads', 'advman'), 8, 'advman-manage-ads', array('Advman_Admin','process'));
			add_submenu_page('advman-manage-ads', __('Manage Ad Zones', 'advman'), __('Manage Zones', 'advman'), 8, 'advman-manage-zones', array('Advman_Admin','process'));
			add_submenu_page('advman-manage-ads', __('Ad Statistics', 'advman'), __('Statistics', 'advman'), 8, 'advman-statistics', array('Advman_Admin','process'));
			add_options_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-settings', array('Advman_Admin','settings'));
		} else {
			add_menu_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-manage', array('Advman_Admin','process'));
			add_submenu_page('advman-manage', __('Manage Ads', 'advman'), __('Manage Ads', 'advman'), 8, 'advman-manage', array('Advman_Admin','process'));
			add_submenu_page('advman-manage', __('Manage Zones', 'advman'), __('Manage Zones', 'advman'), 8, 'advman-manage', array('Advman_Admin','process'));
			add_options_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-settings', array('Advman_Admin','settings'));
		}
		
		// Process any notices that have been taken down
		Advman_Admin::process_notices();
		
		add_action('admin_print_scripts', array('Advman_Admin', 'add_scripts'));
		add_action('admin_notices', array('Advman_Admin','display_notices'), 1 );
		add_action('admin_footer', array('Advman_Admin','display_editor'));
	}
	
	function add_scripts()
	{
		global $plugin_page;
		$mode = OX_Tools::sanitize($_REQUEST['advman-mode'], 'key');
		
		// All pages should have the advman ad counting scripts
//		wp_enqueue_script('advman-delivery', ADVMAN_URL . '/scripts/advman.delivery.js', array('jquery'));

		// Now go through the admin pages
		if (is_admin()) {
			switch ( $plugin_page . '-' . $mode) {
				case 'advman-manage-ads-edit' :
				case 'advman-manage-zones-edit' :
					wp_enqueue_script('postbox');
					wp_enqueue_script('jquery-multiselect', ADVMAN_URL . '/scripts/jquery.multiSelect.js', array('jquery'));
					wp_enqueue_script('advman-link', ADVMAN_URL . '/scripts/link.dev.js', array('jquery','wp-lists','wp-ajax-response'));
					wp_enqueue_script('advman-admin', ADVMAN_URL . '/scripts/advman.admin.js');
					echo "
<link type='text/css' rel='stylesheet' href='" . ADVMAN_URL . "/scripts/advman.admin.css' />
<link type='text/css' rel='stylesheet' href='" . ADVMAN_URL . "/scripts/jquery.multiSelect.css' />";
					break;
				case 'advman-manage-ads-list' :
				case 'advman-manage-zones-list' :
				default :
					wp_enqueue_script('advman', ADVMAN_URL . '/scripts/advman.admin.js');
					echo "
<link type='text/css' rel='stylesheet' href='" . ADVMAN_URL . "/scripts/advman.admin.css' />";
			}
		}
	}
	function get_notices()
	{
		return get_option('plugin_advman_ui_notices');
	}
	function set_notices($notices)
	{
		return update_option('plugin_advman_ui_notices', $notices);
	}
	function add_notice($action,$text,$confirm=false)
	{
		$notices = Advman_Admin::get_notices();
		$notices[$action]['text'] = $text;
		$notices[$action]['confirm'] = $confirm;
		Advman_Admin::set_notices($notices);
	}
	function remove_notice($action)
	{
		$notices = Advman_Admin::get_notices();
		if (!empty($notices[$action])) {
			unset($notices[$action]);
		}
		Advman_Admin::set_notices($notices);
	}
	
	/**
	 * Process input from the Admin UI.  Called staticly from the Wordpress form screen.
	 */
	function process()
	{
		global $advman_engine;
		global $plugin_page;
		
		$mode = OX_Tools::sanitize($_REQUEST['advman-mode'], 'key');
		$action = OX_Tools::sanitize($_REQUEST['advman-action'], 'key');
		
		switch ($plugin_page . '-' . $mode) {
			case 'advman-manage-zones-create' :
				Advman_Admin::process_create_zone($action);
				break;
			case 'advman-manage-zones-edit' :
				Advman_Admin::process_edit_zone($action);
				break;
			case 'advman-manage-zones-list' :
			case 'advman-manage-zones-' :
				Advman_Admin::process_manage_zones($action);
				break;
			case 'advman-statistics' :
				Advman_Admin::process_statistics($action);
				break;
			case 'advman-manage-ads-create' :
				Advman_Admin::process_create_ad($action);
				break;
			case 'advman-manage-ads-edit' :
				Advman_Admin::process_edit_ad($action);
				break;
			case 'advman-manage-ads-edit_network' :
				Advman_Admin::process_edit_network($action);
				break;
			case 'advman-manage-ads-list' :
			case 'advman-manage-ads-' :
			default :
				Advman_Admin::process_manage_ads($action);
				break;
		}
	}
	
	/**
	 * Process input from the 'Manage Ads' page
	 */
	function process_manage_ads($action)
	{
		global $advman_engine;
		
		// Returns either an ID or list of IDs 
		$ids = Advman_Tools::get_ids_from_form();
		$ads = $advman_engine->get_ads($ids);
		
		switch ($action) {
			case 'activate' :
				$advman_engine->set_ads_active($ads, true);
				Advman_Admin::display_page('List_Ad');
				break;
			case 'copy' :
				$advman_engine->copy_ads($ads);
				Advman_Admin::display_page('List_Ad');
				break;
			case 'create' :
				Advman_Admin::display_page('Create_Ad');
				break;
			case 'deactivate' :
				$advman_engine->set_ads_active($ads, false);
				Advman_Admin::display_page('List_Ad');
				break;
			case 'default' :
				$ad = $ads[0]; // can only set one ad as default
				$default = ($advman_engine->get_setting('default-ad') != $ad->name ? $ad->name : '');
				$advman_engine->set_setting('default-ad', $default);
				Advman_Admin::display_page('List_Ad');
				break;
			case 'delete' :
				$advman_engine->remove_ads($ads);
				$page = $advman_engine->get_ad_count() ? 'List_Ad' : 'Create_Ad';
				Advman_Admin::display_page($page);
				break;
			case 'edit' :
				$ad = $ads[0]; // can only edit one ad at a time
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
//			case 'filter' :
//				$filter = Advman_Admin::get_filter_from_form();
//				Advman_Admin::display_page('List_Ad', $filter);
//				break;
			case 'cancel' :
			default :
				Advman_Admin::display_page('List_Ad');
		}
	}
	
	/**
	 * Process input from the 'Create Ad' page
	 */
	function process_create_ad($action)
	{
		global $advman_engine;
		
		switch ($action) {
			case 'import' :
				$tag = Advman_Admin::get_tag_from_form();
				$ad = $advman_engine->import_ad_tag($tag);
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'cancel' :
			default :
				Advman_Admin::display_page('List_Ad');
		}
	}

	/**
	 * Process input from the 'Edit Ad' page
	 */
	function process_edit_ad($action)
	{
		global $advman_engine;
		
		$id = Advman_Admin::get_id_from_form();
		$ad = $advman_engine->get_ad($id);
		
		switch ($action) {
			case 'activate' :
				$advman_engine->set_ad_active($ad, true);
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'copy' :
				$ad = $advman_engine->copy_ad($ad);
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'deactivate' :
				$advman_engine->set_ad_active($ad, false);
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'default' :
				$default = ($advman_engine->get_setting('default-ad') != $ad->name ? $ad->name : '');
				$advman_engine->set_setting('default-ad', $default);
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'delete' :
				$advman_engine->remove_ad($ad);
				if ($advman_engine->get_ad_count()) {
					Advman_Admin::display_page('List_Ad');
				} else {
					Advman_Admin::display_page('Create_Ad');
				}
				break;
			case 'edit_network' :
				Advman_Admin::display_page('Edit_Network', $ad->get_network());
				break;
			case 'preview' :
				Advman_Admin::display_page('Preview_Ad', $ad);
				break;
			case 'reset' :
				$ad->reset_properties();
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'apply' :
				if (Advman_Admin::save_properties($ad)) {
					$advman_engine->set_ad($ad);
				}
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'save' :
				if (Advman_Admin::save_properties($ad)) {
					$advman_engine->set_ad($ad);
				}
				Advman_Admin::display_page('List_Ad');
				break;
			case 'cancel' :
			default :
				Advman_Admin::display_page('List_Ad');
		}
	}
		
	/**
	 * Process input from the 'Edit Ad Network' page
	 */
	function process_edit_network($action)
	{
		global $advman_engine;
		
		$id = Advman_Admin::get_id_from_form();
		$network = $advman_engine->get_network($id);
		
		switch ($action) {
			case 'reset' :
				$network->reset_properties();
				Advman_Admin::display_page('Edit_Network', $network);
				break;
			case 'apply' :
				if (Advman_Admin::save_properties($network)) {
					$advman_engine->set_network($network);
				}
				Advman_Admin::display_page('Edit_Network', $network);
				break;
			case 'save' :
				if (Advman_Admin::save_properties($network)) {
					$advman_engine->set_network($network);
				}
				Advman_Admin::display_page('List_Ad');
				break;
			case 'cancel' :
			default :
				Advman_Admin::display_page('List_Ad');
		}
	}
	
	/**
	 * Process input from the 'Manage Zones' page
	 */
	function process_manage_zones($action)
	{
		global $advman_engine;
		
		$ids = Advman_Admin::get_ids_from_form();
		$zones = $advman_engine->get_zones($ids);
		
		switch ($action) {
			case 'activate' :
				$advman_engine->set_zone_active($zone, true);
				Advman_Admin::display_page('List_Zone');
				break;
			case 'copy' :
				$advman_engine->copy_zones($zones);
				Advman_Admin::display_page('List_Zone');
				break;
			case 'create' :
				Advman_Admin::display_page('Create_Zone');
				break;
			case 'deactivate' :
				$advman_engine->set_zone_active($zone, false);
				Advman_Admin::display_page('List_Zone');
				break;
			case 'delete' :
				$advman_engine->remove_zones($zones);
				if ($advman_engine->get_zone_count()) {
					Advman_Admin::display_page('List_Zone');
				} else {
					Advman_Admin::display_page('Create_Zone');
				}
				break;
			case 'edit' :
				Advman_Admin::display_page('Edit_Zone', $zone);
				break;
			case 'filter' :
				$filter = Advman_Admin::get_filter_from_form();
				Advman_Admin::display_page('List_Zone', $filter);
				break;
			case 'cancel' :
			default :
				Advman_Admin::display_page('List_Zone');
		}
	}
	
	/**
	 * Process input from the 'Create Zone' page
	 */
	function process_create_zone($action)
	{
		global $advman_engine;
		
		$id = Advman_Admin::get_id_from_form();
		$zone = $advman_engine->get_zone($id);
		
		switch ($action) {
			case 'reset' :
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'apply' :
				if (Advman_Admin::save_properties($zone)) {
					$advman_engine->add_zone($zone);
				}
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'save' :
				if (Advman_Admin::save_properties($zone)) {
					$advman_engine->set_zone($zone);
				}
				Advman_Admin::page_manage_zones();
				break;
			case 'cancel' :
			default :
				Advman_Admin::page_manage_zones();
		}
	}

	/**
	 * Process input from the 'Edit Zone' page
	 */
	function process_edit_zone($action)
	{
		global $advman_engine;
		
		$id = Advman_Admin::get_id_from_form();
		$zone = $advman_engine->get_zone($id);
		
		switch ($action) {
			case 'activate' :
				$advman_engine->set_zone_active($zone, true);
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'copy' :
				$zone = $advman_engine->copy_zone($zone);
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'deactivate' :
				$advman_engine->set_zone_active($zone, false);
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'delete' :
				$advman_engine->remove_zone($zone);
				if ($advman_engine->get_zone_count()) {
					Advman_Admin::page_manage_ads();
				} else {
					Advman_Admin::page_create_ad();
				}
				break;
			case 'reset' :
				$zone->reset_properties();
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'apply' :
				if (Advman_Admin::save_properties($zone)) {
					$advman_engine->set_zone($zone);
				}
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'save' :
				if (Advman_Admin::save_properties($zone)) {
					$advman_engine->set_zone($zone);
				}
				Advman_Admin::page_manage_zones();
				break;
			case 'cancel' :
			default :
				Advman_Admin::page_manage_zones();
		}
	}
	
	/**
	 * Process input from any notices that advman displays
	 */
	function process_notices()
	{
		$mode = OX_Tools::sanitize($_POST['advman-mode'], 'key');
		if ($mode == 'notice') {
			$action = OX_Tools::sanitize($_POST['advman-action'], 'key');
			$yes = OX_Tools::sanitize($_POST['advman-notice-confirm-yes'], 'key');
			switch ($action) {
				case 'optimise':
					Advman_Tools::set_auto_optimise(!empty($yes));
					Advman_Admin::remove_notice('optimise');
					break;
				case 'activate advertising-manager':
					Advman_Admin::remove_notice('activate advertising-manager');
					break;
			}
		}
	}
	
	/**
	 * Process input from the 'Edit Settings' page
	 */
	function process_edit_settings($action)
	{
		switch ($action) {
			case 'save' :
				if (Advman_Admin::save_properties($zone)) {
					$advman_engine->set_zone($zone);
				}
				Advman_Admin::page_manage_zones();
				break;
			case 'cancel' :
			default :
				Advman_Admin::page_manage_zones();
		}
	}
	
	/**
	 * Displays a page from advman.  Checks the WP version and delivers the correct template
	 */
	function display_page($page, $obj = null)
	{
		$page_path = str_replace('_', '/', $page);
		include_once(ADVMAN_TEMPLATE_PATH . "/{$page_path}.php");
		$class_name = "Advman_Template_{$page}";
		$class = new $class_name;
		$class->display($obj);
	}
	
	/**
	 * Display notices in the Admin UI.  Called staticly from the Wordpress 'admin_notices' hook.
	 */
	function display_notices()
	{
		$notices = Advman_Admin::get_notices();
		if (!empty($notices)) {
			Advman_Admin::display_page('Notice', $notices);
		}
		
	}
	
	/**
	 * Displays the 'Insert Ad' in the Wordpress post/page editor (when it is in HTML mode).
	 */
	function display_editor()
	{
		global $advman_engine;
		
		$url = $_SERVER['REQUEST_URI'];
		if (strpos($url, 'post.php') || strpos($url, 'post-new.php') || strpos($url, 'page.php') || strpos($url, 'page-new.php') || strpos($url, 'bookmarklet.php')) {
			$ads = $advman_engine->get_ads();
			Advman_Admin::display_page('Editor', $ads);
		}
	}
}
?>