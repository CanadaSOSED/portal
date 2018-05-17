jQuery(document).ready(function(){
	jQuery('table#learndash-data-upgrades button').click(function(e) {
		e.preventDefault();

		var parent_tr 	= jQuery(this).parents('tr')
		var data_nonce 	= jQuery(this).attr('data-nonce');
		var data_slug 	= jQuery(this).attr('data-slug');

		// Close all other progress meters
		jQuery('table#learndash-data-upgrades .learndash-data-upgrades-status').hide();
				
		// disable all other buttons
		jQuery('table#learndash-data-upgrades button.learndash-data-upgrades-button').prop('disabled', true);
		
		
		var post_data = {
			'action': 'learndash-data-upgrades',
			'data': {
				'init': 1,
				'nonce': data_nonce,
				'slug': data_slug,
			}
		}
		//console.log('post_data[%o]', post_data);
		
		learndash_data_upgrades_do_ajax( post_data, parent_tr );
		
	});
});

function learndash_data_upgrades_do_ajax( post_data, container ) {
	if ( ( typeof post_data === 'undefined' ) || ( post_data == '' ) ) {
		active_post_data = {};
		return false;
	}
	
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		dataType: "json",
		cache: false,
		data: post_data,
		error: function(jqXHR, textStatus, errorThrown ) {
		},
		success: function(reply_data) {
			if ( typeof reply_data !== 'undefined' ) {

				if ( typeof reply_data['data'] !== 'undefined' ) {

					// Update the progress meter
					if (jQuery('.learndash-data-upgrades-status', container).length) {
					
						jQuery('.learndash-data-upgrades-status', container).show();

						if ( typeof reply_data['data']['progress_percent'] !== 'undefined' ) {
							jQuery('.learndash-data-upgrades-status .progress-meter-image', container).css('width', reply_data['data']['progress_percent']+'%');
						}

						if ( typeof reply_data['data']['progress_label'] !== 'undefined' ) {
							jQuery('.learndash-data-upgrades-status .progress-label', container).html(reply_data['data']['progress_label']);
						}
					}
					if ( ( typeof reply_data['data']['last_run_info'] !== 'undefined' ) && ( reply_data['data']['last_run_info'] != '' ) ) {
						jQuery('p.description', container).html(reply_data['data']['last_run_info']);
					}
					

					var total_count = 0;
					if ( typeof reply_data['data']['total_count'] !== 'undefined' )
						total_count = parseInt(reply_data['data']['total_count']);
					
					var result_count = 0;
					if ( typeof reply_data['data']['result_count'] !== 'undefined' ) 
						result_count = parseInt(reply_data['data']['result_count']);
					
					if ( result_count < total_count ) {
						post_data['data'] = reply_data['data'];
						learndash_data_upgrades_do_ajax( post_data, container );
					} else {
						jQuery('table#learndash-data-upgrades button.learndash-data-upgrades-button').prop('disabled', false);
					}
				}
			}
		}
	});
}
