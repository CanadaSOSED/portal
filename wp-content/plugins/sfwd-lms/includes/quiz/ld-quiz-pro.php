<?php
/**
 * Extends WP Pro Quiz functionality to meet needs of LearnDash
 *
 * @since 2.1.0
 *
 * @package LearnDash\Quiz
 */



/**
 * Include WP Pro Quiz Plugin
 */

require_once dirname( dirname( __FILE__ ) ) . '/vendor/wp-pro-quiz/wp-pro-quiz.php';



/**
 * LearnDash QuizPro class
 */
class LD_QuizPro {

	public $debug = false;

	/**
	 * LD_QuizPro constructor
	 */
	function __construct() {

		//add_action( 'wp_head', array( $this, 'certificate_details' ) );
		add_action( 'wp_pro_quiz_completed_quiz', array( $this, 'wp_pro_quiz_completed' ) );
		add_action( 'save_post', array( $this, 'edit_process' ), 2000 );
		add_action( 'plugins_loaded', array( $this, 'quiz_edit_redirect' ), 1 );

		add_filter( 'ldadvquiz_the_content', 'wptexturize' );
		add_filter( 'ldadvquiz_the_content', 'convert_smilies' );
		add_filter( 'ldadvquiz_the_content', 'convert_chars' );
		add_filter( 'ldadvquiz_the_content', 'wpautop' );
		add_filter( 'ldadvquiz_the_content', 'shortcode_unautop' );
		add_filter( 'ldadvquiz_the_content', 'prepend_attachment' );

		add_filter( 'learndash_quiz_content', array( $this, 'learndash_quiz_content'), 1, 2 );

		//add_action("the_content", array( $this, 'certificate_link' ));
		if ( ! empty( $_GET['ld_fix_permissions'] ) ) {
			$role = get_role( 'administrator' );
			if ( ( $role ) && ( $role instanceof WP_Role ) ) {
			
				$role->add_cap( 'wpProQuiz_show' );
				$role->add_cap( 'wpProQuiz_add_quiz' );
				$role->add_cap( 'wpProQuiz_edit_quiz' );
				$role->add_cap( 'wpProQuiz_delete_quiz' );
				$role->add_cap( 'wpProQuiz_show_statistics' );
				$role->add_cap( 'wpProQuiz_reset_statistics' );
				$role->add_cap( 'wpProQuiz_import' );
				$role->add_cap( 'wpProQuiz_export' );
				$role->add_cap( 'wpProQuiz_change_settings' );
				$role->add_cap( 'wpProQuiz_toplist_edit' );
				$role->add_cap( 'wpProQuiz_toplist_edit' );
			}
		}

		add_action( 'wp_ajax_ld_adv_quiz_pro_ajax', array( $this, 'ld_adv_quiz_pro_ajax' ) );
		add_action( 'wp_ajax_nopriv_ld_adv_quiz_pro_ajax', array( $this, 'ld_adv_quiz_pro_ajax' ) );

		add_action( 'learndash_quiz_completed', array( $this, 'set_quiz_status_meta' ), 1, 2 );
	}



	/**
	 * Submit quiz and echo JSON representation of the checked quiz answers
	 *
	 * @since 2.1.0
	 *
	 */
	function ld_adv_quiz_pro_ajax() {

		// First we unpack the $_POST['results'] string
		if ( ( isset( $_POST['data']['responses'] ) ) && ( !empty( $_POST['data']['responses'] ) ) && ( is_string( $_POST['data']['responses'] ) ) ) {
			$_POST['data']['responses'] = json_decode( stripslashes( $_POST['data']['responses'] ), true );
		}

		//error_log('in '. __FUNCTION__ );
		//error_log('_POST<pre>'. print_r($_POST, true) .'</pre>');

		$func = isset( $_POST['func'] ) ? $_POST['func'] : '';
		$data = isset( $_POST['data'] ) ? (array)$_POST['data'] : null;

		switch ( $func ) {
			case 'checkAnswers':
				echo $this->checkAnswers( $data );
				break;
		}

		exit;
	}

