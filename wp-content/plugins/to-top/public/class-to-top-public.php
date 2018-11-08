<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       catchplugins.com
 * @since      1.0
 *
 * @package    To_Top
 * @subpackage To_Top/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    To_Top
 * @subpackage To_Top/public
 * @author     Catch Plugins <info@catchplugins.com>
 */
class To_Top_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {

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

		$option = to_top_get_options();

		$dependency = array();

		if ( 'icon' == $option['style'] ) {
			$dependency[] = 'dashicons';
		}
		else if ( 'genericon-icon' == $option['style'] ) {
			$dependency[] = 'genericons';

			wp_register_style( 'genericons', plugin_dir_url( __FILE__ ) . 'css/genericons/genericons.css', false, '3.4.1' );
		}
		else if ( 'font-awesome-icon' == $option['style'] ) {
			$dependency[] = 'font-awesome';

			wp_register_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome/css/font-awesome.min.css', false, '4.5.0' );

		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/to-top-public.css', $dependency, $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {

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

		$option = to_top_get_options();

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/to-top-public.js', array( 'jquery' ), $this->version, false );

		// Localize the script with new data
		wp_localize_script( $this->plugin_name, 'to_top_options', $option );

	}

	public function public_display() {

		$option = to_top_get_options();

		if ( is_admin() && !$option['show_on_admin'] ) {
			//Bail early if in admin and show on admin is disabled
			return;
		}

		if ( 'icon' == $option['style'] ) {
			echo '<span id="to_top_scrollup" class="dashicons ' . esc_attr( $option['icon_type'] ) .'"><span class="screen-reader-text">' . esc_html__( 'Scroll Up', 'to-top' ) . '</span></span>' ;
		}
		else if ( 'genericon-icon' == $option['style'] ) {
			if (  'dashicons-arrow-up' == $option['icon_type'] ) {
				$class = 'genericon genericon-uparrow';
			}
			else if ( 'dashicons-arrow-up-alt' == $option['icon_type'] ) {
				$class = 'genericon genericon-next genericon-rotate-270';
			}
			else {
				$class = 'genericon genericon-collapse';
			}

			echo '<span id="to_top_scrollup" class="' . esc_attr( $class ) .'"><span class="screen-reader-text">' . esc_html__( 'Scroll Up', 'to-top' ) . '</span></span>' ;
		}
		else if ( 'font-awesome-icon' == $option['style'] ) {
			if (  'dashicons-arrow-up' == $option['icon_type'] ) {
				$class = 'fa fa-caret-up';
			}
			else if ( 'dashicons-arrow-up-alt' == $option['icon_type'] ) {
				$class = 'fa fa-arrow-up';
			}
			else {
				$class = 'fa fa-angle-up';
			}

			echo '<span id="to_top_scrollup" class="' .  esc_attr( $class ) .'"><span class="screen-reader-text">' . esc_html__( 'Scroll Up', 'to-top' ) . '</span></span>' ;
		}
		else {
			if( '' != $option['image'] ){
				/**
				 * make image link protocolless
				 * http:// or https:// to //
				 */
				$image = explode( ':', $option['image'] );

				unset( $image[0] );

				$image = implode( '', $image );

				echo '<span id="to_top_scrollup"><img alt="' . esc_attr( $option['image_alt'] ) . '" src="' . esc_url( $image ) . '"/></span>' ;
			}
		}
	}

	public function custom_css() {
		$custom_css = '';

		$option  = to_top_get_options();

		if ( is_admin() && !$option['show_on_admin'] ) {
			return $custom_css;
		}

		$default = to_top_default_options();

		if( $option != $default ) {
			if ( 'image' == $option['style'] ) {
				$custom_css .= 'background-color: transparent; color: transparent; height: auto; width: 65px;';
				if ( $default['image_width'] != $option['image_width'] ) {
					$custom_css .= 'width: ' . esc_attr( $option['image_width'] ) . 'px;';
				}
			}
			else {
				//Type is icon
				if ( $default['icon_color'] != $option['icon_color'] ) {
					$custom_css .= 'color: ' . esc_attr( $option['icon_color'] ) . ';';
				}

				if ( $default['icon_size'] != $option['icon_size'] ) {
					$custom_css .= 'font-size: ' . esc_attr( $option['icon_size'] ) . 'px; width: ' . esc_attr( $option['icon_size'] ) . 'px; height: ' . esc_attr( $option['icon_size'] ) . 'px;';
				}

				if ( $default['icon_bg_color'] != $option['icon_bg_color'] ) {
					$custom_css .= 'background-color: ' . esc_attr( $option['icon_bg_color'] ) . ';';
				}

				if ( $default['border_radius'] != $option['border_radius'] ) {
					$custom_css .= '-webkit-border-radius: ' . esc_attr( $option['border_radius'] ) . '%; -moz-border-radius: ' . esc_attr( $option['border_radius'] ) . '%; border-radius: ' . esc_attr( $option['border_radius'] ) . '%;';
				}
			}

			if ( $default['icon_opacity'] != $option['icon_opacity'] ) {
					$custom_css .= 'opacity: ' . esc_attr( $option['icon_opacity'] / 100 ) . ';';
				}

			if ( $default['location'] != $option['location'] || $default['margin_x'] != $option['margin_x'] || $default['margin_y'] != $option['margin_y'] ) {
				$offset = explode( '-',  $option['location'] );
				$offset1 = $offset[0];
				$offset2 = $offset[1];

				$custom_css .= esc_attr( $offset2 ) . ':' . esc_attr( $option['margin_x'] ) . 'px;';
				$custom_css .= esc_attr( $offset1 ) . ':' . esc_attr( $option['margin_y'] ) . 'px;';

			}
		}

		if ( '' != $custom_css ) {
			$custom_css = "<!-- To Top Custom CSS --><style type='text/css'>#to_top_scrollup {" . $custom_css . "}</style>";
		}

		echo $custom_css;
	}
}