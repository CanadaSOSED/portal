<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: gallery
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_WPCF_Field_gallery' ) ) {
	class SP_WPCF_Field_gallery extends SP_WPCF_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'add_title'   => esc_html__( 'Add Gallery', 'wp-carousel-free' ),
					'edit_title'  => esc_html__( 'Edit Gallery', 'wp-carousel-free' ),
					'clear_title' => esc_html__( 'Clear', 'wp-carousel-free' ),
				)
			);

			$hidden = ( empty( $this->value ) ) ? ' hidden' : '';

			echo $this->field_before();
			echo '<a href="#" class="button button-primary spf-button"><i class="fa fa-plus-circle"></i>' . $args['add_title'] . '</a>';
			echo '<ul class="sp-gallery-images">';
			if ( ! empty( $this->value ) ) {

				$values = explode( ',', $this->value );

				foreach ( $values as $id ) {
					$attachment = wp_get_attachment_image_src( $id, 'thumbnail' );
					echo '<li><img src="' . $attachment[0] . '" alt="" /></li>';
				}
			}

			echo '</ul>';

			// echo '<a href="#" class="button spf-edit-gallery' . $hidden . '"><i class="fa fa-pencil-square-o"></i>' . $args['edit_title'] . '</a>';
			// echo '<a href="#" class="button spf-warning-primary spf-clear-gallery' . $hidden . '"><i class="fa fa-trash"></i>' . $args['clear_title'] . '</a>';
			echo '<ul><li><a href="#" class="button spf-edit-gallery' . $hidden . '"><i class="fa fa-pencil-square-o"></i>' . $args['edit_title'] . '</a></li></ul>';
			echo '<ul><li><a href="#" class="button spf-warning-primary spf-clear-gallery' . $hidden . '"><i class="fa fa-trash"></i>' . $args['clear_title'] . '</a></li></ul>';
			echo '<input type="text" name="' . $this->field_name() . '" value="' . $this->value . '"' . $this->field_attributes() . '/></span>';

			echo $this->field_after();

		}

	}
}
