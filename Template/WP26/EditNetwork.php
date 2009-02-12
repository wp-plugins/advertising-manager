<?php
if(!ADVMAN_VERSION) {die();}

class Template_EditNetwork
{
	function Template_EditNetwork()
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
		// History
		add_meta_box('advman_history', __('History', 'advman'), array(get_class($this), 'displaySectionHistory'), 'advman', 'advanced', 'low');
	}
	
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_advman;
		global $_advman_networks;
		
		$ad = new $target;
		$revisions = $ad->get_default('revisions');
		if (!empty($revisions)) {
			foreach($revisions as $t => $u) {
				$last_user = $u;
				$last_timestamp = date('l, F jS, Y @ h:ia');
				break; // just get first one - the array is sorted by reverse date
			}
		} else {
			$last_user = 'Unknown';
			$last_timestamp = 'More than 30 days ago';
		}
?>

<form action="" method="post" id="advman-form" enctype="multipart/form-data">
<input type="hidden" name="advman-mode" id="advman-mode" value="edit_network">
<input type="hidden" name="advman-action" id="advman-action">
<input type="hidden" name="advman-action-target" id="advman-action-target" value="<?php echo $target; ?>">
<?php  
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );  
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );  
?><div class="wrap">
	<h2>Edit <span class="<?php echo strtolower($ad->network); ?>"><?php echo $ad->networkName; ?></span> Defaults</h2>
<div id="poststuff">
<div class="submitbox" id="submitpost">
<div id="previewview">
</div><!-- previewview -->

<div class="inside">
	<p class="curtime">Last edited by <?php echo $last_user ?> on:<br /><?php echo $last_timestamp ?></p>
</div><!-- inside -->

	<div style="white-space:nowrap">
	<p class="submit">
	<input type="button" value="Cancel" onclick="document.getElementById('advman-action').value='cancel'; this.form.submit();" />
	<input type="button" value="Apply" onclick="document.getElementById('advman-action').value='apply'; this.form.submit();" />
	<input type="submit" value="Save &raquo;" class="button button-highlighted" onclick="document.getElementById('advman-action').value='save';" />
	</p>
	</div>

<div class="side-info">
	<h5>Shortcuts</h5>
	<ul>
		<li><a href="<?php echo $ad->url; ?>" ><?php echo $ad->networkName; ?> Home Page</a></li>
	</ul>

	<h5>Notes</h5>
	<label for="ad_code"><?php _e('Display any notes about this ad here:'); ?></label><br /><br />
	<textarea rows="8" cols="22" name="advman-notes" id="advman-notes"><?php echo $ad->get_default('notes'); ?></textarea><br />
</div><!-- side-info -->
</div><!-- submitpost -->

<div id="post-body">
<?php
		
		// Show normal boxes
		do_meta_boxes('advman','normal',$ad);
		// Show advanced screen
		$this->displayAdvanced($ad);
		// Show advanced boxes
		do_meta_boxes('advman','advanced',$ad);
		
?></div>
</div>
</div>
</form>

</div><!-- wpbody -->
</div><!-- wpcontent -->
</div><!-- wpwrap -->

