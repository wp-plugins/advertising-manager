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
		add_meta_box('adsensem_format', __('Ad Format', 'adsensem'), array(get_class($this), 'displaySectionFormat'), 'adsensem', 'normal');
		// Display Options
		add_meta_box('adsensem_display_options', __('Display Options', 'adsensem'), array(get_class($this), 'displaySectionDisplayOptions'), 'adsensem', 'normal');
		// Optimisation
		add_meta_box('adsensem_optimisation', __('Optimization', 'adsensem'), array(get_class($this), 'displaySectionOptimisation'), 'adsensem', 'advanced');
		// Code
		add_meta_box('adsensem_code', __('Code', 'adsensem'), array(get_class($this), 'displaySectionCode'), 'adsensem', 'advanced');
		// History
		add_meta_box('adsensem_history', __('History', 'adsensem'), array(get_class($this), 'displaySectionHistory'), 'adsensem', 'advanced', 'low');
	}
	
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		global $_adsensem_networks;
		
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

<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
<input type="hidden" name="adsensem-mode" id="adsensem-mode" value="edit_network">
<input type="hidden" name="adsensem-action" id="adsensem-action">
<input type="hidden" name="adsensem-action-target" id="adsensem-action-target" value="<?php echo $target; ?>">
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
	<input type="button" value="Cancel" onclick="document.getElementById('adsensem-action').value='cancel'; this.form.submit();" />
	<input type="button" value="Apply" onclick="document.getElementById('adsensem-action').value='apply'; this.form.submit();" />
	<input type="submit" value="Save &raquo;" class="button button-highlighted" onclick="document.getElementById('adsensem-action').value='save';" />
	</p>
	</div>

<div class="side-info">
	<h5>Shortcuts</h5>
	<ul>
		<li><a href="<?php echo $ad->url; ?>" ><?php echo $ad->networkName; ?> Home Page</a></li>
	</ul>

	<h5>Notes</h5>
	<label for="ad_code">Display any notes about this ad here:</label><br /><br />
	<textarea rows="8" cols="22" name="adsensem-notes" id="adsensem-notes"><?php echo $ad->get_default('notes'); ?></textarea><br />
</div><!-- side-info -->
</div><!-- submitpost -->

<div id="post-body">
<?php
		
		// Show normal boxes
		do_meta_boxes('adsensem','normal',$ad);
		// Show advanced screen
		$this->displayAdvanced($ad);
		// Show advanced boxes
		do_meta_boxes('adsensem','advanced',$ad);
		
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
?>	<table id="adsensem-settings-ad_format">
	<tr id="adsensem-form-adformat">
		<td class="adsensem_label"><label for="adsensem-adformat">Format:</label></td>
		<td>
			<select name="adsensem-adformat" id="adsensem-adformat" onchange="adsensem_form_update(this);">
				<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->get_default('adformat') == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
					<option<?php echo ($ad->get_default('adformat') == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
					<option<?php echo ($ad->get_default('adformat') == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->get_default('adformat') == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
					<option<?php echo ($ad->get_default('adformat') == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
					<option<?php echo ($ad->get_default('adformat') == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-square" label="Square">
					<option<?php echo ($ad->get_default('adformat') == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
					<option<?php echo ($ad->get_default('adformat') == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> 200 x 200 Small Square</option>
					<option<?php echo ($ad->get_default('adformat') == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
				</optgroup>
				<optgroup id="adsensem-optgroup-custom" label="Custom">
					<option<?php echo ($ad->get_default('adformat') == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
				</optgroup>
			</select>
		</td>
	</tr>
	<tr id="adsensem-settings-custom">
		<td class="adsensem_label"><label for="adsensem-width">Dimensions:</label></td>
		<td>
			<input name="adsensem-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo $ad->get_default('width'); ?>" /> x
			<input name="adsensem-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo $ad->get_default('height'); ?>" /> px
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
			<label for="adsensem-show-home"><?php _e('On Homepage:', 'advman'); ?></label>
			<select name="adsensem-show-home" id="adsensem-show-home">
				<option<?php echo ($ad->get_default('show-home') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-home') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td style="white-space:nowrap">
			<label for="adsensem-show-author"><?php _e('By Author:', 'advman'); ?></label>
			<select name="adsensem-show-author" id="adsensem-show-author">
				<option<?php echo ($ad->get_default('show-author') == 'all' ? " selected='selected'" : ''); ?> value="all"> <?php _e('All Authors', 'advman'); ?></option>
<?php foreach ($users as $user) : ?>
				<option<?php echo ($ad->get_default('show-author') == $user->user_id ? " selected='selected'" : ''); ?> value="<?php echo $user->user_id; ?>"> <?php echo $user->display_name ?></option>
<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="adsensem-show-page"><?php _e('On Posts:', 'advman'); ?></label>
			<select name="adsensem-show-post" id="adsensem-show-post">
				<option<?php echo ($ad->get_default('show-post') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-post') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="adsensem-show-page"><?php _e('On Pages:', 'advman'); ?></label>
			<select name="adsensem-show-page" id="adsensem-show-page">
				<option<?php echo ($ad->get_default('show-page') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-page') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label for="adsensem-show-archive"><?php _e('On Archives:', 'advman'); ?></label>
			<select name="adsensem-show-archive" id="adsensem-show-archive">
				<option<?php echo ($ad->get_default('show-archive') == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($ad->get_default('show-archive') == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
			</select>
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	<tr>
		<td style="white-space:nowrap">
			<label class="adsensem_label" for="adsensem-show-search"><?php _e('On Search:', 'advman'); ?></label>
			<select name="adsensem-show-search" id="adsensem-show-search">
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
	<label for="adsensem-weight">Weight:</label>
	<input type="text" name="adsensem-weight" style="width:50px" id="adsensem-weight" value="<?php echo $ad->get_default('weight'); ?>" />
</p>
<br />
<p>
	<label for="adsensem-openx-market" class="selectit">
		<input name="adsensem-openx-market" type="checkbox" id="adsensem-openx-market" value="yes"<?php echo ($ad->get_default('openx-market') == 'yes' ? ' checked="checked"' : ''); ?> onChange="document.getElementById('adsensem-openx-market-cpm').disabled = (!this.checked); document.getElementById('adsensem-openx-market-cpm').style.color = (this.checked ? 'black' : 'gray'); document.getElementById('adsensem-openx-market-cpm-label').style.color = (this.checked ? 'black' : 'lightgray');" />
		OpenX Market Enabled
	</label>
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="adsensem-openx-market-cpm-label" for="adsensem-openx-market-cpm">Average eCPM:</label>
	<input type="text" name="adsensem-openx-market-cpm" style="width:50px" id="adsensem-openx-market-cpm" value="<?php echo $ad->get_default('openx-market-cpm'); ?>"<?php echo ($ad->get_default('openx-market') != 'yes' ? ' disabled="disabled"' : ''); ?> />
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
	<textarea rows="1" cols="60" name="adsensem-html-before" id="adsensem-html-before" onfocus="this.select();"><?php echo $ad->get_default('html-before'); ?></textarea><br /><br />
	<label for="html_after">HTML Code After</label><br />
	<textarea rows="1" cols="60" name="adsensem-html-after" id="adsensem-html-after" onfocus="this.select();"><?php echo $ad->get_default('html-after'); ?></textarea><br /><br />
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
<span style="font-size:x-small; color:gray;">The last 30 days of revisions are stored for each ad.</span>
<?php
	}
}
?>