<?php
/**
 * The admin-specific of the plugin.
 *
 * @link https://shapedplugin.com
 * @since 2.0.0
 *
 * @package WordPress_Carousel_Pro
 * @subpackage WordPress_Carousel_Pro/admin
 */

/**
 * The class for the admin-specific functionality of the plugin.
 */
class WP_Carousel_Free_Admin {
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
	 * The single instance of the class.
	 *
	 * @var self
	 * @since 2.0.0
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since 2.0.0
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

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
	 * Register the stylesheets for the admin area of the plugin.
	 *
	 * @since  3.0.0
	 * @return void
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( 'font-awesome', WPCAROUSELF_URL . 'public/css/font-awesome.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . 'admin', WPCAROUSELF_URL . 'admin/css/wp-carousel-free-admin' . $this->suffix . '.css', array(), $this->version, 'all' );
		// Scripts.
		wp_enqueue_script( $this->plugin_name . 'admin', WPCAROUSELF_URL . 'admin/js/wp-carousel-free-admin' . $this->suffix . '.js', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Change Carousel updated messages.
	 *
	 * @since 2.0.0
	 * @param string $messages The Update messages.
	 * @return statement
	 */
	public function wpcp_carousel_updated_messages( $messages ) {
		global $post, $post_ID;
		$messages['sp_wp_carousel'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Carousel updated.', 'wp-carousel-free' ) ),
			2  => '',
			3  => '',
			4  => __( 'Carousel updated.', 'wp-carousel-free' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Carousel restored to revision from %s', 'wp-carousel-free' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Carousel published.', 'wp-carousel-free' ) ),
			7  => __( 'Carousel saved.', 'wp-carousel-free' ),
			8  => sprintf( __( 'Carousel submitted.', 'wp-carousel-free' ) ),
			9  => sprintf( __( 'Carousel scheduled for: <strong>%1$s</strong>.', 'wp-carousel-free' ), date_i18n( __( 'M j, Y @ G:i', 'wp-carousel-free' ), strtotime( $post->post_date ) ) ),
			10 => sprintf( __( 'Carousel draft updated.', 'wp-carousel-free' ) ),
		);
		return $messages;
	}

	/**
	 * Add carousel admin columns.
	 *
	 * @return statement
	 */
	public function filter_carousel_admin_column() {
		$admin_columns['cb']            = '<input type="checkbox" />';
		$admin_columns['title']         = __( 'Carousel Title', 'wp-carousel-free' );
		$admin_columns['shortcode']     = __( 'Shortcode', 'wp-carousel-free' );
		$admin_columns['carousel_type'] = __( 'Carousel Type', 'wp-carousel-free' );
		$admin_columns['date']          = __( 'Date', 'wp-carousel-free' );

		return $admin_columns;
	}

	/**
	 * Display admin columns for the carousels.
	 *
	 * @since 2.0.0
	 * @param mix    $column The columns.
	 * @param string $post_id The post ID.
	 * @return void
	 */
	public function display_carousel_admin_fields( $column, $post_id ) {
		$upload_data     = get_post_meta( $post_id, 'sp_wpcp_upload_options', true );
		$carousels_types = isset( $upload_data['wpcp_carousel_type'] ) ? $upload_data['wpcp_carousel_type'] : '';
		switch ( $column ) {
			case 'shortcode':
				$column_field = '<input style="width: 270px; padding: 6px;" type="text" onClick="this.select();" readonly="readonly" value="[sp_wpcarousel id=&quot;' . $post_id . '&quot;]"/>';
				echo $column_field;
				break;
			case 'carousel_type':
				echo ucwords( str_replace( '-', ' ', $carousels_types ) );

		} // end switch.
	}

	/**
	 * Add plugin action menu
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @param array  $links The action link.
	 * @param string $plugin_file The file.
	 * @since 2.0.0
	 * @return array
	 */
	public function add_plugin_action_links( $links, $plugin_file ) {

		if ( WPCAROUSELF_BASENAME === $plugin_file ) {
			$ui_links = sprintf( '<a href="%s">%s</a>', admin_url( 'post-new.php?post_type=sp_wp_carousel' ), __( 'Create Carousel', 'wp-carousel-free' ) );

			array_unshift( $links, $ui_links );

			$links['go_pro'] = sprintf( '<a target="_blank" href="%1$s" style="color: #35b747; font-weight: 700;">Go Premium!</a>', 'https://shapedplugin.com/plugin/wordpress-carousel-pro' );
		}

		return $links;
	}

	/**
	 * Plugin row meta.
	 *
	 * Adds row meta links to the plugin list table
	 *
	 * Fired by `plugin_row_meta` filter.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata, including
	 *                            the version, author, author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file, relative to the plugins
	 *                            directory.
	 *
	 * @return array An array of plugin row meta links.
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( WPCAROUSELF_BASENAME === $plugin_file ) {
			$row_meta = [
				'docs' => '<a href="https://wordpresscarousel.com" aria-label="' . esc_attr( __( 'Live Demo', 'wp-carousel-free' ) ) . '" target="_blank">' . __( 'Live Demo', 'wp-carousel-free' ) . '</a>',
				'ideo' => '<a href="https://shapedplugin.com/docs/docs/wordpress-carousel" aria-label="' . esc_attr( __( 'View WP Carousel Video Tutorials', 'wp-carousel-free' ) ) . '" target="_blank">' . __( 'Docs & Video Tutorials', 'wp-carousel-free' ) . '</a>',
			];

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}

		return $plugin_meta;
	}

	/**
	 * Bottom review notice.
	 *
	 * @since 2.0.0
	 * @param string $text The review notice.
	 * @return string
	 */
	public function sp_wpcp_review_text( $text ) {
		$screen = get_current_screen();
		if ( 'sp_wp_carousel' === get_post_type() || 'sp_wp_carousel_page_wpcp_settings' === $screen->id || 'sp_wp_carousel_page_wpcp_help' === $screen->id ) {
			$url  = 'https://wordpress.org/support/plugin/wp-carousel-free/reviews/?filter=5#new-post';
			$text = sprintf( __( 'If you like <strong>WordPress Carousel</strong>, please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'wp-carousel-free' ), $url );
		}

		return $text;
	}

	/**
	 * Redirect after activation.
	 *
	 * @param string $plugin_file Path to the plugin file, relative to the plugin.
	 * @return void
	 */
	public function sp_wpcf_redirect_after_activation( $plugin_file ) {
		if ( WPCAROUSELF_BASENAME === $plugin_file ) {
			exit( esc_url( wp_safe_redirect( admin_url( 'edit.php?post_type=sp_wp_carousel&page=wpcf_help' ) ) ) );
		}
	}
}
