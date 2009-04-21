<?php

global $advman_engine;
require_once (OX_PLUGIN_PATH . '/Adpinion/Adnet.php');
$advman_engine->addAction('register_ad_network', 'OX_Adnet_Adpinion');

if (is_admin()) {
	$advman_engine->addAction('display_template', array('EditAd', 'Template_EditAd_Adpinion', OX_PLUGIN_PATH . '/Adpinion/EditAd.php'));
	$advman_engine->addAction('display_template', array('EditNetwork', 'Template_EditNetwork_Adpinion', OX_PLUGIN_PATH . '/Adpinion/EditNetwork.php'));
}

?>