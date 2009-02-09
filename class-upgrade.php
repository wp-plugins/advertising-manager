<?php

class adsensem_upgrade {
	
	function go()
	{
		global $_adsensem;
	
		$upgraded = false;
		
		// Bug where version was = a string for a while...
		if ($_adsensem['version'] == 'ADVMAN_VERSION') {
			$_adsensem['version'] = '3.3.4';
			$upgraded = true;
		}
		
		/* List of possible upgrade paths here: Ensure that versions automagically stack on top of one another
				e.g. v1.x to v3.x should be possilbe v1.x > v2.x > v3.x */
		
		if (version_compare($_adsensem['version'], '3.0', '<')) {
			adsensem_upgrade::v2_x_to_3_0();
			$upgraded = true;
		}
		
		if (version_compare($_adsensem['version'], '3.3.4', '<')) {
			adsensem_upgrade::v3_0_to_3_3_3();
			$upgraded = true;
		}
	
		//Write notice, ONLY IF UPGRADE HAS OCCURRED
		if ($upgraded) {
			adsensem_admin::add_notice('optimise','Advertising Manager has been upgraded!<br />Would you like Advertising Manager to enable <a href="www.switzer.org">auto optimisation</a>? (RECOMMENDED)','yn');
		}
		
		$_adsensem['version'] = ADVMAN_VERSION;
	}

	
	function v3_0_to_3_3_3()
	{
		global $_adsensem;
		$old = $_adsensem;
		// New / Old class structure mapping
		$adnets = array(
			'ad_adbrite' => 'OX_Adnet_Adbrite',
			'ad_adgridwork' => 'OX_Adnet_Adgridwork',
			'ad_adpinion' => 'OX_Adnet_Adpinion',
			'ad_adroll' => 'OX_Adnet_Adroll',
			'ad_adsense' => 'OX_Adnet_Adsense',
			'ad_adsense_ad' => 'OX_Adnet_Adsense',
			'ad_adsense_classic' => 'OX_Adnet_Adsense',
			'ad_adsense_link' => 'OX_Adnet_Adsense',
			'ad_adsense_referral' => 'OX_Adnet_Adsense',
			'ad_cj' => 'OX_Adnet_Cj',
			'ad_code' => 'OX_Adnet_Html',
			'ad_crispads' => 'OX_Adnet_Crispads',
			'ad_openx_adserver' => 'OX_Adnet_Openx',
			'ad_shoppingads' => 'OX_Adnet_Shoppingads',
			'ad_widgetbucks' => 'OX_Adnet_Widgetbucks',
			'ad_ypn' => 'OX_Adnet_Ypn',
		);
			
		// Change defaults to new class structure
		if (!empty($_adsensem['defaults'])) {
			$new = array();
			$defaults = $_adsensem['defaults'];
			foreach ($defaults as $n => $default) {
				if (!empty($adnets[$n])) {
					// Set the new class structure
					$new[$adnets[$n]] = $_adsensem['defaults'][$n];
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
				}
			}
			$_adsensem['defaults'] = $new;
		}
		
		// Change account IDs to new class structure
		// We are going to change it to something later, but we need it this way for ad conversions...
		if (!empty($_adsensem['account-ids'])) {
			$new = array();
			$accounts = $_adsensem['account-ids'];
			foreach ($accounts as $n => $account) {
				if (!empty($adnets[$n])) {
					// Set the new class structure
					$new[$adnets[$n]] = $_adsensem['account-ids'][$n];
				}
			}
			$_adsensem['account-ids'] = $new;
		}
		
		if (!empty($_adsensem['ads'])) {
			$new = array();
			$id = 1;
			$ads = $_adsensem['ads'];
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
				$width = $ad->get('width', true);
				$height = $ad->get('height', true);
				if (empty($width) || empty($height)) {
					$format = $ad->get('adformat');
					if ( !empty($format) && ($format != 'custom')) {
						list($width, $height, $null) = split('[x]', $format);
						$this->set('width', $width);
						$this->set('height', $height);
					}
				}
				// Make sure that any settings under 'color-url' are now under 'color-link'
				$colorUrl = $ad->get('color-url');
				$colorLink = $ad->get('color-link');
				if (!empty($colorUrl) && empty($colorLink)) {
					$ad->set('color-link', $colorUrl);
					$ad->set('color-url', null);
				}
				// Re-import code because it was not saved in previous versions
				if ($ad->network != 'OX_Adnet_Html') {
					$code = call_user_func(array('adsensem_upgrade', '_render_' . $oldClass), $ad);
					$ad->import_settings($code);
				}
				// Set the new active field
				$ad->active = true;  // Need to set this ad as active in order for this ad to display
				// Set market optimisation
				$ad->set('openx-market', false);
				$ad->set('openx-market-cpm', '0.20');
				$ad->set('weight', '1');
				
				// Changed the 'hide link url' field to 'status' (for cj ads)
				$hideLinkUrl = $ad->get('hide-link-url');
				if (!empty($hideLinkUrl)) {
					$ad->set('status', $hideLinkUrl);
					$ad->set('hide-link-url', null);
				}
				
				// Got rid of the 'Code Method' field in Crisp Ads
				$ad->set('codemethod', null);
				
				// Get rid of the 'default_ad' field (should be 'default-ad')
				$ad->set('default_ad', null);
				
				$new[$ad->id] = $ad;
				$id++;
			}
			
			$_adsensem['ads'] = $new;
			$_adsensem['next_ad_id'] = $id;
		}
		
