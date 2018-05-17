<?php
/**
 * SFWD_CPT_Instance
 * 
 * @since 2.1.0
 * 
 * @package LearnDash\CPT
 */


if ( ! class_exists( 'SFWD_CPT_Instance' ) ) {

	/**
	 * Extends functionality of SWFD_CPT instance
	 *
	 * @todo  consider whether these methods can just be included in SWFD_CPT
	 *        unclear as to why it's separate
	 */
	class SFWD_CPT_Instance extends SFWD_CPT {

		public static $instances = array();



		/**
		 * Sets up properties for CPT to be used in plugins
		 *
		 * @since 2.1.0
		 * 
		 * @param array $args  parameters for setting up the CPT instance
		 */
		function __construct( $args ) {
			extract( $args );

			if ( empty( $plugin_name ) ) {	
				$plugin_name = 'SFWD CPT Instance';
			}

			if ( empty( $post_name ) ) {
				$post_name = $plugin_name;
			}

			if ( empty( $slug_name ) ) {
				$slug_name = sanitize_file_name( strtolower( strtr( $post_name, ' ', '_' ) ) );
			}

			if ( empty( $post_type ) ) {
				$post_type = sanitize_file_name( strtolower( strtr( $slug_name, ' ', '_' ) ) );
			}

			SFWD_CPT_Instance::$instances[ $post_type ] =& $this;

			if ( empty( $name ) ) {
				$name = ! empty( $options_page_title)? $options_page_title:$post_name . esc_html__( ' Options', 'learndash' );
			}

			if ( empty( $prefix ) ) {
				$prefix = sanitize_file_name( $post_type ) . '_';
			}

			if ( ! empty( $taxonomies ) ) {	
				$this->taxonomies = $taxonomies;
			}

			$this->file = __FILE__ . "?post_type={$post_type}";
			$this->plugin_name	= $plugin_name;
			$this->post_name	= $post_name;
			$this->slug_name	= $slug_name;
			$this->post_type	= $post_type;
			$this->name			= $name;
			$this->prefix		= $prefix;
			$posts_per_page = get_option( 'posts_per_page' );

			if ( empty( $posts_per_page ) ) { 
				$posts_per_page = 5;
			}

			if ( empty( $default_options ) ) {

				$this->default_options = array(
					'orderby' => array(
						'name' => esc_html__( 'Sort By', 'learndash' ),
						'type' => esc_html__( 'select', 'learndash' ),
						'initial_options' => array(	
							''		=> esc_html__( 'Select a choice...', 'learndash' ),
							'title'	=> esc_html__( 'Title', 'learndash' ),
							'date'	=> esc_html__( 'Date', 'learndash' ),
							'menu_order' => esc_html__( 'Menu Order', 'learndash' )
						),
						'default' => 'date',
						'help_text' => esc_html__( 'Choose the sort order.', 'learndash' )
					),
					'order' => array(
						'name' => esc_html__( 'Sort Direction', 'learndash' ),
						'type' => 'select',
						'initial_options' => array(	
							''		=> esc_html__( 'Select a choice...', 'learndash' ),
							'ASC'	=> esc_html__( 'Ascending', 'learndash' ),
							'DESC'	=> esc_html__( 'Descending', 'learndash' )
						),
						'default' => 'DESC',
						'help_text' => esc_html__( 'Choose the sort order.', 'learndash' )
					),
					'posts_per_page' => array(
						'name' => esc_html__( 'Posts Per Page', 'learndash' ),
						'type' => 'text',
						'help_text' => esc_html__( 'Enter the number of posts to display per page.', 'learndash' ),
						'default' => $posts_per_page
					),
				);

			} else {				
				$this->default_options = $default_options;
			}

			if ( ! empty( $fields ) ) {
				$this->locations = array(
					'default' => array( 
						'name' => $this->name, 
						'prefix' => $this->prefix, 
						'type' => 'settings', 
						'options' => null 
					),
					$this->post_type => array( 
						'name' => $this->plugin_name, 
						'type' => 'metabox', 'prefix' => '',
						'options' => array_keys( $fields ),
						'default_options' => $fields,
						'display' => array( $this->post_type ) 
					)
				);
			}

			parent::__construct();

			if ( ! empty( $description ) ) { 
				$this->post_options['description'] = wp_kses_post( $description );			
			}

			if ( ! empty( $menu_icon ) ) { 
				$this->post_options['menu_icon'] = esc_url( $menu_icon );
			}

			if ( ! empty( $cpt_options ) ) { 
				$this->post_options = wp_parse_args( $cpt_options, $this->post_options );
			}

			add_action( 'admin_menu', array( &$this, 'admin_menu') );
			add_shortcode( $this->post_type, array( $this, 'shortcode' ) );
			add_action( 'init', array( $this, 'add_post_type' ), 5 );

			$this->update_options();

			if ( ! is_admin() ) {
				add_action( 'pre_get_posts', array( $this, 'pre_posts' ) );

				if ( isset( $template_redirect ) && ( $template_redirect === true ) ) {
					/*if ( !empty( $this->options[ $this->prefix . 'template_redirect'] ) ) {
						add_action("template_redirect", array( $this, 'template_redirect' ) );
					} else*/ {
					add_action( 'template_redirect', array( $this, 'template_redirect_access' ) );
					add_filter( 'the_content', array( $this, 'template_content' ), 1000 );
					}
				}

			}

		} // end __construct()



		/**
		 * Get Archive content
		 *
		 * @todo Consider reworking, function returns content of a post.
		 *       Not archive.
		 *       
		 * @since 2.1.0
		 * 
		 * @param  string $content
		 * @return string $content
		 */
		function get_archive_content( $content ) {
			global $post;
			if ( sfwd_lms_has_access( $post->ID ) ) {
				return $content;
			} else {
				return get_the_excerpt();
			}
		} // end get_archive_content()



		/**
		 * Generate output for courses, lessons, topics, quizzes
		 * Filter callback for 'the_content' (wp core filter)
		 * 
		 * Determines what the user is currently looking at, sets up data,
		 * passes to template, and returns output.
		 *
		 * @since 2.1.0
		 * 
		 * @param  string $content content of post
		 * @return string $content content of post
		 */
		function template_content( $content ) {
			global $wp;
			$post = get_post( get_the_id() );
			$current_user = wp_get_current_user();
			$post_type = '';

			if ( get_query_var( 'post_type' ) ) {
				$post_type = get_query_var( 'post_type' );
			}

			if ( ( ! is_singular() ) || ( $post_type != $this->post_type ) || ( $post_type != $post->post_type ) ) {
				return $content;
			}

			if ( is_user_logged_in() )
				$user_id = get_current_user_id();
			else
				$user_id = 0;
					
			$logged_in = ! empty( $user_id );
			$course_id = learndash_get_course_id();
			$lesson_progression_enabled = false;
			$has_access = '';

			if ( ! empty( $course_id ) ) {
				$course = get_post( $course_id );
				$course_settings = learndash_get_setting( $course );
				$lesson_progression_enabled  = learndash_lesson_progression_enabled();
				$courses_options = learndash_get_option( 'sfwd-courses' );
				$lessons_options = learndash_get_option( 'sfwd-lessons' );
				$quizzes_options = learndash_get_option( 'sfwd-quiz' );
				$course_status = learndash_course_status( $course_id, null );
				$course_certficate_link = learndash_get_course_certificate_link( $course_id, $user_id );
				$has_access = sfwd_lms_has_access( $course_id, $user_id );
				//error_log('has_access['. $has_access .']');
				
				$course_meta = get_post_meta( $course_id, '_sfwd-courses', true );
				if ( !isset( $course_meta['sfwd-courses_course_disable_content_table'] ) ) {
					$course_meta['sfwd-courses_course_disable_content_table'] = false;
				}
			}

			if ( $logged_in ) {
				if ( learndash_is_admin_user( $user_id ) ) {
					$bypass_course_limits_admin_users = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'bypass_course_limits_admin_users' );
					if ( $bypass_course_limits_admin_users == 'yes' ) $bypass_course_limits_admin_users = true;
					else $bypass_course_limits_admin_users = false;
										
				} else {
					$bypass_course_limits_admin_users = false;
				}
				
				// For logged in users to allow an override filter. 
				$bypass_course_limits_admin_users = apply_filters( 'learndash_prerequities_bypass', $bypass_course_limits_admin_users, $user_id, $course_id, $post );
				
			} else {
				$bypass_course_limits_admin_users = true;
			}
			
											
			if ( ! empty( $wp->query_vars['name'] ) ) {
				// single
				if ( ( $logged_in ) && ( !is_course_prerequities_completed( $course_id ) ) && ( !$bypass_course_limits_admin_users ) ) {
					if ( $this->post_type == 'sfwd-courses' ) { 
						$content_type = LearnDash_Custom_Label::label_to_lower('course');
					} elseif ( $this->post_type == 'sfwd-lessons' ) {
						$content_type = LearnDash_Custom_Label::label_to_lower('lesson');
					} elseif ( $this->post_type == 'sfwd-quiz' ) {
						$content_type = LearnDash_Custom_Label::label_to_lower('quiz');
					} else {
						$content_type = strtolower( $this->post_name );
					}

					$course_pre = learndash_get_course_prerequisites( $course_id );
					if ( !empty( $course_pre ) ) {
						foreach( $course_pre as $c_id => $c_status ) {
							break;
						}

						$level = ob_get_level();
						ob_start();
						SFWD_LMS::get_template( 
							'learndash_course_prerequisites_message', 
							array(
								'current_post'				=>	$post,
								// We need to support the 'prerequisite_post' element since modifued templates may suse it. 
								'prerequisite_post' 		=> 	get_post( $c_id ),
								'prerequisite_posts_all' 	=> 	$course_pre,
								'content_type'				=>	$content_type,
								'course_settings'			=>	$course_settings
							), true
						);
						$content = learndash_ob_get_clean( $level );
					}
					
				} else if ( ( $logged_in ) && ( !learndash_check_user_course_points_access( $course_id, $user_id ) ) && ( !$bypass_course_limits_admin_users ) ) {
					
					if ( $this->post_type == 'sfwd-courses' ) { 
						$content_type = LearnDash_Custom_Label::label_to_lower('course');
					} elseif ( $this->post_type == 'sfwd-lessons' ) {
						$content_type = LearnDash_Custom_Label::label_to_lower('lesson');
					} elseif ( $this->post_type == 'sfwd-quiz' ) {
						$content_type = LearnDash_Custom_Label::label_to_lower('quiz');
					} else {
						$content_type = strtolower( $this->post_name );
					}

					$course_access_points = learndash_get_course_points_access( $course_id );
					$user_course_points = learndash_get_user_course_points( $user_id );
			
					$level = ob_get_level();
					ob_start();
					SFWD_LMS::get_template( 
						'learndash_course_points_access_message', 
						array(
							'current_post'				=>	$post,
							'content_type'				=>	$content_type,
							'course_access_points'		=>	$course_access_points,
							'user_course_points'		=>	$user_course_points,
							'course_settings'			=>	$course_settings
						), true
					);
					$content = learndash_ob_get_clean( $level );
					
				}
				else {
					if ( $this->post_type == 'sfwd-courses' ) {

						$courses_prefix = $this->get_prefix();
						$prefix_len = strlen( $courses_prefix );

						$materials = '';
						if ( ! empty( $course_settings['course_materials'] ) ) {
							//$materials = wp_kses_post( wp_specialchars_decode( $course_settings['course_materials'], ENT_QUOTES ) );
							$materials = wp_specialchars_decode( $course_settings['course_materials'], ENT_QUOTES );
							if ( !empty( $materials ) ) {
								$materials = do_shortcode( $materials );
							}
						}

						learndash_check_convert_settings_to_single( $post->ID, $this->post_type );

						$lessons = learndash_get_course_lessons_list( $course_id );

						// For now no paginiation on the course quizzes. Can't think of a scenario where there will be more 
						// than the pager count. 
						$quizzes = learndash_get_course_quiz_list( $course );

						$has_course_content = ( ! empty( $lessons) || ! empty( $quizzes ) );

						$lesson_topics = array();

						$has_topics = false;

						if ( ! empty( $lessons ) ) {
							foreach ( $lessons as $lesson ) {
								$lesson_topics[ $lesson['post']->ID ] = learndash_topic_dots( $lesson['post']->ID, false, 'array', null, $course_id );
								if ( ! empty( $lesson_topics[ $lesson['post']->ID ] ) ) {
									$has_topics = true;
								}
							}
						}

						include_once( 'vendor/paypal/enhanced-paypal-shortcodes.php' );
						$level = ob_get_level();
						ob_start();
						include( SFWD_LMS::get_template( 'course', null, null, true ) );
						$content = learndash_ob_get_clean( $level );

					} elseif ( $this->post_type == 'sfwd-quiz' ) {
						learndash_check_convert_settings_to_single( $post->ID, $this->post_type );
						
						$quiz_settings = learndash_get_setting( $post );
						$meta = @$this->get_settings_values( 'sfwd-quiz' );
						$show_content = ! ( ! empty( $lesson_progression_enabled) && ! is_quiz_accessable( null, $post ) );
						$attempts_count = 0;
						$repeats = trim( @$quiz_settings['repeats'] );

						if ( $repeats != '' ) {
							$user_id = get_current_user_id();

							if ( $user_id ) {
								$usermeta = get_user_meta( $user_id, '_sfwd-quizzes', true );
								$usermeta = maybe_unserialize( $usermeta );

								if ( ! is_array( $usermeta ) ) { 
									$usermeta = array();
								}

								if ( ! empty( $usermeta ) )	{
									foreach ( $usermeta as $k => $v ) {
										if ( ( $v['quiz'] == $post->ID ) && ( ( isset( $v['course'] ) ) && ( isset( $course_id ) ) && ( intval( $v['course'] ) == intval( $course_id ) ) ) ) { 
											$attempts_count++;
										}
									}
								}

							}
						}

						$attempts_left = ( $repeats == '' || $repeats >= $attempts_count );

						if ( ! empty( $lesson_progression_enabled) && ! is_quiz_accessable( null, $post ) ) {
							add_filter( 'comments_array', 'learndash_remove_comments', 1, 2 );
						}

						 /**
						 * Filter for content access
						 *
						 * If not null, will display instead of quiz content
						 * 
						 * @since 2.1.0
						 * 
						 * @param  string
						 */
						$access_message = apply_filters( 'learndash_content_access', null, $post );

						if ( ! is_null( $access_message ) ) {
							$quiz_content = $access_message;
						} else {							
							
							$materials = '';
							if ( ! empty( $quiz_settings['quiz_materials'] ) ) {
								//$materials = wp_kses_post( wp_specialchars_decode( $course_settings['course_materials'], ENT_QUOTES ) );
								$materials = wp_specialchars_decode( $quiz_settings['quiz_materials'], ENT_QUOTES );
								if ( !empty( $materials ) ) {
									$materials = do_shortcode( $materials );
								}
							}
							
							if ( ! empty( $quiz_settings['quiz_pro'] ) ) {
								$quiz_content = wptexturize( do_shortcode( '[LDAdvQuiz '.$quiz_settings['quiz_pro'].']' ) );
							} else {
								$quiz_content = '';
							}

							 /**
							 * Filter quiz content
							 * 
							 * @since 2.1.0
							 * 
							 * @param  string  $quiz_content
							 */
							$quiz_content = apply_filters( 'learndash_quiz_content', $quiz_content, $post );
						}

						$level = ob_get_level();
						ob_start();
						include( SFWD_LMS::get_template( 'quiz', null, null, true ) );
						$content = learndash_ob_get_clean( $level );

					} elseif ( $this->post_type == 'sfwd-lessons' ) {
						learndash_check_convert_settings_to_single( $post->ID, $this->post_type );

						$show_content = false;
						if ( $lesson_progression_enabled ) {
								
							if ( ( learndash_is_admin_user( $user_id ) ) && ( $bypass_course_limits_admin_users ) ) {
								$previous_lesson_completed = true;
								remove_filter( 'learndash_content', 'lesson_visible_after', 1, 2 );
							} else {
								$previous_lesson_completed = apply_filters( 'learndash_previous_step_completed', is_previous_complete( $post ), $post->ID, $user_id );
							}
							$show_content = $previous_lesson_completed;
							
						} else {
							$show_content = true;
							$previous_lesson_completed = true;
						}

						$lesson_settings = learndash_get_setting( $post );
						$quizzes = learndash_get_lesson_quiz_list( $post, null, $course_id );
						$quizids = array();

						if ( ! empty( $quizzes) ) {
							foreach ( $quizzes as $quiz ) {
								$quizids[ $quiz['post']->ID ] = $quiz['post']->ID;
							}
						}

						if ( $lesson_progression_enabled && ! $previous_lesson_completed ) {
							add_filter( 'comments_array', 'learndash_remove_comments', 1,2 );
						}

						$topics = learndash_topic_dots( $post->ID, false, 'array', null, $course_id );

						if ( ! empty( $quizids ) ) {
							$all_quizzes_completed = ! learndash_is_quiz_notcomplete( null, $quizids, false, $course_id );
						} else {
							$all_quizzes_completed = true;
						}

						if ($show_content) {
							
							$materials = '';
							if ( ! empty( $lesson_settings['lesson_materials'] ) ) {
								//$materials = wp_kses_post( wp_specialchars_decode( $course_settings['course_materials'], ENT_QUOTES ) );
								$materials = wp_specialchars_decode( $lesson_settings['lesson_materials'], ENT_QUOTES );
								if ( !empty( $materials ) ) {
									$materials = do_shortcode( $materials );
								}
							}
							
							// We insert the Course started record before the Lesson. 
							$course_args = array(
								'course_id'			=>	$course_id, 
								'user_id'			=>	$current_user->ID,
								'post_id'			=>	$course_id,
								'activity_type'		=>	'course',
								'activity_status'	=>	false,
								'activity_started'	=>	time(), 
								'activity_meta'		=>	array(
															'steps_total'		=>	learndash_get_course_steps_count( $course_id ),
															'steps_completed'	=>	learndash_course_get_completed_steps($current_user->ID, $course_id),
															'steps_last_id'		=>	$post->ID
														)
							);
							
							$course_activity = learndash_get_user_activity( $course_args );
							if ( ( !$course_activity ) || ( empty( $course_activity->activity_started ) ) ) {
								learndash_update_user_activity( $course_args );
							}

							$lesson_args = array(
								'course_id'			=>	$course_id, 
								'user_id'			=>	$current_user->ID,
								'post_id'			=>	$post->ID,
								'activity_type'		=>	'lesson',
								'activity_status'	=>	false,
								'activity_started'	=>	time(),
								'activity_meta'		=>	array(
															'steps_total'		=>	learndash_get_course_steps_count( $course_id ),
															'steps_completed'	=>	learndash_course_get_completed_steps($current_user->ID, $course_id),
														)
							);
							$lesson_activity = learndash_get_user_activity( $lesson_args );
							if ( ( !$lesson_activity ) || ( empty( $lesson_activity->activity_started ) ) ) {
								learndash_update_user_activity( $lesson_args );
							}
						}

						// Added logic for Lesson Videos
						if ( ( defined( 'LEARNDASH_LESSON_VIDEO' ) ) && ( LEARNDASH_LESSON_VIDEO == true ) ) {
							if ( $show_content )  {
								$ld_course_videos = Learndash_Course_Video::get_instance();
								$content = $ld_course_videos->add_video_to_content( $content, $post, $lesson_settings );
							}
						}
						
						$level = ob_get_level();
						ob_start();
						include( SFWD_LMS::get_template( 'lesson', null, null, true ) );
						$content = learndash_ob_get_clean( $level );

					}  elseif ( $this->post_type == 'sfwd-topic' ) {
						learndash_check_convert_settings_to_single( $post->ID, $this->post_type );

						//if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes' ) {
							$course_id = learndash_get_course_id( $post );
							$lesson_id = learndash_course_get_single_parent_step( $course_id, $post->ID );
							
							//} else {
							//$lesson_id = learndash_get_setting( $post, 'lesson' );
							//}

						if ( $lesson_progression_enabled ) {
							$lesson_post = get_post( $lesson_id );
							$previous_topic_completed = is_previous_complete( $post );
							$previous_lesson_completed = is_previous_complete( $lesson_post );

							if ( ( learndash_is_admin_user( $user_id ) ) && ( $bypass_course_limits_admin_users ) ) {
								$previous_lesson_completed = $previous_topic_completed = true;
								remove_filter( 'learndash_content', 'lesson_visible_after', 1, 2 );
							} else {
								
								$previous_topic_completed = apply_filters( 'learndash_previous_step_completed', is_previous_complete( $post ), $post->ID, $user_id );
								$previous_lesson_completed = apply_filters( 'learndash_previous_step_completed', is_previous_complete( $lesson_post ), $lesson_post->ID, $user_id );
							}
							$show_content = ( $previous_topic_completed && $previous_lesson_completed );

						} else {
							$previous_topic_completed = true;
							$previous_lesson_completed = true;
							$show_content = true;
						}
						
						$quizzes = learndash_get_lesson_quiz_list( $post, null, $course_id );
						$quizids = array();
						
						if ( ! empty( $quizzes) ) {
							foreach ( $quizzes as $quiz ) {
								$quizids[ $quiz['post']->ID ] = $quiz['post']->ID;
							}
						}

						if ( $lesson_progression_enabled && ( ! $previous_topic_completed || ! $previous_lesson_completed ) ) {
							add_filter( 'comments_array', 'learndash_remove_comments', 1, 2 );
						}

						if ( ! empty( $quizids ) ) {
							$all_quizzes_completed = ! learndash_is_quiz_notcomplete( null, $quizids, false, $course_id );
						} else {
							$all_quizzes_completed = true;
						}

						$topics = learndash_topic_dots( $lesson_id, false, 'array', null, $course_id );

						if ($show_content) {
							$materials = '';
							$topic_settings = learndash_get_setting( $post );
							if ( ! empty( $topic_settings['topic_materials'] ) ) {
								//$materials = wp_kses_post( wp_specialchars_decode( $course_settings['course_materials'], ENT_QUOTES ) );
								$materials = wp_specialchars_decode( $topic_settings['topic_materials'], ENT_QUOTES );
								if ( !empty( $materials ) ) {
									$materials = do_shortcode( $materials );
								}
							}
							
							// We insert the Course started record before the Topic. 

							$course_args = array(
								'course_id'			=>	$course_id, 
								'user_id'			=>	$current_user->ID,
								'post_id'			=>	$course_id,
								'activity_type'		=>	'course',
								'activity_status'	=>	false,
								'activity_started'	=>	time(), 
								'activity_meta'		=>	array(
															'steps_total'		=>	learndash_get_course_steps_count( $course_id ),
															'steps_completed'	=>	learndash_course_get_completed_steps($current_user->ID, $course_id),
															'steps_last_id'		=>	$post->ID
														)
							);
							$course_activity = learndash_get_user_activity( $course_args );
							if ( ( !$course_activity ) || ( empty( $course_activity->activity_started ) ) ) {
								learndash_update_user_activity( $course_args );
							}
							
							$topic_args = array(
								'course_id'			=>	$course_id, 
								'user_id'			=>	$current_user->ID,
								'post_id'			=>	$post->ID,
								'activity_type'		=>	'topic',
								'activity_status'	=>	false,
								'activity_started'	=>	time(), 
								'activity_meta'		=>	array(
															'steps_total'		=>	learndash_get_course_steps_count( $course_id ),
															'steps_completed'	=>	learndash_course_get_completed_steps($current_user->ID, $course_id),
														)
							);
							$topic_activity = learndash_get_user_activity( $topic_args );
							if ( ( !$topic_activity ) || ( empty( $topic_activity->activity_started ) ) ) {
								learndash_update_user_activity( $topic_args );
							}
						}
						//$topic_settings = learndash_get_setting( $post );

						// Added logic for Lesson Videos
						if ( ( defined( 'LEARNDASH_LESSON_VIDEO' ) ) && ( LEARNDASH_LESSON_VIDEO == true ) ) {
							if ( $show_content ) {
								$ld_course_videos = Learndash_Course_Video::get_instance();
								$content = $ld_course_videos->add_video_to_content( $content, $post, $topic_settings );
							}
						}
						
						$level = ob_get_level();
						ob_start();
						include( SFWD_LMS::get_template( 'topic', null, null, true ) );
						$content = learndash_ob_get_clean( $level );

					} else {
						// archive
						$content = $this->get_archive_content( $content );
					}
				} 
			}

			// Added this defined wrap in v2.1.8 as it was effecting <pre></pre>, <code></code> and other formatting of the content. 
			// See wrike https://www.wrike.com/open.htm?id=77352698 as to why this define exists
			if ( ( defined( 'LEARNDASH_NEW_LINE_AND_CR_TO_SPACE' ) ) && ( LEARNDASH_NEW_LINE_AND_CR_TO_SPACE == true ) ) {

				// Why is this here? 
				$content = str_replace( array( "\n", "\r" ), ' ', $content );


			}
			
			$user_has_access = $has_access? 'user_has_access':'user_has_no_access';

			 /**
			 * Filter content to be return inside div
			 * 
			 * @since 2.1.0
			 * 
			 * @param  string  $content 
			 */
			return '<div class="learndash learndash_post_'. $this->post_type .' '. $user_has_access .'"  id="learndash_post_'.$post->ID.'">'.apply_filters( 'learndash_content', $content, $post ).'</div>';
		} // end template_content()



		/**
		 * Show course completion/quiz completion
		 * Action callback from 'template_redirect' (wp core action)
		 *
		 * @since 2.1.0
		 */
		function template_redirect_access() {
			global $wp;
			global $post;


			/**
			 * Added check to ensure $post is not empty
			 * 
			 * @since 2.3.0.3
			 */
			if ( empty( $post ) ) { 
				return;
			}
			
			if ( !( $post instanceof WP_Post ) ) {
				return;
			}


			if ( get_query_var( 'post_type' ) ) {
				$post_type = get_query_var( 'post_type' );
			} else {
				if ( ! empty( $post ) ) {
					$post_type = $post->post_type;
				}
			}

			if ( empty( $post_type ) ) { 
				return;
			}

			if ( $post_type == $this->post_type ) {
				if ( is_robots() ) {
					/**
					 * Display the robots.txt file content. (wp core action)				
					 * 
					 * @since 2.1.0
					 *
					 * @link https://codex.wordpress.org/Function_Reference/do_robots
					 */
					do_action( 'do_robots' );
				} elseif ( is_feed() ) {
					do_feed();
				} elseif ( is_trackback() ) {
				   include( ABSPATH . 'wp-trackback.php' );
				} elseif ( ! empty( $wp->query_vars['name'] ) ) {
					// single
					if ( ( $post_type == 'sfwd-quiz' ) || ( $post_type == 'sfwd-lessons' )  || ( $post_type == 'sfwd-topic' ) ) {
						global $post;
						sfwd_lms_access_redirect( $post->ID );
					}
				}
				// archive
			}

			if ( ( $this->post_type == 'sfwd-courses') && ( $post_type == 'sfwd-certificates' ) ) {
				
				if ( is_user_logged_in() ) {

					if ( ( isset( $_GET['course_id'] ) ) && ( !empty( $_GET['course_id'] ) ) ) {
						$course_id = intval( $_GET['course_id'] );
					
						if ( ( ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) && ( ( isset( $_GET['user'] ) ) && (!empty( $_GET['user'] ) ) ) )
							$cert_user_id = intval( $_GET['user'] );
						else
							$cert_user_id = get_current_user_id();

						$view_user_id = get_current_user_id();
					
						if ( ( isset( $_GET['cert-nonce'] ) ) && ( !empty( $_GET['cert-nonce'] ) ) ) {
							if ( wp_verify_nonce( esc_attr($_GET['cert-nonce']), $course_id . $cert_user_id . $view_user_id ) )  {
					
								$course_status = learndash_course_status( $course_id, $cert_user_id );
								// Bug: Why are we comparing a string value for Complete. 
								if ( $course_status == esc_html__( 'Completed', 'learndash' ) ) {
							
									if ( ( ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) && ( $cert_user_id != $view_user_id ) ) {
										wp_set_current_user( $cert_user_id );
									}

									/**
									 * Include library to generate PDF
									 */
									require_once( 'ld-convert-post-pdf.php' );							
									post2pdf_conv_post_to_pdf();
									die();
								}
							} 
						}
					}
				}
			}

			if ( ( $this->post_type == 'sfwd-quiz' ) && ( $post_type == 'sfwd-certificates' ) ) {
				global $post;
				
				$cert_id = $post->ID;
				
				if ( ! empty( $_GET ) && ! empty( $_GET['quiz'] ) ) { 
					$quiz_id = $_GET['quiz'];
					$quiz_meta = get_post_meta( $quiz_id, '_sfwd-quiz', true );
				} else {
					$quiz_id = 0;
					$quiz_meta = array();
				}

				if ( ! empty( $post ) && is_single() ) {
					$print_cert = false;
					$cert_post = '';

					if ( isset( $quiz_meta['sfwd-quiz_certificate'] ) && ( ! empty( $quiz_meta['sfwd-quiz_certificate'] ) ) ) {
						$cert_post = $quiz_meta['sfwd-quiz_certificate'];
					}

					if ( empty( $cert_post ) && ! empty( $this->options["{$this->prefix}certificate_post"] ) ) {
						$cert_post = $this->options["{$this->prefix}certificate_post"];
					}
					
					if ( ( isset( $_GET['user'] ) ) && ( !empty( $_GET['user'] ) ) && ( ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) ) 
						$cert_user_id = intval( $_GET['user'] );
					else 
						$cert_user_id = get_current_user_id();
					
					$view_user_id = get_current_user_id();

					if ( ! empty( $cert_post ) && ( $cert_post == $post->ID ) ) {

						if ( ( isset( $_GET['cert-nonce'] ) ) && ( !empty( $_GET['cert-nonce'] ) ) && ( wp_verify_nonce( $_GET['cert-nonce'], $quiz_id . $cert_user_id . $view_user_id ) ) ) {
							$time = isset( $_GET['time'] )? $_GET['time']: -1;
							$quizinfo = get_user_meta( $cert_user_id, '_sfwd-quizzes', true );
							$selected_quizinfo = $selected_quizinfo2 = null;

							if ( ! empty( $quizinfo) ) {
								foreach ( $quizinfo as $quiz_i ) {

									if ( isset( $quiz_i['time'] ) && $quiz_i['time'] == $time && $quiz_i['quiz'] == $quiz_id ) {
										$selected_quizinfo = $quiz_i;
										break;
									}

									if ( $quiz_i['quiz'] == $quiz_id ) {
										$selected_quizinfo2 = $quiz_i;
									}

								}
							}

							$selected_quizinfo = empty( $selected_quizinfo ) ? $selected_quizinfo2 : $selected_quizinfo;
							if ( ! empty( $selected_quizinfo ) ) {
								$certificate_threshold = learndash_get_setting( $selected_quizinfo['quiz'], 'threshold' );
								
								if ( (isset( $selected_quizinfo['percentage'] ) && $selected_quizinfo['percentage'] >= $certificate_threshold * 100) || (isset( $selected_quizinfo['count'] ) && $selected_quizinfo['score'] / $selected_quizinfo['count'] >= $certificate_threshold) ) {
									$print_cert = true;
								}
							}
						} 
					}

					if ( $print_cert ) {
						if ( ( ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) && ( $cert_user_id != $view_user_id ) ) {
							wp_set_current_user( $cert_user_id );
						}
						
						
						/**
						 * Include library to generate PDF
						 */
						require_once( 'ld-convert-post-pdf.php' );
						post2pdf_conv_post_to_pdf();
						die();
					} else {
						if ( ! current_user_can( 'level_8' ) ) {
							esc_html_e( 'Access to certificate page is disallowed.', 'learndash' );
							die();
						}
					}

				}
			}
		} // end template_redirect_access()



		/**
		 * Amend $wp_query based on what content user is viewing
		 * 
		 * If archive for post type of this instance, set order and posts per page
		 * If post archive, don't display certificates
		 *
		 * @since 2.1.0
		 */
		function pre_posts() {
			global $wp_query;

			if ( is_post_type_archive( $this->post_type ) ) {

				foreach ( array( 'orderby', 'order', 'posts_per_page' ) as $field ) {
					if ( $this->option_isset( $field ) ) {
						$wp_query->set( $field, $this->options[ $this->prefix . $field ] );
					}
				}

			} elseif ( ( $this->post_type == 'sfwd-quiz' ) && ( is_post_type_archive( 'post' ) || is_home() ) && ! empty( $this->options["{$this->prefix}certificate_post"] ) ) {
				
				$post_not_in = $wp_query->get( 'post__not_in' );

				if ( ! is_array( $post_not_in ) ) { 
					$post_not_in = array();
				}

				$post_not_in = array_merge( $post_not_in, array( $this->options["{$this->prefix}certificate_post"] ) );
				$wp_query->set( 'post__not_in', $post_not_in );

			}
		} // end pre_posts()
	}
}
