<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 *
 * Field: Typography Advanced
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_typography_advanced extends SP_WPCP_Framework_Options {

	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	public function output() {

		echo $this->element_before();

		$defaults_value = array();

		$default_variants = apply_filters(
			'sp_websafe_fonts_variants', array(
				'regular',
			)
		);

		$websafe_fonts = apply_filters(
			'sp_websafe_fonts', array(
				'Open Sans',
			)
		);

		$value         = wp_parse_args( $this->element_value(), $defaults_value );
		$family_value  = $value['family'];
		$variant_value = $value['variant'];
		$is_variant    = ( isset( $this->field['variant'] ) && $this->field['variant'] === false ) ? false : true;
		$google_json   = sp_wpcp_get_google_fonts();

		// Container.
		echo '<div class="sp_wpcp_font_field" data-id="' . $this->field['id'] . '">';

		if ( is_object( $google_json ) ) {

			$googlefonts = array();

			foreach ( $google_json->items as $key => $font ) {
				$googlefonts[ $font->family ] = $font->variants;
			}

			$is_google = ( array_key_exists( $family_value, $googlefonts ) ) ? true : false;

			echo '<div class="sp-element sp-typography-family sp-wpcp-select-wrapper">Font Family<br>';
			echo '<select disabled name="' . $this->element_name( '[family]' ) . '" class="sp-wpcp-select-css sp-typo-family" data-atts="family"' . $this->element_attributes() . '>';

			do_action( 'sp_typography_family', $family_value, $this );

			echo '<optgroup label="' . __( 'Web Safe Fonts', 'wp-carousel-free' ) . '">';
			foreach ( $websafe_fonts as $websafe_value ) {
				echo '<option value="' . $websafe_value . '" data-variants="' . implode( '|', $default_variants ) . '" data-type="websafe"' . selected( $websafe_value, $family_value, true ) . '>' . $websafe_value . '</option>';
			}
			echo '</optgroup>';

			echo '<optgroup label="' . __( 'Google Fonts', 'wp-carousel-free' ) . '">';
			foreach ( $googlefonts as $google_key => $google_value ) {

				echo '<option disabled value="' . $google_key . '" data-variants="' . implode( '|', $google_value ) . '" data-type="google" ' . selected( $google_key, $family_value, true ) . '>' . $google_key . '</option>';
			}
			echo '</optgroup>';

			echo '</select>';
			echo '</div>';

			if ( ! empty( $is_variant ) ) {

				$variants = ( $is_google ) ? $googlefonts[ $family_value ] : $default_variants;
				$variants = ( $value['font'] === 'google' || $value['font'] === 'websafe' ) ? $variants : 'regular';
				echo '<div class="sp-element sp-typography-variant sp-wpcp-select-wrapper">Font Weight<br>';
				echo '<select disabled name="' . $this->element_name( '[variant]' ) . '" class="sp-wpcp-select-css sp-typo-variant" data-atts="variant">';
				foreach ( $variants as $variant ) {
					echo '<option value="' . $variant . '" ' . $this->checked( $variant_value, $variant, 'selected' ) . '>' . $variant . '</option>';
				}
				echo '</select>';
				echo '</div>';

			}

			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'number',
					'value'      => $value['height'],
					'wrap_class' => 'small-input sp-font-size',
					'before'     => 'Font Size<br>',
					'pro_only'   => true,
					'attributes' => array(
						'title' => __( 'Font Size', 'wp-carousel-free' ),
					),
				)
			);

			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'number',
					'value'      => $value['height'],
					'wrap_class' => 'small-input sp-font-height',
					'before'     => 'Line Height<br>',
					'pro_only'   => true,
					'attributes' => array(
						'title' => __( 'Line Height', 'wp-carousel-free' ),
					),
				)
			);
			echo '<div class="sp-divider"></div>';
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'select_typo',
					'wrap_class' => 'small-input sp-font-alignment sp-wpcp-select-wrapper',
					'class'      => 'sp-wpcp-select-css',
					'before'     => 'Alignment<br>',
					'pro_only'   => true,
					'options'    => array(
						'inherit' => __( 'Inherit', 'wp-carousel-free' ),
					),

				)
			);
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'select_typo',
					'wrap_class' => 'small-input sp-font-transform sp-wpcp-select-wrapper',
					'class'      => 'sp-wpcp-select-css',
					'before'     => 'Transform<br>',
					'pro_only'   => true,
					'options'    => array(
						'none' => __( 'None', 'wp-carousel-free' ),
					),
				)
			);
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'select_typo',
					'wrap_class' => 'small-input sp-font-spacing sp-wpcp-select-wrapper',
					'class'      => 'sp-wpcp-select-css',
					'before'     => 'Letter Spacing<br>',
					'pro_only'   => true,
					'options'    => array(
						'normal' => __( 'Normal', 'wp-carousel-free' ),
					),
				)
			);
			echo '<div class="sp-divider"></div>';
			if ( isset( $this->field['color'] ) && $this->field['color'] == true ) {
				echo '<div class="sp-element sp-typography-color">' . __( 'Color', 'wp-carousel-free' ) . '<br>';
				echo sp_wpcp_add_element(
					array(
						'pseudo'   => true,
						'id'       => $this->field['id'] . '_color',
						'type'     => 'color_picker',
						'pro_only' => true,
						'rgba'     => ( isset( $this->field['rgba'] ) && $this->field['rgba'] === false ) ? false : '',
					)
				);
				echo '</div>';
			}

			/**
			 * Font Preview
			 */
			if ( isset( $this->field['preview'] ) && $this->field['preview'] == true ) {
				if ( isset( $this->field['preview_text'] ) ) {
					$preview_text = $this->field['preview_text'];
				} else {
					$preview_text = 'Lorem ipsum dolor sit amet, pro ad sanctus admodum, vim at insolens appellantur. Eum veri adipiscing an, probo nonumy an vis.';
				}
				echo '<div id="preview-' . $this->field['id'] . '" class="sp-font-preview">' . $preview_text . '</div>';
			}
		} else {

			echo __( 'Error! Can not load json file.', 'wp-carousel-free' );

		}

		// end container.
		echo '</div>';

		echo $this->element_after();

	}

}
