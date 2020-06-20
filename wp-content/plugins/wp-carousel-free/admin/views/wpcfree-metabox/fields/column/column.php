<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: column
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_WPCF_Field_column' ) ) {

	/**
	 * The column field class.
	 */
	class SP_WPCF_Field_column extends SP_WPCF_Fields {

		/**
		 * Column field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render column function.
		 *
		 * @return void
		 */
		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'lg_desktop_icon'        => '<i class="fa fa-television"></i>',
					'desktop_icon'           => '<i class="fa fa-desktop"></i>',
					'laptop_icon'            => '<i class="fa fa-laptop"></i>',
					'tablet_icon'            => '<i class="fa fa-tablet"></i>',
					'mobile_icon'            => '<i class="fa fa-mobile"></i>',
					'all_text'               => '<i class="fa fa-arrows"></i>',
					'lg_desktop_placeholder' => esc_html__( 'Large Desktop', 'wp-carousel-free' ),
					'desktop_placeholder'    => esc_html__( 'Desktop', 'wp-carousel-free' ),
					'laptop_placeholder'     => esc_html__( 'Small Desktop', 'wp-carousel-free' ),
					'tablet_placeholder'     => esc_html__( 'Tablet', 'wp-carousel-free' ),
					'mobile_placeholder'     => esc_html__( 'Mobile', 'wp-carousel-free' ),
					'all_placeholder'        => esc_html__( 'all', 'wp-carousel-free' ),
					'lg_desktop'             => true,
					'desktop'                => true,
					'laptop'                 => true,
					'tablet'                 => true,
					'mobile'                 => true,
					'min'                    => '0',
					'unit'                   => false,
					'all'                    => false,
					'units'                  => array( 'px', '%', 'em' ),
				)
			);

			$default_values = array(
				'lg_desktop' => '5',
				'desktop'    => '4',
				'laptop'     => '3',
				'tablet'     => '2',
				'mobile'     => '1',
				'min'        => '',
				'all'        => '',
				'unit'       => 'px',
			);

			$value = wp_parse_args( $this->value, $default_values );

			echo $this->field_before();

			$min = ( isset( $args['min'] ) ) ? ' min="' . $args['min'] . '"' : '';
			if ( ! empty( $args['all'] ) ) {

				$placeholder = ( ! empty( $args['all_placeholder'] ) ) ? ' placeholder="' . $args['all_placeholder'] . '"' : '';

				echo '<div class="spf--input">';
				echo ( ! empty( $args['all_text'] ) ) ? '<span class="spf--label spf--label-icon">' . $args['all_text'] . '</span>' : '';
				echo '<input type="number" name="' . $this->field_name( '[all]' ) . '" value="' . $value['all'] . '"' . $placeholder . $min . ' class="spf-number" />';
				echo ( count( $args['units'] ) === 1 && ! empty( $args['unit'] ) ) ? '<span class="spf--label spf--label-unit">' . $args['units'][0] . '</span>' : '';
				echo '</div>';

			} else {

				$properties = array();

				foreach ( array( 'lg_desktop', 'desktop', 'laptop', 'tablet', 'mobile' ) as $prop ) {
					if ( ! empty( $args[ $prop ] ) ) {
						$properties[] = $prop;
					}
				}

				$properties = ( $properties === array( 'laptop', 'mobile' ) ) ? array_reverse( $properties ) : $properties;

				foreach ( $properties as $property ) {

					$placeholder = ( ! empty( $args[ $property . '_placeholder' ] ) ) ? ' placeholder="' . $args[ $property . '_placeholder' ] . '"' : '';

					echo '<div class="spf--input">';
					echo ( ! empty( $args[ $property . '_icon' ] ) ) ? '<span class="spf--label spf--label-icon">' . $args[ $property . '_icon' ] . '</span>' : '';
					echo '<input type="number" name="' . $this->field_name( '[' . $property . ']' ) . '" value="' . $value[ $property ] . '"' . $placeholder . $min . ' class="spf-number" />';
					echo ( count( $args['units'] ) === 1 && ! empty( $args['unit'] ) ) ? '<span class="spf--label spf--label-unit">' . $args['units'][0] . '</span>' : '';
					echo '</div>';

				}
			}

			if ( ! empty( $args['unit'] ) && count( $args['units'] ) > 1 ) {
				echo '<select name="' . $this->field_name( '[unit]' ) . '">';
				foreach ( $args['units'] as $unit ) {
					$selected = ( $value['unit'] === $unit ) ? ' selected' : '';
					echo '<option value="' . $unit . '"' . $selected . '>' . $unit . '</option>';
				}
				echo '</select>';
			}

			echo '<div class="clear"></div>';

			echo $this->field_after();

		}

		/**
		 * The output function.
		 *
		 * @return void
		 */
		public function output() {

			$output    = '';
			$element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$unit      = ( ! empty( $this->value['unit'] ) ) ? $this->value['unit'] : 'px';

			$mode = ( ! empty( $this->field['output_mode'] ) ) ? $this->field['output_mode'] : 'padding';
			$mode = ( $mode === 'relative' || $mode === 'absolute' || $mode === 'none' ) ? '' : $mode;
			$mode = ( ! empty( $mode ) ) ? $mode . '-' : '';

			if ( ! empty( $this->field['all'] ) && isset( $this->value['all'] ) && $this->value['all'] !== '' ) {

				$output  = $element . '{';
				$output .= $mode . 'lg_desktop:' . $this->value['all'] . $unit . $important . ';';
				$output .= $mode . 'desktop:' . $this->value['all'] . $unit . $important . ';';
				$output .= $mode . 'laptop:' . $this->value['all'] . $unit . $important . ';';
				$output .= $mode . 'tablet:' . $this->value['all'] . $unit . $important . ';';
				$output .= $mode . 'mobile:' . $this->value['all'] . $unit . $important . ';';
				$output .= '}';

			} else {

				$lg_desktop = ( isset( $this->value['lg_desktop'] ) && $this->value['lg_desktop'] !== '' ) ? $mode . 'lg_desktop:' . $this->value['lg_desktop'] . $unit . $important . ';' : '';
				$desktop    = ( isset( $this->value['desktop'] ) && $this->value['desktop'] !== '' ) ? $mode . 'desktop:' . $this->value['desktop'] . $unit . $important . ';' : '';
				$laptop     = ( isset( $this->value['laptop'] ) && $this->value['laptop'] !== '' ) ? $mode . 'laptop:' . $this->value['laptop'] . $unit . $important . ';' : '';
				$tablet     = ( isset( $this->value['tablet'] ) && $this->value['tablet'] !== '' ) ? $mode . 'tablet:' . $this->value['tablet'] . $unit . $important . ';' : '';
				$mobile     = ( isset( $this->value['mobile'] ) && $this->value['mobile'] !== '' ) ? $mode . 'mobile:' . $this->value['mobile'] . $unit . $important . ';' : '';

				if ( $lg_desktop !== '' || $desktop !== '' || $laptop !== '' || $tablet !== '' || $mobile !== '' ) {
					$output = $element . '{' . $lg_desktop . $desktop . $laptop . $tablet . $mobile . '}';
				}
			}

			$this->parent->output_css .= $output;

			return $output;

		}

	}
}
