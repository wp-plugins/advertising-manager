<?php
require_once(ADVMAN_LIB . '/Tools.php');
require_once(ADVMAN_TEMPLATE_PATH . '/Metabox.php');

class Advman_Template_Edit
{
	function display($ad)
	{
		list($last_user, $last_timestamp, $last_timestamp2) = Advman_Tools::get_last_edit($ad);
		
?>
<div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2><?php printf(__('Edit Settings for %s Ad:', 'advman'), $ad->network_name); ?> <span class="<?php echo strtolower($ad->network); ?>"><?php echo "[{$ad->id}] " . $ad->name; ?></span></h2>
	<form action="" method="post" id="advman-form" enctype="multipart/form-data">
	<input type="hidden" name="advman-mode" id="advman-mode" value="edit_ad">
	<input type="hidden" name="advman-action" id="advman-action">
	<input type="hidden" name="advman-action-target" id="advman-action-target" value="<?php echo $ad->id; ?>">
<?php  
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );  
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
?>
	<div id="poststuff" class="metabox-holder">

	<div id="side-info-column" class="inner-sidebar">
<?php
		$side_meta_boxes = do_meta_boxes('advman', 'side', $ad);
?>	</div><!-- side-info-column -->
	<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : ''; ?>">
	<div id="post-body-content" class="has-sidebar-content">
<?php
		// Title
		$this->display_title($ad);
		// Show normal boxes
		do_meta_boxes('advman','main',$ad);
		// Show advanced screen
		$this->display_advanced($ad);
		// Show advanced boxes
		do_meta_boxes('advman','advanced',$ad);
?>	</div><!-- post-body-content -->
	</div><!-- post-body -->
	<br class="clear" />
	</div><!-- poststuff -->
	</form>
	</div><!-- wrap -->
<?php
	}
	
	function display_title($ad)
	{
?><div id="titlediv">
<div id="titlewrap">
	<input type="text" name="advman-name" size="30" value="<?php echo $ad->name; ?>" id="title" autocomplete="off" />
</div><!-- titlewrap -->
<div class="inside">
	<span style="font-size:x-small;color:gray;"><?php _e('Enter the name for this ad.', 'advman'); ?> <?php _e('Ads with the same name will rotate according to their relative weights.', 'advman'); ?></span>
</div><!-- inside -->
</div><!-- titlediv -->
<?php
	}
	
	function display_advanced($ad)
	{
?><h2><?php _e('Advanced Options', 'advman'); ?></h2>
<?php		
	}
}
?>