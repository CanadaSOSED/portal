<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_course_expire_status' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_course_expire_status extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_course_expire_status';
			$this->shortcodes_section_title 		= 	sprintf( esc_html_x( '%s Expire Status', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( esc_html_x( 'This shortcode displays the user %s access expire date.', 'placeholders: course', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'course' ) );
			
			parent::__construct(); 
		}
		
		function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'course_id' => array(
					'id'			=>	$this->shortcodes_section_key . '_course_id',
					'name'  		=> 	'course_id', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( esc_html_x( '%s ID', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( esc_html_x( 'Enter single %1$s ID. Leave blank for current %2$s.', 'placeholders: Course, Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
				'user_id' => array(
					'id'			=>	$this->shortcodes_section_key . '_user_id',
					'name'  		=> 	'user_id', 
					'type'  		=> 	'number',
					'label' 		=> 	esc_html__( 'User ID', 'learndash' ),
					'help_text'		=>	esc_html__('Enter specific User ID. Leave blank for current User.', 'learndash' ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
				
				'label_before' => array(
					'id'			=>	$this->shortcodes_section_key . '_label_before',
					'name'  		=> 	'label_before', 
					'type'  		=> 	'text',
					'label' 		=> 	esc_html__( 'Label before', 'learndash'),
					'help_text'		=>	esc_html__( 'The label prefix shown before the access expires', 'learndash' ),
					'value' 		=> 	'',
				),

				'label_after' => array(
					'id'			=>	$this->shortcodes_section_key . '_label_after',
					'name'  		=> 	'label_after', 
					'type'  		=> 	'text',
					'label' 		=> 	esc_html__( 'Label after', 'learndash'),
					'help_text'		=>	esc_html__( 'The label prefix shown after access has expired', 'learndash' ),
					'value' 		=> 	'',
				)
			);
		
			if ( ( !isset( $this->fields_args['post_type'] ) ) || ( ( $this->fields_args['post_type'] != 'sfwd-courses' ) && ( $this->fields_args['post_type'] != 'sfwd-lessons' ) && ( $this->fields_args['post_type'] != 'sfwd-topic' ) ) ) {
			
				$this->shortcodes_option_fields['course_id']['required'] 	= 'required';	
				$this->shortcodes_option_fields['course_id']['help_text']	= sprintf( esc_html_x( 'Enter single %s ID.', 'placeholders: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
			} 

			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
