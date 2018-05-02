<?php
if ( ( class_exists( 'LearnDash_Settings_Section_Fields' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Fields_Checkbox' ) ) ) {
	class LearnDash_Settings_Section_Fields_Checkbox extends LearnDash_Settings_Section_Fields {
		
		function __construct() {
			$this->field_type	= 'checkbox';

			parent::__construct(); 
		}

		function create_section_field( $field_args = array() ) {
		
			if ( ( isset( $field_args['options'] ) ) && ( !empty( $field_args['options'] ) ) ) {
				$html = '';
				
				if ( ( isset( $field_args['desc'] ) ) && ( !empty( $field_args['desc'] ) ) ) {
					$html .= $field_args['desc'];
				}
				
				$html .= '<fieldset>';
				$html .= '<legend class="screen-reader-text">';
				$html .= '<span>'. $field_args['label'] .'</span>';
				$html .= '</legend>';
				
				foreach( $field_args['options'] as $option_key => $option_label ) {
				
					$html .= ' <label for="'. $field_args['id'] .'-'. $option_key .'" >';
					$html  .= '<input ';
			
					$html .= $this->get_field_attribute_type( $field_args );
					$html .= $this->get_field_attribute_id( $field_args );
					$html .= $this->get_field_attribute_name( $field_args );
					$html .= $this->get_field_attribute_class( $field_args );
					$html .= $this->get_field_attribute_misc( $field_args );		
					$html .= $this->get_field_attribute_required( $field_args );
					
					if ( isset( $field_args['value'] ) )
						$html .= ' value="'. $option_key .'" ';
					else
						$html .= ' value="" ';
			
					$html .= ' '. checked( $option_key, $field_args['value'], false ) .' ';
					
		
					$html .= ' />';
					
					$html .= $option_label .'</label>';
					$html .= '</br>';
				}
				$html .= '</fieldset>';
			} 
		
			echo $html;
		}
	}
}
add_action( 'learndash_settings_sections_fields_init', function() {
	LearnDash_Settings_Section_Fields_Checkbox::add_field_instance('checkbox');
} );
