<?php

/**
 * This file bootstraps things needed by the class.
 *
 * Typically these are things that need to happen early enough that we cannot
 * rely on doing it when the class is instantiated. Examples:
 *     * Registering translation domains
 *     * Output buffering so we can drop any generated HTML
 */

if ( ! defined( 'WLTE_BUFFERING_ACTIVE' ) && !empty( $_GET['wlte_export'] ) ) {
	ob_start();
	define( 'WLTE_BUFFERING_ACTIVE', true );
}
