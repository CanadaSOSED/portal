<?php
if ( !defined( 'LEARNDASH_REST_API_ENABLED' ) ) {
	define( 'LEARNDASH_REST_API_ENABLED', false );
} 

if ( !defined( 'LEARNDASH_REST_API_NAMESPACE' ) ) {
	define( 'LEARNDASH_REST_API_NAMESPACE', 'ldlms' );
} 

//if ( !defined('LEARNDASH_REST_API_VERSION' ) ) {
//	define( 'LEARNDASH_REST_API_VERSION', 'v1' );
//} 

if ( !class_exists( 'LearnDash_REST_API' ) ) {
	class LearnDash_REST_API {

		/**
		 * @var The reference to *Singleton* instance of this class
		 */
		private static $instance;

		//private static $api_versions = array( 'v1' );
		//private static $api_version_instances = array( );

		private $controllers = array();
		
		function __construct() {
			$this->controllers = array(
				// v1 controllers.
				'LD_REST_Posts_Courses_Controller' 			=> 	dirname( __FILE__ ) . '/v1/class-ld-rest-posts-courses-controller.php',
				'LD_REST_Posts_Lessons_Controller' 			=> 	dirname( __FILE__ ) . '/v1/class-ld-rest-posts-lessons-controller.php',
				'LD_REST_Posts_Topics_Controller'			=>	dirname( __FILE__ ) . '/v1/class-ld-rest-posts-topics-controller.php',
				'LD_REST_Posts_Quizzes_Controller'			=>	dirname( __FILE__ ) . '/v1/class-ld-rest-posts-quizzes-controller.php',
			);

			// These are needed because we are calling before the LD Settings init process has fired. 
			// @todo figure out a better solution than calling the following twice. 
//			do_action('learndash_settings_sections_fields_init');
//			do_action('learndash_settings_sections_init');
			
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) == 'yes') {
				$this->controllers['LD_REST_Terms_Course_Category_Controller'] 	= dirname( __FILE__ ) . '/v1/class-ld-rest-terms-course-category-controller.php';
			}
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_tag' ) == 'yes') {
				$this->controllers['LD_REST_Terms_Course_Tag_Controller']		= dirname( __FILE__ ) . '/v1/class-ld-rest-terms-course-tag-controller.php';
			}
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_category' ) == 'yes') {
				$this->controllers['LD_REST_Terms_Lesson_Category_Controller']	= dirname( __FILE__ ) . '/v1/class-ld-rest-terms-lesson-category-controller.php';
			}
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_tag' ) == 'yes') {
				$this->controllers['LD_REST_Terms_Lesson_Tag_Controller']		= dirname( __FILE__ ) . '/v1/class-ld-rest-terms-lesson-tag-controller.php';
			}
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_category' ) == 'yes') {
				$this->controllers['LD_REST_Terms_Topic_Category_Controller']	= 	dirname( __FILE__ ) . '/v1/class-ld-rest-terms-topic-category-controller.php';
			}
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_tag' ) == 'yes' ) {
				$this->controllers['LD_REST_Terms_Topic_Tag_Controller']	= 	dirname( __FILE__ ) . '/v1/class-ld-rest-terms-topic-tag-controller.php';
			}
						
			add_action( 'rest_api_init', array( $this, 'rest_api_init' ), 10 );
			
			add_filter( 'learndash_post_args', array( $this, 'filter_post_args' ), 1 );
		}			
		
		function rest_api_init() {

			if ( ( defined( 'LEARNDASH_REST_API_ENABLED' ) ) && ( LEARNDASH_REST_API_ENABLED === true ) ) {

				$this->controllers = apply_filters('learndash-rest-api-controllers', $this->controllers );
				if ( !empty( $this->controllers ) ) {

					include_once( dirname( __FILE__ ) . '/v1/class-ld-rest-posts-controller.php' );
					include_once( dirname( __FILE__ ) . '/v1/class-ld-rest-terms-controller.php' );
				
					foreach ( $this->controllers as $controller => $file ) {
					
						if ( file_exists( $file ) ) {
							include_once( $file );
					
							$this->$controller = new $controller();
							$this->$controller->register_routes();
						}
					}
				}
			}
		}

		function filter_post_args( $post_args = array() ) {
			if ( ( !defined( 'LEARNDASH_REST_API_ENABLED' ) ) || ( LEARNDASH_REST_API_ENABLED !== true ) ) {
			
				if ( !empty( $post_args ) ) {
					foreach( $post_args as $post_args_key => $post_args_set ) {
						if ( ( isset( $post_args_set['fields'] ) ) && ( !empty( $post_args_set['fields'] ) ) ) {
							foreach( $post_args_set['fields'] as $fields_key => $field_args ) {
								if ( isset( $field_args['show_in_rest'] ) )
									unset( $post_args[$post_args_key]['fields'][$fields_key]['show_in_rest'] );
								if ( isset( $field_args['rest_args'] ) )
									unset( $post_args[$post_args_key]['fields'][$fields_key]['rest_args'] );
							}
						}
					}
				}
			}			
			
			return $post_args;
		}


		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @return The *Singleton* instance.
		 */
		public static function get_instance() {
			if ( null === static::$instance ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		  * Override class function for 'this'.
		  *
		  * This function handles out Singleton logic in 
		  * @return reference to current instance
		  */
		static function this() {
			return self::$instance;
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_REST_API::get_instance();
}, 99 );