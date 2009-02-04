
	function adsensem_form_update(element){
		//element is the calling element, element.id has the identifier
		//detect calling form element and action accordingly
		switch(element.id){
			/* case 'adsensem-product':adsensem_update_product(); break; */
			case 'adsensem-adformat':adsensem_update_custom(); break;
			case 'adsensem-adtype':adsensem_update_formats(); break;
		}
	}
		
		
	function adsensem_update_color(element,id,what)
	{
		target = document.getElementById(id);
		switch (what) {
			case 'bg':	target.style.background='#' + element.value; break;
			case 'border':	target.style.border='1px solid #' + element.value; break;
			default : target.style.color='#' + element.value; break;
		}
	}
		
	function adsensem_update_formats()
	{
		s = document.getElementById('adsensem-adtype');
		if (s) {
			n = s.length;
			//alert('n='+n);
			for (i=0; i<n; i++) {
				v = s.options[i].value;
				r = document.getElementById('adsensem-form-'+v+'-format');
				//alert('n='+n+',i='+i+',v='+v+',r='+r);
				if (r) {
					r.style.display = s.options[i].selected ? '' : 'none';
				}
			}
		}
	}

		
	function adsensem_update_custom(){
	
		if(document.getElementById('adsensem-adformat') && document.getElementById('adsensem-settings-custom')){
			format=document.getElementById('adsensem-adformat').value;
		
			if(format=='custom'){on='';} else {on='none';}
			document.getElementById('adsensem-settings-custom').style.display=on
		}
	}
	
	
//Initialize everything (call the display/hide functions)
jQuery(document).ready( function($) {
	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	if (typeof(postboxes) != 'undefined') {
		postboxes.add_postbox_toggles('adsensem'); //wp2.7+
	} else {
		add_postbox_toggles('adsensem'); //wp2.6-
	}
	adsensem_update_custom();
	adsensem_update_formats();
});  
//End Initialise
