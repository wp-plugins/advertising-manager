<?php
if(!ADVMAN_VERSION) {die();}

class Template_EditAd
{
	function Template_EditAd()
	{
		// Scripts
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-ui-draggable');
		
		// Ad Format
		add_meta_box('advman_format', __('Ad Format', 'advman'), array(get_class($this), 'displaySectionFormat'), 'advman', 'normal');
		// Display Options
		add_meta_box('advman_display_options', __('Display Options', 'advman'), array(get_class($this), 'displaySectionDisplayOptions'), 'advman', 'normal');
		// Optimisation
		add_meta_box('advman_optimisation', __('Optimization', 'advman'), array(get_class($this), 'displaySectionOptimisation'), 'advman', 'advanced');
		// Code
		add_meta_box('advman_code', __('Code', 'advman'), array(get_class($this), 'displaySectionCode'), 'advman', 'advanced');
		// Revisions
		add_meta_box('advman_history', __('History', 'advman'), array(get_class($this), 'displaySectionHistory'), 'advman', 'advanced', 'low');
	}
	
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_advman;
		global $_advman_networks;
		
		$id = $target;
		$ad = $_advman['ads'][$id];
		list($last_user, $t) = OX_Tools::get_last_edit($ad);
		if ((time() - $t) < (30 * 24 * 60 * 60)) { // less than 30 days ago
			$last_timestamp =  human_time_diff($t);
			$last_timestamp2 = date('l, F jS, Y @ h:ia', $t);
		} else {
			$last_timestamp =  __('> 30 days', 'advman');
			$last_timestamp2 = '';
		}
?>

<div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2><?php printf(__('Edit Settings for %s Ad:', 'advman'), $ad->networkName); ?> <span class="<?php echo strtolower($ad->network); ?>"><?php echo "[$id] " . $ad->name; ?></span></h2>
	<form action="" method="post" id="advman-form" enctype="multipart/form-data">
	<input type="hidden" name="advman-mode" id="advman-mode" value="edit_ad">
	<input type="hidden" name="advman-action" id="advman-action">
	<input type="hidden" name="advman-action-target" id="advman-action-target" value="<?php echo $id; ?>">
<?php  
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );  
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );  
?>	<div id="poststuff" class="metabox-holder">
		<div id="side-info-column" class="inner-sidebar">
			<div id='side-sortables' class='meta-box-sortables'>
				<div id="submitdiv" class="postbox " >
				<div class="handlediv" title="<?php _e('Click to toggle', 'advman'); ?>"><br /></div>
				<h3 class='hndle'><span><?php _e('Save Settings', 'advman'); ?></span></h3>
				<div class="inside">
					<div class="submitbox" id="submitpost">
						<div id="minor-publishing">
							<div style="display:none;"><input type="submit" name="save" value="<?php _e('Save', 'advman'); ?>" /></div>
							<div id="minor-publishing-actions">
								<div id="save-action"></div>
								<div id="preview-action">
									<a class="preview button" href="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/edit.php?page=advertising-manager-manage-ads&advman-show-ad-id=<?php echo $id ?>" target="wp-preview" id="post-preview" tabindex="4"><?php _e('Preview Ad', 'advman'); ?></a>
									<input type="hidden" name="wp-preview" id="wp-preview" value="" />
								</div>
								<div class="clear"></div>
							</div>
							<div id="misc-publishing-actions">
							<div class="misc-pub-section">
								<label for="post_status"><?php _e('Status:', 'advman'); ?></label>
								<b><a href="javascript:submit();" class="edit-post-status hide-if-no-js" onclick="document.getElementById('advman-action').value='<?php echo $ad->active ? 'deactivate' : 'activate'; ?>'; document.getElementById('advman-form').submit();"><?php echo ($ad->active) ? __('Active', 'advman') : __('Paused', 'advman'); ?></a></b>
							</div>
							<div class="misc-pub-section curtime misc-pub-section-last">
								<span id="timestamp"><?php echo __('Last edited', 'advman') . ' <abbr title="' . $last_timestamp2 . '"><b>' . $last_timestamp . __(' ago', 'advman') . '</b></abbr> by ' . $last_user; ?></span>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div id="major-publishing-actions">
						<div id="publishing-action">
							<a class="submitdelete deletion" href="javascript:submit();" onclick="document.getElementById('advman-action').value='cancel'; document.getElementById('advman-form').submit();"><?php _e('Cancel', 'advmgr') ?></a>&nbsp;&nbsp;&nbsp;
							<input type="submit" class="button-primary" id="advman_apply" tabindex="5" accesskey="p" value="<?php _e('Apply', 'advman'); ?>" onclick="document.getElementById('advman-action').value='apply';" />
							<input type="submit" class="button-primary" id="advman_save" tabindex="5" accesskey="p" value="<?php _e('Save', 'advman'); ?>" onclick="document.getElementById('advman-action').value='save';" />
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="tagsdiv" class="postbox " >
			<h3 class='hndle'><span><?php _e('Shortcuts', 'advman'); ?></span></h3>
			<div class="inside">
				<p id="jaxtag"><label class="hidden" for="newtag"><?php _e('Shortcuts', 'advman'); ?></label></p>
				<p class="hide-if-no-js"><a href="javascript:submit();" onclick="if(confirm('You are about to copy the <?php echo $ad->networkName; ?> ad:\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\nAre you sure?\n(Press \'Cancel\' to do nothing, \'OK\' to copy)')){document.getElementById('advman-action').value='copy'; document.getElementById('advman-form').submit(); } else {return false;}">Copy this ad</a></p>
				<p class="hide-if-no-js"><a href="javascript:submit();" onclick="if(confirm('You are about to permanently delete the <?php echo $ad->networkName; ?> ad:\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\nAre you sure?\n(Press \'Cancel\' to keep, \'OK\' to delete)')){document.getElementById('advman-action').value='delete'; document.getElementById('advman-form').submit(); } else {return false;}">Delete this ad</a></p>
				<p class="hide-if-no-js"><a href="javascript:submit();" onclick="document.getElementById('advman-action').value='edit'; document.getElementById('advman-action-target').value='<?php echo $ad->network ?>'; document.getElementById('advman-form').submit();">Edit <?php echo $ad->networkName ?> Defaults</a></p>
			</div>
		</div>
		<div id="categorydiv" class="postbox " >
			<div class="handlediv" title="<?php _e('Click to toggle', 'advman'); ?>"><br /></div>
			<h3 class='hndle'><span><?php _e('Notes', 'advman'); ?></span></h3>
			<div class="inside">
				<label for="ad_code"><?php _e('Display any notes about this ad here:', 'advman'); ?></label><br /><br />
				<textarea rows="8" cols="28" name="advman-notes" id="advman-notes"><?php echo $ad->get('notes'); ?></textarea><br />
			</div>
		</div>
	</div>
