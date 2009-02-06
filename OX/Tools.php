<?php
class OX_Tools
{
	function sort($ads)
	{
		uasort($ads, 'sort_by_class');
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
}
?>