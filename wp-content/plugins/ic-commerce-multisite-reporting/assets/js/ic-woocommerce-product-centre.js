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
	/*Click*/
	jQuery(".error_messange").hide();
	$(".please_wait").hide();
	
	jQuery( "#btnSearch" ).click(function() {
		
		var searching = $("#searching").val();
	
		if(searching == 1){
			return false;
		}
		
	  	var variation_id 	= $("#variation_id").val();
		var product_id 		= $("#product_id").val();
		
		if(product_id == 0 || product_id == "" || product_id == ''){
			product_id = 0;
		}
		
		if(product_id > 0){
			get_product_data(product_id,variation_id);
		}else{
			jQuery(".error_messange").find('p').html("Please search product.");
			jQuery(".error_messange").show();
		}
		
	});
	/*End Click*/
	
	get_product_data();
	
	/*Date Time Picker*/
	jQuery('._proc_date').datepicker({
        dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
    });
	/*Product Search*/
	$(document).on("focus.autocomplete", "._product_name", function () {
        var searching = $("#searching").val();	
		if(searching == 1){return false;}
		
		$(this).autocomplete({
             source: function(req, response){				
			   $.getJSON(ic_pc_ajax_object.ic_pc_ajax_url+'?sub_action=icwcprocentre_product&call=all_product_list&action='+acs_action, req, response);
			},
			select: function(event, ui) {
				 $("._product_id").val(ui.item.product_id);
				 $("._variation_id").val(ui.item.variation_id);
				 
				 if(ui.item.product_id > 0){
					$("#btnSearch").show();
					jQuery(".error_messange").hide();
				 }
				 
			},
            minLength: 3,
        });

        $(this).autocomplete("search");
    });
	
	$("#start_date,#end_date, ._product_name").change(function(){
		$("#btnSearch").show();	
	});
	
	$(".ic_refresh_icon").hide();
	 
});

