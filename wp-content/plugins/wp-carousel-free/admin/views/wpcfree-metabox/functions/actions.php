<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.


if ( ! function_exists( 'wpcf_get_option' ) ) {
	/**
	 * The wpcf_get_option function.
	 *
	 * @param string $option The option unique ID.
	 * @param mixed  $default The default value for the option.
	 * @return statement
	 */
	function wpcf_get_option( $option = '', $default = null ) {
		$options = get_option( 'sp_wpcp_settings' );
		return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
	}
}


/**
 * Populate the taxonomy name list to he select option.
 *
 * @return void
 */
function wpcf_get_taxonomies() {
	extract( $_REQUEST );
	$taxonomy_names = get_object_taxonomies( array( 'post_type' => $wpcp_post_type ), 'names' );
	foreach ( $taxonomy_names as $key => $label ) {
		echo '<option value="' . $label . '">' . $label . '</option>';
	}
	die( 0 );
}
add_action( 'wp_ajax_wpcf_get_taxonomies', 'wpcf_get_taxonomies' );

/**
 * Populate the taxonomy terms list to the select option.
 *
 * @return void
 */
function wpcf_get_terms() {
	extract( $_REQUEST );
	$terms = get_terms( $wpcp_post_taxonomy );
	foreach ( $terms as $key => $value ) {
		echo '<option value="' . $value->term_id . '">' . $value->name . '</option>';
	}
	die( 0 );
}
add_action( 'wp_ajax_wpcf_get_terms', 'wpcf_get_terms' );

/**
 * Get specific post to the select box.
 *
 * @return void
 */
function wpcf_get_posts() {
	extract( $_REQUEST );
	$all_posts = get_posts(
		array(
			'post_type'      => $wpcp_post_type,
			'posts_per_page' => -1,
		)
	);
	foreach ( $all_posts as $key => $post_obj ) {
		echo '<option value="' . $post_obj->ID . '">' . $post_obj->post_title . '</option>';
	}
	die( 0 );
}
add_action( 'wp_ajax_wpcf_get_posts', 'wpcf_get_posts' );

/**
 *
 * Export
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_export' ) ) {
	function spf_export() {

		if ( ! empty( $_GET['export'] ) && ! empty( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'spf_backup_nonce' ) ) {

			header( 'Content-Type: application/json' );
			header( 'Content-disposition: attachment; filename=backup-' . gmdate( 'd-m-Y' ) . '.json' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			echo json_encode( get_option( wp_unslash( $_GET['export'] ) ) );

		}

		die();
	}
	add_action( 'wp_ajax_spf-export', 'spf_export' );
}


/**
 *
 * Import Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_import_ajax' ) ) {
	function spf_import_ajax() {

		if ( ! empty( $_POST['import_data'] ) && ! empty( $_POST['unique'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'spf_backup_nonce' ) ) {

			// $import_data = unserialize( stripslashes( trim( $_POST['import_data'] ) ) );
			$import_data = json_decode( wp_unslash( trim( $_POST['import_data'] ) ), true );

			if ( is_array( $import_data ) ) {

				update_option( wp_unslash( $_POST['unique'] ), wp_unslash( $import_data ) );
				wp_send_json_success( array( 'success' => true ) );

			}
		}

		wp_send_json_error(
			array(
				'success' => false,
				'error'   => esc_html__( 'Error while saving.', 'wp-carousel-free' ),
				'debug'   => $_REQUEST,
			)
		);

	}
	add_action( 'wp_ajax_spf-import', 'spf_import_ajax' );
}

/**
 *
 * Reset Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_reset_ajax' ) ) {
	function spf_reset_ajax() {

		if ( ! empty( $_POST['unique'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'spf_backup_nonce' ) ) {
			delete_option( wp_unslash( $_POST['unique'] ) );
			wp_send_json_success( array( 'success' => true ) );
		}

		wp_send_json_error(
			array(
				'success' => false,
				'error'   => esc_html__( 'Error while saving.', 'wp-carousel-free' ),
				'debug'   => $_REQUEST,
			)
		);
	}
	add_action( 'wp_ajax_spf-reset', 'spf_reset_ajax' );
}

/**
 *
 * Set icons for wp dialog
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_set_icons' ) ) {
	function spf_set_icons() {
		global $post_type;
		if ( 'sp_wp_carousel' === $post_type ) {
			?>
			<div id="spf-modal-icon" class="spf-modal spf-modal-icon">
				<div class="spf-modal-table">
				<div class="spf-modal-table-cell">
					<div class="spf-modal-overlay"></div>
					<div class="spf-modal-inner">
					<div class="spf-modal-title">
						<?php esc_html_e( 'Add Icon', 'wp-carousel-free' ); ?>
						<div class="spf-modal-close spf-icon-close"></div>
					</div>
					<div class="spf-modal-header spf-text-center">
						<input type="text" placeholder="<?php esc_html_e( 'Search a Icon...', 'wp-carousel-free' ); ?>" class="spf-icon-search" />
					</div>
					<div class="spf-modal-content">
						<div class="spf-modal-loading"><div class="spf-loading"></div></div>
						<div class="spf-modal-load"></div>
					</div>
					</div>
				</div>
				</div>
			</div>
			<?php
		}

	}
	add_action( 'admin_footer', 'spf_set_icons' );
	add_action( 'customize_controls_print_footer_scripts', 'spf_set_icons' );
}
