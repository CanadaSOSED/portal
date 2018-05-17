<?php
define( 'LEARNDASH_SETTINGS_SECTION_TYPE', 'metabox' );

require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-pages.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-sections.php' );

require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-custom-labels.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-custom-labels.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-custom-labels-submit.php' );

require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-courses-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-options-submit.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-taxonomies.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-cpt.php' );

if ( ( defined( 'LEARNDASH_COURSE_BUILDER' ) ) && ( LEARNDASH_COURSE_BUILDER === true ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-builder.php' );
	
	// New Course Builder tab
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-courses-builder-single.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-builder-single.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-builder-single-submit.php' );
	
}

require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-courses-shortcodes.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-shortcodes-submit.php' );


require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-lessons-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-lessons-options-submit.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-lessons-display-order.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-lessons-taxonomies.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-lessons-cpt.php' );

require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-topics-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-topics-options-submit.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-topics-taxonomies.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-topics-cpt.php' );


require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-certificate-shortcodes.php' );

if ( ( defined( 'LEARNDASH_ADDONS_UPDATER' ) ) && ( LEARNDASH_ADDONS_UPDATER === true ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-addons.php' );
}

require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-general.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-general-submit.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-general-admin-user.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-general-per-page.php' );


require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-paypal.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-paypal.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-paypal-submit.php' );


if ( ( defined( 'LEARNDASH_TRANSLATIONS' ) ) && ( LEARNDASH_TRANSLATIONS === true ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-translations.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-translations-refresh.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-translations-learndash.php' );
}

//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-page-license.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-license.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-license-submit.php' );

//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-taxonomies.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-general-one.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-general-two.php' );

// Shows settings section on the WP Settings > Permalinks page
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-permalinks.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-permalinks-taxonoies.php' );
