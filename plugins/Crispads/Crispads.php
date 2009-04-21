<?php

global $advman_engine;
require_once (OX_PLUGIN_PATH . '/Crispads/Adnet.php');
$advman_engine->addAction('register_ad_network', 'OX_Adnet_Crispads');

?>