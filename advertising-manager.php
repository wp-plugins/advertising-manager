<?php
/*
Plugin Name: Advertising Manager
PLugin URI: http://code.openx.org/projects/show/advertising-manager
Description: Control and arrange your Advertising and Referral blocks on your Wordpress blog. With Widget and inline post support, integration with all major ad networks.
Author: Scott Switzer, Martin Fitzpatrick
Version: 3.3.5
Author URI: http://www.mutube.com/
*/

// DEFINITIONS
@define("ADVMAN_VERSION", "3.3.5");
@define('ADVMAN_PATH', dirname(__FILE__));
global $wp_version;
$template = (version_compare($wp_version,"2.7-alpha", "<")) ? 'WP26' : 'WP27';
@define('ADVMAN_TEMPLATE_PATH', ADVMAN_PATH . '/Template/' . $template);

// INCLUDES
if ($handle = opendir(ADS_PATH . '/OX/Adnet/')) {
    while (false !== ($file = readdir($handle))) {
		// Make sure that the first character does not start with a '.' (omit hidden files like '.', '..', '.svn', etc.)
		if ($file[0] != '.') {
			require_once(ADS_PATH . '/OX/Adnet/' . $file);
		}
    }
    closedir($handle);
}
require_once(ADS_PATH . '/OX/Tools.php');

// DATA
$_adsensem = get_option('plugin_adsensem');
$_adsensem_notices = array();

class adsensem
{
	/**
	 * The init function is called upon the 'plugins_loaded' action
	 */
	function init()
	{
		global $_adsensem;
		
		//Only run main site code if setup & functional
		if (adsensem::setup_is_valid()) {
			// Add a filter for displaying an ad in the content
			add_filter('the_content', array('adsensem','filter_ads'));
			// Add an action when the wordpress footer displays
			add_action('wp_footer', array('adsensem','footer'));
			add_action('admin_menu', array('adsensem','init_admin'));
			add_action('widgets_init',  array('adsensem','init_widgets'), 1);

			if (version_compare($_adsensem['version'], ADVMAN_VERSION, '<')) {
				include_once('class-upgrade.php');
				
				//Backup cycle
				$backup = get_option('plugin_adsensem_backup');
				$backup[adsensem::major_version($_adsensem['version'])] = $_adsensem;
				update_option('plugin_adsensem_backup',$backup);
				unset($backup);
				
				adsensem_upgrade::go();
				update_option('plugin_adsensem', $_adsensem);
			}
			
		} else {
			// Get basic array
			$_adsensem = adsensem::get_initial_array();
			
			// Check to see if Adsense Deluxe should be upgraded
			$deluxe = get_option('acmetech_adsensedeluxe');
			if (is_array($deluxe)) {
				adsensem::add_notice('upgrade adsense-deluxe','Advertising Manager has detected a previous installation of <strong>Adsense Deluxe</strong>. Import settings?','yn');
			}
			
			update_option('plugin_adsensem', $_adsensem);
		}
		
		// Sync with OpenX
		adsensem::sync();
	}
	
	/**
	 * Initialise the admin screen
	 */
	function init_admin()
	{
		//Pull in the admin functions before triggering
		require_once('class-admin.php');
		adsensem_admin::init_admin();
	}

	/**
	 * Check to see if the $_adsensem array is valid
	 */
	function setup_is_valid()
	{
		global $_adsensem;
		if (is_array($_adsensem)) {
			if(is_array($_adsensem['ads'])) {
				return true;
			}
		}
		return false;
	}
	
	function major_version($v)
	{
		$mv=explode('.', $v);
		return $mv[0]; //Return major version
	}
		
	/**
	 * Initialise the Adsensem array
	 */
	function get_initial_array()
	{
		$_adsensem = array();
		$_adsensem['ads'] = array();
		$_adsensem['next_ad_id'] = 1;
		$_adsensem['default-ad'] = '';
		$_adsensem['version'] = ADVMAN_VERSION;
		
		return $_adsensem;
	}
	
