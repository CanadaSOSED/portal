<?php
// This class handles the data upgrade from the user meta arrays into a DB structure to allow on the floy reporting. Plus to not bloat the 
// user meta table. 

if ( !class_exists( 'Learndash_Admin_Settings_Data_Upgrades' ) ) {
	class Learndash_Admin_Settings_Data_Upgrades {

		private static $instance;

		private $admin_notice_shown = false;
		
		protected $process_times = array();
		protected $data_slug;
		protected $meta_key;

		private $transient_prefix = 'ld-upgraded-';

		
		protected $data_settings_loaded = false;
		protected $data_settings = array();

		protected $upgrade_actions = array();
		
		function __construct() {
			$this->parent_menu_page_url		=	'admin.php?page=learndash_lms_settings';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'learndash_data_upgrades';
			$this->settings_page_title 		=  esc_html__( 'Data Upgrades', 'learndash' );
			$this->settings_tab_title		=	$this->settings_page_title;
			$this->settings_tab_priority	=	30;


			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'learndash_admin_tabs_set', array( $this, 'admin_tabs' ), 50 );
			
			if ( !defined( 'LEARNDASH_PROCESS_TIME_PERCENT' ) )
				define( 'LEARNDASH_PROCESS_TIME_PERCENT', apply_filters('learndash_process_time_percent', 80 ) );

			if ( !defined( 'LEARNDASH_PROCESS_TIME_SECONDS' ) )
				define( 'LEARNDASH_PROCESS_TIME_SECONDS', apply_filters('learndash_process_time_seconds', 10 ) );
		}
		
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new static();
			}

			return self::$instance;
		}
		
		/**
		 * Initialize the LearnDash Settings array
		 *
		 * @since 2.3
		 * 
		 * @param  bool $force_reload optional to force reload from database
		 * @param  none
		 */
		function init_data_settings( $force_reload = false ) {
			
			if ( ( $this->data_settings_loaded != true ) || ( $force_reload == true ) ) {
				$this->data_settings_loaded = true;
				$this->data_settings = get_option('learndash_data_settings', array());

				if ( !isset( $this->data_settings['db_version'] ) )
					$this->data_settings['db_version'] = 0;
			}
		}
				
		/**
		 * Get the LearnDash Settings array
		 *
		 * @since 2.3
		 * 
		 * @param  string $key optional to return only specifc key value. 
		 * @return  mixed 
		 */
		function get_data_settings( $key = '' ) {
			$this->init_data_settings(true);
			
			if ( !empty( $key ) ) {
				if ( isset( $this->data_settings[$key] ) ) {
					return $this->data_settings[$key];
				}
			} else {
				return $this->data_settings;
			}
		}
		
		function set_data_settings( $key = '', $value = '' ) {
			if ( empty( $key ) ) return;
			
			$this->init_data_settings(true);
			$this->data_settings[$key] = $value;
			
			return update_option('learndash_data_settings', $this->data_settings);
		}		
		
		public function admin_init() {
			
			$this->init_data_settings();

			if ( $this->check_upgrade_admin_notice() == true )
				add_action( 'admin_notices', array( $this, 'show_upgrade_admin_notice' ) );
		}
		
		public function show_upgrade_admin_notice() {
			if ( $this->admin_notice_shown != true ) {
				$this->admin_notice_shown = true;

				$admin_notice_message = sprintf( esc_html_x("LearnDash Notice: Please perform a %s. This is a required step to ensure accurate reporting.", 'placeholder: link to LearnDash Data Upgrade admin page', 'learndash'), '<a href="'.  admin_url('admin.php?page=learndash_data_upgrades') .'">'. esc_html__('LearnDash Data Upgrade', 'learndash') . '</a>' );
			
				?>
				<div id="ld-data-upgrade-notice-error" class="notice notice-info is-dismissible">
					<p><?php echo $admin_notice_message; ?></p>
				</div>
				<?php
			}
		}

		public function check_upgrade_admin_notice() {
			$show_admin_notice = false;
			
			if ( ( isset( $this->data_settings['user-meta-courses']['version'] ) ) 
			  && ( $this->data_settings['user-meta-courses']['version'] < LEARNDASH_SETTINGS_TRIGGER_UPGRADE_VERSION ) )
				$show_admin_notice = true;

			if ( ( isset( $this->data_settings['user-meta-quizzes']['version'] ) ) 
			  && ( $this->data_settings['user-meta-quizzes']['version'] < LEARNDASH_SETTINGS_TRIGGER_UPGRADE_VERSION ) )
				$show_admin_notice = true;
			
			return $show_admin_notice;
		}
		
		
		/**
		 * Register settings menu page
		 */
		public function admin_menu() {
			$this->settings_screen_id = add_submenu_page(
				$this->parent_menu_page_url,
				$this->settings_page_title,
				$this->settings_page_title,
				$this->menu_page_capability,
				$this->settings_page_id,
				array( $this, 'admin_page' )
			);
			add_action( 'load-'. $this->settings_screen_id, array( $this, 'on_load_panel' ) );
		}

		function admin_tabs( $admin_menu_section ) {			
			if ( $admin_menu_section == $this->parent_menu_page_url ) {
				
				learndash_add_admin_tab_item(
					$admin_menu_section,
					array(
						'id'			=> 	$this->settings_screen_id, //$this->settings_page_id,
						'link'			=> 	add_query_arg( array( 'page' => $this->settings_page_id ), 'admin.php' ),
						'name'			=> 	$this->settings_tab_title,
					),
					$this->settings_tab_priority
				);
			}
		}
		

		function on_load_panel() {

			wp_enqueue_style( 
				'learndash_style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
				array(), 
				LEARNDASH_SCRIPT_VERSION_TOKEN 
			);
			$learndash_assets_loaded['styles']['learndash_style'] = __FUNCTION__;

			wp_enqueue_style( 
				'sfwd-module-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array(), 
				LEARNDASH_SCRIPT_VERSION_TOKEN 
			);
			$learndash_assets_loaded['styles']['sfwd-module-style'] = __FUNCTION__;
			
			
			wp_enqueue_script( 
				'learndash-admin-settings-data-upgrades-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-settings-data-upgrades'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ), 
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true 
			);
			$learndash_assets_loaded['scripts']['learndash-admin-settings-data-upgrades-script'] = __FUNCTION__;
			
			$this->init_upgrade_actions();
		}

		function init_upgrade_actions() {
			
			$this->upgrade_actions = apply_filters('learndash_admin_settings_upgrades_register_actions', $this->upgrade_actions);			
		}

		public function admin_page() {
			?>
			<div id="learndash-settings" class="wrap">
				<h1><?php esc_html_e( 'Data Upgrades', 'learndash' ); ?></h1>
				<form method="post" action="options.php">
					<div class="sfwd_options_wrapper sfwd_settings_left">
						<div id="advanced-sortables" class="meta-box-sortables">
							<div id="sfwd-courses_metabox" class="postbox learndash-settings-postbox">
								<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'learndash' ); ?>"><br></div>
								<h3 class="hndle"><span><?php esc_html_e( 'Data Upgrades', 'learndash' ); ?></span></h3>
								<div class="inside">
									<div class="sfwd sfwd_options sfwd-courses_settings">

										<table id="learndash-data-upgrades" class="wc_status_table widefat" cellspacing="0">
										<?php
											foreach( $this->upgrade_actions as $upgrade_action_slug => $upgrade_action ) {
												$upgrade_action['instance']->show_upgrade_action();
											}
										?>
										</table>

									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
		}

		function set_last_run_info( $data = array() ) {
			$data_settings = array(
				'last_run' 	=>	 time(),
				'user_id'	=> 	get_current_user_id()
			);
			
			if ( isset( $data['total_count'] ) ) {
				$data_settings['total_count'] =	$data['total_count'];
			}
			
			// Set this to prevent the admin notice nag.
			$data_settings['version'] = LEARNDASH_SETTINGS_TRIGGER_UPGRADE_VERSION;
			
			$this->set_data_settings( $this->data_slug, $data_settings );
		}
		
		function get_last_run_info() {
			$last_run_info = '';
			
			$data_settings = $this->get_data_settings( $this->data_slug );

			if ( !empty( $data_settings ) ) {
				$user = get_user_by( 'id', $data_settings['user_id'] );
			
				 $last_run_info = sprintf(_x('Last run: %s by %s', 'placeholders: date/time, user name', 'learndash'), 			
					learndash_adjust_date_time_display($data_settings['last_run']),
					 $user->display_name); 
			} else {
			 	$last_run_info = esc_html__('Last run: none', 'learndash');
			}

			return $last_run_info;
		}
		
		function clear_previous_run_meta( $data = array() ) {
			global $wpdb;
			
			$wpdb->delete( 
				$wpdb->usermeta, 
				array( 'meta_key' => $this->transient_prefix . $this->data_slug ), 
				array( '%s' ) 
			);
		}		
						
		function do_data_upgrades( $post_data = array(), $reply_data = array() ) {
			
			$this->init_upgrade_actions();
			
			if ( ( isset( $post_data['slug'] ) ) && ( !empty( $post_data['slug'] ) ) ) {
				$post_data_slug = esc_attr( $post_data['slug'] );
				
				if ( isset( $this->upgrade_actions[$post_data_slug] ) ) {
					if ( isset( $post_data['data'] ) )
						$data = $post_data['data'];
					else
						$data = array();
					
					$reply_data = $this->upgrade_actions[$post_data_slug]['instance']->process_upgrade_action( $post_data );
				} 
			}
			return $reply_data;
		}
		
		function init_process_times() {
			$this->process_times['started'] = time();
			$this->process_times['limit'] = intval( ini_get( 'max_execution_time' ) );
			if ( empty( $this->process_times['limit'] ) ) $this->process_times['limit'] = 60;
		}
		
		function out_of_timer() {
			$this->process_times['current_time'] = time();			
			
			$this->process_times['ticks'] = $this->process_times['current_time'] - $this->process_times['started'];
			$this->process_times['percent'] = ($this->process_times['ticks'] / $this->process_times['limit']) * 100;

			// If we are over 80% of the allowed processing time or over 10 seconds then finish up and return
			if (( $this->process_times['percent'] >= LEARNDASH_PROCESS_TIME_PERCENT) || ($this->process_times['ticks'] > LEARNDASH_PROCESS_TIME_SECONDS))
				return true;
		
			return false;
		}
		
		function remove_transient( $transient_key = '' ) {
			if ( !empty( $transient_key ) ) {
				$options_key = $this->transient_prefix. $transient_key;
				$options_key = str_replace('-', '_', $options_key);
				return delete_option( $options_key );
			}
		}

		function get_transient( $transient_key = '' ) {
			if ( !empty( $transient_key ) ) {
				$options_key = $this->transient_prefix . $transient_key;
				$options_key = str_replace('-', '_', $options_key);
				return get_option( $options_key );
			}
		}
		
		function set_transient( $transient_key = '', $transient_data = '' ) {
			
			if ( !empty( $transient_key ) ) {
				$options_key = $this->transient_prefix . $transient_key;
				$options_key = str_replace('-', '_', $options_key);
			
				if ( !empty( $transient_data ) ) {
					update_option( $options_key, $transient_data );
				} else {
					delete_option( $options_key );
				}
			}
		}
		

		// End of functions
		
	}
}


