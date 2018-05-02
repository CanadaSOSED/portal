// Course Registered
jQuery(document).ready(function() {
	if ( jQuery( '#ld_course_info_mycourses_list .ld-course-registered-pager-container a' ).length ) {
		jQuery( '#ld_course_info_mycourses_list' ).on( 'click', '.ld-course-registered-pager-container a', ld_course_registered_pager_handler );
		
		function ld_course_registered_pager_handler( e ) {
			e.preventDefault();
			
			var paged = jQuery( e.currentTarget ).data('paged');
			
			var parent_div = jQuery( e.currentTarget ).parents('.ld_course_info' );
			if ( typeof parent_div === 'undefined')
				return;

			var shortcode_atts = jQuery( parent_div ).data( 'shortcode-atts' );
			if ( typeof parent_div === 'undefined')
				return;
			
			var post_data = {
				'action': 'ld_course_registered_pager',
				'paged': paged,
				'shortcode_atts': shortcode_atts
			};
			
			jQuery.ajax({
				type: "POST",
				url: sfwd_data.ajaxurl,
				dataType: "json",
				cache: false,
				data: post_data,
				error: function(jqXHR, textStatus, errorThrown ) {
					//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
				},
				success: function( reply_data ) {
					if ( typeof reply_data !== 'undefined') {
						if ( typeof reply_data['content'] !== 'undefined' ) {
							jQuery( '#ld_course_info_mycourses_list .ld-courseregistered-content-container' ).html( reply_data['content'] );
						}
						
						if ( typeof reply_data['pager'] !== 'undefined' ) {
							jQuery( '#ld_course_info_mycourses_list .ld-course-registered-pager-container' ).html( reply_data['pager'] );
						}
					}
				}
			});
		}	
	}	
});

// Course Progress
jQuery(document).ready(function() {
	if ( jQuery( '#course_progress_details .ld-course-progress-pager-container a' ).length ) {
		jQuery( '#course_progress_details' ).on( 'click', '.ld-course-progress-pager-container a', ld_course_content_pager_handler );
		
		function ld_course_content_pager_handler( e ) {
			e.preventDefault();
			
			var paged = jQuery( e.currentTarget ).data('paged');
			
			var parent_div = jQuery( e.currentTarget ).parents('.ld_course_info' );
			if ( typeof parent_div === 'undefined')
				return;

			var shortcode_atts = jQuery( parent_div ).data( 'shortcode-atts' );
			if ( typeof parent_div === 'undefined')
				return;
			
			var post_data = {
				'action': 'ld_course_progress_pager',
				'paged': paged,
				'shortcode_atts': shortcode_atts
			};
			
			jQuery.ajax({
				type: "POST",
				url: sfwd_data.ajaxurl,
				dataType: "json",
				cache: false,
				data: post_data,
				error: function(jqXHR, textStatus, errorThrown ) {
					//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
				},
				success: function( reply_data ) {
					if ( typeof reply_data !== 'undefined') {
						if ( typeof reply_data['content'] !== 'undefined' ) {
							jQuery('#course_progress_details .ld-course-progress-content-container').html( reply_data['content'] );
						}
						
						if ( typeof reply_data['pager'] !== 'undefined' ) {
							jQuery('#course_progress_details .ld-course-progress-pager-container').html( reply_data['pager'] );
						}
					}
				}
			});
		}	
	}
	
});

// Quiz Progress
jQuery(document).ready(function() {
	
	if ( jQuery( '#quiz_progress_details .ld-quiz-progress-pager-container a' ).length ) {
		jQuery( '#quiz_progress_details' ).on( 'click', '.ld-quiz-progress-pager-container a', ld_quiz_content_pager_handler );
		
		function ld_quiz_content_pager_handler( e ) {
			e.preventDefault();
			
			var paged = jQuery( e.currentTarget ).data('paged');
			
			var parent_div = jQuery( e.currentTarget ).parents('.ld_course_info' );
			if ( typeof parent_div === 'undefined')
				return;

			var shortcode_atts = jQuery( parent_div ).data( 'shortcode-atts' );
			if ( typeof parent_div === 'undefined')
				return;
			
			var post_data = {
				'action': 'ld_quiz_progress_pager',
				'paged': paged,
				'shortcode_atts': shortcode_atts
			};
			
			jQuery.ajax({
				type: "POST",
				url: sfwd_data.ajaxurl,
				dataType: "json",
				cache: false,
				data: post_data,
				error: function(jqXHR, textStatus, errorThrown ) {
					//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
				},
				success: function( reply_data ) {
					if ( typeof reply_data !== 'undefined') {
						if ( typeof reply_data['content'] !== 'undefined' ) {
							jQuery('#quiz_progress_details .ld-quiz-progress-content-container').html( reply_data['content'] );
						}
						
						if ( typeof reply_data['pager'] !== 'undefined' ) {
							jQuery('#quiz_progress_details .ld-quiz-progress-pager-container').html( reply_data['pager'] );
						}
					}
				}
			});
		}	
	}
	
});

