<?php
/**
 * LearnDash Settings Metabox Abstract Class.
 *
 * @package LearnDash
 * @subpackage Settings
 */

require_once LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-fields.php';

if ( ! class_exists( 'LearnDash_Settings_Metabox' ) ) {
	/**
	 * Absract for LearnDash Settings Sections.
	 */
	abstract class LearnDash_Settings_Metabox {

		/**
		 * Static array of section instances.
		 *
		 * @var array $_instances
		 */
		protected static $_instances = array();

		/**
		 * Match the WP Screen ID
		 *
		 * @var string $settings_screen_id Settings Screen ID.
		 */
		//protected $settings_screen_id = '';

		/**
		 * Match the  Settings Page ID
		 *
		 * @var string $settings_page_id Settings Page ID.
		 */
		//protected $settings_page_id = '';

		/**
		 * Store for all the fields in this section
		 *
		 * @var array $setting_option_fields Array of section fields.
		 */
		protected $setting_option_fields = array();

		/**
		 * Holds the values for the fields. Read in from the wp_options item.
		 *
		 * @var array $setting_option_values Array of section values.
		 */
		protected $setting_option_values = array();

		/**
		 * Flag for if settings values have been loaded.
		 *
		 * @var boolean $settings_values_loaded Flag.
		 */
		protected $settings_values_loaded = false;

		/**
		 * Flag for if settings fields have been loaded.
		 *
		 * @var boolean $settings_fields_loaded Flag.
		 */

		protected $settings_fields_loaded = false;

		/**
		 * This is used as the option_name when the settings
		 * are saved to the options table.
		 *
		 * @var string $settings_section_key
		 */
		protected $settings_metabox_key = '';

		/**
		 * Section label/header
		 * This setting is used to show in the title of the metabox or section.
		 *
		 * @var string $settings_section_label
		 */
		protected $settings_section_label = '';

		/**
		 * Used to show the section description above the fields. Can be empty.
		 *
		 * @var string $settings_section_description
		 */
		protected $settings_section_description = '';

		/**
		 * Unique ID used for metabox on page. Will be derived from
		 * settings_option_key + setting_section_key
		 *
		 * @var string $metabox_key
		 */
		protected $metabox_key = '';

		/**
		 * Controls metabox context on page
		 * See WordPress add_meta_box() function 'context' parameter.
		 *
		 * @var string $metabox_context
		 */
		protected $metabox_context = 'normal';

		/**
		 * Controls metabox priority on page
		 * See WordPress add_meta_box() function 'priority' parameter.
		 *
		 * @var string $metabox_priority
		 */
		protected $metabox_priority = 'default';

		/**
		 * Lets the section define it's own display function instead of using the Settings API
		 *
		 * @var mixed $settings_fields_callback
		 */
		protected $settings_fields_callback = null;

		/**
		 * Used on submit metaboxes to display reset confirmation popup message.
		 *
		 * @var string $reset_confirm_message
		 */
		protected $reset_confirm_message = '';

		/**
		 * Public constructor for class
		 */
		public function __construct() {
//			add_action( 'init', array( $this, 'init' ) );
//			add_action( 'learndash_settings_page_init', array( $this, 'settings_page_init' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			
		}

		/**
		 * Get the instance of our class based on the metabox_key
		 *
		 * @since 2.6.0
		 *
		 * @param string $metabox_key Unique metabox key used to identify instance.
		 */
		final public static function get_metabox_instance( $metabox_key = '' ) {
			if ( ! empty( $metabox_key ) ) {
				if ( isset( self::$_instances[ $metabox_key ] ) ) {
					return self::$_instances[ $metabox_key ];
				}
			}
		}

		/**
		 * Add instance to static tracking array
		 *
		 * @since 2.4.0
		 */
		final public static function add_metabox_instance() {
			$metabox_key = get_called_class();
			if ( ! isset( self::$_instances[ $metabox_key ] ) ) {
				self::$_instances[ $metabox_key ] = new $metabox_key();
			}
		}

		/**
		 * Initialize self
		 */
		public function init() {
			if ( ! $this->settings_values_loaded ) {
				$this->load_settings_values();
			}

			if ( ! $this->settings_fields_loaded ) {
				$this->load_settings_fields();
			}
		}

		/**
		 * Load the section settings values.
		 */
		public function load_settings_values() {
			$this->settings_values_loaded = true;
			//$this->setting_option_values  = get_option( $this->setting_option_key );
		}

		/**
		 * Load the section settings fields.
		 */
		public function load_settings_fields() {
			$this->settings_fields_loaded = true;

			foreach ( $this->setting_option_fields as &$setting_option_field ) {

				$setting_option_field['setting_option_key'] = $this->settings_metabox_key;

				if ( ! isset( $setting_option_field['id'] ) ) {
					$setting_option_field['id'] = $setting_option_field['setting_option_key'] . '_' . $setting_option_field['name'];
				}

				if ( ! isset( $setting_option_field['label_for'] ) ) {
					$setting_option_field['label_for'] = $setting_option_field['id'];
				}

				if ( ! isset( $setting_option_field['display_callback'] ) ) {
					$display_ref = LearnDash_Settings_Section_Fields::get_field_instance( $setting_option_field['type'] )->get_creation_function_ref();
					if ( $display_ref ) {
						$setting_option_field['display_callback'] = $display_ref;
					}
				}

				if ( ! isset( $setting_option_field['callback'] ) ) {
					$setting_option_field['callback'] = $setting_option_field['display_callback'];
				}

				if ( ! isset( $setting_option_field['name_wrap'] ) ) {
					$setting_option_field['name_wrap'] = true;
				}

				if ( ! isset( $setting_option_field['validate_callback'] ) ) {
					$validate_ref = LearnDash_Settings_Section_Fields::get_field_instance( $setting_option_field['type'] )->get_vaidation_function_ref();
					if ( $validate_ref ) {
						$setting_option_field['validate_callback'] = $validate_ref;
					}
				}

				// Now we reorganize the field.
				if ( ! isset( $setting_option_field['args'] ) ) {
					$setting_option_field['args'] = array();
					foreach( $setting_option_field as $field_key => $field_val ) {
						error_log('field_key['. $field_key .']');

						$setting_option_field['args'][ $field_key ] = $field_val;
						if ( 'label' === $field_key ) {
							$setting_option_field[ 'title' ] = $field_val;
						}

						if ( ! in_array( $field_key, array( 'id', 'name', 'args', 'callback' ) ) ) {
							unset( $setting_option_field[ $field_key ] );
						}
					}
				}

				error_log('setting_option_field<pre>'. print_r($setting_option_field, true) .'</pre>');
			}
		}

		/**
		 * Save Section Settings values
		 */
		//public function save_settings_values() {
		//	$this->settings_values_loaded = false;
		//	update_option( $this->setting_option_key, $this->setting_option_values );
		//}

		/**
		 * Initialize the Settings page.
		 *
		 * @param string $settings_page_id ID of page being initialized.
		 */
		/*
		 public function settings_page_init( $settings_page_id = '' ) {

			// Ensure settings_page_id is not empty and that it matches the page_id we want to display this section on.
			if ( ( ! empty( $settings_page_id ) ) && ( $settings_page_id === $this->settings_page_id ) && ( ! empty( $this->setting_option_fields ) ) ) {

				add_settings_section(
					$this->settings_section_key,
					$this->settings_section_label,
					array( $this, 'show_settings_section_description' ),
					$this->settings_page_id
				);

				foreach ( $this->setting_option_fields as $setting_option_field ) {

					add_settings_field(
						$setting_option_field['name'],
						$setting_option_field['label'],
						$setting_option_field['display_callback'],
						$this->settings_page_id,
						$this->settings_section_key,
						$setting_option_field
					);
				}

				register_setting(
					$this->settings_page_id,
					$this->setting_option_key,
					array( $this, 'settings_section_fields_validate' )
				);
			}
		}
		*/

		/**
		 * Show Settings Section Description
		 */
		public function show_settings_section_description() {

			if ( ! empty( $this->settings_section_description ) ) {
				echo wpautop( $this->settings_section_description );
			}
		}

		/**
		 * Show Settings Section reset link
		 */
		public function show_settings_section_reset_confirm_link() {
			if ( ! empty( $this->reset_confirm_message ) ) {
				$reset_url = add_query_arg(
					array(
						'action'     => 'ld_reset_settings',
						'ld_wpnonce' => wp_create_nonce( get_current_user_id() . '-' . $this->setting_option_key ),
					)
				);
				?>
				<p class="delete-action sfwd_input">
					<a href="<?php echo esc_url( $reset_url ); ?>" class="button-secondary submitdelete deletion" data-confirm="<?php echo esc_html( $this->reset_confirm_message ); ?>"><?php esc_html_e( 'Reset Settings', 'learndash' ); ?></a>
				</p>
				<?php
			}
		}

		/**
		 * Added Settings Section meta box.
		 *
		 * @param string $settings_screen_id Settings Screen ID.
		 */
		public function add_meta_boxes( $settings_screen_id = '' ) {
			if ( $settings_screen_id === $this->settings_screen_id ) {
				$this->init();

				add_meta_box(
					$this->settings_metabox_key,
					$this->settings_section_label,
					array( $this, 'show_meta_box' ),
					$this->settings_screen_id,
					$this->metabox_context,
					$this->metabox_priority
				);
			}
		}

		/**
		 * Show Settings Section meta box.
		 */
		public function show_meta_box( $post = null, $metabox = null ) {
			error_log('in '. __FUNCTION__ );

			error_log('post<pre>'. print_r($post, true) .'</pre>');
			error_log('metabox<pre>'. print_r($metabox, true) .'</pre>');

			$this->show_settings_metabox( $this );			
		}

		/**
		 * Show the meta box settings
		 *
		 * @param string $section Section to be shown.
		 */
		public function show_settings_metabox( $metabox = null ) {
			/**
			 * The 'callback' attribute is set if/when the section description is
			 * to be displayed. See the WP add_settings_section() argument #3.
			 */
			//if ( ( ! is_null( $metabox ) ) && ( isset( $metabox['callback'] ) ) && ( ! empty( $section['callback'] ) ) && ( is_callable( $section['callback'] ) ) ) {
			//	call_user_func( $section['callback'] );
			//}

			// If this section defined its own display callback logic.
			if ( ( isset( $metbox->settings_fields_callback ) ) && ( ! empty( $metabox->settings_fields_callback ) ) && ( is_callable( $metabox->settings_fields_callback ) ) ) {
				call_user_func( $metabox->settings_fields_callback, $this->settings_metabox_key );
			} else {
				/**
				 * Note here we are calling a custom version of the WP function
				 * do_settings_fields because we want to control the label and help icons
				 */
				echo '<div class="sfwd sfwd_options ' . esc_attr( $metabox->settings_metabox_key ) . '">';
				$this->show_settings_metabox_fields( $metabox );
				echo '</div>';
			}
		}

		/**
		 * Show Settings Section Fields.
		 *
		 * @param string $page Page shown.
		 * @param string $section Section shown.
		 */
		public function show_settings_metabox_fields( $metabox = null ) {
			//if ( ! isset( self::$_instances[ $this->settings_metabox_key ] ) ) {
			//	return;
			//}
			error_log('in '. __FUNCTION__ );
			//error_log('instance<pre>'. print_r(self::$_instances[ $this->settings_metabox_key ], true) .'</pre>');
			LearnDash_Settings_Section_Fields::show_section_fields( $metabox->setting_option_fields  );
		}

		/**
		 * This validation function is set via the call to 'register_setting'
		 * and will be called for each section.
		 *
		 * @param array $post_fields Array of section fields.
		 */
		public function settings_section_fields_validate( $post_fields = array() ) {
			$setting_option_values = array();

			// This valiadate_args array will be passed to the validation function for context.
			$validate_args = array(
				'settings_page_id'   => $this->settings_page_id,
				'setting_option_key' => $this->setting_option_key,
				'post_fields'        => $post_fields,
				'field'              => null,
			);

			if ( ! empty( $post_fields ) ) {
				foreach ( $post_fields as $key => $val ) {
					if ( isset( $this->setting_option_fields[ $key ] ) ) {
						if ( ( isset( $this->setting_option_fields[ $key ]['validate_callback'] ) ) && ( ! empty( $this->setting_option_fields[ $key ]['validate_callback'] ) ) && ( is_callable( $this->setting_option_fields[ $key ]['validate_callback'] ) ) ) {
							$validate_args['field'] = $this->setting_option_fields[ $key ];
							$setting_option_values[ $key ] = call_user_func( $this->setting_option_fields[ $key ]['validate_callback'], $val, $key, $validate_args );
						}
					}
				}
			}
			return $setting_option_values;
		}

		/**
		 * Static function to get section setting.
		 *
		 * @param string $field_key Section field key.
		 * @param mixed  $default_return Default value if field not found.
		 */
		public static function get_setting( $field_key = '', $default_return = '' ) {
			return self::get_section_setting( get_called_class(), $field_key, $default_return );
		}

		/**
		 * Static function to get all section settings.
		 */
		public static function get_settings_all() {
			return self::get_section_settings_all( get_called_class() );
		}

		/**
		 * Static function to get section to get option label.
		 *
		 * @param string $field_key Section field key.
		 * @param string $option_key Section option key.
		 */
		public static function get_setting_select_option_label( $field_key = '', $option_key = '' ) {
			return self::get_section_setting_select_option_label( get_called_class(), $field_key, $option_key );
		}

		/**
		 * Static function to get a Section Setting value.
		 *
		 * @param string $section Settings Section.
		 * @param string $field_key Settings Section field key.
		 * @param mixed  $default_return Default value if field not found.
		 */
		public static function get_section_setting( $section = '', $field_key = '', $default_return = '' ) {
			if ( empty( $section ) ) {
				$section = get_called_class();
			}

			if ( isset( self::$_instances[ $section ] ) ) {
				self::$_instances[ $section ]->init();

				if ( isset( self::$_instances[ $section ]->setting_option_fields[ $field_key ] ) ) {
					$default_return = self::$_instances[ $section ]->setting_option_fields[ $field_key ]['value'];
				}
			}

			return $default_return;
		}

		/**
		 * Static function to set a Section Setting value.
		 *
		 * @param string $section Settings Section.
		 * @param string $field_key Settings Section field key.
		 * @param mixed  $new_value new value for field.
		 */
		public static function set_section_setting( $section = '', $field_key = '', $new_value = '' ) {
			if ( ( ! empty( $section ) ) && ( ! empty( $field_key ) ) ) {

				if ( isset( self::$_instances[ $section ] ) ) {
					self::$_instances[ $section ]->init();

					if ( isset( self::$_instances[ $section ]->setting_option_fields[ $field_key ] ) ) {
						self::$_instances[ $section ]->setting_option_fields[ $field_key ]['value'] = $new_value;
						self::$_instances[ $section ]->save_settings_values();
					}
				}
			}
		}

		/**
		 * Static function to get all Section fields.
		 *
		 * @param string $section Settings Section.
		 * @param string $field Settings Section field key.
		 */
		public static function get_section_settings_all( $section = '', $field = 'value' ) {
			if ( empty( $section ) ) {
				$section = get_called_class();
			}

			if ( isset( self::$_instances[ $section ] ) ) {
				self::$_instances[ $section ]->init();
				return wp_list_pluck( self::$_instances[ $section ]->setting_option_fields, $field );
			}
		}

		/**
		 * From a section settings you can access the label used on a select by the option key.
		 *
		 * @param string $section Settings Section.
		 * @param string $field_key Settings Section field key.
		 * @param string $option_key Option key.
		 */
		public static function get_section_setting_select_option_label( $section = '', $field_key = '', $option_key = '' ) {
			if ( empty( $section ) ) {
				$section = get_called_class();
			}

			if ( ! empty( $field_key ) ) {

				if ( isset( self::$_instances[ $section ] ) ) {
					self::$_instances[ $section ]->init();

					// If the option_key was not passed we default to the current selected value.
					if ( empty( $option_key ) ) {
						$option_key = self::$_instances[ $section ]->get_setting( $field_key );
					}

					// Now we get the option fields by the field_key and then derive the option label from the option_key.
					if ( ( isset( self::$_instances[ $section ]->setting_option_fields[ $field_key ] ) ) && ( 'select' === self::$_instances[ $section ]->setting_option_fields[ $field_key ]['type'] ) && ( self::$_instances[ $section ]->setting_option_fields[ $field_key ]['options'] ) && ( isset( self::$_instances[ $section ]->setting_option_fields[ $field_key ]['options'][ $option_key ] ) ) ) {
						return self::$_instances[ $section ]->setting_option_fields[ $field_key ]['options'][ $option_key ];
					}
				}
			}
		}
	}
}
