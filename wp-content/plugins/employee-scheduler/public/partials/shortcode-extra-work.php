<?php

/**
 * Extra Work shortcode
 *
 * Display a form where users can record work they do outside of scheduled shifts.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 */
?>

<p><?php _e( 'Use this form to record work you do outside of your scheduled shifts.', 'employee-scheduler' ); ?></p>

<form method="post" action="<?php the_permalink(); ?>" id="shiftee-extra-work">
	<p>
		<label><?php _e( 'Start', 'employee-scheduler' ); ?></label>
		<input type="text" name="shiftee-start" id="shiftee-start" class="shiftee-datetime-picker" required />
	</p>
	<p>
		<label><?php _e( 'End', 'employee-scheduler' ); ?></label>
		<input type="text" name="shiftee-end" id="shiftee-end" class="shiftee-datetime-picker" required />
	</p>
	<?php echo $this->extra_type_dropdown(); ?>
	<?php echo $this->job_dropdown(); ?>
	<?php echo $this->location_dropdown(); ?>
	<p>
		<label><?php _e( 'Description', 'employee-scheduler' ); ?></label>
		<textarea name="shiftee-description" id="shiftee-description"></textarea>
	</p>
	<?php wp_nonce_field( 'shiftee_extra_work', 'shiftee_extra_work_nonce' ); ?>
	<input type="submit" value="<?php _e( 'Record Work', 'employee-scheduler' ); ?>" name="shiftee-extra-work">
</form>