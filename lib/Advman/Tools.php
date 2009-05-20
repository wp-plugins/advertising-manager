<?php
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
	
	/**
	 * Get a template based on the class of an object
	 */
	function get_template($name)
	{
		$namePath = str_replace('_', '/', $name);
		include_once(ADVMAN_TEMPLATE_PATH . "/{$namePath}.php");
		$className = "Advman_Template_{$name}";
		return new $className;
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
	
	function organize_formats($formats)
	{
		$fmt = array();
		$fmt['horizontal']['800x90'] = __('800 x 90 Large Leaderboard', 'advman');
		$fmt['horizontal']['728x90'] = __('728 x 90 Leaderboard', 'advman');
		$fmt['horizontal']['600x90'] = __('600 x 90 Small Leaderboard', 'advman');
		$fmt['horizontal']['550x250'] = __('550 x 250 Mega Unit', 'advman');
		$fmt['horizontal']['550x120'] = __('550 x 120 Small Leaderboard', 'advman');
		$fmt['horizontal']['550x90'] = __('550 x 90 Small Leaderboard', 'advman');
		$fmt['horizontal']['468x180'] = __('468 x 180 Tall Banner', 'advman');
		$fmt['horizontal']['468x120'] = __('468 x 120 Tall Banner', 'advman');
		$fmt['horizontal']['468x90'] = __('468 x 90 Tall Banner', 'advman');
		$fmt['horizontal']['468x60'] = __('468 x 60 Banner', 'advman');
		$fmt['horizontal']['450x90'] = __('450 x 90 Tall Banner', 'advman');
		$fmt['horizontal']['430x90'] = __('430 x 90 Tall Banner', 'advman');
		$fmt['horizontal']['400x90'] = __('400 x 90 Tall Banner', 'advman');
		$fmt['horizontal']['234x60'] = __('234 x 60 Half Banner', 'advman');
		$fmt['horizontal']['200x90'] = __('200 x 90 Tall Half Banner', 'advman');
		$fmt['horizontal']['150x50'] = __('150 x 50 Half Banner', 'advman');
		$fmt['horizontal']['120x90'] = __('120 x 90 Button', 'advman');
		$fmt['horizontal']['120x60'] = __('120 x 60 Button', 'advman');
		$fmt['horizontal']['83x31'] = __('83 x 31 Micro Bar', 'advman');
		$fmt['vertical']['160x600'] = __('160 x 600 Wide Skyscraper', 'advman');
		$fmt['vertical']['120x600'] = __('120 x 600 Skyscraper', 'advman');
		$fmt['vertical']['200x360'] = __('200 x 360 Wide Half Banner', 'advman');
		$fmt['vertical']['240x400'] = __('240 x 400 Vertical Rectangle', 'advman');
		$fmt['vertical']['180x300'] = __('180 x 300 Tall Rectangle', 'advman');
		$fmt['vertical']['200x270'] = __('200 x 270 Tall Rectangle', 'advman');
		$fmt['vertical']['120x240'] = __('120 x 240 Vertical Banner', 'advman');
		$fmt['square']['336x280'] = __('336 x 280 Large Rectangle', 'advman');
		$fmt['square']['336x160'] = __('336 x 160 Wide Rectangle', 'advman');
		$fmt['square']['334x100'] = __('334 x 100 Wide Rectangle', 'advman');
		$fmt['square']['300x250'] = __('300 x 250 Medium Rectangle', 'advman');
		$fmt['square']['300x150'] = __('300 x 150 Small Wide Rectangle', 'advman');
		$fmt['square']['300x125'] = __('300 x 125 Small Wide Rectangle', 'advman');
		$fmt['square']['300x70'] = __('300 x 70 Mini Wide Rectangle', 'advman');
		$fmt['square']['250x250'] = __('250 x 250 Square', 'advman');
		$fmt['square']['200x200'] = __('200 x 200 Small Square', 'advman');
		$fmt['square']['200x180'] = __('200 x 180 Small Rectangle', 'advman');
		$fmt['square']['180x150'] = __('180 x 150 Small Rectangle', 'advman');
		$fmt['square']['160x160'] = __('160 x 160 Small Square', 'advman');
		$fmt['square']['125x125'] = __('125 x 125 Button', 'advman');
		$fmt['custom']['custom'] = __('Custom width and height', 'advman');

		foreach ($fmt as $section => $fmt1) {
			foreach ($fmt1 as $name => $label) {
				if (!in_array($name, $formats)) {
					unset($fmt[$section][$name]);
					if (empty($fmt[$section])) {
						unset($fmt[$section]);
					}
				}
			}
		}
		
		$sct['horizontal'] = __('Horizontal', 'advman');
		$sct['vertical'] = __('Vertical', 'advman');
		$sct['square'] = __('Square', 'advman');
		$sct['custom'] = __('Custom', 'advman');
		
		foreach ($sct as $section => $name) {
			if (!isset($fmt[$section])) {
				unset($sct[$section]);
			}
		}
		
		return array('sections' => $sct, 'formats' => $fmt);
	}
}
?>