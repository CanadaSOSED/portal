<?php
if ( ( class_exists( 'LearnDash_Settings_Section_Fields' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Fields_Html' ) ) ) {
	class LearnDash_Settings_Section_Fields_Html extends LearnDash_Settings_Section_Fields {
		
		function __construct() {
			$this->field_type	= 'html';

			parent::__construct(); 
		}

		function create_section_field( $field_args = array() ) {
		
			$field_type = apply_filters('learndash_settings_field_element_html', 'div' );
			$html  = '<'. $field_type .' ';
			
			//$html .= $this->get_field_attribute_type( $field_args );
			//$html .= $this->get_field_attribute_name( $field_args );
			$html .= $this->get_field_attribute_id( $field_args );
			$html .= $this->get_field_attribute_class( $field_args );
			//$html .= $this->get_field_attribute_placeholder( $field_args );
			$html .= $this->get_field_attribute_misc( $field_args );
			//$html .= $this->get_field_attribute_required( $field_args );
			$html .= '>';
			
			if ( isset( $field_args['value'] ) )
				$html .= wptexturize( do_shortcode( $field_args['value'] ) );
					
					' value="'. $field_args['value'] .'" ';

			$html .= '</'. $field_type .'>';
		
			echo $html;
		}
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_Html::add_field_instance('html');
} );
