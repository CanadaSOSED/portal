<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Field: Number
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_number extends SP_WPCP_Framework_Options {

	/**
	 * Number field constructor.
	 *
	 * @param string $field The field type.
	 * @param string $value The value of the field.
	 * @param string $unique The unique ID of the field.
	 */
	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	/**
	 * Number field output.
	 *
	 * @return void
	 */
	public function output() {

		echo $this->element_before();
		$unit = ( isset( $this->field['unit'] ) ) ? '<em>' . $this->field['unit'] . '</em>' : '';
		echo '<input' . $this->element_pro_only() . ' type="number" name="' . $this->element_name() . '" value="' . $this->element_value() . '"' . $this->element_class() . $this->element_attributes() . '/>' . $unit;
		echo $this->element_after();

	}

}
