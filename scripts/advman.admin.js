var advman_js_text_delete_one = "You are about to delete the ad:";
var advman_js_text_delete_many = "You are about to delete the following ads:";
var advman_js_text_delete_sure = "Are you sure?\n(Press 'Cancel' to keep, 'OK' to delete)";

function advman_set_action(action, id, name)
{
	var submit = true;
	if (action == 'delete') {
		var items = new Array();
		if (id) {
			var ad = document.getElementById('advman-ad-' + id);
			items.push(ad.firstChild.nodeValue);
		} else {
			var cb = document.getElementsByName('advman-targets[]');
			var len = cb.length;
			for (var i=0; i<cb.length; i++) {
				if (cb[i].checked) {
					var ad = document.getElementById('advman-ad-' + cb[i].value);
					items.push(ad.firstChild.nodeValue);
				}
			}
		}
		
		submit = false;
		len = items.length;
		if (len) {
			var msg = (len == 1 ? advman_js_text_delete_one : advman_js_text_delete_many) + '\n\n';
			
			for (var i in items) {
				msg += (items[i] + '\n');
			}
			msg += ('\n' + advman_js_text_delete_sure);
			if ( confirm(msg) ) {
				submit = true;
			}
		}
	}
	
	if (submit) {
		document.getElementById('advman-action').value = action;
		document.getElementById('advman-target').value = id;
		document.getElementById('advman-form').submit();
	}
}

jQuery(document).ready( function($) {
	$('#advman-list-inside').width($('#advman-list-actions').width()-4);
	$('#advman-list-toggle, #advman-list-inside').bind( 'mouseenter', function(){$('#advman-list-inside').removeClass('slideUp').addClass('slideDown'); setTimeout(function(){if ( $('#advman-list-inside').hasClass('slideDown') ) { $('#advman-list-inside').slideDown(100); $('#advman-list-first').addClass('slide-down'); }}, 200) } );
	$('#advman-list-toggle, #advman-list-inside').bind( 'mouseleave', function(){$('#advman-list-inside').removeClass('slideDown').addClass('slideUp'); setTimeout(function(){if ( $('#advman-list-inside').hasClass('slideUp') ) { $('#advman-list-inside').slideUp(100, function(){ $('#advman-list-first').removeClass('slide-down'); } ); }}, 300) } );
});