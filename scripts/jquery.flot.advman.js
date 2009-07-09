function advman_plot()
{
	var mn, ov;  // graphs
	var d = new Array(); // data
	var previousPoint = null; // point to highlight for tool tips
	var from = 0; // from and to on the main graph
	
	// Options for main chart
	var mn_options = {
		lines: { show: true, fill: true },
		xaxis: { mode: "time" },
		selection: { mode: "x" },
		grid: {
			markings: weekendAreas,
			hoverable: true,
			autoHighlight: true
		},
		legend: {
			show: true,
			position: "nw"
		}
	};

	// Function to display tooltip
	function showTooltip(x, y, contents) {
        	$('<div id="advman-tt">' + contents + '</div>').css( {
            		position: 'absolute',
            		display: 'none',
            		top: y + 5,
            		left: x + 5,
            		border: '1px solid #fdd',
            		padding: '2px',
            		'background-color': '#fee',
            		opacity: 0.80
        	}).appendTo("body").fadeIn(200);
    	}
 
	// User hovering over a data point
	$("#advman-mn").bind("plothover", function (event, pos, item) {
        	$("#x").text(pos.x.toFixed(2));
        	$("#y").text(pos.y.toFixed(2));

		if (item) {
                	if (previousPoint != item.datapoint) {
                    		previousPoint = item.datapoint;
                    		$("#advman-tt").remove();
                    		var x = item.datapoint[0].toFixed(2);
				x = new Date(Math.round(x));
				x = months[x.getUTCMonth()] + " " + x.getUTCDate();
				var y = item.datapoint[1].toFixed(2);
				y = Math.round(y);
                    		showTooltip(item.pageX, item.pageY, y + " users on " + x);
//	                	mn.highlight(item.series, item.datapoint);
			}
            	} else {
			$("#advman-tt").remove();
			previousPoint = null;
		}
	});

	// User selecting an area on the plot
	$("#advman-mn").bind("plotselected", function (event, ranges) {
		// do the zooming
		mn = $.plot($("#advman-mn"), [d],
			$.extend(true, {}, mn_options, {
			xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
			})
		);
		
		// don't fire event on the overview to prevent eternal loop
//		ov.setSelection(ranges, true);
	});
	
	// Function to draw data when retrieval is complete
	var lp_data_complete = function(data) {
		from = data['from'];
		if (data['data'].length > 0) {
			var newdata = 0;
			for (var i in data['data']) {
				found = false;
				for (var j in d) {
					if (d[j][0] == (data['data'][i][0] * 1000)) {
						d[j][1] += data['data'][i][1];
						newdata += data['data'][i][1];
						found = true;
						break;
					}
				}
				if (!found) {
					var dd = data['data'][i];
					dd[0] = dd[0] * 1000;
					d.push(dd);
					newdata += dd[1];
				}
			}
			mn = $.plot($("#advman-mn"), [{ label: "Active Users", data: d}], mn_options);
			var text = newdata + (newdata == 1 ? ' new publisher' : ' new publishers'); 
			$("#advman-st").text(text).fadeOut(1500, function() {
				$(this).text(' ').fadeIn();
			});
		}
		from = data['to'] + 1;  // start the next query 1 second after the previous query
	};
	
	// Function to retrieve data
	var lp_get_data = function() {
		$.ajax({
			type: "GET",
			url: "get_data.php?from=" + from,
			dataType: 'json',
			async: true, /* If set to non-async, browser shows page as "Loading.."*/
			cache: false,
			timeout: (60 * 1000),
	    
			success: function(data) {
				lp_data_complete(data); // Process data and plot
				setTimeout( lp_get_data, (1 * 1000) ); // Request next message after n seconds
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				setTimeout( lp_get_data, (60 * 1000) ); // Try again after n seconds
			},
		});
	};
	
	
	// Helper to get the month names
	var months = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"); // static lookup for month names
	// helper for returning the weekends in a period
	function weekendAreas(axes)
	{
		var markings = [];
		var d = new Date(axes.xaxis.min);
		// go to the first Saturday
		d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
		d.setUTCSeconds(0);
		d.setUTCMinutes(0);
		d.setUTCHours(0);
		var i = d.getTime();
		do {
			// when we don't set yaxis the rectangle automatically
			// extends to infinity upwards and downwards
			markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
			i += 7 * 24 * 60 * 60 * 1000;
		} while (i < axes.xaxis.max);
		
		return markings;
	}
	lp_get_data();
});
<div id="advman-st" style="width:600px;height:25px;text-align:center;color:green"></div>
<div id="advman-mn" style="width:600px;height:300px;"></div>
