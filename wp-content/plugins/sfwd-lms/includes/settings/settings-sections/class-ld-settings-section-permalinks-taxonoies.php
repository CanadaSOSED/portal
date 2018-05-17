<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Permalinks_Taxonomies' ) ) ) {
	class LearnDash_Settings_Section_Permalinks_Taxonomies extends LearnDash_Settings_Section {

		function __construct() {
			$this->settings_page_id					=	'permalink';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_permalinks_taxonomies';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_permalinks_taxonomies';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'learndash_settings_permalinks_taxonomies';
		
			// Section label/header
			$this->settings_section_label			=	__( 'LearnDash Taxonomy Permalinks', 'learndash' );
		
			// Used to show the section description above the fields. Can be empty
			$this->settings_section_description		=	__( 'Controls the URL slugs for the custom taxonomies used by LearnDash.', 'learndash' );

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			
			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {
				parent::__construct(); 			
				$this->save_settings_fields();
			} 
		}
		
		function admin_init() {
			do_action( 'learndash_settings_page_init', $this->settings_page_id );
		}
		
		function add_meta_boxes( $settings_screen_id = '' ) {
			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {

				add_meta_box(
					$this->metabox_key,							/* Meta Box ID */
					$this->settings_section_label,				/* Title */
					array( $this, 'show_meta_box' ),  			/* Function Callback */
					$this->settings_screen_id,               	/* Screen: Our Settings Page */
					$this->metabox_context,                 	/* Context */
					$this->metabox_priority                 	/* Priority */
				);
			}
		}
		
		function load_settings_values() {
			parent::load_settings_values();
			
			if ( $this->setting_option_values === false ) {
				$this->setting_option_values = array();
			}
			
			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values, 
				array(
					'ld_course_category' 	=>	'course-category',
					'ld_course_tag' 		=>	'course-tag',
					'ld_lesson_category' 	=> 	'lesson-category',
					'ld_lesson_tag' 		=> 	'lesson-tag',
					'ld_topic_category'		=> 	'topic-category',
					'ld_topic_tag' 			=> 	'topic-tag'
				)
			);
			
			//error_log('in '. __FUNCTION__ );
			//error_log('setting_option_values<pre>'. print_r($this->setting_option_values, true) .'</pre>' );
			
		}
		
		function load_settings_fields() {
			global $sfwd_lms;
			
			$this->setting_option_fields = array();

			// Course Taxonomies
			$courses_taxonomies = $sfwd_lms->get_post_args_section( 'sfwd-courses', 'taxonomies' );
			//error_log('courses_taxonomies<pre>'. print_r($courses_taxonomies, true) .'</pre>');
			if ( ( isset( $courses_taxonomies['ld_course_category'] ) ) && ( $courses_taxonomies['ld_course_category']['public'] == true ) ) {
				$this->setting_option_fields['ld_course_category'] = array(
					'name'  		=> 	'ld_course_category',
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Category base', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'value' 		=> 	$this->setting_option_values['ld_course_category'],
					'class'			=>	'regular-text'
				);
			}

			if ( ( isset( $courses_taxonomies['ld_course_tag'] ) ) && ( $courses_taxonomies['ld_course_tag']['public'] == true ) ) {
				$this->setting_option_fields['ld_course_tag'] = array(
					'name'  		=> 	'ld_course_tag',
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Tag base', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'value' 		=> 	$this->setting_option_values['ld_course_tag'],
					'class'			=>	'regular-text'
				);
			}


			// Lesson Taxonomies
			$lessons_taxonomies = $sfwd_lms->get_post_args_section( 'sfwd-lessons', 'taxonomies' );
			if ( ( isset( $lessons_taxonomies['ld_lesson_category'] ) ) && ( $lessons_taxonomies['ld_lesson_category']['public'] == true ) ) {
				$this->setting_option_fields['ld_lesson_category'] = array(
					'name'  		=> 	'ld_lesson_category',
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Category base', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) ),
					'value' 		=> 	$this->setting_option_values['ld_lesson_category'],
					'class'			=>	'regular-text'
				);
			}

			if ( ( isset( $lessons_taxonomies['ld_lesson_tag'] ) ) && ( $lessons_taxonomies['ld_lesson_tag']['public'] == true ) ) {
				$this->setting_option_fields['ld_lesson_tag'] = array(
					'name'  		=> 	'ld_lesson_tag',
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Tag base', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) ),
					'value' 		=> 	$this->setting_option_values['ld_lesson_tag'],
					'class'			=>	'regular-text'
				);
			}

			// Topic Taxonomies
			$topics_taxonomies = $sfwd_lms->get_post_args_section( 'sfwd-topic', 'taxonomies' );
			if ( ( isset( $topics_taxonomies['ld_topic_category'] ) ) && ( $topics_taxonomies['ld_topic_category']['public'] == true ) ) {
				$this->setting_option_fields['ld_topic_category'] = array(
					'name'  		=> 	'ld_topic_category',
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Category base', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'value' 		=> 	$this->setting_option_values['ld_topic_category'],
					'class'			=>	'regular-text'
				);
			}

			if ( ( isset( $topics_taxonomies['ld_topic_tag'] ) ) && ( $topics_taxonomies['ld_topic_tag']['public'] == true ) ) {
				$this->setting_option_fields['ld_topic_tag'] = array(
					'name'  		=> 	'ld_topic_tag',
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Tag base', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'value' 		=> 	$this->setting_option_values['ld_topic_tag'],
					'class'			=>	'regular-text'
				);
			}
				
			if ( !empty( $this->setting_option_fields ) ) {
				$this->setting_option_fields['nonce'] = array(
					'name'  		=> 	'nonce',
					'type'  		=> 	'hidden',
					'label' 		=> 	'', 
					'value' 		=> 	wp_create_nonce( 'learndash_permalinks_taxonomies_nonce' ),
					'class'			=>	'hidden'
				);
			}
			//error_log('setting_option_fields<pre>'. print_r($this->setting_option_fields, true) . '</pre>');
			
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			
			parent::load_settings_fields();
		}
		
		function save_settings_fields() {
			if ( isset( $_POST[$this->setting_field_prefix] ) ) {
				if ( ( isset( $_POST[$this->setting_field_prefix]['nonce'] ) ) 
				  && ( wp_verify_nonce( $_POST[$this->setting_field_prefix]['nonce'], 'learndash_permalinks_taxonomies_nonce' ) ) ) {

					$post_fields = $_POST[$this->setting_field_prefix];

					if ( ( isset( $post_fields['ld_course_category'] ) ) && ( !empty( $post_fields['ld_course_category'] ) ) ) {
						$this->setting_option_values['ld_course_category'] = $this->esc_url( $post_fields['ld_course_category'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['ld_course_tag'] ) ) && ( !empty( $post_fields['ld_course_tag'] ) ) ) {
						$this->setting_option_values['ld_course_tag'] = $this->esc_url( $post_fields['ld_course_tag'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['ld_lesson_category'] ) ) && ( !empty( $post_fields['ld_lesson_category'] ) ) ) {
						$this->setting_option_values['ld_lesson_category'] = $this->esc_url( $post_fields['ld_lesson_category'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['ld_lesson_tag'] ) ) && ( !empty( $post_fields['ld_lesson_tag'] ) ) ) {
						$this->setting_option_values['ld_lesson_tag'] = $this->esc_url( $post_fields['ld_lesson_tag'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}
					
					if ( ( isset( $post_fields['ld_topic_category'] ) ) && ( !empty( $post_fields['ld_topic_category'] ) ) ) {
						$this->setting_option_values['ld_topic_category'] = $this->esc_url( $post_fields['ld_topic_category'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['ld_topic_tag'] ) ) && ( !empty( $post_fields['ld_topic_tag'] ) ) ) {
						$this->setting_option_values['ld_topic_tag'] = $this->esc_url( $post_fields['ld_topic_tag'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}
					update_option( $this->settings_section_key, $this->setting_option_values );
				}
			}
		}
		
		function esc_url( $value = '' ) {
			if ( !empty( $value ) ) {
				$value = esc_url_raw( trim( $value ) );
				$value = str_replace( 'http://', '', $value );
				return untrailingslashit( $value );
			}
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_Permalinks_Taxonomies::add_section_instance();
} );
