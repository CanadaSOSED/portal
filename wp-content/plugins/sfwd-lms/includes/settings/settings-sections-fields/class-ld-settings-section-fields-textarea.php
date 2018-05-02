<?php
if ( ( class_exists( 'LearnDash_Settings_Section_Fields' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Fields_Textarea' ) ) ) {
	class LearnDash_Settings_Section_Fields_Textarea extends LearnDash_Settings_Section_Fields {
		
		function __construct() {
			$this->field_type	= 'textarea';

			parent::__construct(); 
		}

		function create_section_field( $field_args = array() ) {
		
			$html  = '<textarea ';
			
			$html .= $this->get_field_attribute_name( $field_args );
			$html .= $this->get_field_attribute_id( $field_args );
			$html .= $this->get_field_attribute_class( $field_args );
			$html .= $this->get_field_attribute_placeholder( $field_args );
			$html .= $this->get_field_attribute_required( $field_args );
			$html .= $this->get_field_attribute_misc( $field_args );
					
			$html .= ' >';

			if ( isset( $field_args['value'] ) )
				$html .= $field_args['value'];

			$html  .= '</textarea>';
		
			echo $html;
		}
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_Textarea::add_field_instance('textarea');
} );
