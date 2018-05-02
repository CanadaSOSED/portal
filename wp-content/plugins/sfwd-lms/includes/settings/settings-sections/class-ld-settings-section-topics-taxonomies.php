<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Topics_Taxonomies' ) ) ) {
	class LearnDash_Settings_Topics_Taxonomies extends LearnDash_Settings_Section {

		function __construct() {
			// What screen ID are we showing on
			$this->settings_screen_id				=	'sfwd-topic_page_topics-options';
			
			// The page ID (different than the screen ID)
			$this->settings_page_id					=	'topics-options';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_topics_taxonomies';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_topics_taxonomies';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'taxonomies';
		
			// Section label/header
			$this->settings_section_label			=	sprintf( esc_html_x( '%s Taxonomies', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic') );
		
			// Used to show the section description above the fields. Can be empty
			$this->settings_section_description		=	sprintf( wp_kses_post( _x( '<p>Control which Taxonomies can be used with the LearnDash %s.</p>', 'placeholder: Topics', 'learndash' ) ), LearnDash_Custom_Label::get_label( 'topics') );

			parent::__construct(); 
		}
		
		function load_settings_values() {
			parent::load_settings_values();

			$_INITIALIZE = false; 
			if ( $this->setting_option_values === false ) {
				$_INITIALIZE = true;
				$this->setting_option_values = array(
					'ld_topic_category' 	=>	'yes',
					'ld_topic_tag' 			=> 	'yes',
					'wp_post_category'		=> 	'yes',
					'wp_post_tag' 			=> 	'yes'
				);
				
				// If this is a new install we want to turn off WP Post Category/Tag
				require_once( LEARNDASH_LMS_PLUGIN_DIR .'includes/admin/class-learndash-admin-settings-data-upgrades.php' );
				$this->ld_admin_settings_data_upgrades = Learndash_Admin_Settings_Data_Upgrades::get_instance();
		
				$ld_prior_version = $this->ld_admin_settings_data_upgrades->get_data_settings( 'prior_version' );
				if ( $ld_prior_version == 'new' ) {
					$this->setting_option_values['wp_post_category'] = 'no';
					$this->setting_option_values['wp_post_tag'] = 'no';
				}
			} 
			
			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values, 
				array(
					'ld_topic_category' 	=>	'no',
					'ld_topic_tag' 			=> 	'no',
					'wp_post_category'		=> 	'no',
					'wp_post_tag' 			=> 	'no'
				)
			);
		}

		function load_settings_fields() {

			$this->setting_option_fields = array(
				'ld_topic_category' => array(
					'name'  		=> 	'ld_topic_category', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	sprintf( esc_html_x( 'LearnDash %s Categories', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'help_text'  	=> 	sprintf( esc_html_x( 'Enable the builtin LearnDash %s Categories', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'value' 		=> 	$this->setting_option_values['ld_topic_category'],
					'options'		=>	array(
											'yes'	=>	esc_html__('Yes', 'learndash'),
										)
				),
				'ld_topic_tag' => array(
					'name'  		=> 	'ld_topic_tag', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	sprintf( esc_html_x( 'LearnDash %s Tags', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'help_text'  	=> 	sprintf( esc_html_x( 'Enable the builtin LearnDash %s Tags', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'value' 		=> 	$this->setting_option_values['ld_topic_tag'],
					'options'		=>	array(
											'yes'	=>	esc_html__('Yes', 'learndash'),
										)
				),
				'wp_post_category' => array(
					'name'  		=> 	'wp_post_category', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	esc_html__( 'WordPress Post Categories', 'learndash' ),
					'help_text'  	=> 	esc_html__( 'Enable the builtin WordPress Post Categories', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['wp_post_category'],
					'options'		=>	array(
											'yes'	=>	esc_html__('Yes', 'learndash'),
										)
				),
				'wp_post_tag' => array(
					'name'  		=> 	'wp_post_tag', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	esc_html__( 'WordPress Post Tags', 'learndash' ),
					'help_text'  	=> 	esc_html__( 'Enable the builtin WordPress Post Tags', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['wp_post_tag'],
					'options'		=>	array(
											'yes'	=>	esc_html__('Yes', 'learndash'),
										)
				),
			);
		
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			
			parent::load_settings_fields();
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Topics_Taxonomies::add_section_instance();
} );
