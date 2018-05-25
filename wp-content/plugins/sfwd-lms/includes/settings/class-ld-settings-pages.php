<?php
if ( !class_exists( 'LearnDash_Settings_Page' ) ) {
	abstract class LearnDash_Settings_Page {

		protected static $_instances = array();

		// Match the parent menu below LearnDash main menu. This will be the URL as in 
		// edit.php?post_type=sfwd-courses, admin.php?page=learndash-lms-reports, admin.php?page=learndash_lms_settings
		protected $parent_menu_page_url	=	'';
		
		// Match the user capability to view this page
		protected $menu_page_capability	=	LEARNDASH_ADMIN_CAPABILITY_CHECK;

		// Match the WP Screen ID. DO NOT SET. This is set when from add_submenu_page(). 
		// The value set via WP will be somethiing like 'sfwd-courses_page_courses-options' for the URL admin.php?page=courses-options 
		// because it will reside within the sfwd-courses submenu
		protected $settings_screen_id 	= 	'';
		
		// Match the URL 'page=' parameter value. 
		// For example admin.php?page=learndash-lms-reports value will be 'learndash-lms-reports'
		protected $settings_page_id 	= 	'';
				
		// Title for page <h1></h1> string
		protected $settings_page_title 	= 	'';
	
		// Title for tab string
		protected $settings_tab_title 	= 	'';
	
		// Priority for tab
		protected $settings_tab_priority=	30;
	
	
		// The number of columns to show. Most admin screens will be 2. But we set to 1 for the initial.
		protected $settings_columns		=	1;

		function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			
			add_action( 'learndash_admin_tabs_set', array( $this, 'admin_tabs' ), 10 );

			if ( empty( $this->settings_tab_title ) )
				$this->settings_tab_title = $this->settings_page_title;			
		}

		final public static function get_page_instance( $page_key = '' ) {
			if ( !empty( $page_key ) ) {
				if ( isset( self::$_instances[$page_key] ) ) {
					return self::$_instances[$page_key];
				}
			}
		}

		final public static function add_page_instance() {
			$sectionClass = get_called_class();

			if ( !isset( self::$_instances[$sectionClass] ) ) {
				self::$_instances[$sectionClass] = new $sectionClass();
			}
		}
		

		function admin_init() {
			do_action( 'learndash_settings_page_init', $this->settings_page_id );
		}
		
		function admin_menu( ) {
			if ( !$this->settings_screen_id ) {
				$this->settings_screen_id = add_submenu_page(
					$this->parent_menu_page_url,
					$this->settings_page_title,
					$this->settings_page_title,
					$this->menu_page_capability,
					$this->settings_page_id,
					array( $this, 'show_settings_page' )
				);
			}
			add_action( "load-". $this->settings_screen_id, array( $this, 'load_settings_page') );
		}

		function admin_tabs( $admin_menu_section ) {
			if ( $admin_menu_section == $this->parent_menu_page_url ) {
				
				learndash_add_admin_tab_item(
					$this->parent_menu_page_url,
					array(
						'id'			=> 	$this->settings_screen_id,
						'link'			=> 	add_query_arg( array( 'page' => $this->settings_page_id ), 'admin.php' ),
						'name'			=> 	!empty( $this->settings_tab_title )  ? $this->settings_tab_title : $this->settings_page_title,
					), 
					$this->settings_tab_priority
				);
			}
		}

		function load_settings_page() {
			global $learndash_assets_loaded;
			
			if ( defined( 'LEARNDASH_SETTINGS_SECTION_TYPE' ) && ( LEARNDASH_SETTINGS_SECTION_TYPE == 'metabox' ) ) {
				wp_enqueue_script( 'common' );
				wp_enqueue_script( 'wp-lists' );
				wp_enqueue_script( 'postbox' );

				do_action( 'learndash_add_meta_boxes', $this->settings_screen_id );
				
				add_action( "admin_footer-". $this->settings_screen_id, array( $this, 'load_footer_scripts' ) );
				add_filter( 'screen_layout_columns', array( $this, 'screen_layout_column' ), 10, 2 );
			}
			
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
				'sfwd-module-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ), 
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true 
			);
			$learndash_assets_loaded['scripts']['sfwd-module-script'] = __FUNCTION__;

			wp_localize_script( 'sfwd-module-script', 'sfwd_data', array() );

			do_action('learndash-settings-page-load', $this->settings_screen_id );
		}

		function screen_layout_column( $columns, $screen ) {
			if ( $screen == $this->settings_screen_id ) {
				$columns[$this->settings_screen_id] = $this->settings_columns;
			}
			return $columns;
		}
	
		function load_footer_scripts() {
			?>
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready( function($) {
					// toggle
					$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
					postboxes.add_postbox_toggles( '<?php echo $this->settings_screen_id; ?>' );
					// display spinner
					$('#fx-smb-form').submit( function() {
						$('#publishing-action .spinner').css('display','inline');
					});
					// confirm before reset
					$('.learndash-settings-page-wrap .submitdelete').on('click', function() {
						var confirm_message = $(this).data('confirm');
						if (typeof confirm_message !== 'undefined') {
							return confirm( confirm_message );
						}
					});
				});
				//]]>
			</script>
			<?php
		}

		function show_settings_page() {
			if ( defined( 'LEARNDASH_SETTINGS_SECTION_TYPE' ) && ( LEARNDASH_SETTINGS_SECTION_TYPE == 'metabox' ) ) {
				?>
				<div class="wrap learndash-settings-page-wrap">

					<?php settings_errors(); ?>

					<?php do_action('learndash_settings_page_before_title', $this->settings_screen_id ); ?>
					<?php echo $this->get_admin_page_title() ?>
					<?php do_action('learndash_settings_page_after_title', $this->settings_screen_id ); ?>
					
					<?php do_action('learndash_settings_page_before_form', $this->settings_screen_id ); ?>
					<?php echo $this->get_admin_page_form( true ); ?>
					<?php do_action('learndash_settings_page_inside_form_top', $this->settings_screen_id ); ?>

						<?php settings_fields( $this->settings_page_id );  ?>
						<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
						<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

						<div id="poststuff">
							<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
								<div id="postbox-container-1" class="postbox-container">
									<?php do_meta_boxes( $this->settings_screen_id, 'side', null ); ?>
								</div>
								<div id="postbox-container-2" class="postbox-container">
									<?php do_action('learndash_settings_page_before_metaboxes', $this->settings_screen_id ); ?>
									<?php do_meta_boxes( $this->settings_screen_id, 'normal', null ); ?>
									<?php do_meta_boxes( $this->settings_screen_id, 'advanced', null ); ?>
									<?php do_action('learndash_settings_page_after_metaboxes', $this->settings_screen_id ); ?>
								</div>
							</div>
							<br class="clear">
						</div>
					<?php do_action('learndash_settings_page_inside_form_bottom', $this->settings_screen_id ); ?>
					<?php echo $this->get_admin_page_form( false ); ?>
					<?php do_action('learndash_settings_page_after_form', $this->settings_screen_id ); ?>
				</div>
				<?php
				
			} else {
				?>
				<div class="wrap learndash-settings-page-wrap">
					<?php settings_errors(); ?>
			
					<?php echo $this->get_admin_page_title() ?>

					<?php echo $this->get_admin_page_form( true ); ?>
				    <?php
						// This prints out all hidden setting fields
						settings_fields( $this->settings_page_id );

						do_settings_sections( $this->settings_page_id );
				    ?>
				    <?php submit_button( esc_html__( 'Save Changes', 'learndash' ) ) ; ?>
					<?php echo $this->get_admin_page_form( false ); ?>
				</div>
				<?php
			}
		}
		
		function get_admin_page_title() {
			return apply_filters( 'learndash_admin_page_title', '<h1>'. esc_html( get_admin_page_title() ) . '</h1>' );
		}
		
		function get_admin_page_form( $start = true ) {
			if ( $start === true ) {
				return apply_filters( 'learndash_admin_page_form', '<form id="learndash-settings-page-form" method="post" action="options.php">', $start );
			} else {
				return apply_filters( 'learndash_admin_page_form', '</form>', $start );
			}
		}
		
		// End of functions
	}
}
