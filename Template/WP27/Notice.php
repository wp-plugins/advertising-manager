<?php
if(!ADVMAN_VERSION) {die();}

class Template_Notice
{
	function display($notices = null)
	{
		if (is_array($notices)) {
			foreach ($notices as $action => $notice) {
?>				<div id='update-nag'>
				<form action="edit.php?page=advertising-manager-manage-ads" method="post" id="adsensem-config-manage" enctype="multipart/form-data">
				<input type="hidden" name="adsensem-mode" value="notice">		
				<input type="hidden" name="adsensem-action" value="<?php echo $action; ?>">												
<?php
				echo str_replace('Advertising Manager','<strong>Advertising Manager</strong>',$notice['text']);
				if ($notice['confirm'] == 'yn') {
?>				<input name="adsensem-notice-confirm-yes" type="submit" value="Yes">
				<input name="adsensem-notice-confirm-no" type="submit" value="No">
<?php
				} elseif ($notice['confirm'] == 'ok') {
?>				<input name="adsensem-notice-confirm-ok" type="submit" value="OK">
<?php
				} elseif ($notice['confirm'] == 'x') {
?>				<input name="adsensem-notice-confirm-x" type="submit" value="x">
<?php
				}
?>				</form>
				</div>
<?php
			}
		}
	}
}
?>