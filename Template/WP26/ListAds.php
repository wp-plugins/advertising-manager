<?php
if(!ADVMAN_VERSION) {die();}

class Template_ListAds
{
	function display($target = null, $filter = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem, $_adsensem_networks;
?>		<div class="wrap">
			<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
				<input type="hidden" id="adsensem-mode" name="adsensem-mode" value="list_ads" />
				<input type="hidden" id="adsensem-action" name="adsensem-action" />
				<input type="hidden" id="adsensem-action-target" name="adsensem-action-target" />
			<h2>Manage Your Advertising</h2>
			<ul class="subsubsub">
				<li><a href='' class="current">Show Ads (<?php echo sizeof($_adsensem['ads']); ?>)</a> |</li>
				<li><a href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='create'; document.getElementById('adsensem-form').submit();" >Create New Ad</a></li>
			</ul>
			
				<table id="manage-ads" class="widefat">
				<thead>
				<tr style="height:3em; vertical-align:middle; white-space:nowrap">
					<th>Name</th>
					<th>Format</th>
					<th>Modify</th>
					<th style="text-align:center;">Active</th>
					<th style="text-align:center;">Default</th>
					<th>Notes</th>
				</tr>
				</thead>
<?php
		$previous_network='';
		if (is_array($_adsensem['ads'])) {
			foreach ($_adsensem['ads'] as $id => $ad) {
				if ($ad->networkName !== $previous_network) {
					Template_ListAds::_display_network_row($ad);
					$previous_network = $ad->networkName;
					$shade = 0;
				}
					
?>				<tr class="adrow shade_<?php echo $shade; $shade = ($shade==1) ? 0 : 1; ?>">
					<td><a class="adrow_name" href="javascript:document.getElementById('adsensem-form').submit();" onclick="javascript:document.getElementById('adsensem-action').value='edit'; document.getElementById('adsensem-action-target').value='<?php echo $id; ?>';"><?php echo htmlspecialchars('[' . $id . '] ' . $ad->name, ENT_QUOTES); ?></a></td>
					<td><?php echo htmlspecialchars(Template_ListAds::_display_ad_format($ad), ENT_QUOTES); ?></td>
					<td>
						<input class="button" type="submit" value="Copy" onClick="document.getElementById('adsensem-action').value='copy'; document.getElementById('adsensem-action-target').value='<?php echo $id; ?>';">
<?php
				if ( ($id != $_adsensem['default-ad']) || (count($_adsensem['ads']) == 1) ) {
?>						<input class="button" type="submit" value="Delete" onClick="if(confirm('You are about to permanently delete the <?php echo $ad->networkName; ?> ad:\n\n  <?php echo '[' . $ad->id . '] ' . $ad->name; ?>\n\nAre you sure?\n(Press \'Cancel\' to keep, \'OK\' to delete)')){document.getElementById('adsensem-action').value='delete'; document.getElementById('adsensem-action-target').value='<?php echo $id; ?>';} else {return false;}">
					</td>
<?php
				}
?>					</td>
					<td style="text-align:center;">
						<input class="button" onClick="document.getElementById('adsensem-action').value=(this.checked ? 'activate' : 'deactivate'); document.getElementById('adsensem-action-target').value='<?php echo $id; ?>'; this.form.submit();" type="checkbox"<?php echo ($ad->active) ? " checked='checked'" : '' ?>>
					</td>
					<td style="text-align:center;">
						<input class="button" onClick="document.getElementById('adsensem-action').value='default'; document.getElementById('adsensem-action-target').value='<?php echo $id; ?>'; this.form.submit();" type="checkbox"<?php echo ($ad->name == $_adsensem['default-ad']) ? " checked='checked'" : '' ?>>
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
		global $_adsensem;
		
?>				<tr class="network_header" id="default-options">
					<td style="width:180px;"><a class="<?php echo strtolower($ad->network); ?>" href="javascript:document.getElementById('adsensem-form').submit();" onclick="javascript:document.getElementById('adsensem-action').value='edit'; document.getElementById('adsensem-action-target').value='<?php echo $ad->network; ?>';"><?php echo $ad->networkName; ?></a></td>
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