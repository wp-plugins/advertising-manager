<?php
class Advman_Template_List
{
	function display($id = null)
	{
		global $advman_engine;
		$ads = $advman_engine->get_ads();
		$zones = $advman_engine->get_zones();
		
		$adCount = 0;
		$activeAdCount = 0;
		$networks = array();
		if (!empty($ads)) {
			$adCount = sizeof($ads);
			foreach ($ads as $ad) {
				if ($ad->active) {
					$activeAdCount++;
				}
				$networks[strtolower(get_class($ad))] = $ad->network_name;
			}
		}
		
		$defaultAdName = $advman_engine->get_setting('default-ad');
		
?><div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
<h2><?php _e('Manage Your Advertising', 'advman'); ?></h2>

<form action="" method="post" id="advman-form" enctype="multipart/form-data">
<input type="hidden" id="advman-mode" name="advman-mode" value="list_ads" />
<input type="hidden" id="advman-action" name="advman-action" />
<input type="hidden" id="advman-id" name="advman-id" />

<div class="tablenav">

<div class="alignleft actions">

<div id="advman-list-actions">
	<div id="advman-list-first"><a href="admin.php?page=advman-manage-ads&amp;advman-action=create"><?php _e('Create new ad', 'advman'); ?></a></div>
	<div id="advman-list-toggle"><br /></div>
	<div id="advman-list-inside">
		<div class='advman-list-action'><a href="javascript:advman_set_action('copy');"><?php _e('Copy selected ads', 'advman'); ?></a></div>
		<div class='advman-list-action'><a href="javascript:advman_set_action('delete');"><?php _e('Delete selected ads', 'advman'); ?></a></div>
		<div class='advman-list-action'><a href="javascript:alert(jQuery('#TB_window').id);"><?php _e('TEST THICKBOX', 'advman'); ?></a></div>
	</div>
</div> <!-- advman-list-actions -->

</div><!-- alignleft actions -->
</div> <!-- tablenav -->

<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col"  class="manage-column column-title" style=""><?php _e('Name', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-author" style=""><?php _e('Zone', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-categories" style=""><?php _e('Format', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-tags" style=""><?php _e('Active', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-tags" style=""><?php _e('Default', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-date" style=""><?php _e('Last Edit', 'advman'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col"  class="manage-column column-title" style=""><?php _e('Name', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-author" style=""><?php _e('Zone', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-categories" style=""><?php _e('Format', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-tags" style=""><?php _e('Active', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-tags" style=""><?php _e('Default', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-date" style=""><?php _e('Last Edit', 'advman'); ?></th>
	</tr>
	</tfoot>

	<tbody>
<?php foreach ($ads as $ad) : ?>
	<tr id='post-3' class='alternate author-self status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="advman-ids[]" value="<?php echo $ad->id; ?>" /></th>
		<td class="post-title column-title">
			<strong><a id='advman-ad-<?php echo $ad->id; ?>' class="row-title" href="admin.php?page=advman-manage-ads&amp;advman-action=edit&amp;advman-id=<?php echo $ad->id; ?>" title="<?php printf(__('Edit the ad: %s', 'advman'), $ad->name); ?>">[<?php echo $ad->id; ?>] <?php echo $ad->name; ?></a></strong>
			<div class="row-actions">
				<span class='edit'><a href="admin.php?page=advman-manage-ads&amp;advman-action=edit&amp;advman-id=<?php echo $ad->id; ?>" title="<?php printf(__('Edit the ad &quot;%s&quot;', 'advman'), $ad->name); ?>"><?php _e('Edit', 'advman'); ?></a> | </span>
				<span class='edit'><a class='submitdelete' title="<?php _e('Copy this ad', 'advman'); ?>" href="javascript:advman_set_action('copy','<?php echo $ad->id; ?>', '<?php echo $ad->name; ?>'););"><?php _e('Copy', 'advman'); ?></a> | </span>
				<span class='edit'><a class='submitdelete' title="<?php _e('Delete this ad', 'advman'); ?>" href="javascript:advman_set_action('delete','<?php echo $ad->id; ?>', '<?php echo $ad->name; ?>');" onclick=""><?php _e('Delete', 'advman'); ?></a> | </span>
				<span class='edit'><a class='thickbox' href="<?php echo 'http://www.openx.org?a=b'; //$ad->get_preview_url(); ?>&amp;modal=true&amp;height=500&amp;width=500&amp;TB_iframe=true" id="post-preview" tabindex="4"><?php _e('Preview', 'advman'); ?></a></span>
			</div>
		</td>
		<td class="author column-author"><?php echo $this->displayZones($ad, $zones); ?></td>
		<td class="categories column-categories"> <?php echo $this->displayFormat($ad); ?></td>
		<td class="categories column-tags"><a href="javascript:advman_set_action('<?php echo ($ad->active) ? 'deactivate' : 'activate'; ?>','<?php echo $ad->id; ?>');"> <?php echo ($ad->active) ? __('Yes', 'advman') : __('No', 'advman'); ?></a></td>
		<td class="categories column-tags"><a href="javascript:advman_set_action('default','<?php echo $ad->id; ?>');"> <?php echo ($ad->name == $defaultAdName) ? __('Yes', 'advman') : __('No', 'advman'); ?></a></td>
<?php
		list($last_user, $last_timestamp, $last_timestamp2) = Advman_Tools::get_last_edit($ad->get_property('revisions'));
?>		<td class="date column-date"><abbr title="<?php echo $last_timestamp2 ?>"><?php echo $last_timestamp . __(' ago', 'advman'); ?></abbr><br /> <?php echo __('by', 'advman') . ' ' . $last_user; ?></td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
</form>

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->
</div><!-- wpwrap -->


<?php
	}
	
	function displayZones($ad, $zones)
	{
		$first = true;
		foreach ($zones as $zone) {
			if (in_array($zone['ads'], $ad->id)) {
				if ($first) {
					echo '<br />';
					$first = false;
				}
?><a href="javascript:advman_set_action('edit_zone','<?php echo $zone->id; ?>');" title="<?php printf(__('Edit the zone &quot;%s&quot;', 'advman'), $zone->name); ?>"><?php echo $zone->name; ?></a>
<?php
			}
		}
		
		if ($first) {
			echo '-';
		}
	}


	/**
	 * Display the format field according to the following rules:
	 * 1.  If a format and type combination is set, fill it in
	 * 2.  If not, display the default in grey
	 */
	function displayFormat($ad)
	{
		$format = $ad->get_property('adformat');
		
		// If format is custom, format it like:  Custom (468x60)
		if ($format == 'custom') {
			$format = __('Custom', 'advman') . ' (' . $ad->get_property('width') . 'x' . $ad->get('height') . ')';
		}
		
		// Find a default if the format is not filled in
		if (empty($format)) {
			$format = $ad->get_network_property('adformat');
			if ($format == 'custom') {
				$format = __('Custom', 'advman') . ' (' . $ad->get_property('width') . 'x' . $ad->get('height') . ')';
			}
			if (!empty($format)) {
				$format = "<span style='color:gray;'>" . $format . "</span>";
			}
		}
		
		$type = $ad->get_property('adtype');
		
		// If there is an ad type, prefix it on to the format
		if (empty($type)) {
			$type = $ad->get_network_property('adtype');
			if (!empty($type)) {
				$types = array(
					'ad' => __('Ad Unit', 'advmgr'),
					'link' => __('Link Unit', 'advmgr'),
					'ref_text' => __('Text Referral', 'advmgr'),
					'ref_image' => __('Image Referral', 'advmgr'),
				);
				$type = "<span style='color:gray;'>" . $types[$type] . "</span>";
			}
		}
		
		if (!empty($format) && (!empty($type))) {
			return $type . '<br />' . $format;
		}
		
		return $type . $format;
	}
}
?>