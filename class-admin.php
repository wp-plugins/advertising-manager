<?php
if (!ADVMAN_VERSION) {die();}

require_once(ADVMAN_PATH . '/OX/Tools.php');

function advman_clone($object)
{
	return version_compare(phpversion(), '5.0') < 0 ? $object : clone($object);
}

/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class advman_admin
{
	function init_admin()
	{
		global $_advman;
		global $wp_version;
		
		add_action('admin_head', array('advman_admin','add_header_script'));
		add_action('admin_footer', array('advman_admin','admin_callback_editor'));
		
		wp_enqueue_script('prototype');
		wp_enqueue_script('postbox');
		
		if (version_compare($wp_version,"2.7-alpha", '>')) {
			add_options_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 10, "advertising-manager-manage-ads", array('advman_admin','admin_manage'));
		} else {
			add_management_page(__('Advertising', 'advman'), __('Advertising', 'advman'), 10, "advertising-manager-manage-ads", array('advman_admin','admin_manage'));
		}
		add_action( 'admin_notices', array('advman_admin','admin_notices'), 1 );
		
		$email = get_option('admin_email');
		$user = get_current_user();
		$siteurl = get_option('siteurl');
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
		global $_advman;
		
		if (!empty($_advman['notices'])) {
			include_once(ADVMAN_TEMPLATE_PATH . '/Notice.php');
			Template_Notice::display($_advman['notices']);
		}
		
	}
	
	function generate_name($base = null)
	{
		global $_advman;
		
		if (empty($base)) {
			$base = 'ad';
		}
		
		// Generate a unique name if no name was specified
		$unique = false;
		$i = 1;
		$name = $base;
		while (!$unique) {
			$unique = true;
			foreach ($_advman['ads'] as $ad) {
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
		global $_advman;
		$validId = false;
		
		if (is_numeric($id) && !empty($_advman['ads'][$id])) {
			$validId = $id;
		}
		
		return $validId;
	}
	
	function generate_id()
	{
		global $_advman;
		if (empty($_advman['next_ad_id'])) {
			$_advman['next_ad_id'] = 1;
		}
		
		$nextId = $_advman['next_ad_id'];
		$_advman['next_ad_id'] = $nextId + 1;
		
		return $nextId;
	}

	function _save_ad($target)
	{
		global $_advman;
		
		$id = advman_admin::validate_id($target);
		$_advman['ads'][$id]->id = $id; // Update internal ID reference
		$_advman['ads'][$id]->save_settings();
		update_option('plugin_adsensem', $_advman);
	}
	
	function _save_network($target)
	{
		global $_advman;
		
		if (!empty($_advman['defaults'][$target])) {
			$networkAd = new $target;
			$networkAd->save_defaults();
			update_option('plugin_adsensem', $_advman);
		}
	}
	
	function _save_settings()
	{
		global $_advman;
		
		$_advman['settings']['openx-market'] = !empty($_POST['advman-openx-market']);
		$_advman['settings']['openx-market-cpm'] = !empty($_POST['advman-openx-market-cpm']) ? OX_Tools::sanitize_number($_POST['advman-openx-market-cpm']) : '0.20';
	}
	function _copy_ad($target)
	{
		global $_advman;
		
		//Copy selected advert
		$id = advman_admin::validate_id($target);
		$newid = advman_admin::generate_id();
		$_advman['ads'][$newid] = advman_clone($_advman['ads'][$id]); //clone() php4 hack
		$_advman['ads'][$newid]->id = $newid; //update internal id reference
		$_advman['ads'][$newid]->set('revisions', null); // remove any previous revisions

		$_advman['ads'] = OX_Tools::sort($_advman['ads']);
		$_advman['ads'][$newid]->add_revision();
		update_option('plugin_adsensem', $_advman);
		
		return $newid;
	}
	
	function _delete_ad($target)
	{
		global $_advman;
		
		$id = advman_admin::validate_id($target);
		unset($_advman['ads'][$id]);
		update_option('plugin_adsensem', $_advman);
	}
	
	function _import_ad($target)
	{
		global $_advman;
		global $_advman_networks;
		
		if (empty($target)) {  // Cut'n'paste code rather than selecting an adsense classic function
			//We're attempting to import code
			$imported = false;
			$code = stripslashes($_POST['advman-code']);
			if (!empty($code)) {
				foreach ($_advman_networks as $network => $n) {
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
		
		$name = advman_admin::generate_name($ad->shortName);
		$id = advman_admin::generate_id();
		$ad->name = $name;
		$ad->id = $id;
		$_advman['ads'][$id] = $ad;
		
		OX_Tools::sort($_advman['ads']);
		$_advman['ads'][$id]->add_revision();
		update_option('plugin_adsensem', $_advman);
		
		return $id;
	}
	
	function _set_active($id, $active)
	{
		global $_advman;
		
		//Set selected advert as active
		$id = advman_admin::validate_id($id);
		
		if ($_advman['ads'][$id]->active != $active) {
			$_advman['ads'][$id]->active = $active;
			$_advman['ads'][$id]->add_revision();
			update_option('plugin_adsensem', $_advman);
		}
	}
		
	function _set_default($id)
	{
		global $_advman;
		
		//Set selected advert as active
		$id = advman_admin::validate_id($id);
		
		$name = $_advman['ads'][$id]->name;
		if ($name != $_advman['default-ad']) {
			$_advman['default-ad'] = $name;
			update_option('plugin_adsensem', $_advman);
		}
	}
	
	// turn on/off optimisation across openx for all ads
	function _set_auto_optimise($active)
	{
		global $_advman;
		
		$market = ($active) ? 'yes' : 'no';
		foreach ($_advman['ads'] as $id => $ad) {
			$_advman['ads'][$id]->set('openx-market', $market);
		}
		foreach ($_advman['defaults'] as $network => $settings) {
			$_advman['defaults'][$network]['openx-market'] = $market;
		}
		update_option('plugin_adsensem', $_advman);
	}
		
	function admin_manage()
	{
		
		// Get our options and see if we're handling a form submission.
		global $_advman;
		global $_advman_networks;
		
		$update_advman = false;
		
		$mode = !empty($_POST['advman-mode']) ? OX_Tools::sanitize_key($_POST['advman-mode']) : null;
		$action = !empty($_POST['advman-action']) ? OX_Tools::sanitize_key($_POST['advman-action']) : null;
		$target = !empty($_POST['advman-action-target']) ? OX_Tools::sanitize_key($_POST['advman-action-target']) : null;
		$targets = !empty($_POST['advman-action-targets']) ? OX_Tools::sanitize_key($_POST['advman-action-targets']) : null;
		$filter = null;
		
		switch ($action) {
			
			case 'activate' :
				advman_admin::_set_active($target, true);
				break;
			
			case 'apply' :
				if (is_numeric($target)) {
					advman_admin::_save_ad($target);
					$mode = 'edit_ad';
				} else {
					advman_admin::_save_network($target);
					$mode = 'edit_network';
				}
				break;
			
			case 'cancel' :
				$mode = !empty($_advman['ads']) ? 'list_ads' : 'create_ad';
				break;
			
			case 'clear' :
				break;
			
			case 'copy' :
				if (!empty($target)) {
					$target = advman_admin::_copy_ad($target);
				} elseif (!empty($targets)) {
					foreach ($targets as $target) {
						advman_admin::_copy_ad($target);
					}
				}
				break;
			
			case 'create' :
				$mode = 'create_ad';
				break;
			
			case 'deactivate' :
				advman_admin::_set_active($target, false);
				break;
			
			case 'default' :
				advman_admin::_set_default($target);
				break;
			
			case 'delete' :
				if (!empty($target)) {
					advman_admin::_delete_ad($target);
				} elseif (!empty($targets)) {
					foreach ($targets as $target) {
						advman_admin::_delete_ad($target);
					}
				}
				$mode = !empty($_advman['ads']) ? 'list_ads' : 'create_ad';
				break;
			
			case 'edit' :
				$mode = is_numeric($target) ? 'edit_ad' : 'edit_network';
				break;
			
			case 'filter' :
				if (!empty($_POST['advman-filter-active'])) {
					$filter['active'] = $_POST['advman-filter-active'];
				}
				if (!empty($_POST['advman-filter-network'])) {
					$filter['network'] = $_POST['advman-filter-network'];
				}
				break;
			
			case 'import' :
				$target = advman_admin::_import_ad($target);
				$mode = 'edit_ad';
				break;
			
			case 'list' :
				$mode = 'list_ads';
				break;
			
			case 'save' :
				if ($mode == 'settings') {
					advman_admin::_save_settings();
				} else {
					if (is_numeric($target)) {
						advman_admin::_save_ad($target);
					} else {
						advman_admin::_save_network($target);
					}
					$mode = 'list_ads';
				}
				break;
			
			case 'settings' :
				$mode = 'settings';
				break;
			
			default :
				$mode = !empty($_advman['ads']) ? 'list_ads' : 'create_ad';
				break;
		}
		
		switch ($mode) {
			case 'list_ads' :
				include_once(ADVMAN_TEMPLATE_PATH . '/ListAds.php');
				$templateListAds = new Template_ListAds();
				$templateListAds->display($target, $filter);
				break;
			
			case 'create_ad' :
				include_once(ADVMAN_TEMPLATE_PATH . '/CreateAd.php');
				$templateClass = new Template_CreateAd();
				$templateClass->display($target);
				break;
			
			case 'edit_ad' :
				$shortName = $_advman['ads'][$target]->shortName;
				if (file_exists(ADVMAN_TEMPLATE_PATH . '/EditAd/' . $shortName . '.php')) {
					include_once(ADVMAN_TEMPLATE_PATH . '/EditAd/' . $shortName . '.php');
					$templateClassName = 'Template_EditAd_' . $shortName;
				} else {
					include_once(ADVMAN_TEMPLATE_PATH . '/EditAd.php');
					$templateClassName = 'Template_EditAd';
				}
				$templateClass = new $templateClassName;
				$templateClass->display($target);
				break;
			
			case 'edit_network' :
				$networkAd = new $target;
				$shortName = $networkAd->shortName;
				if (file_exists(ADVMAN_TEMPLATE_PATH . '/EditNetwork/' . $shortName . '.php')) {
					include_once(ADVMAN_TEMPLATE_PATH . '/EditNetwork/' . $shortName . '.php');
					$templateClassName = 'Template_EditNetwork_' . $shortName;
				} else {
					include_once(ADVMAN_TEMPLATE_PATH . '/EditNetwork.php');
					$templateClassName = 'Template_EditNetwork';
				}
				$templateClass = new $templateClassName;
				$templateClass->display($target);
				break;
			
			case 'settings' :
				include_once(ADVMAN_TEMPLATE_PATH . '/Settings.php');
				$templateClass = new Template_Settings();
				$templateClass->display($target);
				break;
			
		}
	}
	
	
/* 		Define basic settings for the Adsense Manager - for block control use admin_manage */

	function admin_options()
	{
		// Get our options and see if we're handling a form submission.
		global $_advman;

		if ($_POST['advman-action'] == 'save') {
			advman_admin::_save_settings();
			update_option('plugin_adsensem', $_advman);
		}
		include_once(ADVMAN_TEMPLATE_PATH . '/Settings.php');
		Template_Settings::display();
	}


/*
		STARTUP SCRIPTS
		Initialised at startup to provide functions to the plugin etc.
*/
	function add_header_script()
	{
		$page = !empty($_GET['page']) ? $_GET['page'] : '';
		if ($page == 'advertising-manager-manage-ads') {
?><link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/advertising-manager/advertising-manager.css" />
<script src="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/advertising-manager/advertising-manager.js"></script>
<?php
		}
	}
	
	/* Add button to simple editor to include Google Adsense code */
	function admin_callback_editor()
	{
		global $_advman;

			//Editor page, so we need to output this editor button code
			if (strpos($_SERVER['REQUEST_URI'], 'post.php') ||
				strpos($_SERVER['REQUEST_URI'], 'post-new.php') ||
				strpos($_SERVER['REQUEST_URI'], 'page.php') ||
				strpos($_SERVER['REQUEST_URI'], 'page-new.php') ||
				strpos($_SERVER['REQUEST_URI'], 'bookmarklet.php')) {
?>			<script language="JavaScript" type="text/javascript">
			<!--
				var ed_advman = document.createElement("select");
				ed_advman.setAttribute("onchange", "add_advman(this)");
				ed_advman.setAttribute("class", "ed_button");
				ed_advman.setAttribute("title", "Select Google Adsense to Add to Content");
				ed_advman.setAttribute("id", "ed_advman");					
				adh = document.createElement("option");
				adh.value='';
				adh.innerHTML='Google Adsense...';
				adh.style.fontWeight='bold';
				ed_advman.appendChild(adh);

				def = document.createElement("option");
				def.value='';
				def.innerHTML='Default Ad';

				ed_advman.appendChild(def);
<?php
		if (sizeof($_advman['ads']) != 0) {
			foreach($_advman['ads'] as $name => $ad) {
?>				var opt = document.createElement("option");
				opt.value='<?php echo $name; ?>';
				opt.innerHTML='#<?php echo $name; ?>';
				ed_advman.appendChild(opt);
<?php
			}
		}
?>				document.getElementById("ed_toolbar").insertBefore(ed_advman, document.getElementById("ed_spell"));
				/* Below is a Kludge for IE, which causes it to re-read the state of onChange etc. set above. Tut tut tut */
				if (navigator.appName == 'Microsoft Internet Explorer') {
					document.getElementById("ed_toolbar").innerHTML=document.getElementById("ed_toolbar").innerHTML; 
				}
				
			    function add_advman(element)
			    {
					if(element.selectedIndex!=0){
	
					if(element.value=='')
						{advman_code = '[ad]';}
					else
						{advman_code = '[ad#' + element.value + ']';}

					contentField = document.getElementById("content");
					if (document.selection && !window.opera) {
						// IE compatibility
						contentField.value += advman_code;
					} else
					if (contentField.selectionStart || contentField.selectionStart == '0') {

						var startPos = contentField.selectionStart;
						var endPos = contentField.selectionEnd;
						contentField.value = contentField.value.substring(0, startPos) + advman_code + contentField.value.substring(endPos, contentField.value.length);

					} else {

						contentField.value += advman_code;
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