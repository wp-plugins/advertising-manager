<?php
require_once(OX_SWIFTY_PATH . '/Adnet.php');	
/*
$_advman_networks['OX_Adnet_Adroll'] = array(
	'www-create'	=>	'http://www.adroll.com/home',
	'www-signup'		=>	'http://www.adroll.com/tag/wordpress?r=ZPERWFQF25BGNG5EDWYBUV',
);
*/
class OX_Swifty_Plugins_Adroll extends OX_Adnet
{
	var $mnemonic = 'Adroll';
	var $network_name = 'AdRoll';
	var $url = 'http://www.adroll.com';
	
	function OX_Swifty_Plugins_Adroll()
	{
		$this->OX_Adnet();
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'slot' => '',
		);
		
		return $properties + parent::get_network_property_defaults();
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
			$this->set_property('account-id', $matches[3]);
			$this->set_property('slot', $matches[4]);
			$code = str_replace("http://{$matches[1]}.adroll.com/{$matches[2]}/{$matches[3]}/{$matches[4]}", "http://{$matches[1]}.adroll.com/{$matches[2]}/{{account-id}}/{{slot}}", $code);
		}
		
		$this->set_property('code', $code);
	}

	function _form_settings_help()
	{
	?><tr><td><p>Configuration is available through <a href="http://www.adroll.com/" target="_blank">Adroll's site</a>. Specific links to configure
			this ad unit are below:</p>
	<ul>
	<li><a href="http://www.adroll.com/private/publishers/advmananagernetwork/adspace/manage/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Manage Ad</a><br />
			Configure ad rotation and display settings.</li>
	<li><a href="http://www.adroll.com/private/publishers/advmananagernetwork/adspace/edit/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Edit Ad</a><br />
			Change dimensions, positioning and tags.</li>
	<li><a href="http://www.adroll.com/private/publishers/advmananagernetwork/adspace/adcode/IPCY22UCBBFBVL6HIN6X2D" target="_blank">Get Ad Code</a><br />
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
