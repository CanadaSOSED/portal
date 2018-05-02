<?php
if ( !class_exists( 'LearnDash_Shortcodes_Section' ) ) {
	
	class LearnDash_Shortcodes_Section {

		protected 	$shortcodes_section_key 		= 	'';
		protected 	$shortcodes_section_title		=	'';
		protected	$shortcodes_section_type		=	1;
		protected	$shortcodes_section_description	=	'';

		protected	$shortcodes_option_fields		=	array();
		protected 	$shortcodes_option_values 		= 	array();

		// This is derived from the $shortcodes_option_fields within the function init_shortcodes_section_fields();
		protected	$shortcodes_settings_fields		=	array();

		// This is the HTML form field prefix used. 
		protected 	$setting_field_prefix			= 	'';

		protected	$fields_args					=	array();
		
		function __construct() {
			$this->init_shortcodes_section_fields();
		}	

		function init_shortcodes_section_fields() {
			foreach( $this->shortcodes_option_fields as $field_id => $setting_option_field ) {
				if ( !isset( $setting_option_field['label_for'] ) )
					$setting_option_field['label_for'] = $setting_option_field['id'];				

				if ( !isset( $setting_option_field['label_for'] ) ) {
					$setting_option_field['label_for'] = $setting_option_field['id'];
				}
				
				if ( !isset( $setting_option_field['display_callback'] ) ) {
					$display_ref = LearnDash_Settings_Section_Fields::get_field_instance( $setting_option_field['type'] );
					if ( !$display_ref ) {
						$setting_option_field['display_callback'] = array( $this, 'field_element_create' );
					} else {
						$setting_option_field['display_callback'] = array($display_ref, 'create_section_field' );
					}
				}

				$this->shortcodes_settings_fields[$field_id] = array(
					'id'				=>	$setting_option_field['id'],
					'title'				=>	$setting_option_field['label'],
					'callback'			=>	$setting_option_field['display_callback'],
					'args'				=>	$setting_option_field
				);
			}				
		}

		function field_element_create( $field_args = array() ) {
			$field_html = '';
		
			if ( ( isset( $field_args['display_func'] ) ) && ( !empty( $field_args['display_func'] ) ) && ( is_callable( $field_args['display_func'] ) ) ) {
				call_user_func( 
					$field_args['display_func'], 
					$field_args, 
					$this->setting_field_prefix 
				);
			} 
		}

		function show_section_fields() {
			$this->show_shortcodes_section_header();
			echo LearnDash_Settings_Section_Fields::show_section_fields( $this->shortcodes_settings_fields );
			$this->show_shortcodes_section_footer();
		}

		function show_shortcodes_section_header() {
			?><form id="learndash_shortcodes_form_<?php echo $this->shortcodes_section_key ?>" class="learndash_shortcodes_form" shortcode_slug="<?php echo $this->shortcodes_section_key ?>" shortcode_type="<?php echo $this->shortcodes_section_type ?>">
				<?php $this->show_shortcodes_section_title(); ?>
				<?php $this->show_shortcodes_section_description(); ?>
				<div class="sfwd sfwd_options learndash_shortcodes_section" style="clear:left">
				<?php
		}

		function show_shortcodes_section_footer() {
			?>
				</div>
				<?php $this->show_shortcodes_section_footer_extra(); ?>
				<p style="clear:left"><input type="submit" class="button-primary" value="<?php esc_html_e( 'Insert Shortcode', 'learndash' ); ?>"></p>
			</form>
			<?php
		}
		
		function show_shortcodes_section_footer_extra() {
			// This is a hook called after the section closing </div> to allow adding JS/CSS
		}
		
		function get_shortcodes_section_key() {
			return $this->shortcodes_section_key;
		}
		
		
		function get_shortcodes_section_title() {
			return $this->shortcodes_section_title;
		}
		
		function show_shortcodes_section_title() {
			if (!empty( $this->shortcodes_section_title ) ) {
				?><h2><?php echo $this->shortcodes_section_title ?> [<?php echo $this->shortcodes_section_key ?>]</h2><?php
			}
		}

		function get_shortcodes_section_description() {
			return $this->shortcodes_section_description;
		}

		function show_shortcodes_section_description() {
			if (!empty( $this->shortcodes_section_description ) ) {
				echo wpautop( $this->shortcodes_section_description );
			}
		}
	}
}
