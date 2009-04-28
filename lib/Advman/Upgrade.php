<?php

class Advman_Upgrade
{
	function upgrade()
	{
		global $advman_engine;
	
		$upgraded = false;
		$optimiseMsg = false;
		$dbVersion = $advman_engine->getVersion();
		
		// Bug where version was = a string for a while...
		if ($dbVersion == 'ADVMAN_VERSION') {
			$dbVersion = '3.3.4';
			$upgraded = true;
		}
		
		/* List of possible upgrade paths here: Ensure that versions automagically stack on top of one another
				e.g. v1.x to v3.x should be possilbe v1.x > v2.x > v3.x */
		
		if (version_compare($dbVersion, '3.0', '<')) {
			Advman_Upgrade::v2_x_to_3_0();
			$upgraded = true;
		}
		
		if (version_compare($dbVersion, '3.3.4', '<')) {
			Advman_Upgrade::v3_0_to_3_3_4();
			$optimiseMsg = true;
			$upgraded = true;
		}
	
		if (version_compare($dbVersion, '3.3.8', '<')) {
			Advman_Upgrade::v3_3_4_to_3_3_8();
			$upgraded = true;
		}
	
		if (version_compare($dbVersion, '3.3.10', '<')) {
			Advman_Upgrade::v3_3_10();
			$upgraded = true;
		}
		
		if (version_compare($dbVersion, '3.3.11', '<')) {
			Advman_Upgrade::v3_3_11();
			$upgraded = true;
		}
	
		if ($upgraded) {
			//Write notice, ONLY IF UPGRADE HAS OCCURRED
			if ($optimiseMsg) {
				$notice = __('<strong>Advertising Manager</strong> has been upgraded from <strong>Adsense Manager</strong>.', 'advman');
				$question = __('Enable <a>auto optimisation</a>? (RECOMMENDED)', 'advman');
				$question = str_replace('<a>', '<a href="http://code.openx.org/wiki/advertising-manager/Auto_Optimization" target="_new">', $question);
				Advman_Admin::add_notice('optimise', "$notice $question", 'yn');
			}
			$advman_engine->setVersion(ADVMAN_VERSION);
		}
	}

	function v3_3_11()
	{
		return;
	}
	function v3_3_10()
	{
		global $_advman;
		
		if (!empty($_advman['defaults'])) {
			foreach ($_advman['defaults'] as $n => $default) {
				$counter = '';
				if ($n == 'OX_Ad_Adsense') {
					$counter = '3';
				}
				$_advman['defaults'][$n]['counter'] = $counter;
			}
		}
	}
	
