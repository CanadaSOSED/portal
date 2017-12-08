jQuery(document).ready(function($) {	
	$('#vendor_image_remove_button').hide();
	$('#vendor_banner_remove_button').hide();
	$('.edit_shop_settings').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
		$('.wcmp_shop_settings_form input[type=text], .wcmp_shop_settings_form textarea, .wcmp_billing_form .select_box').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});
		$('#vendor_image_remove_button').show();
		$('#vendor_banner_remove_button').show();
		$('.green_massenger').each(function(e){$(this).remove();});
		$('.red_massenger').each(function(e){$(this).remove();});
	});
	$('.edit_policy').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
		$('.wcmp_policy_form input[type=text], .wcmp_policy_form textarea, .wcmp_billing_form .select_box').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});
		$('.green_massenger').each(function(e){$(this).remove();});
	});
	$('.edit_billing').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
		$('.wcmp_billing_form input[type=text], .wcmp_billing_form textarea, .wcmp_billing_form .select_box').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});
		$('#vendor_payment_mode').removeAttr('disabled');     
		$('#vendor_bank_account_type').removeAttr('disabled');
		$('.green_massenger').each(function(e){$(this).remove();});
	});
	
	$('.edit_shipping').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
		
		
		$('.wcmp_shipping_form input[type=text]').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});		
		$('.green_massenger').each(function(e){$(this).remove();});
		$('.red_massenger').each(function(e){$(this).remove();});
		
	});
	
});