<?php

/**
 * Master Schedule single shift
 *
 * Display one shift on the master schedule.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 */
?>

<div class="<?php echo $this->shift_classes( $shift ); ?>" <?php echo $this->shift_style( $shift ); ?>>
	<span class="shiftee-job">
		<?php if( isset( $shift['job'] ) ) { ?>
			<a href="<?php echo esc_url( $this->job_link( $shift ) ); ?>"><?php echo esc_html( $shift['job'] ); ?></a>
		<?php } else {
			echo esc_html( get_the_title( $shift['id'] ) );
		} ?>
	</span>
	<span class="shiftee-time">
		<?php echo $this->helper->show_shift_date_and_time( $shift['id'] ); ?>
	</span>
	<?php if( isset( $shift['location'] ) ) { ?>
		<span class="shiftee-location">
            <?php echo __( 'Location', 'employee-scheduler' ) . ': ' . esc_html( $shift['location_name'] ); ?>
        </span>
	<?php } ?>
	<a class="shiftee-details" href="<?php echo esc_url( $this->shift_link( $shift['id'] ) ); ?>"><?php _e( 'View Shift Details', 'employee-scheduler' ); ?></a>
</div>
<?php $i++;
if( $this->count_unassigned( $shifts ) > 3 && 'Unassigned' == $employee && 3 == $i ) { ?>
	<button class="shiftee-show-more-unassigned"><?php _e( 'More unassigned shifts >', 'employee-scheduler' ); ?></button>
	<div class="shiftee-more-unassigned" style="display: none;">
<?php }
if( $i == $this->count_unassigned( $shifts ) ) { ?>
	</div>
<?php }


