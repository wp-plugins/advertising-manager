	var advman_str_delete_confirm = "You are about to permanently delete the %s ad:";
	var advman_str_delete_confirm2 = "Are you sure?\n(Press 'Cancel' to keep, 'OK' to delete)";
	
	function advman_set_action(action, id, name, network)
	{
		submit = true;
		if (action == 'delete') {
			if ( confirm(advman_str_delete_confirm.replace(/%s/, network) + '\n\n  [' + id + '] ' + name + '\n\n' + advman_str_delete_confirm2) ) {
				submit = true;
			} else {
				submit = false;
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
	
/*	
//Initialize everything (call the display/hide functions)
jQuery(document).ready( function($) {
	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	if (typeof(postboxes) != 'undefined') {
		postboxes.add_postbox_toggles('advman'); //wp2.7+
	} else {
		add_postbox_toggles('advman'); //wp2.6-
	}
	
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
	
	advman_update_custom();
	advman_update_formats();
	
	$('#advman-list-inside').width($('#advman-list-actions').width()-4);
	$('#advman-list-toggle, #advman-list-inside').bind( 'mouseenter', function(){$('#advman-list-inside').removeClass('slideUp').addClass('slideDown'); setTimeout(function(){if ( $('#advman-list-inside').hasClass('slideDown') ) { $('#advman-list-inside').slideDown(100); $('#advman-list-first').addClass('slide-down'); }}, 200) } );
	$('#advman-list-toggle, #advman-list-inside').bind( 'mouseleave', function(){$('#advman-list-inside').removeClass('slideDown').addClass('slideUp'); setTimeout(function(){if ( $('#advman-list-inside').hasClass('slideUp') ) { $('#advman-list-inside').slideUp(100, function(){ $('#advman-list-first').removeClass('slide-down'); } ); }}, 300) } );



	var newCat, noSyncChecks = false, syncChecks, catAddAfter;

	$('#link_name').focus();
	// postboxes
	postboxes.add_postbox_toggles('link');

	// category tabs
	$('#category-tabs a').click(function(){
		var t = $(this).attr('href');
		$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
		$('.tabs-panel').hide();
		$(t).show();
		if ( '#categories-all' == t )
			deleteUserSetting('cats');
		else
			setUserSetting('cats','pop');
		return false;
	});
	if ( getUserSetting('cats') )
		$('#category-tabs a[href="#categories-pop"]').click();

	// Ajax Cat
	newCat = $('#newcat').one( 'focus', function() { $(this).val( '' ).removeClass( 'form-input-tip' ) } );
	$('#category-add-submit').click( function() { newCat.focus(); } );
	syncChecks = function() {
		if ( noSyncChecks )
			return;
		noSyncChecks = true;
		var th = $(this), c = th.is(':checked'), id = th.val().toString();
		$('#in-link-category-' + id + ', #in-popular-category-' + id).attr( 'checked', c );
		noSyncChecks = false;
	};

	catAddAfter = function( r, s ) {
		$(s.what + ' response_data', r).each( function() {
			var t = $($(this).text());
			t.find( 'label' ).each( function() {
				var th = $(this), val = th.find('input').val(), id = th.find('input')[0].id, name = $.trim( th.text() ), o;
				$('#' + id).change( syncChecks );
				o = $( '<option value="' +  parseInt( val, 10 ) + '"></option>' ).text( name );
			} );
		} );
	};

	$('#categorychecklist').wpList( {
		alt: '',
		what: 'link-category',
		response: 'category-ajax-response',
		addAfter: catAddAfter
	} );

	$('a[href="#categories-all"]').click(function(){deleteUserSetting('cats');});
	$('a[href="#categories-pop"]').click(function(){setUserSetting('cats','pop');});
	if ( 'pop' == getUserSetting('cats') )
		$('a[href="#categories-pop"]').click();

	$('#category-add-toggle').click( function() {
		$(this).parents('div:first').toggleClass( 'wp-hidden-children' );
		$('#category-tabs a[href="#categories-all"]').click();
		return false;
	} );

	$('.categorychecklist :checkbox').change( syncChecks ).filter( ':checked' ).change();


});
*/
//End Initialise
