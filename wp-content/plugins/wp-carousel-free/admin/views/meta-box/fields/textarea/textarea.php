<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Field: Textarea
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_textarea extends SP_WPCP_Framework_Options {

	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	public function output() {

		echo $this->element_before();
		echo '<textarea name="' . $this->element_name() . '"' . $this->element_class() . $this->element_attributes() . '>' . $this->element_value() . '</textarea>';
		echo $this->element_after();

	}
}
