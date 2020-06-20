<?php

/**
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Admin_Notices {

	public function __construct() {
		if ( isset($_REQUEST['epkb_dismiss_id']) ) {
			self::dismiss_long_notice( $_REQUEST['epkb_dismiss_id'] );
		}
		
		add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
	}

	/**
	 * Show noticers for admin at the top of the page
	 */
	public function show_admin_notices() {

		$notices = get_option( 'epkb_one_time_notices', array() );
		if ( ! empty($notices) ) {
			delete_option( 'epkb_one_time_notices' );
		}

		$notices += get_option( 'epkb_long_notices', array() );

		foreach ( $notices as $notice ) {
			if ( $notice ) {	?>
				<div class="epkb-notice notice notice-<?php echo $notice['type']; ?> notice-<?php echo $notice['id']; ?>" style="display:block;">
					<p>						<?php
						echo $notice['text'];
						if ( ! empty( $notice['id'] ) ) {  ?>
							&nbsp;
							<a href="<?php echo add_query_arg( array( 'epkb_dismiss_id' => $notice['id'] ), (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ); ?>"
						        class="epkb-notice-dismiss"><?php _e( 'Dismiss', 'echo-knowledge-base' ); ?></a>						<?php
						} ?>
					</p>
				</div><?php
			}
		}
		
		if ( empty($_GET['epkb_admin_notice']) ) {
			return;
		}

		$param = EPKB_Utilities::get( 'epkb_notice_param' );
		$class = 'error';
		switch ( $_GET['epkb_admin_notice'] ) {

				case 'kb_refresh_page' :
					$message = __( 'Refresh your page', 'echo-knowledge-base' );
					$class = 'primary';
					break;
				case 'kb_refresh_page_error' :
					$message = __( 'Error occurred. Please refresh your browser and try again.', 'echo-knowledge-base' );
					break;
				case 'kb_security_failed' :
					$message = __( 'You do not have permission.', 'echo-knowledge-base' );
					break;
				default:
					$message = __( 'unknown error (133)', 'echo-knowledge-base' );
					break;
			}

		echo  EPKB_Utilities::get_bottom_notice_message_box( $message, '', $class );
	}

	/**
	 * Dismiss admin notices when Dismiss links are clicked - PER USER
	 *
	 * @return void
	 */
	function dismiss_admin_notices() {
		if ( ! empty( $_GET['epkb_admin_notice'] ) ) {
			update_user_meta( get_current_user_id(), '_epkb_' . EPKB_Utilities::sanitize_english_text( $_GET['epkb_admin_notice'], 'EPKB admin notice' ) . '_dismissed', 1 );
			wp_redirect( remove_query_arg( array( 'epkb_action', 'epkb_admin_notice' ) ) );
			exit;
		}
	}
	
	public static function add_one_time_notice( $text, $type, $id = '' ) {
		$notices = get_option( 'epkb_one_time_notices', array() );
		$notices[] = array(
			'type' => $type,
			'text' => $text,
			'id' => $id 
		);
		update_option( 'epkb_one_time_notices', $notices );
	}
	
	public static function add_ongoing_notice( $text, $type, $id = '' ) {
		$notices = get_option( 'epkb_long_notices', array() );
		
		if ( ! $id ) {
			$notices[] = array(
				'type' => $type,
				'text' => $text,
				'id' => ''
			);
		} else {
			$notices[$id] = array(
				'type' => $type,
				'text' => $text,
				'id' => $id
			);
		}
		update_option( 'epkb_long_notices', $notices );
	}
	
	public static function dismiss_long_notice( $id = '' ) {
		if ( empty($id) ) {
			// delete all 
			delete_option( 'epkb_long_notices' );

		} else {
			$notices = get_option( 'epkb_long_notices', array() );
			if ( isset($notices[$id]) ) {
				unset ( $notices[$id] );
			}
			update_option( 'epkb_long_notices', $notices );
		}
	}
}
