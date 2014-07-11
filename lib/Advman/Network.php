<?php
require_once (ADVMAN_LIB . '/Tools.php');

class Advman_Network
{
    function init()
    {
        global $advman_engine;

        $network = Advman_Tools::current_network();
        $action = OX_Tools::get_request_var('action');

        if ($network) {
            if ($_POST) {
                echo "post:<br>";
                print_r($_POST);
                echo "<br><br>network before:";
                print_r($network);
                if (Advman_Tools::save_properties($network, true)) {
                    echo "<br><br>network after:";
                    print_r($network);
                    exit;
                    $advman_engine->setAdNetwork($network);
                }
                if ($action == 'save') {
                    wp_redirect(admin_url('?page=advman-list'));
                    exit();
                }
            }
        }

        return $network;
    }

    function process()
    {
        $network = Advman_Tools::current_network();

        if ($network) {
            $template = Advman_Tools::get_template('Edit_Network', $network);
            $template->display($network);
        } else {
            wp_die(__('Could not edit default settings for this network', 'advman'));
        }
    }
}
?>