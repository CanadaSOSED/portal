<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Field: Gallery
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_Gallery extends SP_WPCP_Framework_Options {

	/**
	 * The gallery field construcor.
	 *
	 * @param string $field The field type.
	 * @param string $value The field value.
	 * @param string $unique The unique field ID.
	 */
	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	/**
	 * The gallery field output.
	 *
	 * @return void
	 */
	public function output() {

		echo $this->element_before();

		$value  = $this->element_value();
		$add    = ( ! empty( $this->field['add_title'] ) ) ? $this->field['add_title'] : __( 'Add Gallery', 'wp-carousel-free' );
		$edit   = ( ! empty( $this->field['edit_title'] ) ) ? $this->field['edit_title'] : __( 'Edit Gallery', 'wp-carousel-free' );
		$clear  = ( ! empty( $this->field['clear_title'] ) ) ? $this->field['clear_title'] : __( 'Clear', 'wp-carousel-free' );
		$hidden = ( empty( $value ) ) ? ' hidden' : '';
		echo '<a href="#" class="button button-primary sp-add"><i class="fa fa-plus-circle"></i>' . $add . '</a>';
		echo '<ul class="sp-gallery-images">';

		if ( ! empty( $value ) ) {

			$values = explode( ',', $value );

			foreach ( $values as $id ) {
				$attachment = wp_get_attachment_image_src( $id, 'thumbnail' );
				echo '<li><img src="' . $attachment[0] . '" alt="" /></li>';
			}
		}

		echo '</ul>';
		echo '<ul><li><a href="#" class="button sp-edit' . $hidden . '"><i class="fa fa-pencil-square-o"></i>' . $edit . '</a></li></ul>';
		echo '<ul><li><a href="#" class="button sp-warning-primary sp-remove' . $hidden . '"><i class="fa fa-trash"></i>' . $clear . '</a></li></ul>';
		echo '<input type="text" name="' . $this->element_name() . '" value="' . $value . '"' . $this->element_class() . $this->element_attributes() . '/>';

		echo $this->element_after();

	}
}
