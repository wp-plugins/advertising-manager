<?php
// Init some params
$myPath = OX_PLUGIN_PATH . '/Adroll';
$myClass = 'OX_Adnet_Adroll';

require_once ("{$myPath}/Adnet.php");

// Register the ad network on the adserver engine
global $advman_engine;
$advman_engine->addAction('register_ad_network', $myClass);
// Register the ad network templates if admin
if (is_admin()) {
	OX_Admin_Wordpress::add_action('display_template_EditAd', $myClass, array("{$myPath}/EditAd.php", "{$myClass}_Template_EditAd"));
	OX_Admin_Wordpress::add_action('display_template_EditNetwork', $myClass, array("{$myPath}/EditNetwork.php", "{$myClass}_Template_EditNetwork"));
}

?>