		// Move account IDs inside the ad array
		if (!empty($_adsensem['account-ids'])) {
			foreach ($_adsensem['account-ids'] as $class => $accountId) {
				foreach ($_adsensem['ads'] as $id => $ad) {
					if ($class == get_class($ad)) {
						$_adsensem['ads'][$id]['account-id'] = $accountId;
					}
				}
			}
		}
		
		// Be nice does not exist anymore
		if (!empty($_adsensem['be-nice'])) {
			unset($_adsensem['be-nice']);
		}
		if (!empty($_adsensem['benice'])) {
			unset($_adsensem['benice']);
		}
		
		// Remove the networks node
		if (!empty($_adsensem['networks'])) {
			unset($_adsensem['networks']);
		}
	}
	
	function v2_x_to_3_0(){
		global $_adsensem;
		
		$old=$_adsensem;
		
				/*  VERSION 3.x  */
				$_adsensem['ads'] = array();
				
				$_adsensem['be-nice'] = $old['benice'];
				$_adsensem['default-ad'] = $old['defaults']['ad'];
				
				$_adsensem['defaults']=array();
				$_adsensem['defaults']['ad_adsense_classic']=adsensem_upgrade::_process_v2_x_to_3_0($old['defaults']);
				$_adsensem['defaults']['ad_adsense']=$_adsensem['defaults']['ad_adsense_classic'];
				
				/* Copy AdSense account-id to both class/new settings */
				$_adsensem['account-ids']['ad_adsense']=$old['adsense-account'];
				
				/* Now all that remains is to convert the ads. In 2.x ads were stored as simply arrays containing the options.
					To upgrade create new objects using product/slot/etc. info, or for code units run an import cycle. */
				
				if(is_array($old['ads'])){
				foreach($old['ads'] as $oname=>$oad){
					
					if($oad['slot']!=''){$type='slot';}
					else {$type=$oad['product'];}
					
					$name=adsensem_admin::generate_name($oname);
					
					switch($type){
						
						/* HTML Code Ads */
						case 'code':
							$ad=adsensem_admin::import_ad($oad['code']);
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
						break;
						
						/* AdSense Slot Ads */
						case 'slot':
							$ad=new Ad_AdSense();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;
						/* AdSense Ad */
						case 'ad':
							$ad=new Ad_AdSense_Ad();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;
						case 'link':
							$ad=new Ad_AdSense_Link();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;
							
						case 'referral':
						case 'referral-image':
						case 'referral-text':
							$ad=new Ad_AdSense_Referral();
							$_adsensem['ads'][$name]=$ad;
							$_adsensem['ads'][$name]->name=$name;
							$_adsensem['ads'][$name]->p=adsensem_upgrade::_process_v2_x_to_3_0($oad);
						break;	
					}
					
				} 
				}
			
		OX_Tools::sort($_adsensem['ads']);
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
		global $_adsensem;
		$deluxe=get_option('acmetech_adsensedeluxe');
		
			foreach($deluxe['ads'] as $key => $vals){
				$ad=adsensem_admin::import_ad($vals['code']);
				$name=adsensem_admin::generate_name($vals['name']);
				
				$ad->name = $name;
				
				$ad->set('show-home', ($deluxe['enabled_for']['home'] == 1) ? 'yes' : 'no');
				$ad->set('show-post', ($deluxe['enabled_for']['posts'] == 1) ? 'yes' : 'no');
				$ad->set('show-archive', ($deluxe['enabled_for']['archives'] == 1) ? 'yes' : 'no');
				$ad->set('show-page', ($deluxe['enabled_for']['page'] == 1) ? 'yes' : 'no');
				$_adsensem['ads'][$name] = $ad;
				
				if ($vals['make_default'] == 1) {
					$_adsensem['default-ad'] = $name;
				}
			}
		
		OX_Tools::sort($_adsensem['ads']);
	}
	
	function _render_ad_adsense($ad)
	{
		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "pub-' . $_adsensem['account-ids'][$ad->network] . '";' . "\n";
		$code.= 'google_ad_slot = "' . str_pad($ad->get('slot', true),10,'0',STR_PAD_LEFT) . '"' . ";\n"; //String padding to max 10 char slot ID
		
		if($ad->get('adtype', true)=='ref_text'){
			$code.= 'google_ad_output = "textlink"' . ";\n";
			$code.= 'google_ad_format = "ref_text"' . ";\n";
			$code.= 'google_cpa_choice = ""' . ";\n";
		} else if($ad->get('adtype', true)=='ref_image'){
			$code.= 'google_ad_width = ' . $ad->get('width', true) . ";\n";
			$code.= 'google_ad_height = ' . $ad->get('height', true) . ";\n";
			$code.= 'google_cpa_choice = ""' . ";\n";
		} else {
			$code.= 'google_ad_width = ' . $ad->get('width', true) . ";\n";
			$code.= 'google_ad_height = ' . $ad->get('height', true) . ";\n";
		}
		
		$code.= '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";
		
		return $code;
	}
	
	function _render_ad_adbrite($ad)
	{
		$code ='<!-- Begin: AdBrite -->';
		$code .= '<script type="text/javascript">' . "\n";
		$code .= "var AdBrite_Title_Color = '" . $ad->get('color-title', true) . "'\n";
		$code .= "var AdBrite_Text_Color = '" . $ad->get('color-text', true) . "'\n";
		$code .= "var AdBrite_Background_Color = '" . $ad->get('color-bg', true) . "'\n";
		$code .= "var AdBrite_Border_Color = '" . $ad->get('color-border', true) . "'\n";
		$code .= '</script>' . "\n";
	   	$code .= '<script src="http://ads.adbrite.com/mb/text_group.php?sid=' . $ad->get('slot', true) . '&zs=' . $_adsensem['account-ids'][$ad->network] . '" type="text/javascript"></script>';
		$code .= '<div><a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=' . $ad->get('slot', true) . '&afsid=1" style="font-weight:bold;font-family:Arial;font-size:13px;">Your Ad Here</a></div>';
		$code .= '<!-- End: AdBrite -->';
		
		return $code;
	}
	
	function _render_ad_adgridwork($ad)
	{
		$code ='<a href="http://www.adgridwork.com/?r=' . $_adsensem['account-ids'][$ad->network] . '" style="color: #' . $ad->get('color-link', true) .  '; font-size: 14px" target="_blank">Free Advertising</a>';
		$code.='<script type="text/javascript">' . "\n";
		$code.="var sid = '"  . $ad->get('slot', true) . "';\n";
		$code.="var title_color = '" . $ad->get('color-title', true) . "';\n";
		$code.="var description_color = '" . $ad->get('color-text', true) . "';\n";
		$code.="var link_color = '" . $ad->get('color-link', true) . "';\n";
		$code.="var background_color = '" . $ad->get('color-bg', true) . "';\n";
		$code.="var border_color = '" . $ad->get('color-border', true) . "';\n";
		$code.='</script><script type="text/javascript" src="http://www.mediagridwork.com/mx.js"></script>';
		
		return $code;
	}
	
	function _render_ad_adpinion($ad)
	{
		if($ad->get('width', true)>$ad->get('height', true)){$xwidth=18;$xheight=17;} else {$xwidth=0;$xheight=35;}
		$code ='';
	 	$code .= '<iframe src="http://www.adpinion.com/app/adpinion_frame?website=' . $_adsensem['account-ids'][$ad->network] . '&amp;width=' . $ad->get('width', true) . '&amp;height=' . $ad->get('height', true) . '" ';
		$code .= 'id="adframe" style="width:' . ($ad->get('width', true)+$xwidth) . 'px;height:' . ($ad->get('height', true)+$xheight) . 'px;" scrolling="no" frameborder="0">.</iframe>';
	
		return $code;
	}
	
	function _render_ad_adroll($ad)
	{
		$code ='';
		$code .= '<!-- Start: Adroll Ads -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $_adsensem['account-ids'][$ad->network] . '/' . $ad->get('slot', true) . '/">';
		$code .= '</script>';
		$code .= '<!-- Start: Adroll Profile Link -->';
	 	$code .= '<script type="text/javascript" src="http://c.adroll.com/r/' . $_adsensem['account-ids'][$ad->network] . '/' . $ad->get('slot', true) . '/link">';
		$code .= '</script>';
	
		return $code;
	}
	
	function _render_ad_adsense_ad($ad)
	{
		$code='';
		
		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "pub-' . $_adsensem['account-ids'][$ad->network] . '";' . "\n";
				
		if($ad->get('channel', true)!==''){ $code.= 'google_ad_channel = "' . $ad->get('channel', true) . '";' . "\n"; }
		if($ad->get('uistyle', true)!==''){ $code.= 'google_ui_features = "rc:' . $ad->get('uistyle', true) . '";' . "\n"; }
				
		$code.= 'google_ad_width = ' . $ad->get('width', true) . ";\n";
		$code.= 'google_ad_height = ' . $ad->get('height', true) . ";\n";
				
		$code.= 'google_ad_format = "' . $ad->get('adformat', true) . '_as"' . ";\n";
		$code.= 'google_ad_type = "' . $ad->get('adtype', true) . '"' . ";\n";

		switch ($ad->get('alternate-ad', true)) {
			case 'url'		: $code.= 'google_alternate_ad_url = "' . $ad->get('alternate-url', true) . '";' . "\n"; break;
			case 'color'	: $code.= 'google_alternate_ad_color = "' . $ad->get('alternate-color', true) . '";' . "\n"; break;
			case ''				: break;
			default				:
				$alternateAd = $ad->get('alternate-ad');
				if (!empty($alternateAd)) {
					$code.= 'google_alternate_ad_url = "' . get_bloginfo('wpurl') . '/?adsensem-show-ad=' . $alternateAd . '";'  . "\n";
				}
		}
		
		$code.= 'google_color_border = "' . $ad->get('color-border', true) . '"' . ";\n";
		$code.= 'google_color_bg = "' . $ad->get('color-bg', true) . '"' . ";\n";
		$code.= 'google_color_link = "' . $ad->get('color-title', true) . '"' . ";\n";
		$code.= 'google_color_text = "' . $ad->get('color-text', true) . '"' . ";\n";
		$code.= 'google_color_url = "' . $ad->get('color-link', true) . '"' . ";\n";
		
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _render_ad_adsense_link($ad)
	{
		$code='';

		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "pub-' . $_adsensem['account-ids'][$ad->network] . '";' . "\n";
					
		if($ad->get('channel', true)!==''){ $code.= 'google_ad_channel = "' . $ad->get('channel', true) . '";' . "\n"; }
		if($ad->get('uistyle', true)!==''){ $code.= 'google_ui_features = "rc:' . $ad->get('uistyle', true) . '";' . "\n"; }
					
		$code.= 'google_ad_width = ' . $ad->get('width', true) . ";\n";
		$code.= 'google_ad_height = ' . $ad->get('height', true) . ";\n";
					
		$code.= 'google_ad_format = "' . $ad->get('adformat', true) . $ad->get('adtype', true) . '"' . ";\n"; 

		//$code.=$ad->_render_alternate_ad_code();
		$code.= 'google_color_border = "' . $ad->get('color-border', true) . '"' . ";\n";
		$code.= 'google_color_bg = "' . $ad->get('color-bg', true) . '"' . ";\n";
		$code.= 'google_color_link = "' . $ad->get('color-title', true) . '"' . ";\n";
		$code.= 'google_color_text = "' . $ad->get('color-text', true) . '"' . ";\n";
		$code.= 'google_color_url = "' . $ad->get('color-link', true) . '"' . ";\n";
			
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _render_ad_adsense_referral($ad)
	{
		//if($ad===false){$ad=$_adsensem['ads'][$_adsensem['default_ad']];}
		//$ad=adsensem::merge_defaults($ad); //Apply defaults
		if($ad->get('product', true)=='referral-image') {
			$format = $ad->get('adformat', true) . '_as_rimg';
		} else if($ad->get('product', true)=='referral-text') {
			$format = 'ref_text';
		}				
		$code='';

	
		$code .= '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client = "pub-' . $_adsensem['account-ids'][$ad->network] . '";' . "\n";
		
		if($ad->get('channel', true)!==''){ $code.= 'google_ad_channel = "' . $ad->get('channel', true) . '";' . "\n"; }
		
		if($ad->get('product', true)=='referral-image'){
			$code.= 'google_ad_width = ' . $ad->get('width', true) . ";\n";
			$code.= 'google_ad_height = ' . $ad->get('height', true) . ";\n";
		}
		
		if($ad->get('product', true)=='referral-text'){$code.='google_ad_output = "textlink"' . ";\n";}
		$code.='google_cpa_choice = "' . $ad->get('referral', true) . '"' . ";\n";
		
		$code.= "\n" . '//--></script>' . "\n";

		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";

		return $code;
	}
	
	function _render_ad_cj($ad)
	{
		$cjservers=array(
			'www.kqzyfj.com',
			'www.tkqlhce.com',
			'www.jdoqocy.com',
			'www.dpbolvw.net',
			'www.lduhtrp.net');
		
		$code = '';
		$code .= '<!-- Start: CJ Ads -->';
	 	$code .= '<a href="http://' . $cjservers[array_rand($cjservers)] . '/click-' . $_adsensem['account-ids'][$ad->network] . '-' . $ad->get('slot', true) . '"';
		if($ad->get('new-window', true)=='yes'){$code.=' target="_blank" ';}
		
		if($ad->get('hide-link', true)=='yes'){
			$code.='onmouseover="window.status=\'';
			$code.=$ad->get('hide-link-url', true);
			$code.='\';return true;" onmouseout="window.status=\' \';return true;"';
		}
		
		$code .= '>';
		
		$code .= '<img src="http://' . $cjservers[array_rand($cjservers)] . '/image-' . $_adsensem['account-ids'][$ad->network] . '-' . $ad->get('slot', true) . '"';
		$code .= ' width="' . $ad->get('width', true) . '" ';
		$code .= ' height="' . $ad->get('height', true) . '" ';
		$code .= ' alt="' . $ad->get('alt-text', true) . '" ';
		$code .= '>';
		$code .= '</a>';
	
		return $code;
	}
	
	function _render_ad_crispads($ad)
	{
		if ($ad->get('codemethod', true)=='javascript'){
			$code='<script type="text/javascript"><!--//<![CDATA[' . "\n";
			$code.="var m3_u = (location.protocol=='https:'?'https://www.crispads.com/spinner/www/delivery/ajs.php':'http://www.crispads.com/spinner/www/delivery/ajs.php');\n";
			$code.="var m3_r = Math.floor(Math.random()*99999999999);\n";
			$code.="if (!document.MAX_used) document.MAX_used = ',';\n";
			$code.="document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);\n";
			$code.='document.write ("?zoneid=' . $ad->get('slot', true) . '");' . "\n";
			$code.="document.write ('&amp;cb=' + m3_r);\n";
			$code.="if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);\n";
	   		$code.='document.write ("&amp;loc=" + escape(window.location));' . "\n";
			$code.='if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));' . "\n";
			$code.='if (document.context) document.write ("&context=" + escape(document.context));' . "\n";
			$code.='if (document.mmm_fo) document.write ("&amp;mmm_fo=1");' . "\n";
			$code.='document.write ("\'><\/scr"+"ipt>");' . "\n";
			$code.='//]]>--></script><noscript><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $ad->get('identifier', true) . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $ad->get('slot', true) . '&amp;n=' . $ad->get('identifier', true) . '" border="0" alt="" /></a></noscript>';
		} else { //Iframe
			$code='<iframe id="' . $ad->get('identifier', true) . '" name="' . $ad->get('identifier', true) . '" src="http://www.crispads.com/spinner/www/delivery/afr.php?n=' . $ad->get('identifier', true) . '&amp;zoneid=' . $ad->get('slot', true) . '" framespacing="0" frameborder="no" scrolling="no" width="' . $ad->get('width', true) . '" height="' . $ad->get('height', true) . '"><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=' . $ad->get('identifier', true) . '&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=' . $ad->get('slot', true) . '&amp;n=' . $ad->get('identifier', true) . '" border="0" alt="" /></a></iframe>';
			$code.='<script type="text/javascript" src="http://www.crispads.com/spinner/www/delivery/ag.php"></script>';
		}
		
		return $code;
	}
	function _render_ad_shoppingads($ad)
	{
		$code = '<script type="text/javascript"><!--' . "\n";
		$code.= 'shoppingads_ad_client = "' . $_adsensem['account-ids'][$ad->network] . '";' . "\n";
		$code.= 'shoppingads_ad_campaign = "' . $ad->get('campaign', true) . '";' . "\n";

		list($width,$height,$null)=split('[x]',$ad->get('adformat', true));
		$code.= 'shoppingads_ad_width = "' . $width . '";' . "\n";
		$code.= 'shoppingads_ad_height = "' . $height . '";' . "\n";

		$code.= 'shoppingads_ad_kw = "' . $ad->get('keywords', true) . '";' . "\n";

		$code.= 'shoppingads_color_border = "' . $ad->get('color-border', true) . '";' . "\n";
		$code.= 'shoppingads_color_bg = "' . $ad->get('color-bg', true) . '";' . "\n";
		$code.= 'shoppingads_color_heading = "' . $ad->get('color-title', true) . '";' . "\n";
		$code.= 'shoppingads_color_text = "' . $ad->get('color-text', true) . '";' . "\n";
		$code.= 'shoppingads_color_link = "' . $ad->get('color-title', true) . '";' . "\n";

		$code.= 'shoppingads_attitude = "' . $ad->get('attitude', true) . '";' . "\n";
		if($ad->get('new-window', true)=='yes'){$code.= 'shoppingads_options = "n";' . "\n";}

		$code.= '--></script>
		<script type="text/javascript" src="http://ads.shoppingads.com/pagead/show_sa_ads.js">
		</script>' . "\n";
		
		return $code;
	}
	
	function _render_ad_widgetbucks($ad)
	{
		$code ='';
		$code .= '<!-- START CUSTOM WIDGETBUCKS CODE --><div>';
		$code .= '<script src="http://api.widgetbucks.com/script/ads.js?uid=' . $ad->get('slot', true) . '"></script>'; 
		$code .= '</div><!-- END CUSTOM WIDGETBUCKS CODE -->';
		return $code;
	}
	
	function _render_ad_ypn($ad)
	{
		$code = '<script language="JavaScript">';
		$code .= '<!--';
		$code .= 'ctxt_ad_partner = "' . $_adsensem['account-ids'][$ad->network] . '";' . "\n";
		$code .= 'ctxt_ad_section = "' . $ad->get('channel', true) . '";' . "\n";
		$code .= 'ctxt_ad_bg = "";' . "\n";
		$code .= 'ctxt_ad_width = "' . $ad->get('width', true) . '";' . "\n";
		$code .= 'ctxt_ad_height = "' . $ad->get('height', true) . '";' . "\n";
		
		$code .= 'ctxt_ad_bc = "' . $ad->get('color-bg', true) . '";' . "\n";
		$code .= 'ctxt_ad_cc = "' . $ad->get('color-border', true) . '";' . "\n";
		$code .= 'ctxt_ad_lc = "' . $ad->get('color-title', true) . '";' . "\n";
		$code .= 'ctxt_ad_tc = "' . $ad->get('color-text', true) . '";' . "\n";
		$code .= 'ctxt_ad_uc = "' . $ad->get('color-link', true) . '";' . "\n";
		
		$code .= '// -->';
		$code .= '</script>';
		$code .= '<script language="JavaScript" src="http://ypn-js.overture.com/partner/js/ypn.js">';
		$code .= '</script>';
		
		return $code;
	}
}

?>