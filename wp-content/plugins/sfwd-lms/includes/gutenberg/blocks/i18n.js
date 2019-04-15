/**
 * LearnDash i18n Functions
 * 
 * This is a collection of common functions used within the LeanDash blocks
 * 
 * @since 2.5.9
 * @package LearnDash
 */

 if (typeof ldlms_settings['locale'] !== 'undefined') {
	wp.i18n.setLocaleData( ldlms_settings['locale'], 'learndash');
} else {
	wp.i18n.setLocaleData({ '': {} }, 'learndash');
}
