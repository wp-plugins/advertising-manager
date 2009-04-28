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
	
	function sort($ads)
	{
		uasort($ads, array('OX_Tools', '_sort_by_class'));
		return $ads;
	}
	
	function _sort_by_class($a,$b)
	{
		return strcmp(get_class($a), get_class($b));
	}
	
	function organize_colors($colors)
	{
		$clr = array();
		$clr['border'] = __('Border:', 'advman');
		$clr['bg'] = __('Background:', 'advman');
		$clr['title'] = __('Title:', 'advman');
		$clr['text'] = __('Text:', 'advman');
		
		foreach ($clr as $name => $label) {
			if (!in_array($name, $colors)) {
				unset($clr[$name]);
			}
		}
		
		return $clr;
	}
	
	function organize_formats($formats)
	{
		$fmt = array();
		$fmt['horizontal']['728x90'] = __('728 x 90 Leaderboard', 'advman');
		$fmt['horizontal']['468x60'] = __('468 x 60 Banner', 'advman');
		$fmt['horizontal']['234x60'] = __('234 x 60 Half Banner', 'advman');
		$fmt['vertical']['120x600'] = __('120 x 600 Skyscraper', 'advman');
		$fmt['vertical']['160x600'] = __('160 x 600 Wide Skyscraper', 'advman');
		$fmt['square']['300x250'] = __('300 x 250 Medium Rectangle', 'advman');
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