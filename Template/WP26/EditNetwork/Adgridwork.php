<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditNetwork.php');

class Template_EditNetwork_Adgridwork extends Template_EditNetwork
{
	function Template_EditNetwork_Adgridwork()
	{
		// Call parent first!
		parent::Template_EditNetwork();
		// Colors
		add_meta_box('advman_colors', __('Colors', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'normal');
	}
	
	function displaySectionFormat($ad)
	{
?>	<table id="advman-settings-ad_format">
	<tr id="advman-form-adformat">
		<td class="advman_label"><label for="advman-adformat"><?php _e('Format:'); ?></label></td>
		<td>
			<select name="advman-adformat" id="advman-adformat" onchange="advman_form_update(this);">
				<optgroup id="advman-optgroup-horizontal" label="Horizontal">
					<option<?php echo ($ad->get_default('adformat') == '800x90' ? ' selected="selected"' : ''); ?> value="800x90"> 800 x 90 Large Leaderboard</option>
					<option<?php echo ($ad->get_default('adformat') == '728x90' ? ' selected="selected"' : ''); ?> value="728x90"> <?php _e('728 x 90 Leaderboard', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '600x90' ? ' selected="selected"' : ''); ?> value="600x90"> 600 x 90 Small Leaderboard</option>
					<option<?php echo ($ad->get_default('adformat') == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> <?php _e('468 x 60 Banner', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '400x90' ? ' selected="selected"' : ''); ?> value="400x90"> 400 x 90 Tall Banner</option>
					<option<?php echo ($ad->get_default('adformat') == '234x60' ? ' selected="selected"' : ''); ?> value="234x60"> <?php _e('234 x 60 Half Banner', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '200x90' ? ' selected="selected"' : ''); ?> value="200x90"> 200 x 90 Tall Half Banner</option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($ad->get_default('adformat') == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> <?php _e('120 x 600 Skyscraper', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> <?php _e('160 x 600 Wide Skyscraper', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '200x360' ? ' selected="selected"' : ''); ?> value="200x360"> 200 x 360 Wide Half Banner</option>
					<option<?php echo ($ad->get_default('adformat') == '200x270' ? ' selected="selected"' : ''); ?> value="200x270"> 200 x 270 Wide Short Banner</option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($ad->get_default('adformat') == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> <?php _e('336 x 280 Large Rectangle', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> <?php _e('300 x 250 Medium Rectangle', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> <?php _e('250 x 250 Square', 'advman'); ?></option>
					<option<?php echo ($ad->get_default('adformat') == '200x180' ? ' selected="selected"' : ''); ?> value="200x180"> 200 x 180 Small Rectangle</option>
					<option<?php echo ($ad->get_default('adformat') == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> <?php _e('180 x 150 Small Rectangle'); ?></option>
				</optgroup>
			</select>
		</td>
		<td>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the supported ad format sizes.</span>
<?php
	}
	
	function displaySectionColors($ad)
	{
?>	<table id="advman-settings-colors" width="100%">
	<tr>
		<td>
			<table>
			<tr>
				<td class="advman_label"><label for="advman-color-border"><?php _e('Border:'); ?></label></td>
				<td>#<input name="advman-color-border" onChange="advman_update_color(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get_default('color-border'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-title"><?php _e('Title:'); ?></label></td>
				<td>#<input name="advman-color-title" onChange="advman_update_color(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get_default('color-title'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
				<td>#<input name="advman-color-bg" onChange="advman_update_color(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get_default('color-bg'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-text"><?php _e('Text:'); ?></label></td>
				<td>#<input name="advman-color-text" onChange="advman_update_color(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get_default('color-text'); ?>" /></td>
			</tr>
			<tr>
				<td class="advman_label"><label for="advman-color-link"><?php _e('URL:'); ?></label></td>
				<td>#<input name="advman-color-link" onChange="advman_update_color(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get_default('color-link'); ?>" /></td>
			</tr>
			</table>
		</td>
		<td>
			<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo htmlspecialchars($ad->get_default('color-bg'), ENT_QUOTES); ?>;">
			<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo htmlspecialchars($ad->get_default('color-border'), ENT_QUOTES); ?>" class="linkunit-wrapper">
			<div id="ad-color-title" style="color: #<?php echo htmlspecialchars($ad->get_default('color-title'), ENT_QUOTES); ?>; font: 11px verdana, arial, sans-serif; padding: 2px;">
				<b><u>Linked Title</u></b><br /></div>
			<div id="ad-color-text" style="color: #<?php echo htmlspecialchars($ad->get_default('color-text'), ENT_QUOTES); ?>; padding: 2px;" class="text">
				Advertiser's ad text here<br /></div>
			<div id="ad-color-link" style="color: #<?php echo htmlspecialchars($ad->get_default('color-link'), ENT_QUOTES); ?>; font: 10px verdana, arial, sans-serif; padding: 2px;">
				www.advertiser-url.com<br /></div>
			<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">
				&nbsp;<u>Ads by <?php echo $ad->networkName; ?></u></div>
			</div>
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;">Select one of the ad format sizes supported by Adgridwork.  Enter multiple channels separated by '+' signs.</span>
<?php
	}
}
?>