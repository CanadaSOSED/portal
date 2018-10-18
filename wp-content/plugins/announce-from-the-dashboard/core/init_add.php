<?php

if ( !class_exists( 'Afd_Init_Add' ) ) :

final class Afd_Init_Add
{

    public function __construct()
	{

		global $Afd;
		
		$this->include_models();

		add_action( $Afd->ltd . '_init' , array( $this , 'init' ) , 20 );
		
    }
	
	private function include_models()
	{

		global $Afd;

		$includes = array(
			'model/abstract-record.php',
			'model/announces.php',
		);
		
		$Afd->Helper->includes( $includes );

	}
	
	public function init()
	{
		
		$this->setup_manager();
		$this->setup_links();
		
	}
	
	private function setup_manager()
	{
		
		global $Afd;
		
		$capability = $Afd->Plugin->capability;
		
		if( $Afd->Site->is_multisite ) {
			
			$capability = 'manage_network';
			
		}
		
		$Afd->Plugin->capability = apply_filters( $Afd->ltd . '_capability_manager' , $capability );
		
		if( current_user_can( $Afd->Plugin->capability ) ) {

			$Afd->Plugin->is_manager = true;
			
		}

	}
	
	private function setup_links()
	{
		
		global $Afd;
		
		$Afd->Links->author  = 'http://gqevu6bsiz.chicappa.jp/';
		$Afd->Links->forum   = 'http://wordpress.org/support/plugin/' . $Afd->plugin_slug;
		$Afd->Links->review  = 'http://wordpress.org/support/view/plugin-reviews/' . $Afd->plugin_slug;
		$Afd->Links->profile = 'http://profiles.wordpress.org/gqevu6bsiz';
		
		if( $Afd->Site->is_multisite ) {

			$Afd->Links->setting = network_admin_url( 'admin.php?page=' . $Afd->main_slug );

		} else {

			$Afd->Links->setting = admin_url( 'options-general.php?page=' . $Afd->main_slug );

		}

	}

}

new Afd_Init_Add();

endif;
