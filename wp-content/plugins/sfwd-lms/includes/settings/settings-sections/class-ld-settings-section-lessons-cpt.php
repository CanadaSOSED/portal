<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Lessons_CPT' ) ) ) {
	class LearnDash_Settings_Lessons_CPT extends LearnDash_Settings_Section {

		function __construct() {
			// What screen ID are we showing on
			$this->settings_screen_id				=	'sfwd-lessons_page_lessons-options';
			
			// The page ID (different than the screen ID)
			$this->settings_page_id					=	'lessons-options';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_lessons_cpt';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_lessons_cpt';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'cpt_options';
		
			// Section label/header
			$this->settings_section_label			=	sprintf( esc_html_x( '%s Custom Post Type Options', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson') );
		
			// Used to show the section description above the fields. Can be empty
			$this->settings_section_description		=	sprintf( wp_kses_post( _x( '<p>Control the LearnDash %s Custom Post Type Options.</p>', 'placeholder: Lessons', 'learndash' ) ), LearnDash_Custom_Label::get_label( 'lessons') );

			parent::__construct(); 
		}
		
		function load_settings_fields() {
			$this->setting_option_fields = array(
				'exclude_from_search' => array(
					'name'  		=> 	'exclude_from_search', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	esc_html__( 'Exclude From Search', 'learndash' ),
					'help_text'		=>	esc_html__( 'Exclude From Search', 'learndash' ),
					'value' 		=> 	isset( $this->setting_option_values['exclude_from_search'] ) ? $this->setting_option_values['exclude_from_search'] : '',
					'options'		=>	array(
											'yes'	=>	esc_html__('Exclude', 'learndash'),
										)
				),
			);
		
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}
	}
}

add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Lessons_CPT::add_section_instance();
} );