	function init_widgets()
	{
		global $_adsensem;
		/* SITE SECTION: WIDGET DISPLAY CODE
		/* Add the blocks to the Widget panel for positioning WP2.2+*/
		
		if (!empty($_adsensem['ads'])) {
			foreach ($_adsensem['ads'] as $id => $ad) {
				$name = $ad->name;
				$args = array('name' => $name, 'height' => $ad->get('height', true), 'width' => $ad->get('width', true));
				if (function_exists('wp_register_sidebar_widget')) {
					//$id, $name, $output_callback, $options = array()
					wp_register_sidebar_widget("adsensem-$name", "Ad#$name", array('adsensem','widget'), $args, $name);
//					wp_register_widget_control("adsensem-$name", "Ad#$name", array('adsensem','widget_control'), $args, $name); 
				} elseif (function_exists('register_sidebar_module') ) {
					register_sidebar_module("Ad #$name", 'advman_sbm_widget', "adsensem-$name", $args );
//					register_sidebar_module_control("Ad #$name", array('adsensem','widget_control'), "adsensem-$name");
				}			
			}
		}
	}
	
	function sync()
	{
		global $_adsensem;
		
		if (empty($_adsensem['last-sync']) || (mktime(0,0,0) - $_adsensem['last-sync'] > 0) ) {
			$_adsensem['last-sync'] = mktime(0,0,0);
			update_option('plugin_adsensem', $_adsensem);
			// CALL OPENX SYNC!
		}
	}
	
	function add_notice($action,$text,$confirm=false)
	{
		global $_adsensem;
		$_adsensem['notices'][$action]['text'] = $text;
		$_adsensem['notices'][$action]['confirm'] = $confirm;
	}
	
	function remove_notice($action)
	{
		global $_adsensem;
		if (!empty($_adsensem['notices'][$action])) {
			unset($_adsensem['notices'][$action]); //=false;
		}
	}

	
	// This is the function that outputs adsensem widget.
	function widget($args,$n='')
	{
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args); //nb. $name comes out of this, hence the use of $n
		
		global $_adsensem;
      
		//If name not passed in (Sidebar Modules), extract from the widget-id (WordPress Widgets)
		if ($n=='') {
			$n = substr($args['widget_id'],9);   //Chop off beginning adsensem- bit
		}
		
		if ($n !== 'default-ad') {
			$ad = $_adsensem['ads'][$n];
		} else {
			$ad = $_adsensem['ads'][$_adsensem['default-ad']];
		}
		
		if ($ad->is_available()) {
			echo $before_widget;

			if($ad->title != '') {
				echo $before_title . $ad->title . $after_title;
			}

			echo $ad->get_ad(); //Output the selected ad

			echo $after_widget;
		}
	}
  
	/**
	 * Called when the Wordpress footer displays, and adds a comment in the HTML for debugging purposes
	 */
	function footer()
	{
?>		<!-- Advertising Manager v<?php echo ADVMAN_VERSION;?> (<?php timer_stop(1); ?> seconds.) -->
<?php
	}


	function filter_ad_callback($matches)
	{
		global $_adsensem;
		
		if ($matches[1] == '') { /* default ad */
			$matches[1] = $_adsensem['default-ad'];
		}
		
		$ad = adsensem::select_ad($matches[1]);
		if (!empty($ad)) {
			return $ad->get_ad();
		}
		return '';
	}
	
	function select_ad($name)
	{
		global $_adsensem;
		
		// Find the ads which match the name
		$ads = array();
		$totalWeight = 0;
		foreach ($_adsensem['ads'] as $id => $ad) {
			if ( ($ad->name == $name) && ($ad->is_available()) ) {
				$ads[] = $ad;
				$totalWeight += $ad->get('weight', true);
			}
		}
		// Pick the ad
		// Generate a number between 0 and 1
		$rnd = (mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX);
		// Loop through ads until the selected one is chosen
		$wt = 0;
		foreach ($ads as $ad) {
			$wt += $ad->get('weight', true);
			if ( ($wt / $totalWeight) > $rnd) {
				// Display the ad
				return $ad;
			}
		}
	}
		

	/* This filter parses post content and replaces markup with the correct ad,
	<!--adsense#name--> for named ad or <!--adsense--> for default */
	function filter_ads($content)
	{
		global $_adsensem;
		if (!empty($_adsensem['default-ad'])) {
			$content = preg_replace_callback(array("/<!--adsense-->/","/<!--am-->/","/\[ad\]/"),array('adsensem','filter_ad_callback'),$content);
		}
		
		$content = preg_replace_callback(array("/<!--adsense#(.*)-->/","/<!--am#(.*)-->/","/\[ad#(.*)\]/"),array('adsensem','filter_ad_callback'),$content);
		
		return $content;
	}
}

