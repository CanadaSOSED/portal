<?php
/**
 * Utility class to contain all the custom databases used within LearnDash.
 *
 * @since 2.6.0
 * @package LearnDash
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LDLMS_DB' ) ) {
	/**
	 * Class to create the instance.
	 */
	class LDLMS_DB {

		/**
		 * Collection of all tables by section.
		 *
		 * @var array $table_sections.
		 */
		private static $tables = array(
			'activity'  => array(
				'user_activity'      => 'user_activity',
				'user_activity_meta' => 'user_activity_meta',
			),
			'wpproquiz' => array(
				'quiz_category'      => 'category',
				'quiz_form'          => 'form',
				'quiz_lock'          => 'lock',
				'quiz_master'        => 'master',
				'quiz_prerequisite'  => 'prerequisite',
				'quiz_question'      => 'question',
				'quiz_statistic'     => 'statistic',
				'quiz_statistic_ref' => 'statistic_ref',
				'quiz_template'      => 'template',
				'quiz_toplist'       => 'toplist',
			),
		);

		/**
		 * Public constructor for class
		 *
		 * @since 2.6.0
		 */
		public function __construct() {
		}

		/**
		 * Public Initialize function for class
		 *
		 * @since 2.6.0
		 */
		public static function init() {
			/**
			 * We really only need to build the full table names once. So
			 * we use a static flag to control the processing.
			 */
			static $init_called = false;

			if ( true !== $init_called ) {
				$init_called = true;

				/**
				 * Fitler the list of custom database tables.
				 *
				 * @since 2.6.0
				 */
				self::$tables = apply_filters( 'learndash_custom_database_tables', self::$tables );

				if ( ! empty( self::$tables ) ) {
					foreach ( self::$tables as $section_key  => $section_tables ) {
						if ( ( ! empty( $section_tables ) ) && ( is_array( $section_tables ) ) ) {
							foreach ( $section_tables as $table_key => $table_name ) {
								self::$tables[ $section_key ][ $table_key ] = self::get_table_prefix( $section_key ) . $table_name;
							}
						}
					}
				}

				//error_log('tables<pre>'. print_r(self::$tables, true) .'</pre>');
			}
		}

		/**
		 * Get an array of all custom tables.
		 *
		 * @since 2.6.0
		 *
		 * @param string  $table_section Table section prefix.
		 * @param boolean $return_sections Default false returns flat array. True to return table names array with sections.
		 *
		 * @return array of table names.
		 */
		public static function get_tables( $table_section = '', $return_sections = false ) {
			global $wpdb;

			$tables_return = array();

			self::init();

			if ( ! empty( $table_section ) ) {
				if ( isset( self::$tables[ $table_section ] ) ) {
					if ( true === $return_sections ) {
						$tables_return[ $table_section ] = self::$tables[ $table_section ];
					} else {
						$tables_return = self::$tables[ $table_section ];
					}
				}
			} else {
				if ( true === $return_sections ) {
					$tables_return = self::$table;
				} else {
					foreach ( self::$tables as $section_key  => $section_tables ) {
						$tables_return = array_merge( $tables_return, $section_tables );
					}
				}
			}

			return $tables_return;
		}

		/**
		 * Get the WPProQuiz table name prefix. This is appended to the
		 * default WP prefix.
		 *
		 * @since 2.6.0
		 *
		 * @param string $table_section Table section prefix.
		 * @return string table prefix.
		 */
		public static function get_table_prefix( $table_section = '' ) {
			global $wpdb;

			switch ( $table_section ) {

				case 'wpproquiz':
					//require_once( LEARNDASH_LMS_PLUGIN_DIR .'includes/admin/class-learndash-admin-data-upgrades.php' );
					//$ld_admin_data_upgrades = Learndash_Admin_Data_Upgrades::get_instance();
					//$data_settings = $ld_admin_data_upgrades->get_data_settings( 'rename-wpproquiz-tables' );
					//error_log('wpproquizz - data_settings<pre>'. print_r($data_settings, true) .'</pre>');

					$table_prefix = $wpdb->prefix . 'wp_pro_quiz_';
					break;

				//case 'wpproquiz_new':
				//	$table_prefix = $wpdb->prefix . 'learndash_quiz_';
				//	break;

				case 'activity':
				//case 'learndash':
					$table_prefix = $wpdb->prefix . 'learndash_';
					break;

				default:
					$table_prefix = $wpdb->prefix;
					break;
			}

			return $table_prefix;
		}

		/**
		 * Utility function to return the table name. This is to prevent hard-coding
		 * of the table names throughout the code files.
		 *
		 * @since 2.6.0
		 *
		 * @param string $table_name Name of table to return full table name.
		 * @param string $table_section Table section prefix.
		 * @return string Table Name if found.
		 */
		public static function get_table_name( $table_name = '', $table_section = '' ) {
			global $wpdb;

			$tables = self::get_tables( $table_section );
			if ( isset( $tables[ $table_name ] ) ) {
				return $tables[ $table_name ];
			}
		}

		// End of functions.
	}
}

// These are the base table names WITHOUT the $wpdb->prefix.
global $learndash_db_tables;
$learndash_db_tables = LDLMS_DB::get_tables();
