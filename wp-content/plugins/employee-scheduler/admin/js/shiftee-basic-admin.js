(function( $ ) {
	'use strict';

	$(document).ready(function() {

		if ( $.isFunction($.fn.datepicker) ) {
            $('.shiftee-date-picker').datepicker({
                dateFormat: datetimepicker_options.date_format,
                firstDay: datetimepicker_options.first_day_of_week,
            });
        }

        $("#ui-datepicker-div").addClass("cmb2-element"); // make the datepicker work with CMB2 stylesheet

        if(jQuery("#repeat").is(':checked'))
			jQuery("#repeatfields").show();  // checked
		else
			jQuery("#repeatfields").hide();  // unchecked
		jQuery('#repeat').onchange = function() {
			jQuery('#repeatfields').style.display = this.checked ? 'block' : 'none';
		};

		$( "#shiftee-upgrade-shift-meta" ).submit(function( event ) {

			event.preventDefault();

			$( '#shiftee-upgrade-shift-meta' ).hide();

			upgrade_shift_meta();

		});

	});

	function upgrade_shift_meta() {
		var url = shiftee_update_ajax.ajaxurl;
		var nonce = $('#shiftee_upgrade_shift_meta_nonce').val();
		var data = {
			'action': 'upgrade_shift_meta',
			'nonce': nonce,
		};

		$.post(url, data, function (response) {
			console.log( 'getting response');
			console.log( response );
			if( response ) {
				if( 'error' == response ) {
					$("#upgrade_shift_meta_results").html('We encountered an error. Please contact Shiftee support');
				} else if( 'finished' == response ) {
					var success = 'All done!  Enjoy the new features!';
					$("#upgrade_shift_meta_results").html(success);
				} else {
					console.log('continue');
					var total = 'Updating ' + response + ' of ' + $('#shift-count').val() + ' shifts and expenses';
					$("#upgrade_shift_meta_results").html(total);
					upgrade_shift_meta();
				}
			}

		});
	}


})( jQuery );
