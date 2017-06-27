(function( $ ) {
	'use strict';

	$(document).ready(function() {

        if ( $.isFunction($.fn.datepicker) ) {
            $('.shiftee-date-picker').datepicker({
                dateFormat: datetimepicker_options.date_format,
            });

            $('.shiftee-time-picker').datetimepicker({
                timeOnly: true,
                dateFormat: datetimepicker_options.date_format,
                timeFormat: datetimepicker_options.time_format,
                stepMinute: 5,
            });

            $('.shiftee-datetime-picker').datetimepicker({
                dateFormat: datetimepicker_options.date_format,
                timeFormat: datetimepicker_options.time_format,
                firstDay: datetimepicker_options.first_day_of_week,
                stepMinute: 5,
            });
        }


		// Add a new repeating section
		jQuery('.shiftee-repeat').click(function(e){
            var repeatingTemplate = jQuery('#date-time-template').html();
			e.preventDefault();
			var repeating = jQuery(repeatingTemplate);
			var lastRepeatingGroup = jQuery('.repeating-unavailability').last();
			var idx = lastRepeatingGroup.index();
			var attrs = ['for', 'id', 'name'];
			var tags = repeating.find('input, label, select');
			tags.each(function() {
				var section = jQuery(this);
				jQuery.each(attrs, function(i, attr) {
					var attr_val = section.attr(attr);
					if (attr_val) {
						section.attr(attr, attr_val.replace(/unavailable\[\d+\]\[/, 'unavailable\['+(idx + 1)+'\]\['))
					}
				})
			});


			jQuery('#date-time-template').after(repeating);
			repeating.find('.shiftee-time-picker').datetimepicker({
                timeOnly: true,
                timeFormat: "h:mm tt"
			});
		});

		var repeatingTemplate = jQuery('#availability-template').html();

		// Add a new repeating section
		jQuery('.shiftee-repeat-availability').click(function(e){
			e.preventDefault();
			var repeating = jQuery(repeatingTemplate);
			var lastRepeatingGroup = jQuery('.repeating-availability').last();
			var idx = lastRepeatingGroup.index();
			var attrs = ['for', 'id', 'name'];
			var tags = repeating.find('input, label, select');
			tags.each(function() {
				var section = jQuery(this);
				jQuery.each(attrs, function(i, attr) {
					var attr_val = section.attr(attr);
					if (attr_val) {
						section.attr(attr, attr_val.replace(/unavailable\[\d+\]\[/, 'unavailable\['+(idx + 1)+'\]\['))
					}
				})
			});


			jQuery('#availability-template').after(repeating);
			repeating.find('.shiftee-time-picker-min').datetimepicker({
				datepicker:false,
				format:'H:i',
				step: 1,
			});
            repeating.find('.shiftee-datetime-picker').datetimepicker({
                timeFormat: "h:mm tt"
            });
		});

		jQuery('body').on('click', 'a.remove-availability', function (e){
			e.preventDefault();
			jQuery(this).closest('.repeating-unavailability').remove();
            jQuery(this).closest('.repeating-availability').remove();
		});

		$('.shiftee-show-more-unassigned').on( 'click', function (e){
			e.preventDefault();
			$(this).closest('.shiftee-more-unassigned').show();
            $(this).hide();
		});


		$('#shiftee-od-employer-work-request').on( 'submit', function (e){
			$('#shiftee-loading').show();
		});
	});

})( jQuery );
