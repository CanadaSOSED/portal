<?php

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


/**
 * Uninstall this plugin
 *
 */


flush_rewrite_rules(false);

/** Delete plugin options */
// do not delete 'epkb_version' so we know whether this is a new install
// TODO if user explicitly specifies: delete_option( 'epkb_version' );
// TODO if user explicitly specifies: delete_option( 'epkb_config_' );
// TODO if user explicitly specifies: delete_option( 'epkb_categories_icons' );
// epkb_categories_sequence, epkb_articles_sequence

delete_option( 'epkb_error_log' );
delete_option( 'epkb_flush_rewrite_rules' );
