<?php
class OX_Tools
{
	function sort($ads)
	{
		uasort($ads, array('OX_Tools', '_sort_by_class'));
		return $ads;
	}
	
	function _sort_by_class($a,$b)
	{
		return strcmp(get_class($a), get_class($b));
	}
	
	function sanitize_number($number)
	{
		return preg_replace('/[^0-9\.\-]/i', '', $number);
	}
	function sanitize_key($string)
	{
		if (is_array($string)) {
			$a = array();
			foreach ($string as $n => $str) {
				$a[$n] = OX_Tools::sanitize_key($str);
			}
			return $a;
		}
		return preg_replace('/[^0-9a-z\_\-]/i', '', $string);
	}
	
	function get_last_edit($ad)
	{
		$last_user = 'Unknown';
		$last_timestamp = 0;
		
		if (!empty($ad->p['revisions'])) {
			$revisions = $ad->p['revisions'];
			if (!empty($revisions)) {
				foreach($revisions as $t => $u) {
					$last_user = $u;
					$last_timestamp = $t;
					break; // just get first one - the array is sorted by reverse date
				}
			}
		}
		
		return array($last_user, $last_timestamp);
	}
}
?>