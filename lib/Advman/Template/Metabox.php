<?php
require_once(ADVMAN_LIB . '/Tools.php');

class Advman_Template_Metabox
{
	function display_format($ad)
	{
		$format = $ad->get_property('adformat');
		$formats = Advman_Tools::organize_formats($ad->get_ad_formats());
		
?><table id="advman-settings-ad_format">
<tr id="advman-form-adformat">
	<td class="advman_label"><label for="advman-adformat"><?php _e('Format:', 'advman'); ?></label></td>
	<td>
		<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
			<optgroup id="advman-optgroup-default" label="Default">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
			</optgroup>
<?php foreach ($formats['sections'] as $sectionName => $sectionLabel) : ?>
			<optgroup id="advman-optgroup-<?php echo $sectionName ?>" label="<? echo $sectionLabel ?>">
<?php foreach ($formats['formats'][$sectionName] as $formatName => $formatLabel) : ?>
				<option<?php echo ($format == $formatName ? ' selected="selected"' : ''); ?> value="<?php echo $formatName; ?>"> <?php echo $formatLabel; ?></option>
<?php endforeach; ?>
			</optgroup>
<?php endforeach; ?>
		</select>
	</td>
	<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get('adformat', true); ?>"></td>
</tr>
<?php if (!empty($formats['sections']['custom'])) : ?>
<tr id="advman-settings-custom">
	<td class="advman_label"><label for="advman-width"><?php _e('Dimensions:'); ?></label></td>
	<td>
		<input name="advman-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo ($ad->get_property('width')); ?>" /> x
		<input name="advman-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo ($ad->get_property('height')); ?>" /> px
	</td>
</tr>
<?php endif; ?>
</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes.', 'advman'); ?> <?php if (!empty($formats['sections']['custom'])) _e('If your ad size is not one of the standard sizes, select Custom and fill in your size.', 'advman'); ?></span>
<?php
	}
	
	function display_options($ad)
	{
		// Query the users
		$users = get_users_of_blog();
		$defaultAuthor = $ad->get_network_property('show-author');
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
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('show-home'); ?>">
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
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('show-post'); ?>">
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
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('show-page'); ?>">
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
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('show-archive'); ?>">
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
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('show-search'); ?>">
		</td>
		<td style="white-space:nowrap">&nbsp;&nbsp;&nbsp;</td>
		<td></td>
	</tr>
	</table>
</div>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Website display options determine where on your website your ads will appear.', 'advman'); ?></span>
<?php
	}
	
	function display_advanced($ad)
	{
?><h2><?php _e('Advanced Options', 'advman'); ?></h2>
<?php		
	}
	
