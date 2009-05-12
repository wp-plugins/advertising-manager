<?php
/*
Plugin Name: Advertising Manager
PLugin URI: http://code.openx.org/projects/show/advertising-manager
Description: Control and arrange your Advertising and Referral blocks on your Wordpress blog. With Widget and inline post support, integration with all major ad networks.
Author: Scott Switzer, Martin Fitzpatrick
Version: 3.3.16
Author URI: http://www.switzer.org/
*/

// Show notices (DEBUGGING ONLY)
//error_reporting(E_ALL);

// Load Localisation Plug-in
load_plugin_textdomain('advman', false, 'advertising-manager/languages');

// DEFINITIONS
@define("ADVMAN_VERSION", "3.3.16");
@define('ADVMAN_PATH', dirname(__FILE__));
@define('ADVMAN_URL', get_bloginfo('wpurl') . '/wp-content/plugins/advertising-manager');

global $wp_version;
$advman_template = (version_compare($wp_version,"2.7-alpha", "<")) ? 'WP26' : 'WP27';
@define('ADVMAN_TEMPLATE_PATH', ADVMAN_PATH . '/Template/' . $advman_template);

// INCLUDES
if ($advman_handle = opendir(ADVMAN_PATH . '/OX/Adnet/')) {
    while (false !== ($advman_file = readdir($advman_handle))) {
		// Make sure that the first character does not start with a '.' (omit hidden files like '.', '..', '.svn', etc.)
		if ($advman_file[0] != '.') {
			require_once(ADVMAN_PATH . '/OX/Adnet/' . $advman_file);
		}
    }
    closedir($advman_handle);
}
require_once(ADVMAN_PATH . '/OX/Tools.php');

// DATA
global $_advman, $_advman_notices, $_advman_counter;
$_advman = get_option('plugin_adsensem');
$_advman_notices = array();
$_advman_counter = array();

class advman
{
	/**
	 * The init function is called upon the 'plugins_loaded' action
	 */
	function init()
	{
		global $_advman;
		
		//Only run main site code if setup & functional
		if (OX_Tools::is_data_valid()) {
			// Add a filter for displaying an ad in the content
			add_filter('the_content', array('advman','filter_ads'));
			// Add an action when the wordpress footer displays
			add_action('wp_footer', array('advman','footer'));
			add_action('admin_menu', array('advman','init_admin'));
			add_action('widgets_init',  array('advman','init_widgets'), 1);
			
		}
		
		// Sync with OpenX
		OX_Tools::sync();
	}
	
	/**
	 * Initialise the admin screen
	 */
	function init_admin()
	{
		//Pull in the admin functions before triggering
		require_once(ADVMAN_PATH . '/class-admin.php');
		advman_admin::init_admin();
	}

	function major_version($v)
	{
		$mv=explode('.', $v);
		return $mv[0]; //Return major version
	}
		
	function init_widgets()
	{
		global $_advman;
		
		/* SITE SECTION: WIDGET DISPLAY CODE
		/* Add the blocks to the Widget panel for positioning WP2.2+*/
		
		if (OX_Tools::is_data_valid()) {
			if (!empty($_advman['ads'])) {
			    $widgets = array();
			    foreach ($_advman['ads'] as $id => $ad) {
				if (!empty($ad->name)) {
				    $widgets[$ad->name] = $ad;
				}
			    }
			    foreach ($widgets as $name => $ad)
			    {
				$n = __('Ad: ', 'advman') . $ad->name;
				$description = __('An ad from the Advertising Manager plugin');
				$args = array(
				    'name' => $n,
				    'description' => $description,
				    'width' => $ad->get('width', true),
				    'height' => $ad->get('height', true),
				);
				if (function_exists('wp_register_sidebar_widget')) {
					//$id, $name, $output_callback, $options = array()
					wp_register_sidebar_widget("advman-$name", "Ad#$name", array('advman','widget'), $args, $name);
//					wp_register_widget_control("advman-$name", "Ad#$name", array('advman','widget_control'), $args, $name); 
				} elseif (function_exists('register_sidebar_module') ) {
					register_sidebar_module("Ad #$name", 'advman_sbm_widget', "advman-$name", $args );
//					register_sidebar_module_control("Ad #$name", array('advman','widget_control'), "advman-$name");
				}			
			    }
			}
		}
	}
	
	function add_notice($action,$text,$confirm=false)
	{
		global $_advman;
		$_advman['notices'][$action]['text'] = $text;
		$_advman['notices'][$action]['confirm'] = $confirm;
	}
	
	function remove_notice($action)
	{
		global $_advman;
		if (!empty($_advman['notices'][$action])) {
			unset($_advman['notices'][$action]); //=false;
		}
	}
	function revert_db()
	{
	    global $_advman;
	    
	    $version = OX_Tools::sanitize_number($_REQUEST['advman-revert-db']);
	    $backup = get_option('plugin_adsensem_backup');
	    if (!empty($backup[$version])) {
		$_advman = $backup[$version];
		update_option('plugin_adsensem',$_advman);
		if (!empty($_REQUEST['advman-block-upgrade'])) {
		    die();
		}
	    } else {
		echo __('It looks like you are trying to load a backup of Advertising Manager.  The available versions are:') . '<br />';
		foreach (array_keys($backup) as $key) {
		    echo $key . '<br />';
		}
		echo '<br /><br /><br />';
		echo __('Here is a printout of each version:') . '<br />';
		foreach ($backup as $key => $data) {
		    echo "ADVMAN VERSION $key:<br /><pre>";
		    print_r($data);
		    echo '</pre><br /><br /><br />';
		}
		die();
	    }
	}
	
