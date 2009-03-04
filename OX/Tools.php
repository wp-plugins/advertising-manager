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
	
	function is_data_valid()
	{
		global $_advman;
		
		// If there is no data (or no readable data), create an initial array
		if (empty($_advman['version']) || !is_array($_advman['ads'])) {
			// Build an initial array
			$_advman = array();
			$_advman['ads'] = array();
			$_advman['next_ad_id'] = 1;
			$_advman['default-ad'] = '';
			$_advman['version'] = ADVMAN_VERSION;
			$_advman['uuid'] = $viewerId = md5(uniqid('', true));
			
			// If there is no Advertising Manager data, check to see if we can import from Adsense Deluxe
			$deluxe = get_option('acmetech_adsensedeluxe');
			if (is_array($deluxe)) {
				advman::add_notice('upgrade adsense-deluxe',__('<strong>Advertising Manager</strong> has detected a previous installation of <strong>Adsense Deluxe</strong>. Import settings?'),'yn');
			}
			
			update_option('plugin_adsensem', $_advman);
		}
		
		if (version_compare($_advman['version'], ADVMAN_VERSION, '<')) {
			include_once(ADVMAN_PATH . '/class-upgrade.php');
			
			//Backup cycle
			$backup = get_option('plugin_adsensem_backup');
			$backup[advman::major_version($_advman['version'])] = $_advman;
			update_option('plugin_adsensem_backup',$backup);
			unset($backup);
			
			advman_upgrade::go();
			update_option('plugin_adsensem', $_advman);
		}
		
		return true;
	}
	
	/**
	 * This function synchornises with the central server.  This will be used to pass ad deals to publishers if publisher choose to accept
	 */
	function sync()
	{
		global $_advman;
		global $user_login;
		
		// for testing...
//		$_advman['last-sync'] = 1235710700;
		
		if (empty($_advman['last-sync']) || (mktime(0,0,0) - $_advman['last-sync'] > 0) ) {
			// Update that we have already synched the server
			$_advman['last-sync'] = mktime(0,0,0);
			update_option('plugin_adsensem', $_advman);
			
			get_currentuserinfo();
			
			$params = array(
				'p' => 'advman',
				'i' => $_advman['uuid'],
				'v' => ADVMAN_VERSION,
				'e' => get_option('admin_email'),
				'u' => $user_login,
				's' => get_option('siteurl'),
			);
			
			$id = base64_encode(serialize($params));

			$url = 'http://code.openx.org/sync.php?id=' . $id;
//			$url = 'http://localhost:8888/wordpress.27/wp-content/plugins/advertising-manager/sync.php?XDEBUG_SESSION_START=' . time() . '&id=' . $id;
			$data = @file_get_contents($url);
//			$data = OX_Tools::post_url();
		}
	}
}
?>