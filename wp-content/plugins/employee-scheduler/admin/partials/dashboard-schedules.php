<?php

/**
 * View Schedules
 *
 * Admin page to view schedules
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin/partials
 */
?>

<div class="wrap">

	<h1><?php _e( 'View Staff Schedules', 'employee-scheduler' ); ?></h1>

	<p><?php _e( 'You can use this page to view schedules for one or all staff.  To display the schedule on your website, create a page with the <code>[master_schedule]</code> shortcode.', 'employee-scheduler' ); ?></p>

	<form method='post' action='<?php echo admin_url( 'edit.php?post_type=shift&page=view-schedules'); ?>' id='view-schedule'>
		<table class="form-table cmb2-element">
			<tr>
				<th scope="row"><?php _e( 'Staff', 'employee-scheduler' ) ?>:</th>
				<td>
					<select name="employee">
						<?php // @todo - if Pro is installed, this needs to get managers - probably ought to have a filter
						echo $this->helper->make_employee_dropdown_options(); ?>
					</select>
					<p><?php _e( 'Leave this blank to see the master schedule for all staff.', 'employee-scheduler' ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Week starting on', 'employee-scheduler' ) ?>:</th>
				<td>
					<input type="text" size="10" name="thisdate" id="thisdate" class="shiftee-date-picker" value="<?php if( isset( $_POST['thisdate'] ) ) { echo $_POST['thisdate']; } ?>" />
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'View Schedule', 'employee-scheduler' ); ?>" />
		</p>
	</form>

	<?php if( $_POST ) {
		$reportstart = $_POST['thisdate'];
		$reportend = date( 'Y-m-d', strtotime( '+6 days', strtotime( $reportstart ) ) );

		if( '' == $_POST['employee'] ) {
			echo do_shortcode( '[master_schedule begin="' . $reportstart . '" end="' . $reportend . '"]' );
		} else {
			echo do_shortcode( '[your_schedule begin="' . $reportstart . '" end="' . $reportend . '" employee="' . sanitize_text_field( $_POST['employee'] ) . '"]' );
		}
	} ?>

</div>
