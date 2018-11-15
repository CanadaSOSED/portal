<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       catchplugins.com
 * @since      1.0
 *
 * @package    To_Top
 * @subpackage To_Top/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    To_Top
 * @subpackage To_Top/admin
 * @author     Catch Plugins <info@catchplugins.com>
 */
class To_Top_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles( $hook ) {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in To_Top_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The To_Top_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( 'toplevel_page_to-top' == $hook ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/to-top-admin.css', array( 'dashicons', 'wp-color-picker' ), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'-tabs', plugin_dir_url( __FILE__ ) . 'css/admin-dashboard.css', array(), $this->version, 'all' );
		}

		$option = to_top_get_options();

		if ( $option['show_on_admin'] ) {
			//Load CSS if  To Top is enabled on admin
			//No need to enqueue dashicons as it is already present in admin
			wp_enqueue_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . '../public/css/to-top-public.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in To_Top_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The To_Top_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( 'toplevel_page_to-top' == $hook ) {
			wp_enqueue_media();

			wp_enqueue_script( 'minHeight', plugin_dir_url( __FILE__ ) . 'js/jquery.matchHeight.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/to-top-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );
		}

		$option = to_top_get_options();

		if ( $option['show_on_admin'] ) {
			//Load JS if  To Top is enabled on admin
			wp_enqueue_script( $this->plugin_name. '-public', plugin_dir_url( __FILE__ ) . '../public/js/to-top-public.js', array( 'jquery' ), $this->version, false );

			// Localize the script with new data
			wp_localize_script( $this->plugin_name. '-public', 'to_top_options', $option );
		}
	}

	public function action_links($links, $file) {
		if ( $file == $this->plugin_name . '/' . $this->plugin_name . '.php' ) {
			$customizer_link = add_query_arg( array(
					'autofocus[panel]' => 'to_top_panel',
				),
				admin_url('customize.php')
			);

			$settings_link = '<a href="' . esc_url( $customizer_link ) . '">' .esc_html__( 'Settings', 'to-top' ) . '</a>';

			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * catchwebtools: add_plugin_settings_menu
	 * add Catch Web Tools to menu
	 */
	public function add_plugin_settings_menu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page( esc_html__( 'To Top Settings', 'to-top' ), __( 'To Top Settings', 'to-top' ), 'manage_options', 'to-top', array( $this, 'settings_page' ), 'dashicons-arrow-up-alt2', '99.01564' );
	}

	/**
	 * catchwebtools: catch_web_tools_settings_page
	 * Catch Web Tools Setting function
	 */
	public function settings_page() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		require plugin_dir_path( __FILE__ ) . 'partials/to-top-admin-display.php';
	}

	/**
	 * catchwebtools: register_settings
	 * Catch Web Tools Register Settings
	 */
	public function register_settings() {
		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting(
			'to-top-group',
			'to_top_options',
			array( $this, 'sanitize_callback' )
		);
	}

