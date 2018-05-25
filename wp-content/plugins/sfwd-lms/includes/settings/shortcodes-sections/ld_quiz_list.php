<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_quiz_list' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_quiz_list extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_quiz_list';
			$this->shortcodes_section_title 		= 	sprintf( esc_html_x( '%s List', 'placeholder: Quiz', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( wp_kses_post( _x( "This shortcode shows list of %s. You can use this shortcode on any page if you don't want to use the default <code>/%s/</code> page.", 'placeholders: quizzes, quizzes (URL slug)', 'learndash' ) ), LearnDash_Custom_Label::label_to_lower( 'quizzes' ), LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'quizzes' ) );
			
			parent::__construct(); 
		}
		
		function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'course_id' => array(
					'id'			=>	$this->shortcodes_section_key . '_course_id',
					'name'  		=> 	'course_id', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( esc_html_x( '%s ID', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( esc_html_x( 'Enter single %1$s ID. Leave blank for all %2$s.', 'placeholders: Course, Courses', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),

				'orderby' => array(
					'id'			=>	$this->shortcodes_section_key . '_orderby',
					'name'  		=> 	'orderby', 
					'type'  		=> 	'select',
					'label' 		=> 	esc_html__( 'Order by', 'learndash' ),
					'help_text'		=>	wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'learndash' ) ),
					'value' 		=> 	'ID',
					'options'		=>	array(
											''	 		 => sprintf( esc_html_x('Order by %s. (default)', 'placeholder: course', 'learndash'), LearnDash_Custom_Label::label_to_lower( 'course' ) ),
											'id'		 =>	esc_html__('ID - Order by post id.', 'learndash'),
											'title'		 =>	esc_html__('Title - Order by post title', 'learndash'),
											'date'		 =>	esc_html__('Date - Order by post date', 'learndash'),
											'menu_order' =>	esc_html__('Menu - Order by Page Order Value', 'learndash'),
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
											''	 		 =>	sprintf( esc_html_x('Order per %s (default)', 'placeholder: course', 'learndash'), LearnDash_Custom_Label::label_to_lower( 'course' ) ),
											'DESC'		 =>	esc_html__('DESC - highest to lowest values', 'learndash'),
											'ASC'		 =>	esc_html__('ASC - lowest to highest values', 'learndash'),
										)
				),
				'num' => array(
					'id'			=>	$this->shortcodes_section_key . '_num',
					'name'  		=> 	'num', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( esc_html_x( '%s Per Page', 'placeholders: quizzes', 'learndash'), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
					'help_text'		=>	sprintf( esc_html_x( '%s per page. Default is %d. Set to zero for all.', 'placeholders: quizzes, default per page', 'learndash' ), LearnDash_Custom_Label::get_label( 'quizzes' ), LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Per_Page', 'per_page' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text',
					'attrs'			=>	array(
											'min' => 0,
											'step' => 1
										)
				),
				'show_content' => array(
					'id'			=>	$this->shortcodes_section_key . 'show_content',
					'name'  		=> 	'show_content', 
					'type'  		=> 	'select',
					'label' 		=> 	sprintf( esc_html_x('Show %s Content', 'placeholder: Quiz', 'learndash'), LearnDash_Custom_Label::get_label( 'quiz' ) ),
					'help_text'		=>	sprintf( esc_html_x( 'shows %s content.', 'placeholders: quiz', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ),
					'value' 		=> 	'true',
					'options'		=>	array(
											'' => esc_html__('Yes (default)', 'learndash'),
											'false' =>	esc_html__('No', 'learndash'),
										)
				),
				'show_thumbnail' => array(
					'id'			=>	$this->shortcodes_section_key . 'show_thumbnail',
					'name'  		=> 	'show_thumbnail', 
					'type'  		=> 	'select',
					'label' 		=> 	sprintf( esc_html_x('Show %s Thumbnail', 'placeholder: Quiz', 'learndash'), LearnDash_Custom_Label::get_label( 'quiz' ) ),
					'help_text'		=>	sprintf( esc_html_x( 'shows a %s thumbnail.', 'placeholders: quiz', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ),
					'value' 		=> 	'true',
					'options'		=>	array(
											'' => esc_html__('Yes (default)', 'learndash'),
											'false' =>	esc_html__('No', 'learndash'),
										)
				),
				
			);

			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) != 'yes' ) {
				foreach( $this->shortcodes_option_fields['orderby']['options'] as $option_key => $option_label ) {
					if ( empty( $option_key ) ) {
						unset( $this->shortcodes_option_fields['orderby']['options'][$option_key] );
					}
				}

				foreach( $this->shortcodes_option_fields['order']['options'] as $option_key => $option_label ) {
					if ( empty( $option_key ) ) {
						unset( $this->shortcodes_option_fields['order']['options'][$option_key] );
					}
				}
			}

			if ( defined( 'LEARNDASH_COURSE_GRID_FILE' ) ) {
				$this->shortcodes_option_fields['col'] = array(
					'id'			=>	$this->shortcodes_section_key . '_col',
					'name'  		=> 	'col', 
					'type'  		=> 	'number',
					'label' 		=> 	esc_html__('Columns','learndash'),
					'help_text'		=>	sprintf( esc_html_x( 'number of columns to show when using %s grid addon', 'placeholders: course', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'course' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
			}


			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
