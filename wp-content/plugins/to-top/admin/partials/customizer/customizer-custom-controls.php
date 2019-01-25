<?php
/**
* @package Catch Plugins
* @subpackage To Top
* @since To Top 1.0
*/


//Custom control for icons
class To_Top_Custom_Icons extends WP_Customize_Control {

	private $icon_options = array(
		'dashicons-arrow-up'      => 'Arrow Up',
		'dashicons-arrow-up-alt'  => 'Arrow Up Alt',
		'dashicons-arrow-up-alt2' => 'Arrow Up Alt 2',
		);

	public function render_content() {
		$output  = '';
		$output .= '<label><span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
		$output .= '<select ' . esc_url( $this->get_link() ) . ' id="to-top-customizer-icon-type">';
		foreach ( $this->icon_options as $key => $value ) {
			$output .=	'<option class="dashicons ' . $key . '" value="' . $key . '">' . $value . '</option>';
		}
		$output .= '</select>';
		$output .= '</label>';

		echo $output;
	}
}