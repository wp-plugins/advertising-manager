<?php

global $advman_engine;
require_once (OX_PLUGIN_PATH . '/Adgridwork/Adnet.php');
$advman_engine->addAction('register_ad_network', 'OX_Adnet_Adgridwork');

if (is_admin()) {
	$advman_engine->addAction('display_template', array('EditAd', 'Template_EditAd_Adgridwork', OX_PLUGIN_PATH . '/Adgridwork/EditAd.php'));
	$advman_engine->addAction('display_template', array('EditNetwork', 'Template_EditNetwork_Adgridwork', OX_PLUGIN_PATH . '/Adgridwork/EditNetwork.php'));
}

?>