	/**
	 * catchwebtools: catchwebtools_catch_updater_sanitize_callback
	 * Catch Ids Sanitization function callback
	 */
	public function sanitize_callback( $input ){

		if ( isset( $input['reset'] ) && $input['reset'] ) {
			//If reset, restore defaults
			return to_top_default_options();
		}

		//Basic Settings
		if( isset( $input['status'] ) ){
			$input['status']        = absint( $input['status'] );
		}

		if( isset( $input['scroll_offset'] ) ){
			$input['scroll_offset'] = absint( $input['scroll_offset'] );
		}

		if( isset( $input['style'] ) ){
			$input['style']         = sanitize_key( $input['style'] );
		}

		//Icon Settings
		if( isset( $input['icon_opacity'] ) ){
			$input['icon_opacity']  = absint( $input['icon_opacity'] );
		}

		if( isset( $input['icon_color'] ) ){
			$input['icon_color'] 	= (empty( $input['icon_color']) || !preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|',  $input['icon_color'])) ? '' :  $input['icon_color'];
		}

		if( isset( $input['icon_bg_color'] ) ){
			$input['icon_bg_color'] = (empty( $input['icon_bg_color']) || !preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|',  $input['icon_bg_color'])) ? '' :  $input['icon_bg_color'];
		}

		if( isset( $input['icon_size'] ) ){
			$input['icon_size']     = absint( $input['icon_size'] );
		}

		if( isset( $input['border_radius'] ) ){
			$input['border_radius'] = absint( $input['border_radius'] );
		}

		//Image Settings
		if( isset( $input['image'] ) ){
			$input['image']         = esc_url_raw( $input['image'] );
		}

		if( isset( $input['image_width'] ) ){
			$input['image_width']   = absint( $input['image_width'] );
		}

		if( isset( $input['image_alt'] ) ){
			$input['image_alt']     = sanitize_text_field( $input['image_alt'] );
		}

		//Advanced Settings
		if( isset( $input['location'] ) ){
			$input['location']      = sanitize_key( $input['location'] );
		}

		if( isset( $input['margin_x'] ) ){
			$input['margin_x']      = absint( $input['margin_x'] );
		}

		if( isset( $input['margin_y'] ) ){
			$input['margin_y']      = absint( $input['margin_y'] );
		}

		if( isset( $input['show_on_admin'] ) ){
			$input['show_on_admin'] = ( ( isset( $input['show_on_admin'] ) && true == $input['show_on_admin'] ) ? true : false );
		}

		if( isset( $input['enable_autohide'] ) ){
			$input['enable_autohide'] = ( ( isset( $input['enable_autohide'] ) && true == $input['enable_autohide'] ) ? true : false );
		}

		if( isset( $input['autohide_time'] ) ){
			$input['autohide_time']   = absint( $input['autohide_time'] );
		}

		if( isset( $input['enable_hide_small_device'] ) ){
			$input['enable_hide_small_device']= ( ( isset( $input['enable_hide_small_device'] ) && true == $input['enable_hide_small_device'] ) ? true : false );
		}

		if( isset( $input['small_device_max_width'] ) ){
			$input['small_device_max_width']  = absint( $input['small_device_max_width'] );
		}

		return $input;
	}

	/**
	 * Add Options to customizer separating the basic and advanced controls
	 *
	 * @since    1.0
	 */
	public function customize_register( $wp_customize ){

		$to_top_defaults = to_top_default_options();
		//print_r($defaults); die();

		//Custom Controls
		require plugin_dir_path( __FILE__ ) . 'partials/customizer/customizer-custom-controls.php';

		$wp_customize->add_panel( 'to_top_panel', array(
			'priority'	=> 1,
			'title'		=> esc_html__( 'To Top Options', 'to-top' ),
		) );

		/* Basic Settings Start */
		$wp_customize->add_section( 'to_top_basic_settings', array(
			'description'	=> '',
			'panel'			=> 'to_top_panel',
			'title'    		=> esc_html__( 'Basic Settings', 'to-top' ),
		) );

		$wp_customize->add_setting( 'to_top_options[scroll_offset]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['scroll_offset'],
			'type'				=> 'option',
			'transport'			=> 'refresh',
		) );

		$wp_customize->add_control( 'to_top_options[scroll_offset]', array(
			'label'    			=> esc_html__( 'Scroll Offset (px)', 'to-top' ),
			'description' 		=> esc_html__( 'Number of pixels to be scrolled before the button appears', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[scroll_offset]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 0,
		            'max'   => 500,
		            'step'  => 1,
		        	),
		) );

		$wp_customize->add_setting( 'to_top_options[icon_opacity]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['icon_opacity'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[icon_opacity]', array(
			'label'    			=> esc_html__( 'Icon Opacity (%)', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[icon_opacity]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 0,
		            'max'   => 100,
		            'step'  => 1,
		        	),
		) );

		$wp_customize->add_setting( 'to_top_options[style]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['style'],
			'type'				=> 'option',
			'transport'			=> 'refresh',
		) );

		$wp_customize->add_control( 'to_top_options[style]', array(
			'label'    			=> esc_html__( 'Style', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[style]',
			'type'     			=> 'select',
			'choices'			=> array(
					'icon'              => esc_html__( 'Icon Using Dashicons', 'to-top'),
					'genericon-icon'    => esc_html__( 'Icon Using Genericons', 'to-top'),
					'font-awesome-icon' => esc_html__( 'Icon Using Font Awesome Icons', 'to-top'),
					'image'             => esc_html__( 'Image', 'to-top')
				),
		) );

		$wp_customize->add_setting( 'to_top_options[icon_type]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['icon_type'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new To_Top_Custom_Icons ( $wp_customize, 'to_top_options[icon_type]', array(
			'label'    			=> esc_html__( 'Select Icon Type', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[icon_type]',
			'type'     			=> 'select',
			'active_callback'	=> array( $this, 'to_top_is_icon_setting_active' ),
		) ) );

		$wp_customize->add_setting( 'to_top_options[icon_color]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['icon_color'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'to_top_options[icon_color]', array(
			'label'    			=> esc_html__( 'Icon Color', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[icon_color]',
			'type'     			=> 'color',
			'active_callback'	=> array( $this, 'to_top_is_icon_setting_active' ),
		) ) );

