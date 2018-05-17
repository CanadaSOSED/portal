<?php
if ( ( !class_exists( 'LD_REST_Posts_Courses_Controller' ) ) && ( class_exists( 'LD_REST_Posts_Controller' ) ) ) {
	class LD_REST_Posts_Courses_Controller extends LD_REST_Posts_Controller {
		
		public function __construct( $post_type = '' ) {
			$this->post_type = 'sfwd-courses';
			$this->taxonomies = array();
			
			parent::__construct( $this->post_type );
			$this->namespace = LEARNDASH_REST_API_NAMESPACE .'/'. $this->version;
			$this->rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'courses' );
		}
		
	    public function register_routes() {
			
			$this->register_fields();
			
			$collection_params = $this->get_collection_params();
			$schema = $this->get_item_schema();
			
			$get_item_args = array(
				'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
			);
			if ( isset( $schema['properties']['password'] ) ) {
				$get_item_args['password'] = array(
					'description' => esc_html__( 'The password for the post if it is password protected.' ),
					'type'        => 'string',
				);
			}

			register_rest_route( 
	  			$this->namespace, 
				'/' . $this->rest_base, 
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_course_items' ),
						'permission_callback' => array( $this, 'get_course_items_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'create_item' ),
						'permission_callback' => array( $this, 'create_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				) 
			);

			register_rest_route( 
				$this->namespace, 
				'/' . $this->rest_base . '/(?P<id>[\d]+)', 
				array(
					'args' => array(
						'id' => array(
							'description' 	=> esc_html__( 'Unique identifier for the object.' ),
							'required'		=> true,
							'type'        	=> 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_course_item' ),
						'permission_callback' => array( $this, 'get_course_items_permissions_check' ),
						'args'                => $get_item_args,
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_course_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_item' ),
						'permission_callback' => array( $this, 'delete_item_permissions_check' ),
						'args'                => array(
							'force' => array(
								'type'        => 'boolean',
								'default'     => false,
								'description' => esc_html__( 'Whether to bypass trash and force deletion.' ),
							),
						),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				) 
			);

			register_rest_route( 
				$this->namespace, 
				'/' . $this->rest_base . '/(?P<id>[\d]+)/enroll', 
				array(
					'args' => array(
						'id' => array(
							'description' => esc_html__( 'Course ID to enroll user into.' ),
							'required' => true,
							'type' => 'integer',
						),
					),
					array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'course_enroll_user' ),
					),
				) 
			);
			
			
/*
			$taxonomy_objects = get_object_taxonomies( $this->post_type, 'objects' );
			error_log('taxonomy_objects<pre>'. print_r($taxonomy_objects, true) .'</pre>');
			
			if ( isset( $taxonomy_objects['ld_course_category'] ) ) {
				include( dirname( __FILE__ ) . '/class-ld-rest-terms-course-category-controller.php' );
				
				
				$ld_course_category_tax_object = new LD_REST_Terms_Course_Category_Controller('ld_course_category');
				if ( $ld_course_category_tax_object ) {
					$ld_course_category_tax_object->register_routes();
					$this->taxonomies['ld_course_category'] = $ld_course_category_tax_object;
				}
			}
*/
	    }
		
		function get_items_permissions_check( $request ) {
			if ( ( 'edit' === $request['context'] ) || ( 'view' === $request['context'] ) ) {
				return true;
			}
		}
		
		function course_enroll_user( $request ) {
			$current_user_id = get_current_user_id();
			if ( empty( $current_user_id ) ) {
				return new WP_Error( 'rest_not_logged_in', esc_html__( 'You are not currently logged in.', 'learndash' ), array( 'status' => 401 ) );
			}
			$current_user = wp_get_current_user();

			$course = $this->get_post( $request['id'] );
			if ( is_wp_error( $course ) ) {
				return $course;
			}

//			if ( $course->post_type != $this->post_type ) {
//				return new WP_Error( 'rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'learndash' ), array( 'status' => 404 ) );
//			}
//			
//			$course_price_type = get_course_meta_setting( $course->ID, 'course_price_type' );
//			if ( $course_price_type == 'closed' ) {
//				return new WP_Error( 'rest_course_enroll_closed', esc_html__( 'Course Enroll Closed.', 'learndash' ), array( 'status' => 403 ) );
//			}
			
			// At this point we have a valid course and valid user
			// So we call a filter to see if anything wants to prevent us from enrolling this user. 
			$has_access = sfwd_lms_has_access( $course->ID, $current_user->ID );
			if ( ! $has_access ) {
				$user_enrolled = ld_update_course_access( $current_user->ID, $course->ID );
			}

			$data = array( 'ld_course_enrolled_date_gmt' => $this->prepare_date_response( current_time( 'mysql' ) ) );

			// Create the response object
			$response = rest_ensure_response( $data );

			// Add a custom status code
			$response->set_status( 200 );

			return $response;
		}
		// End of functions
	}
}
