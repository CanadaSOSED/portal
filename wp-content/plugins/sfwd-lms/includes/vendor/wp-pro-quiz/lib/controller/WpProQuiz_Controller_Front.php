<?php
class WpProQuiz_Controller_Front {

	/**
	 * @var WpProQuiz_Model_GlobalSettings
	 */
	private $_settings = null;

	public function __construct() {
		$this->loadSettings();

		add_action('wp_enqueue_scripts', array($this, 'loadDefaultScripts'));
		add_shortcode('LDAdvQuiz', array($this, 'shortcode'));
		add_shortcode('LDAdvQuiz_toplist', array($this, 'shortcodeToplist'));
	}

	public function loadDefaultScripts() {
		global $learndash_assets_loaded;
		
		wp_enqueue_script('jquery');

		//wp_enqueue_style(
		//	'wpProQuiz_front_style',
		//	plugins_url('css/wpProQuiz_front' . ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', WPPROQUIZ_FILE),
		//	array(),
		//	LEARNDASH_SCRIPT_VERSION_TOKEN
		//);
		//$learndash_assets_loaded['styles']['wpProQuiz_front_style'] = __FUNCTION__;
		
		$filepath = SFWD_LMS::get_template( 'learndash_quiz_front.css', null, null, true );
		if ( !empty( $filepath ) ) {
			wp_enqueue_style( 'learndash_quiz_front_css', learndash_template_url_from_path( $filepath ), array(), LEARNDASH_SCRIPT_VERSION_TOKEN );
			$learndash_assets_loaded['styles']['learndash_quiz_front_css'] = __FUNCTION__;
		} 

		if($this->_settings->isJsLoadInHead()) {
			$this->loadJsScripts(false, true, true);
		}
	}

