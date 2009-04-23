<?php
require_once(ADVMAN_PATH . '/Template/WP26/EditAd.php');

class Template_EditAd_Chitika extends Template_EditAd
{
	function Template_EditAd_Chitika()
	{
		// Call parent first!
		parent::Template_EditAd();
		// Colors
		add_meta_box('advman_colors', __('Ad Appearance', 'advman'), array(get_class($this), 'displaySectionColors'), 'advman', 'default');
		// Account
		add_meta_box('advman_account', __('Other Settings', 'advman'), array(get_class($this), 'displaySectionOther'), 'advman', 'advanced', 'high');
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
					<option<?php echo ($format == '468x60' ? ' selected="selected"' : ''); ?> value="468x60"> <?php _e('468 x 60 Mini Blog Banner', 'advman'); ?></option>
					<option<?php echo ($format == '468x90' ? ' selected="selected"' : ''); ?> value="468x90"> <?php _e('468 x 90 Small Blog Banner', 'advman'); ?></option>
					<option<?php echo ($format == '468x120' ? ' selected="selected"' : ''); ?> value="468x120"> <?php _e('468 x 120 Blog Banner', 'advman'); ?></option>
					<option<?php echo ($format == '468x180' ? ' selected="selected"' : ''); ?> value="468x180"> <?php _e('468 x 180 Blog Banner', 'advman'); ?></option>
					<option<?php echo ($format == '550x120' ? ' selected="selected"' : ''); ?> value="550x120"> <?php _e('550 x 120 Content Banner', 'advman'); ?></option>
					<option<?php echo ($format == '550x90' ? ' selected="selected"' : ''); ?> value="550x90"> <?php _e('550 x 90 Content Banner', 'advman'); ?></option>
					<option<?php echo ($format == '450x90' ? ' selected="selected"' : ''); ?> value="450x90"> <?php _e('450 x 90 Small Content Banner', 'advman'); ?></option>
					<option<?php echo ($format == '430x90' ? ' selected="selected"' : ''); ?> value="430x90"> <?php _e('430 x 90 Small Content Banner', 'advman'); ?></option>
					<option<?php echo ($format == '400x90' ? ' selected="selected"' : ''); ?> value="400x90"> <?php _e('400 x 90 Small Content Banner', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-vertical" label="Vertical">
					<option<?php echo ($format == '120x600' ? ' selected="selected"' : ''); ?> value="120x600"> <?php _e('120 x 600 Skyscraper', 'advman'); ?></option>
					<option<?php echo ($format == '160x600' ? ' selected="selected"' : ''); ?> value="160x600"> <?php _e('160 x 600 Wide Skyscraper', 'advman'); ?></option>
					<option<?php echo ($format == '180x300' ? ' selected="selected"' : ''); ?> value="180x300"> <?php _e('180 x 300 Small Rectangle, Tall', 'advman'); ?></option>
				</optgroup>
				<optgroup id="advman-optgroup-square" label="Square">
					<option<?php echo ($format == '300x250' ? ' selected="selected"' : ''); ?> value="300x250"> <?php _e('300 x 250 Rectangle', 'advman'); ?></option>
					<option<?php echo ($format == '300x150' ? ' selected="selected"' : ''); ?> value="300x150"> <?php _e('300 x 150 Rectangle, Wide', 'advman'); ?></option>
					<option<?php echo ($format == '300x125' ? ' selected="selected"' : ''); ?> value="300x125"> <?php _e('300 x 125 Mini Rectangle, Wide', 'advman'); ?></option>
					<option<?php echo ($format == '300x70' ? ' selected="selected"' : ''); ?> value="300x70"> <?php _e('300 x 70 Mini Rectangle, Wide', 'advman'); ?></option>
					<option<?php echo ($format == '250x250' ? ' selected="selected"' : ''); ?> value="250x250"> <?php _e('250 x 250 Square', 'advman'); ?></option>
					<option<?php echo ($format == '200x200' ? ' selected="selected"' : ''); ?> value="200x200"> <?php _e('200 x 200 Small Square', 'advman'); ?></option>
					<option<?php echo ($format == '160x160' ? ' selected="selected"' : ''); ?> value="160x160"> <?php _e('160 x 160 Small Square', 'advman'); ?></option>
					<option<?php echo ($format == '336x280' ? ' selected="selected"' : ''); ?> value="336x280"> <?php _e('336 x 280 Rectangle', 'advman'); ?></option>
					<option<?php echo ($format == '336x160' ? ' selected="selected"' : ''); ?> value="336x160"> <?php _e('336 x 160 Rectangle, Wide', 'advman'); ?></option>
					<option<?php echo ($format == '334x100' ? ' selected="selected"' : ''); ?> value="334x100"> <?php _e('334 x 100 Small Rectangle, Wide', 'advman'); ?></option>
					<option<?php echo ($format == '180x150' ? ' selected="selected"' : ''); ?> value="180x150"> <?php _e('180 x 150 Small Rectangle', 'advman'); ?></option>
				</optgroup>
			</select>
		</td>
		<td>
			<img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('adformat'); ?>">
		</td>
	</tr>
	</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Select one of the supported ad format sizes.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionColors($ad)
	{
		$fontTitle = $ad->get('font-title');
		$fontText = $ad->get('font-text');
		
?><table id="advman-settings-colors" width="100%">
<tr>
	<td>
		<table>
		<tr>
			<td class="advman_label"><label for="advman-color-border"><?php _e('Border:'); ?></label></td>
			<td>#<input name="advman-color-border" onChange="advman_update_ad(this,'ad-color-border','border');" size="6" value="<?php echo $ad->get('color-border'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-border'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-title"><?php _e('Title:'); ?></label></td>
			<td>#<input name="advman-color-title" onChange="advman_update_ad(this,'ad-color-title','title');" size="6" value="<?php echo $ad->get('color-title'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-title'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-bg"><?php _e('Background:'); ?></label></td>
			<td>#<input name="advman-color-bg" onChange="advman_update_ad(this,'ad-color-bg','bg');" size="6" value="<?php echo $ad->get('color-bg'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-bg'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-text"><?php _e('Text:'); ?></label></td>
			<td>#<input name="advman-color-text" onChange="advman_update_ad(this,'ad-color-text','text');" size="6" value="<?php echo $ad->get('color-text'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-text'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-color-link"><?php _e('Link:'); ?></label></td>
			<td>#<input name="advman-color-link" onChange="advman_update_ad(this,'ad-color-link','link');" size="6" value="<?php echo $ad->get('color-link'); ?>" /></td>
			<td><img class="default_note" title="<?php echo __('[Default]', 'advman') . ' ' . $ad->get_default('color-link'); ?>"></td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-font-title"><?php _e('Title Font:'); ?></label></td>
			<td>
				<br />
				<select name="advman-font-title" id="advman-font-title" onChange="advman_update_ad(this,'ad-color-title','font-title');">
					<option<?php echo ($fontTitle == 'Arial' ? ' selected="selected"' : ''); ?> value="Arial"> <?php _e('Arial', 'advman'); ?></option>
					<option<?php echo ($fontTitle == 'Comic Sans MS' ? ' selected="selected"' : ''); ?> value="Comic Sans MS"> <?php _e('Comic Sans MS', 'advman'); ?></option>
					<option<?php echo ($fontTitle == 'Courier' ? ' selected="selected"' : ''); ?> value="Courier"> <?php _e('Courier', 'advman'); ?></option>
					<option<?php echo ($fontTitle == 'Georgia' ? ' selected="selected"' : ''); ?> value="Georgia"> <?php _e('Georgia', 'advman'); ?></option>
					<option<?php echo ($fontTitle == 'Tahoma' ? ' selected="selected"' : ''); ?> value="Tahoma"> <?php _e('Tahoma', 'advman'); ?></option>
					<option<?php echo ($fontTitle == 'Times' ? ' selected="selected"' : ''); ?> value="Times"> <?php _e('Times', 'advman'); ?></option>
					<option<?php echo ($fontTitle == 'Verdana' ? ' selected="selected"' : ''); ?> value="Verdana"> <?php _e('Verdana', 'advman'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="advman_label"><label for="advman-font-text"><?php _e('Text Font:'); ?></label></td>
			<td>
				<select name="advman-font-text" id="advman-font-text" onChange="advman_update_ad(this,'ad-color-text','font-text');">
					<option<?php echo ($fontText == 'Arial' ? ' selected="selected"' : ''); ?> value="Arial"> <?php _e('Arial', 'advman'); ?></option>
					<option<?php echo ($fontText == 'Comic Sans MS' ? ' selected="selected"' : ''); ?> value="Comic Sans MS"> <?php _e('Comic Sans MS', 'advman'); ?></option>
					<option<?php echo ($fontText == 'Courier' ? ' selected="selected"' : ''); ?> value="Courier"> <?php _e('Courier', 'advman'); ?></option>
					<option<?php echo ($fontText == 'Georgia' ? ' selected="selected"' : ''); ?> value="Georgia"> <?php _e('Georgia', 'advman'); ?></option>
					<option<?php echo ($fontText == 'Tahoma' ? ' selected="selected"' : ''); ?> value="Tahoma"> <?php _e('Tahoma', 'advman'); ?></option>
					<option<?php echo ($fontText == 'Times' ? ' selected="selected"' : ''); ?> value="Times"> <?php _e('Times', 'advman'); ?></option>
					<option<?php echo ($fontText == 'Verdana' ? ' selected="selected"' : ''); ?> value="Verdana"> <?php _e('Verdana', 'advman'); ?></option>
				</select>
			</td>
		</tr>
		</table>
	</td>
	<td>
		<div id="ad-color-bg" style="margin-top:1em;width:200px;background: #<?php echo ($ad->get('color-bg')) ? $ad->get('color-bg') : 'FFFFFF'; ?>;">
			<div id="ad-color-border" style="font: 10px arial, sans-serif; border: 1px solid #<?php echo ($ad->get('color-border')) ? $ad->get('color-border') : 'FF0000'; ?>" class="linkunit-wrapper">
				<div id="ad-color-title" style="color: #<?php echo ($ad->get('color-title')) ? $ad->get('color-title') : '00FFFF'; ?>; font: 11px verdana, arial, sans-serif; padding: 2px;"><b><u><?php _e('Linked Title', 'advman'); ?></u></b><br /></div>
				<div id="ad-color-text" style="color: #<?php echo ($ad->get('color-text')) ? $ad->get('color-text') : '000000'; ?>; padding: 2px;" class="text"><?php _e('Advertiser\'s ad text here', 'advman'); ?><br /></div>
				<div id="ad-color-link" style="color: #<?php echo ($ad->get('color-link')) ? $ad->get('color-link') : '008000'; ?>; font: 10px verdana, arial, sans-serif; padding: 2px;"><?php _e('www.advertiser-url.com', 'advman'); ?><br /></div>
				<div style="color: #000; padding: 2px;" class="rtl-safe-align-right">&nbsp;<u><?php printf(__('Ads by %s', 'advman'), $ad->getNetworkName()); ?></u></div>
			</div>
		</div>
	</td>
</tr>
</table>
<br />
<span style="font-size:x-small;color:gray;"><?php _e('Choose how you want your ad to appear.  Enter the RGB value of the color in the appropriate box.  The sample ad to the right will show you what your color scheme looks like.', 'advman'); ?></span>
<?php
	}
	
	function displaySectionOther($ad)
	{
?><div style="font-size:small;">
<table>
<tr>
	<td><label for="advman-slot"><?php _e('Account ID:'); ?></label></td>
	<td><input type="text" name="advman-account-id" style="width:200px" id="advman-account-id" value="<?php echo $ad->get('account-id'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-slot"><?php _e('Channel:'); ?></label></td>
	<td><input type="text" name="advman-channel" style="width:200px" id="advman-channel" value="<?php echo $ad->get('channel'); ?>" /></td>
</tr>
<tr>
	<td><label for="advman-slot"><?php _e('Alternate URL:'); ?></label></td>
	<td><input type="text" name="advman-alt-url" style="width:400px" id="advman-alt-url" value="<?php echo $ad->get('alt-url'); ?>" /></td>
</tr>
</table>
</div>
<br />
<span style="font-size:x-small; color:gray;"><?php printf(__('The Account ID is your ID for your %s account.', 'advman'), $ad->getNetworkName()); ?> <?php _e('Enter a channel if you want to break out your reporting by different sections (e.g. home page, post detail)', 'advman'); ?> <?php printf(__('The Alternate URL will be called if %s does not have an ad to display.', 'advman'), $ad->getNetworkName()); ?></span>
<?php
	}
}
?>