	// This is the function that outputs advman widget.
	function widget($args,$n='')
	{
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args); //nb. $name comes out of this, hence the use of $n
		
		global $_advman;
      
		//If name not passed in (Sidebar Modules), extract from the widget-id (WordPress Widgets)
		if ($n=='') {
			$n = substr($args['widget_id'],9);   //Chop off beginning advman- bit
		}
		
		if ($n == 'default-ad') {
		    $n = $_advman['default-ad'];
		}
		
		$ad = advman::select_ad($n);
		
		if (!empty($ad) && $ad->is_available()) {
			echo $before_widget;

			if($ad->title != '') {
				echo $before_title . $ad->title . $after_title;
			}
			advman::update_counters($ad);
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
		global $_advman;
		global $_advman_counter;
		
		if ($matches[1] == '') { /* default ad */
			$matches[1] = $_advman['default-ad'];
		}
		
		$ad = advman::select_ad($matches[1]);
		if (!empty($ad)) {
			advman::update_counters($ad);
			return $ad->get_ad();
		}
		return '';
	}
	
	function select_ad($name)
	{
		global $_advman;
		
		// Find the ads which match the name
		$ads = array();
		$totalWeight = 0;
		foreach ($_advman['ads'] as $id => $ad) {
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
		global $_advman;
		if (!empty($_advman['default-ad'])) {
			$content = preg_replace_callback(array("/<!--adsense-->/","/<!--am-->/","/\[ad\]/"),array('advman','filter_ad_callback'),$content);
		}
		
		$content = preg_replace_callback(array("/<!--adsense#(.*?)-->/","/<!--am#(.*?)-->/","/\[ad#(.*?)\]/"),array('advman','filter_ad_callback'),$content);
		
		return $content;
	}
	
	function update_counters($ad)
	{
		global $_advman_counter;
		
		if (!empty($ad)) {
			if (empty($_advman_counter['id'][$ad->id])) {
				$_advman_counter['id'][$ad->id] = 1;
			} else {
				$_advman_counter['id'][$ad->id]++;
			}
			
			if (empty($_advman_counter['network'][$ad->network])) {
				$_advman_counter['network'][$ad->network] = 1;
			} else {
				$_advman_counter['network'][$ad->network]++;
			}
		}
	}
}

// SHOW ADS
if (!function_exists('adsensem_ad')) {
	function adsensem_ad($name = false)
	{
		return advman_ad($name);
	}
}

function advman_ad($name = false)
{
	global $_advman;
	
	if (empty($name)) { /* default ad */
		$name = $_advman['default-ad'];
	}
	
	$ad = advman::select_ad($name);
	if (!empty($ad)) {
		advman::update_counters($ad);
		echo $ad->get_ad();
	}
}

// SHOW AN AD BY ITS NAME
if (!empty($_REQUEST['advman-ad-name'])) {
	$advman_name = OX_Tools::sanitize_key($_REQUEST['advman-ad-name']);
	advman_ad($advman_name);
	die(0);
}

// SHOW AN AD BY ID
if (!empty($_REQUEST['advman-ad-id'])) {
	$advman_id = OX_Tools::sanitize_number($_REQUEST['advman-ad-id']);
	if (!empty($_advman['ads'][$advman_id])) {
		$advman_ad = $_advman['ads'][$advman_id];
		advman::update_counters($advman_ad);
		echo $advman_ad->get_ad();
	}
	die(0);
}

// END
if (is_admin()) {
	require_once(ADVMAN_PATH . '/class-admin.php');

	// Revert to a previous version of database
	if (isset($_REQUEST['advman-revert-db'])) {
	    advman::revert_db();
	}
	
	/* PRE-OUTPUT PROCESSING - e.g. NOTICEs (upgrade-adsense-deluxe) */
	if (!empty($_POST['advman-mode'])) {
		$advman_mode = OX_Tools::sanitize_key($_POST['advman-mode']);
		if ($advman_mode == 'notice') {
			$advman_action = OX_Tools::sanitize_key($_POST['advman-action']);
			switch ($advman_action) {
				case 'upgrade adsense-deluxe':
					if ($_POST['advman-notice-confirm-yes']) {
						require_once(ADVMAN_PATH . '/class-upgrade.php');
						advman_upgrade::adsense_deluxe_to_3_0();
						advman::remove_notice('upgrade adsense-deluxe');
					} else {
						advman::remove_notice('upgrade adsense-deluxe');
					}
					update_option('plugin_adsensem', $_advman);
					break;	
				case 'optimise':
					$advman_yes = isset($_POST['advman-notice-confirm-yes']);
					if ($advman_yes) {
						advman_admin::_set_auto_optimise(true);
					} else {
						advman_admin::_set_auto_optimise(false);
					}
					advman::remove_notice('optimise');
					update_option('plugin_adsensem', $_advman);
					break;
				case 'activate advertising-manager':
					advman::remove_notice('activate advertising-manager');
					update_option('plugin_adsensem', $_advman);
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
	advman::widget($args,$k2sbm_current_module->options['name']);
}
/* SIDEBAR MODULES COMPATIBILITY FUNCTION */
if (class_exists('adsensem')) {
    advman::add_notice('disable adsensem', __('Please disable Adsense Manager before using Advertising Manager'), 'ok');
    update_option('plugin_adsensem', $_advman);
} else {
    add_action('plugins_loaded', array('advman','init'), 1);
}
?>