<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: heading
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_WPCF_Field_heading' ) ) {
	class SP_WPCF_Field_heading extends SP_WPCF_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			echo ( ! empty( $this->field['content'] ) ) ? $this->field['content'] : '';
			echo ( ! empty( $this->field['image'] ) ) ? '<img src="' . $this->field['image'] . '">' : '';

			echo ( ! empty( $this->field['after'] ) && ! empty( $this->field['link'] ) ) ? '<span class="spacer"></span><span class="support"><a target="_blank" href="' . $this->field['link'] . '">' . $this->field['after'] . '</a></span>' : '';
		}

	}
}
