<?php

require_once (ADVMAN_LIB . '/Tools.php');

class Advman_Notice
{
    function process()
    {
        $action = OX_Tools::sanitize_post_var('advman-notice-action');
        $yes = OX_Tools::sanitize_post_var('advman-notice-confirm-yes');
        switch ($action) {
            case 'upgraded-from-adsensem':
            case 'activate advertising-manager':
                Advman_Notice::remove_notice($action);
                break;
        }
    }
    /**
     * Display notices in the Admin UI.  Called staticly from the Wordpress 'admin_notices' hook.
     */
    function display()
    {
        $notices = Advman_Notice::get_notices();
        if (!empty($notices)) {
            $template = Advman_Tools::get_template('Notice');
            $template->display($notices);
        }

    }
    function get_notices()
    {
        return get_option('plugin_advman_ui_notices');
    }
    function set_notices($notices)
    {
        return update_option('plugin_advman_ui_notices', $notices);
    }
    function add_notice($action,$text,$confirm=false)
    {
        $notices = Advman_Notice::get_notices();
        $notices[$action]['text'] = $text;
        $notices[$action]['confirm'] = $confirm;
        Advman_Notice::set_notices($notices);
    }
    function remove_notice($action)
    {
        $notices = Advman_Notice::get_notices();
        if (!empty($notices[$action])) {
            unset($notices[$action]);
        }
        Advman_Notice::set_notices($notices);
    }

}