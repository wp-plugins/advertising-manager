<?php
/*
Plugin Name: Advertising Manager
PLugin URI: http://code.openx.org/projects/show/advertising-manager
Description: Control and arrange your Advertising and Referral blocks on your Wordpress blog. With Widget and inline post support, integration with all major ad networks.
Author: Scott Switzer, Martin Fitzpatrick
Version: 3.3.10
Author URI: http://www.mutube.com/
*/

// Show notices (DEBUGGING ONLY)
//error_reporting(E_ALL);

// Load all of the definitions that are needed for Advertising Manager
advman_init();
// Run init after the plugins are loaded
add_action('plugins_loaded', 'advman_run', 1);

function advman_init()
{
	@define('ADVMAN_VERSION', '3.3.10');
	@define('ADVMAN_PATH', dirname(__FILE__));
	@define('ADVMAN_LIB', ADVMAN_PATH . '/lib/Advman');
	@define('OX_LIB', ADVMAN_PATH . '/lib/OX');
	@define('ADVMAN_URL', get_bloginfo('wpurl') . '/wp-content/plugins/advertising-manager');

	// Get the template path
	global $wp_version;
	$version = (version_compare($wp_version,"2.7-alpha", "<")) ? 'WP2.6' : 'WP2.7';
	@define('ADVMAN_TEMPLATE_PATH', ADVMAN_PATH . "/lib/Advman/Template/{$version}");

	// Load the language file
	load_plugin_textdomain('advman', false, 'advertising-manager/languages');
	
	// Load all require files that are needed for Advertising Manager
	require_once(OX_LIB . '/Tools.php');
	require_once(OX_LIB . '/Swifty.php');
	require_once(ADVMAN_LIB . '/Dal.php');
	// First, get an instance of the ad engine
	global $advman_engine;
	$advman_engine = new OX_Swifty('Advman_Dal');
	// Next, load admin if needed
	if (is_admin()) {
		require_once(ADVMAN_LIB . '/Admin.php');
	}
}

function advman_run()
{
	global $advman_engine;
	
	// An ad is being requested by its name
	if (!empty($_REQUEST['advman-ad-name'])) {
		$name = OX_Tools::sanitize($_REQUEST['advman-ad-name'], 'key');
		$ad = $advman_engine->selectAd($name);
		if (!empty($ad)) {
			echo $ad->display();
		}
		die(0);
	}
	
	// An ad is being requested by its id
	if (!empty($_REQUEST['advman-ad-id'])) {
		$id = OX_Tools::sanitize($_REQUEST['advman-ad-id'], 'number');
		$ad = $advman_engine->getAd($id);
		if (!empty($ad)) {
			echo $ad->display();
		}
		die(0);
	}

	/* REVERT TO PREVIOUS BACKUP OF AD DATABASE */
	if (!empty($_REQUEST['advman-revert-db'])) {
		$version = OX_Tools::sanitize($_REQUEST['advman-revert-db'], 'number');
		$backup = get_option('plugin_adsensem_backup');
		if (!empty($backup[$version])) {
			$_advman = $backup[$version];
			update_option('plugin_adsensem',$_advman);
			if (!empty($_REQUEST['advman-block-upgrade'])) {
				die();
			}
		}
	}

	// Add a filter for displaying an ad in the content
	add_filter('the_content', 'advman_filter_content');
	// Widgets are initialised
	add_action('widgets_init',  'advman_widgets_init', 1);
	// Add an action when the wordpress footer displays
	add_action('wp_footer', 'advman_footer');
	// If admin, initialise the Admin functionality	
	if (is_admin()) {
		add_action('admin_menu', array('Advman_Admin','init'));
	}
}

function advman_widgets_init()
{
	global $advman_engine;
	
	$ads = $advman_engine->getAds();
	
	if (!empty($ads)) {
		foreach ($ads as $id => $ad) {
			$name = $ad->name;
			$args = array('name' => $name, 'height' => $ad->get('height'), 'width' => $ad->get('width'));
			if (function_exists('wp_register_sidebar_widget')) {
				wp_register_sidebar_widget("advman-$name", "Ad#$name", 'advman_widget', $args, $name);
			} elseif (function_exists('register_sidebar_module') ) {
				register_sidebar_module("Ad #$name", 'advman_sbm_widget', "advman-$name", $args );
			}
		}
	}
}


// This is the function that outputs advman widget.
function advman_widget($args,$n='')
{
	global $advman_engine;
	
	$ads = $advman_engine->getAds();

	// $args is an array of strings that help widgets to conform to
	// the active theme: before_widget, before_title, after_widget,
	// and after_title are the array keys. Default tags: li and h2.
	extract($args); //nb. $name comes out of this, hence the use of $n
	
	//If name not passed in (Sidebar Modules), extract from the widget-id (WordPress Widgets)
	if ($n=='') {
		$n = substr($args['widget_id'],9);   //Chop off beginning advman- bit
	}
	
	if ($n !== 'default-ad') {
		$ad = $advman_engine->selectAd($n);
	} else {
		$ad = $advman_engine->selectAd();  // select the default
	}
	
	if (!empty($ad)) {
		echo $before_widget;

		if($ad->title != '') {
			echo $before_title . $ad->title . $after_title;
		}

		echo $ad->display(); //Output the selected ad

		echo $after_widget;
	}
}

/**
 * Sidebar module compatibility function
 */
function advman_sbm_widget($args)
{
	global $k2sbm_current_module;
	advman_widget($args,$k2sbm_current_module->options['name']);
}
  
/* This filter parses post content and replaces markup with the correct ad,
<!--adsense#name--> for named ad or <!--adsense--> for default */
function advman_filter_content($content)
{
	$patterns = array(
		'/<!--adsense-->/',
		'/<!--adsense#(.*?)-->/',
		'/<!--am-->/',
		'/<!--am#(.*?)-->/',
		'/\[ad\]/',
		'/\[ad#(.*?)\]/',
	);
	
	return preg_replace_callback($patterns, 'advman_filter_content_callback', $content);
}
	
function advman_filter_content_callback($matches)
{
	global $advman_engine;
	
	$ad = $advman_engine->selectAd($matches[1]);
	if (!empty($ad)) {
		return $ad->display();
	}
	return '';
}
	
// Backwards compatibility with adsense-manager
if (!function_exists('adsensem_ad')) {
	function adsensem_ad($name = false)
	{
		return advman_ad($name);
	}
}

function advman_ad($name = false)
{
	global $advman_engine;
	
	$ad = $advman_engine->selectAd($name);
	if (!empty($ad)) {
		echo $ad->display();
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
?>