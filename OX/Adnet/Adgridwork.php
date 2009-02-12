<?php
if(!ADVMAN_VERSION){die();}
require_once(ADVMAN_PATH . '/OX/Adnet.php');	

$_advman_networks['OX_Adnet_Adgridwork'] = array(
		'www-create' => 'http://www.adgridwork.com/u.php?page=submitsite',
		'www-signup'	=>	'http://www.adgridwork.com/?r=18501',														 
		 );

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class OX_Adnet_Adgridwork extends OX_Adnet
{
	/**
	 * The short name for any ad of this type, used when generating a unique name for the ad, or creating class files
	 */
	var $shortName = 'Adgridwork';
	
	/**
	 * The URL for the home page of the ad network site
	 */
	var $url = 'http://www.adgridwork.com';
	
	/**
	 * The name of the network.  Used when displaying ads by network.
	 */
	var $networkName = 'AdGridWork';
	
	function OX_Adnet_Adgridwork()
	{
		$this->OX_Adnet();
	}
		
	function get_default_properties()
	{
		$properties = array(
			'color-bg' 	=> 'FFFFFF',
			'color-border'=> '646360',
			'color-link' => 'FF0000',
			'color-text'	=> '646360',
			'color-title'	=> '000000',
			'slot' => '',
		);
		
		return $properties + parent::get_default_properties();
	}
	
	function import_detect_network($code)
	{
		
		return (
			(strpos($code,'www.adgridwork.com') !== false) ||
			(strpos($code,'www.mediagridwork.com/mx.js') !== false)
		);

	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		if (preg_match("/www\.adgridwork\.com\/\?r=(\d*)/", $code, $matches)) {
			$this->set('account-id', $matches[1]);
			$code = str_replace("www.adgridwork.com/?r={$matches[1]}", "www.adgridwork.com/?r={{account-id}}", $code);
		}
		
		if (preg_match('/var sid = \'(\w*)\'/', $code, $matches)) {
			$this->set('slot', $matches[1]);
			$code = str_replace("var sid = '{$matches[1]}'", "var sid = '{{slot}}'", $code);
		}
		
		if (preg_match('/style=\"color: #(\w*);/', $code, $matches)) {
			$this->set('color-link', $matches[1]);
			$code = str_replace("style=\"color: #{$matches[1]};", "style=\"color: #{{color-link}};", $code);
		}
		
		if (preg_match("/var title_color = '(\w*)'/", $code, $matches)) {
			$this->set('color-title', $matches[1]);
			$code = str_replace("var title_color = '{$matches[1]}'", "var title_color = '{{color-title}}'", $code);
		}
		
		if (preg_match("/var description_color = '(\w*)'/", $code, $matches)) {
			$this->set('color-text', $matches[1]);
			$code = str_replace("var description_color = '{$matches[1]}'", "var description_color = '{{color-text}}'", $code);
		}
		
		if (preg_match("/var link_color = '(\w*)'/", $code, $matches)) {
			$this->set('color-url', $matches[1]);
			$code = str_replace("var link_color = '{$matches[1]}'", "var link_color = '{{color-link}}'", $code);
		}
		
		if (preg_match("/var background_color = '(\w*)'/", $code, $matches)) {
			$this->set('color-bg', $matches[1]);
			$code = str_replace("var background_color = '{$matches[1]}'", "var background_color = '{{color-bg}}'", $code);
		}
		
		if (preg_match("/var border_color = '(\w*)'/", $code, $matches)) {
			$this->set('color-border', $matches[1]);
			$code = str_replace("var border_color = '{$matches[1]}'", "var border_color = '{{color-border}}'", $code);
		}
		
		$this->set('code', $code);
	}

	function _form_settings_help(){
	?><tr><td><p>Further configuration and control over channel and slot setup can be achieved through <a href="http://www.adgridwork.com/u.php" target="_blank">AdGridWorks's online system</a>:</p>
	<ul>
	<li><a href="http://www.adgridwork.com/u.php?page=metrics&sid=<?php echo $this->get('slot'); ?>" target="_blank">Campaign Metrics</a><br />
			View hits, clicks, and other stats information.</li>
	<li><a href="http://www.adgridwork.com/u.php?page=submitsite&sid=<?php echo $this->get('slot'); ?>" target="_blank">Edit Campaign</a><br />
			Change keywords, ad format and layout.</li>
	</ul></td></tr>
	<?php	
	}
}
/*
<a href="http://www.adgridwork.com/?r=18501" style="color: #ff3333; font-size: 14px" target="_blank">Free Advertising</a>
<script type="text/javascript">
var sid = '12';
var title_color = '333399';
var description_color = '00ff00';
var link_color = 'ff0000';
var background_color = 'ffff00';
var border_color = '999999';
</script><script type="text/javascript" src="http://www.mediagridwork.com/mx.js"></script>
*/
?>
