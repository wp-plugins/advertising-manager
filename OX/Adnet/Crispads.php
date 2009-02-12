<?php
if(!ADVMAN_VERSION){die();}
require_once(ADVMAN_PATH . '/OX/Adnet.php');	

$_adsensem_networks['OX_Adnet_Crispads']	= array(
		'www-create' => 'http://www.crispads.com/spinner/www/admin/zone-edit.php',
		'www-signup' => 'http://www.crispads.com/'
		);

class OX_Adnet_Crispads extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Crispads';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = 'http://www.crispads.com';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'Crisp Ads';
	
	function OX_Adnet_Crispads()
	{
		$this->OX_Adnet();
	}

	function get_default_properties()
	{
		$properties = array(
			'identifier' => '',
			'slot' => '',
		);
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code)
	{
		return (	preg_match('/http:\/\/www.crispads.com\/spinner\//', $code, $matches) !==0);
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match("/zoneid=(\w*)/", $code, $matches) !=0) {
			$this->set('slot', $matches[1]);
			$code = str_replace("zoneid={$matches[1]}", "zoneid={{slot}}", $code);
		}
		if (preg_match("/n=(\w*)/", $code, $matches)!=0) {
			$this->set('identifier', $matches[1]);
			$code = str_replace("n={$matches[1]}", "n={{identifier}}", $code);
		}
		
		//Only available on IFRAME ads
		if (preg_match('/width="(\w*)"/', $code, $matches) != 0) {
			$width = $matches[1]; 
			if (preg_match('/height="(\w*)"/', $code, $matches) != 0) {
				$height = $matches[1];
				$this->set('width', $width);
				$this->set('height', $height);
				$this->set('adformat', $width . 'x' . $height); //Only set if both width and height present
				$code = str_replace("width=\"{$width}\"", "width=\"{{width}}\"", $code);
				$code = str_replace("height=\"{$height}\"", "height=\"{{height}}\"", $code);
			}
		}
		
		$code = str_replace('INSERT_RANDOM_NUMBER_HERE', '{{random}}', $code);
		
		$this->set('code', $code);
	}
}
/*
// JAVASCRIPT
<script type="text/javascript"><!--//<![CDATA[
var m3_u = (location.protocol=='https:'?'https://www.crispads.com/spinner/www/delivery/ajs.php':'http://www.crispads.com/spinner/www/delivery/ajs.php');
var m3_r = Math.floor(Math.random()*99999999999);
if (!document.MAX_used) document.MAX_used = ',';
document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
document.write ("?zoneid=123");
document.write ('&amp;cb=' + m3_r);
if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
document.write ("&amp;loc=" + escape(window.location));
if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
if (document.context) document.write ("&context=" + escape(document.context));
if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
document.write ("\'><\/scr"+"ipt>");
//]]>--></script><noscript><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=a234567&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=123&amp;n=a234567" border="0" alt="" /></a></noscript>

//IFRAME
<iframe id="a234567" name="a234567" src="http://www.crispads.com/spinner/www/delivery/afr.php?n=a234567&amp;zoneid=123" framespacing="0" frameborder="no" scrolling="no" width="468" height="60"><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=a234567&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=123&amp;n=a234567" border="0" alt="" /></a></iframe>
<script type="text/javascript" src="http://www.crispads.com/spinner/www/delivery/ag.php"></script>
*/
?>