	function display_optimisation($ad)
	{
?><div style="font-size:small;">
<p>
	<label for="advman-weight"><?php _e('Weight:'); ?></label>
	<input type="text" name="advman-weight" style="width:50px" id="advman-weight" value="<?php echo $ad->get('weight'); ?>" />
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('weight'); ?>">
</p>
<br />
<p>
	<label for="advman-openx-market" class="selectit">
		<input name="advman-openx-market" type="checkbox" id="advman-openx-market" value="yes"<?php echo ($ad->get('openx-market') == 'yes' ? ' checked="checked"' : ''); ?> onChange="document.getElementById('advman-openx-market-cpm').disabled = (!this.checked); document.getElementById('advman-openx-market-cpm').style.color = (this.checked ? 'black' : 'gray'); document.getElementById('advman-openx-market-cpm-label').style.color = (this.checked ? 'black' : 'lightgray');" />
		<?php _e('OpenX Market Enabled', 'advman'); ?>
	</label>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('openx-market'); ?>">
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="advman-openx-market-cpm-label" for="advman-openx-market-cpm"><?php _e('Average eCPM:'); ?></label>
	<input type="text" name="advman-openx-market-cpm" style="width:50px" id="advman-openx-market-cpm" value="<?php echo $ad->get('openx-market-cpm'); ?>"<?php echo ($ad->get('openx-market') != 'yes' ? ' disabled="disabled"' : ''); ?> />
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('openx-market-cpm'); ?>">
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Weight determines how often this ad is displayed relative to the other ads with the same name.  A weight of \'0\' will stop this ad from displaying. OpenX Market optimised ads will display an alternative ad if it will make more money than this ad. Set the avarage amount you make from this network per 1000 ads (eCPM), and Advertising Manager will automatically optimise on the OpenX Market.', 'advman'); ?></span>
<?php
	}
	
	function display_code($ad)
	{
?><div style="font-size:small;">
	<label for="html_before"><?php _e('HTML Code Before'); ?></label><br />
	<textarea rows="1" cols="60" name="advman-html-before" id="advman-html-before" onfocus="this.select();"><?php echo $ad->get('html-before'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('html-before'); ?>"><br /><br />
	<label for="ad_code"><?php _e('Ad Code'); ?></label><br />
	<textarea rows="6" cols="60" id="advman-code" style="background:#cccccc" onfocus="this.select();" onclick="this.select();" readonly="readonly"><?php echo $ad->display(); ?></textarea><br /><br />
	<label for="html_after"><?php _e('HTML Code After'); ?></label><br />
	<textarea rows="1" cols="60" name="advman-html-after" id="advman-html-after" onfocus="this.select();"><?php echo $ad->get('html-after'); ?></textarea><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('html-after'); ?>"><br /><br />
</div>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Place any HTML code you want to display before or after your tag in the appropriate section.  If you want to change your ad network tag, you need to import the new tag again.', 'advman'); ?></span>
<?php
	}
	
	function display_history($ad)
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
	
	function display_colors($ad)
	{
		$colors = Advman_Tools::organize_colors($ad->get_ad_colors());
?><table id="advman-settings-colors" width="100%">
<tr>
	<td>
		<table>
<?php foreach ($colors as $section => $label) : ?>
		<tr>
			<td class="advman_label"><label for="advman-color-<?php echo $section ?>"><?php echo $label; ?></label></td>
			<td>#<input name="advman-color-<?php echo $section ?>" onChange="advman_update_ad(this,'ad-color-<?php echo $section ?>','<?php echo $section ?>');" size="6" value="<?php echo $ad->get('color-' . $section); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('color-' . $section); ?>"></td>
		</tr>
<?php endforeach; ?>
		</table>
	</td>
	<td>
<?php if (!empty($colors['bg'])) : ?>
		<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->get('color-bg', true), ENT_QUOTES); ?>;">
<?php endif; ?>
<?php if (!empty($colors['border'])) : ?>
		<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->get('color-border', true), ENT_QUOTES); ?>" class="linkunit-wrapper">
<?php endif; ?>
<?php if (!empty($colors['title'])) : ?>
		<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->get_property('color-title', true), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;"><b><u><?php _e('Linked Title', 'advman'); ?></u></b><br /></div>
<?php endif; ?>
<?php if (!empty($colors['text'])) : ?>
		<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->get_property('color-text', true), ENT_QUOTES); ?>; padding: 2px;" class="text"><?php _e('Advertiser\'s ad text here', 'advman'); ?><br /></div>
<?php endif; ?>
		<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
			&nbsp;<u><?php printf(__('Ads by %s', 'advman'), $ad->network_name); ?></u></div>
		</div>
	</td>
