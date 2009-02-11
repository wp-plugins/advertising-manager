<?php
if(!ADVMAN_VERSION) {die();}

class Template_CreateAd
{
	function Template_CreateAd()
	{
		// Scripts
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-ui-draggable');
	}
	
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_adsensem;
		
?><div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2><?php _e('Create Ad:', 'advman'); ?></h2>
	<form action="" method="post" id="adsensem-form" enctype="multipart/form-data">
	<input type="hidden" name="adsensem-mode" id="adsensem-mode" value="edit_ad">
	<input type="hidden" name="adsensem-action" id="adsensem-action">
	<input type="hidden" name="adsensem-action-target" id="adsensem-action-target">

	<ul class="subsubsub">
		<li><a href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='list'; document.getElementById('adsensem-form').submit();" >Show Ads (<?php echo sizeof($_adsensem['ads']); ?>)</a> |</li>
		<li><a href="" class="current"><?php _e('Create New Ad', 'advman'); ?></a></li>
	</ul>
	<br />
	<p>&nbsp;</p>
	<p><h3><?php _e('Step 1: Import Your Ad Code', 'advman'); ?></h3></p>
	<p><?php _e('Simply <strong>paste your Ad Code below</strong> and Import!', 'advman'); ?></p>
	
<div id="post-body" class="has-sidebar">
	<div id="post-body-content" class="has-sidebar-content" style="width:600px">

<label class="hidden" for="excerpt"><?php _e('Code'); ?></label><textarea rows="8" cols="70" name="adsensem-code" tabindex="6"></textarea>
<p><span style="font-size:x-small;color:gray;"><?php _e('Advertising Manager will automatically detect many ad network tags', 'advman'); ?></span></p>

			<div id="publishing-action">
				<a class="submitdelete deletion" href="javascript:submit();" onclick="document.getElementById('adsensem-action').value='cancel'; document.getElementById('adsensem-form').submit();"><?php _e('Cancel', 'advmgr') ?></a>&nbsp;&nbsp;&nbsp;
				<input type="submit" class="button-primary" id="advman_save" tabindex="5" accesskey="p" value="<?php _e('Import', 'advman'); ?>" onclick="document.getElementById('adsensem-action').value='import';" />
			</div>
			<div class="clear"></div>
	</form>
</div><!-- wpwrap -->
<?php 
	}
}
?>