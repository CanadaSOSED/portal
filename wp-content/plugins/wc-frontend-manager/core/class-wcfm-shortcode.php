<?php
/**
 * WCFM plugin core
 *
 * Plugin shortcode
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM_Shortcode {

	public $list_product;

	public function __construct() {
		// WC Frontend Manager Shortcode
		add_shortcode('wc_frontend_manager', array(&$this, 'wc_frontend_manager'));
	}

	public function wc_frontend_manager($attr) {
		global $WCFM;
		$this->load_class('wc-frontend-manager');
		return $this->shortcode_wrapper(array('WCFM_Frontend_Manager_Shortcode', 'output'));
	}

	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	public function load_class($class_name = '') {
		global $WCFM;
		if ('' != $class_name && '' != $WCFM->token) {
			require_once ( $WCFM->plugin_path . 'includes/shortcodes/class-' . esc_attr($WCFM->token) . '-shortcode-' . esc_attr($class_name) . '.php' );
		}
	}

}
?>