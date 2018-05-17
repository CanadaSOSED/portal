<?php
if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( !class_exists( 'LearnDash_Settings_Page_Course_Builder_Single' ) ) ) {
	class LearnDash_Settings_Page_Course_Builder_Single extends LearnDash_Settings_Page {

		private $update_success = false;
		
		function __construct() {

			$this->parent_menu_page_url		=	'edit.php?post_type=sfwd-courses';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'courses-builder';
			$this->settings_page_title 		= 	sprintf( esc_html_x( '%s Builder', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') );
			$this->settings_tab_title		=	$this->settings_page_title;
			
			add_action( 'load-sfwd-courses_page_courses-builder', 	array( $this, 'on_load') );			
			
			add_filter( 'post_row_actions', array( $this, 'learndash_course_row_actions'), 20, 2 );
			add_filter( 'learndash_admin_tab_sets', array( $this, 'admin_tab_sets' ), 15, 2 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'learndash_settings_page_after_title', array( $this, 'settings_page_after_title' ) );
			parent::__construct(); 
		}
		
		function settings_page_after_title( $settings_screen_id = '' ) {
			if ( $this->settings_screen_id == $settings_screen_id ) {
				if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
					$course_id = intval( $_GET['course_id'] );
					$course_post = get_post( $course_id );
					if ( ( $course_post ) && ( is_a( $course_post, 'WP_Post' ) ) && ( $course_post->post_type == 'sfwd-courses' )  ) {
						?>
						<div id="course-builder-title-box">
							<h2 class="course-title"><?php echo $course_post->post_title; ?></h2>
							<p class="course-links">
								<strong><?php esc_html_e('Permalink:') ?></strong> <a href="<?php echo get_permalink( $course_id ) ?>"><?php echo get_permalink( $course_id ) ?></a><br />
								<strong><?php esc_html_e('Edit:') ?></strong> <a href="<?php echo get_edit_post_link( $course_id ) ?>"><?php echo get_edit_post_link( $course_id ) ?></a>
							</p>
						</div>
						<?php
					}
				}
				
			}
		}
		
		// Override the settings form as we are not really handling settings to be sent through options.php
		function get_admin_page_form( $start = true ) {
			if ( $start === true ) {
				return apply_filters( 'learndash_admin_page_form', '<form id="learndash-settings-page-form" method="post">', $start );
			} else {
				return apply_filters( 'learndash_admin_page_form', '</form>', $start );
			}
		}
		
		function on_load() {
			if ( is_admin() ) {
				// If the Course Builder screen is being shown...
				$current_screen = get_current_screen();
				if ( $current_screen->id == 'sfwd-courses_page_courses-builder' ) {
					// ...but the 'course_id' query parameters is not found...
					if ( ( !isset( $_GET['course_id'] ) ) || ( empty( $_GET['course_id'] ) ) ) {
						// ...then redirect back to the courses listin screen.
						$courses_list_url = add_query_arg('post_type', 'sfwd-courses', admin_url('edit.php' ) );
						wp_redirect( $courses_list_url );
					} else {
						$this->cb = new Learndash_Admin_Metabox_Course_Builder();
						$this->save_cb_metabox();
						$this->cb->on_load();
					}
				}
			}
		}
		
		function save_cb_metabox() {
			if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) && ( isset($_POST['learndash_course_builder'] ) ) && ( !empty( $_POST['learndash_course_builder'] ) ) ) {
				$course_id = intval( $_GET['course_id'] );
				
				$course_post = get_post( $course_id );
				if ( ( $course_post ) && ( is_a( $course_post, 'WP_Post' ) ) && ( $course_post->post_type == 'sfwd-courses' )  ) {
					$this->cb->save_course_builder( $course_id, $course_post, true );
					$this->update_success = true;
				}
			}
			return;
			
		}
		
		function admin_notice() {
			if ( $this->update_success === true ) {
				?>
				<div class="notice notice-success is-dismissible"> 
					<p><strong><?php esc_html_e('Settings saved.') ?></strong></p>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.') ?></span>
					</button>
				</div>
				<?php
			}
		}
		
		function learndash_course_row_actions( $row_actions = array(), $course_post = null ) {
			global $typenow, $pagenow;
	
			if ( ( $pagenow == 'edit.php') && ( $typenow == 'sfwd-courses' ) && ( is_a( $course_post, 'WP_Post' ) ) ) {
				if ( ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes' ) && ( !isset( $row_actions['ld-course-builder'] ) ) ) {
					if ( apply_filters( 'learndash_show_course_builder_row_actions', true, $course_post ) === true ) {
						$course_label = sprintf( esc_html_x( 'Use %s Builder', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') );
				
						$row_actions['ld-course-builder'] = sprintf(
							'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
							add_query_arg(array('page' => 'courses-builder','course_id' => $course_post->ID), admin_url('admin.php')) ,
							esc_attr( $course_label ),
							esc_html__('Builder', 'learndash')
						);
					}
				}
			}	
	
			return $row_actions;
		}
		
		
		// Filter the LearnDash admin menu (tabs). We remove the 'Course Builder' tab until needed. 
		function admin_tab_sets( $admin_menu_set = array(), $admin_menu_key = '' ) {
			
			if ( $admin_menu_key == 'edit.php?post_type=sfwd-courses' ) {
				if ( ( !isset( $_GET['course_id'] ) ) || ( empty( $_GET['course_id'] ) ) ) {
					// If we don't have the 'course_id' URL parameter then we remove the tab. 
					foreach( $admin_menu_set as $menu_idx => $menu_item ) {
						if ( $menu_item['id'] == 'sfwd-courses_page_courses-builder' ) {
							unset( $admin_menu_set[$menu_idx] );
							break;
						}
					}
				} else {
					// Else of we do have the 'course_id' URL parameter we include this in the tab URL.
					foreach( $admin_menu_set as $menu_idx => &$menu_item ) {
						if ( $menu_item['id'] == 'sfwd-courses_page_courses-builder' ) {
							$menu_item['link'] = add_query_arg('course_id', intval( $_GET['course_id'] ), $menu_item['link'] );
							break;
						}
					}
				}
			}
			
			return $admin_menu_set;
		}
	}
}
add_action( 'learndash_settings_pages_init', function() {
	LearnDash_Settings_Page_Course_Builder_Single::add_page_instance();
} );
