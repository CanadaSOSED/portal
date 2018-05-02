<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Section_General_Per_Page' ) ) ) {
	class LearnDash_Settings_Section_General_Per_Page extends LearnDash_Settings_Section {

		function __construct() {
			$this->settings_page_id					=	'learndash_lms_settings';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_per_page';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_per_page';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'settings_per_page';
		
			// Section label/header
			$this->settings_section_label			=	esc_html__( 'Per Page Default Settings', 'learndash' );
		
			//$this->settings_section_description		=	sprintf( wp_kses_post( _x( 'These settings control the per page output of %s progress and %s on the WordPress User Profile.', 'placeholder: Course, Quizzes', 'learndash' ) ), LearnDash_Custom_Label::get_label( 'course'), LearnDash_Custom_Label::get_label( 'quizzes') );
		
			parent::__construct(); 
		}
				
		function load_settings_values() {
			parent::load_settings_values();
		
			if ( !isset( $this->setting_option_values['per_page'] ) ) { 
				$this->setting_option_values['per_page'] = LEARNDASH_LMS_DEFAULT_WIDGET_PER_PAGE;
			}
			
			if ( !isset( $this->setting_option_values['progress_num'] ) ) { 
				$this->setting_option_values['progress_num'] = $this->setting_option_values['per_page'];
			}

			if ( !isset( $this->setting_option_values['quiz_num'] ) ) { 
				$this->setting_option_values['quiz_num'] = $this->setting_option_values['per_page'];
			}
			
		}		
				
		function validate_section_field( $val, $key, $args = array() ) {
			// Get the digits only
			$val = preg_replace("/[^0-9]/", '', $val);
			
			if ( $val == '' ) {
				switch( $key  ) {
					case 'per_page':
						$val = LEARNDASH_LMS_DEFAULT_WIDGET_PER_PAGE;
						break;
					
					case 'progress_num':
						if ( ( isset( $args['post_fields']['per_page'] ) ) && ( $args['post_fields']['per_page'] != '' ) ) {
							$val = $args['post_fields']['per_page'];
						} else {
							$val = $this->setting_option_values['per_page'];
						}
						break;

					case 'quiz_num':
						if ( ( isset( $args['post_fields']['per_page'] ) ) && ( $args['post_fields']['per_page'] != '' ) ) {
							$val = $args['post_fields']['per_page'];
						} else {
							$val = $this->setting_option_values['per_page'];
						}
						break;
				}
			}
			
			return intval( $val );
		}
		
				
		function load_settings_fields() {

			$this->setting_option_fields = array(
				'per_page' => array(
					'name'  		=> 	'per_page',
					'type'  		=> 	'number',
					'label' 		=> 	esc_html__( 'Default per page', 'learndash' ),
					//'desc_before'	=>
					'help_text'  	=>  sprintf( esc_html_x( 'Default per page controls all shortcodes and widget. Default is %d. Set to zero for no pagination.', 'placeholder: Default per page', 'learndash' ), LEARNDASH_LMS_DEFAULT_WIDGET_PER_PAGE ),
					'value' 		=> 	$this->setting_option_values['per_page'],
					'attrs'			=>	array(
											'step'	=>	1,
											'min'	=>	0
					),
					'validate_callback'	=>	array( $this, 'validate_section_field' )
				),
				'progress_num' => array(
					'name'  		=> 	'progress_num',
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( esc_html_x( '%s progress per page', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'desc_before'	=>	sprintf( wp_kses_post( _x( 'These settings control the per page output of %s progress and %s on the WordPress User Profile.', 'placeholder: Course, Quizzes', 'learndash' ) ), LearnDash_Custom_Label::get_label( 'course'), LearnDash_Custom_Label::get_label( 'quizzes') ),
					'help_text'  	=> 	sprintf( esc_html_x( '%1$s per page. Default is %2$d. Set to zero for no pagination.', 'placeholders: courses, default per page', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ), $this->setting_option_values['per_page'] ),
					'value' 		=> 	$this->setting_option_values['progress_num'],
					'attrs'			=>	array(
											'step'	=>	1,
											'min'	=>	0
					),
					'validate_callback'	=>	array( $this, 'validate_section_field' )
				),
				'quiz_num' => array(
					'name'  		=> 	'quiz_num', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( esc_html_x( '%s per page', 'placeholder: Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
					'help_text'  	=> 	sprintf( esc_html_x( '%1$s per page. Default is %2$d. Set to zero for no pagination.', 'placeholders: quizzes, default per page', 'learndash' ), LearnDash_Custom_Label::get_label( 'quizzes' ), $this->setting_option_values['per_page'] ),
					'value' 		=> 	$this->setting_option_values['quiz_num'],
					'attrs'			=>	array(
											'step'	=>	1,
											'min'	=>	0
					),
					'validate_callback'	=>	array( $this, 'validate_section_field' )
				),
			);
		
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			
			parent::load_settings_fields();
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_General_Per_Page::add_section_instance();
} );