	function v3_3_4_to_3_3_8()
	{
		global $_advman;
		$_advman['settings']['openx-sync'] = true;
		$_advman['uuid'] = $viewerId = md5(uniqid('', true));
	}
	function v3_0_to_3_3_4()
	{
		global $_advman;
		$old = $_advman;
		// New / Old class structure mapping
		$adnets = array(
			'ad_adbrite' => 'OX_Ad_Adbrite',
			'ad_adgridwork' => 'OX_Ad_Adgridwork',
			'ad_adpinion' => 'OX_Ad_Adpinion',
			'ad_adroll' => 'OX_Ad_Adroll',
			'ad_adsense' => 'OX_Ad_Adsense',
			'ad_adsense_ad' => 'OX_Ad_Adsense',
			'ad_adsense_classic' => 'OX_Ad_Adsense',
			'ad_adsense_link' => 'OX_Ad_Adsense',
			'ad_adsense_referral' => 'OX_Ad_Adsense',
			'ad_cj' => 'OX_Ad_Cj',
			'ad_code' => 'OX_Ad_Html',
			'ad_crispads' => 'OX_Ad_Crispads',
			'ad_openx_adserver' => 'OX_Ad_Openx',
			'ad_shoppingads' => 'OX_Ad_Shoppingads',
			'ad_widgetbucks' => 'OX_Ad_Widgetbucks',
			'ad_ypn' => 'OX_Ad_Ypn',
		);
			
		// Change defaults to new class structure
		if (!empty($_advman['defaults'])) {
			$new = array();
			$defaults = $_advman['defaults'];
			foreach ($defaults as $n => $default) {
				if (!empty($adnets[$n])) {
					// Set the new class structure
					$new[$adnets[$n]] = $_advman['defaults'][$n];
					// Set OpenX Market participation
					if (!isset($new[$adnets[$n]]['openx-market'])) {
						$new[$adnets[$n]]['openx-market'] = 'no';
					}
					// Set OpenX Market eCPM
					if (!isset($new[$adnets[$n]]['openx-market-cpm'])) {
						$new[$adnets[$n]]['openx-market-cpm'] = '0.20';
					}
					// Set Weight
					if (!isset($new[$adnets[$n]]['weight'])) {
						$new[$adnets[$n]]['weight'] = '1';
					}
					// Show only to an Author
					if (!isset($new[$adnets[$n]]['show-author'])) {
						$new[$adnets[$n]]['show-author'] = 'all';
					}
					// Show only for a Category
					if (!isset($new[$adnets[$n]]['show-category'])) {
						$new[$adnets[$n]]['show-category'] = 'all';
					}
					// Set height and width for an ad format
					if (!empty($new[$adnets[$n]]['adformat']) && ($new[$adnets[$n]]['adformat'] != 'custom')) {
						list($width, $height) = split('[x]', $new[$adnets[$n]]['adformat']);
						if (is_numeric($width)) {
							$new[$adnets[$n]]['width'] = $width;
						}
						if (is_numeric($height)) {
							$new[$adnets[$n]]['height'] = $height;
						}
					}
				}
			}
			$_advman['defaults'] = $new;
		}
		
		// Change account IDs to new class structure
		// We are going to change it to something later, but we need it this way for ad conversions...
		if (!empty($_advman['account-ids'])) {
			$new = array();
			$accounts = $_advman['account-ids'];
			foreach ($accounts as $n => $account) {
				if (!empty($adnets[$n])) {
					// Set the new class structure
					$new[$adnets[$n]] = $_advman['account-ids'][$n];
				}
			}
			$_advman['account-ids'] = $new;
		}
		
		if (!empty($_advman['ads'])) {
			$new = array();
			$id = 1;
			$ads = $_advman['ads'];
			// Next, make sure that the classes and properties are ok.
			foreach ($ads as $n => $ad) {
				$oldClass = '';
				if (get_class($ad) == '__PHP_Incomplete_Class') {
					$a = null;
					// Ugly hack - for some reason I cannot call $ad->__PHP_Incomplete_Class_Name directly
					foreach ($ad as $key => $value) {
						if ($key == '__PHP_Incomplete_Class_Name') {
							$class = strtolower($value);
							$oldClass = $class;
							if (!empty($adnets[$class])) {
								$a = new $adnets[$class];
								break;
							}
						}
					}
					if ($a) {
						foreach ($ad as $key => $value) {
							switch ($key) {
								case 'id' :
									$a->id = $value;
									break;
								case 'name' :
									$a->name = $value;
									break;
								case 'p' :
									$a->p = $value;
									break;
								case 'title' :
									$a->title = $value;
									break;
							}
						}
						$ad = $a;
					}
				}
				if (empty($ad->id)) {
					$ad->id = $id;
				} else {
					$id = ($ad->id > $id ? $ad->id : $id);
				}
				if (empty($ad->name)) {
					$ad->name = $n;
				}
				// Make sure width and height is set correctly
				$width = $ad->get_property('width');
				$height = $ad->get_property('height');
				if (empty($width) || empty($height)) {
					$format = $ad->get_property('adformat');
					if ( !empty($format) && ($format != 'custom')) {
						list($width, $height, $null) = split('[x]', $format);
						$this->set_property('width', $width);
						$this->set_property('height', $height);
					}
				}
				// Make sure that any settings under 'color-url' are now under 'color-link'
				$colorUrl = $ad->get_property('color-url');
				$colorLink = $ad->get_property('color-link');
				if (!empty($colorUrl) && empty($colorLink)) {
					$ad->set_property('color-link', $colorUrl);
					$ad->set_property('color-url', null);
				}
				// Re-import code because it was not saved in previous versions
				if ($ad->network != 'OX_Ad
_Html') {
					$code = call_user_func(array('advman_upgrade', '_render_' . $oldClass), $ad);
					$ad->import_settings($code);
				}
				// Set the new active field
				$ad->active = true;  // Need to set this ad as active in order for this ad to display
				// Set market optimisation
				$ad->set_property('openx-market', false);
				$ad->set_property('openx-market-cpm', '0.20');
				$ad->set_property('weight', '1');
				
				// Changed the 'hide link url' field to 'status' (for cj ads)
				$hideLinkUrl = $ad->get_property('hide-link-url');
				if (!empty($hideLinkUrl)) {
					$ad->set_property('status', $hideLinkUrl);
					$ad->set_property('hide-link-url', null);
				}
				
				// Got rid of the 'Code Method' field in Crisp Ads
				$ad->set_property('codemethod', null);
				
				// Get rid of the 'default_ad' field (should be 'default-ad')
				$ad->set_property('default_ad', null);
				
				$new[$ad->id] = $ad;
				$id++;
			}
			
			$_advman['ads'] = $new;
			$_advman['next_ad_id'] = $id;
		}
		
		// Move account IDs inside the ad array
		if (!empty($_advman['account-ids'])) {
			foreach ($_advman['account-ids'] as $class => $accountId) {
				foreach ($_advman['ads'] as $id => $ad) {
					if ($class == get_class($ad)) {
						$exitingAccountId = $ad->get_property('account-id');
						if (empty($exitingAccountId)) {
							$_advman['ads'][$id]->set_property('account-id', $accountId);
						}
					}
				}
			}
		}
		
		// Be nice does not exist anymore
		if (!empty($_advman['be-nice'])) {
			unset($_advman['be-nice']);
		}
		if (!empty($_advman['benice'])) {
			unset($_advman['benice']);
		}
		
		// Remove the networks node
		if (!empty($_advman['networks'])) {
			unset($_advman['networks']);
		}
	}
	
