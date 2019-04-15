<?php
/**
 * LearnDash Settings Metabox for Quiz Advanced.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Metabox' ) ) && ( ! class_exists( 'LearnDash_Settings_Metabox_Quiz_Advanced' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_Metabox_Quiz_Advanced extends LearnDash_Settings_Metabox {

		private $pro_quiz_edit = null;
		private $pro_quiz_id = null;
		private $pro_quiz = null;
		/**
		 * Public constructor for class
		 */
		public function __construct() {
			//$this->settings_page_id = 'sfwd-quiz';

			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-quiz';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'learndash-quiz-advanced-settings';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( 'LearnDash %s Advanced Settings', 'placeholder: Quiz', 'learndash' ),
				LearnDash_Custom_Label::get_label( 'quiz' )
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {
			global $post;
			$quiz = $this->init_quiz_edit( $post );
			if ( ( $quiz ) && ( is_a( $quiz, 'WpProQuiz_Model_Quiz' ) ) ) {
				$this->pro_quiz_edit = $quiz;

				$this->setting_option_values['titleHidden'] = $this->pro_quiz_edit->isTitleHidden() ? true : '';
				$this->setting_option_values['btnRestartQuizHidden'] = $this->pro_quiz_edit->isBtnRestartQuizHidden() ? 'yes' : '';
				$this->setting_option_values['btnViewQuestionHidden'] = $this->pro_quiz_edit->isBtnViewQuestionHidden() ? 'yes' : '';
			}

			parent::load_settings_values();
		}

		/**
		 * Initialize the metabox settings fields.
		 */
		public function load_settings_fields() {
			$field_name_wrap = false;
			$this->setting_option_fields = array(
				'titleHidden' => array(
					'name' => 'titleHidden',
					'name_wrap' => $field_name_wrap,
					'type' => 'checkbox',
					'label' => sprintf(
						// translators: placeholder: quiz.
						esc_html_x( 'Hide %s title', 'placeholder: quiz', 'learndash' ),
						LearnDash_Custom_Label::label_to_lower( 'quiz' )
					),
					'help_text' => sprintf(
						// translators: placeholder: quiz.
						esc_html_x( 'The title serves as %s heading.', 'placeholder: quiz', 'learndash' ), 
						LearnDash_Custom_Label::label_to_lower( 'quiz' )
					),
					'value' => $this->pro_quiz_edit->isTitleHidden() ? true : '',
					'options' => array(
						'1' => esc_html__( 'Activate', 'learndash' ),
					),
				),
				'btnRestartQuizHidden' => array(
					'name' => 'btnRestartQuizHidden',
					'name_wrap' => $field_name_wrap,
					'type' => 'checkbox',
					'label' => sprintf(
						// translators: placeholder: quiz.
						esc_html_x( 'Hide "Restart %s" button', 'placeholder: quiz', 'learndash' ),
						LearnDash_Custom_Label::label_to_lower( 'quiz' )
					),
					'help_text' => sprintf(
						// translators: placeholder: quiz.
						esc_html_x( 'Hide the "Restart %s" button in the Frontend.', 'placeholder: quiz', 'learndash' ), 
						LearnDash_Custom_Label::label_to_lower( 'quiz' )
					),
					'value' => $this->pro_quiz_edit->isBtnRestartQuizHidden() ? true : '',
					'options' => array(
						'1' => esc_html__( 'Activate', 'learndash' ),
					),
				),
				'btnViewQuestionHidden' => array(
					'name'      => 'btnViewQuestionHidden',
					'name_wrap' => $field_name_wrap,
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Hide "View question" button', 'learndash' ),
					'help_text' => esc_html__( 'Hide the "View question" button in the Frontend.', 'learndash' ),
					'value'     => $this->pro_quiz_edit->isBtnViewQuestionHidden() ? true : '',
					'options'   => array(
						'1' => esc_html__( 'Activate', 'learndash' ),
					),
				),
				'questionRandom' => array(
					'name'      => 'questionRandom',
					'name_wrap' => $field_name_wrap,
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Display question randomly', 'learndash' ),
					'help_text' => esc_html__( 'Display question randomly in the Frontend.', 'learndash' ),
					'value'     => $this->pro_quiz_edit->isQuestionRandom() ? true : '',
					'options'   => array(
						'1' => esc_html__( 'Activate', 'learndash' ),
					),
				),
				'answerRandom' => array(
					'name'      => 'answerRandom',
					'name_wrap' => $field_name_wrap,
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Display answers randomly', 'learndash' ),
					'help_text' => esc_html__( 'Display answers randomly in the Frontend.', 'learndash' ),
					'value'     => $this->pro_quiz_edit->isAnswerRandom() ? true : '',
					'options'   => array(
						'1' => esc_html__( 'Activate', 'learndash' ),
					),
				),
				'answerRandom' => array(
					'name'      => 'answerRandom',
					'name_wrap' => $field_name_wrap,
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Sort questions by category', 'learndash' ),
					'help_text' => esc_html__( 'Also works in conjunction with the "display randomly question" option.', 'learndash' ),
					'value'     => $this->pro_quiz_edit->isSortCategories() ? true : '',
					'options'   => array(
						'1' => esc_html__( 'Activate', 'learndash' ),
					),
				),
				'timeLimit' => array(
					'name' => 'timeLimit',
					'type' => 'number',
					'label' => esc_html__( 'Time limit', 'learndash' ),
					'input_label' => esc_html__( 'Seconds', 'learndash' ),
					'help_text' => esc_html__( '0 = no limit', 'learndash' ),
					'value' => $this->pro_quiz_edit->getTimeLimit(),
					'class' => 'small-text',
					'attrs' => array(
						'step' => 1,
						'min' => 0,
					),
				),
				'timeLimit' => array(
					'name' => 'timeLimit',
					'type' => 'number',
					'label' => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'Use cookies for %s Answers', 'placeholder: Quiz', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'quiz' )
					),
					'input_label' => esc_html__( 'Seconds', 'learndash' ),
					'help_text' => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( "0 = Don't save answers. This option will save the user's answers into a browser cookie until the %s is submitted.", 'placeholders: Quiz', 'learndash' ),
						LearnDash_Custom_Label::get_label( 'quiz' )
					),
					'value' => $this->pro_quiz_edit->getTimeLimitCookie(),
					'class' => 'small-text',
					'attrs' => array(
						'step' => 1,
						'min' => 0,
					),
				),
				'statisticsOn' => array(
					'name'      => 'statisticsOn',
					'name_wrap' => $field_name_wrap,
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Statistics', 'learndash' ),
					'help_text' => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'Statistics about right or wrong answers. Statistics will be saved by completed %s, not after every question. The statistics is only visible over administration menu. (internal statistics)', 'placeholders: quiz', 'learndash' ),
						LearnDash_Custom_Label::label_to_lower( 'quiz' )
					),
					'value'     => $this->pro_quiz_edit->isStatisticsOn() ? true : '',
					'options'   => array(
						'1' => esc_html__( 'Activate', 'learndash' ),
					),
				),
				'statisticsIpLock' => array(
					'name' => 'timeLimitstatisticsIpLock',
					'type' => 'number',
					'label' => esc_html__( 'Statistics IP-lock', 'learndash' ),
					'input_label' => esc_html__( 'in minutes (recommended 1440 minutes = 1 day)', 'learndash' ),
					'help_text' => esc_html__( 'Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)', 'learndash' ),
					'value' => $this->pro_quiz_edit->getStatisticsIpLock(),
					'class' => 'small-text',
					'attrs' => array(
						'step' => 1,
						'min' => 0,
					),
				),

			);

			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_metabox_key );

			parent::load_settings_fields();
		}

		public function init_quiz_edit( $post ) {
			static $pro_quiz_edit = array();

			$pro_quiz_id = (int) learndash_get_setting( $post->ID, 'quiz_pro' );
			if ( ! empty( $pro_quiz_id ) ) {

				$quiz_mapper = new WpProQuiz_Model_QuizMapper();
				$pro_quiz_edit[ $pro_quiz_id ] = $quiz_mapper->fetch( $pro_quiz_id );
				return $pro_quiz_edit[ $pro_quiz_id ];


				$_get = array();
				$_post = array();

				$_post = array( '1' );
				$_get = array(
					'action' => 'getEdit',
					'quizId' => $pro_quiz_id,
					'post_id' => $post->ID,
				);

				if ( isset( $_GET['templateLoad'] ) && ( ! empty( $_GET['templateLoad'] ) ) ) {
					$_get['templateLoad'] = esc_attr( $_GET['templateLoad'] );
				}

				if ( isset( $_GET['templateLoadId'] ) && ( ! empty( $_GET['templateLoadId'] ) ) ) {
					$this->_get['templateLoadId'] = esc_attr( $_GET['templateLoadId'] );
				}

				$pro_quiz = new WpProQuiz_Controller_Quiz();
				$pro_quiz_edit[ $pro_quiz_id ] = $pro_quiz->route(
					$_get,
					$_post
				);

				if ( ( isset( $pro_quiz_edit[ $pro_quiz_id ] ) ) && ( is_a( $pro_quiz_edit[ $pro_quiz_id ], 'WpProQuiz_View_QuizEdit' ) ) ) {
					return $pro_quiz_edit[ $pro_quiz_id ];
				}
			}
		}
		// End of functions.
	}
}

add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Metabox_Quiz_Advanced::add_metabox_instance();
} );