<?php
	}
	
	function displaySectionFormat($ad)
	{
?>	<table id="advman-settings-ad_format">
	<tr id="advman-form-adformat">
		<td class="advman_label"><label for="advman-adformat"><?php _e('Format:'); ?></label></td>
		<td>
			<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->get_default('adformat') == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($ad->get_default('adformat') == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($ad->get_default('adformat') == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->get_default('adformat') == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($ad->get_default('adformat') == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($ad->get_default('adformat') == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($ad->get_default('adformat') == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($ad->get_default('adformat') == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> 200 x 200 Small Square</option>
					<option<?php echo ($ad->get_default('adformat') == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
				</optgroup>
				<optgroup id="advman-optgroup-custom" label="Custom">
					<option<?php echo ($ad->get_default('adformat') == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
				</optgroup>
			</select>
		</td>
	</tr>
	<tr id="advman-settings-custom">
		<td class="advman_label"><label for="advman-width"><?php _e('Dimensions:'); ?></label></td>
		<td>
			<input name="advman-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo $ad->get_default('width'); ?>" /> x
			<input name="advman-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo $ad->get_default('height'); ?>" /> px
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
		
?><div style="text-align:right; width:250px; font-size:small;">
	<table>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-home"><?php _e('On Homepage:', 'advman'); ?></label>
			<select name="advman-show-home" id="advman-show-home">
				<option<?php echo ($ad->get_default('show-home') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-home') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td style="white-space:nowrap">
			<label for="advman-show-author"><?php _e('By Author:', 'advman'); ?></label>
			<select name="advman-show-author" id="advman-show-author">
				<option<?php echo ($ad->get_default('show-author') == 'all' ? " selected='selected'" : ''); ?> value="all"> <?php _e('All Authors', 'advman'); ?></option>
<?php foreach ($users as $user) : ?>
				<option<?php echo ($ad->get_default('show-author') == $user->user_id ? " selected='selected'" : ''); ?> value="<?php echo $user->user_id; ?>"> <?php echo $user->display_name ?></option>
<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-page"><?php _e('On Posts:', 'advman'); ?></label>
			<select name="advman-show-post" id="advman-show-post">
				<option<?php echo ($ad->get_default('show-post') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-post') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-page"><?php _e('On Pages:', 'advman'); ?></label>
			<select name="advman-show-page" id="advman-show-page">
				<option<?php echo ($ad->get_default('show-page') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-page') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="advman-show-archive"><?php _e('On Archives:', 'advman'); ?></label>
			<select name="advman-show-archive" id="advman-show-archive">
				<option<?php echo ($ad->get_default('show-archive') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-archive') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label class="advman_label" for="advman-show-search"><?php _e('On Search:', 'advman'); ?></label>
			<select name="advman-show-search" id="advman-show-search">
				<option<?php echo ($ad->get_default('show-search') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-search') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
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
	<label for="advman-weight"><?php _e('Weight:'); ?></label>
	<input type="text" name="advman-weight" style="width:50px" id="advman-weight" value="<?php echo $ad->get_default('weight'); ?>" />
</p>
<br />
<p>
	<label for="advman-openx-market" class="selectit">
		<input name="advman-openx-market" type="checkbox" id="advman-openx-market" value="yes"<?php echo ($ad->get_default('openx-market') == 'yes' ? ' checked="checked"' : ''); ?> onChange="document.getElementById('advman-openx-market-cpm').disabled = (!this.checked); document.getElementById('advman-openx-market-cpm').style.color = (this.checked ? 'black' : 'gray'); document.getElementById('advman-openx-market-cpm-label').style.color = (this.checked ? 'black' : 'lightgray');" />
		OpenX Market Enabled
	</label>
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="advman-openx-market-cpm-label" for="advman-openx-market-cpm"><?php _e('Average eCPM:'); ?></label>
	<input type="text" name="advman-openx-market-cpm" style="width:50px" id="advman-openx-market-cpm" value="<?php echo $ad->get_default('openx-market-cpm'); ?>"<?php echo ($ad->get_default('openx-market') != 'yes' ? ' disabled="disabled"' : ''); ?> />
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
	<textarea rows="1" cols="60" name="advman-html-before" id="advman-html-before" onfocus="this.select();"><?php echo $ad->get_default('html-before'); ?></textarea><br /><br />
	<label for="html_after"><?php _e('HTML Code After'); ?></label><br />
	<textarea rows="1" cols="60" name="advman-html-after" id="advman-html-after" onfocus="this.select();"><?php echo $ad->get_default('html-after'); ?></textarea><br /><br />
</div>
<br />
<span style="font-size:x-small;color:gray;">Place any HTML code you want to display before or after your tag in the appropriate section.  If you want to change your ad network tag, you need to import the new tag again.</span>
<?php
	}
	
	function displaySectionHistory($ad)
	{
		$revisions = $ad->get_default('revisions');
		
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
<span style="font-size:x-small; color:gray;"><?php _e('The last 30 days of revisions are stored for each ad.', 'advman'); ?></span>
<?php
	}
}
?>