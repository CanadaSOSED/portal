<?php

/**
 * Shiftee Upgrades
 *
 * Page where the latest upgrades are explained and run
 *
 * @link       http://ran.ge
 * @since      2.1.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin/partials
 */

$update = new Shiftee_Basic_Updater( 'employee-scheduler', '2.1.0' );
?>

<div class="wrap">

	<h1><?php _e( 'Shiftee Upgrades', 'employee-scheduler' ); ?></h1>

	<?php // Version 2.1.0 - upgrade meta data
	if( isset( $_GET['shiftee-upgrade'] ) && 'upgrade_shift_meta' == $_GET['shiftee-upgrade'] ) { ?>

		<p><?php _e( 'This is a very important update for Shiftee.  After this update, you will have access to new features, including:', 'employee-scheduler' ); ?>
			<ul class="shiftee-bullets">
				<li><?php _e( 'Overnight shifts', 'employee-scheduler' ); ?></li>
				<li><?php _e( 'Display dates in the format of your choice', 'employee-scheduler' ); ?></li>
				<li><?php _e( 'Improved user interface', 'employee-scheduler' ); ?></li>
				<li><?php _e( 'You no longer have to enter dates in 24-hour format', 'employee-scheduler' ); ?></li>
			</ul>
		</p>

		<p><?php printf( __( 'If you have questions or problems regarding this update, check out our <a href="%s">Upgrade FAQ</a>.', 'employee-scheduler' ), 'https://shiftee.co/support/faq-shiftee-basic-2-1-0-upgrade/' ); ?></p>

		<p style="font-size: 1.5em;"><strong><?php _e( 'Please back up your site before running this update!', 'employee-scheduler' ); ?></strong></p>

		<form id="shiftee-upgrade-shift-meta" action="<?php admin_url( 'index.php?page=shiftee-upgrades&shiftee-upgrade=upgrade_shift_meta' ); ?>" method="post">
			<?php wp_nonce_field( 'shiftee_upgrade_shift_meta', 'shiftee_upgrade_shift_meta_nonce' ); ?>

			<?php $shifts = get_posts( 'post_type=shift&posts_per_page=-1' );
			$number_of_shifts = count( $shifts );
			$expenses = get_post( 'post_type=expense&posts_per_page=-1' );
			$number_of_expenses = count( $expenses );
			$number = $number_of_expenses + $number_of_shifts; ?>
			<input type="hidden" name="shift-count" id="shift-count" value="<?php echo $number; ?>">

			<?php $current_step = $this->options['shiftee_meta_update_last_step'];
			if( !isset( $current_step ) || '0' == $current_step ) {
				$working_on = '1-100';
			} else {
				$next_step = intval( $current_step ) + 100;
				$working_on = $current_step . '-' . $next_step;
			} ?>
			<input type="hidden" name="working-on" id="working-on" value="<?php echo $working_on; ?>">

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Upgrade Now' ) ?>" name="shiftee-upgrade-shift-meta" />
			</p>
		</form>

		<p id="upgrade_shift_meta_results"></p>

	<?php } ?>

</div>