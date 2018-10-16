<?php

if ( !class_exists( 'Afd_Admin_Master' ) ) :

final class Afd_Admin_Master
{

	private $ready_do = false;
	private $hook     = false;

	public function __construct()
	{
		
		global $Afd;
		
		add_action( $Afd->ltd . '_after_init' , array( $this , 'init' ) , 0 );

		add_action( $Afd->ltd . '_before_admin_init' , array( $this , 'before_init' ) , 0 );
		add_action( $Afd->ltd . '_before_not_admin_init' , array( $this , 'before_init' ) , 0 );
		
		add_action( $Afd->ltd . '_admin_init' , array( $this , 'admin_screen' ) , 20 );
		add_action( $Afd->ltd . '_not_admin_init' , array( $this , 'admin_screen' ) , 20 );
		
		add_action( $Afd->ltd . '_after_init' , array( $this , 'admin_ajax' ) , 20 );
		
	}
	
	public function init()
	{
		
		global $Afd;

		if( !$Afd->Env->is_admin ) {

			return false;
			
		}
		
		$this->setup_includes();

		$this->ready_do_check();
		
		if( $this->ready_do ) {
			
			$this->hook = 'admin_init';
			
		} else {
			
			$this->hook = 'not_admin_init';

		}
		
		do_action( $Afd->ltd . '_before_' . $this->hook );

	}
	
	private function setup_includes()
	{
		
		global $Afd;

		$includes = array(
			'admin/setup.php',
		);
		
		$Afd->Helper->includes( $includes );

	}
	
	private function ready_do_check()
	{
		
		global $Afd;

		$includes = array(
			'admin/ready_do.php',
		);
		
		$Afd->Helper->includes( $includes );
		
		$Ready_Do = new Afd_Admin_Ready_Do();
		
		if( $Ready_Do->is_ready_do() )
			$this->ready_do = true;

	}
	
	public function before_init()
	{
		
		$this->admin_includes();
		
		add_action( 'admin_init' , array( $this , 'regist_init_action' ) , 20 );
		
	}
	
	private function admin_includes()
	{
		
		global $Afd;

		if( $this->hook == 'admin_init' ) {
			
			$includes = array(
				'admin/abstract-manager.php',
				'admin/manager-announce-settng.php',
				'admin/show-announce.php',
			);
			
		} elseif( $this->hook == 'not_admin_init' ) {
			
			$includes = array();

		}

		$Afd->Helper->includes( $includes );
		
	}
	
	public function regist_init_action()
	{
		
		global $Afd;

		do_action( $Afd->ltd . '_' . $this->hook );
		
	}
	
	public function admin_screen()
	{
		
		global $plugin_page;
		global $Afd;

		if( $Afd->Env->is_ajax ) {

			return false;
			
		}

		if( empty( $plugin_page ) or strpos( $plugin_page , $Afd->main_slug ) === false ) {

			return false;
			
		}
		
		do_action( $Afd->ltd . '_admin_screen' );
		
	}

	public function admin_ajax()
	{
		
		global $Afd;

		if( $Afd->Env->is_ajax ) {

			do_action( $Afd->ltd . '_admin_ajax' );
			
		}
		
	}

}

new Afd_Admin_Master();

endif;
