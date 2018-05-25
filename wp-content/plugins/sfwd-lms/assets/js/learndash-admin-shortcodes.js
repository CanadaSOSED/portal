var learndash_shortcodes = jQuery.extend(learndash_shortcodes || {}, {	
	tinymce_editor: null,
	
	show_popup_html: function() {
		var timymce_url = learndash_shortcodes.get_tinymce_url();

        var shortcodes_loaded = jQuery("#learndash_shortcodes_holder").length;
        if (shortcodes_loaded) {
            tb_show( 'LearnDash Shortcodes', timymce_url );

        } else {

            jQuery("body").append('<div id="learndash_shortcodes_holder" style="display: none;"><div id="learndash_shortcodes"></div></div>');

			var post_data = {
				'action': 'learndash_generate_shortcodes_content',
				'post_type': typenow
			};
			jQuery.ajax({
				type: "GET",
				url: ajaxurl,
				dataType: "html",
				cache: false,
				data: post_data,
				error: function(jqXHR, textStatus, errorThrown ) {
					//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
					//console.log('error [%o]', textStatus);
				},
				success: function(reply_data) {
					if ( typeof reply_data !== 'undefined' ) {
						jQuery('#learndash_shortcodes').html( reply_data );
					}
					
					learndash_shortcodes.popup_init();
					tb_show( 'LearnDash Shortcodes', timymce_url );
				}
			});
        }
	},
	get_tinymce_url: function() {
		
        var width = jQuery(window).width();
		var height = jQuery(window).height();

		var W = (950 < width) ? 950 : width;
		var H = height;

        W = W - 80;
        H = H - 84;

		var request_tinymce_url = '#TB_inline?width=' + W + '&height=' + H + '&inlineId=learndash_shortcodes';
		return request_tinymce_url;
	},
	tinymce_callback: function( editor ) {
		learndash_shortcodes.tinymce_editor = editor;
		learndash_shortcodes.show_popup_html();	
	},
	qt_callback: function() {
		learndash_shortcodes.tinymce_editor = null;
		learndash_shortcodes.show_popup_html();
	},
	popup_init: function() {
		jQuery('#learndash_shortcodes_tabs a').click(function(e) {
			e.preventDefault();
			learndash_shortcodes.tabs_switch( jQuery( this ) );
		});
		learndash_shortcodes.tabs_switch( jQuery('#learndash_shortcodes a').first() );
		
		jQuery('form.learndash_shortcodes_form').submit(function(e) {
			e.preventDefault();
			tb_remove();
			learndash_shortcodes.popup_submit(this);
		});
	},
	tabs_switch: function (obj) {
		jQuery('#learndash_shortcodes_sections .hidable').hide();
		jQuery('#learndash_shortcodes_sections #tabs-'+ obj.attr('data-nav')).show();
		jQuery('#learndash_shortcodes_tabs li').removeClass('current');
		obj.parent().addClass('current');
	},
	get_selected_text: function () {
		var txtarea = document.getElementById("content");
		var start = txtarea.selectionStart;
		var finish = txtarea.selectionEnd;
		return txtarea.value.substring(start, finish);
	},
	popup_submit: function ( form ) {
		var shortcode_slug = jQuery(form).attr('shortcode_slug');

		var shortcode_type = jQuery(form).attr('shortcode_type');
		if ( typeof shortcode_type === 'undefined')
			shortcode_type = 1;
		
		var content = '[' + shortcode_slug;
		var elements = form.elements;

		var message = '';

		if ( elements.length > 0 ) {
			var field_count = 0;
			while( field_count < elements.length ) {
				var field = elements[field_count];

				switch( field.type ) {
					case 'textarea':
						if ( shortcode_type == 2 ) {
							message = field.value;
						} else {
							content += ' '+ field.name +'="'+ field.value.replace(/"/g, '\\"') +'"';
						}
						break;
					
					case 'checkbox':
						if ( field.checked ) {
							if ( ( typeof field.value !== 'undefined') && ( field.value != '' ) && ( field.value != '0' ) ) {
								content += ' '+field.name + '="' + field.value.replace(/"/g, '\\"') +'"';
							}
						} 
						break;

					case 'submit':
						break;
						
					case 'text':
					default:
						if ( ( typeof field.value !== 'undefined') && ( field.value != '' ) ) {
							
							//var pattern = /^([a-zA-Z0-9 _-]+/gi;
							var value_replaced = field.value.replace(/\W/g, '');
							content += ' ' + field.name + '="' + field.value.replace(/"/g, '\\"') + '"';
						}
						break;
				}

				field_count += 1;
			}
		}
		content += ']';
		
		if (( shortcode_type == 2 ) && ( message != '' )) {
			content += message;
			content += '[/'+ shortcode_slug +']';
		}
		
		//console.log('content: %o', content );
		if ( learndash_shortcodes.tinymce_editor !== null) {
			learndash_shortcodes.tinymce_editor.setContent( content );
		} else {
			QTags.insertContent( content );
		}
	}
});


