<?php
if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( !class_exists( 'LearnDash_Settings_Page_Translations' ) ) ) {
	class LearnDash_Settings_Page_Translations extends LearnDash_Settings_Page {
		private $ld_options_key = 'ld-translatation-message';

		function __construct() {
			$this->parent_menu_page_url		=	'admin.php?page=learndash_lms_settings';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'learndash_lms_translations';
			$this->settings_page_title 		= 	esc_html__( 'Translations', 'learndash' );
			$this->settings_tab_title		=	$this->settings_page_title;
			$this->settings_tab_priority	=	40;
			
			//do_action('learndash_settings_page_before_title', array( $this, 'settings_page_before_title' ) );
			parent::__construct(); 
		}

		function load_settings_page() {
			global $learndash_assets_loaded;
			
			wp_enqueue_style( 
				'learndash-admin-settings-page-translations-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-settings-page-translations'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array(), 
				LEARNDASH_SCRIPT_VERSION_TOKEN 
			);
			$learndash_assets_loaded['styles']['learndash-admin-settings-page-translations-style'] = __FUNCTION__;
			
			
			wp_enqueue_script( 
				'learndash-admin-settings-page-translations-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-settings-page-translations'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ), 
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true 
			);
			$learndash_assets_loaded['scripts']['learndash-admin-settings-page-translations-script'] = __FUNCTION__;

			$this->handle_translation_message();
			$this->handle_translation_actions();

			parent::load_settings_page(); 
		}
		
		function settings_page_before_title() {
			$this->handle_translation_message();
		}
		
		function get_admin_page_form( $start = true ) {
			if ( $start === true ) {
				//return '<form id="learndash-settings-translations-page-form" method="post" action="">';
				return '';
			} else {
				//return '</form>';
				return '';
			}
		}

		function handle_translation_message() {
			$reply = get_option( $this->ld_options_key, array() );
			if ( !empty( $reply ) ) {
				// Delete the option we don't need anymore
				delete_option( $this->ld_options_key );
				
				if ( ( isset( $reply['status'] ) ) && ( isset( $reply['message'] ) ) ) {
					if ( $reply['status'] == true ) {
						?>
						<div class="notice notice-success is-dismissible"> 
							<?php echo $reply['message']; ?>
							<button type="button" class="notice-dismiss">
								<span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.') ?></span>
							</button>
						</div>
						<?php
						
					} else {
						?>
						<div class="notice notice-error is-dismissible"> 
							<?php echo $reply['message']; ?>
							<button type="button" class="notice-dismiss">
								<span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.') ?></span>
							</button>
						</div>
						<?php
						
					}
				}
			}
		}

		function handle_translation_actions() {
			//error_log('_GET<pre>'. print_r($_GET, true) .'</pre>');
			
			if ( isset( $_GET['action'] ) ) {
				$action = esc_attr( $_GET['action'] );
			}
			
			if ( isset( $_GET['project'] ) ) {
				$project = esc_attr( $_GET['project'] );
			} else {
				$project = '';
			}
			
			if ( isset( $_GET['locale'] ) ) {
				$locale = esc_attr( $_GET['locale'] );
			} else {
				$locale = '';
			}
			
			if ( isset( $_GET['ld-translation-nonce'] ) ) {
				$nonce = esc_attr( $_GET['ld-translation-nonce'] );
			} else {
				$nonce = '';
			}

			if ( !empty( $action ) ) {
				switch( $action ) {
					case 'install':
						if ( ( !empty( $project ) ) && ( !empty( $locale ) ) && ( !empty( $nonce ) ) ) {
							if ( wp_verify_nonce( $nonce, 'ld-translation-'. $action .'-'. $project .'-'. $locale ) ) {
								$reply = LearnDash_Translations::install_translation( $project, $locale );
							} 
						}
						break;

					case 'update':
						if ( ( !empty( $project ) ) && ( !empty( $locale ) ) && ( !empty( $nonce ) ) ) {
							if ( wp_verify_nonce( $nonce, 'ld-translation-'. $action .'-'. $project .'-'. $locale ) ) {
								$reply = LearnDash_Translations::update_translation( $project, $locale );
							} 
						}
						break;
						
					case 'remove':
						if ( ( !empty( $project ) ) && ( !empty( $locale ) ) && ( !empty( $nonce ) ) ) {
							if ( wp_verify_nonce( $nonce, 'ld-translation-'. $action .'-'. $project .'-'. $locale ) ) {
								$reply = LearnDash_Translations::remove_translation( $project, $locale );
							} 
						}
						break;

					case 'refresh':
						if ( wp_verify_nonce( $nonce, 'ld-translation-'. $action ) ) {
							$reply = LearnDash_Translations::refresh_translations( );
						} 
						break;
				
					default:
						break;
				}
				
				if ( ( isset( $reply ) ) && ( !empty( $reply ) ) ) {
					update_option( $this->ld_options_key, $reply );
				}
			
				$redirect_url = remove_query_arg( array( 'action', 'project', 'locale', 'ld-translation-nonce' ) );
				if ( !empty( $redirect_url ) )
					wp_redirect( $redirect_url );
			}
		}
		
		// End of functions
	}
}
add_action( 'learndash_settings_pages_init', function() {
	LearnDash_Settings_Page_Translations::add_page_instance();
} );



