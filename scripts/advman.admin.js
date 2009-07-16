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

	function advman_form_update(element){
		//element is the calling element, element.id has the identifier
		//detect calling form element and action accordingly
		switch(element.id){
			/* case 'advman-product':advman_update_product(); break; */
			case 'advman-adformat':advman_update_custom(); break;
			case 'advman-adtype':advman_update_formats(); break;
		}
	}
	
	function advman_select_update(element)
	{
		element.style.color = (element.options[0].selected) ? 'gray' : 'black';
	}
	function advman_update_ad(element,id,what)
	{
		target = document.getElementById(id);
		switch (what) {
			case 'bg':	target.style.background='#' + element.value; break;
			case 'border':	target.style.borderColor='#' + element.value; break;
			case 'font-link':
			case 'font-text':
			case 'font-title':
				target.style.fontFamily=element.value; break;
			default : target.style.color='#' + element.value; break;
		}
	}
	
	function advman_update_formats()
	{
		s = document.getElementById('advman-adtype');
		if (s) {
			n = s.length;
			for (i=0; i<n; i++) {
				v = s.options[i].value;
				r = document.getElementById('advman-form-adformat-'+v);
				if (r) {
					r.style.display = s.options[i].selected ? '' : 'none';
				}
			}
		}
	}

		
	function advman_update_custom()
	{
		if(document.getElementById('advman-adformat') && document.getElementById('advman-settings-custom')) {
			format=document.getElementById('advman-adformat').value;
			if(format=='custom'){on='';} else {on='none';}
			document.getElementById('advman-settings-custom').style.display=on
		}
	}
	
	
//Initialize everything (call the display/hide functions)
jQuery(document).ready( function($) {
	
	// close postboxes that should be closed on edit pages
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	if (typeof(postboxes) != 'undefined') {
		postboxes.add_postbox_toggles('advman'); //wp2.7+
	} else {
		add_postbox_toggles('advman'); //wp2.6-
	}
	
	// LIST PAGE:  call the 'set action' field
	
	// Options displayed in comma-separated list
	$("#advman-pagetype").multiSelect({
		oneOrMoreSelected: '*',
		allSelected: 'All Pages',
		noneSelected: 'No Pages',
		selectAllText: 'All Pages'
	});
	$("#advman-author").multiSelect({
		oneOrMoreSelected: '*',
		allSelected: 'All Authors',
		noneSelected: 'No Authors',
		selectAllText: 'All Authors'
	});
	
//	// 'Select All' text changed
//	$("#control_8").multiSelect({ selectAllText: 'Pick &lsquo;em all!' });
	
	advman_update_custom();
	advman_update_formats();

	$('#advman-list-inside').width($('#advman-list-actions').width()-4);
	$('#advman-list-toggle, #advman-list-inside').bind( 'mouseenter', function(){$('#advman-list-inside').removeClass('slideUp').addClass('slideDown'); setTimeout(function(){if ( $('#advman-list-inside').hasClass('slideDown') ) { $('#advman-list-inside').slideDown(100); $('#advman-list-first').addClass('slide-down'); }}, 200) } );
	$('#advman-list-toggle, #advman-list-inside').bind( 'mouseleave', function(){$('#advman-list-inside').removeClass('slideDown').addClass('slideUp'); setTimeout(function(){if ( $('#advman-list-inside').hasClass('slideUp') ) { $('#advman-list-inside').slideUp(100, function(){ $('#advman-list-first').removeClass('slide-down'); } ); }}, 300) } );
});  
//End Initialise
