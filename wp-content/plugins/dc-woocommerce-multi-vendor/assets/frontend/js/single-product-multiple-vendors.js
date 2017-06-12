jQuery(document).ready(function($) {
	var total_div = $('#tab-singleproductmultivendor .rowbody').length;
	if(parseInt(total_div) > 4) {	
		var counter = 0;	
		$("#tab-singleproductmultivendor .rowbody").each(function(){
			if(parseInt(counter) >= 4) {
				$(this).hide();	
			}					
			counter = parseInt(counter) + 1;					
		});
		var data = {
			action: 'get_loadmorebutton_single_product_multiple_vendors'
		}
		$.post(woocommerce_params.ajax_url, data, function( response ) {
			$("#tab-singleproductmultivendor").append(response);
		});
	}
	$('body').on('click','button#wcmp-load-more-button', function(e) {
		$("#tab-singleproductmultivendor .rowbody").each(function(){
			$(this).show('slow');
		});
		$(this).hide('slow');
	});
	$('#wcmp_multiple_product_sorting').change(function(e) {
		$('#tab-singleproductmultivendor .ajax_loader_class_msg').show();	
		var sorting_value = $(this).val();
		var attrid = $(this).attr('attrid');
		if( sorting_value != '') {
			var sorting_data = {
				action: 'single_product_multiple_vendors_sorting',
				sorting_value: sorting_value,
				attrid: attrid
			}
			$.post(woocommerce_params.ajax_url, sorting_data, function( response ) {
				$('#tab-singleproductmultivendor .rowbody').each(function(){
					$(this).remove();
				});
				$('#tab-singleproductmultivendor .rowhead').append(response);
				var counter2 = 0;
				var total_div2 = $('#tab-singleproductmultivendor .rowbody').length;
				if( parseInt(total_div2) > 4 ) {
					if($('#tab-singleproductmultivendor #wcmp-load-more-button').css('display') != 'none') {
						$("#tab-singleproductmultivendor .rowbody").each(function(){
							if(parseInt(counter2) >= 4) {
								$(this).hide();	
							}					
							counter2 = parseInt(counter2) + 1;					
						});
					}
				}
				$('#tab-singleproductmultivendor .ajax_loader_class_msg').hide();
			});
		}
	});
	
	$('.goto_more_offer_tab').click(function(e){
		e.preventDefault();
		$('.woocommerce-tabs ul.tabs li').each(function(){
			$(this).removeClass('active');	
		});
		$('.woocommerce-tabs ul.tabs li.singleproductmultivendor_tab').addClass('active');	
		$('.woocommerce-tabs>div').each(function(){
			$(this).hide();	
		});
		$('.woocommerce-tabs div#tab-singleproductmultivendor').show();
		if(!$('.woocommerce-tabs div#tab-singleproductmultivendor').hasClass('active') ) {
			$('.woocommerce-tabs div#tab-singleproductmultivendor').addClass('active');
		}
		$('html, body').animate({
        scrollTop: $("#tab-singleproductmultivendor").offset().top
    }, 2000);		
	});
	
});