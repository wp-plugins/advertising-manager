<?php
if(!ADVMAN_VERSION){die();}
require_once(ADVMAN_PATH . '/OX/Adnet.php');	

$_advman_networks['OX_Adnet_Ypn'] = array(
	//'www-create' => 'http://www.adbrite.com/zones/commerce/purchase.php?product_id_array=22',
	'www-signup'	=>	'http://ypn.yahoo.com/',														 
);

class OX_Adnet_Ypn extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Ypn';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = 'http://ypn.yahoo.com';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'Yahoo! Publisher Network';
	
	function OX_Adnet_Ypn()
	{
		$this->OX_Adnet();
	}
	
	function get_default_properties()
	{
		$properties = array(
			'adformat' => '250x250',
			'channel' => '',
			'color-bg' => 'FFFFFF',
			'color-border' => 'FFFFFF',
			'color-link'	=> '0000FF',
			'color-text' => '000000',
			'color-title' => '0000FF',
			'height' => '250',
			'url' => '',
			'width' => '250',
		);
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code)
	{
		return ( (strpos($code,'ypn-js.overture.com')!==false) );
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match('/ctxt_ad_partner( *)=( *)"(.*)"/', $code, $matches)) {
			$this->set('account-id', $matches[3]);
			$code = str_replace("ctxt_ad_partner{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "ctxt_ad_partner{$matches[1]}={$matches[2]}\"{{account-id}}\"", $code);
		}
		
		if (preg_match('/ctxt_ad_section( *)=( *)"(.*)"/', $code, $matches)){
			$this->set('channel', $matches[3]);
			$code = str_replace("ctxt_ad_section{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "ctxt_ad_section{$matches[1]}={$matches[2]}\"{{channel}}\"", $code);
		}

		if (preg_match('/ctxt_ad_bc( *)=( *)"(.*)"/', $code, $matches)) {
			$this->set('color-border', $matches[3]);
			$code = str_replace("ctxt_ad_bc{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "ctxt_ad_bc{$matches[1]}={$matches[2]}\"{{color-border}}\"", $code);
		}
		if (preg_match('/ctxt_ad_cc( *)=( *)"(.*)"/', $code, $matches)) {
			$this->set('color-bg', $matches[3]);
			$code = str_replace("ctxt_ad_cc{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "ctxt_ad_cc{$matches[1]}={$matches[2]}\"{{color-bg}}\"", $code);
		}
		if (preg_match('/ctxt_ad_lc( *)=( *)"(.*)"/', $code, $matches)) {
			$this->set('color-title', $matches[3]);
			$code = str_replace("ctxt_ad_lc{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "ctxt_ad_lc{$matches[1]}={$matches[2]}\"{{color-title}}\"", $code);
		}
		if (preg_match('/ctxt_ad_tc( *)=( *)"(.*)"/', $code, $matches)) {
			$this->set('color-text', $matches[3]);
			$code = str_replace("ctxt_ad_tc{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "ctxt_ad_tc{$matches[1]}={$matches[2]}\"{{color-text}}\"", $code);
		}
		if (preg_match('/ctxt_ad_uc( *)=( *)"(.*)"/', $code, $matches)) {
			$this->set('color-link', $matches[3]);
			$code = str_replace("ctxt_ad_uc{$matches[1]}={$matches[2]}\"{$matches[3]}\"", "ctxt_ad_uc{$matches[1]}={$matches[2]}\"{{color-link}}\"", $code);
		}
		
		$width = '';
		$height = '';
		if (preg_match('/ctxt_ad_width( *)=( *)(\d*)/', $code, $matches)) {
			$width = $matches[3];
			$code = str_replace("ctxt_ad_width{$matches[1]}={$matches[2]}{$matches[3]}", "ctxt_ad_width{$matches[1]}={$matches[2]}{{width}}", $code);
		}
		if (preg_match('/ctxt_ad_height( *)=( *)(\d*)/', $code, $matches)) {
			$height = $matches[3];
			$code = str_replace("ctxt_ad_height{$matches[1]}={$matches[2]}{$matches[3]}", "ctxt_ad_height{$matches[1]}={$matches[2]}{{height}}", $code);
		}
		if ($width != '') {
			$this->set('width', $width);
		}
		if ($height != '') {
			$this->set('height', $height);
		}
		if (($width != '') && ($height != '')) {
			$this->set('adformat', $width . 'x' . $height); //Only set if both width and height present
		}
		
		$this->set('code', $code);
	}
}
/*
<script language="JavaScript" type="text/javascript">
<!--
ctxt_ad_partner = "1433379257";
ctxt_ad_section = "";
ctxt_ad_bg = "";
ctxt_ad_width = 160;
ctxt_ad_height = 600;
ctxt_ad_bc = "A1A5A9";
ctxt_ad_cc = "FFFFFF";
ctxt_ad_lc = "0000DE";
ctxt_ad_tc = "737374";
ctxt_ad_uc = "439341";
// -->
</script>
<script language="JavaScript" src="http://ypn-js.overture.com/partner/js/ypn.js">
</script>

DESCRIPTION OF FIELDS
// necessarily set from the client:
// ctxt_ad_interface -- their interface on our servers
// ctxt_ad_width -- width of ads (iframe)
// ctxt_ad_height -- height of ads (iframe)
// ctxt_ad_partner -- partner tag for the request

// possibly set from the client:
// ctxt_ad_type -- type tag for the request
// ctxt_ad_url -- the url of the page on which
// the contextual ad is appearing
// ctxt_ad_url_cat -- if url (dynamic) is being used, this optional param
// will tell us to which category the result should be related
// ctxt_ad_market -- if present, market other than 'us'
// ctxt_ad_id -- if present, ctxtId to use for query
// ctxt_ad_keywords -- if present, ctxtKeywords to use for query
// ctxt_ad_frameborder -- frame around ads (iframe)
// ctxt_ad_newwin -- new window option
// ctxt_ad_sl -- sponsored listings
// ctxt_ad_cw -- if present, click wrapper
// ctxt_ad_css -- full url for css
// ctxt_ad_ie -- input encoding. default utf8 [TODO -- to be added]
// ctxt_ad_oe -- output encoding. default utf8 [TODO -- to be added]
// ctxt_ad_bc -- border color
// ctxt_ad_cc -- cell color
// ctxt_ad_lc -- link color
// ctxt_ad_tc -- text color
// ctxt_ad_uc -- url color 
*/
?>
