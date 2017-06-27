<?php

/**
 * Single Shift view
 *
 * Display all the information on the single shift view.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 */
?>

<?php echo $this->maybe_clock_in( get_the_id() ); ?>

<?php echo $this->maybe_clock_out( get_the_id() ); ?>

<?php $employee = $this->helper->get_shift_connection( get_the_id(), 'employee', 'name' );
if( $employee && '' !== $employee ) { ?>
	<p>
		<strong><?php echo apply_filters( 'shiftee_employee_label', __( 'Staff', 'employee-scheduler' ) ); ?></strong>:
		<?php echo esc_html( $employee ); ?>
	</p>
<?php } ?>

<?php $job = $this->helper->get_shift_connection( get_the_id(), 'job', 'name' );
if( $job && '' !== $job ) { ?>
	<p>
		<strong><?php echo apply_filters( 'shiftee_job_label', __( 'Job', 'employee-scheduler' ) ); ?></strong>:
		<?php echo esc_html( $job ); ?>
	</p>
<?php } ?>

<p>
	<strong><?php _e( 'When', 'employee-scheduler' ); ?></strong>:
	<?php echo esc_html( $this->helper->show_shift_date_and_time( get_the_id(), 'scheduled' ) ); ?>
</p>

<?php if( '' !== get_post_meta( get_the_id(), '_shiftee_clock_in', true ) && '' !== get_post_meta( get_the_id(), '_shiftee_clock_out', true ) ) { ?>
	<p>
		<strong><?php _e( 'Hours Worked', 'employee-scheduler' ); ?></strong>:
		<?php echo esc_html( $this->helper->show_shift_date_and_time( get_the_id(), 'worked' ) ); ?>
	</p>
<?php } ?>

<?php echo $this->helper->display_shift_terms( 'location' ); ?>

<?php echo $this->helper->display_shift_terms( 'shift_type' ); ?>

<?php echo $this->helper->display_shift_terms( 'shift_status' ); ?>

<?php echo $this->display_shift_notes(); ?>

<?php $this->display_shift_note_form(); ?>


