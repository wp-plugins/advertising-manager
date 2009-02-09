<?php
if (!ADVMAN_VERSION) {die();}

require_once(ADS_PATH . '/OX/Tools.php');

function adsensem_clone($object)
{
	return version_compare(phpversion(), '5.0') < 0 ? $object : clone($object);
}

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class adsensem_admin
{
	function init_admin()
	{
		global $_adsensem;
		
		add_action('admin_head', array('adsensem_admin','add_header_script'));
		add_action('admin_footer', array('adsensem_admin','admin_callback_editor'));
		
		wp_enqueue_script('prototype');
		wp_enqueue_script('postbox');
		
//		add_submenu_page('edit.php',"Ads", "Ads", 10, "advertising-manager-manage-ads", array('adsensem_admin','admin_manage'));
		add_management_page("Ads", "Ads", 10, "advertising-manager-manage-ads", array('adsensem_admin','admin_manage'));
		add_options_page("Advertising Manager Settings", "Ads", 10, "advertising-manager-options", array('adsensem_admin','admin_options'));
		add_action( 'admin_notices', array('adsensem_admin','admin_notices'), 1 );
		
		$email = get_option('admin_email');
		$user = get_current_user();
		$siteurl = get_option('siteurl');
		
		$update_adsensem = false;
		
		//No startup data found, fill it out now.
		if (adsensem::setup_is_valid() == false) {
			
			// Get basic array
			$_adsensem = adsensem::get_initial_array();
			
			// Check to see if Adsense Deluxe should be upgraded
			$deluxe = get_option('acmetech_adsensedeluxe');
			if (is_array($deluxe)) {
				adsensem_admin::add_notice('upgrade adsense-deluxe','Advertising Manager has detected a previous installation of <strong>Adsense Deluxe</strong>. Import settings?','yn');
			}
			
			$update_adsensem = true; 
			
		} elseif (version_compare($_adsensem['version'], ADVMAN_VERSION, '<')) {
			include_once('class-upgrade.php');
			
			//Backup cycle
			$backup = get_option('plugin_adsensem_backup');
			$backup[adsensem_admin::major_version($_adsensem['version'])] = $_adsensem;
			update_option('plugin_adsensem_backup',$backup);
			unset($backup);
			
			adsensem_upgrade::go();
			$update_adsensem = true;
		}
		
		if ($update_adsensem) {
			update_option('plugin_adsensem', $_adsensem);
		}
	}
			
	function major_version($v)
	{
		$mv=explode('.', $v);
		return $mv[0]; //Return major version
	}
		
	/**
	 * Build an HMTL field of type TEXT
	 */
	function build_input_text_field($name, $value, $size="", $description="", $id="", $style="")
	{
		$n = htmlspecialchars($name, ENT_QUOTES);
		$v = htmlspecialchars($value, ENT_QUOTES);
		$sz = !empty($size) ? (' size="' . htmlspecialchars($size, ENT_QUOTES) . '"') : '';
		$d = !empty($description) ? (' title="' . htmlspecialchars($description, ENT_QUOTES) . '"') : '';
		$i = !empty($id) ? (' id="' . htmlspecialchars($id, ENT_QUOTES) . '"') : '';
		$s = !empty($style) ? (' style="' . htmlspecialchars($style, ENT_QUOTES) . '"') : '';
		return "<input type=\"text\"{$s}{$i} name=\"$name\" value=\"$value\"{$sz}{$d} />";
	}
	
	function build_input_hidden_field($name, $value, $id="")
	{
		$n = htmlspecialchars($name, ENT_QUOTES);
		$v = htmlspecialchars($value, ENT_QUOTES);
		$i = !empty($id) ? (' id="' . htmlspecialchars($id, ENT_QUOTES) . '"') : '';
		return "<input type=\"hidden\"{$i} name=\"$name\" value=\"$value\" />";
	}
	
	function build_label($name, $for="")
	{
		$n = htmlspecialchars($name, ENT_QUOTES);
		$f = !empty($for) ? ( ' for="' . htmlspecialchars($for, ENT_QUOTES) . '"') : '';
		return "<label{$f}>$n</label>";
	}




/*
	NOTIFICATION FUNCTIONS
	Functions below output notices to update the user on import options, issues with the data imported etc.
*/
	function admin_notices()
	{
		global $_adsensem;
		
		if (!empty($_adsensem['notices'])) {
			include_once(ADS_PATH . '/Template/' . TEMPLATE . '/Notice.php');
			Template_Notice::display($_adsensem['notices']);
		}
		
	}
	
	function add_notice($action,$text,$confirm=false)
	{
		global $_adsensem;
		$_adsensem['notices'][$action]['text'] = $text;
		$_adsensem['notices'][$action]['confirm'] = $confirm;
		update_option('plugin_adsensem', $_adsensem);
	}
	
	function remove_notice($action)
	{
		global $_adsensem;
		if (!empty($_adsensem['notices'][$action])) {
			unset($_adsensem['notices'][$action]); //=false;
		}
		update_option('plugin_adsensem', $_adsensem);		
	}

	function generate_name($base = null)
	{
		global $_adsensem;
		
		if (empty($base)) {
			$base = 'ad';
		}
		
		// Generate a unique name if no name was specified
		$unique = false;
		$i = 1;
		$name = $base;
		while (!$unique) {
			$unique = true;
			foreach ($_adsensem['ads'] as $ad) {
				if ($ad->name == $name) {
					$unique = false;
					break;
				}
			}
			if (!$unique) {
				$name = $base . '-' . $i++;
			}
		}
		
		return $name;
	}

	function validate_id($id)
	{
		global $_adsensem;
		$validId = false;
		
		if (is_numeric($id) && !empty($_adsensem['ads'][$id])) {
			$validId = $id;
		}
		
		return $validId;
	}
	
	function generate_id()
	{
		global $_adsensem;
		if (empty($_adsensem['next_ad_id'])) {
			$_adsensem['next_ad_id'] = 1;
		}
		
		$nextId = $_adsensem['next_ad_id'];
		$_adsensem['next_ad_id'] = $nextId + 1;
		
		return $nextId;
	}

	function _save_ad($target)
	{
		global $_adsensem;
		
		$id = adsensem_admin::validate_id($target);
		$_adsensem['ads'][$id]->id = $id; // Update internal ID reference
		$_adsensem['ads'][$id]->save_settings();
		update_option('plugin_adsensem', $_adsensem);
	}
	
	function _save_network($target)
	{
		global $_adsensem;
		
		if (!empty($_adsensem['defaults'][$target])) {
			$networkAd = new $target;
			$networkAd->save_defaults();
			update_option('plugin_adsensem', $_adsensem);
		}
	}
	
	function _save_settings()
	{
		global $_adsensem;
		
		$_adsensem['settings']['openx-market'] = !empty($_POST['adsensem-openx-market']);
		$_adsensem['settings']['openx-market-cpm'] = !empty($_POST['adsensem-openx-market-cpm']) ? OX_Tools::sanitize_number($_POST['adsensem-openx-market-cpm']) : '0.20';
	}
	function _copy_ad($target)
	{
		global $_adsensem;
		
		//Copy selected advert
		$id = adsensem_admin::validate_id($target);
		$newid = adsensem_admin::generate_id();
		$_adsensem['ads'][$newid] = adsensem_clone($_adsensem['ads'][$id]); //clone() php4 hack
		$_adsensem['ads'][$newid]->id = $newid; //update internal id reference
		unset($_adsensem['ads'][$newid]->p['revisions']);

		$_adsensem['ads'] = OX_Tools::sort($_adsensem['ads']);
		$_adsensem['ads'][$newid]->add_revision();
		update_option('plugin_adsensem', $_adsensem);
		
		return $newid;
	}
	
	function _delete_ad($target)
	{
		global $_adsensem;
		
		$id = adsensem_admin::validate_id($target);
		unset($_adsensem['ads'][$id]);
		update_option('plugin_adsensem', $_adsensem);
	}
	
	function _import_ad($target)
	{
		global $_adsensem;
		global $_adsensem_networks;
		
		if (empty($target)) {  // Cut'n'paste code rather than selecting an adsense classic function
			//We're attempting to import code
			$imported = false;
			$code = stripslashes($_POST['adsensem-code']);
			if (!empty($code)) {
				foreach ($_adsensem_networks as $network => $n) {
					if (call_user_func(array($network, 'import_detect_network'), $code)) {
						$ad = new $network;
						$ad->import_settings($code);
						$imported = true;
						break; //leave the foreach loop
					}
				}
			}
			
			// Not a pre-defined network - we will make it HTML code...
			if (!$imported) {
				$ad=new OX_Adnet_Html();
				$ad->import_settings($code);
			}
		} else {
			$ad = new $target;
		}
		
		$name = adsensem_admin::generate_name($ad->shortName);
		$id = adsensem_admin::generate_id();
		$ad->name = $name;
		$ad->id = $id;
		$_adsensem['ads'][$id] = $ad;
		
		OX_Tools::sort($_adsensem['ads']);
		$_adsensem['ads'][$id]->add_revision();
		update_option('plugin_adsensem', $_adsensem);
		
		return $id;
	}
	
	function _set_active($id, $active)
	{
		global $_adsensem;
		
		//Set selected advert as active
		$id = adsensem_admin::validate_id($id);
		
		if ($_adsensem['ads'][$id]->active != $active) {
			$_adsensem['ads'][$id]->active = $active;
			$_adsensem['ads'][$id]->add_revision();
			update_option('plugin_adsensem', $_adsensem);
		}
	}
		
	function _set_default($id)
	{
		global $_adsensem;
		
		//Set selected advert as active
		$id = adsensem_admin::validate_id($id);
		
		$name = $_adsensem['ads'][$id]->name;
		if ($name != $_adsensem['default-ad']) {
			$_adsensem['default-ad'] = $name;
			update_option('plugin_adsensem', $_adsensem);
		}
	}
	
	// turn on/off optimisation across openx for all ads
	function _set_auto_optimise($active)
	{
		global $_adsensem;
		
		foreach ($_adsensem['ads'] as $id => $ad) {
			$_adsensem['ads'][$id]->p['openx-market'] = ($active) ? 'yes' : 'no';
		}
		foreach ($_adsensem['defaults'] as $network => $settings) {
			$_adsensem['defaults']['openx-market'] = ($active) ? 'yes' : 'no';
		}
		update_option('plugin_adsensem', $_adsensem);
	}
		
	function admin_manage()
	{
		
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		global $_adsensem_networks;
		
		$update_adsensem = false;
		
		$mode = !empty($_POST['adsensem-mode']) ? OX_Tools::sanitize_key($_POST['adsensem-mode']) : null;
		$action = !empty($_POST['adsensem-action']) ? OX_Tools::sanitize_key($_POST['adsensem-action']) : null;
		$target = !empty($_POST['adsensem-action-target']) ? OX_Tools::sanitize_key($_POST['adsensem-action-target']) : null;
		$targets = !empty($_POST['adsensem-action-targets']) ? OX_Tools::sanitize_key($_POST['adsensem-action-targets']) : null;
		$filter = null;
		
		switch ($action) {
			
			case 'activate' :
				adsensem_admin::_set_active($target, true);
				break;
			
			case 'apply' :
				if (is_numeric($target)) {
					adsensem_admin::_save_ad($target);
					$mode = 'edit_ad';
				} else {
					adsensem_admin::_save_network($target);
					$mode = 'edit_network';
				}
				break;
			
			case 'cancel' :
				$mode = 'list_ads';
				break;
			
			case 'clear' :
				break;
			
			case 'copy' :
				if (!empty($target)) {
					$target = adsensem_admin::_copy_ad($target);
				} elseif (!empty($targets)) {
					foreach ($targets as $target) {
						adsensem_admin::_copy_ad($target);
					}
				}
				break;
			
			case 'create' :
				$mode = 'create_ad';
				break;
			
			case 'deactivate' :
				adsensem_admin::_set_active($target, false);
				break;
			
			case 'default' :
				adsensem_admin::_set_default($target);
				break;
			
			case 'delete' :
				if (!empty($target)) {
					adsensem_admin::_delete_ad($target);
				} elseif (!empty($targets)) {
					foreach ($targets as $target) {
						adsensem_admin::_delete_ad($target);
					}
				}
				$mode = !empty($_adsensem['ads']) ? 'list_ads' : 'create_ad';
				break;
			
			case 'edit' :
				$mode = is_numeric($target) ? 'edit_ad' : 'edit_network';
				break;
			
			case 'filter' :
				if (!empty($_POST['adsensem-filter-active'])) {
					$filter['active'] = $_POST['adsensem-filter-active'];
				}
				if (!empty($_POST['adsensem-filter-network'])) {
					$filter['network'] = $_POST['adsensem-filter-network'];
				}
				break;
			
			case 'import' :
				$target = adsensem_admin::_import_ad($target);
				$mode = 'edit_ad';
				break;
			
			case 'list' :
				$mode = 'list_ads';
				break;
			
			case 'save' :
				if (is_numeric($target)) {
					adsensem_admin::_save_ad($target);
				} else {
					adsensem_admin::_save_network($target);
				}
				$mode = 'list_ads';
				break;
			
			default :
				$mode = !empty($_adsensem['ads']) ? 'list_ads' : 'create_ad';
				break;
		}
		
		switch ($mode) {
			case 'list_ads' :
				include_once(ADS_PATH . '/Template/' . TEMPLATE . '/ListAds.php');
				Template_ListAds::display($target, $filter);
				break;
			
			case 'create_ad' :
				include_once(ADS_PATH . '/Template/' . TEMPLATE . '/CreateAd.php');
				$templateClass = new Template_CreateAd();
				$templateClass->display($target);
				break;
			
			case 'edit_ad' :
				$shortName = $_adsensem['ads'][$target]->shortName;
				if (file_exists(ADS_PATH . '/Template/' . TEMPLATE . '/EditAd/' . $shortName . '.php')) {
					include_once(ADS_PATH . '/Template/' . TEMPLATE . '/EditAd/' . $shortName . '.php');
					$templateClassName = 'Template_EditAd_' . $shortName;
				} else {
					include_once(ADS_PATH . '/Template/' . TEMPLATE . '/EditAd.php');
					$templateClassName = 'Template_EditAd';
				}
				$templateClass = new $templateClassName;
				$templateClass->display($target);
				break;
			
			case 'edit_network' :
				$networkAd = new $target;
				$shortName = $networkAd->shortName;
				if (file_exists(ADS_PATH . '/Template/' . TEMPLATE . '/EditNetwork/' . $shortName . '.php')) {
					include_once(ADS_PATH . '/Template/' . TEMPLATE . '/EditNetwork/' . $shortName . '.php');
					$templateClassName = 'Template_EditNetwork_' . $shortName;
				} else {
					include_once(ADS_PATH . '/Template/' . TEMPLATE . '/EditNetwork.php');
					$templateClassName = 'Template_EditNetwork';
				}
				$templateClass = new $templateClassName;
				$templateClass->display($target);
				break;
			
		}
	}
	
	
/* 		Define basic settings for the Adsense Manager - for block control use admin_manage */

	function admin_options()
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;

		if ($_POST['adsensem-action'] == 'save') {
			adsensem_admin::_save_settings();
			update_option('plugin_adsensem', $_adsensem);
		}
		include_once(ADS_PATH . '/Template/' . TEMPLATE . '/Settings.php');
		Template_Settings::display();
	}


