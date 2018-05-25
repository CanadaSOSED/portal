<?php

if (!class_exists('Learndash_Admin_Course_Edit')) {
	class Learndash_Admin_Course_Edit {
		
		private $courses_post_type = 'sfwd-courses';
		private $selector_post_types = array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' );
		
		private $course_id = 0;

		private $use_course_builder = false;
		private $course_builder = null;
			
		function __construct() {
			// Hook into the on-load action for our post_type editor
			add_action( 'load-post.php', 			array( $this, 'on_load') );
			add_action( 'load-post-new.php', 		array( $this, 'on_load') );
		}
		
		function on_load($something = '') {
			global $learndash_assets_loaded, $typenow;	// Contains the same as $_GET['post_type]
			
			if ( ( empty( $typenow ) ) || ( $typenow != $this->courses_post_type ) )  return;

			wp_enqueue_script( 
				'learndash-admin-binary-selector-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ),
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true
			);
			$learndash_assets_loaded['styles']['learndash-admin-binary-selector-script'] = __FUNCTION__;

			wp_enqueue_style( 
				'learndash-admin-binary-selector-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array( ),
				LEARNDASH_SCRIPT_VERSION_TOKEN
			);
			$learndash_assets_loaded['styles']['learndash-admin-binary-selector-style'] = __FUNCTION__;
			
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes' ) {
				$this->use_course_builder = true;
				
				if ( apply_filters('learndash_show_course_builder', $this->use_course_builder ) === true ) {
					$this->course_builder = new Learndash_Admin_Metabox_Course_Builder();
					$this->course_builder->on_load();
				}
			}
							
			// Add Metabox and hook for saving post metabox
			add_action( 'add_meta_boxes', 			array( $this, 'add_metaboxes' ) );
			add_action( 'save_post', 				array( $this, 'save_metaboxes'), 20, 3 );
		}
				
		/**
		 * Register Groups meta box for admin
		 *
		 * Managed enrolled groups, users and group leaders
		 * 
		 * @since 2.1.2
		 */
		function add_metaboxes( $post_type = '' ) {
			
			if ( ( !empty( $post_type ) ) && ( $post_type == $this->courses_post_type ) ) {			

				/** 
				 * @since 2.5
				 * Add Course Builder metabox
				 */
				if ( apply_filters('learndash_show_course_builder', $this->use_course_builder ) === true ) {
					add_meta_box(
						'learndash_course_builder',
						sprintf( esc_html_x( 'LearnDash %s Builder', 'Course Builder', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
						array( $this->course_builder, 'course_builder_box' ),
						$this->courses_post_type
					);
				}
				
				/** 
				 * @since 2.3.1
				 * Check if we have defined groups before showing the meta box
				 */
				$group_query_args = array( 
					'post_type' 		=> 	'groups', 
					'post_status' 		=> 	'publish',  
					'posts_per_page' 	=> 	1,
				);
	
				$group_query = new WP_Query( $group_query_args );
				if ( ( $group_query instanceof WP_Query) && ( !empty( $group_query->posts ) ) ) {
	
					add_meta_box(
						'learndash_couse_groups',
						sprintf( esc_html_x( 'LearnDash %s Groups', 'LearnDash Course Groups', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
						array( $this, 'course_groups_page_box' ),
						$this->courses_post_type
					);
				}

				/** 
				 * @since 2.5
				 * Check if we have defined courses before showing the meta box
				 */
				/*
				$course_query_args = array( 
					'post_type' 		=> 	'sfwd-courses', 
					'post_status' 		=> 	'any',
					'posts_per_page' 	=> 	1,
				);
	
				$course_query = new WP_Query( $course_query_args );
				if ( ( $course_query instanceof WP_Query) && ( !empty( $course_query->posts ) ) ) {
	
					add_meta_box(
						'learndash_couse_users',
						sprintf( esc_html_x( 'LearnDash %s Users', 'LearnDash Course Users', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
						array( $this, 'course_users_page_box' ),
						$this->courses_post_type
					);
				}
				*/
			}
		}


		/**
		 * Prints content for Groups meta box for admin
		 *
		 * @since 2.1.2
		 * 
		 * @param  object $post WP_Post
		 * @return string 		meta box HTML output
		 */
		function course_groups_page_box( $post ) {
			$this->course_id = $post->ID;

			// Use nonce for verification
			wp_nonce_field( 'learndash_course_groups_nonce_'. $this->course_id, 'learndash_course_groups_nonce' );
			
			?>
			<div id="learndash_course_groups_page_box" class="learndash_course_groups_page_box">
			<?php
				$ld_binary_selector_course_groups = new Learndash_Binary_Selector_Course_Groups(
					array(
						'html_title'	=>	'',
						'course_id'		=>	$this->course_id,
						'selected_ids'	=>	learndash_get_course_groups( $this->course_id, true ),
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_course_groups->show();
			?>
			</div>
			<?php 
		}

		/**
		 * Prints content for Groups meta box for admin
		 *
		 * @since 2.1.2
		 * 
		 * @param  object $post WP_Post
		 * @return string 		meta box HTML output
		 */
		function course_users_page_box( $post ) {
			$this->course_id = $post->ID;

			$course_price_type = get_course_meta_setting( $this->course_id, 'course_price_type' );
			if ( $course_price_type != 'open' ) {
				$selected_user_ids = array();
				$metabox_description = '';
				
				$course_users_binary_args = array(
					'html_title'	=>	'',
					'course_id'		=>	$this->course_id,
					'search_posts_per_page' => 100
				);

				if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' ) === 'yes' ) {
					$exclude_admin = true;
					$course_users_binary_args['role__not_in'] = 'Administrator';
					$metabox_description .= esc_html__('Admininstrator users are not shown because they are auto-enrolled.', 'learndash');
					
				} else {
					$exclude_admin = false;
				}

				$course_users_query = learndash_get_users_for_course( $this->course_id, array( ), $exclude_admin );
				if ( $course_users_query instanceof WP_User_Query ) {	
					$selected_user_ids = $course_users_query->get_results();
					
					if ( !empty( $selected_user_ids ) ) {
						$selected_user_ids = array_map( 'intval', $selected_user_ids );
						
						$course_groups_users = get_course_groups_users_access( $this->course_id );
						if ( !empty( $course_groups_users ) ) {
							$course_users_binary_args['excluded_ids'] = $course_groups_users;
							$selected_user_ids = array_diff( $selected_user_ids, $course_groups_users );
						}
					}
					$course_users_binary_args['selected_ids'] = $selected_user_ids;
				}

				// Use nonce for verification
				wp_nonce_field( 'learndash_course_users_nonce_'. $this->course_id, 'learndash_course_users_nonce' );

				if ( !empty( $metabox_description ) ) $metabox_description .= ' ';
				$metabox_description .= sprintf( esc_html_x('Users enrolled via Groups using this %s are excluded from the listings below and should be manage via the Group admin screen.', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) );
			
				?>
				<div id="learndash_course_users_page_box" class="learndash_course_users_page_box">
				<?php
					if ( !empty( $metabox_description ) ) {
						?><p><?php echo $metabox_description; ?></p><?php
					}
					$ld_binary_selector_course_users = new Learndash_Binary_Selector_Course_Users( $course_users_binary_args );
					$ld_binary_selector_course_users->show();
				?>
				</div>
				<?php 
			} else {
				?><p><?php echo sprintf( esc_html_x( 'The %s price type is set to "open". This means ALL are automatically enrolled.', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></p><?php
			}
		}
		
		function save_metaboxes( $post_id, $post, $update ) {
		
			// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// If this is just a revision, don't send the email.
			if ( wp_is_post_revision( $post_id ) )
				return;
			
			$post_type = get_post_type( $post_id );	
			if (( empty( $post_type ) ) || ( $this->courses_post_type != $post_type )) {
				return;
			}
			
			// Check permissions
			if ( ! current_user_can( 'edit_courses', $post_id ) ) {
				return;
			}
						
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			
			if ( ( isset( $_POST['learndash_course_groups_nonce'] ) ) && ( wp_verify_nonce( $_POST['learndash_course_groups_nonce'], 'learndash_course_groups_nonce_'. $post_id ) ) ) {

				if ( ( isset( $_POST['learndash_course_groups'] ) ) && ( isset( $_POST['learndash_course_groups'][$post_id] ) ) && ( !empty( $_POST['learndash_course_groups'][$post_id] ) ) ) {
					$course_groups = (array)json_decode( stripslashes( $_POST['learndash_course_groups'][$post_id] ) );
					learndash_set_course_groups( $post_id, $course_groups );
				}
			}
			
			if ( ( isset( $_POST['learndash_course_users_nonce'] ) ) && ( wp_verify_nonce( $_POST['learndash_course_users_nonce'], 'learndash_course_users_nonce_'. $post_id ) ) ) {
				//error_log('_POST<pre>'. print_r($_POST, true) .'</pre>');

				if ( ( isset( $_POST['learndash_course_users'] ) ) && ( isset( $_POST['learndash_course_users'][$post_id] ) ) && ( !empty( $_POST['learndash_course_users'][$post_id] ) ) ) {
					$course_users = (array)json_decode( stripslashes( $_POST['learndash_course_users'][$post_id] ) );
					//error_log('course_users<pre>'. print_r($course_users, true) .'</pre>');
					learndash_set_users_for_course( $post_id, $course_users );
				}
			} 

			// Save Course Builder 
			// Within CB will be security checks 
			if ( apply_filters('learndash_show_course_builder', $this->use_course_builder ) === true ) {
				$this->course_builder->save_course_builder( $post_id, $post, $update );
			}
		}
		
		// End of functions
	}
}
