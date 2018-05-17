<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Custom_Labels_Submit' ) ) ) {
	class LearnDash_Settings_Section_Custom_Labels_Submit extends LearnDash_Settings_Section {

		function __construct() {
		
			$this->settings_page_id					=	'learndash_lms_settings_custom_labels';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'submitdiv';
		
			// Section label/header
			$this->settings_section_label			=	esc_html__( 'Save Options', 'learndash' );
			$this->metabox_context					=	'side';
			$this->metabox_priority					=	'high';
			
			parent::__construct(); 
			
			// We override the parent value set for $this->metabox_key because we want the div ID to match the details WordPress
			// value so it will be hidden.
			$this->metabox_key = 'submitdiv';
		}
		
		function show_meta_box() {

			?>
			<div id="submitpost" class="submitbox">

				<div id="major-publishing-actions">

					<div id="publishing-action">
						<span class="spinner"></span>
						<?php submit_button( esc_html__( 'Save', 'learndash' ), 'primary', 'submit', false ); ?>
					</div>

					<div class="clear"></div>

				</div><!-- #major-publishing-actions -->

			</div><!-- #submitpost -->
			<?php
		}
	
		// This is a requires function
		function load_settings_fields() {
			
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_Custom_Labels_Submit::add_section_instance();
} );
