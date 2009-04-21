<?php

global $advman_engine;
require_once (OX_PLUGIN_PATH . '/Adsense/Adnet.php');
$advman_engine->addAction('register_ad_network', 'OX_Adnet_Adsense');

if (is_admin()) {
	$advman_engine->addAction('display_template', array('EditAd', 'Template_EditAd_Adsense', OX_PLUGIN_PATH . '/Adsense/EditAd.php'));
	$advman_engine->addAction('display_template', array('EditNetwork', 'Template_EditNetwork_Adsense', OX_PLUGIN_PATH . '/Adsense/EditNetwork.php'));
}

?>