<?php
if(!ADSENSEM_VERSION){die();}
require_once(ADS_PATH . '/OX/Adnet.php');	

$_adsensem_networks['OX_Adnet_Cj'] = array(
		'www-create' => 'https://members.cj.com/member/publisher/accounts/listmyadvertisers.do?sortKey=active_start_date&sortOrder=DESC',
		'www-signup'		=>	'http://www.qksrv.net/click-2335597-7282777',
	);

class OX_Adnet_Cj extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Cj';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = 'http://www.cj.com';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'Commission Junction';
	
	function OX_Adnet_Cj()
	{
		$this->OX_Adnet();
	}
	
	function render_ad()
	{
		$cjservers=array(
			'www.kqzyfj.com',
			'www.tkqlhce.com',
			'www.jdoqocy.com',
			'www.dpbolvw.net',
			'www.lduhtrp.net');
		
		$search[] = '{{xdomain}}';
		$replace[] = $cjservers[array_rand($cjservers)];
		
		return parent::render_ad();
	}
	
	function get_default_properties()
	{
		$properties = array(
			'adformat' => '250x250',
			'alt-text' => '',
			'height' => '250',
			'new-window' => 'no',
			'slot' => '',
			'status' => '',
			'width' => '250',
		);
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code)
	{
		# Domains: (add more)
		$domains = array(
		'www.commission-junction.com',
		'www.cj.com',
		'www.qksrv.net',
		'www.kqzyfj.com',
		'www.tkqlhce.com',
		'www.jdoqocy.com',
		'www.dpbolvw.net',
		'www.lduhtrp.net',
		'www.anrdoezrs.net');
		
		$match=false;
		foreach($domains as $d){$match=$match || (strpos($code,'href="http://' . $d)!==false);}
		return $match;
		
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match('/http:\/\/([.\w]*)\/click-(\d*)-(\d*)/', $code, $matches) != 0) { 
			$this->set_account_id($matches[2]);
			$this->p['slot'] = $matches[3]; 
			$code = str_replace("http://{$matches[1]}/click-{$matches[2]}-{$matches[3]}", "http://{{xdomain}}/click-{{account-id}}-{{slot}}", $code);
		}

		$a = $matches[2];
		$s = $matches[3];
		if (preg_match("/http:\/\/([.\w]*)\/image-{$a}-{$s}/", $code, $matches) != 0) { 
			$code = str_replace("http://{$matches[1]}/image-{$a}-{$s}", "http://{{xdomain}}/image-{{account-id}}-{{slot}}", $code);
		}
		
		if (preg_match("/onmouseover=\"window.status='([^']*)';return true;\"/", $code, $matches)) {
			$this->p['status'] = $matches[1];
			$code = str_replace("onmouseover=\"window.status='{$matches[1]}';return true;\"", "onmouseover=\"window.status='{{status}}';return true;\"", $code);
		}

		if (preg_match("/ alt=\"([^\"]*)\"/", $code, $matches)) {
			$this->p['alt-text'] = $matches[1];
			$code = str_replace(" alt=\"{$matches[1]}\"", " alt=\"{{alt-text}}\"", $code);
		}
		
		if ($v = strpos($code, " target=\"_blank\"")) {
			$this->p['new-window'] = 'yes';
			$code = str_replace(" target=\"_blank\"", "{{new-window}}", $code);
		}
		
		if (preg_match('/width="(\w*)"/', $code, $matches) != 0) {
			$width = $matches[1];
			if (preg_match('/height="(\w*)"/', $code, $matches) != 0) {
				$height = $matches[1];
				$this->p['width'] = $width;
				$this->p['height'] = $height;
				$this->p['adformat'] = $width . 'x' . $height; //Only set if both width and height present
				$code = str_replace("width=\"{$width}\"", "width=\"{{width}}\"", $code);
				$code = str_replace("height=\"{$height}\"", "height=\"{{height}}\"", $code);
			}
		}
		
		$this->p['code'] = $code;
	}
		
	function _form_settings_help()
	{
	?><tr><td><p>Further campaigns can be found through <a href="http://www.cj.com/" target="_blank">CJ's</a> site:</p>
	<ul>
	<li><a href="https://members.cj.com/member/publisher/accounts/listmyadvertisers.do?sortKey=active_start_date&sortOrder=DESC" target="_blank">Find Advertisers (By Relationship)</a><br />
			Find more ads from existing relationships.</li>
	<li><a href="https://members.cj.com/member/publisher/accounts/listmyadvertisers.do?sortKey=active_start_date&sortOrder=DESC" target="_blank">Find Advertisers (No Relationship)</a><br />
			Find ads from new advertisers.</li>
	<li><a href="https://members.cj.com/member/publisher/other/getlinkdetail.do?adId=<?php echo $this->p('slot');?>" target="_blank">View Ad Setup</a><br />
			View the online ad setup page for this ad.</li>
	</ul>	</td></tr>
	<tr><td><p>You can also view your <a href="https://www.google.com/adsense/report/overview" target="_blank">statistics and earnings</a> online.</p></td></tr>
	<?php	
	}
}
/*
<a href="http://www.tkqlhce.com/click-2619547-10495765" target="_blank" onmouseover="window.status='http://www.PayPerPost.com';return true;" onmouseout="window.status=' ';return true;">
<img src="http://www.lduhtrp.net/image-2619547-10495765" width="728" height="90" alt="Get Paid to Blog About the Things You Love" border="0"/></a>
*/
?>