	function v2_x_to_3_0(){
		global $_advman;
		
		$old=$_advman;
		
				/*  VERSION 3.x  */
				$_advman['ads'] = array();
				
				$_advman['be-nice'] = $old['benice'];
				$_advman['default-ad'] = $old['defaults']['ad'];
				
				$_advman['defaults']=array();
				$_advman['defaults']['ad_adsense_classic']=advman_upgrade::_process_v2_x_to_3_0($old['defaults']);
				$_advman['defaults']['ad_adsense']=$_advman['defaults']['ad_adsense_classic'];
				
				/* Copy AdSense account-id to both class/new settings */
				$_advman['account-ids']['ad_adsense']=$old['adsense-account'];
				
				/* Now all that remains is to convert the ads. In 2.x ads were stored as simply arrays containing the options.
					To upgrade create new objects using product/slot/etc. info, or for code units run an import cycle. */
				
				if(is_array($old['ads'])){
				foreach($old['ads'] as $oname=>$oad){
					
					if($oad['slot']!=''){$type='slot';}
					else {$type=$oad['product'];}
					
					$name=advman_admin::generate_name($oname);
					
					switch($type){
						
						/* HTML Code Ads */
						case 'code':
							$ad=advman_admin::import_ad($oad['code']);
							$_advman['ads'][$name]=$ad;
							$_advman['ads'][$name]->name=$name;
						break;
						
						/* AdSense Slot Ads */
						case 'slot':
							$ad=new Ad_AdSense();
							$_advman['ads'][$name]=$ad;
							$_advman['ads'][$name]->name=$name;
							$_advman['ads'][$name]->p=advman_upgrade::_process_v2_x_to_3_0($oad);
						break;
						/* AdSense Ad */
						case 'ad':
							$ad=new Ad_AdSense_Ad();
							$_advman['ads'][$name]=$ad;
							$_advman['ads'][$name]->name=$name;
							$_advman['ads'][$name]->p=advman_upgrade::_process_v2_x_to_3_0($oad);
						break;
						case 'link':
							$ad=new Ad_AdSense_Link();
							$_advman['ads'][$name]=$ad;
							$_advman['ads'][$name]->name=$name;
							$_advman['ads'][$name]->p=advman_upgrade::_process_v2_x_to_3_0($oad);
						break;
							
						case 'referral':
						case 'referral-image':
						case 'referral-text':
							$ad=new Ad_AdSense_Referral();
							$_advman['ads'][$name]=$ad;
							$_advman['ads'][$name]->name=$name;
							$_advman['ads'][$name]->p=advman_upgrade::_process_v2_x_to_3_0($oad);
						break;	
					}
					
				} 
				}
			
		OX_Tools::sort($_advman['ads']);
		}
		
	
	function _process_v2_x_to_3_0($old){
		$new=$old;
				
		/* Additional conversaion required for rearrangement of colors system */
		$new['color-border']=$old['colors']['border'];								
		$new['color-title']=$old['colors']['link'];								
		$new['color-bg']=$old['colors']['bg'];								
		$new['color-text']=$old['colors']['text'];
		$new['color-url']=$old['colors']['url'];	
		/* End color rearrangement */
		$new['show-page']=$old['show-post'];
		
		/* Adformat codes etc. need to be moved */
		switch($old['product']){
		case 'ad':
			if($old['alternate-url']){ $new['alternate-ad']='url'; } else if($old['alternate-color']) { $new['alternate-ad']='color'; } else { $new['alternate-ad']='benice'; }
		break;
		case 'link':
			$new['adformat']=$old['linkformat'];
			$new['adtype']=$old['linktype'];
		break;
		case 'referral':
		case 'referral-text':
			$new['adformat']=$old['referralformat'];
		break;
		}
		
		list($new['width'],$new['height'],$null)=split('[x]',$new['adformat']);  //Split to fill width/height information
		
		return $new;
	}
	
	
	function adsense_deluxe_to_3_0()
	{
		global $_advman;
		$deluxe = get_option('acmetech_adsensedeluxe');
		
		foreach ($deluxe['ads'] as $key => $vals) {
			$ad = advman_admin::import_ad($vals['code']);
			$name = advman_admin::generate_name($vals['name']);
			
			$ad->name = $name;
			
			$ad->set_property('show-home', ($deluxe['enabled_for']['home'] == 1) ? 'yes' : 'no');
			$ad->set_property('show-post', ($deluxe['enabled_for']['posts'] == 1) ? 'yes' : 'no');
			$ad->set_property('show-archive', ($deluxe['enabled_for']['archives'] == 1) ? 'yes' : 'no');
			$ad->set_property('show-page', ($deluxe['enabled_for']['page'] == 1) ? 'yes' : 'no');
			$_advman['ads'][$name] = $ad;
			
			if ($vals['make_default'] == 1) {
				$_advman['default-ad'] = $name;
			}
		}
		
		OX_Tools::sort($_advman['ads']);
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