// Go ahead and inlcude out User Meta Courses upgrade class

require_once( LEARNDASH_LMS_PLUGIN_DIR .'includes/admin/classes-data-uprades-actions/class-learndash-admin-data-upgrades-translations.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR .'includes/admin/classes-data-uprades-actions/class-learndash-admin-data-upgrades-group-leader-role.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR .'includes/admin/classes-data-uprades-actions/class-learndash-admin-data-upgrades-user-activity-db-table.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR .'includes/admin/classes-data-uprades-actions/class-learndash-admin-data-upgrades-user-meta-courses.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR .'includes/admin/classes-data-uprades-actions/class-learndash-admin-data-upgrades-user-meta-quizzes.php' );
add_action('plugins_loaded', function() {
	new Learndash_Admin_Data_Upgrades_Translations();
	new Learndash_Admin_Data_Upgrades_Group_Leader_Role();
	new Learndash_Admin_Data_Upgrades_User_Activity_DB_Table();
	new Learndash_Admin_Data_Upgrades_User_Meta_Courses();
	new Learndash_Admin_Settings_Upgrades_User_Meta_Quizzes();
});


function learndash_data_upgrades_ajax() {
	//error_log('_POST<pre>'. print_r($_POST, true) .'</pre>');

	$reply_data = array( 'status' => false);


	if ( isset( $_POST['data'] ) )
		$post_data = $_POST['data'];
	else
		$post_data = array();
		
	$ld_admin_settings_data_upgrades = Learndash_Admin_Settings_Data_Upgrades::get_instance();
	$reply_data['data'] = $ld_admin_settings_data_upgrades->do_data_upgrades( $post_data, $reply_data );
	
	if ( !empty( $reply_data ) )
		echo json_encode($reply_data);

	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_learndash-data-upgrades', 'learndash_data_upgrades_ajax' );


