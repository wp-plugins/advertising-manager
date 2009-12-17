<?php
require_once(OX_LIB . '/Network.php');	

class OX_Plugin_Adify extends OX_Network
{
	function OX_Plugin_Adify()
	{
		$this->OX_Network();
		$this->name = 'Adify';
		$this->short_name = 'adify';
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
			'adformat' => '250x250',
			'color-bg' 	=> 'FFFFFF',
			'color-border'=> 'FFFFFF',
			'color-text'	=> '000000',
			'color-title'	=> '0000FF',
			'height' => '250',
			'slot' => '',
			'width' => '250',
		);
		
		return $properties + parent::get_default_properties();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('custom','728x90', '468x60', '120x600', '160x600', '300x250', '160x160'));
	}
	
	function import($code)
	{
		$ad = false;
		
		if (strpos($code,'sr_adspace_id') !== false) {
			
			$ad = OX_Ad::to_object();
			$ad->network_type = get_class();
			
			// Slot ID
			if (preg_match('/sr_adspace_id( *)=( *)(\d*);/', $code, $matches) != 0) {
				$ad->set_property('slot', $matches[3]);
				$code = str_replace("sr_adspace_id{$matches[1]}={$matches[2]}{$matches[3]}", "sr_adspace_id{$matches[1]}={$matches[2]}{{slot}}", $code);
				$code = str_replace("azId={$matches[3]}", "azId={{slot}}", $code);
				$code = str_replace("ID #{$matches[3]}", "ID #{{slot}}", $code);
			}
			
			// Width / Height
			$width = '';
			$height = '';
			if (preg_match('/sr_adspace_width( *)=( *)(\d*);/', $code, $matches) != 0) {
				$width = $matches[3]; 
				if ($width != '') {
					$ad->set_property('width', $width);
				}
				$code = str_replace("sr_adspace_width{$matches[1]}={$matches[2]}{$width}", "sr_adspace_width{$matches[1]}={$matches[2]}{{width}}", $code);
			}
			if (preg_match('/sr_adspace_height( *)=( *)(\d*);/', $code, $matches) != 0) {
				$height = $matches[3];
				if ($height != '') {
					$ad->set_property('height', $height);
				}
				$code = str_replace("sr_adspace_height{$matches[1]}={$matches[2]}{$height}", "sr_adspace_height{$matches[1]}={$matches[2]}{{height}}", $code);
			}
			if (($width != '') && ($height != '')) {
				$adformats = $this->get_ad_formats();
				$adformat = in_array("{$width}x{$height}", $adformats['all']) ? "{$width}x{$height}" : 'custom';
				$ad->set_property('adformat', $adformat);
			}
			
			$ad->set_property('code', $code);
		}
		
		return $ad;
	}
}
/*
<!-- Begin Adify tag for "Sidebar" Ad Space (160x160) ID #6471907 -->
<script type="text/javascript">
   sr_adspace_id = 6471907;
   sr_adspace_width = 160;
   sr_adspace_height = 160;
   sr_adspace_type = "graphic";
   sr_ad_new_window = true;
   
</script>
<script type="text/javascript" src="http://ad.afy11.net/srad.js?azId=6471907">
</script>
<!-- End Adify tag for "Sidebar" Ad Space (160x160) ID #6471907 -->
*/
?>
