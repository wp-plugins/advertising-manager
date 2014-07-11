<?php
class Advman_Template_Edit
{
	function display($ad, $nw = false)
	{
?>
<div class="wrap">
<?php if ($nw): ?>
	<h2><?php echo __('Edit Network: ', 'advman') . $ad->network_name; ?></h2>
<?php else: ?>
    <h2><?php _e('Edit Ad', 'advman'); ?> <a href="admin.php?page=advman-ad-new" class="add-new-h2"><?php _e('Add New', 'advman'); ?></a></h2>
<?php endif; ?>
	<form method="post">
	<input type="hidden" id="advman-action" name="action">
<?php
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );  
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">

	<div id="side-info-column" class="inner-sidebar">
<?php
		$side_meta_boxes = do_meta_boxes('advman', 'side', $ad);
?>	</div><!-- side-info-column -->
	<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : ''; ?>">
	<div id="post-body-content" class="has-sidebar-content">
<?php
		// Title
		$this->display_title($ad, $nw);
		// Show normal boxes
		do_meta_boxes('advman','normal',$ad);
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
	
	function display_title($ad, $nw = false)
	{
if (!$nw): ?>
<div id="titlediv">
<div id="titlewrap">
	<input type="text" name="advman-name" size="30" value="<?php echo $ad->name; ?>" id="title" autocomplete="off" />
</div><!-- titlewrap -->
<div class="inside">
	<span style="font-size:small;color:gray;"><?php _e('Enter the name for this ad.', 'advman'); ?> <?php _e('Ads with the same name will rotate according to their relative weights.', 'advman'); ?></span>
</div><!-- inside -->
</div><!-- titlediv -->
<?php endif;
	}
	
	function display_advanced($ad)
	{
?><h2><?php _e('Advanced Options', 'advman'); ?></h2>
<?php		
	}
}
?>