/*
		STARTUP SCRIPTS
		Initialised at startup to provide functions to the plugin etc.
*/
	function add_header_script()
	{
		if ($_GET['page']=='advertising-manager-manage-ads') {
?><link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/advertising-manager/advertising-manager.css" />
<script src="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/advertising-manager/advertising-manager.js"></script>
<?php
		}
	}
	
	/* Add button to simple editor to include Google Adsense code */
	function admin_callback_editor()
	{
		global $_adsensem;

			//Editor page, so we need to output this editor button code
			if (strpos($_SERVER['REQUEST_URI'], 'post.php') ||
				strpos($_SERVER['REQUEST_URI'], 'post-new.php') ||
				strpos($_SERVER['REQUEST_URI'], 'page.php') ||
				strpos($_SERVER['REQUEST_URI'], 'page-new.php') ||
				strpos($_SERVER['REQUEST_URI'], 'bookmarklet.php')) {
?>			<script language="JavaScript" type="text/javascript">
			<!--
				var ed_adsensem = document.createElement("select");
				ed_adsensem.setAttribute("onchange", "add_adsensem(this)");
				ed_adsensem.setAttribute("class", "ed_button");
				ed_adsensem.setAttribute("title", "Select Google Adsense to Add to Content");
				ed_adsensem.setAttribute("id", "ed_adsensem");					
				adh = document.createElement("option");
				adh.value='';
				adh.innerHTML='Google Adsense...';
				adh.style.fontWeight='bold';
				ed_adsensem.appendChild(adh);

				def = document.createElement("option");
				def.value='';
				def.innerHTML='Default Ad';

				ed_adsensem.appendChild(def);
<?php
		if (sizeof($_adsensem['ads']) != 0) {
			foreach($_adsensem['ads'] as $name => $ad) {
?>				var opt = document.createElement("option");
				opt.value='<?php echo $name; ?>';
				opt.innerHTML='#<?php echo $name; ?>';
				ed_adsensem.appendChild(opt);
<?php
			}
		}
?>				document.getElementById("ed_toolbar").insertBefore(ed_adsensem, document.getElementById("ed_spell"));
				/* Below is a Kludge for IE, which causes it to re-read the state of onChange etc. set above. Tut tut tut */
				if (navigator.appName == 'Microsoft Internet Explorer') {
					document.getElementById("ed_toolbar").innerHTML=document.getElementById("ed_toolbar").innerHTML; 
				}
				
			    function add_adsensem(element)
			    {
					if(element.selectedIndex!=0){
	
					if(element.value=='')
						{adsensem_code = '[ad]';}
					else
						{adsensem_code = '[ad#' + element.value + ']';}

					contentField = document.getElementById("content");
					if (document.selection && !window.opera) {
						// IE compatibility
						contentField.value += adsensem_code;
					} else
					if (contentField.selectionStart || contentField.selectionStart == '0') {

						var startPos = contentField.selectionStart;
						var endPos = contentField.selectionEnd;
						contentField.value = contentField.value.substring(0, startPos) + adsensem_code + contentField.value.substring(endPos, contentField.value.length);

					} else {

						contentField.value += adsensem_code;
					}
						element.selectedIndex=0;

					}
				}
			// -->
			</script>
<?php
			}
		}
	}
?>