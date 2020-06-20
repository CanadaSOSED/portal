<?php
/**
 * The public-facing functionality of the plugin.
 */
class WP_Carousel_Free_Public {

	/**
	 * Script and style suffix
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $suffix;

	/**
	 * The ID of the plugin.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string      $plugin_name The ID of this plugin
	 */
	protected $plugin_name;

	/**
	 * The version of the plugin
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string      $version The current version fo the plugin.
	 */
	protected $version;

	/**
	 * Initialize the class sets its properties.
	 *
	 * @since 2.0.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->suffix      = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the plugin.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		if ( wpcf_get_option( 'wpcp_enqueue_slick_css', true ) ) {
			wp_enqueue_style( 'wpcf-slick', WPCAROUSELF_URL . 'public/css/slick' . $this->suffix . '.css', array(), $this->version, 'all' );
		}
		if ( wpcf_get_option( 'wpcp_enqueue_fa_css', true ) ) {
			wp_enqueue_style( $this->plugin_name . '-fontawesome', WPCAROUSELF_URL . 'public/css/font-awesome.min.css', array(), $this->version, 'all' );
		}
		wp_enqueue_style( $this->plugin_name, WPCAROUSELF_URL . 'public/css/wp-carousel-free-public' . $this->suffix . '.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the plugin.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( 'wpcp-preloader', WPCAROUSELF_URL . 'public/js/preloader' . $this->suffix . '.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'wpcf-slick', WPCAROUSELF_URL . 'public/js/slick' . $this->suffix . '.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'wpcf-slick-config', WPCAROUSELF_URL . 'public/js/wp-carousel-free-public' . $this->suffix . '.js', array( 'jquery' ), $this->version, true );
	}
}
