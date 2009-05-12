<?php
require_once(ADVMAN_LIB . '/Tools.php');

class Advman_Template_Metabox
{
	function display_format_network($ad)
	{
		return Advman_Template_Metabox::display_format($ad, true);
	}
	
	function display_format_ad($ad)
	{
		return Advman_Template_Metabox::display_format($ad, false);
	}
	
	function display_format($ad, $nw = false)
	{
		$format = ($nw) ? $ad->get_network_property('adformat') : $ad->get_property('adformat');
		$width = ($nw) ? $ad->get_network_property('width') : $ad->get_property('width');
		$height = ($nw) ? $ad->get_network_property('height') : $ad->get_property('height');
		$formats = Advman_Tools::organize_formats($ad->get_ad_formats());
		
?><table id="advman-settings-ad_format">
<tr id="advman-form-adformat">
	<td class="advman_label"><label for="advman-adformat"><?php _e('Format:', 'advman'); ?></label></td>
	<td>
		<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
<?php if (!$nw) : ?>
			<optgroup id="advman-optgroup-default" label="Default">
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
			</optgroup>
<?php endif; ?>
<?php foreach ($formats['sections'] as $sectionName => $sectionLabel) : ?>
			<optgroup id="advman-optgroup-<?php echo $sectionName ?>" label="<? echo $sectionLabel ?>">
<?php foreach ($formats['formats'][$sectionName] as $formatName => $formatLabel) : ?>
				<option<?php echo ($format == $formatName ? ' selected="selected"' : ''); ?> value="<?php echo $formatName; ?>"> <?php echo $formatLabel; ?></option>
<?php endforeach; ?>
			</optgroup>
<?php endforeach; ?>
		</select>
	</td>
<?php if (!$nw) : ?>
	<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get('adformat', true); ?>"></td>
<?php endif; ?>
</tr>
<?php if (!empty($formats['sections']['custom'])) : ?>
<tr id="advman-settings-custom">
	<td class="advman_label"><label for="advman-width"><?php _e('Dimensions:'); ?></label></td>
	<td>
		<input name="advman-width" size="5" title="<?php _e('Custom width for this unit.', 'advman'); ?>" value="<?php echo $width; ?>" /> x
		<input name="advman-height" size="5" title="<?php _e('Custom height for this unit.', 'advman'); ?>" value="<?php echo $height; ?>" /> px
	</td>
</tr>
<?php endif; ?>
</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes.', 'advman'); ?> <?php if (!empty($formats['sections']['custom'])) _e('If your ad size is not one of the standard sizes, select Custom and fill in your size.', 'advman'); ?></span>
<?php
	}
	
	function display_options_network($ad)
	{
		return Advman_Template_Metabox::display_options($ad, true);
	}
	function display_options_ad($ad)
	{
		return Advman_Template_Metabox::display_options($ad, false);
	}
	function display_options($ad, $nw = false)
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
		
		$options = array(
				 'home' => __('On Homepage:', 'advman'),
				 'post' => __('On Posts:', 'advman'),
				 'page' => __('On Pages:', 'advman'),
				 'archive' => __('On Archives:', 'advman'),
				 'search' => __('On Search:', 'advman'),
				 'author' => __('By Author:', 'advman'),
		);
		
