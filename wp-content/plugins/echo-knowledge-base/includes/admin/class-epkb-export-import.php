<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle import and export of KB configuration
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Export_Import {
	
	private $message = array(); // error/warning/success messages
	//private $operation_log = array();
	private $add_ons_info = array(
										'Echo_Knowledge_Base' => 'epkb',
										'Echo_Advanced_Search' => 'asea',
										'Echo_Article_Rating_And_Feedback' => 'eprf', 
										'Echo_Elegant_Layouts' => 'elay',
										'Echo_Widgets' => 'widg',
										// FUTURE DODO Links Editor and MKB
							);

	private $ignored_fields = array('id', 'status', 'kb_main_pages', 'kb_name', 'kb_articles_common_path','categories_in_url_enabled','wpml_is_enabled');

	/**
	 * Run export
	 * @param $kb_id
	 * return text message about error or stop script and show export file
	 * @return String|array
	 */
	public function download_export_file( $kb_id ) {

		// export data and report error if an issue found
		$exported_data = $this->export_kb_config( $kb_id );
		if ( empty($exported_data) ) {
			return $this->message;
		}

		ignore_user_abort( true );
		
		if ( ! $this->is_function_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
			set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=kb_config_export_' . date('Y_m_d_H_i_s') . '.json' );
		header( "Expires: 0" );

		echo json_encode($exported_data);

		return '';
	}
	
	/**
	 * Export KB configuration.
	 *
	 * @param $kb_id
	 * @return null
	 */
	private function export_kb_config( $kb_id ) {
		
		$export_data = array();

		// process each plugin (KB core and add-ons)
		foreach ($this->add_ons_info as $add_on_class => $add_on_prefix) {

			if ( ! class_exists($add_on_class) ) {
				continue;
			}

			// retrieve plugin instance
			/** @var $plugin_instance Echo_Knowledge_Base */
			$plugin_instance = $this->get_plugin_instance( $add_on_prefix );
			if ( empty($plugin_instance) ) {
				return null;
			}

			// retrieve plugin configuration
			$add_on_config = $plugin_instance->kb_config_obj->get_kb_config( $kb_id );
			if ( is_wp_error($add_on_config) ) {
				$this->message['error'] = $add_on_config->get_error_message();
				return null;
			}
			if ( ! is_array($add_on_config) ) {
				$this->message['error'] = __( 'Found invalid data.', 'echo-knowledge-base' ) . ' (' . $add_on_prefix . ')';
				return null;
			}

			// remove protected fields
			foreach( $this->ignored_fields as $ignored_field ) {
				if ( isset($add_on_config[$ignored_field]) )  {
					unset($add_on_config[$ignored_field]);
				}
			}
			
			$export_data[$add_on_prefix] = $add_on_config;
		}

		if ( empty($export_data) ) {
			$this->message['error'] = 'E40'; // do not translate;
			return null;
		}

		return $export_data;
	}

	/**
	 * Import KB configuration from a file.
	 *
	 * @param $kb_id
	 * @return array|null
	 */
	public function import_kb_config( $kb_id ) {

		$import_file_name = $_FILES['import_file']['tmp_name'];
		if ( empty($import_file_name) ) {
			$this->message['error'] = __( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (0)';
			return $this->message;
		}

		// retrieve content of the imported file
		$import_data_file = file_get_contents($import_file_name);
		if ( empty($import_data_file) ) {
			$this->message['error'] = __( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (1)';
			return $this->message;
		}

		// validate imported data
		$import_data = json_decode($import_data_file, true);
		if ( empty($import_data) || ! is_array($import_data) ) {
			$this->message['error'] = __( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (2)';
			return $this->message;
		}

		// KB Core needs to be present
		if ( ! isset($import_data['epkb']) ) {
			$this->message['error'] = __( 'Knowledge Base data is missing', 'echo-knowledge-base' );
			return $this->message;
		}

		// process each plugin (KB core and add-ons)
		foreach ($this->add_ons_info as $add_on_class => $add_on_prefix) {

			$plugin_name = $this->get_plugin_name( $add_on_class );
			
			// add-on is not installed and not data present in import for the add-on
			if ( empty($import_data[$add_on_prefix]) && ! class_exists($add_on_class) ) {
				continue;
			}
			
			// import data exists but plugin is not active
			if ( isset($import_data[$add_on_prefix]) && ! class_exists($add_on_class) ) {
				$this->message['error'] = __( 'Import Failed because found import data for a plugin that is not active: ', 'echo-knowledge-base' ) . $plugin_name;
				return $this->message;
			}

			// plugin is active but import data does not exist
			if ( ! isset($import_data[$add_on_prefix]) && class_exists($add_on_class) ) {
				$this->message['error'] = __( 'Import Failed because Found plugin that is active with no corresponding import data: ', 'echo-knowledge-base' ) . $plugin_name;
				return $this->message;
			}

			// ensure imported data have correct format
			if ( ! is_array($import_data[$add_on_prefix]) ) {
				$this->message['error'] = __( 'Import Failed because found invalid data.', 'echo-knowledge-base' ) . ' (' . $plugin_name . ')';
				return $this->message;
			}

			// verify most data is preset
			$specs_class_name = strtoupper($add_on_prefix) . '_KB_Config_Specs';
			if ( ! class_exists($specs_class_name) || ! method_exists( $specs_class_name, 'get_specs_item_names') ) {
				$this->message['error'] = 'E34 (' . $plugin_name . ')'; // do not translate
				return $this->message;
			}

			$add_on_config = $import_data[$add_on_prefix];

			/** @var $specs_class_name EPKB_KB_Config_Specs */
			$specs_found = 0;
			$specs_not_found = 0;
			$fields_specification = $specs_class_name::get_specs_item_names();
			foreach( $fields_specification as $key ) {
				if ( isset($add_on_config[$key]) ) {
					$specs_found++;
				} else {
					$specs_not_found++;
				}
			}

			// validate imported data
			if ( $specs_found == 0 || $specs_not_found > $specs_found ) {
				$this->message['error'] = __( "Found invalid data.", 'echo-knowledge-base' ) . ' (' . $plugin_name . ',' . $specs_found . ',' . $specs_not_found . ')';
				return $this->message;
			}

			// retrieve plugin instance
			/** @var $plugin_instance Echo_Knowledge_Base */
			$plugin_instance = $this->get_plugin_instance( $add_on_prefix );
			if ( empty($plugin_instance) ) {
				return null;
			}

			// for KB Core, Main and Article Page could have ELAY layout so we need ELAY enabled
			if ( $add_on_prefix == 'epkb' ) {

				if ( ! in_array( $add_on_config['kb_main_page_layout'], EPKB_KB_Config_Layouts::get_main_page_layout_names() ) ) {
					$this->message['error'] = __( "Elegant Layouts needs to be active.", 'echo-knowledge-base' ) . ' (' . $add_on_config['kb_main_page_layout'] . ')';

					return $this->message;
				}

				if ( ! in_array( $add_on_config['kb_article_page_layout'], EPKB_KB_Config_Layouts::get_main_page_layout_names() ) ) {
					$this->message['error'] = __( "Elegant Layouts needs to be active.", 'echo-knowledge-base' ) . ' (' . $add_on_config['kb_article_page_layout'] . ')';

					return $this->message;
				}
			}

			// remove protected fields
			foreach( $this->ignored_fields as $ignored_field ) {
				if ( isset($add_on_config[$ignored_field]) )  {
					unset($add_on_config[$ignored_field]);
				}
			}
			
			$orig_config = $plugin_instance->kb_config_obj->get_kb_config( $kb_id );
			$add_on_config = array_merge( $orig_config, $add_on_config);
			
			// update add-on configuration
			$add_on_config = $plugin_instance->kb_config_obj->update_kb_configuration( $kb_id, $add_on_config );
			/** @var $add_on_config WP_Error */
			if ( is_wp_error($add_on_config) ) {
				$this->message['error'] =  'E36 (' . $plugin_name . ')' . $add_on_config->get_error_message();  // do not translate
				return $this->message;
			}

			//$this->operation_log[] = 'Import completed for plugin ' . $plugin_name;
		}

		//$this->operation_log[] = 'Import finished successfully';
		$this->message['success'] =  __( 'Import finished successfully', 'echo-knowledge-base' );
		
		return $this->message;
	}

	/**
	 * Call function to get/save add_on configuration
	 * @param $prefix
	 * @return null on error (and set error message) or valid DB object
	 */
	private function get_plugin_instance( $prefix ) {

		if ( ! in_array( $prefix, $this->add_ons_info ) ) {
			$this->message['error'] = 'E37 (' . $prefix . ')'; // do not translate
			return null;
		}

		// get function
		$add_on_function_name = $prefix . '_get_instance';
		if ( ! function_exists($add_on_function_name) ) {
			$this->message['error'] = 'E38 (' . $add_on_function_name . ')'; // do not translate
			return null;
		}

		// get DB class instance
		$instance = call_user_func($add_on_function_name);
		if ( is_object($instance) ) {
			return $instance;
		}
		
		$this->message['error'] = 'E39 (' . $instance . ')'; // do not translate

		return null;
	}

	private function get_plugin_name( $add_on_class_name ) {
		return str_replace('_', ' ', $add_on_class_name);
	}

	/**
	 * Checks whether function is disabled.
	 * @param $function
	 * @return bool
	 */
	private function is_function_disabled( $function ) {
		$disabled = explode( ',',  ini_get( 'disable_functions' ) );
		return in_array( $function, $disabled );
	}
}