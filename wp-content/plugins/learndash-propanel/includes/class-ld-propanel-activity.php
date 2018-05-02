<?php
/**
 * LearnDash ProPanel Activity
 *
 * @package LearnDash_ProPanel_Activity
 * @since 2.0
 */
if ( !class_exists( 'LearnDash_ProPanel_Activity' ) ) {
	class LearnDash_ProPanel_Activity extends LearnDash_ProPanel_Widget {

		/**
		 * @var string
		 */
		protected $name;
	
		/**
		 * @var string
		 */
		protected $label;

		/**
		 * LearnDash_ProPanel_Activity constructor.
		 */
		public function __construct() {
			$this->name = 'activity';
			$this->label = esc_html__( 'ProPanel Activity', 'ld_propanel' );

			parent::__construct();
			add_filter( 'learndash_propanel_template_ajax', array( $this, 'activity_template' ), 10, 2 );
			add_filter( 'learndash_propanel_template_ajax', array( $this, 'activity_template_rows' ), 10, 2 );

			add_filter( 'learndash_data_reports_headers', array( $this, 'learndash_data_reports_headers' ), 10, 2 );
		}

		function initial_template() {
			?>
			<div class="ld-propanel-widget ld-propanel-widget-<?php echo $this->name ?> <?php echo ld_propanel_get_widget_screen_type_class( $this->name ); ?>" data-ld-widget-type="<?php echo $this->name ?>"></div>
			<?php
		}

		/**
		 * Initial Activity Template
		 *
		 * @param $output
		 * @param $template
		 *
		 * @return string
		 */
		public function activity_template( $output, $template ) {
			if ( 'activity' == $template ) {

				ob_start();
				include ld_propanel_get_template( 'ld-propanel-reporting-choose-filter.php' );
				$output = ob_get_clean();
				
			} else if (( 'activity-courses' == $template ) || ( 'activity-quizzes' == $template )) {

				// To handle the Activy Courses and Quizzes report output we hook into the LearnDash core reporting function. 
				// It does all the heave processing for us. 
				$reply_data = array( 'status' => false);
				if ( isset( $_GET['args'] ) )
					$post_data = $_GET['args'];
				else
					$post_data = array();
				
				$report_post_args = array();
				if ( isset( $_GET['args'] ) )
					$report_post_args = array_merge( $report_post_args, $_GET['args'] );
				
				if ( ( isset( $report_post_args['init'] ) ) && ( $report_post_args['init'] == '1' ) ) {

					$_GET['filters'] = $_GET['args']['filters'];
					$post_data = ld_propanel_load_post_data( $post_data );

					if ( !empty( $post_data['filters']['courseStatus'] ) ) {
						$report_post_args['filters']['activity_status'] = $post_data['filters']['courseStatus'];
					} else {
						$report_post_args['filters']['activity_status'] = array( 'NOT_STARTED', 'IN_PROGRESS', 'COMPLETED' );
					} 
					$activity_query_args = array();
					$activity_query_args = ld_propanel_load_activity_query_args( $activity_query_args, $post_data );

					if ( !empty( $activity_query_args ) ) {

						if ( ( !isset( $report_post_args['filters']['users_ids'] ) ) && ( isset( $activity_query_args['user_ids'] ) ) ) {
							$report_post_args['filters']['users_ids'] = $activity_query_args['user_ids'];
							//unset( $report_post_args['filters']['user_ids'] );
						} else {
							$report_post_args['filters']['users_ids'] = learndash_get_report_user_ids();
						}
						
						if ( !empty( $report_post_args['filters']['users_ids'] ) ) {
							$exclude_admin_users = ld_propanel_exclude_admin_users();
							if ( $exclude_admin_users ) {
								$admin_user_ids = ld_propanel_get_admin_user_ids();
								if ( !empty( $admin_user_ids ) ) {
									$report_post_args['filters']['users_ids'] = array_diff( $report_post_args['filters']['users_ids'], $admin_user_ids );
								}
							}
						}

						/*
						if ( ( !isset( $report_post_args['filters']['posts_ids'] ) ) && ( isset( $activity_query_args['post_ids'] ) ) ) {
							$report_post_args['filters']['posts_ids'] = $activity_query_args['post_ids'];
							foreach( $report_post_args['filters']['posts_ids'] as $course_id ) {
								$course_post_status = get_post_status( $course_id );
								if ( $course_post_status == 'publish' ) {
									if ( 'activity-courses' == $template ) {
										$report_post_types = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic' );
									} else if ( 'activity-quizzes' == $template ) {
										$report_post_types = array( 'sfwd-quiz' );
									}
									$course_post_ids = ld_propanel_get_course_post_items( $course_id, $report_post_types );
									if ( !empty( $course_post_ids ) ) {
										$report_post_args['filters']['posts_ids'] = array_merge( $report_post_args['filters']['posts_ids'], $course_post_ids );
										$report_post_args['filters']['posts_ids'] = array_unique( $report_post_args['filters']['posts_ids'] );
									}
								}
							}
						}
						*/
						
						if ( ( !isset( $report_post_args['filters']['posts_ids'] ) ) && ( isset( $activity_query_args['post_ids'] ) ) ) {
							$_process_legacy = true;
							
							// If the admin has performed the needed upgrade on the courses and quizzes...
							if ( version_compare( LEARNDASH_VERSION, '2.4.9.9') >= 0 ) {
								$ld_data_upgrade = Learndash_Admin_Settings_Data_Upgrades::get_instance();
								$data_settings_courses = $ld_data_upgrade->get_data_settings('user-meta-courses');
								$data_settings_quizzes = $ld_data_upgrade->get_data_settings('user-meta-quizzes');
								
								if ( ( isset( $data_settings_courses['version'] ) ) && ( version_compare( $data_settings_courses['version'], '2.5', '>=') ) && ( isset( $data_settings_quizzes['version'] ) ) && ( version_compare( $data_settings_quizzes['version'], '2.5', '>=') ) ) {
								    // we can simple query by course_id since that column will be filled in now. 
									$report_post_args['filters']['course_ids'] = $activity_query_args['post_ids'];
									$_process_legacy = false;
								}
							} 
							
							// But if we still need to support legacy we can still do that below. 
							if ( $_process_legacy === true ) {
								$report_post_args['filters']['posts_ids'] = $activity_query_args['post_ids'];
								foreach( $report_post_args['filters']['posts_ids'] as $course_id ) {
									$course_post_status = get_post_status( $course_id );
									if ( $course_post_status == 'publish' ) {
										if ( 'activity-courses' == $template ) {
											$report_post_types = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic' );
										} else if ( 'activity-quizzes' == $template ) {
											$report_post_types = array( 'sfwd-quiz' );
										}
										$course_post_ids = ld_propanel_get_course_post_items( $course_id, $report_post_types );
										if ( !empty( $course_post_ids ) ) {
											$report_post_args['filters']['posts_ids'] = array_merge( $report_post_args['filters']['posts_ids'], $course_post_ids );
											$report_post_args['filters']['posts_ids'] = array_unique( $report_post_args['filters']['posts_ids'] );
										}
									}
								}
							}
						}
					}
				}

				if ( class_exists( 'Learndash_Admin_Settings_Data_Reports' ) ) {
					$ld_admin_settings_data_reports = new Learndash_Admin_Settings_Data_Reports;
					$reply_data['data'] = $ld_admin_settings_data_reports->do_data_reports( $report_post_args, $reply_data );
					unset( $reply_data['data']['filters'] );
					
					$output = $reply_data;
				}
			}

			return $output;
		}
		
		
		/**
		 * Override the LearnDash core reporting column headers. 
		 *
		 * @param $data_headers array of headers. See notes below for exact structure
		 * @param $data_slug stirng for the type of report 'user-courses' or 'user-quizzes'
		 *
		 * @return $data_headers array 
		 *
		 * The follow is an example of the data structure used for the headers. Note this is NOT
		 * a simple key/value array. 
		 * $data_headers['user_id']  = 	array( 
		 *										'label'		=>	'user_id',
		 *										'default'	=>	'',
		 *										'display'	=>	array( $this, 'report_header_user_id' )
		 *									);
		 *
		 * 'label' This is used in place of the array item key for the column header value.
		 * 'default' This is the default value of the field
		 * 'display' This should be a callback function to handle the value determination
		*/
		function learndash_data_reports_headers( $data_headers, $data_slug ) {
			
			if ( $data_slug == 'user-courses' ) {
				if ( !isset( $data_headers['course_started_on'] ) ) {
					$data_headers['course_started_on'] = array(
						'label'		=>	'course_started_on',
						'default'	=>	'',
						'display'	=>	array( $this, 'learndash_courses_report_display_column' )
					);
				}

				/*
				if ( !isset( $data_headers['course_updated_on'] ) ) {
					$data_headers['course_updated_on'] = array(
						'label'		=>	'course_updated_on',
						'default'	=>	'',
						'display'	=>	array( $this, 'learndash_courses_report_display_column' )
					);
				}
				*/
				
				if ( !isset( $data_headers['course_total_time_on'] ) ) {
					$data_headers['course_total_time_on'] = array(
						'label'		=>	__( 'course_total_time_on', 'ld_propanel' ),
						'default'	=>	'',
						'display'	=>	array( $this, 'learndash_courses_report_display_column' )
					);
				}

				if ( !isset( $data_headers['course_last_step_id'] ) ) {
					$data_headers['course_last_step_id'] = array(
						'label'		=>	__( 'course_last_step_id', 'ld_propanel' ),
						'default'	=>	'',
						'display'	=>	array( $this, 'learndash_courses_report_display_column' )
					);
				}

				if ( !isset( $data_headers['course_last_step_type'] ) ) {
					$data_headers['course_last_step_type'] = array(
						'label'		=>	__( 'course_last_step_type', 'ld_propanel' ),
						'default'	=>	'',
						'display'	=>	array( $this, 'learndash_courses_report_display_column' )
					);
				}

				if ( !isset( $data_headers['course_last_step_title'] ) ) {
					$data_headers['course_last_step_title'] = array(
						'label'		=>	__( 'course_last_step_title', 'ld_propanel' ),
						'default'	=>	'',
						'display'	=>	array( $this, 'learndash_courses_report_display_column' )
					);
				}

				if ( !isset( $data_headers['last_login_date'] ) ) {
					$data_headers['last_login_date'] = array(
						'label'		=>	__( 'last_login_date', 'ld_propanel' ),
						'default'	=>	'',
						'display'	=>	array( $this, 'learndash_courses_report_display_column' )
					);
				}
			} else if ( $data_slug == 'user-quizzes' ) {

			}

			return $data_headers;
		}
		
		function learndash_courses_report_display_column( $header_output = '', $header_key, $activity, $report_user ) {
			$data_slug = 'user-courses';
			include ld_propanel_get_template( 'ld-propanel-reporting-columns.php' );
			return $header_output;
		}
		
		
		/**
		 * Build Activity Rows
		 *
		 * @param $output
		 * @param $template
		 *
		 * @return string
		 */
		public function activity_template_rows( $output, $template ) {
			if (( 'activity_rows' == $template ) || ( 'activity' == $template )) {
				$output = '';

				//if ( ld_propanel_count_post_type( 'sfwd-courses' ) ) {
				if ( ld_propanel_get_users_count() ) {

					if ( isset( $_GET['args']['per_page'] ) ) {
						$per_page = abs( intval( $_GET['args']['per_page'] ) );
					} else {
						$per_page_array = ld_propanel_get_pager_values();
						if ( empty( $per_page_array ) ) {
							$per_page_array = array(5);
						}
						
						$per_page = $per_page_array[0];
					}
					

					/**
					 * Build $activity_query_args from info passed as AJAX
					 */
					$activity_query_args = array(
						'per_page' 			=> 	$per_page,
						//'activity_status' 	=> 	array( 'NOT_STARTED', 'IN_PROGRESS', 'COMPLETED' ), // We are only showing completed items for now
						'activity_types'	=>	array('course', 'quiz', 'lesson', 'topic'),	
						'post_types'		=>	array('sfwd-courses', 'sfwd-quiz', 'sfwd-lessons', 'sfwd-topic'),
						'post_status'		=>	'publish',
						'orderby_order'		=>	'ld_user_activity.activity_updated DESC',
						'date_format' 		=> 	'Y-m-d H:i:s',
						'export_buttons'	=>	true, //$_GET['filters']['export_buttons'],
						'nav_top'			=>	true, //$_GET['filters']['nav_top'],
						//'nav_bottom'		=>	true, //$_GET['filters']['nav_bottom'],
					);
				
					foreach( $activity_query_args as $key => $val ) {
						if ( isset( $_GET['filters'][$key] ) ) {
							$activity_query_args[$key] = stripslashes_deep( $_GET['filters'][$key] );
						}
					}

					if ( ( isset( $_GET['container_type'] ) ) && ( $_GET['container_type'] == 'shortcode' ) ) {
						if ( ( !isset( $_GET['filters']['export_buttons'] ) ) || ( $_GET['filters']['export_buttons'] !== '1' ) && ( $_GET['filters']['export_buttons'] !== 'true' ) )
							unset( $activity_query_args['export_buttons'] );
						if ( ( !isset( $_GET['filters']['nav_top'] ) ) || ( $_GET['filters']['nav_top'] !== '1' ) && ( $_GET['filters']['nav_top'] !== 'true' ) )
							unset( $activity_query_args['nav_top'] );
					}


					$activity_query_args = shortcode_atts( $activity_query_args, $_GET['filters'] );

					$post_data = ld_propanel_load_post_data();
				
					$activity_query_args = ld_propanel_load_activity_query_args( $activity_query_args, $post_data );

					if ( !empty( $activity_query_args ) ) {

						$activity_query_args = ld_propanel_adjust_admin_users( $activity_query_args );
				
						//$response['total_users'] = count( $this->activity_query_args['user_ids'] );
				
						$activity_query_args = ld_propanel_convert_fewer_users( $activity_query_args );

						// If apecific post_ids are provided we want to inlcude in all the lessons, topics, quizzes for display
						if ( ( isset( $activity_query_args['post_ids'] ) ) && ( !empty( $activity_query_args['post_ids'] ) ) ) {
							if ( version_compare( LEARNDASH_VERSION, '2.4.9.9') >= 0 ) {
								$activity_query_args['course_ids'] = $activity_query_args['post_ids'];
								$activity_query_args['post_ids'] = '';
								
							} else {
								$post_ids = $activity_query_args['post_ids'];
								foreach( $post_ids as $course_id ) {
									$course_post_status = get_post_status( $course_id );
									if ( $course_post_status == 'publish' ) {
										//$course_post_ids = learndash_get_course_steps( $course_id, $activity_query_args['post_types'] );
										$course_post_ids = ld_propanel_get_course_post_items( $course_id, $activity_query_args['post_types'] );
										if ( !empty( $course_post_ids ) ) {
											$activity_query_args['post_ids'] = array_merge( $activity_query_args['post_ids'], $course_post_ids );
											$activity_query_args['post_ids'] = array_unique( $activity_query_args['post_ids'] );
										}
									}
								}
							}
						}
						
						//if ( empty( $post_data['filters']['courseStatus'] ) ) {
						//	$post_data['filters']['courseStatus'] = array( 'IN_PROGRESS', 'COMPLETED' );
						//} else {
						//	foreach( $post_data['filters']['courseStatus'] as $idx => $status ) {
						//		if ( $status == 'NOT_STARTED' ) {
						//			unset( $post_data['filters']['courseStatus'][$idx] );
						//		}
						//	}
						//}
						$activity_query_args['activity_status'] = array( 'IN_PROGRESS', 'COMPLETED' );

						$paged = 1;

						if ( isset( $_GET['args']['paged'] ) && ! empty( $_GET['args']['paged'] ) ) {
							$activity_query_args['paged'] = abs( intval( $_GET['args']['paged'] ) );
							$paged = intval( $_GET['args']['paged'] );
						}
			
						$activity_query_args = apply_filters( 'ld_propanel_activity_widget_query_args', $activity_query_args, $template );
						if ( learndash_is_admin_user( get_current_user_id() ) ) {
							// Admin will see all groups

						} else if ( learndash_is_group_leader_user( ) ) {
							if ( ( !isset( $activity_query_args['user_ids'] ) ) || ( empty( $activity_query_args['user_ids'] ) ) ) {
								$activity_query_args = array();
							} 
							
							if ( version_compare( LEARNDASH_VERSION, '2.4.9.9') >= 0 ) {
								if ( ( !isset( $activity_query_args['course_ids'] ) ) || ( empty( $activity_query_args['course_ids'] ) ) ) {
									$activity_query_args = array();
								}
							} else {
								if ( ( !isset( $activity_query_args['post_ids'] ) ) || ( empty( $activity_query_args['post_ids'] ) ) ) {
									$activity_query_args = array();
								}	
							}
						} else {	// Regular student user
							if ( ( !isset( $activity_query_args['user_ids'] ) ) || ( empty( $activity_query_args['user_ids'] ) ) ) {
								$activity_query_args = array();
							} else if ( ( !isset( $activity_query_args['post_ids'] ) ) || ( empty( $activity_query_args['post_ids'] ) ) ) {
								$activity_query_args = array();
							}
						}

						if ( !empty( $activity_query_args ) ) {

							//error_log('activity_query_args<pre>'. print_r($activity_query_args, true) .'</pre>');
							$activities = learndash_reports_get_activity( $activity_query_args );
							//error_log('activities<pre>'. print_r($activities, true) .'</pre>');
					
							ob_start();
							if ( empty( $activities['results'] ) ) {
								include ld_propanel_get_template( 'ld-propanel-no-results.php' );
							} else {
								?><div class="report-header"><?php
						
									?><div class="report-pagination"><?php
									if ( isset( $activities['pager'] ) ) {
										$activities['pager']['current_page'] = $activity_query_args['paged'];
										if ( $activity_query_args['nav_top'] == true ) {
											include ld_propanel_get_template( 'ld-propanel-activity-pagination.php' );
										}
									}
									?></div><?php
						
									?><div class="report-exports"><?php
									if ( $activity_query_args['export_buttons'] == true )
										include ld_propanel_get_template( 'ld-propanel-activity-report-header.php' );
									?></div><?php
									?><div class="clearfix"></div><?php
							
								?></div><?php

								$activity_row_date_time_format = apply_filters('ld_propanel_activity_row_date_time_format', get_option('date_format') .' '. get_option('time_format'));

								foreach ( $activities['results'] as $activity ) {
									$activity->activity_started_formatted = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_started ), 'Y-m-d H:i:s' ); 
									$activity->activity_started_formatted = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_started_formatted ), false);

									$activity->activity_completed_formatted = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_completed ), 'Y-m-d H:i:s' ); 
									$activity->activity_completed_formatted = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_completed_formatted ), false);

									$activity->activity_updated_formatted = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_updated ), 'Y-m-d H:i:s' ); 
									$activity->activity_updated_formatted = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_updated_formatted ), false);
							
									include ld_propanel_get_template( 'ld-propanel-activity-rows.php' );
								}

								//if ( $activity_query_args['nav_bottom'] == true )
								//	include ld_propanel_get_template( 'ld-propanel-activity-pagination.php' );
							}
							$output = ob_get_clean();
							
						} else {
							ob_start();
							include ld_propanel_get_template( 'ld-propanel-no-results.php' );
							$output = ob_get_clean();
						}
					} else {
						ob_start();
						include ld_propanel_get_template( 'ld-propanel-no-results.php' );
						$output = ob_get_clean();
					}
				} else {
					ob_start();
					include ld_propanel_get_template( 'ld-propanel-no-results.php' );
					$output = ob_get_clean();
				}
			}

			return array( 'rows_html' => $output );
		}

		/**
		 * @param $activity
		 *
		 * @return mixed
		 */
		public static function get_activity_steps_completed( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) && ( isset( $activity->activity_meta['steps_completed'] ) ) ) {
				return intval($activity->activity_meta['steps_completed']);
			}
		}

		/**
		 * @param $activity
		 *
		 * @return mixed
		 */
		public static function get_activity_steps_total( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) && ( isset( $activity->activity_meta['steps_total'] ) ) ) {
				return intval($activity->activity_meta['steps_total']);
			}
		}

		/**
		 * @param $activity
		 *
		 * @return array|null|WP_Post
		 */
		function get_activity_course( $activity ) {
			if ( ( isset( $activity->activity_course_id ) ) && ( !empty( $activity->activity_course_id ) ) ) {
				$course_id = intval( $activity->activity_course_id );
			} else {
				$course_id = learndash_get_course_id( $activity->post_id );
			}
			
			if ( !empty( $course_id ) ) {
				$course = get_post( $course_id );
				if ( ( $course ) && ( $course instanceof WP_Post ) ) {
					return $course;
				}
			}
		}

		/**
		 * @param $activity
		 *
		 * @return bool
		 */
		function quiz_activity_is_pending( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {

				if ( ( isset( $activity->activity_meta['has_graded'] ) ) 
				  && ( true === $activity->activity_meta['has_graded'] ) 
				  && ( true === LD_QuizPro::quiz_attempt_has_ungraded_question( $activity->activity_meta ) ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * @param $activity
		 *
		 * @return bool
		 */
		function quiz_activity_is_passing( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {

				if ( isset( $activity->activity_meta['pass'] ) ) {
					return (bool) $activity->activity_meta['pass'];
				}
			}

			return false;
		}

		/**
		 * @param $activity
		 *
		 * @return mixed
		 */
		function quiz_activity_score( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {
				if ( isset( $activity->activity_meta['score'] ) ) {
					return $activity->activity_meta['score'];
				}
			}
		}

		/**
		 * @param $activity
		 *
		 * @return mixed
		 */
		function quiz_activity_total_points( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {
				if ( isset( $activity->activity_meta['total_points'] ) ) {
					return intval($activity->activity_meta['total_points']);
				}
			}
		}

		/**
		 * @param $activity
		 *
		 * @return mixed
		 */
		function quiz_activity_awarded_points( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {
				if ( isset( $activity->activity_meta['points'] ) ) {
					return intval($activity->activity_meta['points']);
				}
			}
		}

		/**
		 * @param $activity
		 *
		 * @return int
		 */
		function quiz_activity_points_percentage( $activity ) {
			$awarded_points = intval( $this->quiz_activity_awarded_points( $activity ) );
			$total_points = intval( $this->quiz_activity_total_points( $activity ) );
			if ( ( !empty( $awarded_points ) ) && ( !empty( $total_points ) ) ) {
				return round( 100 * ( intval( $awarded_points ) / intval( $total_points ) ) );
			}
		}




		/**
		 * @param $activity
		 *
		 * @return mixed
		 */
		function quiz_activity_total_score( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {
				if ( isset( $activity->activity_meta['count'] ) ) {
					return intval($activity->activity_meta['count']);
				}
			}
		}

		/**
		 * @param $activity
		 *
		 * @return mixed
		 */
		function quiz_activity_awarded_score( $activity ) {
			if ( ( !empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {
				if ( isset( $activity->activity_meta['score'] ) ) {
					return intval($activity->activity_meta['score']);
				}
			}
		}

		/**
		 * @param $activity
		 *
		 * @return int
		 */
		function quiz_activity_score_percentage( $activity ) {
			$awarded_score = intval( $this->quiz_activity_awarded_score( $activity ) );
			$total_score = intval( $this->quiz_activity_total_score( $activity ) );
			if ( ( !empty( $awarded_score ) ) && ( !empty( $total_score ) ) ) {
				return round( 100 * ( intval( $awarded_score ) / intval( $total_score ) ) );
			}
		}


		function get_quiz_scoring( $activity ) {
			return null;
		}


		function get_quiz_statistics_link( $activity ) {
			$stats_url = '';
			
			if ( ( $activity->user_id == get_current_user_id() ) || ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) {				
				if ( ( isset( $activity->activity_meta['statistic_ref_id'] ) ) && ( !empty( $activity->activity_meta['statistic_ref_id'] ) ) ) {
					/**
					 *	 @since 2.3
					 * See snippet on use of this filter https://bitbucket.org/snippets/learndash/5o78q
					 */
					if ( apply_filters( 
						'show_user_profile_quiz_statistics', 
							get_post_meta( $activity->activity_meta['quiz'], '_viewProfileStatistics', true ), $activity->user_id, $activity->activity_meta, 'learndash-propanel-activity' ) ) {
							$stats_url = '<a class="user_statistic" data-statistic_nonce="'. wp_create_nonce( 'statistic_nonce_'. $activity->activity_meta['statistic_ref_id'] .'_'. get_current_user_id() . '_'. $activity->user_id ) .'" data-user_id="'. $activity->user_id .'" data-quiz_id="'. $activity->activity_meta['pro_quizid'] .'" data-ref_id="'. intval( $activity->activity_meta['statistic_ref_id'] ) .'" href="#" title="'. __('View Quiz Statistics', 'ld_propanel') .'">'. __('Statistics', 'ld_propanel') .'</a>';
					}
				}
			}
			
			return $stats_url;
		}
	}
}
