<?php
if ( ( !class_exists( 'LD_REST_Posts_Quizzes_Controller' ) ) && ( class_exists( 'LD_REST_Posts_Controller' ) ) ) {
	class LD_REST_Posts_Quizzes_Controller extends LD_REST_Posts_Controller {
		
		public function __construct( $post_type = '' ) {
			$this->post_type = 'sfwd-quiz';
			
			parent::__construct( $this->post_type );
			$this->namespace = LEARNDASH_REST_API_NAMESPACE .'/'. $this->version;
			$this->rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'quizzes' );
			$this->topics_rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'topics' );
			$this->lessons_rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'lessons' );
			$this->courses_rest_base = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'courses' );
		}
		
	    public function register_routes() {
			
			$this->register_fields();
			
			//error_log('in '. __FUNCTION__ );
			//error_log('namespace['. $this->namespace .']');
			//error_log('rest_base['. $this->rest_base .']');

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



			// Quiz Default
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
							'description' => esc_html__( 'Unique identifier for the Quiz object.' ),
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => $get_item_args,
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
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
			


			// Quiz at Course
			register_rest_route( 
	  			$this->namespace, 
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->rest_base, 
				array(
					'args' => array(
						'course_id' => array(
							'description' => esc_html__( 'Unique identifier for the Course object.' ),
							'type'        => 'integer',
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
							'description' => esc_html__( 'Unique identifier for the Quiz object.' ),
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => $get_item_args,
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
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
			


			// Quiz at Lesson

			register_rest_route( 
	  			$this->namespace, 
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->lessons_rest_base . '/(?P<lesson_id>[\d]+)/' . $this->rest_base, 
				array(
					'args' => array(
						'course_id' => array(
							'description' => esc_html__( 'Unique identifier for the Course object.' ),
							'type'        => 'integer',
						),
						'lesson_id' => array(
							'description' => esc_html__( 'Unique identifier for the Lesson object.' ),
							'type'        => 'integer',
						),
						'topic_id' => array(
							'description' => esc_html__( 'Unique identifier for the Topic object.' ),
							'type'        => 'integer',
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

			register_rest_route( 
				$this->namespace, 
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->lessons_rest_base . '/(?P<lesson_id>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)', 
				array(
					'args' => array(
						'course_id' => array(
							'description' => esc_html__( 'Unique identifier for the Course object.' ),
							'type'        => 'integer',
						),
						'lesson_id' => array(
							'description' => esc_html__( 'Unique identifier for the Lesson object.' ),
							'type'        => 'integer',
						),
						'id' => array(
							'description' => esc_html__( 'Unique identifier for the object.' ),
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => $get_item_args,
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
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


			// Quiz at Topic
			register_rest_route( 
	  			$this->namespace, 
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->lessons_rest_base . '/(?P<lesson_id>[\d]+)/' . $this->topics_rest_base . '/(?P<topic_id>[\d]+)/' . $this->rest_base, 
				array(
					'args' => array(
						'course_id' => array(
							'description' => esc_html__( 'Unique identifier for the Course object.' ),
							'type'        => 'integer',
						),
						'lesson_id' => array(
							'description' => esc_html__( 'Unique identifier for the Lesson object.' ),
							'type'        => 'integer',
						),
						'topic_id' => array(
							'description' => esc_html__( 'Unique identifier for the Topic object.' ),
							'type'        => 'integer',
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

			register_rest_route( 
				$this->namespace, 
				'/' . $this->courses_rest_base . '/(?P<course_id>[\d]+)/' . $this->lessons_rest_base . '/(?P<lesson_id>[\d]+)/' . $this->topics_rest_base . '/(?P<topic_id>[\d]+)/' . $this->rest_base . '/(?P<id>[\d]+)', 
				array(
					'args' => array(
						'course_id' => array(
							'description' => esc_html__( 'Unique identifier for the Course object.' ),
							'type'        => 'integer',
						),
						'lesson_id' => array(
							'description' => esc_html__( 'Unique identifier for the Lesson object.' ),
							'type'        => 'integer',
						),
						'topic_id' => array(
							'description' => esc_html__( 'Unique identifier for the Topic object.' ),
							'type'        => 'integer',
						),
						'id' => array(
							'description' => esc_html__( 'Unique identifier for the Quiz object.' ),
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => $get_item_args,
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_item_permissions_check' ),
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


	    }
	}
}
