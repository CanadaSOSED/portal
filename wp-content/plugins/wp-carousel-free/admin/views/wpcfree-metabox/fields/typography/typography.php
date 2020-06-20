<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: typography
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_WPCF_Field_typography' ) ) {
	class SP_WPCF_Field_typography extends SP_WPCF_Fields {

		public $chosen = false;

		public $value = array();

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			echo $this->field_before();

			$args = wp_parse_args(
				$this->field,
				array(
					'font_family'        => true,
					'font_weight'        => true,
					'font_style'         => true,
					'font_size'          => true,
					'line_height'        => true,
					'letter_spacing'     => true,
					'text_align'         => true,
					'text_transform'     => true,
					'color'              => true,
					'hover_color'        => false,
					'chosen'             => true,
					'preview'            => true,
					'subset'             => true,
					'multi_subset'       => false,
					'extra_styles'       => false,
					'backup_font_family' => false,
					'font_variant'       => false,
					'word_spacing'       => false,
					'text_decoration'    => false,
					'custom_style'       => false,
					'exclude'            => '',
					'unit'               => 'px',
					'preview_text'       => 'The quick brown fox jumps over the lazy dog',
				)
			);

			$default_value = array(
				'font-family'        => '',
				'font-weight'        => '',
				'font-style'         => '',
				'font-variant'       => '',
				'font-size'          => '',
				'line-height'        => '',
				'letter-spacing'     => '',
				'word-spacing'       => '',
				'text-align'         => '',
				'text-transform'     => '',
				'text-decoration'    => '',
				'backup-font-family' => '',
				'color'              => '',
				'hover_color'        => '',
				'custom-style'       => '',
				'type'               => '',
				'subset'             => '',
				'extra-styles'       => array(),
			);

			$default_value = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;
			$this->value   = wp_parse_args( $this->value, $default_value );
			$this->chosen  = $args['chosen'];
			$chosen_class  = ( $this->chosen ) ? ' spf--chosen' : '';

			echo '<div class="spf--typography' . $chosen_class . '" data-unit="' . $args['unit'] . '" data-exclude="' . $args['exclude'] . '">';

			echo '<div class="spf--blocks spf--blocks-selects">';

			//
			// Font Family.
			if ( ! empty( $args['font_family'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Font Family', 'wp-carousel-free' ) . '</div>';
				echo '<select disabled name="sp_wpcp_shortcode_options[wpcp_section_title_typography][font-family]" class="spf--font-family" data-placeholder="Select a font" style="display: none;">
				<option>Open Sans</option>
				</select>';
				echo '</div>';
			}

			//
			// Backup Font Family.
			if ( ! empty( $args['backup_font_family'] ) ) {
				echo '<div class="spf--block spf--block-backup-font-family hidden">';
				echo '<div class="spf--title">' . esc_html__( 'Backup Font Family', 'wp-carousel-free' ) . '</div>';
				echo $this->create_select(
					apply_filters(
						'spf_field_typography_backup_font_family',
						array(
							'Arial, Helvetica, sans-serif',
							"'Arial Black', Gadget, sans-serif",
							"'Comic Sans MS', cursive, sans-serif",
							'Impact, Charcoal, sans-serif',
							"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
							'Tahoma, Geneva, sans-serif',
							"'Trebuchet MS', Helvetica, sans-serif'",
							'Verdana, Geneva, sans-serif',
							"'Courier New', Courier, monospace",
							"'Lucida Console', Monaco, monospace",
							'Georgia, serif',
							'Palatino Linotype',
						)
					),
					'backup-font-family',
					esc_html__( 'Default', 'wp-carousel-free' )
				);
				echo '</div>';
			}

			//
			// Font Style and Extra Style Select.
			if ( ! empty( $args['font_weight'] ) || ! empty( $args['font_style'] ) ) {

				//
				// Font Style Select.
				echo '<div class="spf--block spf--block-font-style hidden">';
				echo '<div class="spf--title">' . esc_html__( 'Font Style', 'wp-carousel-free' ) . '</div>';
				echo '<select disabled class="spf--font-style-select" data-placeholder="Default">';
				echo '<option disabled value="">' . ( ! $this->chosen ? esc_html__( 'Default', 'wp-carousel-free' ) : '' ) . '</option>';
				if ( ! empty( $this->value['font-weight'] ) || ! empty( $this->value['font-style'] ) ) {
					echo '<option disabled value="' . strtolower( $this->value['font-weight'] . $this->value['font-style'] ) . '" selected></option>';
				}
				echo '</select>';
				echo '<input disabled type="hidden" name="' . $this->field_name( '[font-weight]' ) . '" class="spf--font-weight" value="' . $this->value['font-weight'] . '" />';
				echo '<input disabled type="hidden" name="' . $this->field_name( '[font-style]' ) . '" class="spf--font-style" value="' . $this->value['font-style'] . '" />';

				//
				// Extra Font Style Select.
				if ( ! empty( $args['extra_styles'] ) ) {
					echo '<div class="spf--block-extra-styles hidden">';
					echo ( ! $this->chosen ) ? '<div class="spf--title">' . esc_html__( 'Load Extra Styles', 'wp-carousel-free' ) . '</div>' : '';
					$placeholder = ( $this->chosen ) ? esc_html__( 'Load Extra Styles', 'wp-carousel-free' ) : esc_html__( 'Default', 'wp-carousel-free' );
					echo $this->create_select( $this->value['extra-styles'], 'extra-styles', $placeholder, true );
					echo '</div>';
				}

				echo '</div>';

			}

			//
			// Subset.
			if ( ! empty( $args['subset'] ) ) {
				echo '<div class="spf--block spf--block-subset hidden">';
				echo '<div class="spf--title">' . esc_html__( 'Subset', 'wp-carousel-free' ) . '</div>';
				$subset = ( is_array( $this->value['subset'] ) ) ? $this->value['subset'] : array_filter( (array) $this->value['subset'] );
				echo '<select disabled name="sp_wpcp_shortcode_options[wpcp_section_title_typography][subset]" class="spf--subset" data-placeholder="Default" style="display: none;"><option value="default"></option></select>';
				echo '</div>';
			}

			//
			// Text Align.
			if ( ! empty( $args['text_align'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Text Align', 'wp-carousel-free' ) . '</div>';
				echo '<select disabled name="sp_wpcp_shortcode_options[wpcp_section_title_typography][text-align]" class="spf--text-align" data-placeholder="Center" style="display: none;"><option value="">Center</option></select>';
				echo '</div>';
			}

			//
			// Font Variant.
			if ( ! empty( $args['font_variant'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Font Variant', 'wp-carousel-free' ) . '</div>';
				echo $this->create_select(
					array(
						'normal'         => esc_html__( 'Normal', 'wp-carousel-free' ),
						'small-caps'     => esc_html__( 'Small Caps', 'wp-carousel-free' ),
						'all-small-caps' => esc_html__( 'All Small Caps', 'wp-carousel-free' ),
					),
					'font-variant',
					esc_html__( 'Default', 'wp-carousel-free' )
				);
				echo '</div>';
			}

			//
			// Text Transform.
			if ( ! empty( $args['text_transform'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Text Transform', 'wp-carousel-free' ) . '</div>';
				echo '<select disabled name="sp_wpcp_shortcode_options[wpcp_title_typography][text-align]" class="spf--text-align" data-placeholder="Default" style="display: none;"><option value="none">None</option></select>';
				echo '</div>';
			}

			//
			// Text Decoration.
			if ( ! empty( $args['text_decoration'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Text Decoration', 'wp-carousel-free' ) . '</div>';
				echo $this->create_select(
					array(
						'none'               => esc_html__( 'None', 'wp-carousel-free' ),
						'underline'          => esc_html__( 'Solid', 'wp-carousel-free' ),
						'underline double'   => esc_html__( 'Double', 'wp-carousel-free' ),
						'underline dotted'   => esc_html__( 'Dotted', 'wp-carousel-free' ),
						'underline dashed'   => esc_html__( 'Dashed', 'wp-carousel-free' ),
						'underline wavy'     => esc_html__( 'Wavy', 'wp-carousel-free' ),
						'underline overline' => esc_html__( 'Overline', 'wp-carousel-free' ),
						'line-through'       => esc_html__( 'Line-through', 'wp-carousel-free' ),
					),
					'text-decoration',
					esc_html__( 'Default', 'wp-carousel-free' )
				);
				echo '</div>';
			}

			echo '</div>'; // End of .spf--blocks-selects.

			echo '<div class="spf--blocks spf--blocks-inputs">';

			//
			// Font Size
			if ( ! empty( $args['font_size'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Font Size', 'wp-carousel-free' ) . '</div>';
				echo '<div class="spf--blocks">';
				echo '<div class="spf--block"><input disabled type="text" name="' . $this->field_name( '[font-size]' ) . '" class="spf--font-size spf--input spf-number" value="' . $this->value['font-size'] . '" /></div>';
				echo '<div class="spf--block spf--unit">' . $args['unit'] . '</div>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Line Height.
			if ( ! empty( $args['line_height'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Line Height', 'wp-carousel-free' ) . '</div>';
				echo '<div class="spf--blocks">';
				echo '<div class="spf--block"><input disabled type="text" name="' . $this->field_name( '[line-height]' ) . '" class="spf--line-height spf--input spf-number" value="' . $this->value['line-height'] . '" /></div>';
				echo '<div class="spf--block spf--unit">' . $args['unit'] . '</div>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Letter Spacing.
			if ( ! empty( $args['letter_spacing'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Letter Spacing', 'wp-carousel-free' ) . '</div>';
				echo '<div class="spf--blocks">';
				echo '<div class="spf--block"><input disabled type="text" name="' . $this->field_name( '[letter-spacing]' ) . '" class="spf--letter-spacing spf--input spf-number" value="' . $this->value['letter-spacing'] . '" /></div>';
				echo '<div class="spf--block spf--unit">' . $args['unit'] . '</div>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Word Spacing.
			if ( ! empty( $args['word_spacing'] ) ) {
				echo '<div class="spf--block">';
				echo '<div class="spf--title">' . esc_html__( 'Word Spacing', 'wp-carousel-free' ) . '</div>';
				echo '<div class="spf--blocks">';
				echo '<div class="spf--block"><input disabled type="text" name="' . $this->field_name( '[word-spacing]' ) . '" class="spf--word-spacing spf--input spf-number" value="' . $this->value['word-spacing'] . '" /></div>';
				echo '<div class="spf--block spf--unit">' . $args['unit'] . '</div>';
				echo '</div>';
				echo '</div>';
			}

			echo '</div>'; // End of spf--blocks-inputs.

			//
			// Font Color.
			if ( ! empty( $args['color'] ) ) {
				echo '<div class="spf--blocks spf--blocks-color">';
				$default_color_attr = ( ! empty( $default_value['color'] ) ) ? ' data-default-color="' . $default_value['color'] . '"' : '';
				echo '<div class="spf--block spf--block-font-color">';
				echo '<div class="spf--title">' . esc_html__( 'Font Color', 'wp-carousel-free' ) . '</div>';
				echo '<div class="spf-field-color">';
				echo '<input disabled type="text" name="' . $this->field_name( '[color]' ) . '" class="spf-color spf--color" value="' . $this->value['color'] . '"' . $default_color_attr . ' />';
				echo '</div>';
				echo '</div>';

				//
				// Font Hover Color.
				if ( ! empty( $args['hover_color'] ) ) {
					$default_hover_color_attr = ( ! empty( $default_value['hover_color'] ) ) ? ' data-default-color="' . $default_value['hover_color'] . '"' : '';
					echo '<div class="spf--block spf--block-font-color">';
					echo '<div class="spf--title">' . esc_html__( 'Font Hover Color', 'wp-carousel-free' ) . '</div>';
					echo '<div class="spf-field-color">';
					echo '<input disabled type="text" name="' . $this->field_name( '[hover_color]' ) . '" class="spf-color spf--color" value="' . $this->value['hover_color'] . '"' . $default_hover_color_attr . ' />';
					echo '</div>';
					echo '</div>';
				}
				echo '</div>'; // End of spf--blocks-color
			}

			//
			// Custom style.
			if ( ! empty( $args['custom_style'] ) ) {
				echo '<div class="spf--block spf--block-custom-style">';
				echo '<div class="spf--title">' . esc_html__( 'Custom Style', 'wp-carousel-free' ) . '</div>';
				echo '<textarea name="' . $this->field_name( '[custom-style]' ) . '" class="spf--custom-style">' . $this->value['custom-style'] . '</textarea>';
				echo '</div>';
			}

			//
			// Preview.
			$always_preview = ( $args['preview'] !== 'always' ) ? ' hidden' : '';

			if ( ! empty( $args['preview'] ) ) {
				echo '<div class="spf--block spf--block-preview' . $always_preview . '">';
				echo '<div class="spf--toggle fa fa-toggle-off"></div>';
				echo '<div class="spf--preview">' . $args['preview_text'] . '</div>';
				echo '</div>';
			}

			echo '<input type="hidden" name="' . $this->field_name( '[type]' ) . '" class="spf--type" value="' . $this->value['type'] . '" />';
			echo '<input type="hidden" name="' . $this->field_name( '[unit]' ) . '" class="spf--unit-save" value="' . $args['unit'] . '" />';

			echo '</div>';

			echo $this->field_after();

		}

		public function create_select( $options, $name, $placeholder = '', $is_multiple = false ) {

			$multiple_name = ( $is_multiple ) ? '[]' : '';
			$multiple_attr = ( $is_multiple ) ? ' multiple data-multiple="true"' : '';
			$chosen_rtl    = ( $this->chosen && is_rtl() ) ? ' chosen-rtl' : '';

			$output  = '<select name="' . $this->field_name( '[' . $name . ']' . $multiple_name ) . '" class="spf--' . $name . $chosen_rtl . '" data-placeholder="' . $placeholder . '"' . $multiple_attr . '>';
			$output .= ( ! empty( $placeholder ) ) ? '<option value="">' . ( ( ! $this->chosen ) ? $placeholder : '' ) . '</option>' : '';

			if ( ! empty( $options ) ) {
				foreach ( $options as $option_key => $option_value ) {
					if ( $is_multiple ) {
						$selected = ( in_array( $option_value, $this->value[ $name ] ) ) ? ' selected' : '';
						$output  .= '<option disabled value="' . $option_value . '"' . $selected . '>' . $option_value . '</option>';
					} else {
						$option_key = ( is_numeric( $option_key ) ) ? $option_value : $option_key;
						$selected   = ( $option_key === $this->value[ $name ] ) ? ' selected' : '';
						$output    .= '<option disabled value="' . $option_key . '"' . $selected . '>' . $option_value . '</option>';
					}
				}
			}

			$output .= '</select>';

			return $output;

		}

		public function enqueue() {

			if ( ! wp_style_is( 'spf-webfont-loader' ) ) {

				wp_enqueue_script( 'spf-webfont-loader', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array( 'spf' ), '1.6.28', true );

				$webfonts = array();

				$customwebfonts = apply_filters( 'spf_field_typography_customwebfonts', array() );

				if ( ! empty( $customwebfonts ) ) {
					$webfonts['custom'] = array(
						'label' => esc_html__( 'Custom Web Fonts', 'wp-carousel-free' ),
						'fonts' => $customwebfonts,
					);
				}

				$webfonts['safe'] = array(
					'label' => esc_html__( 'Safe Web Fonts', 'wp-carousel-free' ),
					'fonts' => apply_filters(
						'spf_field_typography_safewebfonts',
						array(
							'Arial',
							'Arial Black',
							'Helvetica',
							'Times New Roman',
							'Courier New',
							'Tahoma',
							'Verdana',
							'Impact',
							'Trebuchet MS',
							'Comic Sans MS',
							'Lucida Console',
							'Lucida Sans Unicode',
							'Georgia, serif',
							'Palatino Linotype',
						)
					),
				);

				$webfonts['google'] = array(
					'label' => esc_html__( 'Google Web Fonts', 'wp-carousel-free' ),
					'spf_field_typography_googlewebfonts',
				);

				$defaultstyles = apply_filters( 'spf_field_typography_defaultstyles', array( 'normal', 'italic', '700', '700italic' ) );

				$googlestyles = apply_filters(
					'spf_field_typography_googlestyles',
					array(
						'100'       => 'Thin 100',
						'100italic' => 'Thin 100 Italic',
						'200'       => 'Extra-Light 200',
						'200italic' => 'Extra-Light 200 Italic',
						'300'       => 'Light 300',
						'300italic' => 'Light 300 Italic',
						'normal'    => 'Normal 400',
						'italic'    => 'Normal 400 Italic',
						'500'       => 'Medium 500',
						'500italic' => 'Medium 500 Italic',
						'600'       => 'Semi-Bold 600',
						'600italic' => 'Semi-Bold 600 Italic',
						'700'       => 'Bold 700',
						'700italic' => 'Bold 700 Italic',
						'800'       => 'Extra-Bold 800',
						'800italic' => 'Extra-Bold 800 Italic',
						'900'       => 'Black 900',
						'900italic' => 'Black 900 Italic',
					)
				);

				$webfonts = apply_filters( 'spf_field_typography_webfonts', $webfonts );

				wp_localize_script(
					'spf',
					'spf_typography_json',
					array(
						'webfonts'      => $webfonts,
						'defaultstyles' => $defaultstyles,
						'googlestyles'  => $googlestyles,
					)
				);

			}

		}

		public function enqueue_google_fonts() {

			$value     = $this->value;
			$families  = array();
			$is_google = false;

			if ( ! empty( $this->value['type'] ) ) {
				$is_google = ( $this->value['type'] === 'google' ) ? true : false;
			} else {
				SP_WPCF::include_plugin_file( 'fields/typography/google-fonts.php' );
				$is_google = ( array_key_exists( $this->value['font-family'], spf_get_google_fonts() ) ) ? true : false;
			}

			if ( $is_google ) {

				// set style
				$font_weight = ( ! empty( $value['font-weight'] ) ) ? $value['font-weight'] : '';
				$font_style  = ( ! empty( $value['font-style'] ) ) ? $value['font-style'] : '';

				if ( $font_weight || $font_style ) {
					$style                       = $font_weight . $font_style;
					$families['style'][ $style ] = $style;
				}

				// set extra styles
				if ( ! empty( $value['extra-styles'] ) ) {
					foreach ( $value['extra-styles'] as $extra_style ) {
						$families['style'][ $extra_style ] = $extra_style;
					}
				}

				// set subsets
				if ( ! empty( $value['subset'] ) ) {
					$value['subset'] = ( is_array( $value['subset'] ) ) ? $value['subset'] : array_filter( (array) $value['subset'] );
					foreach ( $value['subset'] as $subset ) {
						$families['subset'][ $subset ] = $subset;
					}
				}

				$all_styles  = ( ! empty( $families['style'] ) ) ? ':' . implode( ',', $families['style'] ) : '';
				$all_subsets = ( ! empty( $families['subset'] ) ) ? ':' . implode( ',', $families['subset'] ) : '';

				$families = $this->value['font-family'] . str_replace( array( 'normal', 'italic' ), array( 'n', 'i' ), $all_styles ) . $all_subsets;

				$this->parent->typographies[] = $families;

				return $families;

			}

			return false;

		}

	}
}
