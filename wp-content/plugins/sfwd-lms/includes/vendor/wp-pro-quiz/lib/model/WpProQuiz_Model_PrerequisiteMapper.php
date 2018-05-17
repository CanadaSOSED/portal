<?php
class WpProQuiz_Model_PrerequisiteMapper extends WpProQuiz_Model_Mapper {
	public function delete($prerequisiteQuizId) {
		global $wpdb;
		return $wpdb->delete(
			$this->_tablePrerequisite, 
			array('prerequisite_quiz_id' => $prerequisiteQuizId), 
			array('%d')
		);
	}
	
	public function isQuizId($quizId) {
		return $this->_wpdb->get_var(
			$this->_wpdb->prepare("SELECT (quiz_id) FROM {$this->_tablePrerequisite} WHERE quiz_id = %d", 
			$quizId)
		);
	}
	
	public function fetchQuizIds($prerequisiteQuizId) {
		return $this->_wpdb->get_col(
			$this->_wpdb->prepare(
				"SELECT quiz_id FROM {$this->_tablePrerequisite} WHERE prerequisite_quiz_id = %d", 
			$prerequisiteQuizId)
		);
	}
	
	public function save($prerequisiteQuizId, $quiz_ids) {
		foreach($quiz_ids as $quiz_id) {
			$this->_wpdb->insert($this->_tablePrerequisite, array(
				'prerequisite_quiz_id' => $prerequisiteQuizId,
				'quiz_id' => $quiz_id
			), array('%d', '%d'));
		}
	}
	
	public function getNoPrerequisite($prerequisiteQuizId, $userId) {
		if ( ( defined( 'LEARNDASH_QUIZ_PREREQUISITE_ALT' ) ) && ( LEARNDASH_QUIZ_PREREQUISITE_ALT === true ) ) {
			$sql_str = $this->_wpdb->prepare( "SELECT p.quiz_id FROM {$this->_tablePrerequisite} AS p WHERE p.prerequisite_quiz_id = %d", $prerequisiteQuizId );
			$prereq_quiz_ids = $this->_wpdb->get_col( $sql_str );
			return $prereq_quiz_ids;
			
		} else {
			return $this->_wpdb->get_col(
				$this->_wpdb->prepare(
						"SELECT 
							p.quiz_id 
						FROM 
							{$this->_tablePrerequisite} AS p  
						LEFT JOIN 
							{$this->_tableStatisticRef} AS s 
								ON ( s.quiz_id = p.quiz_id AND s.user_id = %d ) 
						WHERE 
							s.user_id IS NULL AND p.prerequisite_quiz_id = %d 
						GROUP BY 
							p.quiz_id",
						$userId, $prerequisiteQuizId)
			);
		}
	}
}