<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Courses_Builder' ) ) ) {
	class LearnDash_Settings_Courses_Builder extends LearnDash_Settings_Section {

		function __construct() {
			// What screen ID are we showing on
			$this->settings_screen_id				=	'sfwd-courses_page_courses-options';
			
			// The page ID (different than the screen ID)
			$this->settings_page_id					=	'courses-options';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_courses_builder';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_courses_builder';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'course_builder';
		
			// Section label/header
			$this->settings_section_label			=	sprintf( esc_html_x( '%s Builder', 'Course Builder', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') );
		
			// Used to show the section description above the fields. Can be empty
			//$this->settings_section_description		=	sprintf( wp_kses_post( _x( '<p>Enables the %s Builder interface. This will allow you to manage %s, %s, %s within the %s editor screen. <span style="ld-important">Enabling this option will also enable the <a href="%s">nested permalinks</a> setting.</span></p>', 'placeholder: Course, Lessons, Topics, Quizzes, Course, URL to admin Permalinks', 'learndash' ) ), LearnDash_Custom_Label::get_label( 'course'), LearnDash_Custom_Label::get_label( 'lessons'), LearnDash_Custom_Label::get_label( 'topics'), LearnDash_Custom_Label::get_label( 'quizzes'), LearnDash_Custom_Label::get_label( 'course'), admin_url('options-permalink.php#learndash_settings_permalinks_nested_urls') );

			parent::__construct(); 

			$this->save_settings_fields();

		}
		
		function load_settings_values() {
			parent::load_settings_values();

			// If the settings set as a whole is empty then we set a default 
			if ( empty( $this->setting_option_values ) ) {
				$this->setting_option_values['enabled'] = 'yes';
				$this->setting_option_values['shared_steps'] = '';
			}
			
			if ( !isset( $this->setting_option_values['per_page'] ) )
				$this->setting_option_values['per_page'] = 25;
			else
				$this->setting_option_values['per_page'] = intval( $this->setting_option_values['per_page'] );

			if ( empty( $this->setting_option_values['per_page'] ) )
				$this->setting_option_values['per_page'] = 25;
			
		}
		
		function load_settings_fields() {
			$this->setting_option_fields = array(
				'enabled' => array(
					'name'  		=> 	'enabled', 
					'type'  		=> 	'checkbox',
					'desc_before'	=>	sprintf( esc_html_x( 'Enables the %1$s Builder interface. This will allow you to manage %2$s, %3$s, %4$s within the %5$s editor screen.', 'placeholder: Course, Lessons, Topics, Quizzes, Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course'), LearnDash_Custom_Label::get_label( 'lessons'), LearnDash_Custom_Label::get_label( 'topics'), LearnDash_Custom_Label::get_label( 'quizzes'), LearnDash_Custom_Label::get_label( 'course') ),
					
					'label' 		=> 	sprintf( esc_html_x( '%s Builder Interface', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') ),
					'help_text'		=>	sprintf( esc_html_x( 'Enable %s Builder Interface', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') ),
					'value' 		=> 	isset( $this->setting_option_values['enabled'] ) ? $this->setting_option_values['enabled'] : '',
					'options'		=>	array(
											'yes'	=>	esc_html__('Enabled', 'learndash'),
										)
				),
				
				'per_page' => array(
					'name'  		=> 	'per_page', 
					'type'  		=> 	'number',
					'label' 		=> 	esc_html__( 'Selector Items Per Page', 'learndash' ),
					'help_text'  	=> 	esc_html__( 'Selector items to display per page.', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['per_page'],
					'class'			=>	'small-text',
					'attrs'			=>	array(
											'step'	=>	1,
											'min'	=>	0
					)
				),
				'shared_steps' => array(
					'name'  		=> 	'shared_steps', 
					'type'  		=> 	'checkbox',
					'desc_before'	=>	sprintf( wp_kses_post(  _x( '<p>Enables using %1$s, %2$s, and %3$s across multiple %4$s. <span style="ld-important">%5$s Builder must be enabled. Enabling this option will also enable the <a href="%6$s">nested permalinks</a> setting.</span></p>', 'placeholder: Lessons, Topics, Quizzes, Courses, Course, URL to admin Permalinks', 'learndash' ) ),
											LearnDash_Custom_Label::get_label( 'lessons'), 
											LearnDash_Custom_Label::get_label( 'topics'), 
											LearnDash_Custom_Label::get_label( 'quizzes'), 
											LearnDash_Custom_Label::get_label( 'courses'),
											LearnDash_Custom_Label::get_label( 'course'), admin_url('options-permalink.php#learndash_settings_permalinks_nested_urls') ),
					
					'label' 		=> 	sprintf( esc_html_x( 'Shared %s Steps', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') ),
					'help_text'		=>	sprintf( esc_html_x( 'Use %1$s, %2$s, and %3$s across multiple %4$s', 'placeholder: Lessons, Topics, Quizzes, Courses', 'learndash' ),
											LearnDash_Custom_Label::get_label( 'lessons' ), 
											LearnDash_Custom_Label::get_label( 'topics' ), 
											LearnDash_Custom_Label::get_label( 'quizzes' ), 
											LearnDash_Custom_Label::get_label( 'courses' ) 
										),
					'value' 		=> 	isset( $this->setting_option_values['shared_steps'] ) ? $this->setting_option_values['shared_steps'] : '',
					'options'		=>	array(
											'yes'	=>	esc_html__('Enabled', 'learndash'),
										)
				),
			);
		
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			global $wp_rewrite;
			if ( !$wp_rewrite->using_permalinks() ) {
				$this->setting_option_fields['shared_steps']['value'] = '';
				$this->setting_option_fields['shared_steps']['attrs'] = array( 'disabled' => 'disabled');
			}
			


			parent::load_settings_fields();
		}
		
		function save_settings_fields() {
			if ( isset( $_POST[$this->setting_field_prefix] ) ) {
				if ( ( isset( $_POST[$this->setting_field_prefix]['enabled'] ) ) && ( $_POST[$this->setting_field_prefix]['enabled'] === 'yes' ) && ( isset( $_POST[$this->setting_field_prefix]['shared_steps'] ) ) && ( $_POST[$this->setting_field_prefix]['shared_steps'] === 'yes' ) ) {

					$ld_permalink_options = get_option( 'learndash_settings_permalinks', array() );
					if ( !isset( $ld_permalink_options['nested_urls'] ) ) 
						$ld_permalink_options['nested_urls'] = 'no';
				
					if ( $ld_permalink_options['nested_urls'] !== 'yes' ) {
						$ld_permalink_options['nested_urls'] = 'yes';
					
						update_option( 'learndash_settings_permalinks', $ld_permalink_options );
					
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}
				} else {
					$_POST[$this->setting_field_prefix]['shared_steps'] = '';
				}
			}
		}
	}
}

add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Courses_Builder::add_section_instance();
} );
