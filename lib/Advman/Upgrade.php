<?php

include_once (ADVMAN_LIB . '/Admin.php');

class Advman_Upgrade
{
	// Upgrade the underlying data structure to the latest version
	function upgrade_advman(&$data)
	{
		$version = Advman_Upgrade::_get_version($data);
		Advman_Upgrade::_backup($data, $version);
		$versions = array('4.0');
		foreach ($versions as $v) {
			if (version_compare($version, $v, '<')) {
				call_user_func(array('Advman_Upgrade', 'advman_' . str_replace('.','_',$v)), $data);
			}
		}
		
		$data['settings']['version'] = ADVMAN_VERSION;
	}
	
	function upgrade_adsensem(&$data)
	{
		$version = Advman_Upgrade::_get_version($data);
		Advman_Upgrade::adsensem_upgrade_ad_classes($data);
		Advman_Upgrade::adsensem_upgrade_ad_ids($data);
		Advman_Upgrade::adsensem_upgrade_ad_settings($data);
		Advman_Upgrade::adsensem_upgrade_network_classes($data);
		Advman_Upgrade::adsensem_upgrade_network_settings($data);
		Advman_Upgrade::adsensem_upgrade_settings($data);
		$notice = __('<strong>Advertising Manager</strong> has been upgraded from your <strong>Adsense Manager</strong> settings.', 'advman');
//		$question = __('Enable <a>auto optimisation</a>? (RECOMMENDED)', 'advman');
//		$question = str_replace('<a>', '<a href="http://code.openx.org/wiki/advertising-manager/Auto_Optimization" target="_new">', $question);
		Advman_Admin::add_notice('optimise', $notice, 'ok');

		// Set the new version
		$data['settings']['version'] = '3.3.11';

		return Advman_Upgrade::upgrade_advman($data);
	}
	
	function _get_version(&$data)
	{
		$version = $data['settings']['version'];
		if (empty($version)) {
			$version = $data['version'];
			if ($version == 'ADVMAN_VERSION') {
				$version = '3.3.4';
			}
			unset ($data['version']);
			$data['settings']['version'] = $version;
		}
		return $version;
	}
	
	function _backup($data, $version)
	{
		$backup = get_option('plugin_advman_backup');
		if (empty($backup)) {
			$backup = get_option('plugin_adsensem_backup');
			delete_option('plugin_adsensem_backup');
		}
		
		$backup[$version] = $data;
		update_option('plugin_advman_backup', $backup);
	}
		
