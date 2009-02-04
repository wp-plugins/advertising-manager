<?php
if(!ADSENSEM_VERSION){die();}
require_once(ADS_PATH . '/OX/Adnet.php');	

$_adsensem_networks['OX_Adnet_Html']  = array(
	'www-signup'		=>	'',
);

class OX_Adnet_Html extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Html';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'HTML Code';
	
	function OX_Adnet_Html()
	{
		$this->OX_Adnet();
	}
	
	function get_default_properties()
	{
		$properties = array(
		);
		return $properties + parent::get_default_properties();
	}
	
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		//Attempt to find html width/height strings
		if(preg_match('/width="(\w*)"/', $code, $matches)!=0){ $width=$matches[1]; }
		if(preg_match('/height="(\w*)"/', $code, $matches)!=0){ $height=$matches[1]; }
		$this->p['adformat'] = $width . "x" . $height;
	}


	function _form_settings_help(){
	?>
			<p>Advertising Manager supports most Ad networks including <?php adsensem_admin::network_list(array('Ad_AdSenseAd','Ad_AdSenseReferral','Ad_Code')); ?>.</p>
			<p>Any networks not supported directly will be can be managed as HTML Code units. You can re-attempt import of code units at any time using the Import Options.</p>
	<?php
	}
}
?>
