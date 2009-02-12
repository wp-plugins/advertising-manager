<?php
if(!ADVMAN_VERSION){die();}
require_once(ADVMAN_PATH . '/OX/Adnet.php');	

$_adsensem_networks['OX_Adnet_Shoppingads'] = array(
		'www-create'	=>	'http://shoppingads.com/getcode/',
		'www-signup'	=>	'http://www.shoppingads.com/refer_1ebff04bf5805f6da1b4',
		 );

class OX_Adnet_Shoppingads extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Shoppingads';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = 'http://www.shoppingads.com';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'Shopping Ads';
	
	function OX_Adnet_Shoppingads()
	{
		$this->OX_Adnet();
	}
		
	function get_default_properties()
	{
		$properties = array(
			'adformat' => '250x250',
			'attitude' => 'cool',
			'campaign' => '',
			'color-bg' => 'FFFFFF',
			'color-border' => 'FFFFFF',
			'color-link' => '008000',
			'color-text' => '000000',
			'color-title' => '00A0E2',
			'height' => '250',
			'keywords' => '',
			'new-window' => 'no',
			'width' => '250',
		);
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code)
	{
		return ( strpos($code,'shoppingads_ad_client')!==false );
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match("/shoppingads_ad_client(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('account-id', $matches[4]);
			$code = str_replace("shoppingads_ad_client{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_client{$matches[1]}={$matches[2]}{$matches[3]}{{account-id}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_ad_campaign(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('campaign', $matches[4]);
			$code = str_replace("shoppingads_ad_campaign{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_campaign{$matches[1]}={$matches[2]}{$matches[3]}{{campaign}}{$matches[5]}", $code);
		}
		
		//Process dimensions and fake adformat (to auto-select from list when editing) (NO CUSTOM OPTIONS)
		if (preg_match("/shoppingads_ad_height(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$s1 = $matches[1];
			$s2 = $matches[2];
			$q1 = $matches[3];
			$height = $matches[4];
			$q2 = $matches[5];
			if (preg_match("/shoppingads_ad_width(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
				$width = $matches[4];
				$this->set('width', $width);
				$this->set('height', $height);
				$this->set('adformat', $width . 'x' . $height);
				$code = str_replace("shoppingads_ad_height{$s1}={$s2}{$q1}{$height}{$q2}", "shoppingads_ad_height{$s1}={$s2}{$q1}{{height}}{$q2}", $code);
				$code = str_replace("shoppingads_ad_width{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_width{$matches[1]}={$matches[2]}{$matches[3]}{{width}}{$matches[5]}", $code);
			}
		}
		
		if (preg_match("/shoppingads_ad_kw(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('keywords', $matches[4]);
			$code = str_replace("shoppingads_ad_kw{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_kw{$matches[1]}={$matches[2]}{$matches[3]}{{keywords}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_border(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('color-border', $matches[4]);
			$code = str_replace("shoppingads_color_border{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_border{$matches[1]}={$matches[2]}{$matches[3]}{{color-border}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_bg(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('color-bg', $matches[4]);
			$code = str_replace("shoppingads_color_bg{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_bg{$matches[1]}={$matches[2]}{$matches[3]}{{color-bg}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_heading(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('color-title', $matches[4]);
			$code = str_replace("shoppingads_color_heading{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_heading{$matches[1]}={$matches[2]}{$matches[3]}{{color-title}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_text(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('color-text', $matches[4]);
			$code = str_replace("shoppingads_color_text{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_text{$matches[1]}={$matches[2]}{$matches[3]}{{color-text}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_link(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('color-link', $matches[4]);
			$code = str_replace("shoppingads_color_link{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_link{$matches[1]}={$matches[2]}{$matches[3]}{{color-link}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_attitude(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('attitude', $matches[4]);
			$code = str_replace("shoppingads_attitude{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_attitude{$matches[1]}={$matches[2]}{$matches[3]}{{attitude}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_options(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set('new-window', ($matches[4]=='n') ? 'yes' : 'no');
			$code = str_replace("shoppingads_options{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_options{$matches[1]}={$matches[2]}{$matches[3]}{{new-window}}{$matches[5]}", $code);
		}
		
		$this->set('code', $code);
	}
}
/*
<script type="text/javascript"><!--' . "\n";
shoppingads_ad_client = 'myaccount';
shoppingads_ad_campaign = 'campaign';
shoppingads_ad_width = '468';
shoppingads_ad_height = '60';
shoppingads_ad_kw = 'keywords';
shoppingads_color_border = 'ccbbaa';
shoppingads_color_bg = 'aabbcc';
shoppingads_color_heading = '112233';
shoppingads_color_text = '226644';
shoppingads_color_link = '444466';
shoppingads_attitude = 'attitude';
shoppingads_options = "n";
--></script>
<script type="text/javascript" src="http://ads.shoppingads.com/pagead/show_sa_ads.js">
</script>
*/
?>