</div>

<div id="post-body" class="has-sidebar">
	<div id="post-body-content" class="has-sidebar-content">

<?php
		// Title
		$this->displaySectionTitle($ad);
		// Show normal boxes
		do_meta_boxes('advman','normal',$ad);
		// Show advanced screen
		$this->displayAdvanced($ad);
		// Show advanced boxes
		do_meta_boxes('advman','advanced',$ad);
?>
	</form>
</div><!-- wpwrap -->

<?php
	}
	
	function displaySectionTitle($ad)
	{
?><div id="titlediv">
	<h3><label for="title"><?php _e('Name', 'advman'); ?></label></h3>
<div id="titlewrap">
	<input type="text" name="advman-name" size="30" value="<?php echo $ad->name ?>" id="title" autocomplete="off" />
</div><!-- titlewrap -->
<br />
<span style="font-size:smaller;color:gray;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Ads with the same name will rotate according to their relative weights.', 'advman'); ?></span>
</div><!-- titlediv -->
<?php
	}
	
	function displaySectionFormat($ad)
	{
		$format = $ad->get('adformat');
		
?><table id="advman-settings-ad_format">
<tr id="advman-form-adformat">
	<td class="advman_label"><label for="advman-adformat"><?php _e('Format:', 'advman'); ?></label></td>
	<td>
		<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
			<optgroup id="advman-optgroup-default" label="Default">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
			</optgroup>
			<optgroup id="advman-optgroup-horizontal" label="Horizontal">
				<option<?php echo ($format == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> <?php _e('728 x 90 Leaderboard', 'advman'); ?></option>
				<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> <?php _e('468 x 60 Banner', 'advman'); ?></option>
				<option<?php echo ($format == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> <?php _e('234 x 60 Half Banner', 'advman'); ?></option>
			</optgroup>
			<optgroup id="advman-optgroup-vertical" label="Vertical">
				<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> <?php _e('120 x 600 Skyscraper', 'advman'); ?></option>
				<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> <?php _e('160 x 600 Wide Skyscraper', 'advman'); ?></option>
				<option<?php echo ($format == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> <?php _e('120 x 240 Vertical Banner', 'advman'); ?></option>
			</optgroup>
			<optgroup id="advman-optgroup-square" label="Square">
				<option<?php echo ($format == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> <?php _e('336 x 280 Large Rectangle', 'advman'); ?></option>
				<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> <?php _e('300 x 250 Medium Rectangle', 'advman'); ?></option>
				<option<?php echo ($format == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> <?php _e('250 x 250 Square', 'advman'); ?></option>
				<option<?php echo ($format == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> <?php _e('200 x 200 Small Square', 'advman'); ?></option>
				<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> <?php _e('180 x 150 Small Rectangle'); ?></option>
				<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> <?php _e('125 x 125 Button', 'advman'); ?></option>
			</optgroup>
			<optgroup id="advman-optgroup-custom" label="Custom">
				<option<?php echo ($format == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
			</optgroup>
		</select>
	</td>
	<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>"></td>
</tr>
<tr id="advman-settings-custom">
	<td class="advman_label"><label for="advman-width"><?php _e('Dimensions:'); ?></label></td>
	<td>
		<input name="advman-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('width')); ?>" /> x
		<input name="advman-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('height')); ?>" /> px
	</td>
</tr>
</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes. If your ad size is not one of the standard sizes, select 'Custom' and fill in your size.</span>
<?php
	}
	
	function displaySectionDisplayOptions($ad)
	{
		// Query the users
		$users = get_users_of_blog();
		$defaultAuthor = $ad->get_default('show-author');
		if (is_numeric($defaultAuthor)) {
			$u = get_users_of_blog($defaultAuthor);
			$defaultAuthor = $u[0]->display_name;
		} else {
			$defaultAuthor = __('All Authors', 'advman');
		}
		
?><div style="text-align:right; width:250px; font-size:small;">
	<table>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-home"><?php _e('On Homepage:', 'advman'); ?></label>
			<select name="advman-show-home" id="advman-show-home">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-home') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-home') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('show-home'); ?>">
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td style="white-space:nowrap">
			<label for="advman-show-author"><?php _e('By Author:', 'advman'); ?></label>
			<select name="advman-show-author" id="advman-show-author">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-author') == 'all' ? " selected='selected'" : ''); ?> value="all"> <?php _e('All Authors', 'advman'); ?></option>
<?php foreach ($users as $user) : ?>
				<option<?php echo ($ad->get('show-author') == $user->user_id ? " selected='selected'" : ''); ?> value="<?php echo $user->user_id; ?>"> <?php echo $user->display_name ?></option>
<?php endforeach; ?>
			</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $defaultAuthor; ?>">
		</td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-page"><?php _e('On Posts:', 'advman'); ?></label>
			<select name="advman-show-post" id="advman-show-post">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-post') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-post') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('show-post'); ?>">
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-page"><?php _e('On Pages:', 'advman'); ?></label>
			<select name="advman-show-page" id="advman-show-page">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-page') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-page') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('show-page'); ?>">
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-archive"><?php _e('On Archives:', 'advman'); ?></label>
			<select name="advman-show-archive" id="advman-show-archive">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-archive') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-archive') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('show-archive'); ?>">
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label class="advman_label" for="advman-show-search"><?php _e('On Search:', 'advman'); ?></label>
			<select name="advman-show-search" id="advman-show-search">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-search') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-search') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('show-search'); ?>">
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	</table>
</div>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Display options determine where on your website your ads will appear.', 'advman'); ?></span>
<?php
	}
	
	function displayAdvanced($ad)
	{
?><h2><?php _e('Advanced Options', 'advman'); ?></h2>
<?php		
	}
	
	function displaySectionOptimisation($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="advman-weight"><?php _e('Weight:'); ?></label>
	<input type="text" name="advman-weight" style="width:50px" id="advman-weight" value="<?php echo $ad->get('weight'); ?>" />
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('weight'); ?>">
</p>
<br />
<p>
	<label for="advman-openx-market" class="selectit">
		<input name="advman-openx-market" type="checkbox" id="advman-openx-market" value="yes"<?php echo ($ad->get('openx-market') == 'yes' ? ' checked="checked"' : ''); ?> onChange="document.getElementById('advman-openx-market-cpm').disabled = (!this.checked); document.getElementById('advman-openx-market-cpm').style.color = (this.checked ? 'black' : 'gray'); document.getElementById('advman-openx-market-cpm-label').style.color = (this.checked ? 'black' : 'lightgray');" />
		OpenX Market Enabled
	</label>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('openx-market'); ?>">
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="advman-openx-market-cpm-label" for="advman-openx-market-cpm"><?php _e('Average eCPM:'); ?></label>
	<input type="text" name="advman-openx-market-cpm" style="width:50px" id="advman-openx-market-cpm" value="<?php echo $ad->get('openx-market-cpm'); ?>"<?php echo ($ad->get('openx-market') != 'yes' ? ' disabled="disabled"' : ''); ?> />
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('openx-market-cpm'); ?>">
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Weight determines how often this ad is displayed relative to the other ads with the same name.  A weight of \'0\' will stop this ad from displaying. OpenX Market optimised ads will display an alternative ad if it will make more money than this ad. Set the avarage amount you make from this network per 1000 ads (eCPM), and Advertising Manager will automatically optimise on the OpenX Market.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionCode($ad)
	{
?><div style="font-size:small;">
	<label for="html_before"><?php _e('HTML Code Before'); ?></label><br />
	<textarea rows="1" cols="60" name="advman-html-before" id="advman-html-before" onfocus="this.select();"><?php echo $ad->get('html-before'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('html-before'); ?>"><br /><br />
	<label for="ad_code"><?php _e('Ad Code'); ?></label><br />
	<textarea rows="6" cols="60" id="advman-code" style="background:#cccccc" onfocus="this.select();" onclick="this.select();" readonly="readonly"><?php echo $ad->render_ad(); ?></textarea><br /><br />
	<label for="html_after"><?php _e('HTML Code After'); ?></label><br />
	<textarea rows="1" cols="60" name="advman-html-after" id="advman-html-after" onfocus="this.select();"><?php echo $ad->get('html-after'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('html-after'); ?>"><br /><br />
</div>
<br />
<span style="font-size:x-small;color:gray;">Place any HTML code you want to display before or after your tag in the appropriate section.  If you want to change your ad network tag, you need to import the new tag again.</span>
<?php
	}
	
	function displaySectionHistory($ad)
	{
		$revisions = $ad->get('revisions');
		
?><ul class='post-revisions'>
<?php
		if (empty($revisions)) {
?>		<li><?php printf(__('More than %d days ago', 'advman'), 30) ?><span style="color:gray"> <?php _e('by Unknown', 'advman'); ?></span></li>
<?php
		} else {
			$now = mktime();
			foreach ($revisions as $ts => $name) {
				$days = (strtotime($now) - strtotime($ts)) / 86400 + 1;
				if ($days <= 30) {
?>		<li><?php echo date('l, F jS, Y @ h:ia', $ts); ?><span style="color:gray"> by <?php echo $name; ?></span></li>
<?php
				}
			}
		}
?>	</ul>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('The last 30 days of revisions are stored for each ad.', 'advman'); ?></span>
<?php
	}
}
?>