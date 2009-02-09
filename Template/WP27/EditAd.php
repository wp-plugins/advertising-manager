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
		add_meta_box('adsensem_format', __('Ad Format', 'adsensem'), array(get_class($this), 'displaySectionFormat'), 'adsensem', 'normal');
		// Display Options
		add_meta_box('adsensem_display_options', __('Display Options', 'adsensem'), array(get_class($this), 'displaySectionDisplayOptions'), 'adsensem', 'normal');
		// Optimisation
		add_meta_box('adsensem_optimisation', __('Optimization', 'adsensem'), array(get_class($this), 'displaySectionOptimisation'), 'adsensem', 'advanced');
		// Code
		add_meta_box('adsensem_code', __('Code', 'adsensem'), array(get_class($this), 'displaySectionCode'), 'adsensem', 'advanced');
		// Revisions
		add_meta_box('adsensem_history', __('History', 'adsensem'), array(get_class($this), 'displaySectionHistory'), 'adsensem', 'advanced', 'low');
	}
	
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		global $_adsensem_networks;
		
		$id = $target;
		$ad = $_adsensem['ads'][$id];
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
	<h2>Edit Settings for <?php echo $ad->networkName; ?> Ad: <span class="<?php echo strtolower($ad->network); ?>"><?php echo "[$id] " . $ad->name; ?></span></h2>
	<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
	<input type="hidden" name="adsensem-mode" id="adsensem-mode" value="edit_ad">
	<input type="hidden" name="adsensem-action" id="adsensem-action">
	<input type="hidden" name="adsensem-action-target" id="adsensem-action-target" value="<?php echo $id; ?>">
	<div id="poststuff" class="metabox-holder">
		<div id="side-info-column" class="inner-sidebar">
			<div id='side-sortables' class='meta-box-sortables'>
				<div id="submitdiv" class="postbox " >
				<div class="handlediv" title="<?php _e('Click to toggle', 'advman'); ?>"><br /></div>
				<h3 class='hndle'><span>Save Settings</span></h3>
				<div class="inside">
					<div class="submitbox" id="submitpost">
						<div id="minor-publishing">
							<div style="display:none;"><input type="submit" name="save" value="Save" /></div>
							<div id="minor-publishing-actions">
								<div id="save-action"></div>
								<div id="preview-action">
									<a class="preview button" href="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/edit.php?page=advertising-manager-manage-ads&adsensem-show-ad-id=<?php echo $id ?>" target="wp-preview" id="post-preview" tabindex="4">Preview Ad</a>
									<input type="hidden" name="wp-preview" id="wp-preview" value="" />
								</div>
								<div class="clear"></div>
							</div>
							<div id="misc-publishing-actions">
							<div class="misc-pub-section">
								<label for="post_status">Status:</label>
								<b><a href="#" class="edit-post-status hide-if-no-js"><?php echo ($ad->active) ? 'Active' : 'Paused'; ?></a></b>
							</div>
							<div class="misc-pub-section curtime misc-pub-section-last">
								<span id="timestamp"><?php echo __('Last edited', 'advman') . ' <abbr title="' . $last_timestamp2 . '"><b>' . $last_timestamp . __(' ago', 'advman') . '</b></abbr> by ' . $last_user; ?></span>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div id="major-publishing-actions">
						<div id="publishing-action">
							<a class="submitdelete deletion" href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='cancel'; document.getElementById('adsensem-form').submit();"><?php _e('Cancel', 'advmgr') ?></a>&nbsp;&nbsp;&nbsp;
							<input type="submit" class="button-primary" id="advman_apply" tabindex="5" accesskey="p" value="<?php _e('Apply', 'advman'); ?>" onclick="document.getElementById('adsensem-action').value='apply';" />
							<input type="submit" class="button-primary" id="advman_save" tabindex="5" accesskey="p" value="<?php _e('Save', 'advman'); ?>" onclick="document.getElementById('adsensem-action').value='save';" />
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="tagsdiv" class="postbox " >
			<h3 class='hndle'><span>Shortcuts</span></h3>
			<div class="inside">
				<p id="jaxtag"><label class="hidden" for="newtag">Shortcuts</label></p>
				<p class="hide-if-no-js"><a href="javascript:submit();" onclick="if(confirm('You are about to copy the <?php echo $ad->networkName; ?> ad:\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\nAre you sure?\n(Press \'Cancel\' to do nothing, \'OK\' to copy)')){document.getElementById('adsensem-action').value='copy'; document.getElementById('adsensem-form').submit(); } else {return false;}">Copy this ad</a></p>
				<p class="hide-if-no-js"><a href="javascript:submit();" onclick="if(confirm('You are about to permanently delete the <?php echo $ad->networkName; ?> ad:\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\nAre you sure?\n(Press \'Cancel\' to keep, \'OK\' to delete)')){document.getElementById('adsensem-action').value='delete'; document.getElementById('adsensem-form').submit(); } else {return false;}">Delete this ad</a></p>
				<p class="hide-if-no-js"><a href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='edit'; document.getElementById('adsensem-action-target').value='<?php echo $ad->network ?>'; document.getElementById('adsensem-form').submit();">Edit <?php echo $ad->networkName ?> Defaults</a></p>
			</div>
		</div>
		<div id="categorydiv" class="postbox " >
			<div class="handlediv" title="<?php _e('Click to toggle', 'advman'); ?>"><br /></div>
			<h3 class='hndle'><span>Notes</span></h3>
			<div class="inside">
				<label for="ad_code">Display any notes about this ad here:</label><br /><br />
				<textarea rows="8" cols="28" name="adsensem-notes" id="adsensem-notes"><?php echo $ad->get('notes'); ?></textarea><br />
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
		do_meta_boxes('adsensem','normal',$ad);
		// Show advanced screen
		$this->displayAdvanced($ad);
		// Show advanced boxes
		do_meta_boxes('adsensem','advanced',$ad);
