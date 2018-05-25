<?php
class WpProQuiz_View_QuizEdit extends WpProQuiz_View_View {
	
	/**
	 * @var WpProQuiz_Model_Quiz
	 */
	public $quiz;
	
	public function show($get = null) {
?>
<style>
.wpProQuiz_quizModus th, .wpProQuiz_quizModus td {
	border-right: 1px solid #A0A0A0;
	padding: 5px;
}
</style>
<div class="wrap wpProQuiz_quizEdit">
<?php /*	<form method="post" action="admin.php?page=ldAdvQuiz&action=addEdit&quizId=<?php echo $this->quiz->getId(); ?>&post_id=<?php echo @$_GET['post_id']; ?>> */ ?>
		<div style="float: right;">
			<select name="templateLoadId">
				<?php 
					foreach($this->templates as $template) {
						echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
					}
				?>
			</select>
			<input type="submit" name="templateLoad" value="<?php esc_html_e('load template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" class="button-primary">
		</div>
		<div style="clear: both;"></div>
		<div id="poststuff">
			<input name="name" id="wpProQuiz_title" type="hidden" class="regular-text" value="<?php echo $this->quiz->getName(); ?>">
			<?php 
			/*<div class="postbox">
				<h3 class="hndle"><?php esc_html_e('Quiz title', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <?php esc_html_e('(required)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="inside">
					<input name="name" id="wpProQuiz_title" type="text" class="regular-text" value="<?php echo $this->quiz->getName(); ?>">
				</div>
			</div>
			*/?>
			<div class="postbox">
				<h3 class="hndle"><?php esc_html_e('Options', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="wrap">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<?php echo sprintf( esc_html_x('Hide %s title', 'Hide quiz title', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Hide title', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="title_hidden">
											<input type="checkbox" id="title_hidden" value="1" name="titleHidden" <?php echo $this->quiz->isTitleHidden() ? 'checked="checked"' : '' ?> >
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('The title serves as %s heading.', 'The title serves as quiz heading.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' )); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php echo sprintf( esc_html_x('Hide "Restart %s" button', 'Hide "Restart quiz" button', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php echo sprintf( esc_html_x('Hide "Restart %s" button', 'Hide "Restart quiz" button', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?></span>
										</legend>
										<label for="btn_restart_quiz_hidden">
											<input type="checkbox" id="btn_restart_quiz_hidden" value="1" name="btnRestartQuizHidden" <?php echo $this->quiz->isBtnRestartQuizHidden() ? 'checked="checked"' : '' ?> >
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('Hide the "Restart %s" button in the Frontend.', 'Hide the "Restart quiz" button in the Frontend.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Hide "View question" button', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Hide "View question" button', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="btn_view_question_hidden">
											<input type="checkbox" id="btn_view_question_hidden" value="1" name="btnViewQuestionHidden" <?php echo $this->quiz->isBtnViewQuestionHidden() ? 'checked="checked"' : '' ?> >
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('Hide the "View question" button in the Frontend.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Display question randomly', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Display question randomly', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="question_random">
											<input type="checkbox" id="question_random" value="1" name="questionRandom" <?php echo $this->quiz->isQuestionRandom() ? 'checked="checked"' : '' ?> >
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Display answers randomly', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Display answers randomly', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="answer_random">
											<input type="checkbox" id="answer_random" value="1" name="answerRandom" <?php echo $this->quiz->isAnswerRandom() ? 'checked="checked"' : '' ?> >
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Sort questions by category', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Sort questions by category', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="sortCategories" <?php $this->checked($this->quiz->isSortCategories()); ?> >
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('Also works in conjunction with the "display randomly question" option.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Time limit', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Time limit', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="time_limit">
											<input type="number" min="0" class="small-text" id="time_limit" value="<?php echo $this->quiz->getTimeLimit(); ?>" name="timeLimit"> <?php esc_html_e('Seconds', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('0 = no limit', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php echo sprintf( esc_html_x('Protect %s Answers in Browser Cookie', 'Protect Quiz Answers in Browser Cookie', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php echo sprintf( esc_html_x('Use cookies for %s Answers', 'Use cookies for Quiz Answers', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></span>
										</legend>
										<label for="time_limit_cookie">
											<input type="number" min="0" class="small-text" id="time_limit_cookie" value="<?php echo intval($this->quiz->getTimeLimitCookie()); ?>" name="timeLimitCookie"> <?php esc_html_e('Seconds', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x("0 = Don't save answers. This option will save the user's answers into a browser cookie until the %s is submitted.", 'placeholders: Quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Statistics', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Statistics', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="statistics_on">
											<input type="checkbox" id="statistics_on" value="1" name="statisticsOn" <?php echo ( !isset( $_GET["post"] ) || $this->quiz->isStatisticsOn() ) ? 'checked="checked"' : ''; ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('Statistics about right or wrong answers. Statistics will be saved by completed %s, not after every question. The statistics is only visible over administration menu. (internal statistics)', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr id="statistics_ip_lock_tr" style="display: none;">
								<th scope="row">
									<?php esc_html_e('Statistics IP-lock', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Statistics IP-lock', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="statistics_ip_lock">
											<input type="number" min="0" class="small-text" id="statistics_ip_lock" value="<?php echo ($this->quiz->getStatisticsIpLock() === null) ? 0 : $this->quiz->getStatisticsIpLock(); ?>" name="statisticsIpLock">
											<?php esc_html_e('in minutes (recommended 1440 minutes = 1 day)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>

							<tr id="statistics_show_profile_tr" style="display: none;">
								<th scope="row">
									<?php esc_html_e('View Profile Statistics', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('View Profile Statistics', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="statistics_on">
											<input type="checkbox" id="view_profile_statistics_on" value="1" name="viewProfileStatistics" <?php echo ( !isset( $_GET["post"] ) || $this->quiz->getViewProfileStatistics() ) ? 'checked="checked"' : ''; ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('Enable user to view statistics for this %s on their profile.', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
									</fieldset>
								</td>
							</tr>


							<tr>
								<th scope="row">
									<?php echo sprintf( esc_html_x('Execute %s only once', 'Execute quiz only once', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
								</th>
								<td>
									<fieldset>
									
										<legend class="screen-reader-text">
											<span><?php echo sprintf( esc_html_x('Execute %s only once', 'Execute quiz only once', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?></span>
										</legend>
										
										<label>
											<input type="checkbox" value="1" name="quizRunOnce" <?php echo $this->quiz->isQuizRunOnce() ? 'checked="checked"' : '' ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you activate this option, the user can complete the %1$s only once. Afterwards the %2$s is blocked for this user.', 'placeholders: quiz, quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
										
										<div id="wpProQuiz_quiz_run_once_type" style="margin-bottom: 5px; display: none;">
											<?php esc_html_e('This option applies to:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN);
											
											$quizRunOnceType = $this->quiz->getQuizRunOnceType();
											$quizRunOnceType = ($quizRunOnceType == 0) ? 1: $quizRunOnceType; 
											
											?>		
											<label>
												<input name="quizRunOnceType" type="radio" value="1" <?php echo ($quizRunOnceType == 1) ? 'checked="checked"' : ''; ?>>
												<?php esc_html_e('all users', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
											</label>
											<label>
												<input name="quizRunOnceType" type="radio" value="2" <?php echo ($quizRunOnceType == 2) ? 'checked="checked"' : ''; ?>>
												<?php esc_html_e('registered useres only', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
											</label>
											<label>
												<input name="quizRunOnceType" type="radio" value="3" <?php echo ($quizRunOnceType == 3) ? 'checked="checked"' : ''; ?>>
												<?php esc_html_e('anonymous users only', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
											</label>
											
											<div id="wpProQuiz_quiz_run_once_cookie" style="margin-top: 10px;">
												<label>
													<input type="checkbox" value="1" name="quizRunOnceCookie" <?php echo $this->quiz->isQuizRunOnceCookie() ? 'checked="checked"' : '' ?>>
													<?php esc_html_e('user identification by cookie', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
												</label>
												<p class="description">
													<?php esc_html_e('If you activate this option, a cookie is set additionally for unregistrated (anonymous) users. This ensures a longer assignment of the user than the simple assignment by the IP address.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
												</p>
											</div>
											
											<div style="margin-top: 15px;">
												<input class="button-secondary" type="button" name="resetQuizLock" value="<?php esc_html_e('Reset the user identification', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>">
												<span id="resetLockMsg" style="display:none; background-color: rgb(255, 255, 173); border: 1px solid rgb(143, 143, 143); padding: 4px; margin-left: 5px; "><?php esc_html_e('User identification has been reset.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
												<p class="description">
													<?php esc_html_e('Resets user identification for all users.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
												</p>
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Show only specific number of questions', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Show only specific number of questions', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="showMaxQuestion" <?php echo $this->quiz->isShowMaxQuestion() ? 'checked="checked"' : '' ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, maximum number of displayed questions will be X from X questions. (The output of questions is random)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div id="wpProQuiz_showMaxBox" style="display: none;">
											<label>
												<?php esc_html_e('How many questions should be displayed simultaneously:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
												<input class="small-text" type="text" name="showMaxQuestionValue" value="<?php echo $this->quiz->getShowMaxQuestionValue(); ?>">
											</label>
											<label>
												<input type="checkbox" value="1" name="showMaxQuestionPercent" <?php echo $this->quiz->isShowMaxQuestionPercent() ? 'checked="checked"' : '' ?>>
												<?php esc_html_e('in percent', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
											</label>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Prerequisites', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Prerequisites', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="prerequisite" <?php $this->checked($this->quiz->isPrerequisite()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you enable this option, you can choose %1$s, which user have to finish before he can start this %2$s.', 'placeholders: quiz, quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
										<p class="description">
											<?php echo sprintf( esc_html_x('In all selected %s statistic function have to be active. If it is not it will be activated automatically.', 'placeholders: quizzes', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quizzes' ) ); ?>
										</p>
										<div id="prerequisiteBox" style="display: none;">
											<table>
												<tr>
													<th style="width: 120px; padding: 0;"><?php echo sprintf( esc_html_x('%s', 'Quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'course' ) ); ?></th>
													<th style="padding: 0; width: 50px;"></th>
													<th style="padding: 0; width: 400px;"><?php echo sprintf( esc_html_x('Prerequisites (This %s has to be finished)', 'Prerequisites (This quiz has to be finished)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?></th>
												</tr>
												<tr>
													<td style="padding: 0;">
														<select multiple="multiple" size="8" style="width: 200px;" name="quizList">
															<?php foreach($this->quizList as $list) {
																if(in_array($list['id'], $this->prerequisiteQuizList))
																	continue;
																
																	echo '<option value="'.$list['id'].'" title="'.$list['name'].'">'.$list['name'].'</option>';
															} ?>
														</select>
													</td>
													<td style="padding: 0; text-align: center;">
														<div>
															<input type="button" id="btnPrerequisiteAdd" value="&gt;&gt;">
														</div>
														<div>
															<input type="button" id="btnPrerequisiteDelete" value="&lt;&lt;">
														</div>
													</td>
													<td style="padding: 0;">
														<select multiple="multiple" size="8" style="width: 200px" name="prerequisiteList[]">
															<?php foreach($this->quizList as $list) {
																if(!in_array($list['id'], $this->prerequisiteQuizList))
																	continue;
																
																	echo '<option value="'.$list['id'].'" title="'.$list['name'].'">'.$list['name'].'</option>';
															} ?>
														</select>
													</td>
												</tr>
											</table>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Question overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Question overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="showReviewQuestion" <?php $this->checked($this->quiz->isShowReviewQuestion()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('Add at the top of the quiz a question overview, which allows easy navigation. Additional questions can be marked "to review".', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<p class="description">
											<?php echo sprintf( esc_html_x('Additional %s overview will be displayed, before %s is finished.', 'placeholders: quiz, quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ), LearnDash_Custom_Label::label_to_lower( 'quiz' )); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<?php esc_html_e('Question overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>: <a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/questionOverview.png'; ?> ">
											</div>
										</div>
										<div class="wpProQuiz_demoBox">
											<?php echo sprintf( esc_html_x( '%s-summary', 'Quiz-summary', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN ), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?>: <a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/quizSummary.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
								<th scope="row">
									<?php echo sprintf( esc_html_x( '%s-summary', 'Quiz-summary', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN ), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php echo sprintf( esc_html_x( '%s-summary', 'Quiz-summary', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN ), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="quizSummaryHide" <?php $this->checked($this->quiz->isQuizSummaryHide()); ?>>
											<?php esc_html_e('Deactivate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you enable this option, no %1$s overview will be displayed, before finishing %2$s.', 'placeholders: quiz, quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
								<th scope="row">
									<?php esc_html_e('Skip question', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Skip question', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="skipQuestionDisabled" <?php $this->checked($this->quiz->isSkipQuestionDisabled()); ?>>
											<?php esc_html_e('Deactivate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, user won\'t be able to skip question. (only in "Overview -> next" mode). User still will be able to navigate over "Question-Overview"', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Admin e-mail notification', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Admin e-mail notification', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="radio" name="emailNotification" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE; ?>" <?php $this->checked($this->quiz->getEmailNotification(), WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE); ?>>
											<?php esc_html_e('Deactivate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<label>
											<input type="radio" name="emailNotification" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER; ?>" <?php $this->checked($this->quiz->getEmailNotification(), WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER); ?>>
											<?php esc_html_e('for registered users only', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<label>
											<input type="radio" name="emailNotification" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL; ?>" <?php $this->checked($this->quiz->getEmailNotification(), WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL); ?>>
											<?php esc_html_e('for all users', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you enable this option, you will be informed if a user completes this %s.', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
										<p class="description">
											<?php esc_html_e('E-Mail settings can be edited in global settings.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('User e-mail notification', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('User e-mail notification', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="userEmailNotification" value="1" <?php $this->checked($this->quiz->isUserEmailNotification()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you enable this option, an email is sent with his %s result to the user. (only registered users)', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'course' ) ); ?>
										</p>
										<p class="description">
											<?php esc_html_e('E-Mail settings can be edited in global settings.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Autostart', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Autostart', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="autostart" value="1" <?php $this->checked($this->quiz->isAutostart()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you enable this option, the %s will start automatically after the page is loaded.', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php echo sprintf( esc_html_x('Only registered users are allowed to start the %s', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php echo sprintf( esc_html_x('Only registered users are allowed to start the %s', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="startOnlyRegisteredUser" value="1" <?php $this->checked($this->quiz->isStartOnlyRegisteredUser()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you enable this option, only registered users allowed start the %s.', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php $this->questionOptions(); ?>
			<?php $this->resultOptions(); ?>
			<?php $this->quizMode(); ?>
			<?php $this->leaderboardOptions(); ?>
			<?php $this->form(); ?>
			<?php
				$quiz_desc = $this->quiz->getText();

				if(!empty($quiz_desc) && $quiz_desc != "AAZZAAZZ" && !empty($get["post_id"])) {
					$post_id = $get["post_id"];
					$quiz_post = get_post($post_id);
					$update_post["ID"] = $post_id;
					$update_post["post_content"] = $quiz_post->post_content."<br>".$quiz_desc;
					wp_update_post($update_post);
					global $wpdb;
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."wp_pro_quiz_master SET text = 'AAZZAAZZ' WHERE id = '%d'", $this->quiz->getId()));
				}
			?>
			<input name="text" type="hidden" value="AAZZAAZZ" />
			<?php /* <div class="postbox">
				<h3 class="hndle"><?php esc_html_e('Quiz description', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <?php esc_html_e('(required)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="inside">
					<p class="description">
						<?php esc_html_e('This text will be displayed before start of the quiz.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
					</p>
					<?php

						wp_editor($this->quiz->getText(), "text"); 
					?>
				</div>
			</div>
			*/
			?>
			<div class="postbox">
				<h3 class="hndle"><?php esc_html_e('Results text', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <?php esc_html_e('(optional)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="inside">
					<p class="description">
						<?php echo sprintf( esc_html_x('This text will be displayed at the end of the %s (in results). (this text is optional)', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
					</p>
					<div style="padding-top: 10px; padding-bottom: 10px;">
						<label for="wpProQuiz_resultGradeEnabled">
							<?php esc_html_e('Activate graduation', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							<input type="checkbox" name="resultGradeEnabled" id="wpProQuiz_resultGradeEnabled" value="1" <?php echo $this->quiz->isResultGradeEnabled() ? 'checked="checked"' : ''; ?>>
						</label>
					</div>
					<div style="display: none;" id="resultGrade">
						<div>
							<strong><?php esc_html_e('Hint:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></strong>
							<ul style="list-style-type: square; padding: 5px; margin-left: 20px; margin-top: 0;">
								<li><?php esc_html_e('Maximal 15 levels', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
								<li>
									<?php echo sprintf( esc_html_x('Percentages refer to the total score of the %1$s. (Current total %2d points in %3$d questions.', 'placeholders: quiz, question points, question count', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ), $this->quiz->fetchSumQuestionPoints(), $this->quiz->fetchCountQuestions()); ?>
									</li>
								<li><?php esc_html_e('Values can also be mixed up', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
								<li><?php esc_html_e('10,15% or 10.15% allowed (max. two digits after the decimal point)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
							</ul>
								
						</div>
						<div>
							<ul id="resultList">
							<?php
								$resultText = $this->quiz->getResultText();
								
								for($i = 0; $i < 15; $i++) {

									if($this->quiz->isResultGradeEnabled() && isset($resultText['text'][$i])) {
							?>
								<li style="padding: 5px; border: 1; border: 1px dotted;">
									<div style="margin-bottom: 5px;"><?php wp_editor($resultText['text'][$i], 'resultText_'.$i, array('textarea_rows' => 3, 'textarea_name' => 'resultTextGrade[text][]')); ?></div>
									<div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
										<?php esc_html_e('from:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="<?php echo $resultText['prozent'][$i]?>"> <?php esc_html_e('percent', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), $resultText['prozent'][$i]); ?>
										<input type="button" style="float: right;" class="button-primary deleteResult" value="<?php esc_html_e('Delete graduation', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>">
										<div style="clear: right;"></div>
										<input type="hidden" value="1" name="resultTextGrade[activ][]">
									</div>
								</li>
							
							<?php } else { ?>
								<li style="padding: 5px; border: 1; border: 1px dotted; <?php echo $i ? 'display:none;' : '' ?>">
									<div style="margin-bottom: 5px;"><?php wp_editor('', 'resultText_'.$i, array('textarea_rows' => 3, 'textarea_name' => 'resultTextGrade[text][]')); ?></div>
									<div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
										<?php esc_html_e('from:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="0"> <?php esc_html_e('percent', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), '0'); ?>
										<input type="button" style="float: right;" class="button-primary deleteResult" value="<?php esc_html_e('Delete graduation', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>">
										<div style="clear: right;"></div>
										<input type="hidden" value="<?php echo $i ? '0' : '1' ?>" name="resultTextGrade[activ][]">
									</div>
								</li>
							<?php } } ?>
							</ul>
							<input type="button" class="button-primary addResult" value="<?php esc_html_e('Add graduation', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>">
						</div>
					</div>
					<div id="resultNormal">
						<?php
						
							$resultText = is_array($resultText) ? '' : $resultText;
							wp_editor($resultText, 'resultText', array('textarea_rows' => 10));
						?>
					</div>
				</div>
			</div>
		<!--<div style="float: left;">
			<input type="submit" name="submit" class="button-primary" id="wpProQuiz_save" value="<?php esc_html_e('Save', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>">
		</div>-->
		<div style="float: right;">
			<input type="text" placeholder="<?php esc_html_e('template name', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" class="regular-text" name="templateName" style="border: 1px solid rgb(255, 134, 134);">
			<select name="templateSaveList">
				<option value="0">=== <?php esc_html_e('Create new template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> === </option>
				<?php 
					foreach($this->templates as $template) {
						echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
					}
				?>
			</select>
			
			<input type="submit" name="template" class="button-primary" id="wpProQuiz_saveTemplate" value="<?php esc_html_e('Save as template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>">
		</div>
		<div style="clear: both;"></div>
		</div>
	<?php /* </form> */ ?>
</div>
<?php
	}
	
	private function resultOptions() {
?>
			<div class="postbox">
				<h3 class="hndle"><?php esc_html_e('Result-Options', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="wrap">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<?php esc_html_e('Show average points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Show average points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="showAverageResult" <?php $this->checked($this->quiz->isShowAverageResult()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('Statistics-function must be enabled.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/averagePoints.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Show category score', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Show category score', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="showCategoryScore" value="1" <?php $this->checked($this->quiz->isShowCategoryScore()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, the results of each category is displayed on the results page.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
									
									<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/catOverview.png'; ?> ">
											</div>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Hide correct questions - display', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Hide correct questions - display', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="hideResultCorrectQuestion" value="1" <?php $this->checked($this->quiz->isHideResultCorrectQuestion()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you select this option, no longer the number of correctly answered questions are displayed on the results page.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
									
									<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/hideCorrectQuestion.png'; ?> ">
											</div>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php echo sprintf( esc_html_x('Hide %s time - display', 'Hide quiz time - display', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php echo sprintf( esc_html_x('Hide %s time - display', 'Hide quiz time - display', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="hideResultQuizTime" value="1" <?php $this->checked($this->quiz->isHideResultQuizTime()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('If you enable this option, the time for finishing the %s won\'t be displayed on the results page anymore.', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
										</p>
									</fieldset>
									
									<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/hideQuizTime.png'; ?> ">
											</div>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Hide score - display', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Hide score - display', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" name="hideResultPoints" value="1" <?php $this->checked($this->quiz->isHideResultPoints()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, final score won\'t be displayed on the results page anymore.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
									
									<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/hideQuizPoints.png'; ?> ">
											</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
	
<?php 
	}
	
	private function questionOptions() {
		?>
		
		<div class="postbox">
				<h3 class="hndle"><?php esc_html_e('Question-Options', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="wrap">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<?php esc_html_e('Show points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Show points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label for="show_points">
											<input type="checkbox" id="show_points" value="1" name="showPoints" <?php echo $this->quiz->isShowPoints() ? 'checked="checked"' : '' ?> >
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php echo sprintf( esc_html_x('Shows in %s, how many points are reachable for respective question.', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' )); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Number answers', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Number answers', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="numberedAnswer" <?php echo $this->quiz->isNumberedAnswer() ? 'checked="checked"' : '' ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If this option is activated, all answers are numbered (only single and multiple choice)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/numbering.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Hide correct- and incorrect message', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Hide correct- and incorrect message', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="hideAnswerMessageBox" <?php echo $this->quiz->isHideAnswerMessageBox() ? 'checked="checked"' : '' ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, no correct- or incorrect message will be displayed.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/hideAnswerMessageBox.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Correct and incorrect answer mark', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Correct and incorrect answer mark', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="disabledAnswerMark" <?php echo $this->quiz->isDisabledAnswerMark() ? 'checked="checked"' : '' ?>>
											<?php esc_html_e('Deactivate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, answers won\'t be color highlighted as correct or incorrect. ', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/mark.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Force user to answer each question', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Force user to answer each question', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="forcingQuestionSolve" <?php $this->checked($this->quiz->isForcingQuestionSolve()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, the user is forced to answer each question.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <br>
											<?php esc_html_e('If the option "Question overview" is activated, this notification will appear after end of the quiz, otherwise after each question.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Hide question position overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Hide question position overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="hideQuestionPositionOverview" <?php $this->checked($this->quiz->isHideQuestionPositionOverview()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, the question position overview is hidden.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/hideQuestionPositionOverview.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Hide question numbering', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Hide question numbering', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="hideQuestionNumbering" <?php $this->checked($this->quiz->isHideQuestionNumbering()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, the question numbering is hidden.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/hideQuestionNumbering.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Display category', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Display category', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" value="1" name="showCategory" <?php $this->checked($this->quiz->isShowCategory()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, category will be displayed in the question.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
										<div class="wpProQuiz_demoBox">
											<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
												<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/showCategory.png'; ?> ">
											</div>
										</div>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		
		<?php 
	}
	
	private function leaderboardOptions() {
	?>
		<div class="postbox">
			<h3 class="hndle"><?php esc_html_e('Leaderboard', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <?php esc_html_e('(optional)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
			<div class="inside">
				<p>
					<?php esc_html_e('The leaderboard allows users to enter results in public list and to share the result this way.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
				</p>
				<p>
					<?php esc_html_e('The leaderboard works independent from internal statistics function.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
				</p>
				<table class="form-table">
					<tbody id="toplistBox">
						<tr>
							<th scope="row">
								<?php esc_html_e('Leaderboard', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="checkbox" name="toplistActivated" value="1" <?php echo $this->quiz->isToplistActivated() ? 'checked="checked"' : ''; ?>> 
									<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('Who can sign up to the list', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input name="toplistDataAddPermissions" type="radio" value="1" <?php echo $this->quiz->getToplistDataAddPermissions() == 1 ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e('all users', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<label>
									<input name="toplistDataAddPermissions" type="radio" value="2" <?php echo $this->quiz->getToplistDataAddPermissions() == 2 ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e('registered users only', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<label>
									<input name="toplistDataAddPermissions" type="radio" value="3" <?php echo $this->quiz->getToplistDataAddPermissions() == 3 ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e('anonymous users only', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<p class="description">
									<?php esc_html_e('Not registered users have to enter name and e-mail (e-mail won\'t be displayed)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('insert automatically', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input name="toplistDataAddAutomatic" type="checkbox" value="1" <?php $this->checked($this->quiz->isToplistDataAddAutomatic()); ?>>
									<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<p class="description">
									<?php esc_html_e('If you enable this option, logged in users will be automatically entered into leaderboard', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('display captcha', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="checkbox" name="toplistDataCaptcha" value="1" <?php echo $this->quiz->isToplistDataCaptcha() ? 'checked="checked"' : ''; ?> <?php echo $this->captchaIsInstalled ? '' : 'disabled="disabled"'; ?>> 
									<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<p class="description">
									<?php esc_html_e('If you enable this option, additional captcha will be displayed for users who are not registered.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<p class="description" style="color: red;">
									<?php esc_html_e('This option requires additional plugin:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									 <a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really Simple CAPTCHA</a>
								</p>
								<?php if($this->captchaIsInstalled) { ?>
								<p class="description" style="color: green;">
									<?php esc_html_e('Plugin has been detected.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<?php } else { ?>
								<p class="description" style="color: red;">
									<?php esc_html_e('Plugin is not installed.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<?php } ?>
								
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('Sort list by', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input name="toplistDataSort" type="radio" value="1" <?php echo ($this->quiz->getToplistDataSort() == 1) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e('best user', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<label>
									<input name="toplistDataSort" type="radio" value="2" <?php echo ($this->quiz->getToplistDataSort() == 2) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e('newest entry', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<label>
									<input name="toplistDataSort" type="radio" value="3" <?php echo ($this->quiz->getToplistDataSort() == 3) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e('oldest entry', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('Users can apply multiple times', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<div>
									<label>
										<input type="checkbox" name="toplistDataAddMultiple" value="1" <?php echo $this->quiz->isToplistDataAddMultiple() ? 'checked="checked"' : ''; ?>> 
										<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									</label>
								</div>
								<div id="toplistDataAddBlockBox" style="display: none;">
									<label>
										<?php esc_html_e('User can apply after:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										<input type="number" min="0" class="small-text" name="toplistDataAddBlock" value="<?php echo $this->quiz->getToplistDataAddBlock(); ?>"> 
										 <?php esc_html_e('minute', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('How many entries should be displayed', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<div>
									<label>
										<input type="number" min="0" class="small-text" name="toplistDataShowLimit" value="<?php echo $this->quiz->getToplistDataShowLimit(); ?>"> 
										<?php esc_html_e('Entries', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									</label>
								</div>
							</td>
						</tr>
						<tr id="AutomaticallyDisplayLeaderboard">
							<th scope="row">
								<?php echo sprintf( esc_html_x('Automatically display leaderboard in %s result', 'Automatically display leaderboard in quiz result', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' )); ?>
							</th>
							<td>
								<div style="margin-top: 6px;">
									<?php esc_html_e('Where should leaderboard be displayed:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?><br>
									<label style="margin-right: 5px; margin-left: 5px;">
										<input type="radio" name="toplistDataShowIn" value="0" <?php echo ($this->quiz->getToplistDataShowIn() == 0) ? 'checked="checked"' : ''; ?>> 
										<?php esc_html_e('don\'t display', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									</label>
									<label>
										<input type="radio" name="toplistDataShowIn" value="1" <?php echo ($this->quiz->getToplistDataShowIn() == 1) ? 'checked="checked"' : ''; ?>> 
										<?php esc_html_e('below the "result text"', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									</label>
									<span class="wpProQuiz_demoBox" style="margin-right: 5px;">
										<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										<span style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/leaderboardInResultText.png'; ?> ">
										</span>
									</span>
									<label>
										<input type="radio" name="toplistDataShowIn" value="2" <?php echo ($this->quiz->getToplistDataShowIn() == 2) ? 'checked="checked"' : ''; ?>> 
										<?php esc_html_e('in a button', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									</label>
									<span class="wpProQuiz_demoBox">
										<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										<span style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/leaderboardInButton.png'; ?> ">
										</span>
									</span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>					
			</div>
		</div>
	<?php 
	}
	
	private function quizMode() {
	?>
		<div class="postbox">
				<h3 class="hndle"><?php echo sprintf( esc_html_x('%s-Mode', 'Quiz-Mode', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?> <?php esc_html_e('(required)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="inside">
					<table style="width: 100%; border-collapse: collapse; border: 1px solid #A0A0A0;" class="wpProQuiz_quizModus">
						<thead>
							<tr>
								<th style="width: 25%;"><?php esc_html_e('Normal', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
								<th style="width: 25%;"><?php esc_html_e('Normal + Back-Button', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
								<th style="width: 25%;"><?php esc_html_e('Check -> continue', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
								<th style="width: 25%;"><?php esc_html_e('Questions below each other', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><label><input type="radio" name="quizModus" value="0" <?php $this->checked($this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_NORMAL); ?>> <?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></label></td>
								<td><label><input type="radio" name="quizModus" value="1" <?php $this->checked($this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_BACK_BUTTON); ?>> <?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></label></td>
								<td><label><input type="radio" name="quizModus" value="2" <?php $this->checked($this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK); ?>> <?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></label></td>
								<td><label><input type="radio" name="quizModus" value="3" <?php $this->checked($this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE); ?>> <?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></label></td>
							</tr>
							<tr>
								<td>
									<?php echo sprintf( esc_html_x('Displays all questions sequentially, "right" or "false" will be displayed at the end of the %s.', 'placeholders: quiz', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
								</td>
								<td>
									<?php esc_html_e('Allows to use the back button in a question.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</td>
								<td>
									<?php esc_html_e('Shows "right or wrong" after each question.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</td>
								<td>
									<?php esc_html_e('If this option is activated, all answers are displayed below each other, i.e. all questions are on a single page.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</td>
							</tr>
							<tr>
								<td>
									<div class="wpProQuiz_demoBox">
										<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/normal.png'; ?> ">
										</div>
									</div>
								</td>
								<td>
									<div class="wpProQuiz_demoBox">
										<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/backButton.png'; ?> ">
										</div>
									</div>
								</td>
								<td>
									<div class="wpProQuiz_demoBox" style="position: relative;">
										<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										<div style="z-index: 9999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/checkCcontinue.png'; ?> ">
										</div>
									</div>
								</td>
								<td>
									<div class="wpProQuiz_demoBox" style="position: relative;">
										<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										<div style="z-index: 9999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/singlePage.png'; ?> ">
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td>
									<?php esc_html_e('How many questions to be displayed on a page:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?><br>
									<input type="number" name="questionsPerPage" value="<?php echo $this->quiz->getQuestionsPerPage(); ?>" min="0">
									<span class="description">
										<?php esc_html_e('(0 = All on one page)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
									</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
	<?php
	}
	
    private function form() {
		$forms = $this->forms;
		$index = 0;
		
		if(!count($forms))
			$forms = array(new WpProQuiz_Model_Form(), new WpProQuiz_Model_Form());
		else
			array_unshift($forms, new WpProQuiz_Model_Form());
		
    	?>
        <div class="postbox">
				<h3 class="hndle"><?php esc_html_e('Custom fields', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
				<div class="inside">
					
					<p class="description">
						<?php esc_html_e('You can create custom fields, e.g. to request the name or the e-mail address of the users.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
					</p>
					<p class="description">
						<?php esc_html_e('The statistic function have to be enabled.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
					</p>
					
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<?php esc_html_e('Custom fields enable', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Custom fields enable', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<label>
											<input type="checkbox" id="formActivated" value="1" name="formActivated" <?php $this->checked($this->quiz->isFormActivated()); ?>>
											<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</label>
										<p class="description">
											<?php esc_html_e('If you enable this option, custom fields are enabled.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										</p>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php esc_html_e('Display position', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e('Display position', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
										</legend>
										<?php esc_html_e('Where should the fileds be displayed:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?><br>
										<label>
											<input type="radio" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START; ?>" name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(), WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START); ?>>
											<?php echo sprintf( esc_html_x('On the %s startpage', 'On the quiz startpage', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
											
											<div style="display: inline-block;" class="wpProQuiz_demoBox">
												<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
												<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
													<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/customFieldsFront.png'; ?> ">
												</div>
											</div>
											
										</label>
										<label>
											<input type="radio" value="<?php echo WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END; ?>" name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(), WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END); ?> >
											<?php echo sprintf( esc_html_x('At the end of the %s (before the %s result)', 'At the end of the quiz (before the quiz result)', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::label_to_lower( 'quiz' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ); ?>
											
											<div style="display: inline-block;" class="wpProQuiz_demoBox">
												<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
												<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
													<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/customFieldsEnd1.png'; ?> ">
												</div>
											</div>
											
											<div style="display: inline-block;" class="wpProQuiz_demoBox">
												<a href="#"><?php esc_html_e('Demo', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
												<div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
													<img alt="" src="<?php echo WPPROQUIZ_URL.'/img/customFieldsEnd2.png'; ?> ">
												</div>
											</div>
											
										</label>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
					
					<div style="margin-top: 10px; padding: 10px; border: 1px solid #C2C2C2;">
						<table style=" width: 100%; text-align: left; " id="form_table">
							<thead>
								<tr>
									<th><?php esc_html_e('Field name', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
									<th><?php esc_html_e('Type', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
									<th><?php esc_html_e('Required?', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($forms as $form) {
									$checkType = $this->selectedArray($form->getType(), array(
										WpProQuiz_Model_Form::FORM_TYPE_TEXT, WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA, 
										WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX, WpProQuiz_Model_Form::FORM_TYPE_SELECT,
										WpProQuiz_Model_Form::FORM_TYPE_RADIO, WpProQuiz_Model_Form::FORM_TYPE_NUMBER,
										WpProQuiz_Model_Form::FORM_TYPE_EMAIL, WpProQuiz_Model_Form::FORM_TYPE_YES_NO,
										WpProQuiz_Model_Form::FORM_TYPE_DATE
									));
								?>
								<tr <?php echo $index++ == 0 ? 'style="display: none;"' : '' ?>>
									<td>
										<input type="text" name="form[][fieldname]" value="<?php echo esc_attr($form->getFieldname()); ?>" class="regular-text"/>
									</td>
									<td style="position: relative;">
										<select name="form[][type]">
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_TEXT; ?>" <?php echo $checkType[0]; ?>><?php esc_html_e('Text', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA; ?>" <?php echo $checkType[1]; ?>><?php esc_html_e('Textarea', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX; ?>" <?php echo $checkType[2]; ?>><?php esc_html_e('Checkbox', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_SELECT; ?>" <?php echo $checkType[3]; ?>><?php esc_html_e('Drop-Down menu', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_RADIO; ?>" <?php echo $checkType[4]; ?>><?php esc_html_e('Radio', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_NUMBER; ?>" <?php echo $checkType[5]; ?>><?php esc_html_e('Number', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_EMAIL; ?>" <?php echo $checkType[6]; ?>><?php esc_html_e('Email', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_YES_NO; ?>" <?php echo $checkType[7]; ?>><?php esc_html_e('Yes/No', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
											<option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_DATE; ?>" <?php echo $checkType[8]; ?>><?php esc_html_e('Date', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
										</select>
										
										<a href="#" class="editDropDown"><?php esc_html_e('Edit list', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
											
										<div class="dropDownEditBox" style="position: absolute; border: 1px solid #AFAFAF; background: #EBEBEB; padding: 5px; bottom: 0;right: 0;box-shadow: 1px 1px 1px 1px #AFAFAF; display: none;">
											<h4><?php esc_html_e('One entry per line', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h4>
											<div>
												<textarea rows="5" cols="50" name="form[][data]"><?php echo $form->getData() === null ? '' : esc_textarea(implode("\n", $form->getData())); ?></textarea>
											</div>
											
											<input type="button" value="<?php esc_html_e('OK', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" class="button-primary">
										</div>
									</td>
									<td>
										<input type="checkbox" name="form[][required]" value="1" <?php $this->checked($form->isRequired()); ?>>
									</td>
									<td>
										<input type="button" name="form_delete" value="<?php esc_html_e('Delete', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" class="button-secondary">
										<a class="form_move button-secondary" href="#" style="cursor:move;"><?php esc_html_e('Move', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										
										<input type="hidden" name="form[][form_id]" value="<?php echo $form->getFormId(); ?>">
										<input type="hidden" name="form[][form_delete]" value="0">
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						
						<div style="margin-top: 10px;">
							<input type="button" name="form_add" id="form_add" value="<?php esc_html_e('Add field', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" class="button-secondary">
						</div>
					</div>
				</div>
			</div>
		<?php
    }
}