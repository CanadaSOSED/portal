<?php
class WpProQuiz_Model_QuestionMapper extends WpProQuiz_Model_Mapper {
	private $_table;

	public function __construct() {
		parent::__construct();
		
		$this->_table = $this->_prefix."question";
	}
	
	public function delete($id) {
		$this->_wpdb->delete($this->_table, array('id' => $id), '%d');
	}
	
	public function deleteByQuizId($id) {
		$this->_wpdb->delete($this->_table, array('quiz_id' => $id), '%d');
	}
	
	public function getSort($questionId) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT sort FROM {$this->_tableQuestion} WHERE id = %d", $questionId));
	}
	
	public function updateSort($id, $sort) {
		$this->_wpdb->update(
				$this->_table,
				array(
						'sort' => $sort),
				array('id' => $id),
				array('%d'),
				array('%d'));
	}
	
	public function setOnlineOff($questionId) {
		return $this->_wpdb->update($this->_tableQuestion, array('online' => 0), array('id' => $questionId), null, array('%d'));
	}
	
	public function getQuizId($questionId) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT quiz_id FROM {$this->_tableQuestion} WHERE id = %d", $questionId));
	}
	
	public function getMaxSort($quizId) {
		return $this->_wpdb->get_var($this->_wpdb->prepare(
			"SELECT MAX(sort) AS max_sort FROM {$this->_tableQuestion} WHERE quiz_id = %d AND online = 1", $quizId));
	}
	
	public function save(WpProQuiz_Model_Question $question, $auto = false) {
		$sort = null;
		
		if($auto && $question->getId()) {
			$statisticMapper = new WpProQuiz_Model_StatisticMapper();
			
			if($statisticMapper->isStatisticByQuestionId($question->getId())) {
				$this->setOnlineOff($question->getId());
				$question->setQuizId($this->getQuizId($question->getId()));
				$question->setId(0);
				$sort = $question->getSort();
			}
		}
		
		if($question->getId() != 0) {
			$this->_wpdb->update(
					$this->_table, 
					array(
						'title' => $question->getTitle(),
						'points' => $question->getPoints(),
						'question' => $question->getQuestion(),
						'correct_msg' => $question->getCorrectMsg(),
						'incorrect_msg' => $question->getIncorrectMsg(),
						'correct_same_text' => (int)$question->isCorrectSameText(),
						'tip_enabled' => (int)$question->isTipEnabled(),
						'tip_msg' => $question->getTipMsg(),
						'answer_type' => $question->getAnswerType(),
						'show_points_in_box' => (int)$question->isShowPointsInBox(),
						'answer_points_activated' => (int)$question->isAnswerPointsActivated(),
						'answer_data' => $question->getAnswerData(true),
						'category_id' => $question->getCategoryId(),
						'answer_points_diff_modus_activated' => (int)$question->isAnswerPointsDiffModusActivated(),
						'disable_correct' => (int)$question->isDisableCorrect(),
						'matrix_sort_answer_criteria_width' => $question->getMatrixSortAnswerCriteriaWidth()
					),
					array('id' => $question->getId()),
					array('%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d'),
					array('%d'));
		} else {
			$this->_wpdb->insert($this->_table, array(
					'quiz_id' => $question->getQuizId(),
					'online' => 1,
					'sort' => $sort !== null ? $sort : ($this->getMaxSort($question->getQuizId()) + 1),
					'title' => $question->getTitle(),
					'points' => $question->getPoints(),
					'question' => $question->getQuestion(),
					'correct_msg' => $question->getCorrectMsg(),
					'incorrect_msg' => $question->getIncorrectMsg(),
					'correct_same_text' => (int)$question->isCorrectSameText(),
					'tip_enabled' => (int)$question->isTipEnabled(),
					'tip_msg' => $question->getTipMsg(),
					'answer_type' => $question->getAnswerType(),
					'show_points_in_box' => (int)$question->isShowPointsInBox(),
					'answer_points_activated' => (int)$question->isAnswerPointsActivated(),
					'answer_data' => $question->getAnswerData(true),
					'category_id' => $question->getCategoryId(),
					'answer_points_diff_modus_activated' => (int)$question->isAnswerPointsDiffModusActivated(),
					'disable_correct' => (int)$question->isDisableCorrect(),
					'matrix_sort_answer_criteria_width' => $question->getMatrixSortAnswerCriteriaWidth()
				),
				array('%d', '%d', '%d', '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d')
			);
			
			$question->setId($this->_wpdb->insert_id);
		}
		
		return $question;
	}
	
	public function fetch($id) {
		
		$row = $this->_wpdb->get_row(
			$this->_wpdb->prepare(
				"SELECT
					*
				FROM
					". $this->_table. "
				WHERE
					id = %d AND online = 1",
				$id),
			ARRAY_A
		);
		
		$model = new WpProQuiz_Model_Question($row);
	
		return $model;
	}
	
	public function fetchById($id, $online = 1 ) {
		
		$ids = array_map('intval', (array)$id);
		$a = array();
		
		if(empty($ids))
			return null;
		
		$sql_str = 	"SELECT * FROM ". $this->_table. " WHERE id IN(". implode(', ', $ids) .") ";
		
		if ( ( $online === 1 ) || ( $online === 1 ) ) {
			$sql_str .= " AND online = ". $online;
		}
		
		$results = $this->_wpdb->get_results(
				$sql_str,
				ARRAY_A
		);
		
		foreach ($results as $row) {
			$a[] = new WpProQuiz_Model_Question($row);
			
		}
		
		return is_array($id) ? $a : (isset($a[0]) ? $a[0] : null);
	}
	
	public function fetchAll($quizId, $rand = false, $max = 0) {
		
		if($rand) {
			$orderBy = 'ORDER BY RAND()';
		} else {
			$orderBy = 'ORDER BY sort ASC';
		}
		
		$limit = '';
		
		if($max > 0) {
			$limit = 'LIMIT 0, '.((int)$max);
		}
		
		$a = array();
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare(
							'SELECT 
								q.*,
								c.category_name 
							FROM 
								'. $this->_table.' AS q
								LEFT JOIN '.$this->_tableCategory.' AS c
									ON c.category_id = q.category_id
							WHERE
								quiz_id = %d AND q.online = 1
							'.$orderBy.' 
							'.$limit
						, $quizId),
				ARRAY_A);
		
		foreach($results as $row) {
			$model = new WpProQuiz_Model_Question($row);
			
			$a[] = $model;
		}
		
		return $a;
	}
	
	public function fetchAllList($quizId, $list) {
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare(
						'SELECT
								'.implode(', ', (array)$list).'
							FROM
								'. $this->_tableQuestion.'
							WHERE
								quiz_id = %d AND online = 1'
						, $quizId),
				ARRAY_A);
		
		return $results;
	}
	
	public function count($quizId) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE quiz_id = %d AND online = 1", $quizId));
	}
	
	public function exists($id) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE id = %d AND online = 1", $id));
	}
	
	public function existsAndWritable($id) {
		return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE id = %d AND online = 1", $id));
	}
	
	public function fetchCategoryPoints($quizId) {
		$results = $this->_wpdb->get_results(
				$this->_wpdb->prepare(
						'SELECT SUM( points ) AS sum_points , category_id
						FROM '.$this->_tableQuestion.'
						WHERE quiz_id = %d AND online = 1
						GROUP BY category_id', $quizId));
		
		$a = array();
		
		foreach($results as $result) {
			$a[$result['category_id']] = $result['sum_points'];
		}
		
		return $a;
	}
}