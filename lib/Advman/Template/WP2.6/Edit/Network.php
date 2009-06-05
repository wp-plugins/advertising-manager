<?php
require_once(ADVMAN_TEMPLATE_PATH . '/Edit.php');
require_once(ADVMAN_LIB . '/Template/Metabox.php');

class Advman_Template_Edit_Network extends Advman_Template_Edit
{
	function display($ad)
	{
		// Main pane - default options
		$properties = $ad->get_network_property_defaults();
		
		// Account information
		if (isset($properties['account-id']) || isset($properties['slot'])) {
			add_meta_box('advman_account', __('Account Details', 'advman'), array('Advman_Template_Metabox', 'display_account_network'), 'advman', 'main');
		}
		
		// Format information
		if (isset($properties['adformat'])) {
			add_meta_box('advman_format', __('Ad Format', 'advman'), array('Advman_Template_Metabox', 'display_format_network'), 'advman', 'main');
		}
		
		$sections = Advman_Tools::organize_appearance($ad);
		if (!empty($sections)) {
			add_meta_box('advman_colors', __('Default Ad Appearance', 'advman'), array('Advman_Template_Metabox', 'display_colors_network'), 'advman', 'main');
		}
		add_meta_box('advman_display_options', __('Default Website Display Options', 'advman'), array('Advman_Template_Metabox', 'display_options_network'), 'advman', 'main');
		// Main pane - advanced options
		add_meta_box('advman_optimisation', __('Default Optimization Settings', 'advman'), array('Advman_Template_Metabox', 'display_optimisation_network'), 'advman', 'advanced');
		add_meta_box('advman_code', __('Default Code Settings', 'advman'), array('Advman_Template_Metabox', 'display_code_network'), 'advman', 'advanced');
		// Main pane - low priority options
		add_meta_box('advman_history', __('History', 'advman'), array('Advman_Template_Metabox', 'display_history_network'), 'advman', 'advanced');
		
		parent::display($ad, true);
	}
}
?>