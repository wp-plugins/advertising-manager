<?php
if(!ADSENSEM_VERSION) {die();}

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
			<h2>Create Ads</h2>
			<ul class="subsubsub">
				<li><a href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='list'; document.getElementById('adsensem-form').submit();" >Show Ads (<?php echo sizeof($_adsensem['ads']); ?>)</a> |</li>
				<li><a href="" class="current">Create New Ad</a></li>
			</ul>
			
			<table>
			<tr>
				<td style="width:50%;vertical-align:top;">
					<h3>Step 1: Import Your Ad Code</h3>
					<p>Simply <strong>paste your Ad Code below</strong> and Import!</p>
					<div>
						<textarea rows="5" cols="65" name="adsensem-code" id="adsensem-code"></textarea>
						<p class="submit" style="text-align:right;vertical-align:bottom;">
							<input type="button" value="Cancel" onclick="document.getElementById('adsensem-action').value='cancel'; this.form.submit();">		
							<input type="button" value="Clear" onclick="document.getElementById('adsensem-code').value='';">		
							<input style="font-weight:bold;" type="submit" value="Import to New Ad Unit&raquo;" onclick="document.getElementById('adsensem-action').value='import';">
						</p>
					</div>		
				</td>
				<td style="width:10%";>&nbsp;</td>
				<td style="width:40%";>
					<p>Advertising Manager supports most Ad networks including <?php adsensem_admin::network_list(); ?>.</p>
				</td>
			</tr>
			</table>
		</form>
	</div>
<?php 
	}
}
?>