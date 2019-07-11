<?php
if ( ! defined( 'LEARNDASH_SETTINGS_SECTION_TYPE' ) ) {
	define( 'LEARNDASH_SETTINGS_SECTION_TYPE', 'metabox' );
}

require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-pages.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-sections.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-metaboxes.php' );

// Common metaboxes shown in the page sidebar.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-side-submit.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-side-quick-links.php' );

// Custom Labels Page.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-custom-labels.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-custom-labels.php' );

// Course Options.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-courses-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-taxonomies.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-cpt.php' );

// Course Builder.
if ( ( defined( 'LEARNDASH_COURSE_BUILDER' ) ) && ( LEARNDASH_COURSE_BUILDER === true ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-builder.php' );

	// New Course Builder tab.
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-courses-builder-single.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-courses-builder-single.php' );
}

// Course Shortcodes tab.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-courses-shortcodes.php' );

// Lessons Options.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-lessons-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-lessons-display-order.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-lessons-taxonomies.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-lessons-cpt.php' );

// Topics Options.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-topics-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-topics-taxonomies.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-topics-cpt.php' );

// Quiz Options.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-quizzes-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-taxonomies.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-cpt.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-builder.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-time-formats.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-template-management.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-admin-email.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-user-email.php' );

if ( ( defined( 'LEARNDASH_QUIZ_BUILDER' ) ) && ( LEARNDASH_QUIZ_BUILDER === true ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-builder.php' );

	// New Quiz Builder tab.
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-quizzes-builder-single.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-quizzes-builder-single.php' );
}
// Question Options.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-questions-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-questions-taxonomies.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-questions-cpt.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-questions-template-management.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-questions-category-management.php' );


// Certificates Shortcodes.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-certificate-shortcodes.php' );

// Add-ons Page.
if ( ( defined( 'LEARNDASH_ADDONS_UPDATER' ) ) && ( LEARNDASH_ADDONS_UPDATER === true ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-addons.php' );
}

// Settings General tab.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-general.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-general-admin-user.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-general-per-page.php' );

if ( ( defined( 'LEARNDASH_REST_API_ENABLED' ) ) && ( true === LEARNDASH_REST_API_ENABLED ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-general-rest-api.php' );
}

// Data Upgrades tab.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-data-upgrades.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-data-upgrades.php' );

// PayPal tab.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-paypal.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-paypal.php' );

// Translations tab.
if ( ( defined( 'LEARNDASH_TRANSLATIONS' ) ) && ( LEARNDASH_TRANSLATIONS === true ) ) {
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-translations.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-translations-refresh.php' );
	require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-translations-learndash.php' );
}

// Import/Export.
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-import-export.php' );

// Assignments
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections-pages/class-ld-settings-page-assignments-options.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-assignments-cpt.php' );


//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-page-license.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-license.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-license-submit.php' );

//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-taxonomies.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-general-one.php' );
//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/class-ld-settings-section-general-two.php' );

// Shows settings section on the WP Settings > Permalinks page.
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-permalinks.php' );
require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-sections/class-ld-settings-section-permalinks-taxonomies.php' );


//require_once( LEARNDASH_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-section-metabox-quiz.php' );