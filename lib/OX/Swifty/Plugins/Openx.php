<?php
require_once(OX_SWIFTY_PATH . '/Adnet.php');	
/*
$_advman_networks['OX_Adnet_Openx'] = array(
		'www-create' => 'http://www.openx.org/',
		'www-signup' => 'http://www.openx.org/'
		);
*/
class OX_Swifty_Plugins_Openx extends OX_Adnet
{
	var $mnemonic = 'Openx';
	var $network_name = 'OpenX';
	var $url = 'http://www.openx.org';
	
	function OX_Swifty_Plugins_Openx()
	{
		$this->OX_Adnet();
	}

	function get_network_property_defaults()
	{
		$properties = array(
			'slot' => '',
		);
		return $properties + parent::get_network_property_defaults();
	}
	
	function import_detect_network($code)
	{
		return (strpos($code, 'd1.openx.org') !== false);
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match("/zoneid=(\w*)/", $code, $matches) !=0) {
			$this->set_property('slot', $matches[1]);
			$code = str_replace('zoneid=' . $matches[1], 'zoneid={{slot}}', $code);
		}
		
		$code = str_replace('INSERT_RANDOM_NUMBER_HERE', '{{random}}', $code);
		
		$this->set_property('code', $code);
	}

	function customiseSection($mode, $section)
	{
		if ($section == 'adformat') {
			return true;
		}
		
		return false;
	}
	
	function displaySection($mode, $section)
	{
		return;
	}
	
	function displayBeforeSection($mode, $section)
	{
		if ($section == 'code') {
			$this->_displaySectionSlot();
		}
	}
}
/*
<script type='text/javascript'><!--//<![CDATA[
   document.MAX_ct0 ='%c';
   var m3_u = (location.protocol=='https:'?'https://d1.openx.org/ajs.php':'http://d1.openx.org/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
   document.write ("?zoneid=12099&amp;withtext=1&amp;blockcampaign=1");
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
   document.write ("&amp;loc=" + escape(window.location));
   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
   if (document.context) document.write ("&context=" + escape(document.context));
   if ((typeof(document.MAX_ct0) != 'undefined') && (document.MAX_ct0.substring(0,4) == 'http')) {
       document.write ("&amp;ct0=" + escape(document.MAX_ct0));
   }
   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
   document.write ("'><\/scr"+"ipt>");
//]]>--></script><noscript><a href='http://d1.openx.org/ck.php?n=a376a149&amp;cb=%n' target='_blank'><img src='http://d1.openx.org/avw.php?zoneid=12099&amp;n=a376a149&amp;ct0=%c' border='0' alt='' /></a></noscript>
*/
?>