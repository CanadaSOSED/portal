<?php

/**
 * Clock Out Button
 *
 * Display a clock out button.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 */
?>

<form method="post" action="<?php echo get_the_permalink(); ?>" id="clock">
	<input type="hidden" name="shift-id" value="<?php get_the_id(); ?>">
	<?php if( isset( $this->options['geolocation'] ) && 1 == $this->options['geolocation'] ) { // geolocation field, if we're using it ?>
		<input type="hidden" id="latitude" name="latitude" value="">
		<input type="hidden" id="longitude" name="longitude" value="">
	<?php } ?>
	<?php wp_nonce_field( 'shiftee_clock_out', 'shiftee_clock_out_nonce' ); ?>
	<input type="submit" name="shiftee-clock-out-form" value="<?php _e( 'Clock Out', 'employee-scheduler' ); ?>" id="clock-in">
</form>
