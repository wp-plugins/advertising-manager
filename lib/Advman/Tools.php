<?php
require_once(OX_LIB . '/Tools.php');

class Advman_Tools
{
	/**
	 * Get the last edit of this ad
	 */
	function get_last_edit($revisions)
	{
		$last_user = __('Unknown', 'advman');
		$last_timestamp = 0;
		
		if (!empty($revisions)) {
			foreach($revisions as $t => $u) {
				$last_user = $u;
				$last_timestamp = $t;
				break; // just get first one - the array is sorted by reverse date
			}
		}
		
		if ((time() - $last_timestamp) < (30 * 24 * 60 * 60)) { // less than 30 days ago
			$last_timestamp =  human_time_diff($t);
			$last_timestamp2 = date('l, F jS, Y @ h:ia', $t);
		} else {
			$last_timestamp =  __('> 30 days', 'advman');
			$last_timestamp2 = '';
		}
		return array($last_user, $last_timestamp, $last_timestamp2);
	}
	function get_ids_from_form()
	{
		// For operations on a single ad
		$id = OX_Tools::sanitize($_POST['advman-target'], 'number');
		if (is_numeric($id)) {
			$ids = array($id);
		} else {
			$ids = OX_Tools::sanitize($_POST['advman-targets'], 'number');
			if (empty($ids)) {
				$ids = array();
			}
		}
		
		return $ids;
	}
	
	/**
	 * This function is called from the Wordpress Settings menu
	 */
	function settings()
	{
		
		// Get our options and see if we're handling a form submission.
		$action = OX_Tools::sanitize($_POST['advman-action'], 'key');
		if ($action == 'save') {
			global $advman_engine;
			$settings = array('openx-market', 'openx-market-cpm', 'openx-sync');
			foreach ($settings as $setting) {
				$value = isset($_POST["advman-{$setting}"]) ? OX_Tools::sanitize($_POST["advman-{$setting}"]) : false;
				$advman_engine->set_setting($setting, $value);
			}
		}
		$template = Advman_Tools::get_template('Settings');
		$template->display();
	}
	
	function get_filter_from_form()
	{
		$filter_active = OX_Tools::sanitize($_POST['advman-filter-active'], 'key');
		$filter_network = OX_Tools::sanitize($_POST['advman-filter-network'], 'key');
		if (!empty($filter_active)) {
			$filter['active'] = $filter_active;
		}
		if (!empty($filter_network)) {
			$filter['network'] = $filter_network;
		}
	}
	
	function get_tag_from_form()
	{
		$tag = OX_Tools::sanitize($_POST['advman-code']);
	}
		
	function organize_appearance($ad)
	{
		$defaults = $ad->get_network_property_defaults();
		
		$app = array();
		$app['color']['border'] = __('Border:', 'advman');
		$app['color']['bg'] = __('Background:', 'advman');
		$app['color']['title'] = __('Title:', 'advman');
		$app['color']['text'] = __('Text:', 'advman');
		$app['color']['link'] = __('Link:', 'advman');
		$app['font']['title'] = __('Title Font:', 'advman');
		$app['font']['text'] = __('Text Font:', 'advman');
		
		foreach ($app as $section => $app1) {
			foreach ($app1 as $name => $label) {
				if (!isset($defaults["{$section}-{$name}"])) {
					unset($app[$section][$name]);
					if (empty($app[$section])) {
						unset($app[$section]);
					}
				}
			}
		}
		
		return $app;
	}
	
