<?php
require_once(ADVMAN_TEMPLATE_PATH . '/Edit.php');
require_once(ADVMAN_LIB . '/Template/Metabox.php');

class Advman_Template_Edit_Ad extends Advman_Template_Edit
{
	function display($ad)
	{
		// Main pane - default options
		add_meta_box('advman_account', __('Account Details', 'advman'), array('Advman_Template_Metabox', 'display_account_ad'), 'advman', 'main');
		$formats = $ad->get_ad_formats();
		if (!empty($formats)) {
			add_meta_box('advman_format', __('Ad Format', 'advman'), array('Advman_Template_Metabox', 'display_format_ad'), 'advman', 'main');
		}
		$sections = Advman_Tools::organize_appearance($ad);
		if (!empty($sections)) {
			add_meta_box('advman_colors', __('Ad Appearance', 'advman'), array('Advman_Template_Metabox', 'display_colors_ad'), 'advman', 'main');
		}
		add_meta_box('advman_display_options', __('Website Display Options', 'advman'), array('Advman_Template_Metabox', 'display_options_ad'), 'advman', 'main');
		// Main pane - advanced options
		add_meta_box('advman_optimisation', __('Optimization', 'advman'), array('Advman_Template_Metabox', 'display_optimisation_ad'), 'advman', 'advanced');
		add_meta_box('advman_code', __('Code', 'advman'), array('Advman_Template_Metabox', 'display_code_ad'), 'advman', 'advanced');
		// Main pane - low priority options
		add_meta_box('advman_history', __('History', 'advman'), array('Advman_Template_Metabox', 'display_history_ad'), 'advman', 'advanced');
		
		parent::display($ad);
	}
}
?>