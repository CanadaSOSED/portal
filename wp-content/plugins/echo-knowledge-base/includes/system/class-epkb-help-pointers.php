<?php

/**
 * Show to user helpful pointers after first activation or on plugin update
 */
class EPKB_Help_Pointers {

	/**
	 * Constructor.
	 *
	 * @param $is_plugin_activated_first_time - whether to show help about new plugin rather than new update
	 */
	public function __construct( $is_plugin_activated_first_time ) {

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		$help_function = $is_plugin_activated_first_time ? 'setup_pointers_for_new_install' : 'setup_pointers_for_update';
		add_action( 'admin_enqueue_scripts', array( $this, $help_function ) );
	}

	/**
	 * Get text for help when plugin is activated the first time
	 */
	public function setup_pointers_for_new_install() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$pointers = array(
			'pointers' => array(
				'posts_lookup' => array(
					'target'       => "#wp-admin-bar-epkb-post-links",
					'next'         => 'pages_lookup',
					'next_trigger' => array(
						/* 'target' => "#epkb_help_later",
						'event'  => 'change blur click' */
					),
					'options'      => array(
						'content'  => 	'<h3>' . /**  esc_html__( 'Quick Tour of Knowledge Base plugin', 'echo-knowledge-base' ) .*/ '</h3>' .
										'<p>' ./**   esc_html__( 'Use this new menu to lookup any post.', 'echo-knowledge-base' ) .*/ '</p>',
						'position' => array(
							'edge'  => 'top',  // where arrow will appear: left, right, top, bottom
							'align' => 'left'  // which part of the edge where arrow appears should be: left, middle, right
						)
					)
				),
				'pages_lookup' => array(
					'target'       => "#wp-admin-bar-epkb-page-links",
					'next'         => 'cpts_lookup',
					'next_trigger' => array(),
					'options'      => array(
						'content'  => 	'<h3>' . /**  esc_html__( 'Quick Tour of Knowledge Base plugin', 'echo-knowledge-base' ) . */'</h3>' .
						                 '<p>' . /**  esc_html__( 'Use this new menu to lookup any page.', 'echo-knowledge-base' ) . */'</p>',
						'position' => array(
							'edge'  => 'top',
							'align' => 'left'
						)
					)
				),
				'cpts_lookup' => array(
					'target'       => "#wp-admin-bar-epkb-cpt-links",
					'next'         => 'plugin_settings',
					'next_trigger' => array(),
					'options'      => array(
						'content'  => 	'<h3>' . /**  esc_html__( 'Quick Tour of Knowledge Base plugin', 'echo-knowledge-base' ) .*/ '</h3>' .
						                 '<p>' . /**  esc_html__( 'Use this new menu to lookup any Custom Post Type.', 'echo-knowledge-base' ) .*/ '</p>',
						'position' => array(
							'edge'  => 'top',
							'align' => 'left'
						)
					)
				),
				'plugin_settings' => array(
					'target'       => "#menu-settings",
					'next'         => '',
					'next_trigger' => array(),
					'options'      => array(
						'content'  => 	'<h3>' . /**  esc_html__( 'Quick Tour of Knowledge Base plugin', 'echo-knowledge-base' ) .*/ '</h3>' .
						                 // TODO FUTURE <ul> does not work for some reason
						                '<p>' ./**   esc_html__( 'Configure the plugin in the <strong style="text-decoration: underline;">Settings</strong> menu.', 'echo-knowledge-base' ) . */'<br/></p>',
						'position' => array(
							'edge'  => 'left',
							'align' => 'left'
						)
					)
				)
			)
		);

		$this->enqueue_pointers( $pointers );
	}

	/**
	 * Get text for help when plugin is activated the first time
	 */
	public function setup_pointers_for_update() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$pointers = array();

		$this->enqueue_pointers( $pointers );
	}

	/**
	 * Enqueue pointers and add script to page.
	 * @param array $pointers
	 */
	public function enqueue_pointers( $pointers ) {
		global $epkb_pointers;

		$epkb_pointers = "\n" . wp_json_encode( $pointers ) . "\n";
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}
}

/**
 * Output JS for help pointers in the footer
 * Based on WooCommerce script
 */
function epkb_print_js() {
	global $epkb_pointers;

	$js = "jQuery( function( $ ) {
				var epkb_pointers = {$epkb_pointers};

				setTimeout( init_epkb_pointers, 1000 );

				function init_epkb_pointers() {
					$.each( epkb_pointers.pointers, function( i ) {
						epkb_show_pointer( i );
						return false;
					});
				}

				function epkb_show_pointer( id ) {
					var pointer = epkb_pointers.pointers[id];
					var options = $.extend( pointer.options, {
						close: function() {
							if ( pointer.next ) {
								epkb_show_pointer( pointer.next );
							}
						}
					} );
					var this_pointer = $( pointer.target ).pointer( options );
					this_pointer.pointer( 'open' );

					if ( pointer.next_trigger ) {
						$( pointer.next_trigger.target ).on( pointer.next_trigger.event, function() {
							setTimeout( function() { this_pointer.pointer( 'close' ); }, 500 );
						});
					}
				}
			});
		";

	// sanitize text
	$js = wp_check_invalid_utf8( $js );
	$js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $js );
	$js = str_replace( "\r", '', $js );

	echo "\n<script type=\"text/javascript\">\njQuery(function($) { $js });\n</script>\n";
}
add_action( 'admin_footer', 'epkb_print_js' );
