<?php
/**
 * Activity Report Download Header
 */
?>
<button class="button button-primary download-activity" data-template="activity-courses" data-nonce="<?php echo wp_create_nonce( 'learndash-data-reports-user-courses-'. get_current_user_id() ); ?>" data-slug="user-courses" type="button" title="<?php printf( _x( 'Export %s Data', 'Export Course Data', 'ld_propanel' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?>"><span class="dashicons dashicons-download"></span> <?php printf( _x( '%s', 'Course', 'ld_propanel' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?><span class="status"></span></button> 
<button class="button button-primary download-activity" data-template="activity-quizzes" data-nonce="<?php echo wp_create_nonce( 'learndash-data-reports-user-quizzes-'. get_current_user_id() ); ?>" data-slug="user-quizzes" type="button" title="<?php printf( _x( 'Export %s Data', 'Export Quiz Data', 'ld_propanel' ), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?>"><span class="dashicons dashicons-download"></span> <?php printf( _x( '%s', 'Quiz', 'ld_propanel' ), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?><span class="status"></span></button> 
