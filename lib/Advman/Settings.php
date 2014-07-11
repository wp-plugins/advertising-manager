<?php
require_once (ADVMAN_LIB . '/Tools.php');

class Advman_Settings
{
    /**
     * This function is called from the Wordpress Settings menu
     */
    function process()
    {

        // Get our options and see if we're handling a form submission.
        $action = OX_Tools::sanitize_post_var('advman-action');
        if ($action == 'save') {
            global $advman_engine;
            $settings = array('verification', 'enable-php', 'stats', 'purge-stats-days');
            foreach ($settings as $setting) {
                $value = isset($_POST["advman-{$setting}"]) ? OX_Tools::sanitize($_POST["advman-{$setting}"]) : false;
                $advman_engine->setSetting($setting, $value);
            }
        }
        $template = Advman_Tools::get_template('Settings');
        $template->display();
    }

}
?>