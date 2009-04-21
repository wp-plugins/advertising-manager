<?php

global $advman_engine;
require_once (OX_PLUGIN_PATH . '/Adroll/Adnet.php');
$advman_engine->addAction('register_ad_network', 'OX_Adnet_Adroll');

if (is_admin()) {
	$advman_engine->addAction('display_template', array('EditAd', 'Template_EditAd_Adroll', OX_PLUGIN_PATH . '/Adroll/EditAd.php'));
	$advman_engine->addAction('display_template', array('EditNetwork', 'Template_EditNetwork_Adroll', OX_PLUGIN_PATH . '/Adroll/EditNetwork.php'));
}

?>