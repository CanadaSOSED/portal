<?php
class WpProQuiz_Helper_Import {
	
	private $_content = null;
	private $_error = false;
	
	public function setImportFileUpload($file) {
		if(!is_uploaded_file($file['tmp_name'])) {
			$this->setError(__('File was not uploaded', 'learndash'));
			return false;
		}
		
		$this->_content = file_get_contents($file['tmp_name']);
		
		return $this->checkCode();
	}
	
	public function setImportString($str) {
		$this->_content = $str;
		
		return $this->checkCode();
	}
	
	private function setError($str) {
		$this->_error = $str;
	}
	
	public function getError() {
		return $this->_error;
	}
	
	private function checkCode() {
		$code = substr($this->_content, 0, 13);
		
		$c = substr($code, 0, 3);
		$v1 = substr($code, 3, 5);
		$v2 = substr($code, 8, 5);
	
		if($c !== 'WPQ') {
			$this->setError(__('File have wrong format', 'learndash'));			
			return false;
		}
		
		if($v2 < 3) {
			$this->setError(__('File is not compatible with the current version', 'learndash'));
			return false;
		}
	
		return true;
	}
	
	public function getContent() {
		return $this->_content;
	}
	
	public function getImportData() {
		
		if($this->_content === null) {
			$this->setError(__('File cannot be processed', 'learndash'));
			return false;
		}
		
		$data = substr($this->_content, 13);
		
		$b = base64_decode($data);
		
		if($b === null) {
			$this->setError(__('File cannot be processed', 'learndash'));
			return false;
		}
		
		$check = $this->saveUnserialize($b, $o);

		if($check === false || !is_array($o)) {
			$this->setError(__('File cannot be processed', 'learndash'));
			return false;
		}
		
		unset($b);
		
		return $o;
	}
	
	public function saveImport($ids = false) {
		$data = $this->getImportData();
		
		if($data === false) {
			return false;
		}
		
		switch($data['exportVersion']) {
			case '3':
			case '4':
				return $this->importData($data, $ids, $data['exportVersion']);
				break;
		}
		
		return false;
	}
	
	private function importData($o, $ids = false, $version = '1') {
		global $wpdb;

		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$formMapper = new WpProQuiz_Model_FormMapper();

		foreach($o['master'] as $master) {
			if(get_class($master) !== 'WpProQuiz_Model_Quiz') {
				continue;
			}
			
			$oldId = $master->getId();
			
			if($ids !== false) {
				if(!in_array($oldId, $ids)) {
					continue;
				}
			}
			
			$master->setId(0);
			$master->setPostId(0);
			
			if($version == 3) {
				if($master->isQuestionOnSinglePage()) {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE);
				} else if($master->isCheckAnswer()) {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK);
				} else if($master->isBackButton()) {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_BACK_BUTTON);
				} else {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_NORMAL);
				}
			}

			$quizMapper->save( $master );

			$user_id = get_current_user_id();
			$quiz_post_id = wp_insert_post(
				array(
					'post_type'   => learndash_get_post_type_slug( 'quiz' ),
					'post_title'  => $master->getName(),
					'post_status' => 'publish',
					'post_author' => $user_id,
				)
			);
	
			if ( ! empty( $quiz_post_id ) ) {
				if ( isset( $o['postmeta'][ $oldId ] ) ) {
					if ( isset( $o['postmeta'][ $oldId ]['_viewProfileStatistics'] ) ) {
						update_post_meta( $quiz_post_id, '_viewProfileStatistics', $o['postmeta'][ $oldId ]['_viewProfileStatistics'] );
						unset( $o['postmeta'][ $oldId ]['_viewProfileStatistics'] );
					}
					update_post_meta( $quiz_post_id, '_' . learndash_get_post_type_slug( 'quiz' ), $o['postmeta'][ $oldId ] );
				}
				learndash_update_setting( $quiz_post_id, 'quiz_pro', $master->getId() );
				$master->setPostId( $quiz_post_id );
			}
			//$quiz_array = $master->get_object_as_array();
			//update_post_meta( $quiz_post_id, '_proquiz', $quiz_array );

			if ( isset( $o['forms'] ) && isset( $o['forms'][$oldId] ) ) {
				foreach ( $o['forms'][ $oldId ] as $form ) {
					/** @var WpProQuiz_Model_Form $form **/
					$form->setFormId( 0 );
					$form->setQuizId( $master->getId() );
				}

				$formMapper->update( $o['forms'][ $oldId ] );
			}

			$question_idx = 0;
			$quiz_questions = array();
			foreach ( $o['question'][ $oldId ] as $question ) {
				if ( get_class( $question ) !== 'WpProQuiz_Model_Question' ) {
					continue;
				}

				$question->setQuizId( $master->getId() );
				$question->setId( 0 );

				$pro_category_id = $question->getCategoryId();
				$pro_category_name = $question->getCategoryName();
				if ( ! empty( $pro_category_name ) ) {
					$categoryMapper = new WpProQuiz_Model_CategoryMapper();
					$category = $categoryMapper->fetchByName( $pro_category_name );
					$categoryId = $category->getCategoryId();
					if ( ( ! empty( $categoryId ) ) && ( absint( $pro_category_id ) !== absint( $categoryId ) ) ) {
						$question->setCategoryId( $category->getCategoryId() );
						$question->setCategoryName( $category->getCategoryName() );
					} else {
						$category->setCategoryName( $question->getCategoryName() );
						$category = $categoryMapper->save( $category );
						$question->setCategoryId( $category->getCategoryId() );
						$question->setCategoryName( $category->getCategoryName() );
					}
				}

				$question_idx++;
				$question->setSort( $question_idx );
				$question = $questionMapper->save( $question );

				$question_post_array = array(
					'post_type'    => learndash_get_post_type_slug( 'question' ),
					'post_title'   => $question->getTitle(),
					'post_content' => $question->getQuestion(),
					'post_status'  => 'publish',
					'post_author'  => $user_id,
					'menu_order'   => $question_idx,
				);
				$question_post_array = wp_slash( $question_post_array );
				$question_post_id = wp_insert_post( $question_post_array );
				if ( ! empty( $question_post_id ) ) {
					update_post_meta( $question_post_id, 'points', absint( $question->getPoints() ) );
					update_post_meta( $question_post_id, 'question_type', $question->getAnswerType() );
					update_post_meta( $question_post_id, 'question_pro_id', absint( $question->getId() ) );

					learndash_update_setting( $question_post_id, 'quiz', $quiz_post_id );
					add_post_meta( $question_post_id, 'ld_quiz_id', $quiz_post_id );

					$quiz_questions[ $question_post_id ] = absint( $question->getId() );
				}
			}

			if ( ! empty( $quiz_questions ) ) {
				update_post_meta( $quiz_post_id, 'ld_quiz_questions', $quiz_questions );
			}
		}

		return true;
	}
	
	private function saveUnserialize($str, &$into) {
		$into = @unserialize($str);
		
		return $into !== false || rtrim($str) === serialize(false);
	}
}