	function advman_4_0(&$data)
	{
		$data['networks'] = $data['defaults'];
		unset($data['defaults']);
		$data['settings']['publisher-id'] = $data['uuid'];
		unset($data['uuid']);
		$data['settings']['last-sync'] = $data['last-sync'];
		unset($data['last-sync']);
	}
	function adsensem_upgrade_ad_classes(&$data)
	{
		$adnets = array(
			'ad_adbrite' => 'OX_Plugin_Adbrite',
			'ad_adgridwork' => 'OX_Plugin_Adgridwork',
			'ad_adpinion' => 'OX_Plugin_Adpinion',
			'ad_adroll' => 'OX_Plugin_Adroll',
			'ad_adsense' => 'OX_Plugin_Adsense',
			'ad_adsense_ad' => 'OX_Plugin_Adsense',
			'ad_adsense_classic' => 'OX_Plugin_Adsense',
			'ad_adsense_link' => 'OX_Plugin_Adsense',
			'ad_adsense_referral' => 'OX_Plugin_Adsense',
			'ad_cj' => 'OX_Plugin_Cj',
			'ad_code' => 'OX_Plugin_Html',
			'ad_crispads' => 'OX_Plugin_Crispads',
			'ad_openx_adserver' => 'OX_Plugin_Openx',
			'ad_shoppingads' => 'OX_Plugin_Shoppingads',
			'ad_widgetbucks' => 'OX_Plugin_Widgetbucks',
			'ad_ypn' => 'OX_Plugin_Ypn',
			'ox_adnet_adbrite' => 'OX_Plugin_Adbrite',
			'ox_adnet_adgridwork' => 'OX_Plugin_Adgridwork',
			'ox_adnet_adify' => 'OX_Plugin_Adify',
			'ox_adnet_adpinion' => 'OX_Plugin_Adpinion',
			'ox_adnet_adroll' => 'OX_Plugin_Adroll',
			'ox_adnet_adsense' => 'OX_Plugin_Adsense',
			'ox_adnet_chitika' => 'OX_Plugin_Chitika',
			'ox_adnet_cj' => 'OX_Plugin_Cj',
			'ox_adnet_crispads' => 'OX_Plugin_Crispads',
			'ox_adnet_openx' => 'OX_Plugin_Openx',
			'ox_adnet_shoppingads' => 'OX_Plugin_Shoppingads',
			'ox_adnet_widgetbucks' => 'OX_Plugin_Widgetbucks',
			'ox_adnet_ypn' => 'OX_Plugin_Ypn',
		);
		
		$aAds = array();
		foreach ($data['ads'] as $n => $ad) {
			$aAd = array();
			if (get_class($ad) != '__PHP_Incomplete_Class') {
				$aAd['network'] = $ad->network;
			}
			foreach ($ad as $key => $value) {
				if ($key == '__PHP_Incomplete_Class_Name') {
					$aAd['network'] = $adnets[strtolower($value)];
				} else {
					$aAd[$key] = $value;
				}
			}
			$aAds[$n] = $aAd;
		}
		
		$data['ads'] = $aAds;
	}
	
	function adsensem_upgrade_network_classes(&$data)
	{
		$adnets = array(
			'ad_adbrite' => 'adbrite',
			'ad_adgridwork' => 'adgridwork',
			'ad_adpinion' => 'adpinion',
			'ad_adroll' => 'adroll',
			'ad_adsense' => 'adsense',
			'ad_adsense_ad' => 'adsense',
			'ad_adsense_classic' => 'adsense',
			'ad_adsense_link' => 'adsense',
			'ad_adsense_referral' => 'adsense',
			'ad_cj' => 'cj',
			'ad_code' => 'html',
			'ad_crispads' => 'crispads',
			'ad_openx_adserver' => 'openx',
			'ad_shoppingads' => 'shoppingads',
			'ad_widgetbucks' => 'widgetbucks',
			'ad_ypn' => 'ypn',
			'ox_adnet_adbrite' => 'adbrite',
			'ox_adnet_adgridwork' => 'adgridwork',
			'ox_adnet_adify' => 'adify',
			'ox_adnet_adpinion' => 'adpinion',
			'ox_adnet_adroll' => 'adroll',
			'ox_adnet_adsense' => 'adsense',
			'ox_adnet_chitika' => 'chitika',
			'ox_adnet_cj' => 'cj',
			'ox_adnet_crispads' => 'crispads',
			'ox_adnet_openx' => 'openx',
			'ox_adnet_shoppingads' => 'shoppingads',
			'ox_adnet_widgetbucks' => 'widgetbucks',
			'ox_adnet_ypn' => 'ypn',
			'ox_ad_adbrite' => 'adbrite',
			'ox_ad_adgridwork' => 'adgridwork',
			'ox_ad_adify' => 'adify',
			'ox_ad_adpinion' => 'adpinion',
			'ox_ad_adroll' => 'adroll',
			'ox_ad_adsense' => 'adsense',
			'ox_ad_chitika' => 'chitika',
			'ox_ad_cj' => 'cj',
			'ox_ad_crispads' => 'crispads',
			'ox_ad_openx' => 'openx',
			'ox_ad_shoppingads' => 'shoppingads',
			'ox_ad_widgetbucks' => 'widgetbucks',
			'ox_ad_ypn' => 'ypn',
		);

		$aNws = array();
		foreach ($data['defaults'] as $c => $network) {
			$newclass = in_array($c, $adnets) ? $c : $adnets[strtolower($c)];
			$aNws[$newclass] = $network;
		}
		$data['defaults'] = $aNws;
		
		foreach ($data['account-ids'] as $c => $accountId) {
			$newclass = in_array($c, $adnets) ? $c : $adnets[strtolower($c)];
			foreach ($data['ads'] as $id => $ad) {
				if ($ad['network'] = 'adsense' && empty($ad['account-id'])) {
					$data['ads'][$id]['account-id'] = $accountId;
				}
			}
		}
		unset($data['account-ids']);
		
		if (isset($data['adsense-account'])) {
			$accountId = $data['adsense-account'];
			foreach ($data['ads'] as $id => $ad) {
				if ($ad['network'] = strtolower($newclass) && empty($ad['account-id'])) {
					$data['ads'][$id]['account-id'] = $accountId;
				}
			}
		}
		unset($data['adsense-account']);
	}
	function adsensem_upgrade_ad_ids(&$data)
	{
		$ads = array();
		$nextId = 1;
		foreach ($data['ads'] as $n => $ad) {
			if (is_numeric($n) && $nextId <= $n) {
				$nextId = $n + 1;
			}
		}
		foreach ($data['ads'] as $n => $ad) {
			if (is_numeric($n)) {
				$ads[$n] = $ad;
			} else {
				$ad['name'] = $n;
				$ads[$nextId++] = $ad;
			}
		}
		
		$data['ads'] = $ads;
		$data['settings']['next_ad_id'] = $nextId;
		unset($data['next_ad_id']);  // old way of storing next ad id
	}
	