</tr>
</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Choose how you want your ad to appear.  Enter the RGB value of the color in the appropriate box.  The sample ad to the right will show you what your color scheme looks like.', 'advman'); ?></span>
<?php
	}
	function display_shortcuts($ad)
	{
?>
<p id="jaxtag"><label class="hidden" for="newtag"><?php _e('Shortcuts', 'advman'); ?></label></p>
<p class="hide-if-no-js"><a href="javascript:submit();" onclick="if(confirm('<?php printf(__('You are about to copy the %s ad:', 'advman'), $ad->network_name); ?>\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\n<?php _e('Are you sure?', 'advman'); ?>\n<?php _e('(Press Cancel to do nothing, OK to copy)', 'advman'); ?>')){document.getElementById('advman-action').value='copy'; document.getElementById('advman-form').submit(); } else {return false;}"><?php _e('Copy this ad', 'advman'); ?></a></p>
<p class="hide-if-no-js"><a href="javascript:submit();" onclick="if(confirm('<?php printf(__('You are about to permanently delete the %s ad:', 'advman'), $ad->network_name); ?>\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\n<?php _e('Are you sure?', 'advman'); ?>\n<?php _e('(Press Cancel to keep, OK to delete)', 'advman'); ?>')){document.getElementById('advman-action').value='delete'; document.getElementById('advman-form').submit(); } else {return false;}"><?php _e('Delete this ad', 'advman'); ?></a></p>
<p class="hide-if-no-js"><a href="javascript:submit();" onclick="document.getElementById('advman-action').value='edit'; document.getElementById('advman-action-target').value='<?php echo $ad->network ?>'; document.getElementById('advman-form').submit();"><?php printf(__('Edit %s Defaults', 'advman'), $ad->network_name); ?></a></p>
<?php
	}
	function display_notes($ad)
	{
?><label for="advman_notes"><?php _e('Display any notes about this ad here:', 'advman'); ?></label><br /><br />
<textarea id="advman_notes" rows="8" cols="28" name="advman-notes"><?php echo $ad->get('notes'); ?></textarea><br />
<?php
	}
	function display_save_settings($ad)
	{
?>
<div id="advman-submitad" class="submitbox">
	<div id="minor-publishing">
	<div style="display:none;"><input type="submit" name="save" value="<?php _e('Save', 'advman'); ?>" /></div>
	<div id="minor-publishing-actions">
		<div id="save-action">
			<input id="save-post" class="button button-highlighted" type="submit" tabindex="4" value="<?php _e('Apply', 'advman'); ?>" onclick="document.getElementById('advman-action').value='apply';" />
		</div>
		<div id="preview-action">
			<a class="preview button" href="<?php echo $ad->get_preview_url(); ?>" target="wp-preview" id="post-preview" tabindex="4"><?php _e('Preview Ad', 'advman'); ?></a>
			<input type="hidden" name="wp-preview" id="wp-preview" value="" />
		</div><!-- preview-action -->
		<div class="clear"></div>
	</div><!-- minor-publishing-actions -->
	<div id="misc-publishing-actions">
	<div class="misc-pub-section">
		<label for="post_status"><?php _e('Status:', 'advman'); ?></label>
		<b><a href="javascript:submit();" class="edit-post-status hide-if-no-js" onclick="document.getElementById('advman-action').value='<?php echo $ad->active ? 'deactivate' : 'activate'; ?>'; document.getElementById('advman-form').submit();"><?php echo ($ad->active) ? __('Active', 'advman') : __('Paused', 'advman'); ?></a></b>
	</div><!-- misc-pub-section -->
	<div class="misc-pub-section curtime misc-pub-section-last">
		<span id="timestamp"><?php echo __('Last edited', 'advman') . ' <abbr title="' . $last_timestamp2 . '"><b>' . $last_timestamp . __(' ago', 'advman') . '</b></abbr> ' . __('by', 'advman') . ' ' . $last_user; ?></span>
	</div><!-- misc-pub-section curtime misc-pub-section-last -->
	</div><!-- misc-publishing-actions -->
	<div class="clear"></div>
	</div><!-- minor-publishing -->
	<div id="major-publishing-actions">
	<div id="delete-action">
		<a class="submitdelete deletion" href="javascript:submit();" onclick="document.getElementById('advman-action').value='cancel'; document.getElementById('advman-form').submit();"><?php _e('Cancel', 'advmgr') ?></a>
	</div><!-- delete-action -->
	<div id="publishing-action">
		<input type="submit" class="button-primary" id="advman_save" tabindex="5" accesskey="p" value="<?php _e('Save', 'advman'); ?>" onclick="document.getElementById('advman-action').value='save';" />
	</div><!-- publishing-action -->
	<div class="clear"></div>
	</div><!-- major-publishing-actions -->
</div><!-- advman-submitad -->
<?php
	}
}
?>