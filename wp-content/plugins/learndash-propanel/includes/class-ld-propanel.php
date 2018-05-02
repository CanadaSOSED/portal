<?php
/**
 * Set up LearnDash ProPanel
 *
 * @package LearnDash_ProPanel
 * @since 2.0
 */

final class LearnDash_ProPanel {

	/**
	 * @var LearnDash_ProPanel The reference to *Singleton* instance of this class
	 */
	private static $instance;

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return LearnDash_ProPanel The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	  * Override class function for 'this'.
	  *
	  * This function handles out Singleton logic in 
	  * @return reference to current instance
	  */
	static function this() {
		return self::$instance;
	}

	/**
	 * LearnDash_ProPanel constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'reporting_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), apply_filters('ld_propanel_admin_enqueue_scripts_priority', 5 ) );
		add_action( 'enqueue_scripts', array( $this, 'scripts' ), apply_filters('ld_propanel_enqueue_scripts_priority', 5 ) );
		
		add_action( 'parse_request', array( $this, 'parse_request' ), 1 );
		
		add_filter( 'learndash_shortcodes_content_args', array( $this, 'add_ld_tinymce_shortcode' ) );
	}

	function reporting_page() {
		$menu_user_cap = '';
		
		if ( LearnDash_Dependency_Check_ProPanel::get_instance()->check_dependency_results()) {
		
			if ( learndash_is_admin_user() ) 
				$menu_user_cap = LEARNDASH_ADMIN_CAPABILITY_CHECK;
			else if ( learndash_is_group_leader_user() ) 
				$menu_user_cap = LEARNDASH_GROUP_LEADER_CAPABILITY_CHECK;
		
			if ( !empty( $menu_user_cap ) ) {
			
				$r_page = add_submenu_page(
					null,
					esc_html__( 'ProPanel Reporting', 'ld_propanel' ),
					esc_html__( 'ProPanel Reporting', 'ld_propanel' ),
					$menu_user_cap,
					'propanel-reporting',
					array( $this, 'admin_full_page_output' )
				);

				// Found out the following is needed needed mainly for group leaders to be able to see the full page reporting screen. Not really needed for admin users.  
				global $_registered_pages;
				$_registered_pages['admin_page_propanel-reporting'] = true;
			}
		}
	}

	function admin_full_page_output() {
		$this->init();

		ob_start();
		$container_type = 'full';
		include ld_propanel_get_template( 'ld-propanel-full-admin.php' );
		echo ob_get_clean();
	}
	
	function parse_request() {
		
		//$current_template = get_current_template();
		//error_log('current_template['. $current_template .']');
		
		//if ( is_page_template('ld-propanel-full-page.php') ) {
		//	error_log('ARE using the template');
		//} else {
		//	error_log('NOT using the template');
		//}
				
		// Check if we are doing the full page front-end ld_propanel template
		if ( ( !is_admin()) && ( isset( $_GET['ld_propanel'] ) ) ) {
			
			if ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) || ( current_user_can( 'propanel_widgets' ) ) ) { 
						
				$this->scripts(true);
			
				$template_full_page_css = ld_propanel_get_template( 'ld-propanel-full-page.css' );
				if ( !empty( $template_full_page_css ) ) {
					$template_full_page_css_url = learndash_template_url_from_path( $template_full_page_css );
					wp_enqueue_style( 'ld-propanel-full-page-style', $template_full_page_css_url, null, LD_PP_VERSION );
				}
			
				ob_start();
				include ld_propanel_get_template( 'ld-propanel-full-page.php' );
				echo ob_get_clean();
				die();
			}
		}
	}
			
	function add_ld_tinymce_shortcode( $shortcode_sections = array() ) {
		
		if ( is_admin() ) {
			
			$fields_args = array(
				'post_type'	=>	''
			);
			
			if ( ( isset( $_GET['post_type'] ) ) && ( !empty( $_GET['post_type'] ) ) ) {
				$fields_args['post_type'] = esc_attr( $_GET['post_type'] );
			}
			
			if ( $fields_args['post_type'] != 'sfwd-certificates' ) {
		
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-tinymce-courseinfo.php';
				$shortcode_sections['ld_propanel'] = new LearnDash_Shortcodes_Section_ld_propanel( array() );
			}
		}
		
		return $shortcode_sections;
		
	}	
			
	public function init() {
		$this->load_textdomain();
		$this->includes();
	}

	/**
	 * Notify user that LearnDash is required.
	 */
	public function notify_user_learndash_required() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'LearnDash is required to be activated before LearnDash ProPanel can work properly.', 'ld_propanel' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Load ProPanel
	 */
	private function includes() {
		if ( LearnDash_Dependency_Check_ProPanel::get_instance()->check_dependency_results()) {
		
			if ( is_admin() ) {
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-base-widget.php';

				// Support for LearnDash Translation admin panel
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-translations-propanel.php';
	
				// ProPanel Overview
				if ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) ) { 
					require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-overview.php';
					$this->overview_widget = new LearnDash_ProPanel_Overview();
				}
	
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-filtering.php';
				$this->filtering_widget = new LearnDash_ProPanel_Filtering();
	
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-reporting.php';
				$this->reporting_widget = new LearnDash_ProPanel_Reporting();
		
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-activity.php';
				$this->activity_widget = new LearnDash_ProPanel_Activity();

				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-progress-chart.php';
				$this->progress_chart_widget = new LearnDash_ProPanel_Progress_Chart();

				//require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-trends.php';
				//$this->trends_widget = new LearnDash_ProPanel_Trends();

				require_once LD_PP_PLUGIN_DIR . 'includes/functions.php';

				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-shortcodes.php';

				LearnDash_ProPanel_Shortcode::get_instance();

				//LearnDash_ProPanel_Shortcodes_Filtering::get_instance();
				
				//if ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) ) { 
				//	LearnDash_ProPanel_Shortcodes_Overview::get_instance();				
				//}
				
				//LearnDash_ProPanel_Shortcodes_Activity::get_instance();				
				//LearnDash_ProPanel_Shortcodes_Reporting::get_instance();
				//LearnDash_ProPanel_Shortcodes_Progress_Chart::get_instance();

			} else {
				
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-base-widget.php';

				// ProPanel Overview
				if ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) ) { 
					require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-overview.php';
					$this->overview_widget = new LearnDash_ProPanel_Overview();
				}

				// ProPanel Filtering
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-filtering.php';
				$this->filtering_widget = new LearnDash_ProPanel_Filtering();

				// ProPanel Reporting
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-reporting.php';
				$this->reporting_widget = new LearnDash_ProPanel_Reporting();

				// ProPanel Activity
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-activity.php';
				$this->activity_widget = new LearnDash_ProPanel_Activity();

				// ProPanel Charts
				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-progress-chart.php';
				$this->progress_chart_widget = new LearnDash_ProPanel_Progress_Chart();

				require_once LD_PP_PLUGIN_DIR . 'includes/functions.php';

				require_once LD_PP_PLUGIN_DIR . 'includes/class-ld-propanel-shortcodes.php';

				LearnDash_ProPanel_Shortcode::get_instance();

				//LearnDash_ProPanel_Shortcodes_Filtering::get_instance();
				
				//if ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) ) { 
				//	LearnDash_ProPanel_Shortcodes_Overview::get_instance();
				//}
				
				//LearnDash_ProPanel_Shortcodes_Activity::get_instance();				
				//LearnDash_ProPanel_Shortcodes_Reporting::get_instance();
				//LearnDash_ProPanel_Shortcodes_Progress_Chart::get_instance();
				//LearnDash_ProPanel_Shortcodes_Link::get_instance();
			}
		}
	}

	/**
	 * Load ProPanel Text Domain
	 */
	private function load_textdomain() {
		load_plugin_textdomain( 'ld_propanel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register scripts for any widgets that may need to enqueue them.
	 */
	public function scripts( $force_load_scripts = false ) {
		if ( LearnDash_Dependency_Check_ProPanel::get_instance()->check_dependency_results()) {
			
			$is_dashboard = false;
				
			if ( is_admin() ) {
				$screen = get_current_screen();
				if ( in_array( $screen->id, array( 'dashboard', 'dashboard_page_propanel-reporting' ) ) ) {
					$force_load_scripts = true;
					$is_dashboard = true;
				}
			}

			if ( true === $force_load_scripts ) {

				$ld_script_prereq = array( 'jquery' );
			
				// For now these are only loaded on admin Dashboard
				//if ( is_admin() ) {
					//wp_register_script( 'ld-propanel-chart-script', LD_PP_PLUGIN_URL . 'assets/js/vendor/Chart.js', array( 'jquery'), LD_PP_VERSION, true );
					//$ld_script_prereq[] = 'ld-propanel-chart-script';
					wp_register_script( 'ld-propanel-chart-script', LD_PP_PLUGIN_URL . 'assets/js/vendor/Chart.js', array( 'jquery' ), LD_PP_VERSION, false );
					$ld_script_prereq[] = 'ld-propanel-chart-script';

					wp_register_script( 'ld-propanel-select2-script', LD_PP_PLUGIN_URL . 'assets/js/vendor/select2.js', array( 'jquery' ), '4.0.3', true );
					$ld_script_prereq[] = 'ld-propanel-select2-script';
					//wp_register_style( 'ld-propanel-select2-style', LD_PP_PLUGIN_URL . 'assets/css/vendor/select2.min.css' );
					wp_enqueue_style( 'ld-propanel-select2-style', LD_PP_PLUGIN_URL . 'assets/css/vendor/select2.min.css' );
					//}
			
				wp_register_script( 'ld-propanel-script', LD_PP_PLUGIN_URL . 'assets/js/ld-propanel.js', $ld_script_prereq, LD_PP_VERSION, true );

				$pager_values = ld_propanel_get_pager_values();
				if ( empty( $pager_values ) ) {
					$pager_values = array( get_option( 'posts_per_page' ) );
				}
				wp_localize_script( 'ld-propanel-script', 'ld_propanel_settings', array( 
						'nonce' 	=> 	wp_create_nonce( 'ld-propanel' ), 
						'ajaxurl'	=>	admin_url( 'admin-ajax.php' ),
						'spinner_admin_img' => admin_url( '/images/spinner.gif' ),
						'is_dashboard' => $is_dashboard,
						'is_debug'	=> false,
						'template_load_delay' => apply_filters('ld_propanel_js_template_load_delay', 1000),
						'default_per_page' => $pager_values[0]
					) 
				);
				wp_enqueue_script( 'ld-propanel-script' );

				wp_enqueue_style( 'dashicons' );
				
				wp_register_style( 'ld-propanel-style', LD_PP_PLUGIN_URL . 'assets/css/ld-propanel.css', null, LD_PP_VERSION );
				wp_enqueue_style( 'ld-propanel-style' );
				
				global $learndash_assets_loaded;
				if ( !isset( $learndash_assets_loaded['scripts']['learndash_template_script_js'] ) ) {
					$filepath = SFWD_LMS::get_template( 'learndash_template_script.js', null, null, true );
					if ( !empty( $filepath ) ) {
						wp_enqueue_script( 'learndash_template_script_js', learndash_template_url_from_path( $filepath ), array( 'jquery' ), LEARNDASH_SCRIPT_VERSION_TOKEN, true );
						$learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;

						$data = array();
						$data['ajaxurl'] = admin_url('admin-ajax.php');
						$data = array( 'json' => json_encode( $data ) );
						wp_localize_script( 'learndash_template_script_js', 'sfwd_data', $data );
					}
				}
				
				LD_QuizPro::showModalWindow();
			}
		}
	}
}