	function adsensem_upgrade_ad_settings(&$data)
	{
		$ads = array();
		foreach ($data['ads'] as $id => $ad) {
			$ad['id'] = $id;
			// set the properties
			if (isset($ad['p'])) {
				foreach ($ad['p'] as $n => $v) {
					$ad[$n] = $v;
				}
				unset($ad['p']);
			}
			if (!isset($ad['name'])) {
				$ad['name'] = OX_Tools::generate_name('ad');
			}
			// remove title
			if (isset($ad['title'])) {
				unset($ad['title']);
			}
			// Make sure that any settings under 'color-url' are now under 'color-link'
			if (!empty($ad['color-url']) && empty($ad['color-link'])) {
				$ad['color-link'] = $ad['color-url'];
				unset($ad['color-url']);
			}
			// Set the OpenX Market
			if (!isset($ad['openx-market'])) {
				$ad['openx-market'] = false;
			}
			// Set the OpenX Market CPM
			if (!isset($ad['openx-market-cpm'])) {
				$ad['openx-market-cpm'] = '0.20';
			}
			// Set the Weight
			if (!isset($ad['weight'])) {
				$ad['weight'] = '1';
			}
			// Changed the 'hide link url' field to 'status' (for cj ads)
			if (isset($ad['hide-link-url'])) {
				$ad['status'] = $ad['hide-link-url'];
				unset($ad['hide-link-url']);
			}
			// remove codemethod
			if (isset($ad['codemethod'])) {
				unset($ad['codemethod']);
			}
			
			// Get rid of the 'default_ad' field (should be 'default-ad')
			if (isset($ad['default_ad'])) {
				unset($ad['default_ad']);
			}
			
			if (!isset($ad['openx-sync'])) {
				$ad['opnex-sync'] = true;
			}
			if (!isset($ad['uuid'])) {
				$ad['uid'] = md5(uniqid('', true));
			}
			// Make sure width and height are correct
			if (empty($ad['width']) || empty($ad['height'])) {
				$format = $ad['adformat'];
				if ( !empty($format) && ($format != 'custom')) {
					list($width, $height, $null) = split('[x]', $format);
					$ad['width'] = $width;
					$ad['height'] = $height;
				}
			}
			
			$ads[$id] = $ad;
		}
		
		$data['ads'] = $ads;
	}
	
