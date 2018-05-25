<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Translations_LearnDash' ) ) ) {
	class LearnDash_Settings_Section_Translations_LearnDash extends LearnDash_Settings_Section {

		// Must match the Text Domain
		private $project_slug = 'learndash';

		function __construct() {
			$this->settings_page_id					=	'learndash_lms_translations';
		
			$this->setting_option_key 				= 	'learndash';

			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'settings_translations_'. $this->project_slug;
					
			// Section label/header
			$this->settings_section_label			=	esc_html__( 'LearnDash LMS', 'learndash' );
		
			LearnDash_Translations::register_translation_slug( $this->project_slug, LEARNDASH_LMS_PLUGIN_DIR .'languages/' );
			
			parent::__construct(); 
		}
				
		// Not used for LearnDash core. to be used for LD add-ons
		// This will not add the metabox IF there are no available translations yet from the remote GlotPress
		/*
		function add_meta_boxes( $settings_screen_id = '' ) {
			if ( $settings_screen_id == $this->settings_screen_id ) {
				if ( LearnDash_Translations::project_has_available_translations( $this->project_slug ) === true ) {
					parent::add_meta_boxes( $settings_screen_id );
				}
			}
		}
		*/
		
		function show_meta_box() {
			$ld_translations = new LearnDash_Translations( $this->project_slug );
			$ld_translations->show_meta_box();
		}
	}

	add_action( 'init', function() {
		LearnDash_Settings_Section_Translations_LearnDash::add_section_instance();
	}, 1 );
}
