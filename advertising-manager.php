<?php
/*
Plugin Name: Advertising Manager
PLugin URI: http://wordpress.org/extend/plugins/advertising-manager/
Description: Control and arrange your Advertising and Referral blocks on your Wordpress blog. With Widget and inline post support, integration with all major ad networks.
Author: Scott Switzer, Martin Fitzpatrick
Version: 3.3.4
Author URI: http://www.mutube.com/
*/

global $wp_version;
$template = (version_compare($wp_version,"2.7-alpha", "<")) ? 'WP26' : 'WP27';
@define('TEMPLATE', $template);

// Definitions
@define("ADSENSEM_VERSION", "3.3.4");
@define("AM_BRAND_NAME", "Advertising Manager");
@define("AM_BRAND_DEFAULT_NETWORK", "Adsense");
@define("AM_BRAND_ACTIVATE",'Earn even more with <a href="http://www.text-link-ads.com/?ref=55499" target="_blank">Text Link Ads</a> and <a href="http://www.inlinks.com/?ref=211569" target="_blank">InLinks!</a>');
//@define('ADS_PATH','/wp-content/plugins' . strrchr(dirname(__FILE__),'/') . "/");
@define('ADS_PATH', dirname(__FILE__));

// Ad Network Includes
// Traverse the Ad Network directory and include all files
if ($handle = opendir(ADS_PATH . '/OX/Adnet/')) {
    while (false !== ($file = readdir($handle))) {
		// Make sure that the first character does not start with a '.' (omit hidden files like '.', '..', '.svn', etc.)
		if ($file[0] != '.') {
			require_once(ADS_PATH . '/OX/Adnet/' . $file);
		}
    }
    closedir($handle);
}

$_adsensem = get_option('plugin_adsensem');
$_adsensem_notices = array();


// This function is for backwards compatibility.  Do we still need it?
if (!function_exists('adsensem_ad')) {
	function adsensem_ad($id = false)
	{
		global $_adsensem;
		if ($id === false) {
			$ad = $_adsensem['ads'][$_adsensem['default-ad']];
		} else {
			$ad = $_adsensem['ads'][$id];
		}
		if (is_object($ad)) {
			if ($ad->is_available()) {
				echo $ad->get_ad();
			} 
		}
	}
}



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
	
	/**
	 * Initialise the Adsensem array
	 */
	function get_initial_array()
	{
		$_adsensem = array();
		$_adsensem['ads'] = array();
		$_adsensem['next_ad_id'] = 1;
		$_adsensem['default-ad'] = '';
		$_adsensem['version'] = ADSENSEM_VERSION;
		
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
				$args = array('name' => $name, 'height' => $ad->p['height'], 'width' => $ad->p['width']);
				if (function_exists('wp_register_sidebar_widget')) {
					//$id, $name, $output_callback, $options = array()
					wp_register_sidebar_widget("adsensem-$name", "Ad#$name", array('adsensem','widget'), $args, $name);
//					wp_register_widget_control("adsensem-$name", "Ad#$name", array('adsensem','widget_control'), $args, $name); 
				} elseif (function_exists('register_sidebar_module') ) {
					register_sidebar_module("Ad #$name", 'adsensem_sbm_widget', "adsensem-$name", $args );
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
?>		<!-- Advertising Manager v<?php echo ADSENSEM_VERSION;?> (<?php timer_stop(1); ?> seconds.) -->
<?php
	}


	function filter_ad_callback($matches)
	{
		global $_adsensem;
		
		if ($matches[1] == '') { /* default ad */
			$matches[1] = $_adsensem['default-ad'];
		}
		
		// Find the ads which match the name
		$ads = array();
		$totalWeight = 0;
		foreach ($_adsensem['ads'] as $id => $ad) {
			if ( ($ad->name == $matches[1]) && ($ad->is_available()) ) {
				$ads[] = $ad;
				$totalWeight += $ad->p['weight'];
			}
		}
		// Pick the ad
		// Generate a number between 0 and 1
		$rnd = (mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX);
		// Loop through ads until the selected one is chosen
		$wt = 0;
		foreach ($ads as $ad) {
			$wt += $ad->p['weight'];
			if ( ($wt / $totalWeight) > $rnd) {
				// Display the ad
				return $ad->get_ad();
			}
		}
		return '';
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

/* SHOW ALTERNATE AD UNITS */
if ($_REQUEST['adsensem-show-ad']) {
?>	<html><body>
<?php
	adsensem_ad($_REQUEST['adsensem-show-ad']);
?>	</body></html>
<?php
	die(0);
}
/* END SHOW ALTERNATE AD UNITS */
// SHOW AN AD BY ID
if ($_REQUEST['adsensem-show-ad-id']) {
	if (!empty($_adsensem['ads'][$_REQUEST['adsensem-show-ad-id']])) {
		echo '<html><body>' . $_adsensem['ads'][$_REQUEST['adsensem-show-ad-id']]->get_ad() . '</body></html>';
	}
	die(0);
}

// END
if (is_admin()) {
	require_once('class-admin.php');

	/* REVERT TO PREVIOUS BACKUP OF AD DATABASE */
	if ($_REQUEST['adsensem-revert-db']){
		$backup=get_option('plugin_adsensem_backup');
		$_adsensem=$backup[$_REQUEST['adsensem-revert-db']];
		update_option('plugin_adsensem',$_adsensem);
		if ($_REQUEST['adsensem-block-upgrade']) {
			die();
		}
	}
	/* END REVERT TO PREVIOUS BACKUP OF AD DATABASE */
	
	
	/* PRE-OUTPUT PROCESSING - e.g. NOTICEs (upgrade-adsense-deluxe) */
	switch ($_POST['adsensem-mode'] . ':' . $_POST['adsensem-action']) {
		case 'notice:upgrade adsense-deluxe':
			if ($_POST['adsensem-notice-confirm-yes']) {
				require_once('class-upgrade.php');
				adsensem_upgrade::adsense_deluxe_to_3_0();
				adsensem_admin::remove_notice('upgrade adsense-deluxe');
			} else {
				adsensem_admin::remove_notice('upgrade adsense-deluxe');
			}
			break;	
		case 'notice:optimise':
			if ($_POST['adsensem-notice-confirm-yes']) {
				adsensem_admin::_set_auto_optimise(true);
			} else {
				adsensem_admin::_set_auto_optimise(false);
			}
			adsensem_admin::remove_notice('optimise');
			break;
		case 'notice:activate advertising-manager':
			adsensem_admin::remove_notice('activate advertising-manager');
			break;
	}
/* END PRE-OUTPUT PROCESSING */
}



/* SIDEBAR MODULES COMPATIBILITY FUNCTION */
function adsensem_sbm_widget($args)
{
	global $k2sbm_current_module;
	adsensem::widget($args,$k2sbm_current_module->options['name']);
}
/* SIDEBAR MODULES COMPATIBILITY FUNCTION */

add_action('plugins_loaded', array('adsensem','init'), 1);	
add_action('admin_menu', array('adsensem','init_admin'));
add_action('widgets_init',  array('adsensem','init_widgets'), 1);
?>