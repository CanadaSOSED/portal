<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Field: carousel_type
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_carousel_type extends SP_WPCP_Framework_Options {

	/**
	 * Carousel type constructor.
	 *
	 * @param array  $field The field array.
	 * @param string $value The value of the carousel type.
	 * @param string $unique The id for the carousel type field.
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
		echo ( empty( $input_attr ) ) ? '<div class="sp-field-carousel-type">' : '';

		if ( isset( $this->field['options'] ) ) {
			$options = $this->field['options'];
			foreach ( $options as $key => $value ) {
				$pro_only      = true == $value['pro_only'] ? 'disabled' : '';
				$pro_only_text = true == $value['pro_only'] ? '<strong class="ct-pro-only">PRO</strong>' : '';
				echo '<label><input ' . $pro_only . ' type="' . $input_type . '" name="' . $this->element_name( $input_attr ) . '" value="' . $key . '"' . $this->element_class() . $this->element_attributes( $key ) . $this->checked( $this->element_value(), $key ) . '/><span>' . $pro_only_text . '<i class="' . $value['icon'] . '"></i><p class="sp-carousel-type">' . $value['text'] . '</p></span></label>';
			}
		}

		echo ( empty( $input_attr ) ) ? '</div>' : '';
		echo $this->element_after();

	}
}
