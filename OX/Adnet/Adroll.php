<?php
if(!ADVMAN_VERSION){die();}
require_once(ADS_PATH . '/OX/Adnet.php');	

$_adsensem_networks['OX_Adnet_Adroll'] = array(
	'www-create'	=>	'http://www.adroll.com/home',
	'www-signup'		=>	'http://www.adroll.com/tag/wordpress?r=ZPERWFQF25BGNG5EDWYBUV',
);

class OX_Adnet_Adroll extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Adroll';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = 'http://www.adroll.com';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'AdRoll';
	
	function OX_Adnet_Adroll()
	{
		$this->OX_Adnet();
	}
	
	function get_default_properties()
	{
		$properties = array(
			'slot' => '',
		);
		
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code){
		
		return (
			preg_match('/src="http:\/\/(\w*).adroll.com\/(\w*)\//', $code, $matches) !==0
		);
		
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match("/http:\/\/(\w*).adroll.com\/(\w*)\/(\w*)\/(\w*)/", $code, $matches)!=0) { 
			$this->p['account-id'] = $matches[3];
			$this->p['slot'] = $matches[4];
			$code = str_replace("http://{$matches[1]}.adroll.com/{$matches[2]}/{$matches[3]}/{$matches[4]}", "http://{$matches[1]}.adroll.com/{$matches[2]}/{{account-id}}/{{slot}}", $code);
		}
		
		$this->p['code'] = $code;
	}

	function _form_settings_help()
	{
	?><tr><td><p>Configuration is available through <a href="http://www.adroll.com/" target="_blank">Adroll's site</a>. Specific links to configure
			this ad unit are below:</p>
	<ul>
	<li><a href="http://www.adroll.com/private/publishers/adsensemanagernetwork/adspace/manage/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Manage Ad</a><br />
			Configure ad rotation and display settings.</li>
	<li><a href="http://www.adroll.com/private/publishers/adsensemanagernetwork/adspace/edit/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Edit Ad</a><br />
			Change dimensions, positioning and tags.</li>
	<li><a href="http://www.adroll.com/private/publishers/adsensemanagernetwork/adspace/adcode/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Get Ad Code</a><br />
			Get current ad code for this unit.</li>
	</ul></td></tr><?php
	}
}
/*
<!-- Start: Ads -->
<script type="text/javascript" src="http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/IPCY22UCBBFBVL6HIN6X2D/">
</script>
<!-- Start: Your Profile Link -->
<script type="text/javascript" src="http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/IPCY22UCBBFBVL6HIN6X2D/link">
</script>
*/
?>
