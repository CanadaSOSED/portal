<?php
if ( !class_exists( 'Learndash_Admin_Data_Upgrades_Group_Leader_Role' ) ) {
	class Learndash_Admin_Data_Upgrades_Group_Leader_Role extends Learndash_Admin_Settings_Data_Upgrades {
		
		public static $instance = null;

		function __construct() {
			self::$instance =& $this;
			
			add_action( 'init', array( $this, 'create_group_leader_role') );
		}
		
		public static function getInstance() {
		    if ( ! isset( self::$_instance ) ) {
		        self::$_instance = new self();
		    }
		    return self::$_instance;
		}
		
		/**
		 * Create Group Leader Role
		 *
		 * Checks to see if settings needs to be updated. 
		 * @since 2.5.6
		 * 
		 * @param  none
		 * @param  none
		 */
		function create_group_leader_role() {
					
			if ( is_admin() ) {
				$gl_role_created = $this->get_data_settings( 'gl_role' );
				if ( ( defined( 'LEARNDASH_ACTIVATED' ) && LEARNDASH_ACTIVATED ) || ( !$gl_role_created ) ) {
					
					learndash_add_group_admin_role();
					
					$this->set_data_settings( '$gl_role_created', time() );
				}
			}
		}
				
		// end of functions
	}
}