	private function loadJsScripts($footer = true, $quiz = true, $toplist = false) {
		global $learndash_assets_loaded;

		if ($quiz) {
			wp_enqueue_script(
				'wpProQuiz_front_javascript',
				plugins_url('js/wpProQuiz_front' . ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min' ) .'.js', WPPROQUIZ_FILE),
				array('jquery', 'jquery-ui-sortable'),
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				$footer
			);
			$learndash_assets_loaded['scripts']['wpProQuiz_front_javascript'] = __FUNCTION__;

			wp_localize_script('wpProQuiz_front_javascript', 'WpProQuizGlobal', array(
				'ajaxurl' => str_replace(array("http:", "https:"), array("",""), admin_url('admin-ajax.php')),
				'loadData' => esc_html__('Loading', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
				'questionNotSolved' => esc_html__('You must answer this question.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
				'questionsNotSolved' => sprintf( esc_html_x('You must answer all questions before you can complete the %s.', 'You must answer all questions before you can complete the quiz.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ),
				'fieldsNotFilled' => esc_html__('All fields have to be filled.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN)
			));
			
			wp_enqueue_script(
				'jquery-cookie',
				plugins_url('js/jquery.cookie' . ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min' ) .'.js', WPPROQUIZ_FILE),
				array('jquery', 'jquery-ui-sortable'),
				'1.4.0',
				$footer
			);
			$learndash_assets_loaded['scripts']['jquery-cookie'] = __FUNCTION__;
		}

		if ($toplist) {
			wp_enqueue_script(
				'wpProQuiz_front_javascript_toplist',
				plugins_url('js/wpProQuiz_toplist'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && LEARNDASH_SCRIPT_DEBUG ) ? '' : '.min') .'.js', WPPROQUIZ_FILE),
				array('jquery', 'jquery-ui-sortable'),
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				$footer
			);
			$learndash_assets_loaded['scripts']['wpProQuiz_front_javascript_toplist'] = __FUNCTION__;

			if (!wp_script_is('wpProQuiz_front_javascript') ) {
				wp_localize_script(
					'wpProQuiz_front_javascript_toplist', 
					'WpProQuizGlobal', array(
						'ajaxurl' => str_replace(array("http:", "https:"), array("",""), admin_url('admin-ajax.php')),
						'loadData' => esc_html__('Loading', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
						'questionNotSolved' => esc_html__('You must answer this question.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN),
						'questionsNotSolved' => sprintf( esc_html_x('You must answer all questions before you can complete the %s.', 'You must answer all questions before you can complete the quiz.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' )),
						'fieldsNotFilled' => esc_html__('All fields have to be filled.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN)
					)
				);
			}
		}

		if(!$this->_settings->isTouchLibraryDeactivate()) {
			wp_enqueue_script(
				'jquery-ui-touch-punch',
				plugins_url('js/jquery.ui.touch-punch.min.js', WPPROQUIZ_FILE),
				array('jquery', 'jquery-ui-sortable'),
				'0.2.2',
				$footer
			);
			$learndash_assets_loaded['scripts']['jquery-ui-touch-punch'] = __FUNCTION__;
		}
	}

	public function shortcode($attr) {
		
		global $learndash_shortcode_used;
		$learndash_shortcode_used = true;
		
		$id = $attr[0];
		$content = '';

		if(!$this->_settings->isJsLoadInHead()) {
			$this->loadJsScripts();
		}

		if(is_numeric($id)) {
			ob_start();

			$this->handleShortCode($id);

			$content = ob_get_contents();

			ob_end_clean();
		}

		if($this->_settings->isAddRawShortcode()) {
			return '[raw]'.$content.'[/raw]';
		}

		return $content;
	}

	public function handleShortCode($id) {
		$view = new WpProQuiz_View_FrontQuiz();

		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		$formMapper = new WpProQuiz_Model_FormMapper();

		$quiz = $quizMapper->fetch($id);

		$maxQuestion = false;

		if($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

			$value = $quiz->getShowMaxQuestionValue();

			if($quiz->isShowMaxQuestionPercent()) {
				$count = $questionMapper->count($id);

				$value = ceil($count * $value / 100);
			}

			$question = $questionMapper->fetchAll($id, true, $value);
			$maxQuestion = true;

		} else {
			$question = $questionMapper->fetchAll($id);
		}

		if(empty($quiz) || empty($question)) {
			echo '';

			return;
		}

		$view->quiz = $quiz;
		$view->question = $question;
		$view->category = $categoryMapper->fetchByQuiz($quiz->getId());
		$view->forms = $formMapper->fetch($quiz->getId());

		if($maxQuestion)
			$view->showMaxQuestion();
		else
			$view->show();
	}

	public function shortcodeToplist($attr) {
		
		global $learndash_shortcode_used;
		$learndash_shortcode_used = true;
		
		$id = $attr[0];
		$content = '';

		if(!$this->_settings->isJsLoadInHead()) {
			$this->loadJsScripts(true, false, true);
		}

		if(is_numeric($id)) {
			ob_start();

			$this->handleShortCodeToplist($id, isset($attr['q']));

			$content = ob_get_contents();

			ob_end_clean();
		}

		if($this->_settings->isAddRawShortcode() && !isset($attr['q'])) {
			return '[raw]'.$content.'[/raw]';
		}

		return $content;
	}

	private function handleShortCodeToplist($quizId, $inQuiz = false) {
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$view = new WpProQuiz_View_FrontToplist();

		$quiz = $quizMapper->fetch($quizId);

		if($quiz->getId() <= 0 || !$quiz->isToplistActivated()) {
			echo '';
			return;
		}

		$view->quiz = $quiz;
		$view->points = $quizMapper->sumQuestionPoints($quizId);
		$view->inQuiz = $inQuiz;
		$view->show();
	}

	private function loadSettings() {
		$mapper = new WpProQuiz_Model_GlobalSettingsMapper();

		$this->_settings = $mapper->fetchAll();
	}

	public static function ajaxQuizLoadData($data, $func) {
		if (is_user_logged_in() )
			$user_id	= 	get_current_user_id();
		else
			$user_id	=	0;

		if ( isset( $data['quizId'] ) )
			$id 		= 	$data['quizId'];
		else
			$id			= 	0;
		
		if ( isset( $data['quiz'] ) )
			$quiz_post_id 		= 	$data['quiz'];
		else
			$quiz_post_id		= 	0;

		if ( ( !isset( $data['quiz_nonce'] ) ) || ( !wp_verify_nonce( $data['quiz_nonce'], 'sfwd-quiz-nonce-' . $quiz_post_id . '-'. $id .'-'. $user_id ) ) ) {
			//wp_send_json_error();
			die();
		}
		
		$view = new WpProQuiz_View_FrontQuiz();

		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		$formMapper = new WpProQuiz_Model_FormMapper();

		$quiz = $quizMapper->fetch($id);

		if($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

			$value = $quiz->getShowMaxQuestionValue();

			if($quiz->isShowMaxQuestionPercent()) {
				$count = $questionMapper->count($id);

				$value = ceil($count * $value / 100);
			}

			$question = $questionMapper->fetchAll($id, true, $value);

		} else {
			$question = $questionMapper->fetchAll($id);
		}

		if(empty($quiz) || empty($question)) {
			return null;
		}

		$view->quiz = $quiz;
		$view->question = $question;
		$view->category = $categoryMapper->fetchByQuiz($quiz->getId());
		$view->forms = $formMapper->fetch($quiz->getId());

		return json_encode($view->getQuizData());
	}
}