	function adsensem_upgrade_network_settings(&$data)
	{
		foreach ($data['defaults'] as $c => $network) {
			if (!isset($network['counter'])) {
				$data['defaults'][$c]['counter'] = ($c == 'OX_Ad_Adsense') ? '3' : '';
			}
			if (!isset($network['openx-market'])) {
				$data['defaults'][$c]['openx-market'] = 'no';
			}
			// Set OpenX Market eCPM
			if (!isset($network['openx-market-cpm'])) {
				$data['defaults'][$c]['openx-market-cpm'] = '0.20';
			}
			// Set Weight
			if (!isset($network['weight'])) {
				$data['defaults'][$c]['weight'] = '1';
			}
			// Show only to an Author
			if (!isset($network['show-author'])) {
				$data['defaults'][$c]['show-author'] = 'all';
			}
			if (!isset($network['color-border']) && isset($network['colors']['border'])) {
				$data['defaults'][$c]['color-border'] = $network['colors']['border'];
			}
			if (!isset($network['color-title']) && isset($network['colors']['title'])) {
				$data['defaults'][$c]['color-title'] = $network['colors']['title'];
			}
			if (!isset($network['color-bg']) && isset($network['colors']['bg'])) {
				$data['defaults'][$c]['color-bg'] = $network['colors']['bg'];
			}
			if (!isset($network['color-text']) && isset($network['colors']['text'])) {
				$data['defaults'][$c]['color-text'] = $network['colors']['text'];
			}
			if (!isset($network['color-link']) && isset($network['colors']['url'])) {
				$data['defaults'][$c]['color-link'] = $network['colors']['url'];
			}
			
			if (!isset($network['show-page']) && isset($network['show-post'])) {
				$data['defaults'][$c]['show-page'] = $network['show-post'];
			}
			if (!isset($network['adformat']) && isset($network['linkformat'])) {
				$data['defaults'][$c]['adformat'] = $network['linkformat'];
			}
			if (!isset($network['adformat']) && isset($network['referralformat'])) {
				$data['defaults'][$c]['adformat'] = $network['referralformat'];
			}
			// Set height and width for an ad format
			if (!empty($network['adformat']) && ($network['adformat'] != 'custom')) {
				list($width, $height) = split('[x]', $network['adformat']);
				if (is_numeric($width)) {
					$data['defaults'][$c]['width'] = $width;
				}
				if (is_numeric($height)) {
					$data['defaults'][$c]['height'] = $height;
				}
			}
			
			
			unset($data['defaults'][$c]['colors']);
			unset($data['defaults'][$c]['product']);
		}
	}
	function adsensem_upgrade_settings(&$data)
	{
		// Be nice does not exist anymore
		if (isset($data['be-nice'])) {
			unset($data['be-nice']);
		}
		if (isset($data['benice'])) {
			unset($data['benice']);
		}
		// Reset ad ids just in case
		$nextId = 1;
		foreach ($data['ads'] as $id => $ad) {
			if ($id > $nextId) {
				$nextId = $id;
			}
		}
		$data['settings']['next_ad_id'] = $nextId + 1;
		
		if (isset($data['defaults']['ad'])) {
			if (!isset($data['default-ad'])) {
				$data['default-ad'] = $data['defaults']['ad'];
			}
			unset($data['defaults']['ad']);
		}
		if (isset($data['default-ad'])) {
			$data['settings']['default-ad'] = $data['default-ad'];
			unset($data['default-ad']);
		}
	}
	
	
	function _display_adsense($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? ('pub-' . $_advman['account-ids'][$ad->network]) : '';

		$code = '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $accountId . '";' . "\n";
		$code.= 'google_ad_slot = "' . str_pad($ad->get_property('slot'),10,'0',STR_PAD_LEFT) . '"' . ";\n"; //String padding to max 10 char slot ID
		
		if($ad->get_property('adtype')=='ref_text'){
			$code.= 'google_ad_output = "textlink"' . ";\n";
			$code.= 'google_ad_format = "ref_text"' . ";\n";
			$code.= 'google_cpa_choice = ""' . ";\n";
		} else if($ad->get_property('adtype')=='ref_image'){
			$code.= 'google_ad_width = ' . $ad->get_property('width') . ";\n";
			$code.= 'google_ad_height = ' . $ad->get_property('height') . ";\n";
			$code.= 'google_cpa_choice = ""' . ";\n";
		} else {
			$code.= 'google_ad_width = ' . $ad->get_property('width') . ";\n";
			$code.= 'google_ad_height = ' . $ad->get_property('height') . ";\n";
		}
		
		$code.= '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";
		
		return $code;
	}
	
