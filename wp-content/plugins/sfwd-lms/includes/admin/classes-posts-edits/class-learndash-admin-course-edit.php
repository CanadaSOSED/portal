<?php
/**
 * LearnDash Admin Course Edit Class.
 *
 * @package LearnDash
 * @subpackage Admin
 */

if ( ( class_exists( 'Learndash_Admin_Post_Edit' ) ) && ( ! class_exists( 'Learndash_Admin_Course_Edit' ) ) ) {
	/**
	 * Class for LearnDash Admin Course Edit.
	 */
	class Learndash_Admin_Course_Edit extends Learndash_Admin_Post_Edit {

		/**
		 * Object level variable for current Course ID being edited.
		 *
		 * @var integer $course_id
		 */
		private $course_id = 0;

		/**
		 * Object level flag to contain setting is Course Builder
		 * is to be used.
		 *
		 * @var boolean $use_course_builder
		 */
		private $use_course_builder = false;

		/**
		 * Instance of Course Builder Metabox object userd
		 * throughout this class.
		 *
		 * @var object $course_builder Instance of Learndash_Admin_Metabox_Course_Builder
		 */
		private $course_builder = null;

		/**
		 * Public constructor for class.
		 */
		public function __construct() {
			$this->post_type = learndash_get_post_type_slug( 'course' );
			parent::__construct();
		}

		/**
		 * On Load handler function for this post type edit.
		 * This function is called by a WP action when the admin
		 * page 'post.php' or 'post-new.php' are loaded.
		 */
		public function on_load() {
			if ( $this->post_type_check() ) {
				parent::on_load();

				if ( LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes' ) {
					$this->use_course_builder = true;

					if ( apply_filters( 'learndash_show_course_builder', $this->use_course_builder ) === true ) {
						$this->course_builder = Learndash_Admin_Metabox_Course_Builder::add_instance( 'Learndash_Admin_Metabox_Course_Builder' );
						$this->course_builder->builder_on_load();
					}
				}
			}
		}

		/**
		 * Register Groups meta box for admin
		 * Managed enrolled groups, users and group leaders
		 *
		 * @since 2.1.2
		 * @param string $post_type Port Type being edited.
		 */
		public function add_metaboxes( $post_type = '', $post = null ) {

			if ( $this->post_type_check( $post_type ) ) {
				/**
				 * Add Course Builder metabox.
				 *
				 * @since 2.5
				 */
				if ( true === apply_filters( 'learndash_show_course_builder', $this->use_course_builder ) ) {
					add_meta_box(
						'learndash_course_builder',
						sprintf(
							// translators: placeholder: Course.
							esc_html_x( 'LearnDash %s Builder', 'Course Builder', 'learndash' ),
							LearnDash_Custom_Label::get_label( 'course' )
						),
						array( $this->course_builder, 'show_builder_box' ),
						$this->post_type,
						'normal',
						'high'
					);
				}

				parent::add_metaboxes( $post_type );

				/**
				 * Check if we have defined groups before showing the meta box.
				 *
				 * @since 2.3.1
				 */
				$group_query_args = array(
					'post_type'      => 'groups',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
				);

				$group_query = new WP_Query( $group_query_args );
				if ( ( is_a( $group_query, 'WP_Query' ) ) && ( ! empty( $group_query->posts ) ) ) {
					add_meta_box(
						'learndash_course_groups',
						sprintf(
							// translators: placeholder: Course.
							esc_html_x( 'LearnDash %s Groups', 'LearnDash Course Groups', 'learndash' ),
							LearnDash_Custom_Label::get_label( 'course' )
						),
						array( $this, 'course_groups_page_box' ),
						$this->post_type,
						'normal',
						'high'
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
						'learndash_course_users',
						sprintf( esc_html_x( 'LearnDash %s Users', 'LearnDash Course Users', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
						array( $this, 'course_users_page_box' ),
						$this->post_type
					);
				}
				*/

				global $wp_meta_boxes;
				if ( isset( $wp_meta_boxes[ $this->post_type ]['normal']['high']['learndash_course_builder'] ) ) {
					$course_builder_metabox = $wp_meta_boxes[ $this->post_type ]['normal']['high']['learndash_course_builder'];
					unset( $wp_meta_boxes[ $this->post_type ]['normal']['high']['learndash_course_builder'] );
					$wp_meta_boxes[ $this->post_type ]['normal']['high'] = array_merge( 
						array( 'learndash_course_builder' => $course_builder_metabox ), 
						$wp_meta_boxes[ $this->post_type ]['normal']['high'] 
					);
				}

				/**
				 * Check if the editor is classic or new Gutenberg Block editor and hide non-important metaboxes
				 */
				if ( ( $post ) && ( is_a( $post, 'WP_Post' ) ) ) {
					$user_closed_postboxes = get_user_meta( get_current_user_id(), 'closedpostboxes_' . $this->post_type, true );
					if ( ( is_string( $user_closed_postboxes ) ) && ( '' === $user_closed_postboxes ) ) {
						if ( ( function_exists( 'use_block_editor_for_post' ) ) && ( use_block_editor_for_post( $post ) ) ) {
							$all_postboxes = array(
								'sfwd-courses',
								'learndash_course_groups',
							);
						} else {
							$all_postboxes = array(
								//'sfwd-courses',
								'learndash_course_groups',
							);
						}

						update_user_meta( get_current_user_id(), 'closedpostboxes_' . $this->post_type, $all_postboxes );
					}
				}
			}
		}

		/**
		 * Prints content for Groups meta box for admin
		 *
		 * @since 2.1.2
		 *
		 * @param object $post WP_Post.
		 */
		public function course_groups_page_box( $post ) {
			$this->course_id = $post->ID;

			// Use nonce for verification.
			wp_nonce_field( 'learndash_course_groups_nonce_' . $this->course_id, 'learndash_course_groups_nonce' );

			?>
			<div id="learndash_course_groups_page_box" class="learndash_course_groups_page_box">
			<?php
				$ld_binary_selector_course_groups = new Learndash_Binary_Selector_Course_Groups(
					array(
						'html_title'            => '',
						'course_id'             => $this->course_id,
						'selected_ids'          => learndash_get_course_groups( $this->course_id, true ),
						'search_posts_per_page' => 100,
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
		 * @param object $post WP_Post.
		 */
		public function course_users_page_box( $post ) {
			$this->course_id = $post->ID;

			$course_price_type = get_course_meta_setting( $this->course_id, 'course_price_type' );
			if ( 'open' !== $course_price_type ) {
				$selected_user_ids = array();
				$metabox_description = '';

				$course_users_binary_args = array(
					'html_title'            => '',
					'course_id'             => $this->course_id,
					'search_posts_per_page' => 100,
				);

				if ( 'yes' === LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' ) ) {
					$exclude_admin = true;
					$course_users_binary_args['role__not_in'] = 'Administrator';
					$metabox_description  .= esc_html__( 'Admininstrator users are not shown because they are auto-enrolled.', 'learndash' );
				} else {
					$exclude_admin = false;
				}

				$course_users_query = learndash_get_users_for_course( $this->course_id, array(), $exclude_admin );
				if ( $course_users_query instanceof WP_User_Query ) {
					$selected_user_ids = $course_users_query->get_results();

					if ( ! empty( $selected_user_ids ) ) {
						$selected_user_ids = array_map( 'intval', $selected_user_ids );

						$course_groups_users = get_course_groups_users_access( $this->course_id );
						if ( ! empty( $course_groups_users ) ) {
							$course_users_binary_args['excluded_ids'] = $course_groups_users;
							$selected_user_ids = array_diff( $selected_user_ids, $course_groups_users );
						}
					}
					$course_users_binary_args['selected_ids'] = $selected_user_ids;
				}

				// Use nonce for verification.
				wp_nonce_field( 'learndash_course_users_nonce_' . $this->course_id, 'learndash_course_users_nonce' );

				if ( ! empty( $metabox_description ) ) {
					$metabox_description .= ' ';
				}

				$metabox_description .= sprintf(
					// translators: placeholder: Course.
					esc_html_x( 'Users enrolled via Groups using this %s are excluded from the listings below and should be manage via the Group admin screen.', 'placeholder: Course', 'learndash' ),
					LearnDash_Custom_Label::get_label( 'course' )
				);
				?>
				<div id="learndash_course_users_page_box" class="learndash_course_users_page_box">
				<?php
				if ( ! empty( $metabox_description ) ) {
					?><p><?php echo $metabox_description; ?></p><?php
				}
				$ld_binary_selector_course_users = new Learndash_Binary_Selector_Course_Users( $course_users_binary_args );
				$ld_binary_selector_course_users->show();
				?>
				</div>
				<?php
			} else {
				?><p><?php printf(
					// translators: placeholder: Course.
					esc_html_x( 'The %s price type is set to "open". This means ALL are automatically enrolled.', 'placeholder: Course', 'learndash' ),
					LearnDash_Custom_Label::get_label( 'course' )
				); ?></p><?php
			}
		}

		/**
		 * Save metabox handler function.
		 *
		 * @param integer $post_id Post ID Question being edited.
		 * @param object  $post WP_Post Question being edited.
		 * @param boolean $update If update true, else false.
		 */
		public function save_post( $post_id = 0, $post = null, $update = false ) {
			if ( ! $this->post_type_check( $post ) ) {
				return false;
			}

			if ( ! parent::save_post( $post_id, $post, $update ) ) {
				return false;
			}

			/**
			 * Verify this came from the our screen and with proper authorization,
			 * because save_post can be triggered at other times.
			 */
			if ( ( isset( $_POST['learndash_course_groups_nonce'] ) ) && ( wp_verify_nonce( $_POST['learndash_course_groups_nonce'], 'learndash_course_groups_nonce_' . $post_id ) ) ) {
				if ( ( isset( $_POST['learndash_course_groups'] ) ) && ( isset( $_POST['learndash_course_groups'][ $post_id ] ) ) && ( ! empty( $_POST['learndash_course_groups'][ $post_id ] ) ) ) {
					$course_groups = (array) json_decode( stripslashes( $_POST['learndash_course_groups'][ $post_id ] ) );
					learndash_set_course_groups( $post_id, $course_groups );
				}
			}

			if ( ( isset( $_POST['learndash_course_users_nonce'] ) ) && ( wp_verify_nonce( $_POST['learndash_course_users_nonce'], 'learndash_course_users_nonce_' . $post_id ) ) ) {
				if ( ( isset( $_POST['learndash_course_users'] ) ) && ( isset( $_POST['learndash_course_users'][ $post_id ] ) ) && ( ! empty( $_POST['learndash_course_users'][ $post_id ] ) ) ) {
					$course_users = (array) json_decode( stripslashes( $_POST['learndash_course_users'][ $post_id ] ) );
					learndash_set_users_for_course( $post_id, $course_users );
				}
			}

			/**
			 * Save Course Builder
			 * Within CB will be security checks.
			 */
			if ( apply_filters( 'learndash_show_course_builder', $this->use_course_builder ) === true ) {
				$this->course_builder->save_course_builder( $post_id, $post, $update );
			}
		}

		// End of functions.
	}
}
new Learndash_Admin_Course_Edit();
