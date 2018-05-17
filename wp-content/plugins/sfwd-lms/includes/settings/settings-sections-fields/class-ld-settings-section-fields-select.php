<?php
if ( ( class_exists( 'LearnDash_Settings_Section_Fields' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Fields_Select' ) ) ) {
	class LearnDash_Settings_Section_Fields_Select extends LearnDash_Settings_Section_Fields {
		
		function __construct() {
			$this->field_type	= 'select';

			parent::__construct(); 
		}

		function create_section_field( $field_args = array() ) {

			if ( ( isset( $field_args['options'] ) ) && ( !empty( $field_args['options'] ) ) ) {
				$html = '';
				
				$html  .= '<select ';
		
				$html .= $this->get_field_attribute_type( $field_args );
				$html .= $this->get_field_attribute_name( $field_args );
				$html .= $this->get_field_attribute_id( $field_args );
				$html .= $this->get_field_attribute_class( $field_args );
				$html .= $this->get_field_attribute_misc( $field_args );
				$html .= $this->get_field_attribute_required( $field_args );
		
				$html .= '" ';
				$html .= ' >';

				foreach( $field_args['options'] as $option_key => $option_label ) {
					$html .= '<option value="'. $option_key .'" '. selected( $option_key, $field_args['value'], false ) .'>'. $option_label .'</option>';
				}
				$html .= '</select>';
			} 
		
			echo $html;
		}
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_Select::add_field_instance('select');
} );
