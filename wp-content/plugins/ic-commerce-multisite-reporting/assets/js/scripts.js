var data ;
var html = '';
var product_monthly_sales ;
var product_daily_sales;
var chart;
var acs_action 		= 'icwcprocentre_ajax';
var ic_product_id 	= 0;
var ic_variation_id = 0;
var currency_symbol = "$";

jQuery(function($){
	
	jQuery(".error_messange").hide();
	jQuery(".please_wait").hide();
	
	/*Click*/
	jQuery( "#btnSearch" ).click(function() {		
		var searching = $("#searching").val();
		if(searching == 1){
			return false;
		}		
		var userblog_id = $("#userblog_id").val();		
		set_site_data(userblog_id);
	});
	/*End Click*/
	
	$("#userblog_id").change(function(){
		$("#btnSearch").show();
		$("#searching").val(0);
		$("#btnSearch").removeClass('ic_disabled');
	});
	
	$(".ic_refresh_icon").hide();	
	
	
	
	jQuery( "#start_date" ).datepicker({
		dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		maxDate:0,
		onClose: function( selectedDate ) {
			$( "#end_date" ).datepicker( "option", "minDate", selectedDate );
		}
	});							
	
	jQuery( "#end_date" ).datepicker({
		dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		maxDate: 0,
		onClose: function( selectedDate ) {
			$( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
		}
	});  
});

function reset_form(){
	unblock_content();
	$(".ic_refresh_icon").hide();				
	$("#userblog_id").attr('disabled',false);
	$("#searching").val(0);
	$("#btnSearch").removeClass('ic_disabled');
	$(".please_wait").hide();
}

function set_site_data(userblog_id){
	
	jQuery(".ic_dashboard").fadeIn('slow');
				
	$ = jQuery;
	$("#userblog_id").attr('disabled',true);
	$("#searching").val(1);
	$("#btnSearch").addClass('ic_disabled');
	$(".please_wait").show();
	
	block_content();
	
	var start_date 		= $("#start_date").val();
	var end_date 		= $("#end_date").val();
	var userblog_id 	= $("#userblog_id").val();
	var user_id 		= $("#user_id").val();
	
	var form_data = {
		'action'			: icmsr_ajax_object.ajax_action,
		'sub_action' 		: 'dashboard_data',
		'userblog_id' 		: userblog_id,
		'user_id' 			: user_id,
		'start_date' 		: start_date,
		'end_date' 			: end_date
	}
	
	currency_symbol = icmsr_ajax_object.currency_symbol,
	
	$.ajax({
		type		: "POST",
		url			: icmsr_ajax_object.ajax_url,
		dataType	: "json",
		data		: form_data,
		success		:function(response) {
			console.log(response);
						
			$(".ic_dashboard").fadeIn('slow');
			var summary_boxes = response.summary_boxes;
			$.each(summary_boxes, function (summary_box_index, summary_box_value) {
				$("."+summary_box_index).html(summary_box_value);
			});
			
			var chart_data = response.chart_data;
			pie_chart(chart_data,'site_wise_sales_chart');
			
			reset_form();
		},
		error		:function(errorThrown){
			reset_form();
			console.log(errorThrown);
		}
	});
	
	return false;
}

var currency_symbol		= "&";
function pie_chart(response, do_inside_id) {
	var $ = jQuery;
	
    try {
			// PIE CHART
			var chart = new AmCharts.AmPieChart();
			//chart.type = 'funnel';
			//chart.theme= "light",
			chart.dataProvider = response;
			chart.titleField = "blogname";
			chart.valueField = "amoount";
			chart.outlineColor = "#FFFFFF";
			chart.outlineAlpha = 0.8;
			chart.outlineThickness = 2;
			chart.fontSize = 12;
			chart.sequencedAnimation = true;
			chart.startEffect = "elastic";
			chart.innerRadius = "30%";
			chart.startDuration = 2;
			chart.labelRadius = 15;			
			chart.balloonText = "[[title]]<br><span style='font-size:14px'><b>"+currency_symbol+"[[amoount]]</b> ([[percents]]%)</span>";
			
			// the following two lines makes the chart 3D
			chart.depth3D = 10;
			chart.angle = 15;
			
			/* chart.legend = {
				"position":"right",
				"marginRight":100,
				"autoMargins":false
			  },*/

			// WRITE
			chart.write(do_inside_id);
			
			//Updating the graph to show the new data
			chart.validateData();
    } catch (e) {
        alert(e.message);
    }

}

function block_content(){
	jQuery('div.block_content').block({ 
		//message: '<h2>Processing, please wait!</h2>', 
		//message: '<img src="http://www.accufinance.com/images/busy.gif" id="loader"/>',
		message: null,
		css: {
			backgroundColor			: '#fff',
			'-webkit-border-radius'	: '10px',
			'-moz-border-radius'	: '10px',
			border		:'1px solid #5AB6DF',
			//border		:'none',
			padding		:'15px',
			paddingTop	:'19px',
			opacity		:.9,
			color		:'#fff'
		},
		overlayCSS: {
			//backgroundColor: '#fff',
			//background	: 'none',
			opacity		: 0.6
		}
	});
}

function unblock_content(){
	jQuery('div.block_content').unblock();
}