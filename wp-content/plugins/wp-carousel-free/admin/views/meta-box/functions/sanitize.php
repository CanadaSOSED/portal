<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

if ( ! function_exists( 'sp_sanitize_text' ) ) {
/**
 *  Text sanitize
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 * @param string $value Sanitize text field value.
 * @param string $field The field type.
 * @return statement
 */
	function sp_sanitize_text( $value, $field ) {
		return wp_filter_nohtml_kses( $value );
	}
	add_filter( 'sp_sanitize_text', 'sp_sanitize_text', 10, 2 );
}

/**
 *
 * Textarea sanitize
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_sanitize_textarea' ) ) {
	function sp_sanitize_textarea( $value ) {

		global $allowedposttags;
		return wp_kses( $value, $allowedposttags );

	}
	add_filter( 'sp_sanitize_textarea', 'sp_sanitize_textarea' );
}

/**
 *
 * Checkbox sanitize
 * Do not touch, or think twice.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_sanitize_checkbox' ) ) {
	function sp_sanitize_checkbox( $value ) {

		if ( ! empty( $value ) && $value == 1 ) {
			$value = true;
		}

		if ( empty( $value ) ) {
			$value = false;
		}

		return $value;

	}
	add_filter( 'sp_sanitize_checkbox', 'sp_sanitize_checkbox' );
	add_filter( 'sp_sanitize_switcher', 'sp_sanitize_checkbox' );
}

/**
 *
 * Image select sanitize
 * Do not touch, or think twice.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_sanitize_image_select' ) ) {
	function sp_sanitize_image_select( $value ) {

		if ( isset( $value ) && is_array( $value ) ) {
			if ( count( $value ) ) {
				$value = $value;
			} else {
				$value = $value[0];
			}
		} elseif ( empty( $value ) ) {
			$value = '';
		}

		return $value;

	}
	add_filter( 'sp_sanitize_image_select', 'sp_sanitize_image_select' );
}

/**
 *
 * Group sanitize
 * Do not touch, or think twice.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_sanitize_group' ) ) {
	function sp_sanitize_group( $value ) {
		return ( empty( $value ) ) ? '' : $value;
	}
	add_filter( 'sp_sanitize_group', 'sp_sanitize_group' );
}

/**
 *
 * Title sanitize
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_sanitize_title' ) ) {
	function sp_sanitize_title( $value ) {
		return sanitize_title( $value );
	}
	add_filter( 'sp_sanitize_title', 'sp_sanitize_title' );
}

/**
 *
 * Text clean
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_sanitize_clean' ) ) {
	function sp_sanitize_clean( $value ) {
		return $value;
	}
	add_filter( 'sp_sanitize_clean', 'sp_sanitize_clean', 10, 2 );
}
