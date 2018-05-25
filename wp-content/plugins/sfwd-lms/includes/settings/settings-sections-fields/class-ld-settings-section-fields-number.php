<?php
if ( ( class_exists( 'LearnDash_Settings_Section_Fields' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Fields_Number' ) ) ) {
	class LearnDash_Settings_Section_Fields_Number extends LearnDash_Settings_Section_Fields {
		
		function __construct() {
			$this->field_type	= 'number';

			parent::__construct(); 
		}

		function create_section_field( $field_args = array() ) {
		
			$html  = '<input ';
			
			$html .= $this->get_field_attribute_type( $field_args );
			$html .= $this->get_field_attribute_name( $field_args );
			$html .= $this->get_field_attribute_id( $field_args );
			$html .= $this->get_field_attribute_class( $field_args );
			$html .= $this->get_field_attribute_placeholder( $field_args );
			$html .= $this->get_field_attribute_misc( $field_args );
			$html .= $this->get_field_attribute_required( $field_args );

			if ( isset( $field_args['value'] ) )
				$html .= ' value="'. $field_args['value'] .'" ';
			else
				$html .= ' value="" ';
					
			$html .= ' />';
		
			echo $html;
		}

		function validate_section_field( $val, $key, $args = array() ) {
			return intval( $val );
		}
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_Number::add_field_instance('number');
} );
