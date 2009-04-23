<?php
if(!ADVMAN_VERSION) {die();}

class Template_ListAds
{
	function display($target = null, $filter = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_advman, $_advman_networks;
?>		<div class="wrap">
			<form action="" method="post" id="advman-form" enctype="multipart/form-data">
				<input type="hidden" id="advman-mode" name="advman-mode" value="list_ads" />
				<input type="hidden" id="advman-action" name="advman-action" />
				<input type="hidden" id="advman-action-target" name="advman-action-target" />
			<h2><?php _e('Manage Your Advertising', 'advman'); ?></h2>
			
				<table id="manage-ads" class="widefat">
				<thead>
				<tr style="height:3em; vertical-align:middle; white-space:nowrap">
					<th><?php _e('Name', 'advman'); ?></th>
					<th><?php _e('Format', 'advman'); ?></th>
					<th><?php _e('Modify', 'advman'); ?></th>
					<th style="text-align:center;"><?php _e('Active', 'advman'); ?></th>
					<th style="text-align:center;"><?php _e('Default', 'advman'); ?></th>
					<th><?php _e('Notes', 'advman'); ?></th>
				</tr>
				</thead>
<?php
		$previous_network='';
		if (is_array($_advman['ads'])) {
			foreach ($_advman['ads'] as $id => $ad) {
				if ($ad->getNetworkName() !== $previous_network) {
					Template_ListAds::_display_network_row($ad);
					$previous_network = $ad->getNetworkName();
					$shade = 0;
				}
					
?>				<tr class="adrow shade_<?php echo $shade; $shade = ($shade==1) ? 0 : 1; ?>">
					<td><a class="adrow_name" href="javascript:document.getElementById('advman-form').submit();" onclick="javascript:document.getElementById('advman-action').value='edit'; document.getElementById('advman-action-target').value='<?php echo $id; ?>';"><?php echo htmlspecialchars('[' . $id . '] ' . $ad->name, ENT_QUOTES); ?></a></td>
					<td><?php echo htmlspecialchars(Template_ListAds::_display_ad_format($ad), ENT_QUOTES); ?></td>
					<td>
						<input class="button" type="submit" value="<?php _e('Copy', 'advman'); ?>" onClick="document.getElementById('advman-action').value='copy'; document.getElementById('advman-action-target').value='<?php echo $id; ?>';">
<?php
				if ( ($id != $_advman['default-ad']) || (count($_advman['ads']) == 1) ) {
?>						<input class="button" type="submit" value="<?php _e('Delete', 'advman'); ?>" onClick="if(confirm('<?php printf(__('You are about to permanently delete the %s ad:', 'advman'), $ad->getNetworkName()); ?>\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\n<?php _e('Are you sure?', 'advman'); ?>\n<?php _e('(Press Cancel to keep, OK to delete)', 'advman'); ?>')){document.getElementById('advman-action').value='delete'; document.getElementById('advman-action-target').value='<?php echo $id; ?>';} else {return false;}">
					</td>
<?php
				}
?>					</td>
					<td style="text-align:center;">
						<input class="button" onClick="document.getElementById('advman-action').value=(this.checked ? 'activate' : 'deactivate'); document.getElementById('advman-action-target').value='<?php echo $id; ?>'; this.form.submit();" type="checkbox"<?php echo ($ad->active) ? " checked='checked'" : '' ?>>
					</td>
					<td style="text-align:center;">
						<input class="button" onClick="document.getElementById('advman-action').value='default'; document.getElementById('advman-action-target').value='<?php echo $id; ?>'; this.form.submit();" type="checkbox"<?php echo ($ad->name == $_advman['default-ad']) ? " checked='checked'" : '' ?>>
					</td>
					<td><?php echo htmlspecialchars($ad->get('notes'), ENT_QUOTES); ?></td>
				</tr>
<?php
			}
		}
?>				</table>
<?php
//				<p>Earn even more with <a href="http://www.text-link-ads.com/?ref=55499" target="_blank">Text Link Ads</a> and <a href="http://www.inlinks.com/?ref=211569" target="_blank">InLinks!</a></p>
?>
				<p>By editing the <strong>Network Defaults</strong> you can update all ads from a network at once.<br />
					<strong>Default Ad</strong> indicates which ad will be displayed in a space on your site where no specific ID is used.<br />
					Ads with the <strong>same name</strong> rotate according to their relative weights.
				</p>
				<p>Ads can be included in <strong>templates</strong> using <code>&lt;?php adsensem_ad('name'); ?&gt;</code> or <code>&lt;?php adsensem_ad(); ?&gt;</code> for the default Ad.<br />
					Ads can be inserted into <strong>posts / pages</strong> using <code>[ad#name]</code> or <code>[ad]</code> for the default Ad. <br/>
					Note that the old <code>&lt;!--adsense#name--&gt;</code> style still works if you prefer it.
				</p>
			</form>
		</div>
<?php									
	}
	
	function _display_network_row($ad)
	{
		global $_advman;
		
?>				<tr class="network_header" id="default-options">
					<td style="width:180px;"><a class="<?php echo strtolower($ad->getNetwork()); ?>" href="javascript:document.getElementById('advman-form').submit();" onclick="javascript:document.getElementById('advman-action').value='edit'; document.getElementById('advman-action-target').value='<?php echo $ad->getNetwork(); ?>';"><?php echo $ad->getNetworkName(); ?></a></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><?php echo $ad->get_default('notes'); ?></td>
				</tr>
<?php
	}
	
	function _display_ad_format($ad)
	{
		$atypes = array('ref_text' => 'Text');
		$text = $ad->get('adformat');
		if (empty($text)) {
			$type = $ad->get('adtype');
			if (!empty($type)) {
				$text = $atypes[$type];
			} else {
				$text = '(default)';
			}
		} else {
			if ($text == 'custom') {
				$text = $ad->get('width') . 'x' . $ad->get('height');
			}
		}
		
		return $text;
	}
}
?>