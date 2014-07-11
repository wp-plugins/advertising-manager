<?php
require_once(ADVMAN_TEMPLATE_PATH . '/Edit.php');
require_once(ADVMAN_LIB . '/Template/Metabox.php');

class Advman_Template_Edit_Ad extends Advman_Template_Edit
{
	function display($ad)
	{
		// Main pane - default options
		$properties = $ad->get_network_property_defaults();

		// Account information
		$fields = array('account-id','slot','counter','adformat','adtype','alt-text','color-bg','color-border','color-link','color-text','color-title','font-text','font-title','status');

		foreach ($fields as $field) {
			if (isset($properties[$field])) {
				add_meta_box('advman_settings', __('Settings', 'advman'), array('Advman_Template_Metabox', 'display_settings_ad'), 'advman', 'normal');
				break;
			}
		}
		
		add_meta_box('advman_display_options', __('Website Display Options', 'advman'), array('Advman_Template_Metabox', 'display_options_ad'), 'advman', 'normal');

		// Main pane - advanced options
		add_meta_box('advman_verification', __('Verification', 'advman'), array('Advman_Template_Metabox', 'display_verification_ad'), 'advman', 'advanced');
		add_meta_box('advman_code', __('Code', 'advman'), array('Advman_Template_Metabox', 'display_code_ad'), 'advman', 'advanced');

		// Main pane - low priority options
		add_meta_box('advman_history', __('History', 'advman'), array('Advman_Template_Metabox', 'display_history_ad'), 'advman', 'advanced');

		// Sidebar - default options
		add_meta_box('advman_submit', __('Save Settings', 'advman'), array('Advman_Template_Metabox', 'display_save_settings_ad'), 'advman', 'side');
		add_meta_box('advman_shortcuts', __('Shortcuts', 'advman'), array('Advman_Template_Metabox', 'display_shortcuts_ad'), 'advman', 'side');
		add_meta_box('advman_notes', __('Notes', 'advman'), array('Advman_Template_Metabox', 'display_notes_ad'), 'advman', 'side');
		
		parent::display($ad);
	}
}
?>