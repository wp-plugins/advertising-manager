<?php

global $advman_engine;
require_once (OX_PLUGIN_PATH . '/Adbrite/Adnet.php');
$advman_engine->addAction('register_ad_network', 'OX_Adnet_Adbrite');

if (is_admin()) {
	$advman_engine->addAction('display_template', array('EditAd', 'Template_EditAd_Adbrite', OX_PLUGIN_PATH . '/Adbrite/EditAd.php'));
	$advman_engine->addAction('display_template', array('EditNetwork', 'Template_EditNetwork_Adbrite', OX_PLUGIN_PATH . '/Adbrite/EditNetwork.php'));
}

?>