<?php
/**
 * LearnDash Settings field checkbox.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section_Fields' ) ) && ( ! class_exists( 'LearnDash_Settings_Section_Fields_Checkbox' ) ) ) {

	/**
	 * Class to create the settings field.
	 */
	class LearnDash_Settings_Section_Fields_Checkbox extends LearnDash_Settings_Section_Fields {

		/**
		 * Public constructor for class
		 */
		public function __construct() {
			$this->field_type = 'checkbox';

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

			$html = apply_filters( 'learndash_settings_field_html_before', '', $field_args );

			if ( ( isset( $field_args['options'] ) ) && ( ! empty( $field_args['options'] ) ) ) {
				if ( ( isset( $field_args['desc'] ) ) && ( ! empty( $field_args['desc'] ) ) ) {
					$html .= $field_args['desc'];
				}

				$html .= '<fieldset>';
				$html .= '<legend class="screen-reader-text">';
				$html .= '<span>' . $field_args['label'] . '</span>';
				$html .= '</legend>';

				foreach ( $field_args['options'] as $option_key => $option_label ) {

					$html .= ' <label for="' . $field_args['id'] . '-' . $option_key . '" >';
					$html .= '<input ';

					$html .= $this->get_field_attribute_type( $field_args );
					$html .= $this->get_field_attribute_id( $field_args );
					$html .= $this->get_field_attribute_name( $field_args );
					$html .= $this->get_field_attribute_class( $field_args );
					$html .= $this->get_field_attribute_misc( $field_args );
					$html .= $this->get_field_attribute_required( $field_args );

					if ( isset( $field_args['value'] ) ) {
						$html .= ' value="' . $option_key . '" ';
					} else {
						$html .= ' value="" ';
					}

					$html .= ' ' . checked( $option_key, $field_args['value'], false ) . ' ';

					$html .= ' />';

					$html .= $option_label . '</label>';
					$html .= '</br>';
				}
				$html .= '</fieldset>';
			}

			$html = apply_filters( 'learndash_settings_field_html_after', $html, $field_args );

			echo $html;
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
			if ( isset( $args['field']['options'][ $val ] ) ) {
				return $val;
			} elseif ( isset( $args['field']['default'] ) ) {
				return $args['field']['default'];
			} else {
				return '';
			}
		}
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_Checkbox::add_field_instance( 'checkbox' );
} );
