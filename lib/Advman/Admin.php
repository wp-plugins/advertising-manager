<?php
require_once (ADVMAN_LIB . '/Tools.php');

class Advman_Admin
{
	function init()
	{
		global $plugin_page;
		global $advman_page;
		
		// Check to see if we are submitting from our page.  We need to do this early (before the screen starts loading), becuase different pages need either redirection, or special script /style files.
		if (substr($plugin_page,0,6) == 'advman') {
			$action = OX_Tools::sanitize($_REQUEST['advman-action'], 'key');
			$mode = OX_Tools::sanitize($_REQUEST['advman-mode'], 'key');
			// by default, go to the list screen
			if (empty($mode)) {
				$mode = 'list';
			}
			$current_page = $plugin_page . ':' . $mode;
			
			switch ($current_page) {
				case 'advman-ad:list' :		$p = Advman_Admin::process_ad_list($action); break;
				case 'advman-ad:create' :	$p = Advman_Admin::process_ad_create($action); break;
				case 'advman-ad:edit' :		$p = Advman_Admin::process_ad_edit($action); break;
				case 'advman-ad:preview' :	$p = Advman_Admin::process_ad_preview($action); break;
				case 'advman-ad:edit_network' :	$p = Advman_Admin::process_ad_edit_network($action); break;

				case 'advman-zone:list' :	$p = Advman_Admin::process_zone_list($action); break;
				case 'advman-zone:create' :	$p = Advman_Admin::process_zone_create($action); break;
				case 'advman-zone:edit' :	$p = Advman_Admin::process_zone_edit($action); break;

				case 'advman-stats:list' :	$p = Advman_Admin::process_statistics($action); break;
				default : $p = 'List_Ad'; break;
			}
			
			// Deal with redirecting pages
			if ($current_page != $p) {
				list($page, $mode, $id) = preg_split('/[:]/', $p);
				$p = "admin.php?page=$page";
				if (!empty($mode)) {
					$p .= "&advman-mode=$mode";
				}
				if (!empty($id)) {
					$p .= "&advman-id=$id";
				}
				wp_redirect($p);
			} else {
				// Build the page we are eventually going to
				switch ($p) {
					case 'advman-ad:list' :		$p = 'List/Ad'; break;
					case 'advman-ad:create' :	$p = 'Create/Ad'; break;
					case 'advman-ad:edit' :		$p = 'Edit/Ad'; break;
					case 'advman-ad:preview' :	$p = 'Preview/Ad'; break;
					case 'advman-ad:edit_network' :	$p = 'Network/Ad'; break;
	
					case 'advman-zone:list' :	$p = 'List/Zone'; break;
					case 'advman-zone:create' :	$p = 'Create/Zone'; break;
					case 'advman-zone:edit' :	$p = 'Edit/Zone'; break;
	
					case 'advman-stats:list' :	$p = 'List/Stats'; break;
					default : $p = 'List/Ad'; break;
				}
				
				include_once (ADVMAN_TEMPLATE_PATH . "/{$p}.php");
				$o = str_replace('/', '_', $p);
				$n = "Advman_Template_{$o}";
				$advman_page = new $n;
			}
			
		}
	}
	/**
	 * Initialise menu items, notices, etc.
	 */
	function menu()
	{
		global $wp_version;
		
		if (version_compare($wp_version,"2.7-alpha", '>')) {
			add_object_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-ad', array('Advman_Admin','display'));
			add_submenu_page('advman-ad', __('Manage Ads', 'advman'), __('Manage Ads', 'advman'), 8, 'advman-ad', array('Advman_Admin','display'));
			add_submenu_page('advman-ad', __('Manage Ad Zones', 'advman'), __('Manage Zones', 'advman'), 8, 'advman-zone', array('Advman_Admin','display'));
			add_submenu_page('advman-ad', __('Ad Statistics', 'advman'), __('Statistics', 'advman'), 8, 'advman-stats', array('Advman_Admin','display'));
			add_options_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-settings', array('Advman_Admin','display'));
		} else {
			add_menu_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-ad', array('Advman_Admin','display'));
			add_submenu_page('advman-ad', __('Manage Ads', 'advman'), __('Manage Ads', 'advman'), 8, 'advman-ad', array('Advman_Admin','display'));
			add_submenu_page('advman-ad', __('Manage Zones', 'advman'), __('Manage Zones', 'advman'), 8, 'advman-zone', array('Advman_Admin','display'));
			add_submenu_page('advman-ad', __('Ad Statistics', 'advman'), __('Statistics', 'advman'), 8, 'advman-stats', array('Advman_Admin','display'));
			add_options_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 8, 'advman-settings', array('Advman_Admin','display'));
		}
		
