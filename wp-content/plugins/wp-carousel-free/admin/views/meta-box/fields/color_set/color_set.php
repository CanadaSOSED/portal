<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 *
 * Field: Color Set
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class SP_WPCP_Framework_Option_color_set extends SP_WPCP_Framework_Options {
	/**
	 * The field constructor.
	 */
	public function __construct( $field, $value = '', $unique = '' ) {
		parent::__construct( $field, $value, $unique );
	}

	/**
	 * Output
	 *
	 * @return void
	 */
	public function output() {

		echo $this->element_before();

		$defaults_value = array(
			'color1' => '',
			'color2' => '',
			'color3' => '',
			'color4' => '',
			'color5' => '',
			'color6' => '',
			'title1' => '',
			'title2' => '',
			'title3' => '',
			'title4' => '',
			'title5' => '',
			'title6' => '',
		);

		$value = wp_parse_args( $this->element_value(), $defaults_value );
		$title = wp_parse_args( $this->field['default'], $defaults_value );

		// Container.
		echo '<div class="sp_wpcp_color_set_field" data-id="' . $this->field['id'] . '">';

		if ( isset( $this->field['color1'] ) && $this->field['color1'] == true ) {
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[color1]' ),
					'value'      => $value['color1'],
					'default'    => ( isset( $this->field['default']['color1'] ) ) ? $this->field['default']['color1'] : '',
					'wrap_class' => 'sp-color-set',
					'before'     => $title['title1'] . '<br>',
				)
			);
		}
		if ( isset( $this->field['color2'] ) && $this->field['color2'] == true ) {
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[color2]' ),
					'value'      => $value['color2'],
					'default'    => ( isset( $this->field['default']['color2'] ) ) ? $this->field['default']['color2'] : '',
					'wrap_class' => 'sp-color-set',
					'before'     => $title['title2'] . '<br>',
				)
			);
		}
		if ( isset( $this->field['color3'] ) && $this->field['color3'] == true ) {
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[color3]' ),
					'value'      => $value['color3'],
					'default'    => ( isset( $this->field['default']['color3'] ) ) ? $this->field['default']['color3'] : '',
					'wrap_class' => 'sp-color-set',
					'before'     => $title['title3'] . '<br>',
				)
			);
		}
		if ( isset( $this->field['color4'] ) && $this->field['color4'] == true ) {
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[color4]' ),
					'value'      => $value['color4'],
					'default'    => ( isset( $this->field['default']['color4'] ) ) ? $this->field['default']['color4'] : '',
					'wrap_class' => 'sp-color-set',
					'before'     => $title['title4'] . '<br>',
				)
			);
		}
		if ( isset( $this->field['color5'] ) && $this->field['color5'] == true ) {
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[color5]' ),
					'value'      => $value['color5'],
					'default'    => ( isset( $this->field['default']['color5'] ) ) ? $this->field['default']['color5'] : '',
					'wrap_class' => 'sp-color-set',
					'before'     => $title['title5'] . '<br>',
				)
			);
		}
		if ( isset( $this->field['color6'] ) && $this->field['color6'] == true ) {
			echo sp_wpcp_add_element(
				array(
					'pseudo'     => true,
					'type'       => 'color_picker',
					'name'       => $this->element_name( '[color6]' ),
					'value'      => $value['color6'],
					'default'    => ( isset( $this->field['default']['color6'] ) ) ? $this->field['default']['color6'] : '',
					'wrap_class' => 'sp-color-set',
					'before'     => $title['title6'] . '<br>',
				)
			);
		}

		// end container.
		echo '</div>';

		echo $this->element_after();

	}

}
