<?php

require_once (ADVMAN_LIB . '/Tools.php');
require_once (ADVMAN_LIB . '/List.php');
require_once (ADVMAN_LIB . '/Ad.php');
require_once (ADVMAN_LIB . '/Network.php');
require_once (ADVMAN_LIB . '/Editor.php');
require_once (ADVMAN_LIB . '/Notice.php');

class Advman_Admin
{
	/**
	 * Add plugin hooks
	 */
	function init()
	{
        // 'Ads' top level menu
        add_object_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-list', array('Advman_List','process'));

        // 'List' submenu item
        $listhook = add_submenu_page('advman-list', __('All Ads', 'advman'), __('All Ads', 'advman'), 8, 'advman-list', array('Advman_List','process'));
        add_action("load-$listhook", array('Advman_List', 'add_options'));
        add_action("admin_head-$listhook", array('Advman_List', 'add_contextual_help' ));
        add_action("admin_head-$listhook", array('Advman_List', 'add_css' ));

        // 'Create' submenu item
        add_submenu_page('advman-list', __('Add New', 'advman'), __('Add New', 'advman'), 8, 'advman-ad-new', array('Advman_Ad','process'));

        // 'Edit' item - adding a submenu page with a null parent will make the page available, but will not place on a menu - the intended effect for edit pages
        add_submenu_page(null, __('Edit Ad', 'advman'), __('Edit Ad', 'advman'), 8, 'advman-ad', array('Advman_Ad','process'));
        add_submenu_page(null, __('Edit Network', 'advman'), __('Edit Network', 'advman'), 8, 'advman-network', array('Advman_Network','process'));

        // 'Settings' for Advertising Manager
        add_options_page(__('Ads', 'advman'), __('Ads', 'advman'), 8, 'advman-settings', array('Advman_Settings','process'));

        // Display any advman notices if we have any
        add_action('admin_notices', array('Advman_Notice','display'), 1 );

        // Add our 'add ad' select box to a few screens
		add_action('admin_footer-post.php', array('Advman_Editor','process'));
        add_action('admin_footer-post-new.php', array('Advman_Editor','process'));
        add_action('admin_footer-page.php', array('Advman_Editor','process'));
        add_action('admin_footer-page-new.php', array('Advman_Editor','process'));
        add_action('admin_footer-bookmarklet.php', array('Advman_Editor','process'));

        // Process any actions
        $page = OX_Tools::get_request_var('page');

        switch ($page) {
            case 'advman-ad-new'  : Advman_Ad::init(); break;
            case 'advman-ad'      : Advman_Ad::init(); break;
            case 'advman-network' : Advman_Network::init(); break;
            case 'advman-list'    : Advman_List::init(); break;
        }

        // Check to see if any notice was acted upon as well
        //Advman_Notice::process();

        //$mode = OX_Tools::sanitize_post_var('advman-mode');

        //if ($mode == 'notice') {
        //    Advman_Notice::process();
		//}
    }

	/**
	 * Process input from the Admin UI.  Called staticly from the Wordpress form screen.
	 */
    function process_old()
    {
        global $advman_engine;

        $page = get_query_var('action');
        wp_die("action:$page");
		
		$filter = null;
		$mode = OX_Tools::sanitize_post_var('advman-mode');
		$action = OX_Tools::sanitize_post_var('advman-action');
		$target = OX_Tools::sanitize_post_var('advman-target');
		$targets = OX_Tools::sanitize_post_var('advman-targets');
		
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
			
			case 'clear' :
				break;
			


			case 'default' :
				$default = ($advman_engine->getSetting('default-ad') != $ad->name ? $ad->name : '');
				$advman_engine->setSetting('default-ad', $default);
				break;
			

			case 'edit' :
				$mode = !empty($id) ? 'edit_ad' : 'edit_network';
				break;
			
			case 'filter' :
				$filter_active = OX_Tools::sanitize_post_var('advman-filter-active');
				$filter_network = OX_Tools::sanitize_post_var('advman-filter-network');
				if (!empty($filter_active)) {
					$filter['active'] = $filter_active;
				}
				if (!empty($filter_network)) {
					$filter['network'] = $filter_network;
				}
				break;
			
			case 'list' :
				$mode = 'list_ads';
				break;
			
			case 'reset' :
				$mode = 'edit_network';
				$ad = $advman_engine->factory($target);
				if ($ad) {
					$ad->reset_network_properties();
					$advman_engine->setAdNetwork($ad);
				}
				break;
			
			case 'apply' :
			case 'save' :
				if ($mode == 'edit_ad') {
					if (Advman_Admin::save_properties($ad)) {
						$advman_engine->setAd($ad);
					}
				} elseif ($mode == 'edit_network') {
					$ad = $advman_engine->factory($target);
					if ($ad) {
						if (Advman_Admin::save_properties($ad, true)) {
							$advman_engine->setAdNetwork($ad);
						}
					}
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
		
		$template = null;
		switch ($mode) {
			case 'create_ad' :
				$template = Advman_Tools::get_template('Create');
				$template->display();
				break;
			
			case 'edit_ad' :
				$template = Advman_Tools::get_template('Edit_Ad', $ad);
				$template->display($ad);
				break;
			
			case 'edit_network' :
				$ad = $advman_engine->factory($target);
				if ($ad) {
					$template = Advman_Tools::get_template('Edit_Network', $ad);
					$template->display($ad);
				}
				break;
			
			case 'settings' :
				$template = Advman_Tools::get_template('Settings');
				$template->display();
				break;
			
			case 'list_ads' :
			default :
				$template = Advman_Tools::get_template('List');
				$template->display(null, $filter);
				break;
			
		}
		
		if (is_null($template)) {
			$template = Advman_Tools::get_template('List');
			$template->display();
		}
	}
}
?>