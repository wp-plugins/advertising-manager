<?php
if(!ADVMAN_VERSION) {die();}

class Template_CreateAd
{
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		global $_adsensem_networks;
?>	<div class="wrap">
		<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
			<input type="hidden" name="adsensem-mode" id="adsensem-mode" value="create_ad">	
			<input type="hidden" name="adsensem-action" id="adsensem-action">
			<input type="hidden" name="adsensem-action-target" id="adsensem-action-target">
			<h2><?php _e('Create Ads', 'advman'); ?></h2>
			<ul class="subsubsub">
				<li><a href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='list'; document.getElementById('adsensem-form').submit();" >Show Ads (<?php echo sizeof($_adsensem['ads']); ?>)</a> |</li>
				<li><a href="" class="current"><?php _e('Create New Ad', 'advman'); ?></a></li>
			</ul>
			
			<table>
			<tr>
				<td style="width:50%;vertical-align:top;">
					<h3><?php _e('Step 1: Import Your Ad Code', 'advman'); ?></h3>
					<p><?php _e('Simply <strong>paste your Ad Code below</strong> and Import!', 'advman'); ?></p>
					<div>
						<textarea rows="5" cols="65" name="adsensem-code" id="adsensem-code"></textarea>
						<p class="submit" style="text-align:right;vertical-align:bottom;">
							<input type="button" value="<?php _e('Cancel', 'advman'); ?>" onclick="document.getElementById('adsensem-action').value='cancel'; this.form.submit();">		
							<input type="button" value="<?php _e('Clear', 'advman'); ?>" onclick="document.getElementById('adsensem-code').value='';">		
							<input style="font-weight:bold;" type="submit" value="<?php _e('Import to New Ad Unit&raquo;', 'advman'); ?>" onclick="document.getElementById('adsensem-action').value='import';">
						</p>
					</div>		
				</td>
				<td style="width:10%";>&nbsp;</td>
				<td style="width:40%";>
					<p><?php _e('Advertising Manager supports most Ad networks.', 'advman'); ?></p>
					<p><?php _e('Any networks not supported directly will be managed as HTML Code units.', 'advman'); ?></p>
				</td>
			</tr>
			</table>
		</form>
	</div>
<?php 
	}
}
?>