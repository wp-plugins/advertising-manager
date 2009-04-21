<?php

global $advman_engine;
require_once (OX_PLUGIN_PATH . '/Adify/Adnet.php');
$advman_engine->addAction('register_ad_network', 'OX_Adnet_Adify');

if (is_admin()) {
	$advman_engine->addAction('display_template', array('EditAd', 'Template_EditAd_Adify', OX_PLUGIN_PATH . '/Adify/EditAd.php'));
	$advman_engine->addAction('display_template', array('EditNetwork', 'Template_EditNetwork_Adify', OX_PLUGIN_PATH . '/Adify/EditNetwork.php'));
}

?>