<?php
if ( !class_exists( 'Learndash_Admin_Data_Upgrades_Translations' ) ) {
	class Learndash_Admin_Data_Upgrades_Translations extends Learndash_Admin_Settings_Data_Upgrades {
		
		public static $instance = null;

		function __construct() {
			self::$instance =& $this;
			
			add_action( 'init', array( $this, 'upgrade_translations') );
		}
		
		public static function getInstance() {
		    if ( ! isset( self::$_instance ) ) {
		        self::$_instance = new self();
		    }
		    return self::$_instance;
		}
		
		/**
		 * Update the LearnDash Translations 
		 *
		 * Checks to see if settings needs to be updated. 
		 * @since 2.3
		 * 
		 * @param  none
		 * @param  none
		 */
		function upgrade_translations() {
					
			if ( is_admin() ) {
				$translations_installed = $this->get_data_settings( 'translations_installed' );
				if ( ( defined( 'LEARNDASH_ACTIVATED' ) && LEARNDASH_ACTIVATED ) || ( !$translations_installed ) ) {
					$this->download_translations( );
					$this->set_data_settings( 'translations_installed', time() );
				}
			}
		}
		
		function download_translations(  ) {
			$wp_installed_languages = get_available_languages();
			if ( !in_array( 'en_US', $wp_installed_languages ) ) {
				$wp_installed_languages = array_merge( array( 'en_US' ), $wp_installed_languages );
			}
			
			if ( !empty( $wp_installed_languages ) ) {
				
				LearnDash_Translations::get_available_translations( 'learndash', true );
				
				foreach( $wp_installed_languages as $locale ) {
					$reply = LearnDash_Translations::install_translation( 'learndash', $locale );
				}
			}
		}
		
		// end of functions
	}
}
