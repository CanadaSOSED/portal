<?php
if ( !class_exists('LD_REST_Posts_Controller' ) ) {
	abstract class LD_REST_Posts_Controller extends WP_REST_Posts_Controller {

		protected $version = 'v1';

		public function __construct( $post_type = '' ) {
			parent::__construct( $post_type );
		}		

		public function get_course_item( $request ) {
			//error_log('in '. __FUNCTION__ );
			//error_log('request[id] ['. $request['id'] .']');
			
			$post = $this->get_post( intval( $request['id'] ) );
			//error_log('post<pre>'. print_r($post, true) .'</pre>');
			
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			$data     = $this->prepare_item_for_response( $post, $request );
			$response = rest_ensure_response( $data );

			switch( $this->post_type ) {
				case 'sfwd-courses':
					$this->ld_course_steps_object = LDLMS_Factory_Post::course_steps( intval( $request['id'] ) );
					if ( $this->ld_course_steps_object ) {
						$course_steps = $this->ld_course_steps_object->get_steps('h');
						//error_log('course_steps<pre>'. print_r($course_steps, true) .'</pre>');
						$response->data['steps'] = $course_steps;
					}
					
					$user_course_progress = get_user_meta( get_current_user_id(), '_sfwd-course_progress', true );
					//error_log('user_course_progress<pre>'. print_r($user_course_progress, true) .'</pre>');
					if ( isset( $user_course_progress[$request['id']] ) ) {
						$response->data['student_progress'] = $user_course_progress[$request['id']];
					}
					
					
					
					
					break;
				
				default:
					break;
			}


/*			
			$object_taxonomies = get_object_taxonomies( $post );
			error_log('object_taxonomies<pre>'. print_r($object_taxonomies, true) .'</pre>');
			if ( !empty( $object_taxonomies ) ) {
				foreach( $object_taxonomies as $tax_slug ) {
					if ( !isset( $response->data[$tax_slug] ) ) {
					
						//$taxonomy = get_taxonomy( $tax_slug );
					
						$object_terms = wp_get_object_terms( $post->ID, $tax_slug, array('fields' => 'ids') );
						error_log('object_terms<pre>'. print_r($object_terms, true) .'</pre>');
						
						$response->data[$tax_slug] = $object_terms;
					}
				}
			}
*/			

			if ( is_post_type_viewable( get_post_type_object( $post->post_type ) ) ) {
				$response->link_header( 'alternate',  get_permalink( $post->ID ), array( 'type' => 'text/html' ) );
			}

			return $response;
		}
		

		public function get_item_permissions_check( $request ) {
			//error_log('in '. __FUNCTION__ );
			
			$post = $this->get_post( $request['id'] );
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			if ( 'edit' === $request['context'] && $post && ! $this->check_update_permission( $post ) ) {
				return new WP_Error( 'rest_forbidden_context', esc_html__( 'Sorry, you are not allowed to edit this post.' ), array( 'status' => rest_authorization_required_code() ) );
			}

			if ( $post && ! empty( $request['password'] ) ) {
				// Check post password, and return error if invalid.
				if ( ! hash_equals( $post->post_password, $request['password'] ) ) {
					return new WP_Error( 'rest_post_incorrect_password', esc_html__( 'Incorrect post password.' ), array( 'status' => 403 ) );
				}
			}

			// Allow access to all password protected posts if the context is edit.
			if ( 'edit' === $request['context'] ) {
				add_filter( 'post_password_required', '__return_false' );
			}

			//if ( $post ) {
			//	return $this->check_read_permission( $post );
			//}

			return true;
		}

		public function get_course_items( $request ) {
			global $learndash_post_types;
			
			//error_log('in '. basename(__FILE__) .':'. __FUNCTION__ );
			
			//error_log('_GET<pre>'. print_r($_GET, true) .'</pre>');
			//error_log('post_type['. $this->post_type .']');
			//error_log('request<pre>'. print_r($request, true) .'</pre>');
						
			$args = array();
			
			/*
			 * This array defines mappings between public API query parameters whose
			 * values are accepted as-passed, and their internal WP_Query parameter
			 * name equivalents (some are the same). Only values which are also
			 * present in $registered will be set.
			 */
			$parameter_mappings = array(
				'author'         => 'author__in',
				'author_exclude' => 'author__not_in',
				'exclude'        => 'post__not_in',
				'include'        => 'post__in',
				'menu_order'     => 'menu_order',
				'offset'         => 'offset',
				'order'          => 'order',
				'orderby'        => 'orderby',
				'page'           => 'paged',
				'parent'         => 'post_parent__in',
				'parent_exclude' => 'post_parent__not_in',
				'search'         => 's',
				'slug'           => 'post_name__in',
				'status'         => 'post_status',
			);
			$registered = $this->get_collection_params();
			//error_log('registered<pre>'. print_r($registered, true) .'</pre>');

			/*
			 * For each known parameter which is both registered and present in the request,
			 * set the parameter's value on the query $args.
			 */
			foreach ( $parameter_mappings as $api_param => $wp_param ) {
				if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
					$args[ $wp_param ] = $request[ $api_param ];
				}
			}
			
			if ( is_user_logged_in() )
				$current_user_id = get_current_user_id();
			else
				$current_user_id = 0;

			global $learndash_post_types;

			if ( in_array( $this->post_type, $learndash_post_types ) === false ) {	
				return new WP_Error( 'rest_post_invalid_id_2', esc_html__( 'Invalid post ID.', 'learndash' ), array( 'status' => 404 ) );
			}
			
			switch( $this->post_type ) {
				case 'sfwd-courses':
					if ( !empty( $request['course_id'] ) ) {
						$this->ld_course_steps_object = LDLMS_Factory_Post::course_steps( intval( $request['course_id'] ) );
						if ( $this->ld_course_steps_object ) {
							$course_steps = $this->ld_course_steps_object->get_steps();
						}
					}
					break;
				
				default:
					$args['course_id'] = $request['course_id'];
					if ( !empty( $args['course_id'] ) ) {
						$this->ld_course_steps_object = LDLMS_Factory_Post::course_steps( intval( $request['course_id'] ) );
						if ( is_null( $this->ld_course_steps_object ) ) {
							return new WP_Error( 'rest_post_invalid_id_3', esc_html__( 'Invalid Course ID.' ), array( 'status' => 404 ) );
						}
					
						$course_price_type = get_course_meta_setting( $args['course_id'], 'course_price_type' );
						$has_access = sfwd_lms_has_access( $args['course_id'], $current_user_id );
						if ( ( ! $has_access ) && ( $course_price_type != 'open' ) ) {
							return new WP_Error( 'rest_cannot_view', esc_html__( 'Sorry, you are not allowed view items.' ), array( 'status' => rest_authorization_required_code() ) );
						}

						if ( $this->post_type === 'sfwd-lessons' ) {
							$args['post__in'] = array(0);
						
							$lesson_ids = $this->ld_course_steps_object->get_children_steps( 0, $this->post_type );
							if ( !empty( $lesson_ids ) ) {
								$args['post__in'] 	= $lesson_ids;
								if ( !isset( $_GET['orderby'] ) ) 
									$args['orderby']	= 'post__in';
								if ( !isset( $_GET['order'] ) ) 
									$args['order']		= 'ASC';
							}
						
						} else if ( $this->post_type === 'sfwd-topic' ) {
							$args['post__in'] = array(0);
						
							if ( ( isset( $request['lesson_id'] ) ) && ( !empty( $request['lesson_id'] ) ) ) {
								$topic_ids = $this->ld_course_steps_object->get_children_steps( $request['lesson_id'], $this->post_type );
								if ( !empty( $topic_ids ) ) {
									$args['post__in'] 	= $topic_ids;
									if ( !isset( $_GET['orderby'] ) ) 
										$args['orderby']	= 'post__in';
									if ( !isset( $_GET['order'] ) ) 
										$args['order']		= 'ASC';
								} 
							}
						} else if ( $this->post_type === 'sfwd-quiz' ) {
							$args['post__in'] = array(0);
							
							if ( ( isset( $request['topic_id'] ) ) && ( !empty( $request['topic_id'] ) ) ) {
								$quiz_ids = $this->ld_course_steps_object->get_children_steps( $request['topic_id'], $this->post_type );
							} else if ( ( isset( $request['lesson_id'] ) ) && ( !empty( $request['lesson_id'] ) ) ) {
								$quiz_ids = $this->ld_course_steps_object->get_children_steps( $request['lesson_id'], $this->post_type );
							} else {
								$quiz_ids = $this->ld_course_steps_object->get_children_steps( 0, $this->post_type );
							}

							if ( !empty( $quiz_ids ) ) {
								$args['post__in'] 	= $quiz_ids;
								if ( !isset( $_GET['orderby'] ) ) 
									$args['orderby']	= 'post__in';
								if ( !isset( $_GET['order'] ) ) 
									$args['order']		= 'ASC';
							}
						}
					}
					
					break;
			}

			$request_params = $request->get_url_params();

			if ( !isset( $_GET['posts_per_page'] ) ) {
				$lessons = sfwd_lms_get_post_options( 'sfwd-lessons' );
				$args['posts_per_page'] = $lessons['posts_per_page'];
			}

			$args['date_query'] = array();
			// Set before into date query. Date query must be specified as an array of an array.
			if ( isset( $request['before'] ) ) {
				$args['date_query'][0]['before'] = $request['before'];
			}

			// Set after into date query. Date query must be specified as an array of an array.
			if ( isset( $request['after'] ) ) {
				$args['date_query'][0]['after'] = $request['after'];
			}

			// Force the post_type argument, since it's not a user input variable.
			$args['post_type'] = $this->post_type;

			$taxonomies = wp_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

			foreach ( $taxonomies as $taxonomy ) {
				$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;
				$tax_exclude = $base . '_exclude';

				if ( ! empty( $request[ $base ] ) ) {
					if ( !isset( $args['tax_query'] ) ) $args['tax_query'] = array();
					
					$args['tax_query'][] = array(
						'taxonomy'         => $taxonomy->name,
						'field'            => 'term_id',
						'terms'            => $request[ $base ],
						'include_children' => false,
					);
				}

				if ( ! empty( $request[ $tax_exclude ] ) ) {
					if ( !isset( $args['tax_query'] ) ) $args['tax_query'] = array();
					
					$args['tax_query'][] = array(
						'taxonomy'         => $taxonomy->name,
						'field'            => 'term_id',
						'terms'            => $request[ $tax_exclude ],
						'include_children' => false,
						'operator'         => 'NOT IN',
					);
				}
			}


			/**
			 * Filter the query arguments for a request.
			 *
			 * Enables adding extra arguments or setting defaults for a post
			 * collection request.
			 *
			 * @param array           $args    Key value array of query var to query value.
			 * @param WP_REST_Request $request The request used.
			 */
			$args = apply_filters( "learndash_rest_{$this->post_type}_query", $args, $request );
			
			$query_args = $this->prepare_items_query( $args, $request );
			//error_log('query_args<pre>'. print_r($query_args, true) .'</pre>');
			
			$posts_query = new WP_Query();
			$query_result = $posts_query->query( $query_args );

			$posts = array();
			foreach ( $query_result as $post ) {
				$data = $this->prepare_item_for_response( $post, $request );
				$posts[] = $this->prepare_response_for_collection( $data );
			}

			$page = (int) $query_args['paged'];
			$total_posts = (int) $posts_query->found_posts;

			$max_pages = ceil( $total_posts / (int) $args['posts_per_page'] );

			$response = rest_ensure_response( $posts );
			$response->header( 'X-WP-Total', (int) $total_posts );
			$response->header( 'X-WP-TotalPages', (int) $max_pages );

			$request_params = $request->get_query_params();
			if ( ! empty( $request_params['filter'] ) ) {
				// Normalize the pagination params.
				unset( $request_params['filter']['posts_per_page'] );
				unset( $request_params['filter']['paged'] );
			}
			$base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

			if ( $page > 1 ) {
				$prev_page = $page - 1;
				if ( $prev_page > $max_pages ) {
					$prev_page = $max_pages;
				}
				$prev_link = add_query_arg( 'page', $prev_page, $base );
				$response->link_header( 'prev', $prev_link );
			}
			if ( $max_pages > $page ) {
				$next_page = $page + 1;
				$next_link = add_query_arg( 'page', $next_page, $base );
				$response->link_header( 'next', $next_link );
			}

			return $response;
		}
		
		public function get_course_items_permissions_check( $request ) {
			global $learndash_post_types;

			// THIS IS DISABLED FOR TESTING
			return true;
			
			
			if ( is_user_logged_in() )
				$current_user_id = get_current_user_id();
			else
				$current_user_id = 0;
			

			if ( in_array( $this->post_type, $learndash_post_types ) === false ) {	
				return new WP_Error( 'rest_post_invalid_id_2', esc_html__( 'Invalid post ID.', 'learndash' ), array( 'status' => 404 ) );
			}

			switch( $this->post_type ) {
				case 'sfwd-courses':
					break;
				
				default:
					$course = get_post( $request['course_id'] );
					if ( ( !$course ) || ( $course->post_type !== 'sfwd-courses' ) ) {
						return new WP_Error( 'rest_post_invalid_id_1', esc_html__( 'Invalid Course ID.' ), array( 'status' => 404 ) );
					}

					$course_price_type = get_course_meta_setting( $course->ID, 'course_price_type' );
					$has_access = sfwd_lms_has_access( $course->ID, $current_user_id );
					if ( ( ! $has_access ) && ( $course_price_type != 'open' ) ) {
						return new WP_Error( 'rest_cannot_view', esc_html__( 'Sorry, you are not allowed view items.' ), array( 'status' => rest_authorization_required_code() ) );
					}
			}
			return true;
		}
		
		
		
		function register_fields() {
			global $sfwd_lms;
			
			$post_args_fields = $sfwd_lms->get_post_args_section( $this->post_type, 'fields' );
			if ( !empty( $post_args_fields ) ) {
				foreach( $post_args_fields as $field_key => $field_set ) {
					//if ( $field_key == 'course_prerequisite_compare' ) {
						//error_log('field_set<pre>'. print_r($field_set, true) .'</pre>');
						//}
					
					if ( ( isset( $field_set['show_in_rest'] ) ) && ( $field_set['show_in_rest'] === true ) ) {	
						if ( ( isset( $field_set['rest_args'] ) ) && ( is_array( $field_set['rest_args'] ) ) )
							$field_args = $field_set['rest_args'];
						else
							$field_args = array();
						
						if ( ( !isset( $field_args['get_callback'] ) ) || ( empty( $field_args['get_callback'] ) ) ) {
							$field_args['get_callback'] = array( $this, 'ld_get_field_value' );
						}

						if ( ( !isset( $rest_field_args['update_callback'] ) ) || ( empty( $rest_field_args['update_callback'] ) ) ) {
							$field_args['update_callback'] = array( $this, 'ld_update_field_value' );
						}
						
						if ( ( !isset( $field_args['schema'] ) ) || ( empty( $field_args['schema'] ) ) ) {
							$field_args['schema'] = array();
						}
						
						if ( ( !isset( $field_args['schema']['name'] ) ) || ( empty( $field_args['schema']['name'] ) ) ) {
							if ( isset( $field_set['name'] ) )
								$field_args['schema']['description'] 	= $field_set['name'];
						}
						
						if ( ( !isset( $field_args['schema']['type'] ) ) || ( empty( $field_args['schema']['type'] ) ) ) {
							if ( isset( $field_set['type'] ) ) {
								switch( $field_set['type'] ) {
									case 'select':
									case 'multiselect':
										$field_args['schema']['type'] = 'string';
										break;
										
									case 'checkbox':
										$field_args['schema']['type'] = 'boolean';
										break;
									
									default:
										$field_args['schema']['type'] = $field_set['type'];	
										break;
								}
							}
						}
						
						if ( ( !isset( $field_args['schema']['required'] ) ) || ( empty( $field_args['schema']['required'] ) ) ) {
							$field_args['schema']['required'] = false;
						}
						
						if ( ( !isset( $field_args['schema']['sanitize_callback'] ) ) || ( empty( $field_args['schema']['sanitize_callback'] ) ) ) {
							$field_args['schema']['sanitize_callback']     = 'sanitize_key';
						}
						
						if ( ( !isset( $field_args['schema']['validate_callback'] ) ) || ( empty( $field_args['schema']['validate_callback'] ) ) ) {
							$field_args['schema']['validate_callback']     = 'rest_validate_request_arg';
						}
						
						if ( ( !isset( $field_args['schema']['default'] ) ) || ( empty( $field_args['schema']['default'] ) ) ) {	
							if ( isset( $field_set['default'] ) )
								$field_args['schema']['default']			= $field_set['default'];
						}
						
						if ( ( !isset( $field_args['schema']['initial_options'] ) ) || ( empty( $field_args['schema']['initial_options'] ) ) ) {	
							if ( ( isset( $field_set['initial_options'] ) ) && ( !empty( $field_set['initial_options'] ) ) )
								$field_args['schema']['enum'] = array_keys( $field_set['initial_options'] );
							
						}
						
						//if ( $field_key == 'course_prerequisite_compare' ) {
						//	error_log('field_key['. $field_key .']<pre>'. print_r($field_args, true) .'</pre>');
						//}

						register_rest_field( 
							$this->post_type, 
							$field_key, 
							$field_args
						);
						
					}
				}
			}
		}
		
		function ld_get_field_value( array $postdata, $field_name, WP_REST_Request $request, $post_type ) {
			if ( ( isset( $postdata['id'] ) ) && ( !empty( $postdata['id'] ) ) ) {
				$ld_post = get_post( $postdata['id'] );
				if ( ( is_a( $ld_post, 'WP_Post' ) ) && ( $ld_post->post_type == $this->post_type ) ) {
					return learndash_get_setting( $ld_post, $field_name );
				}
			}
		}

		function ld_update_field_value( $value, WP_Post $post, $field_name, WP_REST_Request $request, $post_type ) {
			learndash_update_setting( $post->ID, $field_name, $value );
			
			return true;
		}

		function update_course_item_permissions_check( $request ) {
			if ( ! current_user_can( 'edit_courses', $request['id'] ) ) {
				return new WP_Error( 'rest_cannot_edit', esc_html__( 'Sorry, you are not allowed to edit/update this post.', 'learndash' ), array( 'status' => rest_authorization_required_code() ) );
			}

			return true;
		}
	}
}
