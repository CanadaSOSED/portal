<?php
// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_WPCP_MCE_Carousel_List' ) ) {

	/**
	 * The Tiny MCE button class.
	 */
	class SP_WPCP_MCE_Carousel_List {

		/**
		 * Instance of the class.
		 *
		 * @var $instance
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 2.0.0
		 */
		public static function init() {
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			add_action( 'wp_ajax_wpcp_cpt_list', array( $this, 'wpcp_carousel_list_ajax' ) );
			add_action( 'admin_footer', array( $this, 'wpcp_cpt_list' ) );
			add_action( 'admin_head', array( $this, 'wpcp_mce_button' ) );
		}

		/**
		 * Hooks your functions into the correct filters
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function wpcp_mce_button() {
			// check user permissions.
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}
			// check if WYSIWYG is enabled.
			if ( 'true' == get_user_option( 'rich_editing' ) ) {
				add_filter( 'mce_external_plugins', array( $this, 'add_mce_plugin' ) );
				add_filter( 'mce_buttons', array( $this, 'register_mce_button' ) );
			}
		}

		/**
		 * Script for our mce button.
		 *
		 * @since 2.0.0
		 * @param string $plugin_array The button.
		 * @return string
		 */
		public function add_mce_plugin( $plugin_array ) {
			$plugin_array['sp_wpcp_mce_button'] = WPCAROUSELF_URL . 'admin/js/mce-button.js';
			return $plugin_array;
		}

		/**
		 * Register our button in the editor.
		 *
		 * @since 2.0.0
		 * @param string $buttons The Tiny mce button.
		 * @return statement
		 */
		public function register_mce_button( $buttons ) {
			array_push( $buttons, 'sp_wpcp_mce_button' );
			return $buttons;
		}

		/**
		 * Function to fetch cpt posts list
		 *
		 * @since 2.0.0
		 * @param string $post_type List of the post type.
		 * @return void
		 */
		public function posts( $post_type ) {

			global $wpdb;
			$cpt_type        = $post_type;
			$cpt_post_status = 'publish';
			$cpt             = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, post_title
					FROM $wpdb->posts 
					WHERE $wpdb->posts.post_type = %s
					AND $wpdb->posts.post_status = %s
					ORDER BY ID DESC",
					$cpt_type,
					$cpt_post_status
				)
			);

			$list = array();

			foreach ( $cpt as $post ) {
				$selected  = '';
				$post_id   = $post->ID;
				$post_name = $post->post_title;
				$list[]    = array(
					'text'  => $post_name,
					'value' => $post_id,
				);
			}

			wp_send_json( $list );
		}

		/**
		 * Function to fetch buttons
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public function wpcp_carousel_list_ajax() {
			// check for nonce.
			check_ajax_referer( 'sp-wpcp-mce-nonce', 'security' );
			$posts = $this->posts( 'sp_wp_carousel' ); // change 'post' if you need posts list.
			return $posts;
		}

		/**
		 * Function to output button list ajax script
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function wpcp_cpt_list() {
			// create nonce.
			global $current_screen;
			$current_screen->post_type;
			if ( 'post' || 'page' === $current_screen ) {
				$nonce = wp_create_nonce( 'sp-wpcp-mce-nonce' );
				?>
				<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {
						var data = {
							'action'   : 'wpcp_cpt_list',	// wp ajax action.
							'security' : '<?php echo $nonce; ?>' // nonce value created earlier.
						};
						// fire ajax.
						  jQuery.post( ajaxurl, data, function( response ) {
							  // if nonce fails then not authorized else settings saved.
							  if( response === '-1' ){
								  // do nothing.
								  console.log('error');
							  } else {
								  if (typeof(tinyMCE) != 'undefined') {
									  if (tinyMCE.activeEditor != null) {
										tinyMCE.activeEditor.settings.spWPCPCarouselList = response;
									}
								}
							  }
						  });
					});
				</script>
				<?php
			}
		}

	} // Mce Class
}

/**
 *  Kicking this off
 */
$sp_wpcp_mce_btn = new SP_WPCP_MCE_Carousel_List();
$sp_wpcp_mce_btn->init();