?>
	</form>
</div><!-- wpwrap -->

<?php
	}
	
	function displaySectionTitle($ad)
	{
?><div id="titlediv">
	<h3><label for="title">Name</label></h3>
<div id="titlewrap">
	<input type="text" name="adsensem-name" size="30" value="<?php echo $ad->name ?>" id="title" autocomplete="off" />
</div><!-- titlewrap -->
<br />
<span style="font-size:smaller;color:gray;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ads with the same name will rotate according to their relative weights.</span>
</div><!-- titlediv -->
<?php
	}
	
	function displaySectionFormat($ad)
	{
		$format = $ad->get('adformat');
		
?><table id="adsensem-settings-ad_format">
<tr id="adsensem-form-adformat">
	<td class="adsensem_label"><label for="adsensem-adformat">Format:</label></td>
	<td>
		<select name="adsensem-adformat" id="adsensem-adformat" onchange="adsensem_form_update(this);">
			<optgroup id="adsensem-optgroup-default" label="Default">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
			</optgroup>
			<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
				<option<?php echo ($format == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
				<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
				<option<?php echo ($format == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
			</optgroup>
			<optgroup id="adsensem-optgroup-vertical" label="Vertical">
				<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
				<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
				<option<?php echo ($format == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
			</optgroup>
			<optgroup id="adsensem-optgroup-square" label="Square">
				<option<?php echo ($format == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
				<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
				<option<?php echo ($format == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
				<option<?php echo ($format == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> 200 x 200 Small Square</option>
				<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
				<option<?php echo ($format == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
			</optgroup>
			<optgroup id="adsensem-optgroup-custom" label="Custom">
				<option<?php echo ($format == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
			</optgroup>
		</select>
	</td>
	<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>"></td>
</tr>
<tr id="adsensem-settings-custom">
	<td class="adsensem_label"><label for="adsensem-width">Dimensions:</label></td>
	<td>
		<input name="adsensem-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('width')); ?>" /> x
		<input name="adsensem-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo ($ad->get('height')); ?>" /> px
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
			<label for="adsensem-show-home"><?php _e('On Homepage:', 'advman'); ?></label>
			<select name="adsensem-show-home" id="adsensem-show-home">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-home') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get('show-home') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('show-home'); ?>">
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td style="white-space:nowrap">
			<label for="adsensem-show-author"><?php _e('By Author:', 'advman'); ?></label>
			<select name="adsensem-show-author" id="adsensem-show-author">
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
			<label for="adsensem-show-page"><?php _e('On Posts:', 'advman'); ?></label>
			<select name="adsensem-show-post" id="adsensem-show-post">
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
			<label for="adsensem-show-page"><?php _e('On Pages:', 'advman'); ?></label>
			<select name="adsensem-show-page" id="adsensem-show-page">
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
			<label for="adsensem-show-archive"><?php _e('On Archives:', 'advman'); ?></label>
			<select name="adsensem-show-archive" id="adsensem-show-archive">
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
			<label class="adsensem_label" for="adsensem-show-search"><?php _e('On Search:', 'advman'); ?></label>
			<select name="adsensem-show-search" id="adsensem-show-search">
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
<span style="font-size:x-small;color:gray;">Display options determine where on your website your ads will appear.</span>
<?php
	}
	
	function displayAdvanced($ad)
	{
?><h2>Advanced Options</h2>
<?php		
	}
	
	function displaySectionOptimisation($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="adsensem-weight">Weight:</label>
	<input type="text" name="adsensem-weight" style="width:50px" id="adsensem-weight" value="<?php echo $ad->get('weight'); ?>" />
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('weight'); ?>">
</p>
<br />
<p>
	<label for="adsensem-openx-market" class="selectit">
		<input name="adsensem-openx-market" type="checkbox" id="adsensem-openx-market" value="yes"<?php echo ($ad->p['openx-market'] == 'yes' ? ' checked="checked"' : ''); ?> onChange="document.getElementById('adsensem-openx-market-cpm').disabled = (!this.checked); document.getElementById('adsensem-openx-market-cpm').style.color = (this.checked ? 'black' : 'gray'); document.getElementById('adsensem-openx-market-cpm-label').style.color = (this.checked ? 'black' : 'lightgray');" />
		OpenX Market Enabled
	</label>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('openx-market'); ?>">
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="adsensem-openx-market-cpm-label" for="adsensem-openx-market-cpm">Average eCPM:</label>
	<input type="text" name="adsensem-openx-market-cpm" style="width:50px" id="adsensem-openx-market-cpm" value="<?php echo $ad->get('openx-market-cpm'); ?>"<?php echo ($ad->p['openx-market'] != 'yes' ? ' disabled="disabled"' : ''); ?> />
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('openx-market-cpm'); ?>">
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;">Weight determines how often this ad is displayed relative to the other ads with the same name.  A weight of '0' will stop this ad from displaying. OpenX Market optimised ads will display an alternative ad if it will make more money than this ad. Set the avarage amount you make from this network per 1000 ads (eCPM), and Advertising Manager will automatically optimise on the OpenX Market.</span>
<?php
	}
	
	function displaySectionCode($ad)
	{
?><div style="font-size:small;">
	<label for="html_before">HTML Code Before</label><br />
	<textarea rows="1" cols="60" name="adsensem-html-before" id="adsensem-html-before" onfocus="this.select();"><?php echo $ad->get('html-before'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('html-before'); ?>"><br />
	<label for="ad_code">Ad Code</label><br />
	<textarea rows="6" cols="60" id="adsensem-code" style="background:#cccccc" onfocus="this.select();" onclick="this.select();" readonly="readonly"><?php echo $ad->render_ad(); ?></textarea><br />
	<label for="html_after">HTML Code After</label><br />
	<textarea rows="1" cols="60" name="adsensem-html-after" id="adsensem-html-after" onfocus="this.select();"><?php echo $ad->get('html-after'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('html-after'); ?>"><br />
</div>
<br />
<span style="font-size:x-small;color:gray;">Place any HTML code you want to display before or after your tag in the appropriate section.  If you want to change your ad network tag, you need to import the new tag again.</span>
<?php
	}
	
	function displaySectionHistory($ad)
	{
		$revisions = $ad->p['revisions'];
		
?><ul class='post-revisions'>
<?php
		if (empty($revisions)) {
?>		<li>More than 30 days ago<span style="color:gray"> by Unknown</span></li>
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
<span style="font-size:x-small; color:gray;">The last 30 days of revisions are stored for each ad.</span>
<?php
	}
}
?>