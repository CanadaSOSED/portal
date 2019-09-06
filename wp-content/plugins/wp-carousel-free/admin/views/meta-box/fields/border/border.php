<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 *
 * Field: Border
 *
 * @since 1.0
 * @version 1.0
 */
class SP_WPCP_Framework_Option_border extends SP_WPCP_Framework_Options {

	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	public function output() {

		echo $this->element_before();

		$defaults_value = array(
			'width'       => '',
			'style'       => '',
			'color'       => '',
			'hover_color' => '',
		);

		$value = wp_parse_args( $this->element_value(), $defaults_value );

		// Container.
		echo '<div class="sp_wpcp_border_field" data-id="' . $this->field['id'] . '">';

			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'number',
					'name'       => $this->element_name( '[width]' ),
					'value'      => $value['width'],
					'default'    => ( isset( $this->field['default']['width'] ) ) ? $this->field['default']['width'] : '',
					'wrap_class' => 'small-input sp-border-width',
					'before'     => 'Width<br>',
					'after'      => '(px)',
					'attributes' => array(
						'min'   => 0,
						'title' => __( 'Border Width', 'logo-carousel-pro' ),
					),
				)
			);
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'select_typo',
					'name'       => $this->element_name( '[style]' ),
					'value'      => $value['style'],
					'default'    => ( isset( $this->field['default']['style'] ) ) ? $this->field['default']['style'] : '',
					'wrap_class' => 'small-input sp-border-style sp-wpcp-select-wrapper',
					'class'      => 'sp-wpcp-select-css',
					'before'     => 'Style<br>',
					'attributes' => array(
						'title' => __( 'Border Style', 'logo-carousel-pro' ),
					),
					'options'    => array(
						'none'   => __( 'None', 'logo-carousel-pro' ),
						'solid'  => __( 'Solid', 'logo-carousel-pro' ),
						'dotted' => __( 'Dotted', 'logo-carousel-pro' ),
						'dashed' => __( 'Dashed', 'logo-carousel-pro' ),
						'double' => __( 'Double', 'logo-carousel-pro' ),
						'groove' => __( 'Groove', 'logo-carousel-pro' ),
						'ridge'  => __( 'Ridge', 'logo-carousel-pro' ),
						'inset'  => __( 'Inset', 'logo-carousel-pro' ),
						'outset' => __( 'Outset', 'logo-carousel-pro' ),
					),
				)
			);
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[color]' ),
					'value'      => $value['color'],
					'default'    => ( isset( $this->field['default']['color'] ) ) ? $this->field['default']['color'] : '',
					'wrap_class' => 'small-input sp-border-color',
					'before'     => 'Color<br>',
					'attributes' => array(
						'title' => __( 'Border Color', 'logo-carousel-pro' ),
					),
				)
			);
		if ( isset( $this->field['hover_color'] ) && $this->field['hover_color'] == true ) {
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[hover_color]' ),
					'value'      => $value['hover_color'],
					'default'    => ( isset( $this->field['default']['hover_color'] ) ) ? $this->field['default']['hover_color'] : '',
					'wrap_class' => 'small-input sp-border-hover-color',
					'before'     => 'Hover Color<br>',
					'attributes' => array(
						'title' => __( 'Border Hover Color', 'logo-carousel-pro' ),
					),
				)
			);
		}

		// end container.
		echo '</div>';

		echo $this->element_after();

	}

}
