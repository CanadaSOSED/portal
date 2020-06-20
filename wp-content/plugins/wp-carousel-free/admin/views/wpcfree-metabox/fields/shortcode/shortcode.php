<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: shortcode
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_WPCF_Field_shortcode' ) ) {
	class SP_WPCF_Field_shortcode extends SP_WPCF_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			// Get the Post ID.
			$post_id = get_the_ID();

			echo ( ! empty( $post_id ) ) ? '<div class="wpcp-scode-wrap"><span class="wpcp-sc-title">Shortcode:</span><span class="wpcp-shortcode-selectable">[sp_wpcarousel id="' . $post_id . '"]</span></div><div class="wpcp-scode-wrap"><span class="wpcp-sc-title">Template Include:</span><span class="wpcp-shortcode-selectable">&lt;?php echo do_shortcode(\'[sp_wpcarousel id="' . $post_id . '"]\'); ?&gt;</span></div>' : '';
		}

	}
}