	/**
	 * Check answers for submitted quiz
	 *
	 * @since 2.1.0
	 *
	 * @param  array $data Quiz information and answers to be checked
	 * @return string  JSON representation of checked answers
	 */
	function checkAnswers( $data ) {
		
		//error_log('in '. __FUNCTION__ );
		//error_log('_POST<pre>'. print_r($_POST, true) .'</pre>');
		
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
		
		$view       = new WpProQuiz_View_FrontQuiz();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$quiz       = $quizMapper->fetch( $id );
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		$formMapper     = new WpProQuiz_Model_FormMapper();

		$questionModels = $questionMapper->fetchAll( $id );

		$view->quiz     = $quiz;
		$view->question = $questionModels;
		$view->category = $categoryMapper->fetchByQuiz( $quiz->getId() );

		$question_count = count( $questionModels );
		ob_start();
		$quizData = $view->showQuizBox( $question_count );
		ob_get_clean();

		$json    = $quizData['json'];
		$results = array();
		$question_index = 0;

		foreach ( $data['responses'] as $question_id => $info ) {
			if (isset( $questionModel ) ) unset( $questionModel );
			
			foreach ( $questionModels as $questionModel ) {
				if ( $questionModel->getId() == intval( $question_id ) ) {
			
					$userResponse = $info['response'];	

					if ( ( is_array( $userResponse ) ) && ( !empty( $userResponse ) ) ) {
						foreach ( $userResponse as $key => $value ) {
							if ( ( $value != 0) && ($value != 1) ) {
						
								if ( $value == "false" ) {
									$userResponse[ $key ] = false;
								} else if ( $value == "true" ) {
									$userResponse[ $key ] = true;
								}
							}
						}
					}

					$questionData           = $json[ $question_id ];
					$correct                = false;
					$points                 = 0;
					$statisticsData         = new stdClass();
					$extra                  = array();
					$extra['type']          = $questionData['type'];
					$questionData['points'] = isset( $questionData['points'] ) ? $questionData['points'] : $questionData['globalPoints'];

					$question_index++;
					$answer_pointed_activated = $questionModel->isAnswerPointsActivated();

					switch ( $questionData['type'] ) {
						case 'free_answer':
							//$correct = (strtolower( $userResponse ) == strtolower( $questionData['correct'][0] ));
					
							$correct = false;
							
							foreach($questionData['correct'] as $questionData_correct) {
								if (  stripslashes( strtolower( trim( $userResponse ) ) ) == stripslashes( strtolower( trim( $questionData_correct ) ) ) ) {
									$correct = true;
									break;
								}
							}

							$points  = ( $correct) ? $questionData['points'] : 0;

							$points = apply_filters( 'learndash_ques_free_answer_pts', $points, $questionData, $userResponse );
							$correct = apply_filters( 'learndash_ques_free_answer_correct', $correct, $questionData, $userResponse );
								
							$extra['r'] = $userResponse;
							if ( ! $quiz->isDisabledAnswerMark() && empty( $questionData['disCorrect'] ) ) {
								$extra['c'] = $questionData['correct'];
							}

							break;

						case 'multiple':
							$correct = true;
							$r       = array();

							foreach ( $questionData['correct'] as $answerIndex => $correctAnswer ) {

								//$checked = $questionData['correct'][ $userResponse[ $answerIndex ] ];


								if ( $answer_pointed_activated ){
									
									/**
									 * Points are calculated per answer, add up all the points the user marked correctly
									 */
									
									/*
									if ( ! empty( $correctAnswer ) && ! empty( $userResponse[ $answerIndex ] ) ) {
										$r[ $answerIndex ] = true;
										$correct = true;
										$points += $questionData['points'][ $answerIndex ];
									} else {
										$r[ $answerIndex ] = false;
										$correct = false;
									}

									//if ( $userResponse != $questionData['correct'] ) {
									// $correct = false;
									//}

									$points = apply_filters( 'learndash_ques_multiple_answer_pts_each', $points, $questionData, $answerIndex, $correctAnswer, $userResponse );
									$correct = apply_filters( 'learndash_ques_multiple_answer_correct_each', $correct, $questionData, $answerIndex, $correctAnswer, $userResponse );
									*/
									
									
									
									if ( ( isset( $userResponse[ $answerIndex ] ) ) && ( $userResponse[ $answerIndex ] == $correctAnswer ) ) { 
										$r[ $answerIndex ] = $userResponse[ $answerIndex ];
										$correct_this_item = true;
										
										if ( $userResponse[ $answerIndex ] == true )
											$points += $questionData['points'][ $answerIndex ];
										
									} else {
										$r[ $answerIndex ] = false;
										$correct_this_item = false;
									}

									if ( has_filter( 'learndash_ques_multiple_answer_pts_each' ) ) {
										$points = apply_filters( 'learndash_ques_multiple_answer_pts_each', $points, $questionData, $answerIndex, $correctAnswer, $userResponse );
									} else {
										/**
										 * Added logic to subtract points on selected incorrect answers. 
										 *
										 * @since 2.5.7
										 */
										if ( $questionData['correct'][ $answerIndex ] == 0 ) {
											if ( $correct_this_item == false ) {
												if ( intval( $questionData['points'][ $answerIndex ] ) > 0 ) {
													$points -= intval( $questionData['points'][ $answerIndex ] );
												} //else {
												//	$points -= 1;
												//}
											}

											end( $questionData['correct'] );
											$last_key = key( $questionData['correct'] );

											// If we are at the last index and the points is less than zero we keep it from being negative.
											if ( ( $last_key == $answerIndex ) && ( $points < 0 ) ) {
												$points = 0;
											}
										}
									}
									
									$correct_this_item = apply_filters( 'learndash_ques_multiple_answer_correct_each', $correct_this_item, $questionData, $answerIndex, $correctAnswer, $userResponse );
									if ( ( $correct_this_item != true ) && ( $correct == true ) )
										$correct = false;
									 

								} else {

									/**
									 * Points are allocated for the entire question if the user selects all the correct answers and none of
									 * the incorrect answers
									 *
									 * if the user selects an answer that is marked as correct, mark the question true and let the
									 * foreach loop check the next answer
									 *
									 * if they select an incorrect answer, or fail to select a correct answer, mark it false and break
									 * the foreach
									 *
									 * we don't want to break the foreach if the user did not select an incorrect answer
									 */
									if ( ! empty( $correctAnswer ) && ! empty( $userResponse[ $answerIndex ] ) ) {
										$correct = true;
										$r[ $answerIndex ] = true;
										$points = $questionData['points'];
									} elseif ( empty( $correctAnswer ) && ! empty( $userResponse[ $answerIndex ] ) ) {
										$correct = false;
										$r[ $answerIndex ] = false;
										$points = 0;
										break;
									} elseif ( ! empty( $correctAnswer ) && empty( $userResponse[ $answerIndex ] ) ) {
										$correct = false;
										$r[ $answerIndex ] = false;
										$points = 0;
										break;
									}

									// See https://bitbucket.org/snippets/learndash/aKdpz for examples of this filter. 
									$points = apply_filters( 'learndash_ques_multiple_answer_pts_whole', $points, $questionData, $answerIndex, $correctAnswer, $userResponse );
									$correct = apply_filters( 'learndash_ques_multiple_answer_correct_whole', $correct, $questionData, $answerIndex, $correctAnswer, $userResponse );

								}


							}

							$extra['r'] = $userResponse;

							if ( ! $quiz->isDisabledAnswerMark() ) {
								$extra['c'] = $questionData['correct'];
							}

							break;

						case 'single':
							foreach ( $questionData['correct'] as $answerIndex => $correctAnswer ) {
								if ($userResponse[ $answerIndex ] == true) {

									if ( ( ( isset( $questionData['diffMode'] ) ) && ( ! empty( $questionData['diffMode'] ) ) ) || ( !empty( $correctAnswer ) ) ) {
										//DiffMode or Correct
										if ( is_array( $questionData['points'] ) ) {
											$points = $questionData['points'][ $answerIndex ];
										} else {
											$points = $questionData['points'];
										}
									}

									if ( ! empty( $correctAnswer) || ! empty( $questionData['disCorrect'] ) ) {
										//Correct
										$correct = true;
									}
							
									// See https://bitbucket.org/snippets/learndash/aKdpz for examples of this filter. 
									$points = apply_filters( 'learndash_ques_single_answer_pts', $points, $questionData, $answerIndex, $correctAnswer, $userResponse );
									$correct = apply_filters( 'learndash_ques_single_answer_correct', $correct, $questionData, $answerIndex, $correctAnswer, $userResponse );
							
								}
							}

							$extra['r'] = $userResponse;

							if ( ! $quiz->isDisabledAnswerMark() && empty( $questionData['disCorrect'] ) ) {
								$extra['c'] = $questionData['correct'];
							}
							break;

						case 'sort_answer':
						case 'matrix_sort_answer':
							$correct                 = true;
							$questionData['correct'] = LD_QuizPro::datapos_array( $question_id, count( $questionData['correct'] ) );

							foreach ( $questionData['correct'] as $answerIndex => $answer ) {
								if ( ! isset( $userResponse[ $answerIndex ] ) || $userResponse[ $answerIndex ] != $answer ) {
									$correct = false;
								} else {
									if ( is_array( $questionData['points'] ) ) {
										$points += $questionData['points'][ $answerIndex ];
									}
								}

								$statisticsData->{$answerIndex} = @$userResponse[ $answerIndex ];
							}

							if ( $correct ) {
								if ( ! is_array( $questionData['points'] ) ) {
									$points = $questionData['points'];
								}
							} else {
								$statisticsData = new stdClass();
							}

							$extra['r'] = $userResponse;

							if ( ! $quiz->isDisabledAnswerMark() && empty( $questionData['disCorrect'] ) ) {
								$extra['c'] = $questionData['correct'];
							} else {
								$statisticsData = new stdClass();
							}

							break;

						case 'cloze_answer':
							$answerData = array();
							
							foreach ( $questionData['correct'] as $answerIndex => $correctArray ) {
								$answerData[$answerIndex] = false;
																
								if ( ! isset( $userResponse[ $answerIndex ] ) )
									$answerData[$answerIndex] = false;
							
								$userResponse[ $answerIndex ] =  stripslashes( trim( $userResponse[ $answerIndex ] ) );
								if ( apply_filters('learndash_quiz_question_cloze_answers_to_lowercase', true ) ) {
									$user_answer_formatted = strtolower( $userResponse[ $answerIndex ] );
								} else {
									$user_answer_formatted = $userResponse[ $answerIndex ];
								}
							
								$answerData[$answerIndex] = in_array( $user_answer_formatted, $correctArray );
								$answerData[$answerIndex] =	apply_filters( 'learndash_quiz_check_answer', $answerData[$answerIndex], $questionData['type'], $userResponse[ $answerIndex ], $correctArray, $answerIndex, $questionModel );
								$statisticsData->{$answerIndex} = $answerData[$answerIndex];
								
								if ( $answerData[$answerIndex] === true ) {
									if ( ( $questionModel->isAnswerPointsActivated() ) && ( is_array( $questionData['points'] ) ) ) {
										$points += $questionData['points'][ $answerIndex ];
									} else {
										$points = $questionData['points'];
									}
								}
							}
							
							// If we have one wrong answer
							if ( in_array( false, $answerData ) === true ) {
								$correct = false;
								
								// If we are NOT using individual points and there is at least one wrong answer
								// then we clear the points. 
								if ( !$questionModel->isAnswerPointsActivated() ) {
									$points = 0;
								}
								
							} else {
								// If all the fields are correct then the points stand and we set the correct to true
								$correct = true;
							}

							$extra['r'] = $userResponse;

							if ( ! $quiz->isDisabledAnswerMark() && empty( $questionData['disCorrect'] ) ) {
								$extra['c'] = $questionData['correct'];
							}
							break;

						case 'assessment_answer':
							$correct = true;
							$points  = intVal( $userResponse );
							$extra['r'] = $userResponse;
							
							break;

						case 'essay':

							$essay_data = $questionModel->getAnswerData();

							$essay_data = array_shift( $essay_data );

							switch ( $essay_data->getGradingProgression() ) {
								case 'not-graded-none':
									$points = 0;
									$correct = false;
									$extra['graded_status'] = 'not_graded';
									break;

								case 'not-graded-full':
									$points = $essay_data->getPoints();
									$correct = false;
									$extra['graded_status'] = 'not_graded';
									break;

								case 'graded-full' :
									$points = $essay_data->getPoints();
									$correct = true;
									$extra['graded_status'] = 'graded';
									break;

								default:
									$points = 0;
									$correct = false;
									$extra['graded_status'] = 'not_graded';
							}

							$essay_id = learndash_add_new_essay_response( $userResponse, $questionModel, $quiz, $data );
							$extra['graded_id'] = $essay_id;
							break;

						default:
							break;
					}

					if ( ! $quiz->isHideAnswerMessageBox() ) {
						foreach ( $questionModels as $key => $value ) {
							if ( $value->getId() == $question_id ) {
								if ( $correct || $value->isCorrectSameText() ) {
									//$extra['AnswerMessage'] = do_shortcode( apply_filters( 'comment_text', $value->getCorrectMsg() ) );
									$extra['AnswerMessage'] = do_shortcode( apply_filters( 'the_content', $value->getCorrectMsg() ) );
								} else {
									//$extra['AnswerMessage'] = do_shortcode( apply_filters( 'comment_text', $value->getIncorrectMsg() ) );
									$extra['AnswerMessage'] = do_shortcode( apply_filters( 'the_content', $value->getIncorrectMsg() ) );
								}

								break;
							}
						}
					}

					$extra['possiblePoints'] = $questionModel->getPoints();
			
					$results[ $question_id ] = array(
						'c' => $correct,
						'p' => $points,
						's' => $statisticsData,
						'e' => $extra
					);
					
					break;
				}
			}
		}

		do_action( 'ldadvquiz_answered', $results, $quiz, $questionModels);
		
		$total_points = 0;
		
		foreach( $results as $r_idx => $result ) {

			if ( ( isset( $result['e'] ) ) && ( !empty( $result['e'] ) ) ) {
				if ( ( isset( $result['e']['type'] ) ) && ( !empty( $result['e']['type'] ) ) ) {
					$response_str = '';
					
					switch( $result['e']['type'] ) {
						case 'essay':
							if ( ( isset( $result['e']['graded_id'] ) ) && ( !empty( $result['e']['graded_id'] ) ) ) {
								$response_str = maybe_serialize( array( 'graded_id' => $result['e']['graded_id'] ) );
							}
							break;

						case 'free_answer':
							if ( ( isset( $result['e']['r'] ) ) && ( !empty( $result['e']['r'] ) ) ) {
								
								$response_str = maybe_serialize( array( $result['e']['r'] ) );
							}
							break;
						
						case 'assessment_answer':
							if ( isset( $result['p'] ) ) {
								$response_str = maybe_serialize( array( (string)$result['p'] ) );
							}
							break;
							
						case 'multiple':
						case 'single':
						default:
							if ( ( isset( $result['e']['r'] ) ) && ( !empty( $result['e']['r'] ) ) ) {
								$result_array = array();
								foreach( $result['e']['r'] as $ri_idx => $ri ) {
									if ( $ri === true )
										$ri = 1;
									else if ( $ri === false )
										$ri = 0;
									
									$result_array[$ri_idx] = $ri;
								}
								$response_str = maybe_serialize( $result_array );
							}
							break;
						
							break;
					}
					
					if ( !empty( $response_str ) ) {
						$answers_nonce = wp_create_nonce( 'ld_quiz_anonce'. $user_id .'_'. $id .'_'. $quiz_post_id .'_'. $r_idx .'_'. $response_str );
						$results[$r_idx]['a_nonce'] = $answers_nonce;
					}
				}
			}
			
			$points_array = array(
				'points' => intval( $result['p'] ),
				'correct' => intval( $result['c'] ),
				'possiblePoints' => intval( $result['e']['possiblePoints'] )
			);
			if ( $points_array['correct'] === false ) $points_array['correct'] = 0;
			else if ( $points_array['correct'] === true ) $points_array['correct'] = 1;
			$points_str = maybe_serialize( $points_array );
			$points_nonce = wp_create_nonce( 'ld_quiz_pnonce'. $user_id .'_'. $id .'_'. $quiz_post_id .'_'. $r_idx .'_'. $points_str );
			$results[$r_idx]['p_nonce'] = $points_nonce;
		}
		
		return json_encode( $results );
	}

