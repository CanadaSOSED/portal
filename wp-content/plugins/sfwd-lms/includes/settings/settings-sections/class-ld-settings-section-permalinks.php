<?php
/**
 * LearnDash Settings Section for Permalinks section shown on WP Settings > Permalinks page..
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_Section_Permalinks' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_Section_Permalinks extends LearnDash_Settings_Section {

		/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			$this->settings_page_id = 'permalink';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash_settings_permalinks';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'learndash_settings_permalinks';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'learndash_settings_permalinks';

			// Section label/header.
			$this->settings_section_label = __( 'LearnDash Permalinks', 'learndash' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = __( 'Controls the URL slugs for the custom posts used by LearnDash.', 'learndash' );

			add_action( 'admin_init', array( $this, 'admin_init' ) );

			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {
				parent::__construct(); 			
				$this->save_settings_fields();
			}
		}

		/**
		 * Hook into the admin init action to fire up the LD settings page init processing.
		 * Remember the Permalinks page is not a LD page.
		 */
		public function admin_init() {
			do_action( 'learndash_settings_page_init', $this->settings_page_id );
		}

		/**
		 * Function to handle metabox init.
		 *
		 * @param string $settings_screen_id Screen ID of current page.
		 */
		public function add_meta_boxes( $settings_screen_id = '' ) {
			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {

				add_meta_box(
					$this->metabox_key,
					$this->settings_section_label,
					array( $this, 'show_meta_box' ),
					$this->settings_screen_id,
					$this->metabox_context,
					$this->metabox_priority
				);
			}
		}

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( $this->setting_option_values === false ) {
				$this->setting_option_values = array();

				// On the initial if we don't have saved values we grab them from the Custom Labels.
				$custom_label_settings = get_option( 'learndash_custom_label_settings', array() );

				if ( ( isset( $custom_label_settings['courses'] ) ) && ( ! empty( $custom_label_settings['courses'] ) ) ) {
					$this->setting_option_values['courses'] = LearnDash_Custom_Label::label_to_slug( 'courses' );
				}

				if ( ( isset( $custom_label_settings['lessons'] ) ) && ( ! empty( $custom_label_settings['lessons'] ) ) ) {
					$this->setting_option_values['lessons'] = LearnDash_Custom_Label::label_to_slug( 'lessons' );
				}

				if ( ( isset( $custom_label_settings['topic'] ) ) && ( ! empty( $custom_label_settings['topic'] ) ) ) {
					$this->setting_option_values['topics'] = LearnDash_Custom_Label::label_to_slug( 'topic' );
				}

				if ( ( isset( $custom_label_settings['quizzes'] ) ) && ( ! empty( $custom_label_settings['quizzes'] ) ) ) {
					$this->setting_option_values['quizzes'] = LearnDash_Custom_Label::label_to_slug( 'quizzes' );
				}

				// As we don't have existing values we want to save here and force the flush rewrite.
				update_option( $this->settings_section_key, $this->setting_option_values );
				set_transient( 'sfwd_lms_rewrite_flush', true );
			}

			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values,
				array(
					'courses' => 'courses',
					'lessons' => 'lessons',
					'topics' => 'topic',
					'quizzes' => 'quizzes',
				)
			);
		}

		/**
		 * Initialize the metabox settings fields.
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'courses' => array(
					'name' => 'courses',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Courses.
						_x( '%s', 'placeholder: Courses', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'courses' )
					),
					'value' => $this->setting_option_values['courses'],
					'class' => 'regular-text',
				),
				'lessons' => array(
					'name' => 'lessons',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Lessons.
						_x( '%s', 'placeholder: Lessons', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'lessons' )
					),
					'value' => $this->setting_option_values['lessons'],
					'class' => 'regular-text',
				),
				'topics' => array(
					'name' => 'topics',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Topics.
						_x( '%s', 'placeholder: Topics', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'topics' )
					),
					'value' => $this->setting_option_values['topics'],
					'class' => 'regular-text',
				),
				'quizzes' => array(
					'name' => 'quizzes',
					'type' => 'text',
					'label' => sprintf(
						// translators: placeholder: Quizzes.
						_x( '%s', 'placeholder: Quizzes', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'quizzes' )
					),
					'value' => $this->setting_option_values['quizzes'],
					'class' => 'regular-text'
				),
				'nested_urls' => array(
					'name' => 'nested_urls',
					'type' => 'checkbox',
					'label' => __( 'Enable Nested URLs', 'learndash' ),
					'desc' => sprintf(
						// translators: placeholders: Lesson, Topic, Quiz, Course, Site Home URL, URL to Course Builder Settings.
						_x( 'This option will restructure %1$s, %2$s and %3$s URLs so they are nested hierarchically within the %4$s URL.<br />For example instead of the default topic URL <code>%5$s</code> the nested URL would be <code>%6$s</code>. If <a href="%7$s">Course Builder Share Steps</a> has been enabled this setting is also automatically enabled.', 
						'placeholders: Lesson, Topic, Quiz, Course, Site Home URL, URL to Course Builder Settings', 'learndash' ), 
						LearnDash_Custom_Label::get_label( 'lesson' ), 
						LearnDash_Custom_Label::get_label( 'topic' ), 
						LearnDash_Custom_Label::get_label( 'quiz' ), 
						LearnDash_Custom_Label::get_label( 'course' ),
						get_option( 'home' ) . '/' . $this->setting_option_values['topics'] . '/topic-slug',
						get_option( 'home' ) . '/' . $this->setting_option_values['courses'] . '/course-slug/' . $this->setting_option_values['lessons'] . '/lesson-slug/' . $this->setting_option_values['topics'] . '/topic-slug',
						admin_url( 'admin.php?page=courses-options' )
					),
					'value' => isset( $this->setting_option_values['nested_urls'] ) ? $this->setting_option_values['nested_urls'] : '',
					'options' => array(
						'yes' => __( 'Yes', 'learndash' ),
					),
				),

				'nonce' => array(
					'name' => 'nonce',
					'type' => 'hidden',
					'label' => '',
					'value' => wp_create_nonce( 'learndash_permalinks_nonce' ),
					'class' => 'hidden',
				),
			);

			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Save the metabox fields. This is needed due to special processing needs.
		 */
		public function save_settings_fields() {

			if ( isset( $_POST[ $this->setting_field_prefix ] ) ) {
				if ( ( isset( $_POST[ $this->setting_field_prefix ]['nonce'] ) ) && ( wp_verify_nonce( $_POST[ $this->setting_field_prefix ]['nonce'], 'learndash_permalinks_nonce' ) ) ) {

					$post_fields = $_POST[ $this->setting_field_prefix ];

					if ( ( isset( $post_fields['courses'] ) ) && ( ! empty( $post_fields['courses'] ) ) ) {
						$this->setting_option_values['courses'] = $this->esc_url( $post_fields['courses'] );

						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed.
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['lessons'] ) ) && ( ! empty( $post_fields['lessons'] ) ) ) {
						$this->setting_option_values['lessons'] = $this->esc_url( $post_fields['lessons'] );

						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed.
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['topics'] ) ) && ( ! empty( $post_fields['topics'] ) ) ) {
						$this->setting_option_values['topics'] = $this->esc_url( $post_fields['topics'] );

						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed.
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['quizzes'] ) ) && ( ! empty( $post_fields['quizzes'] ) ) ) {
						$this->setting_option_values['quizzes'] = $this->esc_url( $post_fields['quizzes'] );

						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed.
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $post_fields['nested_urls'] ) ) && ( ! empty( $post_fields['nested_urls'] ) ) ) {
						$this->setting_option_values['nested_urls'] = $this->esc_url( $post_fields['nested_urls'] );

						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed.
						set_transient( 'sfwd_lms_rewrite_flush', true );
					} else {
						// We check the Course Options > Course Builder setting. If this is set to 'yes' then we MUST keep the nested URLs set to true.
						if ( ! isset( $this->setting_option_values['nested_urls'] ) ) {
							$this->setting_option_values['nested_urls'] = 'no';
						}

						if ( 'yes' !== $this->setting_option_values['nested_urls'] ) {
							$learndash_settings_courses_builder = get_option( 'learndash_settings_courses_builder', array() );
							if ( ! isset( $learndash_settings_courses_builder['shared_steps'] ) ) {
								$learndash_settings_courses_builder['shared_steps'] = 'no';
							}

							if ( 'yes' === $learndash_settings_courses_builder['shared_steps'] ) {
								$this->setting_option_values['nested_urls'] = 'yes';

								// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed.
								set_transient( 'sfwd_lms_rewrite_flush', true );
							}
						}
					}

					update_option( $this->settings_section_key, $this->setting_option_values );
				}
			}
		}

		/**
		 * Class utility function to escape the URL
		 *
		 * @param string $value URL to Escape.
		 *
		 * @return string filtered URL.
		 */
		public function esc_url( $value = '' ) {
			if ( ! empty( $value ) ) {
				$value = esc_url_raw( trim( $value ) );
				$value = str_replace( 'http://', '', $value );
				return untrailingslashit( $value );
			}
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_Permalinks::add_section_instance();
} );
