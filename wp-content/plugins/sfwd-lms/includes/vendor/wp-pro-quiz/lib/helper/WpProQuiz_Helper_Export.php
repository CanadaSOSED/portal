<?php
class WpProQuiz_Helper_Export {
	
	const WPPROQUIZ_EXPORT_VERSION = 4;
	
	public function export( $ids ) {
		$export = array();

		$export['version'] = WPPROQUIZ_VERSION;
		$export['exportVersion'] = WpProQuiz_Helper_Export::WPPROQUIZ_EXPORT_VERSION;
		$export['ld_version'] = LEARNDASH_VERSION;
		$export['LEARNDASH_SETTINGS_DB_VERSION'] = LEARNDASH_SETTINGS_DB_VERSION;
		$export['date'] = time();

		$v = str_pad( WPPROQUIZ_VERSION, 5, '0', STR_PAD_LEFT );
		$v .= str_pad( WpProQuiz_Helper_Export::WPPROQUIZ_EXPORT_VERSION, 5, '0', STR_PAD_LEFT );
		$code = 'WPQ' . $v;

		$export['master'] = $this->getQuizMaster( $ids );

		foreach ($export['master'] as $master ) {
			$export['question'][ $master->getId() ] = $this->getQuestion( $master );
			$export['forms'][ $master->getId() ] = $this->getForms( $master->getId() );
			$export['postmeta'][ $master->getId() ] = $this->getPostMeta( $master );
		}

		return $code.base64_encode( serialize( $export ) );
	}

	private function getQuizMaster( $ids = array() ) {
		$r = array();
		if ( ! empty( $ids ) ) {
			$m = new WpProQuiz_Model_QuizMapper();
			foreach ( $ids as $quiz_post_id ) {
				$quiz_post_id = absint( $quiz_post_id );
				if ( ! empty( $quiz_post_id ) ) {
					$quiz_pro_id = learndash_get_setting( $quiz_post_id, 'quiz_pro' );
					if ( ! empty( $quiz_pro_id ) ) {
						$master = $m->fetch( $quiz_pro_id );
						if ( ( $master ) && ( is_a( $master, 'WpProQuiz_Model_Quiz' ) ) && ( $master->getId() > 0 ) ) {
							$master->setPostId( $quiz_post_id );
							$r[] = $master;
						}
					}
				}
			}
		}

		return $r;
	}

	public function getQuestion( $quiz_pro ) {
		if ( ( ! empty( $quiz_pro ) ) && ( is_a( $quiz_pro, 'WpProQuiz_Model_Quiz' ) ) ) {
			$m = new WpProQuiz_Model_QuestionMapper();
			return $m->fetchAll( $quiz_pro );
		}
	}
	
	public function getPostMeta( $quiz_pro ) {
		if ( ( ! empty( $quiz_pro ) ) && ( is_a( $quiz_pro, 'WpProQuiz_Model_Quiz' ) ) ) {
			$quiz_post_id = $quiz_pro->getPostId();
			if ( ! empty( $quiz_post_id ) ) {
				$quiz_post_meta = learndash_get_setting( $quiz_post_id );
				if ( ! is_array( $quiz_post_meta ) ) {
					$quiz_post_meta = array();
				}
				$quiz_post_meta['_viewProfileStatistics'] = get_post_meta( $quiz_post_id, '_viewProfileStatistics', true );

				return $quiz_post_meta;
			}
		}
	}

	private function getForms($quizId) {
		$formMapper = new WpProQuiz_Model_FormMapper();

		return $formMapper->fetch($quizId);
	}
}