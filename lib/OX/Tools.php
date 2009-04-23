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

	/**
	 * Get a template based on the class of an object
	 */
	function get_template($name, $class = null)
	{
		$className = null;
		
		if (is_object($class)) {
			$shortName = $ad->shortName;
			$template = OX_Admin_Wordpress::get_action('display_template_' . $name, get_class($class));
			
			if (file_exists($template[0])) {
				include_once($template[0]);
				$className = $template[1];
			}
		}
		if (empty($className)) {
			include_once(OX_TEMPLATE_PATH . "/{$name}.php");
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
	
	function sanitize($field, $type = null)
	{
		if (is_array($field)) {
			$a = array();
			foreach ($field as $name => $value) {
				$n = OX_Tools::sanitize($name, 'key');
				$v = OX_Tools::sanitize($value, $type);
				$a[$n] = $v;
			}
			return $a;
		}
		switch ($type) {
			case 'n' :
			case 'number' :
			case 'int' :
				return preg_replace('#[^0-9\.\-]#i', '', $field);
				break;
			case 'format' :
				return $field == 'custom' ? $field : preg_replace('#[^0-9x]#i', '', $field);
				break;
			case 'key' :
				return preg_replace('#[^a-z0-9-_]#i', '', $field);

			default :
				return stripslashes(str_replace("\0", '', $field));
				break;
		}
		
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
		global $advman_engine;
		$ads = $advman_engine->getAds();
		
		// Generate a unique name if no name was specified
		$unique = false;
		$i = 1;
		$name = $base;
		while (!$unique) {
			$unique = true;
			foreach ($ads as $ad) {
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
}
?>