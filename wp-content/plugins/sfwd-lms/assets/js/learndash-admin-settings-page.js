jQuery(document).ready(function() {
	/*
	jQuery('.learndash-settings-page-wrap a.learndash-description-link').toggle( function( e ) {
		//jQuery(e.currentTarget).parent().find('span.learndash-description').slideDown();
		jQuery(e.currentTarget).parent().find('span.learndash-description').show();
	}, function( e ) {
		//jQuery(e.currentTarget).parent().find('span.learndash-description').slideUp();
		jQuery(e.currentTarget).parent().find('span.learndash-description').hide();
	});
	*/

	if ( jQuery( '.sfwd_options .sfwd_input_type_select-edit-delete' ).length ) {
		jQuery( '.sfwd_options .sfwd_input_type_select-edit-delete' ).each( function( idx, item ) {
			var item_spinner = jQuery(item).find('.spinner');
			item_spinner.css( 'float', 'none' );

			jQuery( item ).find( 'select' ).change( function( e ) {
				
				var select_val = jQuery( item ).find( 'select' ).val();
				console.log( 'select_val[%o]', select_val );
				
				// Hide any previous update message.
				jQuery( item ).find( '.message' ).hide();

				if ( select_val.length ) {
					var select_text = jQuery( item ).find( 'select option:selected' ).text();
					jQuery( item ).find( 'input[type="text"]' ).val( select_text );
					jQuery( item ).find( 'input[type="text"]' ).attr( 'disabled', false );
					jQuery( item ).find( 'input[type="button"]' ).attr( 'disabled', false );
				} else {
					jQuery( item ).find( 'input[type="text"]' ).val( '' );
					jQuery( item ).find( 'input[type="button"]' ).attr( 'disabled', true );
					jQuery( item ).find( 'input[type="text"]' ).attr( 'disabled', true );
				}
			});

			jQuery( item ).find( 'input[type="button"]' ).click( function ( e ) {
				var field_action = jQuery( e.currentTarget ).data( 'action' );
				var field_value = jQuery( item ).find( 'select' ).val();
				var updated_text = jQuery( item ).find( 'input[type="text"]' ).val();

				var post_data = jQuery(item).find('.ajax_data').data( 'ajax' );
				if ( typeof post_data !== 'undefined' ) {
					post_data['field_action'] = field_action;
					post_data['field_value'] = field_value;
					post_data['field_text'] = updated_text;

					item_spinner.css('visibility', 'visible');
				
					jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						dataType: 'json',
						cache: false,
						data: post_data,
						error: function (jqXHR, textStatus, errorThrown) {
						},
						success: function ( reply_data ) {
							item_spinner.css('visibility', 'hidden');

							if ( ( typeof reply_data.status !== 'undefined' ) && ( reply_data.status === true ) ) {
								if (field_action == 'update') {
									jQuery(item).find( 'select option[value="'+ field_value +'"]' ).text( updated_text );
								} else if (field_action == 'delete') {
									jQuery(item).find('select option[value="' + field_value + '"]').remove();
								}

								jQuery( item ).find('select').val( '' );
								jQuery( item ).find('input[type="text"]').val( '' );
							}

							if ( typeof reply_data.message !== 'undefined' ) {
								jQuery( item ).find( '.message' ).html( reply_data.message );
								jQuery( item ).find( '.message' ).show().fadeOut(3000);
							}
						}
					});
				}
			});
		});
	}
});
