<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Field: Button set
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_button_set extends SP_WPCP_Framework_Options {

	/**
	 * Button set constructor.
	 *
	 * @param array  $field The field array.
	 * @param string $value The value of the button set.
	 * @param string $unique The id for the button set field.
	 */
	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	/**
	 * Function for the button set field.
	 *
	 * @return void
	 */
	public function output() {

		$input_type = ( ! empty( $this->field['radio'] ) ) ? 'radio' : 'checkbox';
		$input_attr = ( ! empty( $this->field['multi_select'] ) ) ? '[]' : '';

		echo $this->element_before();
		echo ( empty( $input_attr ) ) ? '<div class="sp-field-button-set">' : '';

		if ( isset( $this->field['options'] ) ) {
			$options = $this->field['options'];
			foreach ( $options as $key => $value ) {
				echo '<label><input type="' . $input_type . '" name="' . $this->element_name( $input_attr ) . '" value="' . $key . '"' . $this->element_class() . $this->element_attributes( $key ) . $this->checked( $this->element_value(), $key ) . '/><span class="sp-button-set">' . $value . '</span></label>';
			}
		}

		echo ( empty( $input_attr ) ) ? '</div>' : '';
		echo $this->element_after();

	}
}
