<?php
/**
 * LearnDash Settings Fields API.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ! class_exists( 'LearnDash_Settings_Section_Fields' ) ) {
	/**
	 * Class to create the settings field.
	 */
	abstract class LearnDash_Settings_Section_Fields {

		/**
		 * Array to hold all field type instances.
		 *
		 * @var array
		 */
		protected static $_instances = array();

		/**
		 * Define the field type 'text', 'select', etc. This is unique.
		 *
		 * @var string
		 */
		protected $field_type = '';

		/**
		 * Public constructor for class
		 */
		public function __construct() {
		}

		/**
		 * Get field instance by key
		 *
		 * @since 2.4
		 *
		 * @param string $field_key Key to unique field instance.
		 *
		 * @return object instance of field if present.
		 */
		final public static function get_field_instance( $field_key = '' ) {
			if ( ! empty( $field_key ) ) {
				if ( isset( self::$_instances[ $field_key ] ) ) {
					return self::$_instances[ $field_key ];
				}
			}
		}

		/**
		 * Add field instance by key
		 *
		 * @since 2.4
		 *
		 * @param string $field_key Key to unique field instance.
		 *
		 * @return object instance of field if present.
		 */
		final public static function add_field_instance( $field_key = '' ) {
			if ( ! empty( $field_key ) ) {
				if ( ! isset( self::$_instances[ $field_key ] ) ) {
					$section_class = get_called_class();
					self::$_instances[ $field_key ] = new $section_class();
				}
				return self::$_instances[ $field_key ];
			}
		}

		/**
		 * Utility function so we are not hard coding the create/validate
		 * member functions in various settings files.
		 *
		 * @since 2.4
		 *
		 * @return reference to validation function.
		 */
		final public function get_creation_function_ref() {
			return array( $this, 'create_section_field' );
		}

		/**
		 * Utility function so we are not hard coding the create/validate
		 * member functions in various settings files.
		 *
		 * @since 2.4
		 *
		 * @return reference to validation function.
		 */
		final public function get_vaidation_function_ref() {
			return array( $this, 'validate_section_field' );
		}

		/**
		 * Show all fields in section.
		 *
		 * @since 2.4
		 *
		 * @param array $section_fields Array of fields for section.
		 */
		public static function show_section_fields( $section_fields = array() ) {

			if ( ! empty( $section_fields ) ) {

				foreach ( $section_fields as $field_id => $field ) {
					self::show_section_field_row( $field );
				}
			}
		}

		/**
		 * Shows the field row
		 *
		 * @since 2.4
		 *
		 * @param array $field Array of field settings.
		 */
		public static function show_section_field_row( $field ) {
			$field_error_class = '';

			if ( ( isset( $field['args']['setting_option_key'] ) ) && ( ! empty( $field['args']['setting_option_key'] ) ) ) {
				$settings_errors = get_settings_errors( $field['args']['setting_option_key'] );
				if ( ! empty( $settings_errors ) ) {
					foreach ( $settings_errors as $settings_error ) {
						if ( ( $settings_error['setting'] == $field['args']['setting_option_key'] ) && ( $settings_error['code'] == $field['args']['name'] ) && ( 'error' == $settings_error['type'] ) ) {
							$field_error_class = 'learndash-settings-field-error';
						}
					}
				}
			}

			$field_class = '';
			if ( ( isset( $field['args']['type'] ) ) && ( ! empty( $field['args']['type'] ) ) ) {
				$field_class = 'sfwd_input_type_' . $field['args']['type'];
			}

			if ( ( isset( $field['args']['desc_before'] ) ) && ( ! empty( $field['args']['desc_before'] ) ) ) {
				echo wptexturize( $field['args']['desc_before'] );
			}

			if ( ( isset( $field['args']['type'] ) ) && ( 'hidden' !== $field['args']['type'] ) ) {
				?>
				<div id="<?php echo $field['args']['id']; ?>_field" class="sfwd_input <?php echo $field_class; ?> <?php echo $field_error_class; ?>">
					<span class="sfwd_option_label">
						<a class="sfwd_help_text_link" style="cursor:pointer;" title="<?php esc_html_e( 'Click for Help!', 'learndash' ); ?>"
							onclick="toggleVisibility('<?php echo $field['args']['id']; ?>_tip');"><img 
								alt="" src="<?php echo LEARNDASH_LMS_PLUGIN_URL; ?>assets/images/question.png" /><label for="<?php echo esc_attr( $field['args']['label_for'] ); ?>" class="sfwd_label"><?php echo $field['title']; ?>
								<?php
							if ( isset( $field['args']['required'] ) ) {
								?><span class="learndash_required_field"><abbr title="<?php esc_html_e('Required', 'learndash' ); ?>">*</abbr></span><?php
							}
							?></label>
						</a>
					</span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div">
							<?php call_user_func( $field['callback'], $field['args'] ); ?>
						</div>
						<?php
						if ( ( isset( $field['args']['help_text'] ) ) && ( ! empty( $field['args']['help_text'] ) ) ) {
							if ( ( isset( $field['args']['help_show'] ) ) && ( true === $field['args']['help_show'] ) ) {
								$help_style = ' style="display: block !important;" ';
							} else {
								$help_style = ' style="display: none;" ';
							}
							?>
							<div id="<?php echo $field['args']['id']; ?>_tip" class="sfwd_help_text_div" <?php echo $help_style; ?>>
								<label class="sfwd_help_text"><?php echo $field['args']['help_text']; ?></label>
							</div>
							<?php
						}
						?>
					</span>
					<p class="ld-clear"></p>
				</div>
				<?php
			} else {
				call_user_func( $field['callback'], $field['args'] );
			}
			if ( ( isset( $field['args']['desc_after'] ) ) && ( ! empty( $field['args']['desc_after'] ) ) ) {
				echo wptexturize( $field['args']['desc_after'] );
			}
		}

		/**
		 * Skeleton function to create the field output.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args main field args array.
		 */
		public function create_section_field( $field_args = array() ) {
			return;
		}

		/**
		 * Create the HTML output from the field args 'id' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array   $field_args main field args array. should contain element for 'attrs'.
		 * @param boolean $wrap Flag to wrap field atrribute in normal output or just return value.
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_id( $field_args = array(), $wrap = true ) {
			$field_attribute = '';

			if ( isset( $field_args['id'] ) ) {
				if ( true === $wrap ) {
					$field_attribute .= ' id="' . $field_args['id'] . '" ';
				} else {
					$field_attribute .= $field_args['id'];
				}
			}

			return $field_attribute;
		}

		/**
		 * Create the HTML output from the field args 'required' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args main field args array. should contain element for 'attrs'.
		 *
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_required( $field_args = array() ) {
			$field_attribute = '';

			if ( isset( $field_args['required'] ) ) {
				$field_attribute .= ' required="' . $field_args['required'] . '" ';
			}

			return $field_attribute;
		}

		/**
		 * Create the HTML output from the field args 'name' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array   $field_args main field args array. should contain element for 'attrs'.
		 * @param boolean $wrap Flag to wrap field atrribute in normal output or just return value.
		 *
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_name( $field_args = array(), $wrap = true ) {
			$field_attribute = '';

			if ( isset( $field_args['name'] ) ) {
				if ( ! empty( $field_args['setting_option_key'] ) ) {
					if ( true === $wrap ) {
						if ( ( isset( $field_args['name_wrap'] ) ) && ( true === $field_args['name_wrap'] ) ) {
							$field_attribute .= ' name="' . $field_args['setting_option_key'] . '[' . $field_args['name'] . ']" ';	
						} else {
							$field_attribute .= ' name="' . $field_args['name'] . '" ';
						}
					} else {
						if ( ( isset( $feld_args['name_wrap'] ) ) && ( true === $feld_args['name_wrap'] ) ) {
							$field_attribute .= $field_args['setting_option_key'] . '[' . $field_args['name'] . ']';
						} else {
							$field_attribute .= $field_args['name'];
						}
					}
				} else {
					if ( true === $wrap ) {
						$field_attribute .= ' name="' . $field_args['name'] . '" ';
					} else {
						$field_attribute .= $field_args['name'];
					}
				}
			}

			return $field_attribute;
		}

		/**
		 * Create the HTML output from the field args 'placeholder' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args main field args array. should contain element for 'attrs'.
		 *
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_placeholder( $field_args = array() ) {
			$field_attribute = '';

			if ( ( isset( $field_args['placeholder'] ) ) && ( ! empty( $field_args['placeholder'] ) ) ) {
				$field_attribute .= ' placeholder="' . esc_html( $field_args['placeholder'] ) . '" ';
			}

			return $field_attribute;
		}

		/**
		 * Create the HTML output from the field args 'placeholder' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array   $field_args main field args array. should contain element for 'attrs'.
		 * @param boolean $wrap Flag to wrap field atrribute in normal output or just return value.
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_value( $field_args = array(), $wrap = true ) {
			$field_attribute = '';

			if ( isset( $field_args['id'] ) ) {
				if ( true === $wrap ) {
					$field_attribute .= ' value="' . $field_args['value'] . '" ';
				} else {
					$field_attribute .= $field_args['value'];
				}
			}

			return $field_attribute;
		}

		/**
		 * Create the HTML output from the field args 'type' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args main field args array. should contain element for 'attrs'.
		 *
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_type( $field_args = array() ) {
			$field_attribute = '';

			if ( isset( $field_args['type'] ) ) {
				$field_attribute .= ' type="' . $field_args['type'] . '" ';
			}

			return $field_attribute;
		}		

		/**
		 * Create the HTML output from the field args 'class' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args main field args array. should contain element for 'attrs'.
		 *
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_class( $field_args = array() ) {
			$field_attribute = '';

			$field_attribute .= ' class="learndash-section-field learndash-section-field-' . $this->field_type;

			if ( ( isset( $field_args['class'] ) ) && ( ! empty( $field_args['class'] ) ) ) {
				$field_attribute .= ' ' . $field_args['class'];
			}
			$field_attribute .= '" ';

			return $field_attribute;
		}

		/**
		 * Create the HTML output from the field args 'attrs' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args main field args array. should contain element for 'attrs'.
		 *
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_misc( $field_args = array() ) {
			$field_attribute = '';

			if ( ( isset( $field_args['attrs'] ) ) && ( ! empty( $field_args['attrs'] ) ) ) {
				foreach ( $field_args['attrs'] as $key => $val ) {
					$field_attribute .= ' ' . $key . '="' . $val . '" ';
				}
			}

			return $field_attribute;
		}


		/**
		 * Create the HTML output from the field args 'input_label' attribute.
		 *
		 * @since 2.4
		 *
		 * @param array $field_args main field args array. Should contain element for 'input_label'.
		 *
		 * @return string of HTML representation of the attrs array attributes.
		 */
		public function get_field_attribute_input_label( $field_args = array() ) {
			$field_attribute = '';

			if ( ( isset( $field_args['input_label'] ) ) && ( ! empty( $field_args['input_label'] ) ) ) {
				$field_attribute .= ' ' . $field_args['input_label'];
			}

			return $field_attribute;
		}


		/**
		 * Default validation function. Should be overriden in Field subclass.
		 *
		 * @since 2.4
		 *
		 * @param mixed  $val Value to validate.
		 * @param string $key Key of value being validated.
		 * @param array  $args Array of field args.
		 *
		 * @return mixed $val validated value.
		 */
		public function validate_section_field( $val, $key, $args = array() ) {
			if ( ! empty( $val ) ) {
				if ( isset( $args['field']['type'] ) ) {
					switch ( $args['field']['type'] ) {
						case 'wpeditor':
						case 'html':
							//$val = wp_filter_post_kses( $val );
							$val = wp_check_invalid_utf8( $val );
							if ( ! empty( $val ) ) {
								//$val = sanitize_post_field( $args['setting_option_key'] . '_' . $key, $val, 0, 'db' );
								$val = sanitize_post_field( 'post_content', $val, 0, 'db' );
							}
							break;

						case 'number':
							$val = intval( $val );
							break;

						default:
							$val = sanitize_text_field( $val );
							break;
					}
				} else {
					$val = sanitize_text_field( $val );
				}
			}

			return $val;
		}
	}
}

// All known LD setting field type (for now).
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-text.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-email.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-html.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-number.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-hidden.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-checkbox.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-radio.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-textarea.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-select.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-select-edit-delete.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-fields/class-ld-settings-section-fields-wpeditor.php' );
