<?php
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-fields.php' );

if ( !class_exists( 'LearnDash_Settings_Section' ) ) {
	abstract class LearnDash_Settings_Section {

		//private static $shown = false;

		protected static $_instances = array();
		
		// Match the WP Screen ID
		protected 	$settings_screen_id 			= 	'';

		protected 	$settings_page_id				=	'';
		
		// Store for all the fields in this section
		protected 	$setting_option_fields 			= 	array();
	
		// Holds the values for the fields. Read in from the wp_options item. 
		protected 	$setting_option_values 			= 	array();
		protected	$settings_values_loaded			=	false;
		protected	$settings_fields_loaded			=	false;

		// This is the 'option_name' key used to store the section values. 
		protected 	$setting_option_key 			= 	'';

		// This is the HTML form field prefix used. 
		protected 	$setting_field_prefix			= 	'';
	
		// This is used as the option_name when the settings are saved to the options table
		protected	$settings_section_key			= 	'';

		// Section label/header
		protected	$settings_section_label			=	'';

		// Used to show the section description above the fields. Can be empty
		protected	$settings_section_description	=	'';

		// Unique ID used for metabox on page. Will be derived from settings_option_key + setting_section_key
		protected	$metabox_key					=	'';

		// Controls location and position of metabox
		protected	$metabox_context				=	'normal';
		protected	$metabox_priority				=	'default';

		// Lets the section define it's own display function instead of using the Settings API
		protected 	$settings_fields_callback		=	null;

		// Used on submit metaboxes to display reset confirmation popup message.
		protected	$reset_confirm_message			=	'';


		function __construct() {
			add_action( 'init', array( $this, 'init' ) ); // We set to zero here because we need to load before other parts of LD.
			add_action( 'learndash_settings_page_init', array( $this, 'settings_page_init' ) );
			//add_action( 'admin_notices', array( $this, 'handle_admin_notices' ) );
			
			if ( defined( 'LEARNDASH_SETTINGS_SECTION_TYPE' ) && ( LEARNDASH_SETTINGS_SECTION_TYPE == 'metabox' ) ) {
				add_action( 'learndash_add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			}

			$this->metabox_key = $this->setting_option_key .'_'. $this->settings_section_key;

			if ( empty( $this->settings_screen_id ) ) {
				$this->settings_screen_id = 'admin_page_'. $this->settings_page_id;
			}
		}
		
		final public static function get_section_instance( $section_key = '' ) {
			if ( !empty( $section_key ) ) {
				if ( isset( self::$_instances[$section_key] ) ) {
					return self::$_instances[$section_key];
				}
			}
		}
		
		final public static function add_section_instance() {
			$sectionClass = get_called_class();

			if ( !isset( self::$_instances[$sectionClass] ) ) {
				self::$_instances[$sectionClass] = new $sectionClass();
			}
		}
			
		//
		function init() {
			if ( !$this->settings_values_loaded )
				$this->load_settings_values();

			if ( !$this->settings_fields_loaded ) {
				$this->load_settings_fields();
			}
		}

		function load_settings_values() {
			$this->settings_values_loaded = true;
			$this->setting_option_values = get_option( $this->setting_option_key );			
		}

		function load_settings_fields() {
			$this->settings_fields_loaded = true;
			
			foreach( $this->setting_option_fields as &$setting_option_field ) {
			
				$setting_option_field['setting_option_key'] = $this->setting_option_key;
				
				if ( !isset( $setting_option_field['id'] ) ) {
					$setting_option_field['id'] = $setting_option_field['setting_option_key'] .'_'. $setting_option_field['name'];
				}

				if ( !isset( $setting_option_field['label_for'] ) ) {
					$setting_option_field['label_for'] = $setting_option_field['id'];
				}
				
				if ( !isset( $setting_option_field['display_callback'] ) ) {
					$display_ref = LearnDash_Settings_Section_Fields::get_field_instance( $setting_option_field['type'] )->get_creation_function_ref();
					if ( $display_ref ) {
						$setting_option_field['display_callback'] = $display_ref;
					}
				}
				
				if ( !isset( $setting_option_field['validate_callback'] ) ) {
					$display_ref = LearnDash_Settings_Section_Fields::get_field_instance( $setting_option_field['type'] )->get_vaidation_function_ref();
					if ( $display_ref ) {
						$setting_option_field['validate_callback'] = $display_ref;
					}
				}
			}
		}

		function save_settings_values() {
			$this->settings_values_loaded = false;
			update_option( $this->setting_option_key, $this->setting_option_values );
		}

		function settings_page_init( $settings_page_id = '' ) {
		
			// Ensure settings_page_id is not empty and that it matches the page_id we want to display this section on. 
			if ( ( !empty( $settings_page_id ) ) && ( $settings_page_id == $this->settings_page_id ) && ( !empty( $this->setting_option_fields ) ) ) {
			
				add_settings_section(
					$this->settings_section_key,
					$this->settings_section_label,
					array($this, 'show_settings_section_description' ),
					$this->settings_page_id
				);

				foreach( $this->setting_option_fields as $setting_option_field ) {
					
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
		
		function show_settings_section_description() {

			if (!empty( $this->settings_section_description ) ) 
				echo wpautop( $this->settings_section_description );
		}

		function show_settinfs_section_reset_confirm_link() {
			if ( !empty( $this->reset_confirm_message ) ) {
				$reset_url = add_query_arg( 
					array(
						'action' 		=> 	'ld_reset_settings',
						'ld_wpnonce' 	=> 	wp_create_nonce( get_current_user_id() .'-'. $this->setting_option_key ),
					)
				);
				?>
				<p class="delete-action sfwd_input">
					<a href="<?php echo esc_url( $reset_url ); ?>" class="button-secondary submitdelete deletion" data-confirm="<?php echo esc_html( $this->reset_confirm_message ) ?>"><?php esc_html_e( 'Reset Settings', 'learndash' ); ?></a>
				</p>
				<?php
			}
		}

/*
		function handle_admin_notices() {
			if ( ( !self::$shown ) && ( isset( $_GET['ld-settings-cleared'] ) ) && ( isset( $_GET['page'] ) ) && ( !empty( $_GET['page'] ) ) && ($_GET['page'] == $this->settings_page_id ) ) {
				self::$shown = true;
				?>
		    	<div class="notice notice-success is-dismissible">
		        	<p><?php esc_html_e( 'Settings Cleared!', 'learndash' ); ?></p>
				</div>
				<?php
			}
		}
*/
		function add_meta_boxes( $settings_screen_id = '' ) {
			add_meta_box(
				$this->metabox_key,							/* Meta Box ID */
				$this->settings_section_label,				/* Title */
				array( $this, 'show_meta_box' ),  			/* Function Callback */
				$this->settings_screen_id,               	/* Screen: Our Settings Page */
				$this->metabox_context,                 	/* Context */
				$this->metabox_priority                 	/* Priority */
			);
		}
	
		function show_meta_box() {
			global $wp_settings_sections;
			
			if ( isset( $wp_settings_sections[$this->settings_page_id][$this->settings_section_key] ) )  {
				$this->show_settings_section( $wp_settings_sections[$this->settings_page_id][$this->settings_section_key] );
			} else {
				$this->show_settings_section();
			}
		}

		function show_settings_section( $section = null ) {
			// The 'callback' attribute is set if/when the section description is to be displayed. See the WP 
			// add_settings_section() argument #3.
			if ( ( !is_null( $section ) )
			  && ( isset( $section['callback'] ) ) 
			  && ( !empty( $section['callback'] ) ) 
			  && ( is_callable( $section['callback']  ) ) ) {
				call_user_func( $section['callback'] );
			}

			// If this section defined its own display callback logic. 
			if ( ( isset( $this->settings_fields_callback ) ) && ( !empty( $this->settings_fields_callback ) ) && ( is_callable( $this->settings_fields_callback ) ) ) {
				call_user_func( $this->settings_fields_callback, $this->settings_page_id, $this->settings_section_key );
			} else {
				//echo '<table class="form-table">';
				//do_settings_fields( $this->settings_page_id, $this->settings_section_key );
				//echo '</table>';
				
				// Note here we are calling a custom version of the WP function do_settings_fields because we want to control the label and help icons
				echo '<div class="sfwd sfwd_options '. $this->settings_section_key .'">';
				$this->show_settings_section_fields( $this->settings_page_id, $this->settings_section_key );
				$this->show_settinfs_section_reset_confirm_link();
				echo '</div>';
			}
		}
		
		function show_settings_section_fields($page, $section) {
			global $wp_settings_fields;

			if ( ! isset( $wp_settings_fields[$page][$section] ) )
				return;

			LearnDash_Settings_Section_Fields::show_section_fields( $wp_settings_fields[$page][$section] );
		}

		// This validation function is set via the call to 'register_setting' and will be called for each section 
		function settings_section_fields_validate( $post_fields = array() ) {
			
			//error_log('in '. __FUNCTION__ );
			//error_log('post_fields<pre>'. print_r($post_fields, true) .'</pre>');
			
			$sectionClass = get_called_class();
			//error_log('sectionClass['. $sectionClass .']');
			
			$setting_option_values = array();
		
			// This valiadate_args array will be passed to the validation function for context. 
			$validate_args = array(
				'settings_page_id'		=>	$this->settings_page_id,
				'setting_option_key'	=>	$this->setting_option_key,
				'post_fields'			=>	$post_fields,
				'field'					=>	null
			);						

			if ( !empty( $post_fields ) ) { 
				foreach( $post_fields as $key => $val ) {
					if ( isset( $this->setting_option_fields[$key] ) ) {
						if ( ( isset( $this->setting_option_fields[$key]['validate_callback'] ) ) && ( !empty( $this->setting_option_fields[$key]['validate_callback'] ) ) && ( is_callable( $this->setting_option_fields[$key]['validate_callback'] ) ) ) {
							$validate_args['field'] = $this->setting_option_fields[$key];
							$setting_option_values[$key] = call_user_func( $this->setting_option_fields[$key]['validate_callback'], $val, $key, $validate_args );
						} 
					}
				}
			}
			return $setting_option_values;
		}

		public static function get_setting( $field_key = '', $default_return = '' ) {
			$sectionClass = get_called_class();
			return self::get_section_setting( $sectionClass, $field_key, $default_return );
		}

		public static function get_settings_all( ) {
			$sectionClass = get_called_class();
			return self::get_section_settings_all( $sectionClass );
		}

		public static function get_setting_select_option_label( $field_key = '', $option_key = '' ) {
			$sectionClass = get_called_class();
			return self::get_section_setting_select_option_label( $sectionClass, $field_key, $option_key );
		}


		public static function get_section_setting( $section = '', $field_key = '', $default_return = '' ) {
			if ( empty( $section ) )
				$section = get_called_class();

			if ( isset( self::$_instances[$section] ) ) {
				self::$_instances[$section]->init();
				
				if ( isset( self::$_instances[$section]->setting_option_fields[$field_key] ) ) {
					$default_return = self::$_instances[$section]->setting_option_fields[$field_key]['value'];
				} 
			} 
			
			return $default_return;
		}

		public static function set_section_setting( $section = '', $field_key = '', $new_value = '' ) {
			if ( ( !empty( $section ) ) && ( !empty( $field_key ) ) ) {

				if ( isset( self::$_instances[$section] ) ) {
					self::$_instances[$section]->init();
				
					if ( isset( self::$_instances[$section]->setting_option_fields[$field_key] ) ) {
						self::$_instances[$section]->setting_option_fields[$field_key]['value'] = $new_value;
						self::$_instances[$section]->save_settings_values();
					}
				}
			} 
		}


		public static function get_section_settings_all( $section = '', $field = 'value' ) {
			if ( empty( $section ) )
				$section = get_called_class();

			if ( isset( self::$_instances[$section] ) ) {
				
				self::$_instances[$section]->init();

				return wp_list_pluck( self::$_instances[$section]->setting_option_fields, $field );
			}
		}
		
		// From a section settings you can access the label used on a select by the option key. 
		public static function get_section_setting_select_option_label( $section = '', $field_key = '', $option_key = '' ) {
			if ( empty( $section ) )
				$section = get_called_class();

			if ( !empty( $field_key ) ) {

				if ( isset( self::$_instances[$section] ) ) {
					self::$_instances[$section]->init();
										
					// If the option_key was not passed we default to the current selected value.
					if ( empty( $option_key ) ) {
						$option_key = self::$_instances[$section]->get_setting( $field_key );
					}
						
					// Now we get the option fields by the field_key and then derive the option label from the option_key	
					if ( ( isset( self::$_instances[$section]->setting_option_fields[$field_key] ) ) 
					  && ( self::$_instances[$section]->setting_option_fields[$field_key]['type'] == 'select' ) 
					  && ( self::$_instances[$section]->setting_option_fields[$field_key]['options'] ) 
					  && ( isset( self::$_instances[$section]->setting_option_fields[$field_key]['options'][$option_key] ) ) ) {
						  return self::$_instances[$section]->setting_option_fields[$field_key]['options'][$option_key];
					}
				}
			}
		}
	}
}