function get_product_data(product_id, variation_id){
	$ = jQuery;
	
	var searching = $("#searching").val();
	
	if(searching == 1){
		return false;
	}
	
	var start_date 	= $("#start_date").val();
	var end_date 	= $("#end_date").val();
	
	$(".please_wait").show();
	jQuery(".error_messange").hide();
	
	//$("#btnSearch").hide();
	
	block_content();
	$(".ic_refresh_icon").show();
	
	$("#searching").val(1);
	$("#btnSearch").addClass('ic_disabled');
	
	$.ajax({
		//url: ajaxurl,
		url:ic_pc_ajax_object.ic_pc_ajax_url,
		//data:$("#frm_purchase").serialize(),
		data: {
			'action'			: 'icwcprocentre_ajax',
			'sub_action' 		: 'icwcprocentre',
			'call' 			  	: 'list',
			'product_id' 		: product_id,
			'variation_id' 		: variation_id,
			'start_date' 		: start_date,
			'end_date' 			: end_date,
		},
		success:function(response) {
				//alert(JSON.stringify(response));
				
				$(".ic_dashboard").fadeIn('slow');
				$(".please_wait").hide();
				
				unblock_content();
				$(".ic_refresh_icon").hide();
				
				data = JSON.parse(response);
				//alert(JSON.stringify(data.product_monthly_sales));
				product_monthly_sales  = data.product_monthly_sales ;
				product_daily_sales  = data.product_daily_sales ;
				
				//alert(JSON.stringify(product_daily_sales));
				
				$("#variation_id").val(data.variation_id);
				$("#product_id").val(data.product_id);
				$("._product_name").val(data.product_name);
				
				currency_symbol = data.currency_symbol;
				
				jQuery("._product_description").html(data.description);
				
				/*Product Name*/
				jQuery("._product_name").html(data.product_name);
			
				/*Product Name*/
				jQuery("._product_name").html(data.product_name);
				
				/*Product Image URL*/
				jQuery("._product_image").attr("src",data.image_url);
				 
				 /*_max_qty_order_date*/
				 jQuery("._max_qty_order_date").html(data.max_qty_order_date);
				 
				 
				 /*max_order_date*/
				 jQuery("._max_order_date").html(data.max_order_date);
				 
				  /*min_order_date*/
				 jQuery("._min_order_date").html(data.min_order_date);
				 
				   /*min_order_date*/
				 jQuery("._max_quanity").html(data.max_quanity);
				 
				 
				 /*Sales*/
				 jQuery("._product_sales_qty").html(data.product_sales.qty);
				 jQuery("._product_sales_line_total").html(data.product_sales.line_total_html);
				 
				 /*Refund*/
				 jQuery("._product_refund_qty").html(data.product_refund.qty);
				 jQuery("._product_refund_line_total").html(data.product_refund.line_total);
				 
				 
				 /*Coupon*/
				 jQuery("._product_coupon_qty").html(data.product_coupon.qty);
				 jQuery("._product_coupon_line_total").html(data.product_coupon.line_total);
				 
				 
				 /*product_average_sales*/
				 jQuery("._product_average_sales_qty").html(data.product_average_sales.qty);
				 jQuery("._product_average_sales_line_total").html(data.product_average_sales.line_total);
				 
				  /*product_new_customer*/
				 jQuery("._product_new_customer_qty").html(data.product_new_customer.qty);
				 jQuery("._product_new_customer_line_total").html(data.product_new_customer.line_total);
				 
				 
				  /*product_repeat_customer*/
				 jQuery("._product_repeat_customer_qty").html(data.product_repeat_customer.qty);
				 jQuery("._product_repeat_customer_line_total").html(data.product_repeat_customer.line_total);
		
			  /*Product Country*/
				html = '';
				html += '<table class="ic_table">';
				html += '<thead>';
				html += '	<tr>';
				html += '		<th>Country</th>';
				html += '		<th class="align_right">Qty.</th>';
				html += '		<th class="align_right">Amount</th>';
				html += ' 	</tr>';
				html += '</thead>';
				html += '<tbody>';
				 $.each(data.product_country, function(key,value) {
					html += '<tr>';
					html += '	<td>'+ value.billing_country +'</td>';
					html += '	<td class="align_right">'+ value.qty +'</td>';
					html += '	<td class="align_right">'+ value.line_total +'</td>';                            
					html += '</tr>';
				 });
				html += '</tbody>';
				html += ' </table>';
				
				jQuery("._product_product_country").html(html);
					
				/*Product Order Status*/
				html = '';
				html += '<table class="ic_table">';
				html += '<thead>';
				html += '	<tr>';
				html += '		<th>Order Status</th>';
				html += '		<th class="align_right">Qty.</th>';
				html += '		<th class="align_right">Amount</th>';
				html += ' 	</tr>';
				html += '</thead>';
				html += '<tbody>';
				 $.each(data.product_order_status, function(key,value) {
					html += '<tr>';
					html += '	<td>'+ value.post_status +'</td>';
					html += '	<td class="align_right">'+ value.qty +'</td>';
					html += '	<td class="align_right">'+ value.line_total +'</td>';                            
					html += '</tr>';
				 });
				html += '</tbody>';
				html += ' </table>';
				
				jQuery("._product_order_status").html(html);
				
				
				
				html = '';
				html += '<table class="ic_table">';
				html += '<thead>';
				html += '	<tr>';
				//html += '		<th>Country</th>';
				//html += '		<th>Qty.</th>';
				html += '		<th></th>';
				$.each(data.product_monthly_sales, function(key,value) {
					html += '	<th class="align_right">'+ value.post_date +'</th>';					
				});
				html += ' 	</tr>';
				html += '</thead>';
				html += '<tbody>';
				html += '	<tr>';
					html += '		<td>Quantity</td>';
					$.each(data.product_monthly_sales, function(key,value) {
						html += '	<td class="align_right">'+ value.qty +'</td>';					
					});
				html += '	</tr>';
				html += '	<tr>';
					html += '		<td>Sales Amt.</td>';
					$.each(data.product_monthly_sales, function(key,value) {
						html += '	<td class="align_right">'+ value.line_total_html +'</td>';					
					});
				html += '	</tr>';
				html += '</tbody>';
				html += ' </table>';
				
				jQuery("._product_monthly_sales").html(html);
				
				html = '';
				html += '<div class="sum_stk_plner">';
				html += '	<div class="col-sm-3 box box_yellow">';
				html += '		<span class="title">Current Stock Quantity</span>';
				html += '		<span class="count">'+ data.summary_stock_planner.current_stock_quantity +'</span>';
				html += '	</div>';
							
				html += '	<div class="col-sm-3 box box_green">';
				html += '		<span class="title">Average Sales Quantity</span>';
				html += '		<span class="count">'+ data.summary_stock_planner.avg_sales_quantity +'</span>';
				html += '	</div>';
							
				html += '	<div class="col-sm-3 box box_skyblue">';
				html += '		<span class="title">Stock Valid Days</span>';
				html += '		<span class="count">'+ data.summary_stock_planner.stock_valid_days +'</span>';
				html += '	</div>';
							
				html += '	<div class="col-sm-3 box box_light_blue">';
				html += '		<span class="title">Stock Valid Date</span>';
				html += '		<span class="count">'+ data.summary_stock_planner.stock_valid_date +'</span>';
				html += '	</div>';
				html += '</div>';				
				//html += '	<td class="align_right">'+ data.summary_stock_planner.sales_quantity +'</td>';
				
				jQuery("._summary_stock_planner").html(html).show();					
				get_product_monthly_sales();
				get_product_daily_sales();
				updateDonutChart('#product_percentage_sales', data.product_percentage_sales.percentage_sales, true);
				updateDonutChart('#product_percentage_refund', data.product_percentage_refund.percentage_sales, true);
				$("#searching").val(0);
				$("#btnSearch").removeClass('ic_disabled');
			
		},
		error: function(errorThrown){
			$("#searching").val(0);
			$("#btnSearch").removeClass('ic_disabled');
			
			console.log(errorThrown);
			alert("e2");
		}
	});
	return false;

} 
function get_product_monthly_sales(){
	/*Chart Start Here*/
	 chart = AmCharts.makeChart("_product_monthly_sales", {
		  "type": "serial",
		  "theme": "light",
		  "marginRight": 70,
		  "dataProvider": product_monthly_sales,
		  "valueAxes": [{
			"axisAlpha": 0,
			"position": "left",
			"title": "Product Monthly Sales"
		  }],
		  "startDuration": 1,
		  "graphs": [{
			"balloonText": "<b>[[category]]: "+currency_symbol+"[[value]]</b>",
			//"balloonText": "Quantity: [[quantity]]<br />Sales Amount: "+currency_symbol+"[[amount]]",
			"fillColorsField": "color",
			"fillAlphas": 0.9,
			"lineAlpha": 0.2,
			"type": "column",
			"valueField": "line_total"
		  }],
		  "chartCursor": {
			"categoryBalloonEnabled": false,
			"cursorAlpha": 0,
			"zoomable": false
		  },
		  "categoryField": "post_date",		  
		  "categoryAxis": {
			"gridPosition": "start",
			"labelRotation": 45
			
		  },
		  "export": {
			"enabled": false
		  }
		
		});
		
		//alert(1)
	/*End Chart Heter*/

}
function get_product_daily_sales(){
	/*Chart Start Here*/
	var chart = AmCharts.makeChart("_product_daily_sales", {
		"type": "serial",
		"theme": "light",
		"marginRight": 40,
		"marginLeft": 40,
		"autoMarginOffset": 20,
		"mouseWheelZoomEnabled":true,
		"dataDateFormat": "YYYY-MM-DD",
		"valueAxes": [{
			"id": "v1",
			"axisAlpha": 0,
			"position": "left",
			"ignoreAxisWidth":true
		}],
		"balloon": {
			"borderThickness": 1,
			"shadowAlpha": 0
		},
		"graphs": [{
			"id": "g1",
			"balloon":{
			  "drop":false,
			  //"adjustBorderColor":false,
			  //"color":"#ffffff"
			},
			"bullet": "round",
			"bulletBorderAlpha": 1,
			"bulletColor": "#FFFFFF",
			"bulletSize": 10,
			"hideBulletsCount": 50,
			"lineThickness": 2,
			"title": "Product daily Sales",
			"useLineColorForBulletBorder": true,
			"valueField": "amount",
			//"balloonText": "<span style='font-size:18px;'>[[value]]</span>",
			"balloonText": "Quantity: [[quantity]]<br />Sales Amount: "+currency_symbol+"[[amount]]",
			/*"balloonFunction": function(item, graph) {
			  var result = graph.balloonText;
			  for (var key in item.dataContext) {
				if (item.dataContext.hasOwnProperty(key) && !isNaN(item.dataContext[key])) {
				  var formatted = AmCharts.formatNumber(item.dataContext[key], {
					precision: chart.precision,
					decimalSeparator: chart.decimalSeparator,
					thousandsSeparator: chart.thousandsSeparator
				  }, 2);
				  result = result.replace("[[" + key + "]]", formatted);
				}
			  }
			  return result;
			}*/
		}],
		/*"chartScrollbar": {
			"graph": "g1",
			"oppositeAxis":false,
			"offset":30,
			"scrollbarHeight": 80,
			"backgroundAlpha": 0,
			"selectedBackgroundAlpha": 0.1,
			"selectedBackgroundColor": "#888888",
			"graphFillAlpha": 0,
			"graphLineAlpha": 0.5,
			"selectedGraphFillAlpha": 0,
			"selectedGraphLineAlpha": 1,
			"autoGridCount":true,
			"color":"#AAAAAA"
		},*/
		"chartCursor": {
			"pan": false,
			"valueLineEnabled": true,
			"valueLineBalloonEnabled": true,
			"cursorAlpha":1,
			"cursorColor":"#258cbb",
			"limitToGraph":"g1",
			"valueLineAlpha":0.2,
			"valueZoomable":true
		},
		"valueScrollbar":{
		  "oppositeAxis":false,
		  "offset":50,
		  "scrollbarHeight":10
		},
		"categoryField": "order_date",
		"categoryAxis": {
			"parseDates": true,
			"dashLength": 1,
			"minorGridEnabled": true
		},
		"export": {
			"enabled": false
		},
		"dataProvider":product_daily_sales
	});
		/*End Chart Heter*/

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