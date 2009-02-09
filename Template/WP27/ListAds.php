<?php
if(!ADVMAN_VERSION) {die();}

class Template_ListAds
{
	function display($target = null, $filter = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem, $_adsensem_networks;
		$adCount = 0;
		$activeAdCount = 0;
		$networks = array();
		if (!empty($_adsensem['ads'])) {
			$adCount = sizeof($_adsensem['ads']);
			foreach ($_adsensem['ads'] as $ad) {
				if ($ad->active) {
					$activeAdCount++;
				}
				$networks[$ad->network] = $ad->networkName;
			}
		}
		$filterActive = !empty($filter['active']) ? $filter['active'] : null;
		$filterNetwork = !empty($filter['network']) ? $filter['network'] : null;
		
?><div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
<h2>Manage Your Advertising</h2>
<script type='text/javascript'>
/* <![CDATA[ */
function ADS_setAction(action, id, name, network)
{
	submit = true;
	if (action == 'delete') {
		if ( confirm('You are about to permanently delete the ' + network + ' ad:\n\n  [' + id + '] ' + name + '\n\nAre you sure?\n(Press \'Cancel\' to keep, \'OK\' to delete)') ) {
			submit = true;
		} else {
			submit = false;
		}
	}
	
	if (submit) {
		document.getElementById('adsensem-action').value = action;
		document.getElementById('adsensem-action-target').value = id;
		document.getElementById('adsensem-form').submit();
	}
}
/* ]]> */
</script>

<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
<input type="hidden" id="adsensem-mode" name="adsensem-mode" value="list_ads" />
<input type="hidden" id="adsensem-action" name="adsensem-action" />
<input type="hidden" id="adsensem-action-target" name="adsensem-action-target" />

<ul class="subsubsub">
<li><a href=''  class="current">Show Ads <span class="count">(<?php echo sizeof($_adsensem['ads']); ?>)</span></a> |</li>

<li><a href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='create'; document.getElementById('adsensem-form').submit();" >Create New Ad</a></li></ul>

<input type="hidden" name="mode" value="list" />


<div class="tablenav">

<div class="alignleft actions">
<select id="adsensem-bulk-top" name="action">
<option value="" selected="selected">Bulk Actions</option>
<option value="copy">Copy</option>
<option value="delete">Delete</option>
</select>
<input type="submit" value="Apply" name="doaction" id="doaction" class="button-secondary action" onclick="document.getElementById('adsensem-action').value = document.getElementById('adsensem-bulk-top').value;" />

<select name='adsensem-filter-network' class='postform' >
	<option value='0'> View all ad types </option>
<?php foreach ($networks as $network => $networkName): ?>
	<option class="level-0"<?php echo ($filterNetwork == $network) ? ' selected' : '' ?> value="<?php echo $network ?>"> View only <?php echo $networkName ?> ads </option>
<?php endforeach; ?>
</select>
<select name='adsensem-filter-active' class='postform' >
	<option value='0'> View all ad statuses </option>
	<option class="level-0"<?php echo ($filterActive == 'active') ? ' selected' : '' ?> value="active"> View active ads only </option>
	<option class="level-0"<?php echo ($filterActive == 'inactive') ? ' selected' : '' ?> value="inactive"> View paused ads only </option>
</select>
<input type="submit" id="post-query-submit" value="Filter" class="button-secondary" onclick="document.getElementById('adsensem-action').value = 'filter';" />
<?php if ( !empty($filterActive) || !empty($filterNetwork)) : ?>
<input type="submit" value="Clear" class="button-secondary" onclick="document.getElementById('adsensem-action').value = 'clear';" />
<?php endif ?>
</div>


<div class="view-switch">
	<a href="/wordpress.27/wp-admin/edit.php?mode=list"><img class="current" id="view-switch-list" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('List View', 'advman'); ?>" alt="List View" /></a>

	<a href="/wordpress.27/wp-admin/edit.php?mode=excerpt"><img  id="view-switch-excerpt" src="../wp-includes/images/blank.gif" width="20" height="20" title="Excerpt View" alt="Excerpt View" /></a>
</div>

<div class="clear"></div>
</div>

<div class="clear"></div>

<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col"  class="manage-column column-title" style=""><?php _e('Name', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-author" style=""><?php _e('Type', 'advman'); ?></th>
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
	<th scope="col"  class="manage-column column-author" style=""><?php _e('Type', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-categories" style=""><?php _e('Format', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-tags" style=""><?php _e('Active', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-tags" style=""><?php _e('Default', 'advman'); ?></th>
	<th scope="col"  class="manage-column column-date" style=""><?php _e('Last Edit', 'advman'); ?></th>
	</tr>
	</tfoot>

	<tbody>
<?php foreach ($_adsensem['ads'] as $id => $ad) : ?>
<?php if ( ($filterActive == 'active' && $ad->active) || ($filterActive == 'inactive' && !$ad->active) || empty($filterActive) ) : ?>
<?php if ( ($filterNetwork == $ad->network) || empty($filterNetwork) ) : ?>
	<tr id='post-3' class='alternate author-self status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="adsensem-action-targets[]" value="<?php echo $ad->id; ?>" /></th>
		<td class="post-title column-title">
			<strong><a class="row-title" href="javascript:ADS_setAction('edit','<?php echo $ad->id; ?>');" title="Edit the ad &quot;<?php echo $ad->name; ?>&quot;">[<?php echo $ad->id; ?>] <?php echo $ad->name; ?></a></strong>
			<div class="row-actions">
				<span class='edit'><a href="javascript:ADS_setAction('edit','<?php echo $ad->id; ?>');" title="Edit the ad &quot;<?php echo $ad->name; ?>&quot;">Edit</a> | </span>
				<span class='edit'><a class='submitdelete' title="<?php _e('Copy this ad', 'advman'); ?>" href="javascript:ADS_setAction('copy','<?php echo $ad->id; ?>');">Copy</a> | </span>
				<span class='edit'><a class='submitdelete' title="<?php _e('Delete this ad', 'advman'); ?>" href="javascript:ADS_setAction('delete','<?php echo $ad->id; ?>', '<?php echo $ad->name; ?>', '<?php echo $ad->networkName; ?>');" onclick=""><?php _e('Delete', 'advman'); ?></a> | </span>
				<span class='edit'><a href="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/edit.php?page=advertising-manager-manage-ads&adsensem-show-ad-id=<?php echo $id ?>" target="wp-preview" id="post-preview" tabindex="4"><?php _e('Preview', 'advman'); ?></a></span>
			</div>
		</td>
		<td class="author column-author"><a href="javascript:ADS_setAction('edit','<?php echo $ad->network; ?>');" title="Edit the ad network &quot;<?php echo $ad->networkName; ?>&quot;"><?php echo $ad->networkName; ?></a></td>
		<td class="categories column-categories"> <?php echo $ad->get('adformat'); ?></td>
		<td class="categories column-tags"><a href="javascript:ADS_setAction('<?php echo ($ad->active) ? 'deactivate' : 'activate'; ?>','<?php echo $ad->id; ?>');"> <?php echo ($ad->active) ? 'Yes' : 'No'; ?></a></td>
		<td class="categories column-tags"><a href="javascript:ADS_setAction('default','<?php echo $ad->id; ?>');"> <?php echo ($ad->name == $_adsensem['default-ad']) ? 'Yes' : 'No'; ?></a></td>
<?php
		list($last_user, $t) = OX_Tools::get_last_edit($ad);
		$last_timestamp = (time() - $t) < (30 * 24 * 60 * 60) ? human_time_diff($t) : __('> 30 days', 'advman');
		$last_timestamp2 = date('l, F jS, Y @ h:ia', $t);
?>		<td class="date column-date"><abbr title="<?php echo $last_timestamp2 ?>"><?php echo $last_timestamp . __(' ago', 'advman'); ?></abbr><br /><?php echo __('by ', 'advman') . $last_user; ?></td>
	</tr>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
	</tbody>
</table>
<div class="tablenav">
	<div class="alignleft actions">
		<select id="adsensem-bulk-bottom" name="action">
		<option value="" selected="selected">Bulk Actions</option>
		<option value="copy">Copy</option>
		<option value="delete">Delete</option>
		</select>
		<input type="submit" value="Apply" name="doaction" id="doaction" class="button-secondary action" onclick="document.getElementById('adsensem-action').value = document.getElementById('adsensem-bulk-bottom').value;" />
		<br class="clear" />
	</div>
	<br class="clear" />
</div>
</form>

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->
</div><!-- wpwrap -->


<?php
	}
}
?>