// SHOW ADS - OLDER VERSION
if (!function_exists('adsensem_ad')) {
	function adsensem_ad($name = false)
	{
		global $_adsensem;
		
		if (empty($name)) { /* default ad */
			$name = $_adsensem['default-ad'];
		}
		
		$ad = adsensem::select_ad($name);
		if (!empty($ad)) {
			echo $ad->get_ad();
		}
	}
}


/* SHOW ALTERNATE AD UNITS */
if (!empty($_REQUEST['adsensem-show-ad'])) {
	$name = OX_Tools::sanitize_name($_REQUEST['adsensem-show-ad']);
	echo '<html><body>';
	adsensem_ad($name);
	echo '</body></html>';
	die(0);
}
/* END SHOW ALTERNATE AD UNITS */
// SHOW AN AD BY ID
if (!empty($_REQUEST['adsensem-show-ad-id'])) {
	$id = OX_Tools::sanitize_number($_REQUEST['adsensem-show-ad-id']);
	if (!empty($_adsensem['ads'][$id])) {
		echo '<html><body>' . $_adsensem['ads'][$_REQUEST['adsensem-show-ad-id']]->get_ad() . '</body></html>';
	}
	die(0);
}

// END
if (is_admin()) {
	require_once('class-admin.php');

	/* REVERT TO PREVIOUS BACKUP OF AD DATABASE */
	if (!empty($_REQUEST['adsensem-revert-db'])) {
		$version = OX_Tools::sanitize_number($_REQUEST['adsensem-revert-db']);
		$backup = get_option('plugin_adsensem_backup');
		if (!empty($backup[$version])) {
			$_adsensem = $backup[$version];
			update_option('plugin_adsensem',$_adsensem);
			if (!empty($_REQUEST['adsensem-block-upgrade'])) {
				die();
			}
		}
	}
	/* END REVERT TO PREVIOUS BACKUP OF AD DATABASE */
	
	
	/* PRE-OUTPUT PROCESSING - e.g. NOTICEs (upgrade-adsense-deluxe) */
	if (!empty($_POST['adsensem-mode'])) {
		$mode = OX_Tools::sanitize_key($_POST['adsensem-mode']);
		if ($mode == 'notice') {
			$action = OX_Tools::sanitize_key($_POST['adsensem-action']);
			switch ($action) {
				case 'upgrade adsense-deluxe':
					if ($_POST['adsensem-notice-confirm-yes']) {
						require_once('class-upgrade.php');
						adsensem_upgrade::adsense_deluxe_to_3_0();
						adsensem::remove_notice('upgrade adsense-deluxe');
					} else {
						adsensem::remove_notice('upgrade adsense-deluxe');
					}
					update_option('plugin_adsensem', $_adsensem);
					break;	
				case 'optimise':
					$yes = isset($_POST['adsensem-notice-confirm-yes']);
					if ($yes) {
						adsensem_admin::_set_auto_optimise(true);
					} else {
						adsensem_admin::_set_auto_optimise(false);
					}
					adsensem::remove_notice('optimise');
					update_option('plugin_adsensem', $_adsensem);
					break;
				case 'activate advertising-manager':
					adsensem::remove_notice('activate advertising-manager');
					update_option('plugin_adsensem', $_adsensem);
					break;
			}
		}
	}
/* END PRE-OUTPUT PROCESSING */
}



/* SIDEBAR MODULES COMPATIBILITY FUNCTION */
function advman_sbm_widget($args)
{
	global $k2sbm_current_module;
	adsensem::widget($args,$k2sbm_current_module->options['name']);
}
/* SIDEBAR MODULES COMPATIBILITY FUNCTION */

add_action('plugins_loaded', array('adsensem','init'), 1);	
?>