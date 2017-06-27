<?php

/**
 * About Shiftee
 *
 * Page announcing the name change and sharing information about Shiftee
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin/partials
 */


if( is_plugin_active( 'shiftee/shiftee.php' ) ) {
    $shiftee = true;
} else {
    $shiftee = false;
}
?>

<div class="wrap" id="about-shiftee">

    <h1><?php _e( 'Shiftee', 'employee-scheduler' ); ?></h1>

    <h2 class="now-shiftee"><?php _e( 'Welcome to Shiftee!', 'employee-scheduler' ); ?></h2>

    <p><?php printf( __( 'We\'re delighted to have you on board!  Check out the <a href="%s">Instructions</a> page in the Shiftee menu to get started.  Our website at <a href="%s">shiftee.co has more documentation</a> if you need it.', 'employee-scheduler' ), admin_url( 'admin.php?page=instructions' ), 'http://shiftee.co/docs' ); ?></p>

    <h3><?php _e( 'Looking for support?', 'employee-scheduler' ); ?></h3>

    <?php if( $shiftee ) { ?>
        <p><?php printf( __( '<a href="%s">Contact our support team</a>, and we\'ll help you out right away!', 'employee-scheduler' ), 'https://shiftee.co/contact/' ); ?></p>
    <?php } else { ?>
        <p><?php printf( __( 'We can answer your questions on the <a href="%s">WordPress support forum</a>!', 'employee-scheduler' ), 'https://wordpress.org/support/plugin/employee-scheduler/' ); ?></p>
    <?php } ?>

    <?php if( !$shiftee ) { ?>
        <h3><?php _e( 'Shiftee can do more for you!', 'employee-scheduler' ); ?></h3>
        <p><?php _e( 'Shiftee has more features to make it easier to manage your staff!', 'employee-scheduler' ); ?></p>
        <ul>
            <li><?php _e( 'Bulk shift creator/editor', 'employee-scheduler' ); ?></li>
            <li><?php _e( 'Automatically check for scheduling conflicts', 'employee-scheduler' ); ?></li>
            <li><?php _e( 'Payroll reports', 'employee-scheduler' ); ?></li>
            <li><?php _e( 'Filter shifts and expenses', 'employee-scheduler' ); ?></li>
            <li><?php _e( 'Compare hours scheduled to hours worked', 'employee-scheduler' ); ?></li>
            <li><?php _e( 'Manager user role', 'employee-scheduler' ); ?></li>
            <li><?php _e( 'Personalized priority support.', 'employee-scheduler' ); ?></li>
        </ul>

        <p><a href="https://shiftee.co/downloads/shiftee/" target="_blank" class="button button-primary">
			    <?php _e( 'Upgrade to Shiftee', 'employee-scheduler' ); ?>
            </a>
        </p>
    <?php } ?>

    <p><?php printf( __( '<strong>We are planning on adding a lot of fantastic new features to Shiftee</strong>, including a mobile app!  To stay in the loop and be the first to hear about upcoming updates, you can sign up for our newsletter at <a href="%s">https://shiftee.co</a>.', 'employee-scheduler' ), 'https://shiftee.co' ); ?></p>

    <p><?php _e( 'Meanwhile, happy scheduling!', 'employee-scheduler' ); ?></p>

    <p class="made-by"><?php printf( __( 'Made by <a href="%s">Range</a>', 'employee-scheduler' ), 'https://ran.ge' ); ?></p>


</div>
