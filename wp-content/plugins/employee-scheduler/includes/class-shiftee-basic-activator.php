<?php

/**
 * Fired during plugin activation
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Basic_Activator {

	/**
	 * Activation actions.
	 *
	 * Check for compatible WordPress and PHP, create user roles.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {
		global $wp_version;

		$php = '5.3';
		$wp  = '4.0';

		if ( version_compare( PHP_VERSION, $php, '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
			wp_die(
				'<p>' .
				sprintf(
					__( 'This plugin cannot be activated because it requires a PHP version greater than %1$s. Your PHP version can be updated by your hosting company.', 'employee-scheduler' ),
					$php
				)
				. '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'Go back', 'employee-scheduler' ) . '</a>'
			);
		}

		if ( version_compare( $wp_version, $wp, '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
			wp_die(
				'<p>' .
				sprintf(
					__( 'This plugin cannot be activated because it requires a WordPress version greater than %1$s. Please go to Dashboard &rarr; Updates to update to the latest version of WordPress .', 'employee-scheduler' ),
					$php
				)
				. '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'Go back', 'employee-scheduler' ) . '</a>'
			);
		}

		add_role( 'employee', __( 'Shiftee Staff', 'employee-scheduler' ), array( 'read' => true, 'edit_posts' => false, 'publish_posts' => false ) );
		// @todo - if you already have the plugin, the Employee user role name doesn't change.  Also, role should be called 'shiftee_employee' not 'employee'
		add_role( 'former-employee', __( 'Shiftee Former Staff', 'employee_scheduler' ), array( 'read' => false, 'edit_posts' => false, 'publish_posts' => false ) );
	}

}
