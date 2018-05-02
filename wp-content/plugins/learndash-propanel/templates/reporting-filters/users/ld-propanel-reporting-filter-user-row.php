<?php
/**
 * Rows of Courses for a selected User
 */
?>
<?php
if ( current_user_can( 'edit_user', $activity->user_id ) ) { 
	$user_link = get_edit_user_link( $activity->user_id ) ."#ld_course_info";
} else {
	$user_link = "#";
}

if ( current_user_can( 'edit_courses', $activity->post_id ) ) { 
	$post_link = get_edit_post_link( $activity->post_id ) ."#ld_course_info";
} else {
	$post_link = "#";
}

switch ( $header_key ) {
	case 'course_id':
		echo $activity->post_id;
		break;
		
		case 'course':
			?><strong title="<?php echo sprintf( _x( '%s ID:', 'Course ID:', 'ld_propanel' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?> <?php echo $activity->post_id; ?>" class="display-name"><?php echo esc_html( $activity->post_title ); ?></strong><?php
			break;
			
		case 'progress':
			?>
			<div class="progress-bar" title="<?php echo sprintf( __("%d of %d steps completed", 'ld_propanel'), LearnDash_ProPanel_Activity::get_activity_steps_completed( $activity ), LearnDash_ProPanel_Activity::get_activity_steps_total( $activity ) ) ?>">
				<?php 
				if ( is_null( $activity->activity_status ) ) {
					$progress_percent = 0;
					$progress_label = __('Not Started', 'ld_propanel' );
				} else if ( $activity->activity_status == false ) {
					$progress_percent = round( 100 * ( intval( LearnDash_ProPanel_Activity::get_activity_steps_completed( $activity ) ) / intval( LearnDash_ProPanel_Activity::get_activity_steps_total( $activity ) ) ) );
					$progress_label = $progress_percent .'%';
				} else if ( $activity->activity_status == true ) { 
					$progress_percent = 100;
					$progress_label = $progress_percent.'%';
				}
				?>
				<span class="actual-progress" style="width: <?php echo $progress_percent; ?>%;"></span>
			</div>	
			<strong class="progress-amount"><?php echo $progress_label; ?></strong>
		<?php 
		break;
	
	case 'last_update':
		echo learndash_adjust_date_time_display( intval($activity->activity_completed) ); 
		break;
}