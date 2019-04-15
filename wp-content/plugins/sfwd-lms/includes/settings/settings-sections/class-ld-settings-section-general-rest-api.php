<?php
/**
 * LearnDash Settings Section for REST API Metabox.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_Section_General_REST_API' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_Section_General_REST_API extends LearnDash_Settings_Section {

		/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			$this->settings_page_id = 'learndash_lms_settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash_settings_rest_api';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'learndash_settings_rest_api';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_rest_api';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'REST API Settings', 'learndash' );

			$this->settings_section_description = esc_html__( 'These settings control the REST API support within LearnDash.', 'learndash' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ! isset( $this->setting_option_values['enabled'] ) ) {
				$this->setting_option_values['enabled'] = 'yes';
			}

			if ( ( ! isset( $this->setting_option_values['sfwd-courses'] ) ) || ( empty( $this->setting_option_values['sfwd-courses'] ) ) ) { 
				$this->setting_option_values['sfwd-courses'] = 'sfwd-courses';
			}

			if ( ( ! isset( $this->setting_option_values['sfwd-lessons'] ) ) || ( empty( $this->setting_option_values['sfwd-lessons'] ) ) ) { 
				$this->setting_option_values['sfwd-lessons'] = 'sfwd-lessons';
			}

			if ( ( ! isset( $this->setting_option_values['sfwd-topic'] ) ) || ( empty( $this->setting_option_values['sfwd-topic'] ) ) ) { 
				$this->setting_option_values['sfwd-topic'] = 'sfwd-topic';
			}

			if ( ( ! isset( $this->setting_option_values['sfwd-quiz'] ) ) || ( empty( $this->setting_option_values['sfwd-quiz'] ) ) ) { 
				$this->setting_option_values['sfwd-quiz'] = 'sfwd-quiz';
			}

			if ( ( ! isset( $this->setting_option_values['users'] ) ) || ( empty( $this->setting_option_values['users'] ) ) ) { 
				$this->setting_option_values['users'] = 'users';
			}

			if ( ( ! isset( $this->setting_option_values['groups'] ) ) || ( empty( $this->setting_option_values['groups'] ) ) ) { 
				$this->setting_option_values['groups'] = 'groups';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 */

		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'enabled' => array(
					'name' => 'enabled',
					'type' => 'checkbox',
					'label' => esc_html__( 'Enabled REST API', 'learndash' ),
					'help_text' => esc_html__( 'This setting will enable the LearnDash REST API custom namespace and endpoints.', 'learndash' ),
					'value' => isset( $this->setting_option_values['enabled'] ) ? $this->setting_option_values['enabled'] : 'no',
					'options' => array(
						'yes' => esc_html__( 'Yes', 'learndash' ),
					),
				),
				'sfwd-courses' => array(
					'name' => 'sfwd-courses',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Course.
						_x( '%s Endpoint Slug', 'placeholder: Course', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'course' )
					),
					'help_text' => esc_html__( 'Leave blank to user the default sfwd-courses.', 'learndash' ),
					'value' => $this->setting_option_values['sfwd-courses'],
					'class' => 'regular-text',
				),
				'sfwd-lessons' => array(
					'name' => 'sfwd-lessons',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Lesson.
						_x( '%s Endpoint Slug', 'placeholder: Lesson', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'lesson' )
					),
					'help_text' => esc_html__( 'Leave blank to user the default sfwd-lessons.', 'learndash' ),
					'value' => $this->setting_option_values['sfwd-lessons'],
					'class' => 'regular-text',
				),
				'sfwd-topic' => array(
					'name' => 'sfwd-topic',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Topic.
						_x( '%s Endpoint Slug', 'placeholder: Topic', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'topic' )
					),
					'help_text' => esc_html__( 'Leave blank to user the default sfwd-topic.', 'learndash' ),
					'value' => $this->setting_option_values['sfwd-topic'],
					'class' => 'regular-text',
				),
				'sfwd-quiz' => array(
					'name' => 'sfwd-quiz',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Quizc.
						_x( '%s Endpoint Slug', 'placeholder: Quiz', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'quiz' )
					),
					'help_text' => esc_html__( 'Leave blank to user the default sfwd-quiz.', 'learndash' ),
					'value' => $this->setting_option_values['sfwd-quiz'],
					'class' => 'regular-text',
				),
				'users' => array(
					'name' => 'users',
					'type' => 'text',
					'label' => esc_html_x( 'User Endpoint Slug', 'learndash' ),
					'help_text' => esc_html__( 'Leave blank to user the default user.', 'learndash' ),
					'value' => $this->setting_option_values['users'],
					'class' => 'regular-text',
				),
				'groups' => array(
					'name' => 'groups',
					'type' => 'text',
					'label' => esc_html_x( 'Groups Endpoint Slug', 'learndash' ),
					'help_text' => esc_html__( 'Leave blank to users the default group.', 'learndash' ),
					'value' => $this->setting_option_values['groups'],
					'class' => 'regular-text'
				),
			);

			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_General_REST_API::add_section_instance();
} );
