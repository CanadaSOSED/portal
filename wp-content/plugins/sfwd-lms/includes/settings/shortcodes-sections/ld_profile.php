<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_profile' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_profile extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_profile';
			$this->shortcodes_section_title 		= 	esc_html__( 'Profile', 'learndash' );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( esc_html_x( 'Displays user\'s enrolled %1$s, %2$s progress, %3$s scores, and achieved certificates.', 'placeholder: courses, course, quiz', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ), LearnDash_Custom_Label::label_to_lower( 'course' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) );
			
			parent::__construct(); 
		}
		
		function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				
				'per_page' => array(
					'id'			=>	$this->shortcodes_section_key . '_per_page',
					'name'  		=> 	'per_page', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( esc_html_x( '%s per page', 'placeholder: Courses', 'learndash' ), LearnDash_Custom_Label::get_label( 'Courses' ) ),
					'help_text'		=>	sprintf( esc_html_x( '%s per page. Default is %d. Set to zero for all.', 'placeholder: Courses, default per page', 'learndash' ), LearnDash_Custom_Label::get_label( 'Courses' ), LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Per_Page', 'per_page' ) ),
					'value' 		=> 	false,
					'class'			=>	'small-text',
				),
				
				'orderby' => array(
					'id'			=>	$this->shortcodes_section_key . '_orderby',
					'name'  		=> 	'orderby', 
					'type'  		=> 	'select',
					'label' 		=> 	esc_html__( 'Order by', 'learndash' ),
					'help_text'		=>	wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'learndash' ) ),
					'value' 		=> 	'ID',
					'options'		=>	array(
											''			=>	esc_html__('ID - Order by post id. (default)', 'learndash'),
											'title'			=>	esc_html__('Title - Order by post title', 'learndash'),
											'date'			=>	esc_html__('Date - Order by post date', 'learndash'),
											'menu_order'	=>	esc_html__('Menu - Order by Page Order Value', 'learndash')
										)
				),
				'order' => array(
					'id'			=>	$this->shortcodes_section_key . '_order',
					'name'  		=> 	'order', 
					'type'  		=> 	'select',
					'label' 		=> 	esc_html__( 'Order', 'learndash' ),
					'help_text'		=>	esc_html__( 'Order', 'learndash' ),
					'value' 		=> 	'ID',
					'options'		=>	array(
											''			=>	esc_html__('DESC - highest to lowest values (default)', 'learndash'),
											'ASC'			=>	esc_html__('ASC - lowest to highest values', 'learndash'),
										)
				),
				
				'course_points_user' => array(
					'id'			=>	$this->shortcodes_section_key . 'course_points_user',
					'name'  		=> 	'course_points_user', 
					'type'  		=> 	'select',
					'label' 		=> 	sprintf( esc_html_x('Show Earned %s Points', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( esc_html_x('Show Earned %s Points', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'value' 		=> 	'',
					'options'		=>	array(
											''	=>	esc_html__('Yes', 'learndash'),
											'no'	=>	esc_html__('No', 'learndash'),
										)
				),

				'expand_all' => array(
					'id'			=>	$this->shortcodes_section_key . 'expand_all',
					'name'  		=> 	'expand_all', 
					'type'  		=> 	'select',
					'label' 		=> 	sprintf( esc_html_x('Expand All %s Sections', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( esc_html_x('Expand All %s sections', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'value' 		=> 	'no',
					'options'		=>	array(
											''		=>	esc_html__('No', 'learndash'),
											'yes'	=>	esc_html__('Yes', 'learndash')
										)
				),

				'profile_link' => array(
					'id'			=> $this->shortcodes_section_key . 'profile_link',
					'name'  		=> 'profile_link', 
					'type'  		=> 'select',
					'label' 		=> esc_html__( 'Show Profile Link', 'learndash' ),
					'help_text'		=> esc_html__( 'Show Profile Link', 'learndash' ),
					'value' 		=>  'yes',
					'options'		=> array(
										'' => esc_html__( 'Yes', 'learndash' ),
										'no' =>	esc_html__( 'No', 'learndash' ),
										)
				),

				'show_quizzes' => array(
					'id'			=>	$this->shortcodes_section_key . 'show_quizzes',
					'name'  		=> 	'show_quizzes', 
					'type'  		=> 	'select',
					'label' 		=> 	esc_html__( 'Show User Quiz Attempts', 'learndash' ),
					'help_text'		=>	esc_html__( 'Show User Quiz Attempts', 'learndash' ),
					'value' 		=> 	'yes',
					'options'		=>	array(
											'' => esc_html__( 'Yes', 'learndash' ),
											'no' =>	esc_html__( 'No', 'learndash' )
										)
				),
			);
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
