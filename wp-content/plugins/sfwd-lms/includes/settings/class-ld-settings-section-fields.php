<?php
// 		if ( in_array( $options['type'], array( 'multiselect', 'select', 'multicheckbox', 'radio', 'checkbox', 'textarea', 'text', 'submit', 'hidden' ) ) ) {


if ( !class_exists( 'LearnDash_Settings_Section_Fields' ) ) {
	abstract class LearnDash_Settings_Section_Fields {

		protected static $_instances = array();

		// Define the field type 'text', 'select', etc. This is unique
		protected $field_type = '';

		function __construct() {
			
		}

		final public static function get_field_instance( $field_key = '' ) {
			if ( !empty( $field_key ) ) {
				if ( isset( self::$_instances[$field_key] ) ) {
					return self::$_instances[$field_key];
				}
			}
		}

		final public static function add_field_instance( $field_key = '' ) {
			if ( !empty( $field_key ) ) {
				if ( !isset( self::$_instances[$field_key] ) ) {
					$sectionClass = get_called_class();
					self::$_instances[$field_key] = new $sectionClass();
				}
				return self::$_instances[$field_key];
			}
		}

		// Utility function so we are not hard coding the create/validate member functions in various settings files. 
		final public function get_creation_function_ref() {
			return array( $this, 'create_section_field' );
		}
		final public function get_vaidation_function_ref() {
			return array( $this, 'validate_section_field' );
		}

		static function show_section_fields( $section_fields = array() ) {

			if ( !empty( $section_fields ) ) {

				foreach ( $section_fields as $field_id => $field ) {
					self::show_section_field_row( $field );
				}
			}
		}
		
		static function show_section_field_row( $field ) {
			$field_error_class = '';

			if ( ( isset( $field['args']['setting_option_key'] ) ) && ( !empty( $field['args']['setting_option_key'] ) ) ) {
				$settings_errors = get_settings_errors( $field['args']['setting_option_key'] );
				if ( !empty( $settings_errors ) ) {
					foreach( $settings_errors as $settings_error ) {
						if ( ( $settings_error['setting'] == $field['args']['setting_option_key'] ) && ( $settings_error['code'] == $field['args']['name'] ) && ( $settings_error['type'] == 'error' ) ) {
							$field_error_class = 'learndash-settings-field-error';
						}
					}
				}
			}
			
			$field_class = '';
			if ( ( isset( $field['args']['type'] ) ) && ( !empty( $field['args']['type'] ) ) ) {
				$field_class = 'sfwd_input_type_'. $field['args']['type'];
			} 
			
			if ( ( isset( $field['args']['desc_before'] ) ) && ( !empty( $field['args']['desc_before'] ) ) ) {
				echo wptexturize( $field['args']['desc_before'] );
			}
			
			?>
			<div id="<?php echo $field['args']['id'] ?>_field" class="sfwd_input <?php echo $field_class ?> <?php echo $field_error_class; ?>">
				<span class="sfwd_option_label">
					<a class="sfwd_help_text_link" style="cursor:pointer;" title="<?php esc_html_e( 'Click for Help!', 'learndash' ) ?>"
						onclick="toggleVisibility('<?php echo $field['args']['id'] ?>_tip');"><img 
							alt="" src="<?php echo LEARNDASH_LMS_PLUGIN_URL ?>assets/images/question.png" /><label for="<?php echo esc_attr( $field['args']['label_for'] ) ?>" class="sfwd_label"><?php echo $field['title']; ?><?php 
						if ( isset( $field['args']['required'] ) ) {
							?><span class="learndash_required_field"><abbr title="<?php esc_html_e('Required', 'learndash' ) ?>">*</abbr></span><?php
						}
						?></label>
					</a>
				</span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div">
						<?php call_user_func( $field['callback'], $field['args'] ); ?>
					</div>
					<div id="<?php echo $field['args']['id'] ?>_tip" class="sfwd_help_text_div" style="display: none;">
						<label class="sfwd_help_text"><?php echo $field['args']['help_text'] ?></label>
					</div>
				</span>
				<p class="ld-clear"></p>
			</div>
			<?php
			if ( ( isset( $field['args']['desc_after'] ) ) && ( !empty( $field['args']['desc_after'] ) ) ) {
				echo wptexturize( $field['args']['desc_after'] );
			}
			
		}

		function create_section_field( $field_args = array() ) {
			return;
		}
				
		function get_field_attribute_id( $field_args = array() ) {
			$field_attribute = '';
			
			if ( isset( $field_args['id'] ) ) {
				$field_attribute .= ' id="'. $field_args['id'] .'" ';
			}
			
			return $field_attribute;
		}

		function get_field_attribute_required( $field_args = array() ) {
			$field_attribute = '';
			
			if ( isset( $field_args['required'] ) ) {
				$field_attribute .= ' required="'. $field_args['required'] .'" ';
			}
			
			return $field_attribute;
		}
		
		function get_field_attribute_name( $field_args = array() ) {
			$field_attribute = '';
			
			if ( isset( $field_args['name'] ) ) {
				if ( !empty( $field_args['setting_option_key'] ) )
					$field_attribute .= ' name="'. $field_args['setting_option_key'] .'['. $field_args['name'] .']" ';
				else
					$field_attribute .= ' name="'. $field_args['name'] .'" ';
			}
			
			return $field_attribute;
		}

		function get_field_attribute_placeholder( $field_args = array() ) {
			$field_attribute = '';

			if ( ( isset( $field_args['placeholder'] ) ) && ( !empty( $field_args['placeholder'] ) ) ) {
				$field_attribute .= ' placeholder="'. esc_html( $field_args['placeholder'] ) .'" ';
			}

			return $field_attribute;
		}

		function get_field_attribute_type( $field_args = array() ) {
			$field_attribute = '';

			if ( isset( $field_args['type'] ) ) {
				$field_attribute .= ' type="'. $field_args['type'] .'" ';
			}
			
			return $field_attribute;
		}		
				
		function get_field_attribute_class( $field_args = array() ) {
			$field_attribute = '';
		
			$field_attribute .= ' class="learndash-section-field learndash-section-field-'. $this->field_type;
		
			if ( ( isset( $field_args['class'] ) ) && ( !empty( $field_args['class'] ) ) ) {
				$field_attribute .= ' '. $field_args['class'];
			}
			$field_attribute .= '" ';
		
			return $field_attribute;
		}
				
		function get_field_attribute_misc( $field_args = array() ) {
			$field_attribute = '';
			
			if ( ( isset( $field_args['attrs'] ) ) && ( !empty( $field_args['attrs'] ) ) ) {
				foreach( $field_args['attrs'] as $key => $val ) {
					$field_attribute .= ' '. $key .'="'. $val .'" ';
				}
			}
			
			return $field_attribute;
		}
		
		// Default validation function. Should be overriden in Field subclass
		function validate_section_field( $val, $key, $args = array() ) {
			if ( !empty( $val ) )
				$val = sanitize_text_field( $val );
			
			return $val;
		}
	}
}

// All known LD setting field type (for now). 
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-text.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-html.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-number.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-hidden.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-checkbox.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-radio.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-textarea.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-select.php' );
