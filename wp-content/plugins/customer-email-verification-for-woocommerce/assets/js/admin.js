( function( $, data, wp, ajaxurl ) {
		
	var $cev_settings_form = $("#cev_settings_form");
	var $cev_frontend_messages_form = $("#cev_frontend_messages_form");	
			
	var cev_settings_init = {
		
		init: function() {									
			$cev_settings_form.on( 'click', '.cev_settings_save', this.save_wc_cev_settings_form );
			$cev_frontend_messages_form.on( 'click', '.cev_frontend_messages_save', this.save_wc_cev_frontend_messages_form );			
		},

		save_wc_cev_settings_form: function( event ) {
			
			event.preventDefault();
			
			$cev_settings_form.find(".spinner").addClass("active");
			var ajax_data = $cev_settings_form.serialize();
			
			$.post( ajaxurl, ajax_data, function(response) {
				$cev_settings_form.find(".spinner").removeClass("active");
				$cev_settings_form.find('.success_msg').show();
			});
			
		}, 
		save_wc_cev_frontend_messages_form: function( event ) {
			
			event.preventDefault();
			
			$cev_frontend_messages_form.find(".spinner").addClass("active");
			var ajax_data = $cev_frontend_messages_form.serialize();
			
			$.post( ajaxurl, ajax_data, function(response) {
				$cev_frontend_messages_form.find(".spinner").removeClass("active");
				$cev_frontend_messages_form.find('.success_msg').show();
			});
			
		}
	};
	
	$(window).load(function(e) {
        cev_settings_init.init();
    });	
	
})( jQuery, customer_email_verification_script, wp, ajaxurl );


jQuery( document ).ready(function() {
	jQuery('.cev_color_field input').wpColorPicker();	
});