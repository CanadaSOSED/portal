<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Field: Select
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_select extends SP_WPCP_Framework_Options {

	/**
	 * Select field constructor.
	 *
	 * @param string $field Field type.
	 * @param string $value The field value.
	 * @param string $unique The field ID.
	 */
	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	/**
	 * Select field output.
	 *
	 * @return void
	 */
	public function output() {

		echo $this->element_before();

		if ( isset( $this->field['options'] ) ) {
			$options          = $this->field['options'];
			$class            = $this->element_class();
			$options          = ( is_array( $options ) ) ? $options : array_filter( $this->element_data( $options ) );
			$extra_name       = ( isset( $this->field['attributes']['multiple'] ) ) ? '[]' : '';
			$placeholder      = ( isset( $this->field['attributes']['placeholder'] ) ) ? $this->field['attributes']['placeholder'] : '';
			$placeholder_data = ( isset( $this->field['attributes']['placeholder'] ) ) ? 'data-placeholder="' . $placeholder . '"' : '';
			$chosen_rtl       = ( is_rtl() && strpos( $class, 'chosen' ) ) ? 'chosen-rtl' : '';
			echo '<select ' . $placeholder_data . ' id="sp_' . $this->field['id'] . '" name="' . $this->element_name( $extra_name ) . '"' .
					 $this->element_class( $chosen_rtl )
					 . $this->element_attributes() . '>';

			echo ( isset( $this->field['default_option'] ) ) ? '<option value="">' . $this->field['default_option'] . '</option>' : '';

			if ( ! empty( $options ) ) {
				foreach ( $options as $key => $value ) {
					$pro_only = true == $value['pro_only'] ? ' disabled' : '';
					echo '<option' . $pro_only . ' value="' . $key . '" ' . $this->checked( $this->element_value(), $key, 'selected' ) . '>' . $value['text'] . '</option>';
				}
			}
			echo '</select>';

		}
		echo $this->element_after();

	}

}
