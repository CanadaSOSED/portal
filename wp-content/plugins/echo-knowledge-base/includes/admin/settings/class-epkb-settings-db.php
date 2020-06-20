<?php

/**
 * Manage plugin settings (plugin-wide configuration and license keys) in the database.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Settings_DB {

	// Prefix for WP option name that stores settings
	const EPKB_SETTINGS_NAME =  'epkb_settings';
	private $cached_settings = array();
	private $default_settings = array();

	/**
	 * Get settings from the WP Options table.
	 * If settings are missing then insert into database defaults.
	 *
	 * @return array return current settings; if not found return defaults
	 */
	public function get_settings() {
		/** @var $wpdb $Wpdb */
		global $wpdb;

		// retrieve settings if already cached
		if ( ! empty($this->cached_settings) ) {
			$all_settings = wp_parse_args( $this->cached_settings, $this->default_settings );
			return $all_settings;
		}

		$this->default_settings = EPKB_Settings_Specs::get_default_settings();

		$settings = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '" . self::EPKB_SETTINGS_NAME . "'" );
		$settings = ( empty($settings) ) ? array() : maybe_unserialize( $settings );
		$settings = is_array($settings) ? $settings : array();

		// use defaults for missing fields
		$all_settings = wp_parse_args( $settings, $this->default_settings );

		// is settings missing in the database then add them
		if ( $settings != $all_settings ) {
			$this->save_settings( $all_settings );
		}

		$this->cached_settings = $all_settings;

		return $all_settings;
	}

	/**
	 * Return specific value from the plugin settings values. Values are automatically trimmed.
	 *
	 * @param $setting_name
	 *
	 * @param string $default
	 * @return string with value or empty string if this settings not found
	 */
	public function get_value( $setting_name, $default='' ) {
		if ( empty($setting_name) ) {
			return $default;
		}

		$plugin_settings = $this->get_settings();
		if ( isset($plugin_settings[$setting_name]) ) {
			return $plugin_settings[$setting_name];
		}

		return  isset($this->default_settings[$setting_name]) ? $this->default_settings[$setting_name] : $default;
	}

	/**
	 * Sanitize and validate input data. Then add or update SINGLE or MULTIPLE settings. Does NOT override current settings if new value
	 * is not supplied.
	 *
	 * @param array $settings contains settings or empty if adding default settings
	 *
	 * @return true|WP_Error
	 */
	public function update_settings( array $settings=array() ) {

		// first sanitize and validate input
		$fields_specification = EPKB_Settings_Specs::get_fields_specification();
		$input_filter = new EPKB_Input_Filter();
		$settings = $input_filter->validate_and_sanitize_specs( $settings, $fields_specification );
		if ( is_wp_error($settings) ) {
			EPKB_Logging::add_log( 'Failed to sanitize settings', $settings );
			return $settings;
		}

		// merge new settings with current settings
		$settings = array_merge( $this->get_settings(), $settings );

		// save settings
		$result = $this->save_settings( $settings );
		if ( is_wp_error($result) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Save new settings into the database
	 *
	 * @param $settings
	 * @return array|WP_Error - return settings or WP_Error
	 */
	private function save_settings( $settings ) {
		global $wpdb;

		$serialized_settings = maybe_serialize($settings);
		if ( empty($serialized_settings) ) {
			EPKB_Logging::add_log( 'Failed to serialize settings' );
			return new WP_Error( 'save_settings', __( 'Failed to convert settings', 'echo-knowledge-base' ) );
		}

		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->options (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s)
 												 ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)",
												 self::EPKB_SETTINGS_NAME, $serialized_settings, 'no' ) );
		if ( $result === false ) {
			EPKB_Logging::add_log( 'Failed to save settings' );
			return new WP_Error( 'save_settings', __( 'Failed to save settings', 'echo-knowledge-base' ) );
		}

		// cached the settings for future use
		$this->cached_settings = $settings;

		return $settings;
	}
}