		$wp_customize->add_setting( 'to_top_options[icon_bg_color]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['icon_bg_color'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control ( $wp_customize, 'to_top_options[icon_bg_color]', array(
			'label'    			=> esc_html__( 'Icon Background Color', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[icon_bg_color]',
			'type'     			=> 'color',
			'active_callback'	=> array( $this, 'to_top_is_icon_setting_active' ),
		) ) );

		$wp_customize->add_setting( 'to_top_options[icon_size]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['icon_size'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[icon_size]', array(
			'label'    			=> esc_html__( 'Icon Size (px)', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[icon_size]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 1,
		            'step'  => 1,
		        	),
			'active_callback'	=> array( $this, 'to_top_is_icon_setting_active' ),
		) );

		$wp_customize->add_setting( 'to_top_options[border_radius]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['border_radius'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[border_radius]', array(
			'label'    			=> esc_html__( 'Border Radius (%)', 'to-top' ),
			'description' 		=> esc_html__( '0 will make the icon background square, 50 will make it a circle', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[border_radius]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 0,
		            'max'   => 50,
		            'step'  => 1,
		        	),
			'active_callback'	=> array( $this, 'to_top_is_icon_setting_active' ),
		) );

		$wp_customize->add_setting( 'to_top_options[image]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['image'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control ( $wp_customize, 'to_top_options[image]', array(
			'label'    			=> esc_html__( 'Image', 'to-top' ),
			'description' 		=> '',
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[image]',
			'type'     			=> 'image',
			'active_callback'	=> array( $this, 'to_top_is_image_setting_active' ),
		) ) );

		$wp_customize->add_setting( 'to_top_options[image_width]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['image_width'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[image_width]', array(
			'label'    			=> esc_html__( 'Image Width (px)', 'to-top' ),
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[image_width]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 1,
		            'max'   => 200,
		            'step'  => 1,
		        	),
			'active_callback'	=> array( $this, 'to_top_is_image_setting_active' ),
		) );

		$wp_customize->add_setting( 'to_top_options[image_alt]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['image_alt'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[image_alt]', array(
			'label'    			=> esc_html__( 'Image Alt', 'to-top' ),
			'description' 		=> '',
			'section'  			=> 'to_top_basic_settings',
			'settings' 			=> 'to_top_options[image_alt]',
			'type'     			=> 'text',
			'active_callback'	=> array( $this, 'to_top_is_image_setting_active' ),
		) );

		/* Basic Settings End */

		/* Advanced Settings Start */

		$wp_customize->add_section( 'to_top_advance_settings', array(
			'description'	=> '',
			'panel'			=> 'to_top_panel',
			'title'    		=> esc_html__( 'Advanced Settings', 'to-top' ),
		) );

		$wp_customize->add_setting( 'to_top_options[location]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['location'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[location]', array(
			'label'    			=> esc_html__( 'Location', 'to-top' ),
			'description' 		=> '',
			'section'  			=> 'to_top_advance_settings',
			'settings' 			=> 'to_top_options[location]',
			'type'     			=> 'select',
			'choices'			=> array(
				'bottom-right'	=> esc_html__( 'Bottom Right', 'to-top' ),
				'bottom-left'	=> esc_html__( 'Bottom Left', 'to-top' ),
				'top-right'		=> esc_html__( 'Top Right', 'to-top' ),
				'top-left'		=> esc_html__( 'Top Left', 'to-top' ),
				),
		) );

		$wp_customize->add_setting( 'to_top_options[margin_x]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['margin_x'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[margin_x]', array(
			'label'    			=> esc_html__( 'Margin X (px)', 'to-top' ),
			'description' 		=> '',
			'section'  			=> 'to_top_advance_settings',
			'settings' 			=> 'to_top_options[margin_x]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 1,
		            'step'  => 1,
		        	),
		) );

		$wp_customize->add_setting( 'to_top_options[margin_y]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['margin_y'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[margin_y]', array(
			'label'      => esc_html__( 'Margin Y (px)', 'to-top' ),
			'description'=> '',
			'section'    => 'to_top_advance_settings',
			'settings'   => 'to_top_options[margin_y]',
			'type'       => 'number',
			'input_attrs'=> array(
		            'style' => 'width: 55px;',
		            'min'   => 1,
		            'step'  => 1,
		        	),
		) );

		$wp_customize->add_setting( 'to_top_options[show_on_admin]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['show_on_admin'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[show_on_admin]', array(
			'label'    			=> esc_html__( 'Check to show on WP-ADMIN', 'to-top' ),
			'description' 		=> esc_html__( 'Button will be shown on admin section', 'to-top' ),
			'section'  			=> 'to_top_advance_settings',
			'settings' 			=> 'to_top_options[show_on_admin]',
			'type'     			=> 'checkbox',
		) );

		$wp_customize->add_setting( 'to_top_options[enable_autohide]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['enable_autohide'],
			'type'				=> 'option',
			'transport'			=> 'refresh',
		) );

		$wp_customize->add_control( 'to_top_options[enable_autohide]', array(
			'label'    			=> esc_html__( 'Check to Enable Auto Hide', 'to-top' ),
			'description' 		=> '',
			'section'  			=> 'to_top_advance_settings',
			'settings' 			=> 'to_top_options[enable_autohide]',
			'type'     			=> 'checkbox',
		) );

		$wp_customize->add_setting( 'to_top_options[autohide_time]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['autohide_time'],
			'type'				=> 'option',
			'transport'			=> 'refresh',
		) );

		$wp_customize->add_control( 'to_top_options[autohide_time]', array(
			'label'    			=> esc_html__( 'Auto Hide Time (secs)', 'to-top' ),
			'description' 		=> esc_html__( 'Button will be auto hidden after this duration in seconds, if enabled', 'to-top' ),
			'section'  			=> 'to_top_advance_settings',
			'settings' 			=> 'to_top_options[autohide_time]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 1,
		            'step'  => 1,
		        	),
			'active_callback'	=> array( $this, 'to_top_is_auto_hide_enabled' ),
		) );

		$wp_customize->add_setting( 'to_top_options[enable_hide_small_device]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['enable_hide_small_device'],
			'type'				=> 'option',
			'transport'			=> 'refresh',
		) );

		$wp_customize->add_control( 'to_top_options[enable_hide_small_device]', array(
			'label'    			=> esc_html__( 'Check to Hide on Small Devices', 'to-top' ),
			'description' 		=> esc_html__( 'Button will be hidden on small devices when the width below matches', 'to-top' ),
			'section'  			=> 'to_top_advance_settings',
			'settings' 			=> 'to_top_options[enable_hide_small_device]',
			'type'     			=> 'checkbox',
		) );

		$wp_customize->add_setting( 'to_top_options[small_device_max_width]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['small_device_max_width'],
			'type'				=> 'option',
			'transport'			=> 'refresh',
		) );

		$wp_customize->add_control( 'to_top_options[small_device_max_width]', array(
			'label'    			=> esc_html__( 'Small Device Max Width (px)', 'to-top' ),
			'description' 		=> esc_html__( 'Button will be hidden on devices with lesser or equal width', 'to-top' ),
			'section'  			=> 'to_top_advance_settings',
			'settings' 			=> 'to_top_options[small_device_max_width]',
			'type'     			=> 'number',
			'input_attrs' 	=> array(
		            'style' => 'width: 55px;',
		            'min'   => 1,
		            'step'  => 1,
		        	),
			'active_callback'	=> array( $this, 'to_top_is_hide_on_small_devices_enabled' ),
		) );

		/* Advanced Settings End */

		/* Reset Settings Start */

		$wp_customize->add_section( 'to_top_reset_settings', array(
			'description'	=> '',
			'panel'			=> 'to_top_panel',
			'title'    		=> esc_html__( 'Reset Settings', 'to-top' ),
		) );

		$wp_customize->add_setting( 'to_top_options[reset]', array(
			'capability'		=> 'edit_theme_options',
			'default'			=> $to_top_defaults['reset'],
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( 'to_top_options[reset]', array(
			'label'    			=> esc_html__( 'Check to Reset All Settings', 'to-top' ),
			'description' 		=> esc_html__( 'Caution: All data will be lost. Refresh the page after save to view full effects.', 'to-top' ),
			'section'  			=> 'to_top_reset_settings',
			'settings' 			=> 'to_top_options[reset]',
			'type'     			=> 'checkbox',
		) );

		/* Reset Settings End */
	}

	/**
	 * Custom scripts on Customizer for Catch Box
	 *
	 * @since To Top 1.0
	 */
	function customizer_enqueue_scripts() {

		$option = to_top_get_options();

	    wp_enqueue_script( 'to_top_customizer_custom_script', plugin_dir_url( __FILE__ ) . 'js/to-top-customizer-scripts.js', array( 'jquery' ), '20151223', true );
	}

	/**
	 * Custom styles on Customizer for Catch Box
	 *
	 * @since To Top 1.0
	 */
	function customizer_enqueue_styles() {

	    wp_enqueue_style( 'to_top_customizer_custom_style', plugin_dir_url( __FILE__ ) . 'css/customizer.css' );

	}



	/**
	 * Sanitizes Checkboxes
	 * @param  $input entered value
	 * @return sanitized output
	 *
	 * @since 1.0
	 */
	function sanitize_checkbox( $checked ) {
		// Boolean check.
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}

	/**
	 * Active Callbacks
	 * @return true or false
	 *
	 * @since 1.0
	 */

	function to_top_is_icon_setting_active( $control ) {
		$style = $control->manager->get_setting( 'to_top_options[style]' )->value();

		//return true only if icon setting is selected
		if( $style === 'icon' || $style === 'genericon-icon' || $style === 'font-awesome-icon' ) {
			return true;
		}
		else {
			return false;
		}
	}

	function to_top_is_image_setting_active( $control ) {
		$style = $control->manager->get_setting( 'to_top_options[style]' )->value();

		//return true only if icon setting is selected
		if( $style === 'image') {
			return true;
		} else {
			return false;
		}
	}

	function to_top_is_auto_hide_enabled( $control ) {
		$autohide = $control->manager->get_setting( 'to_top_options[enable_autohide]' )->value();
		if ( $autohide ) {
			return true;
		} else {
			return false;
		}
	}

	function to_top_is_hide_on_small_devices_enabled( $control ) {
		$hide_on_small_devices = $control->manager->get_setting( 'to_top_options[enable_hide_small_device]' )->value();
		if ( $hide_on_small_devices ) {
			return true;
		} else {
			return false;
		}
	}
}