<?php

if ( !class_exists( 'Afd_Third_Party' ) ) :

final class Afd_Third_Party
{

	private $ThirdParty;

    public function __construct()
	{

		global $Afd;
		
		$this->ThirdParty = new stdClass;
		
		add_action( $Afd->ltd . '_init' , array( $this , 'init' ) , 9 );
		
    }
	
	public function init()
	{
		
		$this->define_constants();
		$this->includes();
		
	}
	
	private function define_constants()
	{
		
		global $Afd;

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		$check_plugins = array(
		);
		
		if( empty( $check_plugins ) )
			return false;
			
		$plugins = array();

		foreach( $check_plugins as $name => $base_name ) {
			
			if( is_plugin_active( $base_name ) ) {
				
				$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $base_name );
				$plugins[$name] = (object) array( 'ver' => $plugin_data['Version'] );
				
			}

		}
		
		if( !empty( $plugins ) ) {

			$this->ThirdParty = (object) $plugins;
			$Afd->ThirdParty = $this->ThirdParty;
			
		}
		
	}

	private function includes()
	{

		global $Afd;

	}

}

new Afd_Third_Party();

endif;
