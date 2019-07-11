<?php
/**
 * LearnDash Settings Section for Question Taxonomies Metabox.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_Questions_Taxonomies' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_Questions_Taxonomies extends LearnDash_Settings_Section {

		/**
		 * Protected constructor for class
		 */
		protected function __construct() {

			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-question_page_questions-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'questions-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash_settings_questions_taxonomies';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'learndash_settings_questions_taxonomies';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'taxonomies';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( '%s Taxonomies', 'placeholder: Question', 'learndash' ),
				LearnDash_Custom_Label::get_label( 'question' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				wp_kses_post(
					// translators: placeholder: Quiz, Questions.
					_x( '<p>Control which Taxonomies can be used with the LearnDash %1$s %2$s.</p>', 'placeholder: Quiz, Questions', 'learndash' )
				),
				LearnDash_Custom_Label::get_label( 'quiz' ), LearnDash_Custom_Label::get_label( 'questions' )
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			$_INITIALIZE = false;
			if ( false === $this->setting_option_values ) {
				$_INITIALIZE = true;
				$this->setting_option_values = array(
					'proquiz_question_category' => 'yes',
					'ld_question_category' => 'no',
					'ld_question_tag' => 'no',
					'wp_post_category' => 'no',
					'wp_post_tag' => 'no',
				);

				// If this is a new install we want to turn off WP Post Category/Tag.
				require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/admin/class-learndash-admin-data-upgrades.php' );
				$this->ld_admin_data_upgrades = Learndash_Admin_Data_Upgrades::get_instance();

				$ld_prior_version = $this->ld_admin_data_upgrades->get_data_settings( 'prior_version' );
				if ( $ld_prior_version == 'new' ) {
					$this->setting_option_values['wp_post_category'] = 'no';
					$this->setting_option_values['wp_post_tag'] = 'no';
				}
			} 

			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values,
				array(
					'proquiz_question_category' => 'yes',
					'ld_question_category' => 'no',
					'ld_question_tag' => 'no',
					'wp_post_category' => 'no',
					'wp_post_tag' => 'no',
				)
			);
		}

		/**
		 * Initialize the metabox settings fields.
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				/*
				'ld_question_category' => array(
					'name' => 'ld_question_category',
					'type' => 'checkbox',
					'label' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'LearnDash %s Categories', 'placeholder: Question', 'learndash' ), 
						LearnDash_Custom_Label::get_label( 'question' )
					),
					'help_text' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'Enable the builtin LearnDash %s Categories', 'placeholder: Question', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'question' )
					),
					'value' => $this->setting_option_values['ld_question_category'],
					'options' => array(
						'yes' => esc_html__( 'Yes', 'learndash' ),
					),
				),
				*/
				/*
				'ld_question_tag' => array(
					'name' => 'ld_question_tag',
					'type' => 'checkbox',
					'label' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'LearnDash %s Tags', 'placeholder: Question', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'question' )
					),
					'help_text' => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'Enable the builtin LearnDash %s Tags', 'placeholder: Question', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'question' )
					),
					'value' => $this->setting_option_values['ld_question_tag'],
					'options' => array(
						'yes' => esc_html__( 'Yes', 'learndash' ),
					),
				),
				*/
				/*
				'wp_post_category' => array(
					'name' => 'wp_post_category',
					'type' => 'checkbox',
					'label' => esc_html__( 'WordPress Post Categories', 'learndash' ),
					'help_text' => esc_html__( 'Enable the builtin WordPress Post Categories', 'learndash' ),
					'value' => $this->setting_option_values['wp_post_category'],
					'options' => array(
						'yes' => esc_html__( 'Yes', 'learndash' ),
					),
				),
				*/
				/*
				'wp_post_tag' => array(
					'name' => 'wp_post_tag',
					'type' => 'checkbox',
					'label' => esc_html__( 'WordPress Post Tags', 'learndash' ),
					'help_text' => esc_html__( 'Enable the builtin WordPress Post Tags', 'learndash' ),
					'value' => $this->setting_option_values['wp_post_tag'],
					'options' => array(
						'yes' => esc_html__( 'Yes', 'learndash' ),
					),
				),
				*/
				'proquiz_question_category' => array(
					'name' => 'proquiz_question_category',
					'type' => 'checkbox',
					'label' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( '%s Categories', 'placeholder: Question', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'question' )
					),
					'help_text' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'Enable %s Categories', 'placeholder: Question', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'question' )
					),
					'value' => $this->setting_option_values['proquiz_question_category'],
					'options' => array(
						'yes' => esc_html__( 'Yes', 'learndash' ),
					),
					'attrs' => array(
						'disabled' => 'disabled',
					),
				),
			);

			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Questions_Taxonomies::add_section_instance();
} );
