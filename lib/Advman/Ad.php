<?php
require_once (ADVMAN_LIB . '/Tools.php');

class Advman_Ad
{
    function init()
    {
        global $advman_engine;

        $ad = Advman_Tools::current_ad();
        $action = OX_Tools::get_request_var('action');

        if ($ad) {
            if ($_POST) {
                if (Advman_Tools::save_properties($ad)) {
                    $advman_engine->setAd($ad);
                }
                if ($action == 'save') {
                    wp_redirect(admin_url('?page=advman-list'));
                    exit();
                }
            }

            if ($action) {
                switch ($action) {
                    case 'activate' :
                        if (!$ad->active) {
                            $ad->active = true;
                            $advman_engine->setAd($ad);
                        }
                        break;
                    case 'deactivate' :
                        if ($ad->active) {
                            $ad->active = false;
                            $advman_engine->setAd($ad);
                        }
                        break;
                    case 'copy' :
                        $ad = $advman_engine->copyAd($ad->id);
                        break;
                    case 'delete' :
                        $ad = $advman_engine->deleteAd($ad->id);
                        wp_redirect("?page=advman-list");
                        exit;
                }

                $url = remove_query_arg(array('action', '_wpnonce'));
                wp_redirect($url);
            }

        } else {
            if ($_POST) {
                $tag = OX_Tools::sanitize($_POST['advman-code']);
                $ad = $advman_engine->importAdTag($tag);
                wp_redirect(admin_url('?page=advman-ad&ad=' . $ad->id));
                exit();
            }
        }

        return $ad;
    }

    function process()
    {
        $ad = Advman_Tools::current_ad();

        if ($ad) {
            $template = Advman_Tools::get_template('Edit_Ad', $ad);
            $template->display($ad);
        } else {
            $template = Advman_Tools::get_template('Create');
            $template->display();
        }
    }
}
?>