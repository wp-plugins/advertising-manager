<?php

require_once (ADVMAN_LIB . '/Tools.php');

class Advman_Editor
{
    function process()
    {
        global $advman_engine;

        $ads = $advman_engine->getAds();
        $template = Advman_Tools::get_template('Editor');
        $template->display($ads);
    }
}
?>