	function organize_formats($tfs)
	{
		$types = array(
			'text' => __('Text ads', 'advman'),
			'image' => __('Image ads', 'advman'),
			'ref_text' => __('Text referrals', 'advman'),
			'ref_image' => __('Image referrals', 'advman'),
			'textimage' => __('Text and image ads', 'advman'),
			'link' => __('Ad links', 'advman'),
			'video' => __('Video ads', 'advman'),
			'all' => __('All ad types', 'advman'),
		);
		
		$sections = array(
			'horizontal' => __('Horizontal', 'advman'),
			'vertical' => __('Vertical', 'advman'),
			'square' => __('Square', 'advman'),
			'other' => __('Other ad formats', 'advman'),
			'custom' => __('Custom width and height', 'advman'),
		);
		
		$formats_horizontal = array(
			'800x90' => __('%1$s x %2$s Large Leaderboard', 'advman'),
			'728x90' => __('%1$s x %2$s Leaderboard', 'advman'),
			'600x90' => __('%1$s x %2$s Small Leaderboard', 'advman'),
			'550x250' => __('%1$s x %2$s Mega Unit', 'advman'),
			'550x120' => __('%1$s x %2$s Small Leaderboard', 'advman'),
			'550x90' => __('%1$s x %2$s Small Leaderboard', 'advman'),
			'468x180' => __('%1$s x %2$s Tall Banner', 'advman'),
			'468x120' => __('%1$s x %2$s Tall Banner', 'advman'),
			'468x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'468x60' => __('%1$s x %2$s Banner', 'advman'),
			'450x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'430x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'400x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'234x60' => __('%1$s x %2$s Half Banner', 'advman'),
			'200x90' => __('%1$s x %2$s Tall Half Banner', 'advman'),
			'150x50' => __('%1$s x %2$s Half Banner', 'advman'),
			'120x90' => __('%1$s x %2$s Button', 'advman'),
			'120x60' => __('%1$s x %2$s Button', 'advman'),
			'83x31' => __('%1$s x %2$s Micro Bar', 'advman'),
			'728x15#4' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
			'728x15#5' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
			'468x15#4' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
			'468x15#5' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
		);
		
		$formats_vertical = array(
			'160x600' => __('%1$s x %2$s Wide Skyscraper', 'advman'),
			'120x600' => __('%1$s x %2$s Skyscraper', 'advman'),
			'200x360' => __('%1$s x %2$s Wide Half Banner', 'advman'),
			'240x400' => __('%1$s x %2$s Vertical Rectangle', 'advman'),
			'180x300' => __('%1$s x %2$s Tall Rectangle', 'advman'),
			'200x270' => __('%1$s x %2$s Tall Rectangle', 'advman'),
			'120x240' => __('%1$s x %2$s Vertical Banner', 'advman'),
		);
		
		$formats_square = array(
			'336x280' => __('%1$s x %2$s Large Rectangle', 'advman'),
			'336x160' => __('%1$s x %2$s Wide Rectangle', 'advman'),
			'334x100' => __('%1$s x %2$s Wide Rectangle', 'advman'),
			'300x250' => __('%1$s x %2$s Medium Rectangle', 'advman'),
			'300x150' => __('%1$s x %2$s Small Wide Rectangle', 'advman'),
			'300x125' => __('%1$s x %2$s Small Wide Rectangle', 'advman'),
			'300x70' => __('%1$s x %2$s Mini Wide Rectangle', 'advman'),
			'250x250' => __('%1$s x %2$s Square', 'advman'),
			'200x200' => __('%1$s x %2$s Small Square', 'advman'),
			'200x180' => __('%1$s x %2$s Small Rectangle', 'advman'),
			'180x150' => __('%1$s x %2$s Small Rectangle', 'advman'),
			'160x160' => __('%1$s x %2$s Small Square', 'advman'),
			'125x125' => __('%1$s x %2$s Button', 'advman'),
			'200x90#4' => __('%1$s x %2$s Tall Half Banner, %3$s Links', 'advman'),
			'200x90#5' => __('%1$s x %2$s Tall Half Banner, %3$s Links', 'advman'),
			'180x90#4' => __('%1$s x %2$s Half Banner, %3$s Links', 'advman'),
			'180x90#5' => __('%1$s x %2$s Half Banner, %3$s Links', 'advman'),
			'160x90#4' => __('%1$s x %2$s Tall Button, %3$s Links', 'advman'),
			'160x90#5' => __('%1$s x %2$s Tall Button, %3$s Links', 'advman'),
			'120x90#4' => __('%1$s x %2$s Button, %3$s Links', 'advman'),
			'120x90#5' => __('%1$s x %2$s Button, %3$s Links', 'advman'),
		);
		
		$sectforms = array(
			'horizontal' => $formats_horizontal,
			'vertical' => $formats_vertical,
			'square' => $formats_square,
		);
		
		$data = array();
		foreach ($tfs as $t => $fs) {
			foreach ($sectforms as $sect => $forms) {
				foreach ($forms as $form => $label) {
					$k = array_search($form, $fs);
					if ($k !== false) {
						$data[$t][$sect][] = $form;
						$formats[$form] = $label;
						unset($fs[$k]);
					}
				}
			}
			
			if (!empty($fs)) {
				foreach ($fs as $k => $f) {
					if ($f == 'custom') {
						$data[$t]['custom'][] = $f;
						$formats[$f] = __('Custom width and height', 'advman');
					} else {
						$data[$t]['other'][] = $f;
						$formats[$f] = (strpos($f, '#') === false) ? __('%1$s x %2$s Banner', 'advman') : __('%1$s x %2$s Banner, %3$s Links', 'advman');
					}
				}
			}
		}
		
		return array('data' => $data, 'types' => $types, 'sections' => $sections, 'formats' => $formats);
	}
	