	function _display_adbrite($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? $_advman['account-ids'][$ad->network] : '';

		$code ='<!-- Begin: AdBrite -->';
		$code .= '<script type="text/javascript">' . "\n";
		$code .= "var AdBrite_Title_Color = '" . $ad->get_property('color-title') . "'\n";
		$code .= "var AdBrite_Text_Color = '" . $ad->get_property('color-text') . "'\n";
		$code .= "var AdBrite_Background_Color = '" . $ad->get_property('color-bg') . "'\n";
		$code .= "var AdBrite_Border_Color = '" . $ad->get_property('color-border') . "'\n";
		$code .= '</script>' . "\n";
	   	$code .= '<script src="http://ads.adbrite.com/mb/text_group.php?sid=' . $ad->get_property('slot') . '&zs=' . $accountId . '" type="text/javascript"></script>';
		$code .= '<div><a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=' . $ad->get_property('slot') . '&afsid=1" style="font-weight:bold;font-family:Arial;font-size:13px;">Your Ad Here</a></div>';
		$code .= '<!-- End: AdBrite -->';
		
		return $code;
	}
	
	function _display_adgridwork($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? $_advman['account-ids'][$ad->network] : '';

		$code ='<a href="http://www.adgridwork.com/?r=' . $accountId . '" style="color: #' . $ad->get_property('color-link') .  '; font-size: 14px" target="_blank">Free Advertising</a>';
		$code.='<script type="text/javascript">' . "\n";
		$code.="var sid = '"  . $ad->get_property('slot') . "';\n";
		$code.="var title_color = '" . $ad->get_property('color-title') . "';\n";
		$code.="var description_color = '" . $ad->get_property('color-text') . "';\n";
		$code.="var link_color = '" . $ad->get_property('color-link') . "';\n";
		$code.="var background_color = '" . $ad->get_property('color-bg') . "';\n";
		$code.="var border_color = '" . $ad->get_property('color-border') . "';\n";
		$code.='</script><script type="text/javascript" src="http://www.mediagridwork.com/mx.js"></script>';
		
		return $code;
	}
	
	function _display_adpinion($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? $_advman['account-ids'][$ad->network] : '';

		if($ad->get_property('width')>$ad->get('height')){$xwidth=18;$xheight=17;} else {$xwidth=0;$xheight=35;}
		$code ='';
	 	$code .= '<iframe src="http://www.adpinion.com/app/adpinion_frame?website=' . $accountId . '&amp;width=' . $ad->get_property('width') . '&amp;height=' . $ad->get('height') . '" ';
		$code .= 'id="adframe" style="width:' . ($ad->get_property('width')+$xwidth) . 'px;height:' . ($ad->get('height')+$xheight) . 'px;" scrolling="no" frameborder="0">.</iframe>';
	
		return $code;
	}
	
	function _display_adroll($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? $_advman['account-ids'][$ad->network] : '';

		$code ='';
		$code .= '<!-- Start: Adroll Ads -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $accountId . '/' . $ad->get_property('slot') . '/">';
		$code .= '</script>';
		$code .= '<!-- Start: Adroll Profile Link -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $accountId . '/' . $ad->get_property('slot') . '/link">';
		$code .= '</script>';
	
		return $code;
	}
	
