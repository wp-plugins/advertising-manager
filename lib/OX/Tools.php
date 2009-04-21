<?php
class OX_Tools
{
	function require_directory($dir)
	{
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				// Make sure that the first character does not start with a '.' (omit hidden files like '.', '..', '.svn', etc.)
				// as well as make sure the file is not a directory
				if ($file[0] != '.') {
					$require_file = is_dir("{$dir}/{$file}") ? "{$dir}/{$file}/{$file}.php" : "{$dir}/{$file}";
					
					if (file_exists($require_file)) {
						require_once $require_file;
					}
				}
			}
			closedir($handle);
		}
	}

	function get_template($name, $ad = null)
	{
		global $wp_version;

		// Get the template path
		$version = (version_compare($wp_version,"2.7-alpha", "<")) ? '2.6' : '2.7';
		$path = ADVMAN_LIB . "/OX/Admin/Templates/Wordpress/{$version}";

		$className = null;
		
		if (!empty($ad)) {
			$shortName = $ad->shortName;
			if (file_exists("{$path}/{$name}/{$shortName}.php")) {
				include_once("{$path}/{$name}/{$shortName}.php");
				$className = "Template_{$name}_{$shortName}";
			}
		}
		if (empty($className)) {
			include_once("{$path}/{$name}.php");
			$className = "Template_{$name}";
		}
		
		return new $className;
	}
	
	function major_version($v)
	{
		$mv=explode('.', $v);
		return $mv[0]; //Return major version
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
	function sanitize_field($string)
	{
		return str_replace("\0", '', $string);
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
		return preg_replace('#[^a-z0-9-_]#i', '', $string);
	}

	function get_key($key, $default = null)
	{
		return OX_Tools::_get_key($key, $default, $_GET);
	}
	function get_key_request($key, $default = null)
	{
		return OX_Tools::_get_key($key, $default, $_REQUEST);
	}
	function get_post_key($key, $default = null)
	{
		return OX_Tools::_get_key($key, $default, $_POST);
	}
	function get_post_field($field, $default = null)
	{
		return OX_Tools::_get_field($field, $default, $_POST);
	}
	function _get_field($field, $default, $array)
	{
		$value = $array[$field];
		if (isset($array[$field])) {
			return OX_Tools::sanitize_field($array[$field]);
		}
		
		return $default;
	}
	function _get_key($key, $default, $array)
	{
		$value = $array[$key];
		if (isset($array[$key])) {
			return OX_Tools::sanitize_key($array[$key]);
		}
		
		return $default;
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
	function generate_name($base = null)
	{
		global $_advman;
		
		if (empty($base)) {
			$base = 'ad';
		}
		
		// Generate a unique name if no name was specified
		$unique = false;
		$i = 1;
		$name = $base;
		while (!$unique) {
			$unique = true;
			foreach ($_advman['ads'] as $ad) {
				if ($ad->name == $name) {
					$unique = false;
					break;
				}
			}
			if (!$unique) {
				$name = $base . '-' . $i++;
			}
		}
		
		return $name;
	}

	function validate_id($id)
	{
		global $_advman;
		$validId = false;
		
		if (is_numeric($id) && !empty($_advman['ads'][$id])) {
			$validId = $id;
		}
		
		return $validId;
	}
	
	function generate_id()
	{
		global $_advman;
		if (empty($_advman['next_ad_id'])) {
			$_advman['next_ad_id'] = 1;
		}
		
		$nextId = $_advman['next_ad_id'];
		$_advman['next_ad_id'] = $nextId + 1;
		
		return $nextId;
	}
}
?>