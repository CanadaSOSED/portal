<?php
if ( ( !class_exists( 'LD_REST_Posts_Lessons_Controller' ) ) && ( class_exists( 'LD_REST_Posts_Controller' ) ) ) {
	class LD_REST_Posts_Lessons_Controller extends LD_REST_Posts_Controller {
		
		public function __construct( $post_type = '' ) {
			$this->post_type = 'sfwd-lessons';
			
			parent::__construct( $this->post_type );
			$this->namespace = LEARNDASH_REST_API_NAMESPACE .'/'. $this->version;
			$this->rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'lessons' );
			$this->courses_rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'courses' );
		}
		
	    public function register_routes() {
			
			$this->register_fields();
			
//			error_log('in '. __FUNCTION__ );
//			error_log('namespace['. $this->namespace .']');
//			error_log('rest_base['. $this->rest_base .']');

			$collection_params = $this->get_collection_params();

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
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->rest_base, 
				array(
					'args' => array(
						'course_id' => array(
							'description' 	=> esc_html__( 'Unique identifier for the Course object.' ),
							'required'		=> true,
							'type'        	=> 'integer',
						),
					),
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
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)', 
				array(
					'args' => array(
						'course_id' => array(
							'description' => esc_html__( 'Unique identifier for the Course object.' ),
							'type'        => 'integer',
						),
						'id' => array(
							'description' => esc_html__( 'Unique identifier for the Lesson object.' ),
							'type'        => 'integer',
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
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)/mark_complete', 
				array(
					'args' => array(
						'course_id' => array(
							'description' => esc_html__( 'Course ID.' ),
							'required' => true,
							'type' => 'integer',
						),
						'id' => array(
							'description' => esc_html__( 'Lesson ID.' ),
							'required' => true,
							'type' => 'integer',
						),
					),
					array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'lesson_mark_complete' ),
					),
				) 
			);
	    }
		
		function lesson_mark_complete( $request ) {
			//error_log('in '. basename(__FILE__) .':'. __FUNCTION__ );

			$course_id = $request['course_id'];
			//error_log('course_id['. $course_id .']');
			
			$lesson_id = $request['id'];
			//error_log('lesson_id['. $lesson_id .']');

			if ( empty( $course_id ) ) {
				return new WP_Error( 'rest_post_invalid_id_X', esc_html__( 'Invalid Course ID.' ), array( 'status' => 404 ) );
			}
			
			if ( empty( $lesson_id ) ) {
				return new WP_Error( 'rest_post_invalid_id_Y', esc_html__( 'Invalid Lesson ID.' ), array( 'status' => 404 ) );
			}

			$current_user_id = get_current_user_id();
			if ( empty( $current_user_id ) ) {
				return new WP_Error( 'rest_not_logged_in', esc_html__( 'You are not currently logged in.', 'learndash' ), array( 'status' => 401 ) );
			}
			//$current_user = wp_get_current_user();

			$has_access = sfwd_lms_has_access( $course->ID, $current_user->ID );
			if ( ( ! $has_access ) && ( $course_price_type != 'open' ) ) {
				return new WP_Error( 'rest_cannot_view', esc_html__( 'Sorry, you are not allowed view items.' ), array( 'status' => rest_authorization_required_code() ) );
			}

			$return = learndash_process_mark_complete( $current_user_id, $lesson_id );
			if ( $return === true ) {
				$data = array( 
					'completed_status' => true,
					'completed_date_gmt' => $this->prepare_date_response( current_time( 'mysql' ) ) 
				);

				// Create the response object
				$response = rest_ensure_response( $data );

				// Add a custom status code
				$response->set_status( 200 );

				return $response;
			}
		}
				
		// End of functions
	}
}
