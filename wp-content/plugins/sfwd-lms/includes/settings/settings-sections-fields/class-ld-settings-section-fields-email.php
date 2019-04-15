<?php
/**
 * LearnDash Settings field text/input.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section_Fields_Text' ) ) && ( ! class_exists( 'LearnDash_Settings_Section_Fields_Email' ) ) ) {

	/**
	 * Class to create the settings field.
	 */
	class LearnDash_Settings_Section_Fields_Email extends LearnDash_Settings_Section_Fields_Text {

		/**
		 * Public constructor for class
		 */
		public function __construct() {
			$this->field_type = 'email';

			parent::__construct();
		}

		/**
		 * Validate field
		 *
		 * @since 2.6.0
		 *
		 * @param mixed  $val Value to validate.
		 * @param string $key Key of value being validated.
		 * @param array  $args Array of field args.
		 *
		 * @return integer value.
		 */
		public function validate_section_field( $val, $key, $args = array() ) {
			return sanitize_email( $val );
		}

		// End of functions.
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_Text::add_field_instance( 'email' );
} );