	function _display_adsense_ad($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? ('pub-' . $_advman['account-ids'][$ad->network]) : '';

		$code='';
		
		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $accountId . '";' . "\n";
				
		if($ad->get_property('channel')!==''){ $code.= 'google_ad_channel = "' . $ad->get('channel') . '";' . "\n"; }
		if($ad->get_property('uistyle')!==''){ $code.= 'google_ui_features = "rc:' . $ad->get('uistyle') . '";' . "\n"; }
				
		$code.= 'google_ad_width = ' . $ad->get_property('width') . ";\n";
		$code.= 'google_ad_height = ' . $ad->get_property('height') . ";\n";
				
		$code.= 'google_ad_format = "' . $ad->get_property('adformat') . '_as"' . ";\n";
		$code.= 'google_ad_type = "' . $ad->get_property('adtype') . '"' . ";\n";

		switch ($ad->get_property('alternate-ad')) {
			case 'url'		: $code.= 'google_alternate_ad_url = "' . $ad->get_property('alternate-url') . '";' . "\n"; break;
			case 'color'	: $code.= 'google_alternate_ad_color = "' . $ad->get_property('alternate-color') . '";' . "\n"; break;
			case ''				: break;
			default				:
				$alternateAd = $ad->get_property('alternate-ad');
				if (!empty($alternateAd)) {
					$code.= 'google_alternate_ad_url = "' . get_bloginfo('wpurl') . '/?advman-ad-name=' . $alternateAd . '";'  . "\n";
				}
		}
		
		$code.= 'google_color_border = "' . $ad->get_property('color-border') . '"' . ";\n";
		$code.= 'google_color_bg = "' . $ad->get_property('color-bg') . '"' . ";\n";
		$code.= 'google_color_link = "' . $ad->get_property('color-title') . '"' . ";\n";
		$code.= 'google_color_text = "' . $ad->get_property('color-text') . '"' . ";\n";
		$code.= 'google_color_url = "' . $ad->get_property('color-link') . '"' . ";\n";
		
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _display_adsense_link($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? ('pub-' . $_advman['account-ids'][$ad->network]) : '';

		$code='';

		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $accountId . '";' . "\n";
					
		if($ad->get_property('channel')!==''){ $code.= 'google_ad_channel = "' . $ad->get('channel') . '";' . "\n"; }
		if($ad->get_property('uistyle')!==''){ $code.= 'google_ui_features = "rc:' . $ad->get('uistyle') . '";' . "\n"; }
					
		$code.= 'google_ad_width = ' . $ad->get_property('width') . ";\n";
		$code.= 'google_ad_height = ' . $ad->get_property('height') . ";\n";
					
		$code.= 'google_ad_format = "' . $ad->get_property('adformat') . $ad->get('adtype') . '"' . ";\n"; 

		//$code.=$ad->_render_alternate_ad_code();
		$code.= 'google_color_border = "' . $ad->get_property('color-border') . '"' . ";\n";
		$code.= 'google_color_bg = "' . $ad->get_property('color-bg') . '"' . ";\n";
		$code.= 'google_color_link = "' . $ad->get_property('color-title') . '"' . ";\n";
		$code.= 'google_color_text = "' . $ad->get_property('color-text') . '"' . ";\n";
		$code.= 'google_color_url = "' . $ad->get_property('color-link') . '"' . ";\n";
			
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _display_adsense_referral($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? ('pub-' . $_advman['account-ids'][$ad->network]) : '';

		//if($ad===false){$ad=$_advman['ads'][$_advman['default_ad']];}
		//$ad=advman::merge_defaults($ad); //Apply defaults
		if($ad->get_property('product')=='referral-image') {
			$format = $ad->get_property('adformat') . '_as_rimg';
		} else if($ad->get_property('product')=='referral-text') {
			$format = 'ref_text';
		}				
		$code='';

	
		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "' . $accountId . '";' . "\n";
		
		if($ad->get_property('channel')!==''){ $code.= 'google_ad_channel = "' . $ad->get('channel') . '";' . "\n"; }
		
		if($ad->get_property('product')=='referral-image'){
			$code.= 'google_ad_width = ' . $ad->get_property('width') . ";\n";
			$code.= 'google_ad_height = ' . $ad->get_property('height') . ";\n";
		}
		
		if($ad->get_property('product')=='referral-text'){$code.='google_ad_output = "textlink"' . ";\n";}
		$code.='google_cpa_choice = "' . $ad->get_property('referral') . '"' . ";\n";
		
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _display_cj($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? $_advman['account-ids'][$ad->network] : '';

		$cjservers=array(
			'www.kqzyfj.com',
			'www.tkqlhce.com',
			'www.jdoqocy.com',
			'www.dpbolvw.net',
			'www.lduhtrp.net');
		
		$code = '';
		$code .= '<!-- Start: CJ Ads -->';
	 	$code .= '<a href="http://' . $cjservers[array_rand($cjservers)] . '/click-' . $accountId . '-' . $ad->get_property('slot') . '"';
		if($ad->get_property('new-window')=='yes'){$code.=' target="_blank" ';}
		
		if($ad->get_property('hide-link')=='yes'){
			$code.='onmouseover="window.status=\'';
			$code.=$ad->get_property('hide-link-url');
			$code.='\';return true;" onmouseout="window.status=\' \';return true;"';
		}
		
		$code .= '>';
		
		$code .= '<img src="http://' . $cjservers[array_rand($cjservers)] . '/image-' . $accountId . '-' . $ad->get_property('slot') . '"';
		$code .= ' width="' . $ad->get_property('width') . '" ';
		$code .= ' height="' . $ad->get_property('height') . '" ';
		$code .= ' alt="' . $ad->get_property('alt-text') . '" ';
		$code .= '>';
		$code .= '</a>';
	
		return $code;
	}
	
	function _display_crispads($ad)
	{
		global $_advman;

		if ($ad->get_property('codemethod')=='javascript'){
			$code='<script type="text/javascript"><!--//<![CDATA[' . "\n";
			$code.="var m3_u = (location.protocol=='https:'?'https://www.crispads.com/spinner/www/delivery/ajs.php':'http://www.crispads.com/spinner/www/delivery/ajs.php');\n";
			$code.="var m3_r = Math.floor(Math.random()*99999999999);\n";
			$code.="if (!document.MAX_used) document.MAX_used = ',';\n";
			$code.="document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);\n";
			$code.='document.write ("?zoneid=' . $ad->get_property('slot') . '");' . "\n";
			$code.="document.write ('&amp;cb=' + m3_r);\n";
			$code.="if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);\n";
	   		$code.='document.write ("&amp;loc=" + escape(window.location));' . "\n";
			$code.='if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));' . "\n";
			$code.='if (document.context) document.write ("&context=" + escape(document.context));' . "\n";
			$code.='if (document.mmm_fo) document.write ("&amp;mmm_fo=1");' . "\n";
			$code.='document.write ("\'><\/scr"+"ipt>");' . "\n";
			$code.='//]]>--></script><noscript><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $ad->get_property('identifier') . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $ad->get('slot') . '&amp;n=' . $ad->get('identifier') . '" border="0" alt="" /></a></noscript>';
		} else { //Iframe
			$code='<iframe id="' . $ad->get_property('identifier') . '" name="' . $ad->get('identifier') . '" src="http://www.crispads.com/spinner/www/delivery/afr.php?n=' . $ad->get('identifier') . '&amp;zoneid=' . $ad->get('slot') . '" framespacing="0" frameborder="no" scrolling="no" width="' . $ad->get('width') . '" height="' . $ad->get('height') . '"><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $ad->get('identifier') . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $ad->get('slot') . '&amp;n=' . $ad->get('identifier') . '" border="0" alt="" /></a></iframe>';
			$code.='<script type="text/javascript" src="http://www.crispads.com/spinner/www/delivery/ag.php"></script>';
		}
		
		return $code;
	}
	function _display_shoppingads($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? $_advman['account-ids'][$ad->network] : '';

		$code = '<script type="text/javascript"><!--' . "\n";
		$code.= 'shoppingads_ad_client = "' . $accountId . '";' . "\n";
		$code.= 'shoppingads_ad_campaign = "' . $ad->get_property('campaign') . '";' . "\n";

		list($width,$height)=split('[x]',$ad->get_property('adformat'));
		$code.= 'shoppingads_ad_width = "' . $width . '";' . "\n";
		$code.= 'shoppingads_ad_height = "' . $height . '";' . "\n";

		$code.= 'shoppingads_ad_kw = "' . $ad->get_property('keywords') . '";' . "\n";

		$code.= 'shoppingads_color_border = "' . $ad->get_property('color-border') . '";' . "\n";
		$code.= 'shoppingads_color_bg = "' . $ad->get_property('color-bg') . '";' . "\n";
		$code.= 'shoppingads_color_heading = "' . $ad->get_property('color-title') . '";' . "\n";
		$code.= 'shoppingads_color_text = "' . $ad->get_property('color-text') . '";' . "\n";
		$code.= 'shoppingads_color_link = "' . $ad->get_property('color-link') . '";' . "\n";

		$code.= 'shoppingads_attitude = "' . $ad->get_property('attitude') . '";' . "\n";
		if($ad->get_property('new-window')=='yes'){$code.= 'shoppingads_options = "n";' . "\n";}

		$code.= '--></script>
		<script type="text/javascript" src="http://ads.shoppingads.com/pagead/show_sa_ads.js">
		</script>' . "\n";
		
		return $code;
	}
	
	function _display_widgetbucks($ad)
	{
		global $_advman;

		$code ='';
		$code .= '<!-- START CUSTOM WIDGETBUCKS CODE --><div>';
		$code .= '<script src="http://api.widgetbucks.com/script/ads.js?uid=' . $ad->get_property('slot') . '"></script>'; 
		$code .= '</div><!-- END CUSTOM WIDGETBUCKS CODE -->';
		return $code;
	}
	
	function _display_ypn($ad)
	{
		global $_advman;
		$accountId = !empty($_advman['account-ids'][$ad->network]) ? $_advman['account-ids'][$ad->network] : '';

		$code = '<script language="JavaScript">';
		$code .= '<!--';
		$code .= 'ctxt_ad_partner = "' . $accountId . '";' . "\n";
		$code .= 'ctxt_ad_section = "' . $ad->get_property('channel') . '";' . "\n";
		$code .= 'ctxt_ad_bg = "";' . "\n";
		$code .= 'ctxt_ad_width = "' . $ad->get_property('width') . '";' . "\n";
		$code .= 'ctxt_ad_height = "' . $ad->get_property('height') . '";' . "\n";
		
		$code .= 'ctxt_ad_bc = "' . $ad->get_property('color-bg') . '";' . "\n";
		$code .= 'ctxt_ad_cc = "' . $ad->get_property('color-border') . '";' . "\n";
		$code .= 'ctxt_ad_lc = "' . $ad->get_property('color-title') . '";' . "\n";
		$code .= 'ctxt_ad_tc = "' . $ad->get_property('color-text') . '";' . "\n";
		$code .= 'ctxt_ad_uc = "' . $ad->get_property('color-link') . '";' . "\n";
		
		$code .= '// -->';
		$code .= '</script>';
		$code .= '<script language="JavaScript" src="http://ypn-js.overture.com/partner/js/ypn.js">';
		$code .= '</script>';
		
		return $code;
	}
}

?>