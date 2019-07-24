<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 * Image Sizes field.
 *
 * @package SP MetaBox Framework.
 */
class SP_WPCP_Framework_Option_image_sizes extends SP_WPCP_Framework_Options {
	/**
	 * Field: Image Sizes
	 *
	 * @param options $field The field options.
	 * @param string  $value The field value.
	 * @param string  $unique The Unique ID of the field.
	 */
	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	/**
	 * The image sizes output.
	 *
	 * @return void
	 */
	public function output() {

		echo $this->element_before();

			global $_wp_additional_image_sizes;

			$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {

				$width  = get_option( "{$_size}_size_w" );
				$height = get_option( "{$_size}_size_h" );
				$crop   = (bool) get_option( "{$_size}_crop" ) ? 'hard' : 'soft';

				$sizes[ $_size ] = "{$_size} - $crop:{$width}x{$height}";

			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

				$width  = $_wp_additional_image_sizes[ $_size ]['width'];
				$height = $_wp_additional_image_sizes[ $_size ]['height'];
				$crop   = $_wp_additional_image_sizes[ $_size ]['crop'] ? 'hard' : 'soft';

				$sizes[ $_size ] = "{$_size} - $crop:{$width}x{$height}";
			}
		}

			$sizes = array_merge(
				$sizes, array(
					'full'   => __( 'original uploaded image', 'wp-carousel-free' ),
				)
			);
			$extra_name       = ( isset( $this->field['attributes']['multiple'] ) ) ? '[]' : '';
			$placeholder      = ( isset( $this->field['attributes']['placeholder'] ) ) ? $this->field['attributes']['placeholder'] : '';
			$placeholder_data = ( isset( $this->field['attributes']['placeholder'] ) ) ? 'data-placeholder="' . $placeholder . '"' : '';
			$chosen_rtl       = ( is_rtl() && strpos( $class, 'chosen' ) ) ? 'chosen-rtl' : '';

			echo '<select ' . $placeholder_data . ' id="sp_' . $this->field['id'] . '" name="' . $this->element_name( $extra_name ) . '"' .
					 $this->element_class( $chosen_rtl )
					 . $this->element_attributes() . '>';

			echo ( isset( $this->field['default_option'] ) ) ? '<option value="">' . $this->field['default_option'] . '</option>' : '';

		if ( ! empty( $sizes ) ) {

			foreach ( $sizes as $key => $value ) {
				echo '<option value="' . $key . '" ' . $this->checked( $this->element_value(), $key, 'selected' ) . '>' . $value . '</option>';
			}
		}

			echo '</select>';

		echo $this->element_after();

	}

}
