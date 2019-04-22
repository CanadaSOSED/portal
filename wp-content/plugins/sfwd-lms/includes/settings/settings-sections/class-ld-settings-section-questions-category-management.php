<?php
/**
 * LearnDash Settings Section Question Category Mangement.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_Questions_Category_Management' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_Questions_Category_Management extends LearnDash_Settings_Section {

		/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-question_page_questions-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'questions-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash_settings_questions_category_management';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'learndash_settings_questions_category_management';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'questions_category_management';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Question.
				esc_html_x( '%s Category Management', 'placeholder: Question', 'learndash' ),
				LearnDash_Custom_Label::get_label( 'question' )
			);

			parent::__construct();

			// Hook to handle the AJAX delete/update actions.
			add_action( 'wp_ajax_' . $this->setting_field_prefix, array( $this, 'ajax_action' ) );
		}

		/**
		 * Load the field settings values
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			$this->setting_option_values = array(
				'question_category' => array(
					'' => __( 'Select a category', 'learndash' ),
				),
			);

			if ( ( is_admin() ) && ( isset( $_GET['page'] ) ) && ( 'questions-options' === $_GET['page'] ) ) {
				$category_mapper = new WpProQuiz_Model_CategoryMapper();
				$question_categories = $category_mapper->fetchAll();
				if ( ( ! empty( $question_categories ) ) && ( is_array( $question_categories ) ) ) {
					foreach ( $question_categories as $question_category ) {
						$category_name = $question_category->getCategoryName();
						$category_id = $question_category->getCategoryId();

						if ( ! empty( $category_name ) ) {
							$this->setting_option_values['question_category'][ $category_id ] = esc_html( $category_name );
						}
					}
				}
			}
		}

		/**
		 * Load the field settings fields
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array(
				'_question_category' => array(
					'name'      => 'question_category',
					'type'      => 'select-edit-delete',
					'label'     => sprintf(
						// translators: placeholder: Question.
						esc_html_x( '%s categories', 'placeholder: Question', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'question' )
					),
					'help_text' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'Manage %s categories. Select a category then update the title or delete.', 'placeholder: Question', 'learndash' ), LearnDash_Custom_Label::get_label( 'question' )
					),
					'value'     => '',
					'options'   => $this->setting_option_values['question_category'],
					'buttons'	=> array(
						'delete' => esc_html__( 'Delete', 'learndash' ),
						'update' => esc_html__( 'Update', 'learndash' ),
					),
				),
			);

			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * This function handles the AJAX actions from the browser.
		 *
		 * @since 2.5.9
		 */
		public function ajax_action() {
			$reply_data = array( 'status' => false );

			if ( current_user_can( 'wpProQuiz_edit_quiz' ) ) {
				if ( ( isset( $_POST['field_nonce'] ) ) && ( ! empty( $_POST['field_nonce'] ) ) 
				  && ( isset( $_POST['field_key'] ) ) && ( ! empty( $_POST['field_key'] ) ) && ( wp_verify_nonce( esc_attr( $_POST['field_nonce'] ), $_POST['field_key'] ) ) ) {

					if ( isset( $_POST['field_action'] ) ) {
						if ( 'update' === $_POST['field_action'] ) {
							if ( ( isset( $_POST['field_value'] ) ) && ( ! empty( $_POST['field_value'] ) )
							  && ( isset( $_POST['field_text'] ) ) && ( ! empty( $_POST['field_text'] ) ) ) {
								$category_id = intval( $_POST['field_value'] );
								$category_new_name = esc_attr( $_POST['field_text'] );

								$category_mapper = new WpProQuiz_Model_CategoryMapper();
								$category = $category_mapper->fetchById( $category_id );
								if ( ( $category ) && ( is_a( $category, 'WpProQuiz_Model_Category' ) ) ) {
									$category_current_name = $category->getCategoryName();
									if ( $category_current_name != $category_new_name ) {
										$update_ret = $category_mapper->updateCatgoryName( $category_id, $category_new_name );
										if ( $update_ret ) {
											$reply_data['status'] = true;
											$reply_data['message'] = '<span style="color: green" >' . __( 'Category updated.', 'learndash' ) . '</span>';
										}
									}
								}
							}
						} else if ( 'delete' === $_POST['field_action'] ) {
							if ( ( isset( $_POST['field_value'] ) ) && ( ! empty( $_POST['field_value'] ) ) ) {
								$category_id = intval( $_POST['field_value'] );

								$category_mapper = new WpProQuiz_Model_CategoryMapper();
								$category = $category_mapper->fetchById( $category_id );
								if ( ( $category ) && ( is_a( $category, 'WpProQuiz_Model_Category' ) ) ) {
									$update_ret = $category_mapper->delete( $category_id );
									if ( $update_ret ) {
										$reply_data['status'] = true;
										$reply_data['message'] = '<span style="color: green" >' . __( 'Category deleted.', 'learndash' ) . '</span>';
									}
								}
							}
						}
					}
				}
			}

			if ( ! empty( $reply_data ) ) {
				echo json_encode( $reply_data );
			}

			wp_die(); // This is required to terminate immediately and return a proper response.

		}

		// End of functions.
	}
}

add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Questions_Category_Management::add_section_instance();
} );
