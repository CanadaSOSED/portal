<?php
/**
 * This file contains the code that displays the quiz navigation admin.
 * 
 * @since 2.1.0
 * 
 * @package LearnDash\Quiz
 */

?>
<?php
global $pagenow;
global $typenow;
global $quiz_navigation_admin_pager;

if ( ( isset( $quiz_id ) ) && ( ! empty( $quiz_id ) ) ) {

	if ( ! isset( $widget ) ) {
		$widget = array(
			'show_widget_wrapper' => true,
			'current_question_id' => 0,
		);
	}

	$widget_json = htmlspecialchars( json_encode( $widget ) );

	if ( ( isset( $widget['show_widget_wrapper'] ) ) && ( $widget['show_widget_wrapper'] == 'true' ) ) { ?>
		<div id="quiz_navigation-<?php echo $quiz_id ?>" class="quiz_navigation" data-widget_instance="<?php echo $widget_json; ?>">
	<?php } ?>

	<div class="learndash_navigation_questions_list">
	<?php
	if ( ( isset( $questions_list ) ) && ( ! empty( $questions_list ) ) ) {

		$question_label_idx = 1;
		if ( ( isset( $quiz_navigation_admin_pager ) ) && ( ! empty( $quiz_navigation_admin_pager ) ) ) {
			if ( ( isset( $quiz_navigation_admin_pager['paged'] ) ) && ( $quiz_navigation_admin_pager['paged'] > 1 ) ) {
				$question_label_idx = ( absint( $quiz_navigation_admin_pager['paged'] ) - 1 ) * $quiz_navigation_admin_pager['per_page'] + 1;
			}
		}

		?><ul class="learndash-quiz-questions" class="learndash-quiz-questions-<?php echo absint( $quiz_id ); ?>"><?php
		foreach ( $questions_list as $q_post_id => $q_pro_id ) {
			if ( absint( $q_post_id ) === absint( $widget['current_question_id'] ) ) {
				$selected_style = ' style=" font-weight: bold; " ';
			} else {
				$selected_style = '';
			}
			$question_edit_link = get_edit_post_link( $q_post_id );
			$question_edit_link = add_query_arg('quiz_id', $quiz_id, $question_edit_link );

			?><li class="learndash-quiz-question-item" <?php echo $selected_style; ?>><span class="learndash-quiz-question-item-label"><?php echo $question_label_idx; ?>.</span> <a href="<?php echo $question_edit_link; ?>"><?php echo get_the_title( $q_post_id ); ?></a></li><?php
			$question_label_idx += 1;
		}
		?></ul><?php
	}
	if ( ( isset( $quiz_navigation_admin_pager ) ) && ( ! empty( $quiz_navigation_admin_pager ) ) ) {
		echo SFWD_LMS::get_template(
			'learndash_pager.php',
			array(
				'pager_results' => $quiz_navigation_admin_pager,
				'pager_context' => 'quiz_navigation_admin'
			)
		);
	}
	//if ( ( $widget['current_question_id'] != 0 ) && ( $widget['current_question_id'] != $quiz_id ) ) { 
	if ( learndash_get_post_type_slug( 'quiz' ) !== $typenow ) {
		?>
		<p class="widget_quiz_return">
			<?php esc_html_e( 'Return to', 'learndash' ); ?> <a href='<?php echo get_edit_post_link( $quiz_id ); ?>'><?php echo get_the_title( $quiz_id); ?></a>
		</p>
		<?php
	}
	?></div><?php
	if ( ( isset( $widget['show_widget_wrapper'] ) ) && ( $widget['show_widget_wrapper'] == 'true' ) ) { ?>
		</div> <!-- Closing <div id='course_navigation'> -->
	<?php } ?>
	<?php
}

