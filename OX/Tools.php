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
	function sanitize_format($number)
	{
		if (strtolower($number) == 'custom') {
			return $number;
		}
		
		return preg_replace('/[^0-9x]/i', '', $number);
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
		$last_user = __('Unknown', 'advman');
		$last_timestamp = 0;
		
		$revisions = $ad->get('revisions');
		if (!empty($revisions)) {
			foreach($revisions as $t => $u) {
				$last_user = $u;
				$last_timestamp = $t;
				break; // just get first one - the array is sorted by reverse date
			}
		}
		
		return array($last_user, $last_timestamp);
	}
	
	function add_revision($revisions = null)
	{
		// Get the user login information
		global $user_login;
		get_currentuserinfo();
		
		// If there is no revisions, use my own revisions
		if (!is_array($revisions)) {
			$revisions = array();
		}
		
		// Deal with revisions
		$r = array();
		$now = mktime();
		$r[$now] = $user_login;
		
		// Get rid of revisions more than 30 days old
		if (!empty($revisions)) {
			foreach ($revisions as $ts => $user) {
				$days = (strtotime($now) - strtotime($ts)) / 86400 + 1;
				if ($days <= 30) {
					$r[$ts] = $user;
				}
			}
		}
		
		krsort($r);
		return $r;
	}
	function post_url($url, $data, $optional_headers = null)
	{
		$params = array('http' => array(
			'method' => 'post',
			'content' => $data
		));
		if ($optional_headers!== null) {
			$params['http']['header'] = $optional_headers;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			//throw new Exception("Problem with $url, $php_errormsg");
			return false;  // silently fail
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
			//throw new Exception("Problem reading data from $url, $php_errormsg");
			return false; //silently fail
		}
		return $response;
	}
}
?>