// Course List Shortcode
jQuery(document).ready(function() {
	
	if ( jQuery( '.ld-course-list-content .learndash-pager-course_list a' ).length ) {
		jQuery( '.ld-course-list-content' ).on( 'click', '.learndash-pager-course_list a', ld_course_list_content_pager_handler );
		
		function ld_course_list_content_pager_handler( e ) {
			e.preventDefault();
			
			var parent_div = jQuery( e.currentTarget ).parents('.ld-course-list-content' );
			if ( typeof parent_div === 'undefined')
				return;

			var shortcode_atts = jQuery( parent_div ).data( 'shortcode-atts' );
			if ( typeof parent_div === 'undefined')
				return;
			
			var paged = jQuery( e.currentTarget ).data('paged');
			
			var post_data = {
				'action': 'ld_course_list_shortcode_pager',
				'paged': paged,
				'shortcode_atts': shortcode_atts
			};
			
			jQuery.ajax({
				type: "POST",
				url: sfwd_data.ajaxurl,
				dataType: "json",
				cache: false,
				data: post_data,
				error: function(jqXHR, textStatus, errorThrown ) {
					//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
				},
				success: function( reply_data ) {
					if ( typeof reply_data !== 'undefined') {
						if ( typeof reply_data['content'] !== 'undefined' ) {
							jQuery( parent_div ).html( reply_data['content'] );
						}
					}
				}
			});
		}	
	}
	
});

// Course Navigation Widget
jQuery(document).ready(function() {
	
	if ( jQuery( '.widget_ldcoursenavigation .learndash-pager-course_navigation_widget a' ).length ) {
		jQuery( '.widget_ldcoursenavigation' ).on( 'click', '.learndash-pager-course_navigation_widget a', ld_course_navigation_widget_pager_handler );
		
		function ld_course_navigation_widget_pager_handler( e ) {
			e.preventDefault();
			
			var parent_div = jQuery( e.currentTarget ).parents('.course_navigation' );
			if ( typeof parent_div === 'undefined')
				return;
			
			var widget_data = jQuery( parent_div ).data( 'widget_instance' );
			if ( typeof widget_data === 'undefined')
				return;
			
			var paged = jQuery( e.currentTarget ).data('paged');
			
			var post_data = {
				'action': 'ld_course_navigation_pager',
				'paged': paged,
				'widget_data': widget_data
			};
			
			jQuery.ajax({
				type: "POST",
				url: sfwd_data.ajaxurl,
				dataType: "json",
				cache: false,
				data: post_data,
				error: function(jqXHR, textStatus, errorThrown ) {
					//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
				},
				success: function( reply_data ) {
					if ( typeof reply_data !== 'undefined') {
						//console.log('reply_data[%o]', reply_data);
						if ( ( typeof reply_data['content'] !== 'undefined' ) && ( reply_data['content'].length ) ) {
							jQuery( parent_div ).html( reply_data['content'] );
						}
					}
				}
			});
		}	
	}
});


// Course Navigation Admin Widget
jQuery(document).ready(function() {
	
	if ( jQuery( '#learndash_course_navigation_admin_meta .course_navigation .learndash-pager a' ).length ) {
		jQuery( '#learndash_course_navigation_admin_meta' ).on( 'click', '.course_navigation .learndash-pager a', ld_course_navigation_widget_pager_handler );
		
		function ld_course_navigation_widget_pager_handler( e ) {
			e.preventDefault();
			
			console.log('course navigation admin clicked');
			
			var parent_div = jQuery( e.currentTarget ).parents('.course_navigation' );
			if ( typeof parent_div === 'undefined')
				return;
			
			var widget_data = jQuery( parent_div ).data( 'widget_instance' );
			if ( typeof widget_data === 'undefined')
				return;
			
			var paged = jQuery( e.currentTarget ).data('paged');
			
			var post_data = {
				'action': 'ld_course_navigation_admin_pager',
				'paged': paged,
				'widget_data': widget_data
			};
			
			jQuery.ajax({
				type: "POST",
				url: sfwd_data.ajaxurl,
				dataType: "json",
				cache: false,
				data: post_data,
				error: function(jqXHR, textStatus, errorThrown ) {
					//console.log('init: error HTTP Status['+jqXHR.status+'] '+errorThrown);
				},
				success: function( reply_data ) {
					if ( typeof reply_data !== 'undefined') {
						//console.log('reply_data[%o]', reply_data);
						if ( ( typeof reply_data['content'] !== 'undefined' ) && ( reply_data['content'].length ) ) {
							jQuery( parent_div ).html( reply_data['content'] );
						}
					}
				}
			});
		}	
	}
});