	/**
	 * Redirect from the Advanced Quiz edit or add link to the Quiz edit or add link
	 *
	 * @since 2.1.0
	 */
	function quiz_edit_redirect() {

		if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'ldAdvQuiz' && empty( $_GET['module'] ) && ! empty( $_GET['action'] ) && $_GET['action'] == 'addEdit' ) {

			if ( ! empty( $_GET['post_id'] ) ) {
				header( 'Location: ' . admin_url( 'post.php?action=edit&post=' . $_GET['post_id'] ) );
				exit;
			} else if ( ! empty( $_GET['quizId'] ) ) {
				$post_id = learndash_get_quiz_id_by_pro_quiz_id( $_GET['quizId'] );

				if ( ! empty( $post_id ) ) {
					header( 'Location: ' . admin_url( 'post.php?action=edit&post=' . $post_id ) );
				} else {
					header( 'Location: ' . admin_url( 'edit.php?post_type=sfwd-quiz' ) );
				}

				exit;
			}

			header( 'Location: ' . admin_url( 'post-new.php?post_type=sfwd-quiz' ) );
			exit;
		}
	}



	/**
	 * Echoes quiz content
	 *
	 * @since 2.1.0
	 *
	 * @param  int $pro_quiz_id
	 */
	static function showQuizContent( $pro_quiz_id ) {
		global $post;

		if ( empty( $post) || $post->post_type == 'sfwd-quiz' ) {
			return '';
		}

		echo LD_QuizPro::get_description( $pro_quiz_id );
	}



	/**
	 * Returns the HTML representation of the quiz description
	 *
	 * @since 2.1.0
	 *
	 * @param  int $pro_quiz_id
	 * @return string HTML representation of quiz description
	 */
	static function get_description( $pro_quiz_id ) {
		$post_id = learndash_get_quiz_id_by_pro_quiz_id( $pro_quiz_id );

		if ( empty( $post_id ) ) {
			return '';
		}

		$quiz = get_post( $post_id );

		if ( empty( $quiz->post_content) ) {
			return '';
		}

		/**
		 * Filter the description of the quiz
		 *
		 * @param  string $quiz->post_content
		 */
		$content = apply_filters( 'ldadvquiz_the_content', $quiz->post_content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		return "<div class='wpProQuiz_description'>" . $content . '</div>';
	}



	/**
	 * Outputs the debugging message to the error log file
	 *
	 * @since 2.1.0
	 *
	 * @param  string $msg Debugging message
	 */
	function debug( $msg ) {
		$original_log_errors = ini_get( 'log_errors' );
		$original_error_log  = ini_get( 'error_log' );
		ini_set( 'log_errors', true );
		ini_set( 'error_log', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'debug.log' );

		global $processing_id;

		if ( empty( $processing_id ) ) {
			$processing_id = time();
		}

		if ( isset( $_GET['debug'] ) || ! empty( $this->debug ) ) {
			error_log( "[$processing_id] " . print_r( $msg, true ) );
		}
		//Comment This line to stop logging debug messages.

		ini_set( 'log_errors', $original_log_errors );
		ini_set( 'error_log', $original_error_log );
	}


	/**
	 * Does the list of questions for this quiz have a graded question in it
	 * Dataset used is not the quizdata saved to user meta, but follows the
	 * Question Model of WpProQuiz
	 *
	 * @since 2.1.0
	 *
	 * @param array $questions
	 *
	 * @return bool
	 */
	static function quiz_has_graded_question( $questions ) {
		$graded_question_types = array( 'essay' );

		foreach ( $questions as $question ) {
			if ( ! is_a( $question, 'WpProQuiz_Model_Question' )  ) {
				continue;
			}

			if ( in_array( $question->getAnswerType(), $graded_question_types ) ){
				// found one! halt foreach and return true;
				return true;
			}
		}

		// foreach completed without finding any, return false
		return false;
	}



	/**
	 * Checks a users submitted quiz attempt to see if that quiz
	 * has graded questions and if all of them have been graded
	 *
	 */
	static function quiz_attempt_has_ungraded_question( $quiz_attempt ) {
		if (isset( $quiz_attempt['graded'] ) ) {
			foreach( $quiz_attempt['graded'] as $graded ) {
				if ( 'not_graded' == $graded['status'] ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * This function runs when a quiz is started and is used to set the quiz start timestamp
	 *
	 * @since 2.3
	 */
	function set_quiz_status_meta( $quizdata, $user ) {
		
		if (empty( $quizdata ) ) return;
		if ( !( $user instanceof WP_User ) ) return;
		
		if ( isset( $quizdata['questions'] ) )
			unset( $quizdata['questions'] );

		if ( ( isset( $quizdata['quiz'] ) ) && ( $quizdata['quiz'] instanceof WP_Post ) ) {
			$quiz_post = $quizdata['quiz'];
			unset( $quizdata['quiz'] );
			$quizdata['quiz'] = intval($quiz_post->ID);
		}		

		$course_id = 0;
		
		if ( ( isset( $quizdata['course'] ) ) && ( $quizdata['course'] instanceof WP_Post ) ) {
			$course_post = $quizdata['course'];
			unset( $quizdata['course'] );
			$quizdata['course'] = intval($course_post->ID);
			$course_id = $quizdata['course'];
			$quizdata['steps_completed'] = learndash_course_get_completed_steps($user->ID, $quizdata['course']);

			// Update the Course if this quiz has.
			$course_args = array(
				'course_id'			=>	$quizdata['course'], 
				'user_id'			=>	$user->ID,
				'post_id'			=>	$quizdata['course'],
				'activity_type'		=>	'course',
				'activity_started'	=>	time(), 
				'activity_meta'			=>	array( 
												'steps_completed'	=>	$quizdata['steps_completed'],
												'steps_last_id'		=>	$quizdata['quiz']
											)
			);
			$course_activity = learndash_get_user_activity( $course_args );
			if ( !$course_activity ) {
				learndash_update_user_activity( $course_args );
			}
		}		
		
		if  ( ( isset( $quizdata['started'] ) ) && ( !empty( $quizdata['started'] ) ) && ( isset( $quizdata['completed'] ) ) && ( !empty( $quizdata['completed'] ) ) ) {
			
			if ( $quizdata['pass'] == true )
				$quizdata_pass = true;
			else	
				$quizdata_pass = false;
			
			learndash_update_user_activity(
				array(
					'course_id'				=>	$course_id,
					'user_id'				=>	$user->ID,
					'post_id'				=>	$quizdata['quiz'],
					'activity_type'			=>	'quiz',
					'activity_action'		=>	'insert',
					'activity_status'		=>	$quizdata_pass,
					'activity_started'		=>	$quizdata['started'],
					'activity_completed' 	=>	$quizdata['completed'],
					'activity_meta'			=>	$quizdata 
				)
			);
		}		
	}

	/**
	 * This function runs when a quiz is completed, and does the action 'wp_pro_quiz_completed_quiz'
	 *
	 * @since 2.1.0
	 */
	function wp_pro_quiz_completed( $statistic_ref_id = 0) {
		
		$this->debug( $_POST );
		$this->debug( $_SERVER );

		$quiz_id   = isset( $_POST['quizId'] ) ? intval( $_POST['quizId'] ) : null;
		$score     = isset( $_POST['results']['comp']['correctQuestions'] ) ? $_POST['results']['comp']['correctQuestions'] : null;
		$points    = isset( $_POST['results']['comp']['points'] ) ? $_POST['results']['comp']['points'] : null;
		$result    = isset( $_POST['results']['comp']['result'] ) ? $_POST['results']['comp']['result'] : null;
		$timespent = isset( $_POST['timespent'] ) ? $_POST['timespent'] : null;

		if ( is_null( $quiz_id ) || is_null( $points ) ) {
			return;
		}

		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$questions  = $questionMapper->fetchAll( $quiz_id );
		if ( is_array( $questions ) ) {
			$questions_count = count( $questions );
		}
		
		// check if these set of questions has questions that need to be graded
		$has_graded = LD_QuizPro::quiz_has_graded_question( $questions );

		// store the id's of the graded question to be saved in usermeta
		$graded = array();
		foreach ( $_POST['results'] as $question_id => $individual_result ) {
			if ( 'comp' == $question_id ) continue;
			
			if ( isset( $individual_result['graded_id'] ) && ! empty( $individual_result['graded_id'] ) ) {
				$graded[ $question_id ] = array(
						'post_id' => intval( $individual_result['graded_id'] ),
						'status' => esc_html( $individual_result['graded_status'] ),
						'points_awarded' => intval( $individual_result['points'] ),
				);
			}
		}

		$this->debug( $questions );

		if ( empty( $result) ) {
			$total_points = 0;

			//foreach ( $questions as $q ) {
			//	$q_points = $q->getPoints();
			//	error_log('q_points['. $q_points .']');
			//	$total_points += $q->getPoints();
			//}

			// Rewrote logic here to only count points for the questions shown to the user.
			// For example I might have a Quiz showing only 5 of 10 questions. In the above code
			// the points counted inlcude ALL 10 questions. Not correct. 
			// Insead we do the logic below and only process the 5 shown questions. 
			foreach ( $_POST['results'] as $question_id => $q_result ) {
				if ( 'comp' == $question_id ) continue;
				
				if ( ( isset( $q_result['possiblePoints'] ) ) && ( !empty( $q_result['possiblePoints'] ) ) ) {
					$total_points += intval( $q_result['possiblePoints'] );
				}
			}
		} else {
			$total_points = round( $points * 100 / $result );
		}

		$questions_shown_count = count( $_POST['results'] ) - 1;
		
		if (is_user_logged_in() )
			$user_id	= 	get_current_user_id();
		else
			$user_id	=	0;
		

		if ( ( isset( $_POST['quiz_nonce'] ) ) && ( isset( $_POST['quizId'] ) ) && ( isset( $_POST['quizId'] ) ) && ( !empty( $user_id ) ) ) {
			if ( !wp_verify_nonce( $_POST['quiz_nonce'], 'sfwd-quiz-nonce-' . intval( $_POST['quiz'] ) . '-'.  intval( $_POST['quizId'] ) .'-' . $user_id ) ) {
				return;
			}
		} else	if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
			return;
		}

		$usermeta = get_user_meta( $user_id, '_sfwd-quizzes', true );
		$usermeta = maybe_unserialize( $usermeta );

		if ( ! is_array( $usermeta ) ) {
			$usermeta = array();
		}

		$ld_quiz_id = @$_REQUEST['quiz']; //$this->get_ld_quiz_id( $quiz_id);

		if ( empty( $ld_quiz_id ) ) {
			return;
		}

		$courseid = ( ( isset( $_POST['course_id'] ) ) && ( intval( $_POST['course_id'] ) > 0 ) ) ? intval( $_POST['course_id'] ) : learndash_get_course_id( $ld_quiz_id );
		$lessonid = ( ( isset( $_POST['lesson_id'] ) ) && ( intval( $_POST['lesson_id'] ) > 0 ) ) ? intval( $_POST['lesson_id'] ) : 0;
		$topicid  = ( ( isset( $_POST['topic_id'] ) ) && ( intval( $_POST['topic_id'] ) > 0 ) ) ? intval( $_POST['topic_id'] ) : 0;

		$quiz = get_post_meta( $ld_quiz_id, '_sfwd-quiz', true );
		$passingpercentage = intVal( $quiz['sfwd-quiz_passingpercentage'] );
		$pass = ( $result >= $passingpercentage) ? 1 : 0;
		$quiz = get_post( $ld_quiz_id );

		$this->debug(
			array(
				'quiz' 				=> 	$ld_quiz_id,
				'quiz_title' 		=> 	$quiz->post_title,
				'score' 			=> 	$score,
				'count' 			=> 	$questions_count,
				'pass' 				=> 	$pass,
				'rank' 				=> 	'-',
				'time' 				=> 	time(),
				'pro_quizid' 		=> 	$quiz_id,
				'course'			=>	$courseid,
				'lesson'			=>	$lessonid,
				'topic'				=>	$topicid,
			)
		);

		$quizdata = array(
			'quiz' 					=> 	$ld_quiz_id,
			'score' 				=> 	$score,
			'count' 				=> 	$questions_count,
			'question_show_count'	=>	$questions_shown_count,
			'pass' 					=> 	$pass,
			'rank' 					=> 	'-',
			'time' 					=> 	time(),
			'pro_quizid' 			=> 	$quiz_id,
			'course'				=>	$courseid,
			'lesson'				=>	$lessonid,
			'topic'					=>	$topicid,
			'points' 				=> 	$points,
			'total_points' 			=> 	$total_points,
			'percentage' 			=> 	$result,
			'timespent' 			=> 	$timespent,
			'has_graded'   			=> 	( $has_graded ) ? true : false,
			'statistic_ref_id' 		=> 	$statistic_ref_id
		);
		//error_log('quizdata<pre>'. print_r($quizdata, true) .'</pre>');
		
		//On the timestamps below we divide against 1000 because they were generated via JavaScript which uses milliseconds. 
		if ( isset( $_POST['results']['comp']['quizStartTimestamp'] ) )
			$quizdata['started'] = intval( $_POST['results']['comp']['quizStartTimestamp'] / 1000 );
		if ( isset( $_POST['results']['comp']['quizEndTimestamp'] ) )
			$quizdata['completed'] = intval( $_POST['results']['comp']['quizEndTimestamp'] / 1000 );


		if ( $graded ) {
			$quizdata['graded'] = $graded;
		}

		$this->debug( $quizdata );

		$usermeta[] = $quizdata;

		$quizdata['quiz'] = $quiz;
		
		if ( ! empty( $courseid ) ) {
			$quizdata['course'] = get_post( $courseid );
		} else {
			$quizdata['course'] = 0;
		}

		if ( ! empty( $lessonid ) ) {
			$quizdata['lesson'] = get_post( $lessonid );
		} else {
			$quizdata['lesson'] = 0;
		}

		if ( ! empty( $topicid ) ) {
			$quizdata['topic'] = get_post( $topicid );
		} else {
			$quizdata['topic'] = 0;
		}
		
		$quizdata['questions'] = $questions;

		update_user_meta( $user_id, '_sfwd-quizzes', $usermeta );

		if ( ! empty( $courseid ) ) {
			// Remove use of second argument here as the function exepect this to be a lesson_id and we are for some reason passing the ProQuiz ID.
			learndash_ajax_mark_complete( $ld_quiz_id /*, $quiz_id */);
		}

		learndash_process_mark_complete( $user_id, $ld_quiz_id, false, $courseid );

		/**
		 * Does the action 'learndash_quiz_completed'
		 *
		 * @since 2.1.0
		 *
		 * @param  array  	$quizdata
		 * @param  object  $current_user
		 */
		global $current_user;
		
		do_action( 'learndash_quiz_completed', $quizdata, $current_user ); //Hook for completed quiz
	}



	/**
	 * Returns the Quiz ID when submitting the Pro Quiz ID
	 *
	 * @since 2.1.0
	 *
	 * @param  int 	$pro_quizid
	 * @return int  quiz ID
	 */
	function get_ld_quiz_id( $pro_quizid ) {
		$quizzes = SFWD_SlickQuiz::get_all_quizzes();
		//$this->debug( $quizzes);

		foreach ( $quizzes as $quiz ) {
			$quizmeta = get_post_meta( $quiz->ID, '_sfwd-quiz', true );
			if ( ! empty( $quizmeta['sfwd-quiz_quiz_pro'] ) && $quizmeta['sfwd-quiz_quiz_pro'] == $pro_quizid ) {
				return $quiz->ID;
			}
		}
	}



	/**
	 * Returns an array of quizes in the string format of "$quiz_id - $quiz_name"
	 *
	 * @since 2.1.0
	 *
	 * @return array  $list  String of $q->getId() . ' - ' . $q->getName()
	 */
	static function get_quiz_list() {
		$quizzes_list = array();

		/*

		$transient_key = "learndash_quizzes_list";
		$quizzes_list = learndash_get_valid_transient( $transient_key );
		if ( $quizzes_list === false ) {

			$quiz    = new WpProQuiz_Model_QuizMapper();
			$quizzes = $quiz->fetchAll();

			if ( ! empty( $quizzes ) ) {
				foreach ( $quizzes as $q ) {
					$quizzes_list[ $q->getId() ] = $q->getId() . ' - ' . $q->getName();
				}
			}
			set_transient( $transient_key, $quizzes_list, MINUTE_IN_SECONDS );
		}
		return $quizzes_list;
		*/
		
		 /**
		 * Logic rewrite 
		 * The above logic was abondoned as being to heave for sites running over a few hundred quizzes. This function is only used on the 
		 * single Quix admin editor form and only used to show a select box with the quiz title. So don't need to ever overhead of the 
		 * MVC object loading. 
		 *
		 * 
		 * @since 2.2.0.2
		 * 
		 */
		
		
		global $wpdb;

		$sql_str = "SELECT id, name FROM ". $wpdb->prefix . "wp_pro_quiz_master ORDER BY id ";
		//error_log('sql_str['. $sql_str .']');
		$quiz_items = $wpdb->get_results( $sql_str );
		//error_log('quiz_items<pre>'. print_r($quiz_items, true) .'</pre>');

		if ( ! empty( $quiz_items ) ) {
			foreach ( $quiz_items as $q ) {
				$quizzes_list[ $q->id ] = $q->id . ' - ' . $q->name;
			}
		}
		return $quizzes_list;
	}



	/**
	 * Echoes the HTML with inline javascript that contains the JSON representation of the certificate details and continue link details
	 *
	 * @since 2.1.0
	 */
	static function certificate_details( $pro_quiz_id = null ) {
		
		$quiz_post_id = 0;
		
		if ( is_null( $pro_quiz_id ) ) {
			global $post;
			if ( ( $post instanceof WP_Post ) && ( $post->post_type == 'sfwd-quiz' ) ) {
				$pro_quiz_id = $post->ID;
			} 	
		} else {
			$quiz_post_id = learndash_get_quiz_id_by_pro_quiz_id( $pro_quiz_id );
			if ( !empty( $quiz_post_id ) ) {
				$quiz_post = get_post( $quiz_post_id );
				if ( ( $quiz_post instanceof WP_Post ) && ( $quiz_post->post_type == 'sfwd-quiz' ) ) {
					$quiz_post_id = $quiz_post->ID;
				} 
			}
		}

		if ( !empty( $quiz_post_id ) ) {
			$continue_link = learndash_quiz_continue_link( $quiz_post_id );

			echo '<script>';
			echo 'var certificate_details = ' . json_encode( learndash_certificate_details( $quiz_post_id ) ) . ';';
			echo '</script>';

			echo '<script>';
			echo 'var certificate_pending = "' . 
				SFWD_LMS::get_template( 
					'learndash_quiz_messages', 
					array(
						'quiz_post_id'	=>	$quiz_post_id,
						'context' 		=> 	'quiz_certificate_pending_message',
						'message' 		=> 	esc_html__( 'Certificate Pending - Questions still need to be graded, please check your profile for the status.', 'learndash' )
					)
				) . '";';
			echo '</script>';

			/** Continue link will appear through javascript **/
			echo '<script>';
			echo "var continue_details ='" . $continue_link . "';";
			echo '</script>';
		}
	}
	
	/**
	 * Returns the certificate link appended to input HTML content if the Post ID is set, else it only returns the input HTML content
	 *
	 * @since 2.1.0
	 *
	 * @param  string $content HTML
	 * @return string HTML $content or $content concatenated with the certificate link
	 */
	static function certificate_link( $content, $pro_quiz_id = null ) {
		$quiz_post_id = 0;
		
		if ( is_null( $pro_quiz_id ) ) {
			global $post;
			if ( ( $post instanceof WP_Post ) && ( $post->post_type == 'sfwd-quiz' ) ) {
				$pro_quiz_id = $post->ID;
			} 	
		} else {
			$quiz_post_id = learndash_get_quiz_id_by_pro_quiz_id( $pro_quiz_id );
			if ( !empty( $quiz_post_id ) ) {
				$quiz_post = get_post( $quiz_post_id );
				if ( ( $quiz_post instanceof WP_Post ) && ( $quiz_post->post_type == 'sfwd-quiz' ) ) {
					$quiz_post_id = $quiz_post->ID;
				} 
			}
		}

		if ( !empty( $quiz_post_id ) ) {
			$cd  = learndash_certificate_details( $quiz_post_id );
			if ( ( !empty( $cd ) ) && ( isset( $cd['certificateLink'] ) ) && ( !empty( $cd['certificateLink'] ) ) ) {
				$user_id = get_current_user_id();
				
				$ret = "<a class='btn-blue' href='" . $cd['certificateLink'] . "' target='_blank'>" . apply_filters('ld_certificate_link_label', 
				SFWD_LMS::get_template( 
					'learndash_quiz_messages', 
					array(
						'quiz_post_id'	=>	$quiz_post_id,
						'context' 		=> 	'quiz_certificate_button_label',
						'message' 		=> 	esc_html__( 'PRINT YOUR CERTIFICATE', 'learndash' )
					)
				), $user_id, $quiz_post_id ) . '</a>';
				$content .= $ret;
			}
		}

		return $content;
	}



	/**
	 * Returns the HTML of the add or edit page for the current quiz.  If advanced quizes are disabled, it returns an empty string.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	static function edithtml() {
		global $pagenow, $post;
		$_post = array( '1' );

		if ( ! empty( $_GET['templateLoadId'] ) ) {
			$_post = $_GET;
		}

		if ( $pagenow == 'post-new.php' && @$_GET['post_type'] == 'sfwd-quiz' || $pagenow == 'post.php' && ! empty( $_GET['post'] ) && @get_post( $_GET['post'] )->post_type == 'sfwd-quiz' ) {
			//To fix issues with plugins using get_current_screen
			$screen_file = ABSPATH . '/wp-admin/includes/screen.php';
			require_once( $screen_file );
			//To fix issues with plugins using get_current_screen

			$quizId = 0;

			if ( ! empty( $_GET['post'] ) ) {
				$quizId = intval( learndash_get_setting( $_GET['post'], 'quiz_pro', true ) );

				/**
				 * Filter whether advance quiz is disabled or not
				 *
				 * @param  bool
				 */
				if ( apply_filters( 'learndash_disable_advance_quiz', false, get_post( $_GET['post'] ) ) ) {
					return '';
				}
			}

			$pro_quiz = new WpProQuiz_Controller_Quiz();

			ob_start();
			$pro_quiz->route( array(
				'action' => 'addEdit',
				'quizId' => $quizId,
				'post_id' => @$_GET['post'] ),
				$_post
			);
			$return = ob_get_clean();

			return $return;
		}
	}



	/**
	 * Routes to the WpProQuiz_Controller_Quiz controller to output the add or edit page for quizes if not autosaving, post id is set,
	 *   and the current user has permissions to add or edit quizes.  If there is an available template to load, wordpress redirects to
	 *   the proper URL.
	 *
	 * @since 2.1.0
	 *
	 * @param  int $post_id
	 */
	static function edit_process( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( empty( $post_id) || empty( $_POST['post_type'] ) ) {
			return '';
		}

		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		$post = get_post( $post_id );

		/**
		 * Filter whether advance quiz is disabled or not
		 *
		 * @param  bool
		 */
		if ( 'sfwd-quiz' != $post->post_type || empty( $_POST['form'] ) || ! empty( $_POST['disable_advance_quiz_save'] ) || apply_filters( 'learndash_disable_advance_quiz', false, $post ) ) {
			return;
		}

		$quizId   = intval( learndash_get_setting( $post_id, 'quiz_pro', true ) );
		$pro_quiz = new WpProQuiz_Controller_Quiz();
		ob_start();
		$pro_quiz->route( array( 'action' => 'addEdit', 'quizId' => $quizId, 'post_id' => $post_id) );
		ob_get_clean();

		if ( ! empty( $_POST['templateLoad'] ) && ! empty( $_POST['templateLoadId'] ) ) {
			$url = admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '&templateLoad=' . rawurlencode( $_POST['templateLoad'] ) . '&templateLoadId=' . $_POST['templateLoadId'];
			wp_redirect( $url );
			exit;
		}
	}



	/**
	 * Returns a MD5 checksum on a concatenated string comprised of user id, question id, and pos
	 *
	 * @since 2.1.0
	 *
	 * @param  int 		$question_id
	 * @param  int 		$pos
	 * @return string 	MD5 Checksum
	 */
	static function datapos( $question_id, $pos ) {
		$pos = intval( $pos );;
		return md5( get_current_user_id() . $question_id . $pos );
	}



	/**
	 * Returns an array of MD5 Checksums on a concatenated string comprised of user id, question id, and i, where the array size is count and i is incremented from 0 for each array element
	 *
	 * @since 2.1.0
	 *
	 * @param  int 		$question_id
	 * @param  int 		$count
	 * @return array  	Array of MD5 checksum strings
	 */
	static function datapos_array( $question_id, $count ) {
		$datapos_array = array();
		$user_id       = get_current_user_id();

		for ( $i = 0; $i < $count; $i++ ) {
			$datapos_array[ $i] = md5( $user_id . $question_id . $i );
		}

		return $datapos_array;
	}

	static function showModalWindow() {
		static $show_only_once = false;
		
		if ( !$show_only_once ) {
			$show_only_once = true;
			?>
			<style>
			.wpProQuiz_blueBox {
				padding: 20px; 
				background-color: rgb(223, 238, 255); 
				border: 1px dotted;
				margin-top: 10px;
			}
			.categoryTr th {
				background-color: #F1F1F1;
			}
			.wpProQuiz_modal_backdrop {
				background: #000;
				opacity: 0.7;
				top: 0;
				bottom: 0;
				right: 0;
				left: 0;
				position: fixed;
				z-index: 159900;
			}
			.wpProQuiz_modal_window {
				position: fixed;
				background: #FFF;
				top: 40px;
				bottom: 40px;
				left: 40px;
				right: 40px;
				z-index: 160000;
			}
			.wpProQuiz_actions {
				display: none;
				padding: 2px 0 0;
			}

			.mobile .wpProQuiz_actions {
				display: block;
			}

			tr:hover .wpProQuiz_actions {
				display: block;
			}
			</style>
			<div id="wpProQuiz_user_overlay" style="display: none;">
				<div class="wpProQuiz_modal_window" style="padding: 20px; overflow: scroll;">
					<input type="button" value="<?php esc_html_e('Close'); ?>" class="button-primary" style=" position: fixed; top: 48px; right: 59px; z-index: 160001;" id="wpProQuiz_overlay_close">
			
					<div id="wpProQuiz_user_content" style="margin-top: 20px;"></div>
			
					<div id="wpProQuiz_loadUserData" class="wpProQuiz_blueBox" style="background-color: #F8F5A8; display: none; margin: 50px;">
						<img alt="load" src="data:image/gif;base64,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">
						<?php esc_html_e('Loading', 'learndash'); ?>
					</div>
				</div>
				<div class="wpProQuiz_modal_backdrop"></div>
			</div>
			<?php
		}
	}
	

	function learndash_quiz_content($quiz_content = '', WP_Post $quiz_post ) {
		return $quiz_content;
		
		
		//error_log('in '. __FUNCTION__ );
		//error_log('quiz_content['. $quiz_content .']');
		//error_log('post<pre>'. print_r($post, true) .'</pre>');
		
		//$user_quiz_progress = get_user_meta( 1, '_sfwd-quizzes', true);
		//error_log('user_quiz_progress<pre>'. print_r($user_quiz_progress, true) .'</pre>');
		
		// First we get the wp_pro_quiz_id from the post meta for this 
		$wp_pro_quiz_id = get_post_meta( $quiz_post->ID, 'quiz_pro_id', true);
		//error_log('wp_pro_quiz_id['. $wp_pro_quiz_id .']');
		
		$user_id = get_current_user_id();
		
		if ( ( !empty( $wp_pro_quiz_id ) ) && ( !empty( $user_id ) ) ) {
		
			global $wpdb;
			$sql_str = $wpdb->prepare( "SELECT statistic_ref_id FROM ". $wpdb->prefix . "wp_pro_quiz_statistic_ref WHERE quiz_id=%d AND user_id=%d ORDER BY create_time DESC", $wp_pro_quiz_id, $user_id );
			//error_log('sql_str['. $sql_str .']');
			$quiz_post_id_statistic_ref_id = $wpdb->get_var( $sql_str );
			//error_log('quiz_post_id_statistic_ref_id['. $quiz_post_id_statistic_ref_id .']');
			if ( !empty( $quiz_post_id_statistic_ref_id ) ) {
				$sql_str = $wpdb->prepare( "SELECT * FROM ". $wpdb->prefix . "wp_pro_quiz_statistic WHERE statistic_ref_id=%d", 
					$quiz_post_id_statistic_ref_id );
				//error_log('sql_str['. $sql_str .']');
				$quiz_post_id_statistics = $wpdb->get_results( $sql_str );
				//error_log('quiz_post_id_statistics<pre>'. print_r($quiz_post_id_statistics, true) .'</pre>');
				if ( !empty( $quiz_post_id_statistics ) ) {
					$quiz_statistics_data = array();
					$quiz_statistics_data['data'] = $quiz_post_id_statistics;
					$quiz_statistics_data['button'] = '<input type="button" name="viewStatistics" value="'. esc_html_x( 'View Previous Answers', 'Previous Quiz Button Label', 'learndash' ) .'" class="wpProQuiz_button">';
					$quiz_content .= '<div id="learndash-quiz-statistics" data="'. htmlspecialchars( json_encode( $quiz_statistics_data ) ) .'">';
				}
			}
		}
		
		return $quiz_content;
	}


}

new LD_QuizPro();
