<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Courses_Builder_Single' ) ) ) {
	class LearnDash_Settings_Courses_Builder_Single extends LearnDash_Settings_Section {
		
		private $cb;

		function __construct() {
			// What screen ID are we showing on
			$this->settings_screen_id				=	'sfwd-courses_page_courses-builder';
			
			// The page ID (different than the screen ID)
			$this->settings_page_id					=	'courses-builder';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_courses_builder';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_courses_builder';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'courses_builder';
		
			// Section label/header
			$this->settings_section_label			=	sprintf( esc_html_x( '%s Builder', 'Course Builder', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );

			// Add a cstom callback for our metabox as we don't display a list of settings fields.
			$this->settings_fields_callback			=	array( $this, 'display_meta_box_inside' );
			
			parent::__construct(); 
		}
		
		function display_meta_box_inside() {
			if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
				$course_id = intval( $_GET['course_id'] );
				$course_post = get_post( $course_id );
				if ( ( is_a( $course_post, 'WP_Post' ) ) && ( $course_post->post_type == 'sfwd-courses' ) ) {
					$this->cb = new Learndash_Admin_Metabox_Course_Builder();
					$this->cb->course_builder_box( $course_post );
				}
			} 
		}

		// End of functions
	}
}

add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Courses_Builder_Single::add_section_instance();
} );
