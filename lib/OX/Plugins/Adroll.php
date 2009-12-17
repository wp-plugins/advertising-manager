<?php
require_once(OX_LIB . '/Network.php');	

class OX_Plugin_Adroll extends OX_Network
{
	function OX_Plugin_Adroll()
	{
		$this->OX_Network();
		$this->name = 'Adroll';
		$this->short_name = 'adroll';
	}
	
	/**
	 * This function is called statically from the ad engine.  Use this function to put any hooks in the ad engine that you want to use.
	 */
	function register_plugin(&$engine)
	{
		$engine->add_action('ad_network', get_class());
	}
	
	function get_default_properties()
	{
		$properties = array(
			'account-id' => '',
			'slot' => '',
		);
		
		return $properties + parent::get_default_properties();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('728x90', '468x60', '234x60', '120x600', '160x600', '120x240', '336x280', '300x250', '250x250', '200x200', '180x150', '125x125'));
	}
	
	function import($code, &$ad)
	{
		$ad = false;
		
		if (preg_match('/src="http:\/\/(\w*).adroll.com\/(\w*)\//', $code, $matches) !==0 ) {
			
			$ad = OX_Ad::to_object();
			$ad->network_type = get_class();
			
			if (preg_match("/http:\/\/(\w*).adroll.com\/(\w*)\/(\w*)\/(\w*)/", $code, $matches)!=0) { 
				$ad->set_property('account-id', $matches[3]);
				$ad->set_property('slot', $matches[4]);
				$code = str_replace("http://{$matches[1]}.adroll.com/{$matches[2]}/{$matches[3]}/{$matches[4]}", "http://{$matches[1]}.adroll.com/{$matches[2]}/{{account-id}}/{{slot}}", $code);
			}
			
			$ad->set_property('code', $code);
		}
		
		return $ad;
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
