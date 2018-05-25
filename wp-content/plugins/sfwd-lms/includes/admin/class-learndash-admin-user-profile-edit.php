<?php

if (!class_exists('Learndash_Admin_User_Profile_Edit')) {
	class Learndash_Admin_User_Profile_Edit {
		
		function __construct() {
			// Hook into the on-load action for our post_type editor
			add_action( 'load-profile.php', 		array( $this, 'on_load_user_profile') );
			add_action( 'load-user-edit.php', 		array( $this, 'on_load_user_profile') );

			add_action( 'show_user_profile', 		array( $this, 'show_user_profile') );
			add_action( 'edit_user_profile', 		array( $this, 'show_user_profile') );

			add_action( 'personal_options_update',  array( $this, 'save_user_profile' ), 1 );
			add_action( 'edit_user_profile_update', array( $this, 'save_user_profile' ), 1 );

			add_action( 'wp_ajax_learndash_remove_quiz', array( $this, 'remove_quiz_ajax' ) );


		}
				
		function on_load_user_profile() {
			global $learndash_assets_loaded;
			
			wp_enqueue_style( 
				'learndash_style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
				array(), 
				LEARNDASH_SCRIPT_VERSION_TOKEN 
			);
			$learndash_assets_loaded['styles']['learndash_style'] = __FUNCTION__;

			$filepath = SFWD_LMS::get_template( 'learndash_template_style.css', null, null, true );
			if ( !empty( $filepath ) ) {
				wp_enqueue_style( 'learndash_template_style_css', learndash_template_url_from_path( $filepath ), array(), LEARNDASH_SCRIPT_VERSION_TOKEN );
				$learndash_assets_loaded['styles']['learndash_template_style_css'] = __FUNCTION__;
			} 


			wp_enqueue_style( 
				'learndash-admin-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
				array(), 
				LEARNDASH_SCRIPT_VERSION_TOKEN 
			);
			$learndash_assets_loaded['styles']['learndash-admin-style'] = __FUNCTION__;


			wp_enqueue_style( 
				'sfwd-module-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css',
				array(), 
				LEARNDASH_SCRIPT_VERSION_TOKEN 
			);
			$learndash_assets_loaded['styles']['sfwd-module-style'] = __FUNCTION__;	


			wp_enqueue_script( 
				'learndash-admin-binary-selector-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ),
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true
			);
			$learndash_assets_loaded['scripts']['learndash-admin-binary-selector-script'] = __FUNCTION__;

			wp_enqueue_script( 
				'sfwd-module-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ), 
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true 
			);
			$learndash_assets_loaded['scripts']['sfwd-module-script'] = __FUNCTION__;	

			$filepath = SFWD_LMS::get_template( 'learndash_pager.css', null, null, true );
			if ( !empty( $filepath ) ) {
				wp_enqueue_style( 'learndash_pager_css', learndash_template_url_from_path( $filepath ), array(), LEARNDASH_SCRIPT_VERSION_TOKEN );
				$learndash_assets_loaded['styles']['learndash_pager_css'] = __FUNCTION__;
			} 

			$filepath = SFWD_LMS::get_template( 'learndash_pager.js', null, null, true );
			if ( !empty( $filepath ) ) {
				wp_enqueue_script( 'learndash_pager_js', learndash_template_url_from_path( $filepath ), array( 'jquery' ), LEARNDASH_SCRIPT_VERSION_TOKEN, true );
				$learndash_assets_loaded['scripts']['learndash_pager_js'] = __FUNCTION__;
			}



			$data = array();

			if ( ! empty( $this->script_data ) ) {
				$data = $this->script_data;
			}
			if ( !isset( $data['ajaxurl'] ) )
				$data['ajaxurl'] = admin_url('admin-ajax.php');

			$data = array( 'json' => json_encode( $data ) );
			wp_localize_script( 'sfwd-module-script', 'sfwd_data', $data );


			wp_enqueue_style( 
				'learndash-admin-binary-selector-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array( ),
				LEARNDASH_SCRIPT_VERSION_TOKEN
			);
			$learndash_assets_loaded['styles']['learndash-admin-binary-selector-style'] = __FUNCTION__;
			
			// We need to load the wpProQuiz_admin.js in order to show the quiz statistics on the profile. 
			//$WpProQuiz_Controller_Admin = new WpProQuiz_Controller_Admin();
			//$WpProQuiz_Controller_Admin->enqueueScript();
		}
		
		function show_user_profile( $user ) {
			$this->show_user_courses( $user );
			$this->show_user_groups( $user );
			$this->show_leader_groups( $user );
			
			$this->show_user_course_info( $user );
			//$this->show_user_course_progress( $user );
			//$this->show_user_upgrade_data_link( $user );
			$this->show_user_delete_data_link( $user );
			
			//$user_couses = get_user_meta( $user->ID, '_sfwd-course_progress', true );
			//error_log( 'user_couses<pre>'. print_r( $user_couses, true ) .'</pre>' );

			//$user_quizzes = get_user_meta( $user->ID, '_sfwd-quizzes', true );
			//error_log( 'user_quizzes<pre>'. print_r( $user_quizzes, true ) .'</pre>' );
			
			/*
			foreach( $user_quizzes as $user_quiz ) {
				error_log( 'user_quiz<pre>'. print_r( $user_quiz, true ) .'</pre>' );
				error_log('time: ['. $user_quiz['time'] .'] ['. learndash_adjust_date_time_display( $user_quiz['time'], 'Y-m-d H:i:s' ) .']');

				if ( isset( $user_quiz['started'] ) ) {
					error_log('started: ['. $user_quiz['started'] .'] ['. learndash_adjust_date_time_display( $user_quiz['started'], 'Y-m-d H:i:s' ) .']');
					$user_quiz['started_alt'] = abs( intval( $user_quiz['time'] - round( $user_quiz['timespent'], 0 ) ) );
					error_log('started_alt: ['. $user_quiz['started_alt'] .'] ['. learndash_adjust_date_time_display( $user_quiz['started_alt'], 'Y-m-d H:i:s' ) .']');
				}
				
				if ( isset( $user_quiz['completed'] ) ) {
					error_log('completed: ['. $user_quiz['completed'] .'] ['. learndash_adjust_date_time_display( $user_quiz['completed'], 'Y-m-d H:i:s' ) .']');
				}
			}
			*/
			//LD_QuizPro::showModalWindow();
		}
			
		/**
		 * Displays users course information at bottom of profile
		 * Fires on action 'show_user_profile'
		 * Fires on action 'edit_user_profile'
		 * 
		 * @since 2.1.0
		 * 
		 * @param  object $user  wp user object
		 */
		function show_user_course_info( $user ) {
			$user_id = $user->ID;
			echo '<h3>' . sprintf( esc_html_x( '%s Info', 'Course Info Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ) . '</h3>';
			//echo $this->get_course_info( $user_id );
			
			$atts = array(
				'progress_num' => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Per_Page', 'progress_num' ),
				'progress_orderby' => 'title',
				'progress_order' => 'ASC',
				'quiz_num' => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Per_Page', 'quiz_num' ),
				'quiz_orderby' => 'taken',
				'quiz_order' => 'DESC'
			);
			
			$atts = apply_filters('learndash_profile_course_info_atts', $atts, $user );
			
			echo SFWD_LMS::get_course_info( $user_id, $atts );
		}
		
		function show_user_upgrade_data_link( $user ) {
			?>
			<h2><?php esc_html_e( 'Upgrade User Data', 'learndash' ); ?></h2>
			<p><button class="learndash-data-upgrades-button button button-primary" data-nonce="<?php echo wp_create_nonce( 'learndash-data-upgrades-user-meta-courses-' . get_current_user_id() ); ?>" data-slug="user-meta-courses"><?php printf( esc_html_x( 'Upgrade User %s Data', 'Upgrade User Course Data Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></button> <button class="learndash-data-upgrades-button button button-primary" data-nonce="<?php echo wp_create_nonce( 'learndash-data-upgrades-user-meta-quizzes-' . get_current_user_id() ); ?>" data-slug="user-meta-quizzes"><?php printf( esc_html_x( 'Upgrade User %s Data', 'Upgrade User Quiz Data Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></button></p><?php
		}
				
				
		/**
		 * Output link to delete course data for user
		 *
		 * @since 2.1.0
		 * 
		 * @param  object $user WP_User object
		 */
		function show_user_delete_data_link( $user ) {
			//if ( ! learndash_is_admin_user( ) ) {
			if ( !current_user_can( 'edit_users' ) ) {
				return '';
			}

			?>
			<div id="learndash_delete_user_data">
				<h2><?php printf( esc_html_x( 'Permanently Delete %s Data', 'Permanently Delete Course Data Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'Course' ) ); ?></h2>
				<p><input type="checkbox" id="learndash_delete_user_data" name="learndash_delete_user_data" value="<?php echo $user->ID; ?>"> <label for="learndash_delete_user_data"><?php echo wp_kses_post( __( 'Check and click update profile to permanently delete user\'s LearnDash course data. <strong>This cannot be undone.</strong>', 'learndash' ) ); ?></label></p>
				<?php
			
					global $wpdb;
					$sql_str = $wpdb->prepare("SELECT quiz_id as proquiz_id FROM ". $wpdb->prefix ."wp_pro_quiz_lock WHERE user_id=%d", $user->ID);
					//error_log('sql_str['. $sql_str .']');
					$proquiz_ids = $wpdb->get_col( $sql_str );
					if ( !empty( $proquiz_ids ) ) {
						//error_log('quiz_ids<pre>'. print_r($quiz_ids, true) .'</pre>');
						$quiz_ids = array();
				
						foreach( $proquiz_ids as $proquiz_id ) {
							$quiz_id = learndash_get_quiz_id_by_pro_quiz_id( $proquiz_id );
							if ( !empty( $quiz_id ) ) {
								$quiz_ids[] = $quiz_id;
							}
						}
				
						if ( !empty( $quiz_ids ) ) {
							$quiz_query_args = array(
								'post_type' 		=> 	'sfwd-quiz',
								'post_status' 		=> 	array( 'publish' ),
								'post__in'			=>	$quiz_ids,
								'nopaging'			=> 	true,
								'orderby'			=>	'title',
								'order'				=>	'ASC',
							);
							$quiz_query = new WP_Query( $quiz_query_args );
							if ( !empty( $quiz_query->posts ) ) {
					
								?>
								<p><label for=""><?php esc_html_e( 'Remove the Quiz lock(s) for this user.', 'learndash' ); ?></label> <select
									id="learndash_delete_quiz_user_lock_data" name="learndash_delete_quiz_user_lock_data">
									<option value=""></option>
									<?php
										foreach( $quiz_query->posts as $quiz_post ) {
											?><option value="<?php echo $quiz_post->ID ?>"><?php echo $quiz_post->post_title; ?></option><?php
										}
									?>
								</select>
								<input type="hidden" name="learndash_delete_quiz_user_lock_data-nonce" value="<?php echo wp_create_nonce('learndash_delete_quiz_user_lock_data-'. intval($user->ID)) ?>">
								<?php
							}
						}
					}
				?>
			</div>
			<?php
		}

		function save_user_profile( $user_id ) {
			if ( !current_user_can( 'edit_users' ) ) {
				return;
			}
			
			if ( empty( $user_id ) ) {
				return;
			}
			
			if ( ( isset( $_POST['learndash_user_courses'] ) ) && ( isset( $_POST['learndash_user_courses'][$user_id] ) ) && ( !empty( $_POST['learndash_user_courses'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_user_courses-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_user_courses-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_user_courses-'. $user_id .'-nonce'], 'learndash_user_courses-'.$user_id )) {
						$user_courses = (array)json_decode( stripslashes( $_POST['learndash_user_courses'][$user_id] ) );
						learndash_user_set_enrolled_courses( $user_id, $user_courses );
					}
				}
			}

			if ( ( isset( $_POST['learndash_user_groups'] ) ) && ( isset( $_POST['learndash_user_groups'][$user_id] ) ) && ( !empty( $_POST['learndash_user_groups'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_user_groups-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_user_groups-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_user_groups-'. $user_id .'-nonce'], 'learndash_user_groups-'.$user_id )) {

						$user_groups = (array)json_decode( stripslashes( $_POST['learndash_user_groups'][$user_id] ) );
						learndash_set_users_group_ids( $user_id, $user_groups );
					}
				}
			}

			if ( ( isset( $_POST['learndash_leader_groups'] ) ) && ( isset( $_POST['learndash_leader_groups'][$user_id] ) ) && ( !empty( $_POST['learndash_leader_groups'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_leader_groups-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_leader_groups-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_leader_groups-'. $user_id .'-nonce'], 'learndash_leader_groups-'.$user_id )) {
						$user_groups = (array)json_decode( stripslashes( $_POST['learndash_leader_groups'][$user_id] ) );
						learndash_set_administrators_group_ids( $user_id, $user_groups );
					}
				}
			}
			
			if ( ( isset( $_POST['learndash_delete_quiz_user_lock_data'] ) ) && ( !empty( $_POST['learndash_delete_quiz_user_lock_data'] ) ) ) {
				if ( ( isset( $_POST['learndash_delete_quiz_user_lock_data-nonce'] ) ) && ( !empty( $_POST['learndash_delete_quiz_user_lock_data-nonce'] ) ) ) {
					if ( wp_verify_nonce( $_POST['learndash_delete_quiz_user_lock_data-nonce'], 'learndash_delete_quiz_user_lock_data-'. $user_id ) ) {
						learndash_remove_user_quiz_locks( $user_id, $_POST['learndash_delete_quiz_user_lock_data'] );
					} 
				}
			}

			if ( isset( $_POST['learndash_course_points'] ) ) {
				update_user_meta($user_id, 'course_points', learndash_format_course_points( $_POST['learndash_course_points'] ) );
			}

			if ( ( isset( $_POST['learndash_delete_user_data'] ) ) && ( !empty( $_POST['learndash_delete_user_data'] ) ) && ( intval( $_POST['learndash_delete_user_data'] ) === intval( $user_id ) ) ) {
				learndash_delete_user_data( $user_id );
			}

			learndash_save_user_course_complete( $user_id );
		}
		
		function show_user_courses( $user ) {
			// First check is the user viewing the screen is admin...
			//if ( learndash_is_admin_user( ) ) {
			if ( current_user_can( 'edit_users' ) ) {
				// Then is the user profile being viewed is not admin
				if ( learndash_is_admin_user( $user->ID ) ) {
					
					/**
					 * See example if 'learndash_override_course_auto_enroll' filter 
					 * https://bitbucket.org/snippets/learndash/kon6y
					 *
					 * @since 2.3
					 */
					$course_autoenroll_admin = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
					if ( $course_autoenroll_admin == 'yes' ) $course_autoenroll_admin = true;
					else $course_autoenroll_admin = false;
					$course_autoenroll_admin_filtered = apply_filters('learndash_override_course_auto_enroll', $course_autoenroll_admin, $user->ID );
					
					if ( $course_autoenroll_admin_filtered ) {
						?>
						<h3><?php echo sprintf( esc_html_x('User Enrolled %s', 'User Enrolled Courses', 'learndash'), LearnDash_Custom_Label::get_label( 'courses' ) )  ?></h3>
						<p><?php esc_html_e('Administrators are automatically enrolled in all Courses.', 'learndash') ?></p>
						<?php
						return;
					} 
				}
				
				$ld_binary_selector_user_courses = new Learndash_Binary_Selector_User_Courses(
					array(
						'user_id'				=>	$user->ID,
						'selected_ids'			=>	learndash_user_get_enrolled_courses( $user->ID, array(), true ),
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_user_courses->show();
			}
		}

		function show_user_groups( $user ) {
			//if ( learndash_is_admin_user( ) ) {
			if ( current_user_can( 'edit_users' ) ) {
				
				$ld_binary_selector_user_groups = new Learndash_Binary_Selector_User_Groups(
					array(
						'user_id'				=>	$user->ID,
						'selected_ids'			=>	learndash_get_users_group_ids( $user->ID, true ),
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_user_groups->show();
			}
		}

		function show_leader_groups( $user ) {
			//if ( learndash_is_admin_user() ) {
			if ( current_user_can( 'edit_users' ) ) {
				if ( learndash_is_group_leader_user( $user->ID ) ) {
					$ld_binary_selector_leader_groups = new Learndash_Binary_Selector_Leader_Groups(
						array(
							'user_id'				=>	$user->ID,
							'selected_ids'			=>	learndash_get_administrators_group_ids( $user->ID, true ),
							'search_posts_per_page' => 100
						)
					);
					$ld_binary_selector_leader_groups->show();
				}
			}
		}

		function show_user_course_progress( $user ) {
			if ( current_user_can( 'edit_users' ) ) {
				$user_courses_registered = ld_get_mycourses( $user->ID );
				$user_courses_registered = !empty( $user_courses_registered ) ? $user_courses_registered : array() ;

				$user_course_progress = get_user_meta( $user->ID, '_sfwd-course_progress', true );
				$user_course_progress = !empty( $user_course_progress ) ? $user_course_progress : array() ;
				
				//error_log('user_courses_registered<pre>'. print_r($user_courses_registered, true) .'</pre>');
				//error_log('user_course_progress<pre>'. print_r($user_course_progress, true) .'</pre>');
				
				$courses_ids = array_merge( $user_courses_registered, array_keys( $user_course_progress ) );
				
				if ( !empty( $courses_ids ) ) {
					$course_query_args = array(
						'post_type'			=>	'sfwd-courses',
						'post_status'		=>	'publish',
						'fields'			=>	'ids',
						'nopaging'			=>	true,
						'orderby'			=>	'title',
						'order'				=>	'ASC',
						'post__in'			=>	$courses_ids
					);
				
					$course_query = new WP_Query( $course_query_args );
				
					if ( ( isset( $course_query->posts ) ) && ( !empty( $course_query->posts ) ) ) {
						?>
						<h2>Course Progress</h2>
						<table id="learndash-admin-user-courses" class="learndash-admin-user-courses">
						<thead>
							<tr>
								<th class="col-title"><?php esc_html_e('Title', 'learndash' ); ?></th>
								<th class="col-status"><?php esc_html_e('Status', 'learndash' ); ?></th>
								<th class="col-steps"><?php esc_html_e('Steps', 'learndash' ); ?></th>
								<th class="col-steps"><?php esc_html_e('Started / Completed', 'learndash' ); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach( $course_query->posts as $course_id ) {
								$course = get_post( $course_id );
								
								$course_edit_permalink = '';
								if ( current_user_can( 'edit_courses', $course->ID ) )
									$course_edit_permalink = get_permalink( $course->ID );
								?>
								<tr>
									<td class="col=title">
										<?php if ( !empty( $course_edit_permalink ) ) { ?>
											<a href="<?php $course_edit_permalink ?>">
										<?php } ?>
										<?php echo get_the_title( $course->ID ) ?>
										<?php if ( !empty( $course_edit_permalink ) ) { ?>
											</a>
										<?php } ?>
										<br />
										
										<div class="row-actions">
											<?php if ( !empty( $course_edit_permalink ) ) { ?>
												<span class="edit">
													<a href="<?php $course_edit_permalink ?>" aria-label="Edit">edit</a>
													|
												</span>
											<?php } ?>
											<span class="view">
												<a href="<?php echo get_permalink( $course->ID ); ?>" aria-label="View"><?php esc_html_e('view', 'learndash') ?></a>
												|
											</span>
										</div>
								</td>
									<?php
										$course_status = learndash_course_status( $course_id, $user->ID );
									?>
									<td class="col-status"><span class="leardash-course-status leardash-course-status-<?php echo sanitize_title_with_dashes($course_status) ?>"><?php echo $course_status ?></span></td>

									<?php
									
									$coursep['completed'] = '     ';
									$coursep['total'] = '     ';
									
									if ( isset( $user_course_progress[$course_id] ) ) {
										$coursep = $user_course_progress[$course_id];

										$course_steps_count = learndash_get_course_steps_count( $course_id ); 
										$course_steps_completed = learndash_course_get_completed_steps( $user->ID, $course_id, $coursep );
					
										$completed_on = get_user_meta( $user->ID, 'course_completed_' . $course_id, true );
										if ( !empty( $completed_on ) ) {
						
											$coursep['completed'] = $course_steps_count;
											$coursep['total'] = $course_steps_count;
					
										} else {
											$coursep['total'] = $course_steps_count;
											$coursep['completed'] = $course_steps_completed;
						
											if ( $coursep['completed'] > $coursep['total'] )
												$coursep['completed'] = $coursep['total'];
										}
									}
									?><td class="col-steps"><?php echo sprintf('%-5s / %5s', $coursep['completed'], $coursep['total'] );?></td><?php
									
									?><td class="col-dates"><?php
										$output_str = '';
										
										$since = ld_course_access_from( $course->ID,  $user->ID );
										if ( !empty( $since ) ) {
											if ( !empty( $output_str ) ) $output_str .= '<br />';
											$output_str .= sprintf( esc_html_x('Started: %s', 'placeholder: Started date', 'learndash' ), learndash_adjust_date_time_display( $since ) );
										} else {
											$since = learndash_user_group_enrolled_to_course_from( $user->ID, $course->ID );
											if ( !empty( $since ) ) {
												if ( !empty( $output_str ) ) $output_str .= '<br />';
												$output_str .= sprintf( esc_html_x('Started: %s (Group Access)', 'placeholder: Started Group date', 'learndash'), learndash_adjust_date_time_display( $since ) );
											}
										}
										
										
										// Display the Course Access if expired or expiring
										$expire_access = learndash_get_setting( $course_id, 'expire_access' );
										if ( !empty( $expire_access ) ) {
											$expired = ld_course_access_expired( $course_id, $user->ID );
											if ( $expired ) {
												if ( !empty( $output_str ) ) $output_str .= '<br />'; 
												$output_str .= esc_html__('(access expired)', 'learndash');
											} else {
												$expired_on = ld_course_access_expires_on($course_id, $user->ID);
												if (!empty( $expired_on ) ) {
													if ( !empty( $output_str ) ) $output_str .= '<br />'; 
													$output_str .= sprintf( esc_html_x('Expires: %s', 'Course Expires on date', 'learndash'), learndash_adjust_date_time_display( $expired_on ) );
												}
											}
										}
										
										$completed = get_user_meta( $user->ID, 'course_completed_'. $course->ID, true );
										if ( !empty( $completed ) ) {
											if ( !empty( $output_str ) ) $output_str .= '<br />'; 
											$output_str .= sprintf( esc_html_x('Completed: %s', 'placeholder: Completed date', 'learndash' ), learndash_adjust_date_time_display( $completed ) );
										} 
										
										echo $output_str
									?></td><?php
									
									
								?>
								</tr><?php
							}
						?>
						<tbody>
						</table>
						<?php
					}
				}
			}
		}
		
		function remove_quiz_ajax() {
			$data = array();

			$quiz_time = 0;
			if ( isset( $_POST['quiz_time'] ) ) {
				$quiz_time = esc_attr( $_POST['quiz_time'] );
			}
			
			$quiz_nonce = 0;
			if ( isset( $_POST['quiz_nonce'] ) ) {
				$quiz_nonce = esc_attr( $_POST['quiz_nonce'] );
			}
			
			$user_id = 0;
			if ( isset( $_POST['user_id'] ) ) {
				$user_id = intval( $_POST['user_id'] );
			}
			
			if ( ( !empty( $user_id ) ) && ( !empty( $quiz_time ) ) && ( !empty( $quiz_nonce ) ) ) {
				$user_quizzes = learndash_get_user_quiz_attempt( $user_id, array('time' => $quiz_time ) );
				if ( !empty( $user_quizzes ) ) {
					foreach( $user_quizzes as $q_idx => $q_item ) {
						
						if ( wp_verify_nonce( $quiz_nonce, 'remove_quiz_'. $user_id .'_'. $q_item['quiz'] .'_'. $q_item['time'] ) ) {
							learndash_remove_user_quiz_attempt( $user_id, array( 'time' => $q_item['time'] ) );
						} 
					}
				}
			}
			
			echo json_encode( $data );
			die();
		}
		
		
		// End of functions
	}
}
