<?php

/**
 * Master Schedule
 *
 * Display the master schedule.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 */

?>

<h3><?php printf( __( 'Schedule for %s through %s', 'employee-scheduler' ), esc_attr( $this->terminal_date( $week, 'start' ) ), esc_attr( $this->terminal_date( $week, 'end' ) ) ); ?></h3>

<?php if( 'on' == $nav ) {
	$this->schedule_nav();
} ?>

<table id="shiftee-master-schedule" <?php echo $class; ?>>
	<thead>
		<tr>
			<?php foreach( $week as $day => $shifts ) { ?>
				<th data-sort="string" class="shiftee-header-row">
					<span>
						<?php echo date( 'l', $day ) . ', ' .  $this->helper->display_datetime( $day, 'date' ); ?>
					</span>
				</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach( $employeearray as $employee ) { ?>
		<tr>
			<th colspan="7" class="shiftee-staff-row">
				<?php echo $this->employee_information( $employee ); ?>
			</th>
        </tr>
        <tr>
			<?php
			foreach( $week as $day => $shifts ) {
			    $has_shift = false;
				$i = 0; ?>
				<td>
					<?php foreach( $shifts as $shift ) {
						if( isset( $shift['employee'] ) && $employee == $shift['employee'] ) {
						    $has_shift = true;
							ob_start();
							include 'shortcode-master-schedule-single-shift.php';
							$single_shift = ob_get_clean();
							echo apply_filters( 'shiftee_master_schedule_shift', $single_shift, 10, $shift['id'] );
						}
					}

					if( !$has_shift ) {
					  _e( 'No shifts', 'employee-scheduler' );
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
