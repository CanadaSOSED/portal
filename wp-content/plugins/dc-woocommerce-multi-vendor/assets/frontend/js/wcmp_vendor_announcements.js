jQuery(document).ready(function($){
	jQuery(function($) {
		$( "#tabs-1" ).tabs();
	});	
	jQuery(function($) {
		$( "#accordion-1" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});
		$( "#accordion-2" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});	
		$( "#accordion-3" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});
		$( "#accordion-4" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});
	});		
	$("body").on("click", ".msg_stat_click", function(e){		
		var myparent = $(this).parent();		
		$(".msg_stat").slideUp();
		if(!myparent.hasClass('oppned')) {
			$(this).next(".msg_stat").slideDown(300);	
			myparent.addClass('oppned');
		}
		else {
			myparent.removeClass('oppned');
		}
	});		
	jQuery("body").on("click", ".msg_stat_click", function(event){
		event.preventDefault();
	});
	$("body").on("click", "._wcmp_vendor_message_delete", function(e){
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parent().parent().parent().parent();
		var element_to_be_deleted2 = $(this).parent().parent().parent().parent().next();
		var tab_element = $(this).parent().parent().parent().parent().parent().parent().parent();		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');
		var data = {
			action : 'wcmp_vendor_announcements_operation',
			actionmode : 'mark_delete',
			msg_id : msg_id			
		}
		$.post(woocommerce_params.ajax_url, data, function(res) {			
			if(	res == 1 ) {
				element_to_be_deleted1.hide();
				element_to_be_deleted2.hide();
				$("#wcmp_msg_tab_to_be_refrash").val('_archive');
				$("#wcmp_msg_tab_to_be_refrash2").val('_read');
				$("#wcmp_msg_tab_to_be_refrash3").val('_unread');
				var data = {
					action : 'wcmp_announcements_refresh_tab_data',
					tabname : tab_to_refrash					
				}
				$.post(woocommerce_params.ajax_url, data, function(res) {
					$("#"+tab_id+' .msg_container').html(res);
					jQuery(function($) {
						$( "#accordion-1" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						$( "#accordion-2" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});	
						$( "#accordion-3" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						
					});
					jQuery('#accordion-1').accordion("refresh");
					jQuery('#accordion-2').accordion("refresh");
					jQuery('#accordion-3').accordion("refresh");
					
					lodder.hide();
				});
			}				
		});
	});
	$("div#wcmp_msg_tab_1").on("click", "._wcmp_vendor_message_read", function(e){			
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this);	
		var element_parent = $(this).parent();
		var tab_element = $(this).parent().parent().parent().parent().parent().parent().parent();		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
				
		var data = {
			action : 'wcmp_vendor_announcements_operation',
			actionmode : 'mark_read',
			msg_id : msg_id			
		}
		$.post(woocommerce_params.ajax_url, data, function(res) {			
			if(	res != 0 ) {
				element_to_be_deleted1.hide();
				element_parent.prepend('<li class="_wcmp_vendor_message_unread"><a href="#">'+res+'</a></li>');
				$("#wcmp_msg_tab_to_be_refrash").val('_read');
				$("#wcmp_msg_tab_to_be_refrash2").val('_unread');
				lodder.hide();				
			}				
		});
	});
	
	$("div#wcmp_msg_tab_1").on("click", "._wcmp_vendor_message_unread",  function(e){
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this);	
		var element_parent = $(this).parent();
		var tab_element = $(this).parent().parent().parent().parent().parent().parent().parent();		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var data = {
			action : 'wcmp_vendor_announcements_operation',
			actionmode : 'mark_unread',
			msg_id : msg_id			
		}
		$.post(woocommerce_params.ajax_url, data, function(res) {				
			if(	res != 0 ) {
				element_to_be_deleted1.hide();
				element_parent.prepend('<li class="_wcmp_vendor_message_read"><a href="#">'+res+'</a></li>');
				$("#wcmp_msg_tab_to_be_refrash").val('_read');
				$("#wcmp_msg_tab_to_be_refrash2").val('_unread');
				lodder.hide();
			}				
		});
	});
	
	$("div#wcmp_msg_tab_2").on("click", "._wcmp_vendor_message_unread",  function(e){
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parent().parent().parent().parent();
		var element_to_be_deleted2 = $(this).parent().parent().parent().parent().next();	
		var element_parent = $(this).parent();

		var tab_element = $(this).parent().parent().parent().parent().parent().parent().parent();		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');
		
		var data = {
			action : 'wcmp_vendor_announcements_operation',
			actionmode : 'mark_unread',
			msg_id : msg_id			
		}
		$.post(woocommerce_params.ajax_url, data, function(res) {				
			if(	res != 0 ) {
				element_to_be_deleted1.remove();
				element_to_be_deleted2.remove();				
				$("#wcmp_msg_tab_to_be_refrash2").val('_unread');
				$("#wcmp_msg_tab_to_be_refrash").val('_all');
				var data2 = {
					action : 'wcmp_announcements_refresh_tab_data',
					tabname : tab_to_refrash					
				}				
				$.post(woocommerce_params.ajax_url, data2, function(res2) {
					$("#"+tab_id+' .msg_container').html(res2);
					jQuery(function($) {							
						$( "#accordion-2" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});						
					});					
					jQuery('#accordion-2').accordion("refresh");					
					lodder.hide();
				});
			}				
		});
	});
	
	$("div#wcmp_msg_tab_3").on("click", "._wcmp_vendor_message_read", function(e){			
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parent().parent().parent().parent();
		var element_to_be_deleted2 = $(this).parent().parent().parent().parent().next();	
		var element_parent = $(this).parent();
		
		var tab_element = $(this).parent().parent().parent().parent().parent().parent().parent();		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');	
				
		var data = {
			action : 'wcmp_vendor_announcements_operation',
			actionmode : 'mark_read',
			msg_id : msg_id			
		}
		$.post(woocommerce_params.ajax_url, data, function(res) {			
			if(	res != 0 ) {
				element_to_be_deleted1.remove();
				element_to_be_deleted2.remove();				
				$("#wcmp_msg_tab_to_be_refrash").val('_read');
				$("#wcmp_msg_tab_to_be_refrash2").val('_all');
				
				var data2 = {
					action : 'wcmp_announcements_refresh_tab_data',
					tabname : tab_to_refrash					
				}				
				$.post(woocommerce_params.ajax_url, data2, function(res2) {
					$("#"+tab_id+' .msg_container').html(res2);
					jQuery(function($) {							
						$( "#accordion-3" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});						
					});					
					jQuery('#accordion-3').accordion("refresh");					
					lodder.hide();
				});
				
			}				
		});
	});
	
	$("div#wcmp_msg_tab_4").on("click", "._wcmp_vendor_message_restore", function(e){			
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parent().parent().parent().parent();
		var element_to_be_deleted2 = $(this).parent().parent().parent().parent().next();	
		var element_parent = $(this).parent();
		
		var tab_element = $(this).parent().parent().parent().parent().parent().parent().parent();		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');		
		var data = {
			action : 'wcmp_vendor_announcements_operation',
			actionmode : 'mark_restore',
			msg_id : msg_id			
		}
		$.post(woocommerce_params.ajax_url, data, function(res) {			
			if(	res != 0 ) {
				element_to_be_deleted1.remove();
				element_to_be_deleted2.remove();				
				$("#wcmp_msg_tab_to_be_refrash").val('_read');
				$("#wcmp_msg_tab_to_be_refrash2").val('_unread');
				$("#wcmp_msg_tab_to_be_refrash3").val('_all');
				var data2 = {
					action : 'wcmp_announcements_refresh_tab_data',
					tabname : tab_to_refrash					
				}				
				$.post(woocommerce_params.ajax_url, data2, function(res2) {
					$("#"+tab_id+' .msg_container').html(res2);
					jQuery(function($) {							
						$( "#accordion-4" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});						
					});					
					jQuery('#accordion-4').accordion("refresh");					
					lodder.hide();
				});
			}				
		});
	});	
	
	$("#tabs-1 ul.wcmp_msg_tab_nav li").click(function(){
		var clicked_tab = $(this).attr('data-element');
		var clicked_tab_anchor = $(this).find('a');
		var target_tab_id = clicked_tab_anchor.attr('href');		
		var tab_to_be_refrash = $("#wcmp_msg_tab_to_be_refrash").val();
		var tab_to_be_refrash2 = $("#wcmp_msg_tab_to_be_refrash2").val();
		var tab_to_be_refrash3 = $("#wcmp_msg_tab_to_be_refrash3").val();
		if( tab_to_be_refrash == clicked_tab || tab_to_be_refrash2 == clicked_tab || tab_to_be_refrash3 == clicked_tab  ) {
			$(target_tab_id+' div.ajax_loader_class_msg').show();
			var data = {
				action : 'wcmp_announcements_refresh_tab_data',
				tabname : clicked_tab					
			}
			$.post(woocommerce_params.ajax_url, data, function(res) {
				//console.log(res);	
				$(target_tab_id+' .msg_container').html(res);
					jQuery(function($) {
						$( "#accordion-1" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						$( "#accordion-2" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});	
						$( "#accordion-3" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						$( "#accordion-4" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
					});
					jQuery('#accordion-1').accordion("refresh");
					jQuery('#accordion-2').accordion("refresh");
					jQuery('#accordion-3').accordion("refresh");
					jQuery('#accordion-4').accordion("refresh");
				$(target_tab_id+' div.ajax_loader_class_msg').hide();
				if( tab_to_be_refrash == clicked_tab) {
					$("#wcmp_announcements_tab_to_be_refrash").val('');
				}
				else if(tab_to_be_refrash2 == clicked_tab) {
					$("#wcmp_announcements_tab_to_be_refrash2").val('');
				}
				else if(tab_to_be_refrash3 == clicked_tab) {
					$("#wcmp_announcements_tab_to_be_refrash3").val('');
				}
			});			
		}				
	});
	
	$("body").on("click", ".wcmp_black_btn_msg_for_nav", function(e){
		var myparent = $(this).parent().parent();
		var myparent2 = $(this).parent();
		var hidden_msg = myparent.find('.wcmp_hide_message');		
		var first_num_ele = myparent2.find('span.first_nav_num');
		var second_num_ele = myparent2.find('span.second_nav_num');     
		var first_num = parseInt(first_num_ele.html());
		var second_num = parseInt(second_num_ele.html());		
		var counter = 0;
		if(hidden_msg.length <= 12) {
			first_num_ele.html(second_num);
			$(this).hide();
		}
		else {
			first_num_ele.html( parseInt(first_num)+6);
		}
		hidden_msg.each(function() {
			$(this).removeClass('wcmp_hide_message');				
			counter =	Number(counter) + 1;
			if (Number(counter) == 12) {
        return false;
      }				
		});		
	});
	
});
