<?php
class OX_Tools
{
	function sort($ads)
	{
		uasort($ads, '_sort_by_class');
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
			foreach ($string as $str) {
				$a[] = sanitize_key($str);
			}
			return $a;
		}
		return preg_replace('/[^0-9a-z\_\-]/i', '', $string);
	}
}
?>