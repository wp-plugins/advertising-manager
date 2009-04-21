/*
 * Viewport - jQuery selectors for finding elements in viewport
 *
 * Copyright (c) 2008-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *  http://www.appelsiini.net/projects/viewport
 *
 */


var traverse = function(obj,chain,time) {
	$(obj).ready(function() {
		$(obj).children().each(function() {
			if (this.tagName == 'IFRAME') {
				$(this).load(function() {
					alert(this);
				});
			}
		});
	});
	$(obj).ready(function() {
		if (obj.src) {
			console.log(obj.src);
			chain.push(obj.tagName+'_'+obj.src);
		}
		alert(obj.tagName);
		if (obj.tagName == 'IFRAME') {
			alert($(obj).contents().length);
			alert($(obj).contents().html());
			alert($(obj).contents().children().length);
			$(obj).contents().children().each(function() {
				chain = traverse(this,chain,time);
			});
		} else {
			alert($(obj).children().length);
			$(obj).children().each(function() {
				chain = traverse(this,chain,time);
			});
		}
	});
	return chain;
};

var ox_inview = function(o) {
	// inview is when a % of the element is within the viewable area of the screen
	var threshold = 0.5;

	var o_height = $(o).outerHeight(false);
	var o_width = $(o).outerWidth(false);
	var o_top = $(o).offset().top;
	var o_left = $(o).offset().left;
	var o_bottom = o_top + o_height;
	var o_right = o_left + o_width;
	
	var w_height = $(window).height();
	var w_width = $(window).width();
	var w_top = $(window).scrollTop();
	var w_left = $(window).scrollLeft();
	var w_bottom = w_top + w_height;
	var w_right = w_left + w_width;
	
	if (o_top <= w_bottom && o_bottom >= w_top && o_left <= w_right && o_right >= w_left) {
		var width = (o_right>w_right?w_right:o_right) - (o_left>w_left?o_left:w_left);
		var height = (o_bottom>w_bottom?w_bottom:o_bottom) - (o_top>w_top?o_top:w_top);
		return ( (width*height) / (o_width*o_height) >= threshold);
	}
	
	return false;
};

var ox_log = function(action, object) {
	console.log('action:'+action+', object:'+object+', page:'+page);
};