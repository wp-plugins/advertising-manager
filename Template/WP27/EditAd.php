<?php
if(!ADSENSEM_VERSION) {die();}

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
		$network_name = $ad->networkName;
		$revisions = $ad->p['revisions'];
		if (!empty($revisions)) {
			foreach($revisions as $t => $u) {
				$last_user = $u;
				$last_timestamp = date('l, F jS, Y @ h:ia', $t);
				break; // just get first one - the array is sorted by reverse date
			}
		} else {
			$last_user = 'Unknown';
			$last_timestamp = 'More than 30 days ago';
		}
?>

<div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2>Edit Settings for <?php echo $network_name; ?> Ad: <span class="<?php echo strtolower($ad->network); ?>"><?php echo "[$id] " . $ad->name; ?></span></h2>
	<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
	<input type="hidden" name="adsensem-mode" id="adsensem-mode" value="edit_ad">
	<input type="hidden" name="adsensem-action" id="adsensem-action">
	<input type="hidden" name="adsensem-action-target" id="adsensem-action-target" value="<?php echo $id; ?>">
	<div id="poststuff" class="metabox-holder">
		<div id="side-info-column" class="inner-sidebar">
			<div id='side-sortables' class='meta-box-sortables'>
				<div id="submitdiv" class="postbox " >
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class='hndle'><span>Save Settings</span></h3>
				<div class="inside">
					<div class="submitbox" id="submitpost">
						<div id="minor-publishing">
							<div style="display:none;"><input type="submit" name="save" value="Save" /></div>
							<div id="minor-publishing-actions">
								<div id="save-action"></div>
								<div id="preview-action">
									<a class="preview button" href="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/edit.php?page=adsense-manager-manage-ads&adsensem-show-ad-id=<?php echo $id ?>" target="wp-preview" id="post-preview" tabindex="4">Preview Ad</a>
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
								<span id="timestamp">Last edited by <?php echo $last_user ?> on: <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $last_timestamp ?></b></span>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div id="major-publishing-actions">
						<div id="publishing-action">
							<a class="submitdelete deletion" href="#">Cancel</a>&nbsp;&nbsp;&nbsp;
							<input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="Apply" />
							<input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="Save" />
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
				<p class="hide-if-no-js"><a href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='edit'; document.getElementById('adsensem-action-target').value='<?php echo $ad->network ?>'; document.getElementById('adsensem-form').submit();">Edit <?php echo $network_name ?> Defaults</a></p>
			</div>
		</div>
		<div id="categorydiv" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class='hndle'><span>Notes</span></h3>
			<div class="inside">
				<label for="ad_code">Display any notes about this ad here:</label><br /><br />
				<textarea rows="8" cols="28" name="adsensem-notes" id="adsensem-notes"><?php echo $ad->p['notes']; ?></textarea><br />
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
?><table id="adsensem-settings-ad_format">
<tr id="adsensem-form-adformat">
	<td class="adsensem_label"><label for="adsensem-adformat">Format:</label></td>
	<td>
		<select name="adsensem-adformat" id="adsensem-adformat" onchange="adsensem_form_update(this);">
			<optgroup id="adsensem-optgroup-default" label="Default">
				<option value=""> Use Default</option>
			</optgroup>
			<optgroup id="adsensem-optgroup-horizontal" label="Horizontal">
				<option<?php echo ($ad->p['adformat'] == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> 728 x 90 Leaderboard</option>
				<option<?php echo ($ad->p['adformat'] == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> 468 x 60 Banner</option>
				<option<?php echo ($ad->p['adformat'] == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> 234 x 60 Half Banner</option>
			</optgroup>
			<optgroup id="adsensem-optgroup-vertical" label="Vertical">
				<option<?php echo ($ad->p['adformat'] == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> 120 x 600 Skyscraper</option>
				<option<?php echo ($ad->p['adformat'] == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> 160 x 600 Wide Skyscraper</option>
				<option<?php echo ($ad->p['adformat'] == '120x240' ? ' selected="selected"' : ''); ?> value="120x240"> 120 x 240 Vertical Banner</option>
			</optgroup>
			<optgroup id="adsensem-optgroup-square" label="Square">
				<option<?php echo ($ad->p['adformat'] == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> 336 x 280 Large Rectangle</option>
				<option<?php echo ($ad->p['adformat'] == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> 300 x 250 Medium Rectangle</option>
				<option<?php echo ($ad->p['adformat'] == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> 250 x 250 Square</option>
				<option<?php echo ($ad->p['adformat'] == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> 200 x 200 Small Square</option>
				<option<?php echo ($ad->p['adformat'] == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> 180 x 150 Small Rectangle</option>
				<option<?php echo ($ad->p['adformat'] == '125x125' ? ' selected="selected"' : ''); ?> value="125x125"> 125 x 125 Button</option>
			</optgroup>
			<optgroup id="adsensem-optgroup-custom" label="Custom">
				<option<?php echo ($ad->p['adformat'] == 'custom' ? ' selected="selected"' : ''); ?> value="custom"> Custom width and height</option>
			</optgroup>
		</select>
	</td>
	<td><img class="default_note" title="[Default] <?php echo $ad->d('adformat'); ?>"></td>
</tr>
<tr id="adsensem-settings-custom">
	<td class="adsensem_label"><label for="adsensem-width">Dimensions:</label></td>
	<td>
		<input name="adsensem-width" size="5" title="Custom width for this unit." value="<?php echo ($ad->p['width']); ?>" /> x
		<input name="adsensem-height" size="5" title="Custom width for this unit." value="<?php echo ($ad->p['height']); ?>" /> px
	</td>
</tr>
</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes. If your ad size is not one of the standard sizes, select 'Custom' and fill in your size.</span>
<?php
	}
	
	function displaySectionDisplayOptions($ad)
	{
?><div style="text-align:right; width:250px; font-size:small;">
	<label for="adsensem-show-home">On Homepage:</label>
	<select name="adsensem-show-home" id="adsensem-show-home">
		<option value=""> Use Default</option>
		<option<?php echo ($ad->p['show-home'] == 'yes' ? " selected='selected'" : ''); ?> value="yes"> Yes</option>
		<option<?php echo ($ad->p['show-home'] == 'no' ? " selected='selected'" : ''); ?> value="no"> No</option>
	</select>
	<img class="default_note" title="[Default] <?php echo $ad->d('show-home'); ?>">
	<br />
	
	<label for="adsensem-show-post">On Posts:</label></td><td>
	<select name="adsensem-show-post" id="adsensem-show-post">
		<option value=""> Use Default</option>
		<option<?php echo ($ad->p['show-post'] == 'yes' ? " selected='selected'" : ''); ?> value="yes"> Yes</option>
		<option<?php echo ($ad->p['show-post'] == 'no' ? " selected='selected'" : ''); ?> value="no"> No</option>
	</select>
	<img class="default_note" title="[Default] <?php echo $ad->d('show-post'); ?>">
	<br />
	
	<label for="adsensem-show-page">On Pages:</label>
	<select name="adsensem-show-page" id="adsensem-show-page">
		<option value=""> Use Default</option>
		<option<?php echo ($ad->p['show-page'] == 'yes' ? " selected='selected'" : ''); ?> value="yes"> Yes</option>
		<option<?php echo ($ad->p['show-page'] == 'no' ? " selected='selected'" : ''); ?> value="no"> No</option>
	</select>
	<img class="default_note" title="[Default] <?php echo $ad->d('show-page'); ?>">
	<br />
		
	<label for="adsensem-show-archive">On Archives:</label>
	<select name="adsensem-show-archive" id="adsensem-show-archive">
		<option value=""> Use Default</option>
		<option<?php echo ($ad->p['show-archive'] == 'yes' ? " selected='selected'" : ''); ?> value="yes"> Yes</option>
		<option<?php echo ($ad->p['show-archive'] == 'no' ? " selected='selected'" : ''); ?> value="no"> No</option>
	</select>
	<img class="default_note" title="[Default] <?php echo $ad->d('show-archive'); ?>">
	<br />
	
	<label class="adsensem_label" for="adsensem-show-search">On Search:</label>
	<select name="adsensem-show-search" id="adsensem-show-search">
		<option value=""> Use Default</option>
		<option<?php echo ($ad->p['show-search'] == 'yes' ? " selected='selected'" : ''); ?> value="yes"> Yes</option>
		<option<?php echo ($ad->p['show-search'] == 'no' ? " selected='selected'" : ''); ?> value="no"> No</option>
	</select>
	<img class="default_note" title="[Default] <?php echo $ad->d('show-search'); ?>">
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
	<input type="text" name="adsensem-weight" style="width:50px" id="adsensem-weight" value="<?php echo $ad->p['weight']; ?>" />
	<img class="default_note" title="[Default] <?php echo $ad->d('weight'); ?>">
</p>
<br />
<p>
	<label for="adsensem-openx-market" class="selectit">
		<input name="adsensem-openx-market" type="checkbox" id="adsensem-openx-market" value="yes"<?php echo ($ad->p['openx-market'] == 'yes' ? ' checked="checked"' : ''); ?> onChange="document.getElementById('adsensem-openx-market-cpm').disabled = (!this.checked); document.getElementById('adsensem-openx-market-cpm').style.color = (this.checked ? 'black' : 'gray'); document.getElementById('adsensem-openx-market-cpm-label').style.color = (this.checked ? 'black' : 'lightgray');" />
		OpenX Market Enabled
	</label>
	<img class="default_note" title="[Default] <?php echo $ad->d('openx-market'); ?>">
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="adsensem-openx-market-cpm-label" for="adsensem-openx-market-cpm">Average eCPM:</label>
	<input type="text" name="adsensem-openx-market-cpm" style="width:50px" id="adsensem-openx-market-cpm" value="<?php echo $ad->p['openx-market-cpm']; ?>"<?php echo ($ad->p['openx-market'] != 'yes' ? ' disabled="disabled"' : ''); ?> />
	<img class="default_note" title="[Default] <?php echo $ad->d('openx-market-cpm'); ?>">
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
	<textarea rows="1" cols="60" name="adsensem-html-before" id="adsensem-html-before" onfocus="this.select();"><?php echo $ad->p['html-before']; ?></textarea><img class="default_note" title="[Default] <?php echo $ad->d('html-before'); ?>"><br />
	<label for="ad_code">Ad Code</label><br />
	<textarea rows="6" cols="60" id="adsensem-code" style="background:#cccccc" onfocus="this.select();" onclick="this.select();" readonly="readonly"><?php echo $ad->render_ad(); ?></textarea><br />
	<label for="html_after">HTML Code After</label><br />
	<textarea rows="1" cols="60" name="adsensem-html-after" id="adsensem-html-after" onfocus="this.select();"><?php echo $ad->p['html-after']; ?></textarea><img class="default_note" title="[Default] <?php echo $ad->d('html-after'); ?>"><br />
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