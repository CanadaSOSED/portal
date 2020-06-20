<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: switcher
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_WPCF_Field_switcherf' ) ) {
	class SP_WPCF_Field_switcherf extends SP_WPCF_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			$text_off   = ( ! empty( $this->field['text_off'] ) ) ? $this->field['text_off'] : esc_html__( 'Off', 'wp-carousel-free' );
			$text_width = ( ! empty( $this->field['text_width'] ) ) ? ' style="width: ' . $this->field['text_width'] . 'px;"' : '';

			echo $this->field_before();

			echo '<div class="spf--switcher"' . $text_width . '>';
			echo '<span class="spf--off">' . $text_off . '</span>';
			echo '<span class="spf--ball"></span>';
			echo '<input type="text" name="' . $this->field_name() . '" value="' . $this->value . '"' . $this->field_attributes() . ' />';
			echo '</div>';

			echo ( ! empty( $this->field['label'] ) ) ? '<span class="spf--label">' . $this->field['label'] . '</span>' : '';

			echo '<div class="clear"></div>';

			echo $this->field_after();

		}

	}
}