	function add_zone_ajax()
	{
		global $advman_engine;
		
		check_ajax_referer( 'advman-add-zone' );
		$x = new WP_Ajax_Response();
		$new_zone = trim(OX_Tools::sanitize($_POST['advman-zone-name']));
		$found = false;
		$zones = $advman_engine->get_zones();
		if (!empty($zones)) {
			foreach ( $zones as $zone ) {
				if ($new_zone == $zone->name) {
					$found = true;
					break;
				}
			}
		}
		
		if (!$found) {
			
			$zone = new OX_Zone();
			$zone->name = $new_zone;
			// If the name contains the size, automatically set the size (e.g. 'MyZone 768x60')
			if (preg_match('#(\d*)\s*x\s*(\d*)#', $new_zone, $matches)) {
				$zone->set_property('adformat', $matches[1] . 'x' . $matches[2]);
			}
			$zone = $advman_engine->insertZone($zone);
			$id = $zone->id;
			
			$new_zone = esc_html(stripslashes($new_zone));
			$x->add( array(
				'what' => 'advman-zone',
				'id' => $id,
				'data' => "<li id='advman-zone-{$id}'><label for='in-advman-zone-{$id}' class='selectit'><input value='{$id}' type='checkbox' name='link_category[]' id='in-advman-zone-{$id}' checked='checked' /> {$new_zone}</label></li>",
				'position' => -1
			) );
		}
		$x->send();
	}
	
	function set_auto_optimise($active)
	{
		global $advman_engine;
		
		$market = ($active) ? 'yes' : 'no';
		$ads = $advman_engine->get_ads();
		foreach ($ads as $id => $ad) {
			$p = $ad->get_network_property('openx-market');
			if ($p != $market) {
				$ad->set_network_property('openx-market', $market);
			}
			$p = $ad->get_property('openx-market');
			if (!empty($p) && $p != $market) {
				$ad->set_property('openx-market', $market);
			}
		}
	}
	
	function save_properties(&$ad, $default = false)
	{
		global $advman_engine;
		
		// Whether we changed any setting in this entity
		$changed = false;
		
		// Set the ad properties (if not setting default properties)
		if (!$default) {
			if (isset($_POST['advman-name'])) {
				$value = OX_Tools::sanitize($_POST['advman-name']);
				if ($value != $ad->name) {
					Advman_Admin::check_default($ad, $value);
					$ad->name = $value;
					$changed = true;
				}
			}
			
			if (isset($_POST['advman-active'])) {
				$value = $_POST['advman-active'] == 'yes';
				if ($ad->active != $value) {
					$ad->active = $value;
					$changed = true;
				}
			}
		}
		
		$properties = $ad->get_network_property_defaults();
		if (!empty($properties)) {
			foreach ($properties as $property => $d) {
				if (isset($_POST["advman-{$property}"])) {
					$value = OX_Tools::sanitize($_POST["advman-{$property}"]);
					if ($default) {
						if ($ad->get_network_property($property) != $value) {
							$ad->set_network_property($property, $value);
							$changed = true;
						}
					} else {
						if ($ad->get_property($property) != $value) {
							$ad->set_property($property, $value);
							$changed = true;
						}
					}
					// deal with adtype
					if ($property == 'adtype') {
						if (isset($_POST["advman-adformat-{$value}"])) {
							$v = OX_Tools::sanitize($_POST["advman-adformat-{$value}"]);
							if ($default) {
								if ($ad->get_network_property('adformat') != $v) {
									$ad->set_network_property('adformat', $v);
									$changed = true;
								}
							} else {
								if ($ad->get_property('adformat') != $v) {
									$ad->set_property('adformat', $v);
									$changed = true;
								}
							}
						}
					}
				}
			}
		}
		
		return $changed;
	}
	
	function check_default($ad, $value)
	{
		global $advman_engine;
		
		$d = $advman_engine->get_setting('default-ad');
		if (!empty($d) && $ad->name == $d) {
			$modify = true;
			$ads = $advman_engine->get_ads();
			foreach ($ads as $a) {
				if ($a->id != $ad->id && $a->name == $d) {
					$modify = false;
					break;
				}
			}
			if ($modify) {
				$advman_engine->set_setting('default-ad', $value);
			}
		}
	}
}
?>