		$users = get_users_of_blog();
?><div style="text-align:right; width:250px; font-size:small;">
	<table>
<?php foreach ($options as $option => $label) : ?>
<?php $value = $nw ? $ad->get_network_property('show-' . $option) : $ad->get_property('show-' . $option); ?>
	<tr>
		<td style="white-space:nowrap"><label for="advman-show-<?php echo $option ?>"><?php echo $label; ?></label></td>
		<td align="left">
			<select name="advman-show-<?php echo $option ?>" id="advman-show-<?php echo $option ?>">
<?php if (!$nw) : ?>
				<option value=""> <?php _e('Use Default', 'advman'); ?></option>
<?php endif; ?>
<?php if ($option == 'author'): ?>
<?php foreach ($users as $user) : ?>
				<option<?php echo ($value == $user->user_id ? " selected='selected'" : ''); ?> value="<?php echo $user->user_id; ?>"> <?php echo $user->display_name ?></option>
<?php endforeach; ?>
<?php else: ?>
				<option<?php echo ($value == 'yes' ? " selected='selected'" : ''); ?> value="yes"> <?php _e('Yes', 'advman'); ?></option>
				<option<?php echo ($value == 'no' ? " selected='selected'" : ''); ?> value="no"> <?php _e('No', 'advman'); ?></option>
<?php endif; ?>
			</select>
<?php if (!$nw) : ?>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('show-' . $option); ?>">
<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Website display options determine where on your website your ads will appear.', 'advman'); ?></span>
<?php
	}
	
	function display_optimisation_network($ad)
	{
		return Advman_Template_Metabox::display_optimisation($ad, true);
	}
	function display_optimisation_ad($ad)
	{
		return Advman_Template_Metabox::display_optimisation($ad, false);
	}
	function display_optimisation($ad, $nw = false)
	{
		$weight = ($nw) ? $ad->get_network_property('weight') : $ad->get_property('weight');
		$openxMarket = ($nw) ? $ad->get_network_property('openx-market') : $ad->get_property('openx-market');
		$openxMarketCpm = ($nw) ? $ad->get_network_property('openx-market-cpm') : $ad->get_property('openx-market-cpm');
		
?><div style="font-size:small;">
<p>
	<label for="advman-weight"><?php _e('Weight:'); ?></label>
	<input type="text" name="advman-weight" style="width:50px" id="advman-weight" value="<?php echo $weight; ?>" />
<?php if (!$nw): ?>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('weight'); ?>">
<?php endif; ?>
</p>
<br />
<p>
	<label for="advman-openx-market" class="selectit">
		<input name="advman-openx-market" type="checkbox" id="advman-openx-market" value="yes"<?php echo ($openxMarket == 'yes' ? ' checked="checked"' : ''); ?> onChange="document.getElementById('advman-openx-market-cpm').disabled = (!this.checked);" />
		<?php _e('OpenX Market Enabled', 'advman'); ?>
	</label>
<?php if (!$nw): ?>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('openx-market'); ?>">
<?php endif; ?>
</p>
<p>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="advman-openx-market-cpm-label" for="advman-openx-market-cpm"><?php _e('Average eCPM:'); ?></label>
	<input type="text" name="advman-openx-market-cpm" style="width:50px" id="advman-openx-market-cpm" value="<?php echo $openxMarketCpm; ?>"<?php echo ($openxMarket != 'yes' ? ' disabled="disabled"' : ''); ?> />
<?php if (!$nw): ?>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('openx-market-cpm'); ?>">
<?php endif; ?>
</p>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php _e('Weight determines how often this ad is displayed relative to the other ads with the same name.  A weight of \'0\' will stop this ad from displaying. OpenX Market optimised ads will display an alternative ad if it will make more money than this ad. Set the avarage amount you make from this network per 1000 ads (eCPM), and Advertising Manager will automatically optimise on the OpenX Market.', 'advman'); ?></span>
<?php
	}
	
	function display_code_network($ad)
	{
		return Advman_Template_Metabox::display_code($ad, true);
	}
	function display_code_ad($ad)
	{
		return Advman_Template_Metabox::display_code($ad, false);
	}
	function display_code($ad, $nw = false)
	{
		$edit = $ad->network == 'html';
		$htmlBefore = ($nw) ? $ad->get_network_property('html-before') : $ad->get_property('html-before');
		$htmlAfter = ($nw) ? $ad->get_network_property('html-after') : $ad->get_property('html-after');
		
?><div style="font-size:small;">
	<label for="html_before"><?php _e('HTML Code Before'); ?></label><br />
	<textarea rows="1" cols="60" name="advman-html-before" id="advman-html-before" onfocus="this.select();"><?php echo $htmlBefore; ?></textarea>
<?php if (!$nw): ?>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('html-before'); ?>">
<?php endif; ?>
	<br /><br />
<?php if (!$nw): ?>
	<label for="ad_code"><?php _e('Ad Code'); ?></label><br />
	<textarea rows="6" cols="60" id="advman-code"<?php if (!$edit) echo " style='background:#cccccc'"; ?> onfocus="this.select();" onclick="this.select();"<?php if (!$edit) echo " readonly='readonly'"; ?>><?php echo $ad->display(); ?></textarea><br /><br />
<?php endif; ?>
	<label for="html_after"><?php _e('HTML Code After'); ?></label><br />
	<textarea rows="1" cols="60" name="advman-html-after" id="advman-html-after" onfocus="this.select();"><?php echo $htmlAfter; ?></textarea>
<?php if (!$nw): ?>
	<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('html-after'); ?>">
<?php endif; ?>
	<br /><br />
</div>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Place any HTML code you want to display before or after your tag in the appropriate section.'); ?> <?php _e('If you want to change your ad network tag, you need to import the new tag again.', 'advman'); ?></span>
<?php
	}
	
	
	function display_history_network($ad)
	{
		return Advman_Template_Metabox::display_history($ad, true);
	}
	function display_history_ad($ad)
	{
		return Advman_Template_Metabox::display_history($ad, false);
	}
	function display_history($ad, $nw = false)
	{
		$revisions = ($nw) ? $ad->get_network_property('revisions') : $ad->get_property('revisions');
		
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
	
	function display_colors_network($ad)
	{
		return Advman_Template_Metabox::display_colors($ad, true);
	}
	function display_colors_ad($ad)
	{
		return Advman_Template_Metabox::display_colors($ad, false);
	}
	function display_colors($ad, $nw = false)
	{
		$settings = Advman_Tools::organize_appearance($ad);
?><table id="advman-settings-colors" width="100%">
<tr>
	<td>
		<table>
<?php if (!empty($settings['color'])) : ?>
<?php foreach ($settings['color'] as $section => $label) : ?>
<?php $color = ($nw) ? $ad->get_network_property('color-' . $section) : $ad->get_property('color-' . $section); ?>
		<tr>
			<td class="advman_label"><label for="advman-color-<?php echo $section ?>"><?php echo $label; ?></label></td>
			<td>#<input name="advman-color-<?php echo $section ?>" onChange="advman_update_ad(this,'ad-color-<?php echo $section ?>','<?php echo $section ?>');" size="6" value="<?php echo $color; ?>" /></td>
<?php if (!$nw): ?>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_network_property('color-' . $section); ?>"></td>
<?php endif; ?>
		</tr>
<?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($settings['font'])) : ?>
<?php foreach ($settings['font'] as $section => $label) : ?>
<?php $font = ($nw) ? $ad->get_network_property('font-' . $section) : $ad->get_property('font-' . $section); ?>
		<tr>
			<td class="advman_label"><label for="advman-font-title"><?php _e('Title Font:'); ?></label></td>
			<td>
				<br />
				<select name="advman-font-<?php echo $section ?>" id="advman-font-<?php echo $section ?>" onChange="advman_update_ad(this,'ad-color-<?php echo $section ?>','font-<?php echo $section ?>');">
					<option<?php echo ($font == 'Arial' ? ' selected="selected"' : ''); ?> value="Arial"> <?php _e('Arial', 'advman'); ?></option>
					<option<?php echo ($font == 'Comic Sans MS' ? ' selected="selected"' : ''); ?> value="Comic Sans MS"> <?php _e('Comic Sans MS', 'advman'); ?></option>
					<option<?php echo ($font == 'Courier' ? ' selected="selected"' : ''); ?> value="Courier"> <?php _e('Courier', 'advman'); ?></option>
					<option<?php echo ($font == 'Georgia' ? ' selected="selected"' : ''); ?> value="Georgia"> <?php _e('Georgia', 'advman'); ?></option>
					<option<?php echo ($font == 'Tahoma' ? ' selected="selected"' : ''); ?> value="Tahoma"> <?php _e('Tahoma', 'advman'); ?></option>
					<option<?php echo ($font == 'Times' ? ' selected="selected"' : ''); ?> value="Times"> <?php _e('Times', 'advman'); ?></option>
					<option<?php echo ($font == 'Verdana' ? ' selected="selected"' : ''); ?> value="Verdana"> <?php _e('Verdana', 'advman'); ?></option>
				</select>
			</td>
		</tr>
<?php endforeach; ?>
<?php endif; ?>
		</table>
	</td>
	<td>
<?php if (!empty($settings['color']['bg'])) : ?>
<?php 	$color = ($nw) ? $ad->get_network_property('color-bg') : $ad->get('color-bg'); ?>
<?php 	$color = empty($color) ? 'FFFFFF' : $color; ?>
		<div id="ad-color-bg" style="width:220px; background: #<?php echo htmlspecialchars($color, ENT_QUOTES); ?>;">
<?php endif; ?>
<?php if (!empty($settings['color']['border'])) : ?>
<?php 	$color = ($nw) ? $ad->get_network_property('color-border') : $ad->get('color-border'); ?>
<?php 	$color = empty($color) ? 'EEEEEE' : $color; ?>
		<div id="ad-color-border" style="padding:4px; border: 1px solid #<?php echo htmlspecialchars($color, ENT_QUOTES); ?>">
<?php endif; ?>
<?php if (!empty($settings['color']['title'])) : ?>
<?php 	$font = ($nw) ? $ad->get_network_property('font-title') : $ad->get('font-title'); ?>
<?php	$font = empty($font) ? 'verdana, arial, sans-serif' : $font; ?>
<?php 	$color = ($nw) ? $ad->get_network_property('color-title') : $ad->get('color-title'); ?>
<?php 	$color = empty($color) ? 'EEEEEE' : $color; ?>
		<div id="ad-color-title" style="font: 12px <?php echo $font ?>; padding: 2px; color: #<?php echo htmlspecialchars($color, ENT_QUOTES); ?>;"><b><u><?php _e('Linked Title', 'advman'); ?></u></b><br /></div>
<?php endif; ?>
<?php if (!empty($settings['color']['text'])) : ?>
<?php 	$font = ($nw) ? $ad->get_network_property('font-text') : $ad->get('font-text'); ?>
<?php	$font = empty($font) ? 'verdana, arial, sans-serif' : $font; ?>
<?php 	$color = ($nw) ? $ad->get_network_property('color-text') : $ad->get('color-text'); ?>
<?php 	$color = empty($color) ? 'EEEEEE' : $color; ?>
		<div id="ad-color-text" style="font: 11px <?php echo $font ?>; padding: 2px; color: #<?php echo htmlspecialchars($color, ENT_QUOTES); ?>;"><?php _e('Advertiser\'s ad text here', 'advman'); ?><br /></div>
<?php endif; ?>
<?php if (!empty($settings['color']['link'])) : ?>
<?php 	$font = ($nw) ? $ad->get_network_property('font-link') : $ad->get('font-link'); ?>
<?php	$font = empty($font) ? 'verdana, arial, sans-serif' : $font; ?>
<?php 	$color = ($nw) ? $ad->get_network_property('color-link') : $ad->get('color-link'); ?>
<?php 	$color = empty($color) ? 'EEEEEE' : $color; ?>
		<div id="ad-color-link" style="font: 11px <?php echo $font ?>; padding: 2px; color: #<?php echo htmlspecialchars($color, ENT_QUOTES); ?>;"><?php _e('www.advertiser-url.com', 'advman'); ?><br /></div>
<?php endif; ?>
		<div style="color: #000000; font: 10px verdana, arial, sans-serif; text-align:center"><u><?php printf(__('Ads by %s', 'advman'), $ad->network_name); ?></u></div>
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
<p class="hide-if-no-js"><a href="javascript:submit();" onclick="document.getElementById('advman-action').value='edit'; document.getElementById('advman-target').value='<?php echo $ad->network ?>'; document.getElementById('advman-form').submit();"><?php printf(__('Edit %s Defaults', 'advman'), $ad->network_name); ?></a></p>
<?php
	}
	function display_notes($ad)
	{
?><label for="advman_notes"><?php _e('Display any notes about this ad here:', 'advman'); ?></label><br /><br />
<textarea id="advman_notes" rows="8" cols="28" name="advman-notes"><?php echo $ad->get('notes'); ?></textarea><br />
<?php
	}
	function display_save_settings_network($ad)
	{
		Advman_Template_Metabox::display_save_settings($ad, true);
	}
	function display_save_settings_ad($ad)
	{
		Advman_Template_Metabox::display_save_settings($ad, false);
	}
	function display_save_settings($ad, $nw = false)
	{
		list($last_user, $last_timestamp, $last_timestamp2) = Advman_Tools::get_last_edit($ad->get_property('revisions'));
?>
<div id="advman-submitad" class="submitbox">
	<div id="minor-publishing">
	<div style="display:none;"><input type="submit" name="save" value="<?php _e('Save', 'advman'); ?>" /></div>
	<div id="minor-publishing-actions">
		<div id="save-action">
			<input id="save-post" class="button button-highlighted" type="submit" tabindex="4" value="<?php _e('Apply', 'advman'); ?>" onclick="document.getElementById('advman-action').value='apply';" />
		</div>
<?php if (!$nw) : ?>
		<div id="preview-action">
			<a class="preview button" href="<?php echo $ad->get_preview_url(); ?>" target="wp-preview" id="post-preview" tabindex="4"><?php _e('Preview Ad', 'advman'); ?></a>
			<input type="hidden" name="wp-preview" id="wp-preview" value="" />
		</div><!-- preview-action -->
<?php endif; ?>
		<div class="clear"></div>
	</div><!-- minor-publishing-actions -->
	<div id="misc-publishing-actions">
<?php if (!$nw) : ?>
	<div class="misc-pub-section">
		<label for="post_status"><?php _e('Status:', 'advman'); ?></label>
		<b><a href="javascript:submit();" class="edit-post-status hide-if-no-js" onclick="document.getElementById('advman-action').value='<?php echo $ad->active ? 'deactivate' : 'activate'; ?>'; document.getElementById('advman-form').submit();"><?php echo ($ad->active) ? __('Active', 'advman') : __('Paused', 'advman'); ?></a></b>
	</div><!-- misc-pub-section -->
<?php endif; ?>
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