		// Deal with input that needs to be processed before Wordpress menus display
		Advman_Admin::pre_process();
		
	}
	
	function add_scripts()
	{
		global $plugin_page;
		
		if (is_admin() && ($plugin_page == 'advman-ad' || $plugin_page == 'advman-zone' || $plugin_page == 'advman-stats')) {
			add_thickbox();
			wp_enqueue_script('postbox');
			wp_enqueue_script('thickbox', array('jquery'));
			wp_enqueue_script('jquery-multiselect', ADVMAN_URL . '/scripts/jquery.multiSelect.js', array('jquery'));
			wp_enqueue_script('advman-link', ADVMAN_URL . '/scripts/link.dev.js', array('jquery','wp-lists','wp-ajax-response'));
			wp_enqueue_script('advman-admin', ADVMAN_URL . '/scripts/advman.admin.js');
		}
	}
	
	function add_styles()
	{
		global $plugin_page;
		
		if (is_admin() && ($plugin_page == 'advman-ad' || $plugin_page == 'advman-zone' || $plugin_page == 'advman-stats')) {
			wp_enqueue_style('thickbox');
			wp_enqueue_style('advman-admin', ADVMAN_URL . '/scripts/advman.admin.css');
			wp_enqueue_style('jquery-multiselect', ADVMAN_URL . '/scripts/jquery.multiSelect.css');
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
	
	function pre_process()
	{
		// Process any notices that have been taken down
		Advman_Admin::process_notices();
		// Display an ad preview
//		Advman_Admin::process_preview_ad();
	}
	/**
	 * Process input from the Admin UI.  Called staticly from the Wordpress form screen.
	 * page values:
	 * 	advman-ad
	 * 	advman-zone
	 * 	advman-stats
	 *
	 * mode value:
	 * 	advman-list
	 * 	advman-create
	 * 	advman-edit
	 */
	function process()
	{
		global $advman_engine;
		global $plugin_page;
		
		$mode = OX_Tools::sanitize($_REQUEST['advman-mode'], 'key');
		$action = OX_Tools::sanitize($_REQUEST['advman-action'], 'key');
		
		switch ($plugin_page . '-' . $mode) {
			case 'advman-zone-create' :
				Advman_Admin::process_zone_create($action);
				break;
			case 'advman-zone-edit' :
				Advman_Admin::process_zone_edit($action);
				break;
			case 'advman-zone-list' :
			case 'advman-zone-' :
				Advman_Admin::process_zone_list($action);
				break;
			case 'advman-stats' :
				Advman_Admin::process_statistics($action);
				break;
			case 'advman-ad-create' :
				Advman_Admin::process_ad_create($action);
				break;
			case 'advman-ad-edit' :
				Advman_Admin::process_ad_edit($action);
				break;
			case 'advman-ad-preview' :
				Advman_Admin::process_ad_preview($action);
				break;
			case 'advman-ad-edit_network' :
				Advman_Admin::process_ad_edit_network($action);
				break;
			case 'advman-ad-list' :
			case 'advman-ad-' :
			default :
				Advman_Admin::process_ad_list($action);
				break;
		}
	}
	
	/**
	 * Process input from the 'Ad List' page
	 */
	function process_ad_list($action)
	{
		global $advman_engine;
		global $plugin_page;
		
		
		$ids = Advman_Tools::get_ids_from_form();
		$ads = $advman_engine->get_ads($ids);
		$page = 'advman-ad:list';
		
		switch ($action) {
			case 'activate' :
				$advman_engine->set_ads_active($ids, true);
				break;
			case 'copy' :
				$advman_engine->copy_ads($ids);
				break;
			case 'create' :
				$page = 'Create_Ad';
				break;
			case 'deactivate' :
				$advman_engine->set_ads_active($ids, false);
				break;
			case 'default' :
				$ad = $ads[0]; // can only set one ad as default
				$default = ($advman_engine->get_setting('default-ad') != $ad->name ? $ad->name : '');
				$advman_engine->set_setting('default-ad', $default);
				break;
			case 'delete' :
				$advman_engine->remove_ads($ids);
				break;
			case 'edit' :
				$ad = $ads[0]; // can only edit one ad at a time
				$page = 'Edit_Ad';
				break;
//			case 'filter' :
//				$filter = Advman_Admin::get_filter_from_form();
//				Advman_Admin::display_page('List_Ad', $filter);
//				break;
			case 'preview' :
				$ad = $ads[0]; // can only preview one ad at a time
				$page = 'Preview_Ad';
				break;
		}
		
		// If there are no ads, go to the 'Create Ad' page
		if ($advman_engine->get_ad_count() == 0) {
			$page = 'advman-ad:create';
		}
		
		return $page;
	}
	
	/**
	 * Process input from the 'Create Ad' page
	 */
	function process_ad_create($action)
	{
		global $advman_engine;
		
		$page = 'advman-ad:create';
		
		switch ($action) {
			case 'import' :
				$tag = Advman_Tools::get_tag_from_form();
				$ad = $advman_engine->import_ad_tag($tag);
				$ad->name = Advman_Tools::generate_ad_name($ad);
				$page = 'advman-ad:edit:' . $ad->id;
				break;
			case 'cancel' :
				$page = 'advman-ad:list';
		}
		
		return $page;
	}

	/**
	 * Process input from the 'Preview Ad' page
	 */
	function process_ad_preview($action)
	{
		
	}
	/**
	 * Process input from the 'Edit Ad' page
	 */
	function process_ad_edit($action)
	{
		global $advman_engine;
		
		$id = OX_Tools::sanitize($_REQUEST['advman-id'], 'number');
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
				if (Advman_Tools::save_properties($ad)) {
					$advman_engine->set_ad($ad);
				}
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
			case 'save' :
				if (Advman_Tools::save_properties($ad)) {
					$advman_engine->set_ad($ad);
				}
				Advman_Admin::display_page('List_Ad');
				break;
			case 'cancel' :
				Advman_Admin::display_page('List_Ad');
				break;
			default :
				Advman_Admin::display_page('Edit_Ad', $ad);
				break;
		}
	}
		
	/**
	 * Process input from the 'Edit Ad Network' page
	 */
	function process_ad_edit_network($action)
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
				if (Advman_Tools::save_properties($network)) {
					$advman_engine->set_network($network);
				}
				Advman_Admin::display_page('Edit_Network', $network);
				break;
			case 'save' :
				if (Advman_Tools::save_properties($network)) {
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
	 * Process input from the 'List Zones' page
	 */
	function process_zone_list($action)
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
	function process_zone_create($action)
	{
		global $advman_engine;
		
		$id = Advman_Admin::get_id_from_form();
		$zone = $advman_engine->get_zone($id);
		
		switch ($action) {
			case 'reset' :
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'apply' :
				if (Advman_Tools::save_properties($zone)) {
					$advman_engine->add_zone($zone);
				}
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'save' :
				if (Advman_Tools::save_properties($zone)) {
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
	function process_zone_edit($action)
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
				if (Advman_Tools::save_properties($zone)) {
					$advman_engine->set_zone($zone);
				}
				Advman_Admin::page_edit_zone($zone);
				break;
			case 'save' :
				if (Advman_Tools::save_properties($zone)) {
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
	function process_settings_edit($action)
	{
		switch ($action) {
			case 'save' :
				if (Advman_Tools::save_properties($zone)) {
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
	function display()
	{
		global $advman_page;
		
		switch ($advman_page) {
			case 'advman-ad:create' : $page_path = 'Create_Ad'; break;
			case 'advman-ad:list'	: $page_path = 'List_Ad'; break;
			case 'advman-ad:edit'	: $page_path = 'Edit_Ad'; break;
		}
		
		if (!empty($page_path)) {
			include_once(ADVMAN_TEMPLATE_PATH . "/{$obj}.php");
			$class_name = "Advman_Template_{$obj}";
			$class = new $class_name;
			$class->display($obj);
		}
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
/*
 To request a page with the wordpress adornments:
 admin.php?page=advman-ad&action=edit&id=1
 admin.php?page=advman-ad&action=delete&id=1&id=2&id=3
 To request a page without the wordpress adornments:
*/
 
?>