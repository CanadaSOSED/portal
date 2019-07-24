<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Email validate
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_wpcp_validate_email' ) ) {

	/**
	 * Validate email address.
	 *
	 * @param email  $value the value of the field.
	 * @param string $field The field.
	 * @return statement
	 */
	function sp_wpcp_validate_email( $value, $field ) {

		if ( ! sanitize_email( $value ) ) {
			return __( 'Please write a valid email address!', 'wp-carousel-free' );
		}

	}
	add_filter( 'sp_wpcp_validate_email', 'sp_wpcp_validate_email', 10, 2 );
}

/**
 *
 * Numeric validate
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_wpcp_validate_numeric' ) ) {
	/**
	 * Validate numeric value.
	 *
	 * @param mix    $value Verify the number.
	 * @param string $field The field.
	 * @return statement
	 */
	function sp_wpcp_validate_numeric( $value, $field ) {

		if ( ! is_numeric( $value ) ) {
			return __( 'Please write a numeric data!', 'wp-carousel-free' );
		}

	}
	add_filter( 'sp_wpcp_validate_numeric', 'sp_wpcp_validate_numeric', 10, 2 );
}

/**
 *
 * Required validate
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'sp_wpcp_validate_required' ) ) {
	/**
	 * Validate required.
	 *
	 * @param mix $value Value of the field.
	 * @return statement
	 */
	function sp_wpcp_validate_required( $value ) {
		if ( empty( $value ) ) {
			return __( 'Fatal Error! This field is required!', 'wp-carousel-free' );
		}
	}
	add_filter( 'sp_wpcp_validate_required', 'sp_wpcp_validate_required' );
}
