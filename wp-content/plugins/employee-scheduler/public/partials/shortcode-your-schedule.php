<?php

/**
 * Your Schedule
 *
 * Display one employee's schedule.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 */
?>

<?php if( is_admin() && '' !== $employee ) { ?>
    <h3>
        <?php
            $employee = get_user_by( 'id', $employee );
            $employee_name = $employee->display_name;
            printf( __( 'Schedule for %s through %s for %s', 'employee-scheduler' ),
            esc_attr( $this->terminal_date( $week, 'start' ) ),
            esc_attr( $this->terminal_date( $week, 'end' ) ) ,
            $employee_name
        ); ?>
    </h3>
<?php } else { ?>
    <h3><?php printf( __( 'Schedule for %s through %s', 'employee-scheduler' ), esc_attr( $this->terminal_date( $week, 'start' ) ), esc_attr( $this->terminal_date( $week, 'end' ) ) ); ?></h3>
<?php } ?>


<?php if( 'on' == $nav ) {
	$this->schedule_nav();
} ?>

<table id="shiftee-your-schedule" <?php echo $class; ?>>
	<thead>
		<tr>
			<?php foreach( $week as $day => $shifts ) { ?>
				<th class="shiftee-header-row">
					<?php echo $this->helper->display_datetime( $day, 'date' ); ?>
				</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $job_array as $job ) { ?>
			<tr>
				<th colspan="7" class="shiftee-staff-row">
					<?php echo esc_html( $job ); ?>
				</th>
            </tr>
            <tr>
				<?php foreach( $week as $day => $shifts ) { ?>
					<td>
						<?php foreach( $shifts as $shift ) {
							if( isset( $shift['job'] ) && $job == $shift['job'] ) { ?>
								<div class="<?php echo $this->shift_classes( $shift ); ?>" <?php echo $this->shift_style( $shift['id'] ); ?>>
									<span class="shiftee-time"><?php echo $this->helper->show_shift_date_and_time( $shift['id'] ); ?></span>
									<span>
										<a class="shiftee-details" href="<?php echo esc_url( $this->shift_link( $shift['id'] ) ); ?>">
											<?php _e( 'View Shift Details', 'employee-scheduler' ); ?>
										</a>
									</span>
								</div>
							<?php }
						} ?>
					</td>
				<?php } ?>
			</tr>
		<?php } ?>
	</tbody>
</table>

<?php if( 'on' == $nav ) {
	$this->schedule_nav();
} ?>
