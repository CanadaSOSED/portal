<?php
/**
 * LearnDash Settings field WPEditor.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section_Fields' ) ) && ( ! class_exists( 'LearnDash_Settings_Section_Fields_WPEditor' ) ) ) {
	/**
	 * Class to create the settings field.
	 */
	class LearnDash_Settings_Section_Fields_WPEditor extends LearnDash_Settings_Section_Fields {

		/**
		 * Public constructor for class
		 */
		public function __construct() {
			$this->field_type = 'wpeditor';

			parent::__construct();
		}

		/**
		 * Function to crete the settiings field.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args An array of field arguments used to process the ouput.
		 * @return void
		 */
		public function create_section_field( $field_args = array() ) {
			$field_args = apply_filters( 'learndash_settings_field', $field_args );
			if ( isset( $field_args['editor_args'] ) ) {
				$wpeditor_args = $field_args['editor_args'];
			} else {
				$wpeditor_args = array();
			}
			//$wpeditor_args = array_merge( $wpeditor_args, array( 'textarea_name' => $this->get_field_attribute_name( $field_args, false ) ) );

			if ( ( isset( $field_args['attrs'] ) ) && ( ! empty( $field_args['attrs'] ) ) ) {
				$wpeditor_args = array_merge( $wpeditor_args, $field_args['attrs'] );
			}

			$html = apply_filters( 'learndash_settings_field_html_before', '', $field_args );
			wp_editor(
				$this->get_field_attribute_value( $field_args, false ),
				$this->get_field_attribute_id( $field_args, false ),
				$wpeditor_args
			);

			$html = apply_filters( 'learndash_settings_field_html_after', $html, $field_args );

			echo $html;
		}
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_WPEditor::add_field_instance( 'wpeditor' );
} );
