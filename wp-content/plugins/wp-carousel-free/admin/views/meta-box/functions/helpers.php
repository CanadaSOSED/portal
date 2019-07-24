<?php
/**
 *
 * Helper functions of the framework.get_the_generator
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package SP Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

if ( ! function_exists( 'sp_wpcp_add_element' ) ) {
	/**
	 * Add framework element
	 *
	 * @param array  $field fields array.
	 * @param string $value Field value.
	 * @param string $unique The unique for the ID.
	 * @return mix
	 */
	function sp_wpcp_add_element( $field = array(), $value = '', $unique = '' ) {

		$output     = '';
		$depend     = '';
		$sub        = ( isset( $field['sub'] ) ) ? 'sub-': '';
		$unique     = ( isset( $unique ) ) ? $unique : '';
		$languages  = sp_language_defaults();
		$class      = 'SP_WPCP_Framework_Option_' . $field['type'];
		$wrap_class = ( isset( $field['wrap_class'] ) ) ? ' ' . $field['wrap_class'] : '';
		$hidden     = ( isset( $field['show_only_language'] ) && ( $field['show_only_language'] != $languages['current'] ) ) ? ' hidden' : '';
		$is_pseudo  = ( isset( $field['pseudo'] ) ) ? ' sp-pseudo-field' : '';
		$is_pro     = ( isset( $field['is_pro'] ) ) ? ' disabled' : '';

		if ( isset( $field['dependency'] ) ) {
			$hidden  = ' hidden';
			$depend .= ' data-' . $sub . 'controller="' . $field['dependency'][0] . '"';
			$depend .= ' data-' . $sub . 'condition="' . $field['dependency'][1] . '"';
			$depend .= ' data-' . $sub . 'value="' . $field['dependency'][2] . '"';
		}

		$output .= '<div class="sp-element sp-field-' . $field['type'] . $is_pseudo . $wrap_class . $hidden . '"' . $depend . '>';

		if ( isset( $field['title'] ) ) {
			$field_desc = ( isset( $field['desc'] ) ) ? '<p class="sp-text-desc">' . $field['desc'] . '</p>' : '';
			$output .= '<div class="sp-title"><h4>' . $field['title'] . '</h4>' . $field_desc . '</div>';
		}

		$output .= ( isset( $field['title'] ) ) ? '<div class="sp-fieldset">' : '';
		$value   = ( ! isset( $value ) && isset( $field['default'] ) ) ? $field['default'] : $value;
		$value   = ( isset( $field['value'] ) ) ? $field['value'] : $value;

		if ( class_exists( $class ) ) {
			ob_start();
			$element = new $class( $field, $value, $unique );
			$element->output();
			$output .= ob_get_clean();
		} else {
			$output .= '<p>' . __( 'This field class is not available!', 'wp-carousel-free' ) . '</p>';
		}

		$output .= ( isset( $field['title'] ) ) ? '</div>' : '';
		$output .= '<div class="clear"></div>';
		$output .= '</div>';

		return $output;

	}
}

if ( ! function_exists( 'sp_encode_string' ) ) {
	/**
	 * Encode string for backup options
	 *
	 * @param mix $string All the setting options.
	 * @since 1.0.0
	 * @return mix
	 */
	function sp_encode_string( $string ) {
		return serialize( $string );
	}
}

if ( ! function_exists( 'sp_decode_string' ) ) {
	/**
	 * Decode string for backup options
	 *
	 * @param mix $string all the setting in string.
	 * @since 1.0.0
	 * @return mix
	 */
	function sp_decode_string( $string ) {
		return unserialize( $string );
	}
}

if ( ! function_exists( 'sp_wpcp_get_google_fonts' ) ) {
	/**
	 * Get google font from json file.
	 *
	 * @since 1.0.0
	 * @return mix
	 */
	function sp_wpcp_get_google_fonts() {

		global $sp_google_fonts;

		if ( ! empty( $sp_google_fonts ) ) {

			return $sp_google_fonts;

		} else {

			ob_start();
			sp_wpcp_locate_template( 'fields/typography_advanced/google-fonts.json' );
			$json = ob_get_clean();

			$sp_google_fonts = json_decode( $json );

			return $sp_google_fonts;
		}

	}
}

if ( ! function_exists( 'sp_get_icon_fonts' ) ) {
	/**
	 * Get icon fonts from json file
	 *
	 * @since 1.0.0
	 * @param string $file The icon name.
	 * @return statement
	 */
	function sp_get_icon_fonts( $file ) {

		ob_start();
		sp_wpcp_locate_template( $file );
		$json = ob_get_clean();

		return json_decode( $json );

	}
}

if ( ! function_exists( 'sp_array_search' ) ) {
	/**
	 * Array search key & value
	 *
	 * @since 1.0.0
	 * @param array  $array The search array.
	 * @param string $key The array key.
	 * @param mix    $value	The array value.
	 * @return mix
	 */
	function sp_array_search( $array, $key, $value ) {

		$results = array();

		if ( is_array( $array ) ) {
			if ( isset( $array[ $key ] ) && $array[ $key ] == $value ) {
				$results[] = $array;
			}

			foreach ( $array as $sub_array ) {
				$results = array_merge( $results, sp_array_search( $sub_array, $key, $value ) );
			}
		}

		return $results;

	}
}

if ( ! function_exists( 'sp_get_var' ) ) {
	/**
	 * Getting POST Var
	 *
	 * @since 1.0.0
	 * @param variable $var The post var.
	 * @param string   $default The post value.
	 * @return statement
	 */
	function sp_get_var( $var, $default = '' ) {

		if ( isset( $_POST[ $var ] ) ) {
			return $_POST[ $var ];
		}

		if ( isset( $_GET[ $var ] ) ) {
			return $_GET[ $var ];
		}

		return $default;

	}
}

if ( ! function_exists( 'sp_get_vars' ) ) {
	/**
	 *  Getting POST Vars
	 *
	 * @since 1.0.0
	 * @param variable $var The post variable.
	 * @param string   $depth the depth of the post.
	 * @param string   $default the default value.
	 * @return statement
	 */
	function sp_get_vars( $var, $depth, $default = '' ) {

		if ( isset( $_POST[ $var ][ $depth ] ) ) {
			return $_POST[ $var ][ $depth ];
		}

		if ( isset( $_GET[ $var ][ $depth ] ) ) {
			return $_GET[ $var ][ $depth ];
		}

		return $default;

	}
}

if ( ! function_exists( 'sp_wpcp_load_option_fields' ) ) {
	/**
	 * Load options fields
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function sp_wpcp_load_option_fields() {

		$located_fields = array();

		foreach ( glob( SP_WPCP_F_DIR . '/fields/*/*.php' ) as $sp_field ) {
			$located_fields[] = basename( $sp_field );
			sp_wpcp_locate_template( str_replace(  SP_WPCP_F_DIR, '', $sp_field ) );
		}

		$override_name = apply_filters( 'sp_wpcp_framework_override', 'sp-framework-override' );
		$override_dir  = get_template_directory() . '/' . $override_name . '/fields';

		if ( is_dir( $override_dir ) ) {

			foreach ( glob( $override_dir . '/*/*.php' ) as $override_field ) {

				if ( ! in_array( basename( $override_field ), $located_fields ) ) {

					sp_wpcp_locate_template( str_replace( $override_dir, '/fields', $override_field ) );

				}
			}
		}

		do_action( 'sp_wpcp_load_option_fields' );

	}
}
