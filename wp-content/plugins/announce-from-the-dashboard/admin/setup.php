<?php

if ( !class_exists( 'Afd_Admin_Setup' ) ) :

final class Afd_Admin_Setup
{

	private $base_plugin;
	private $assets_url;
	private $script_slug;

	public function __construct()
	{
		
		global $Afd;
		
		$this->base_plugin = trailingslashit( $Afd->plugin_slug ) . $Afd->plugin_slug . '.php';
		$this->assets_url  = $Afd->plugin_url . trailingslashit( basename( dirname( __FILE__ ) ) ) . trailingslashit( 'assets' );
		$this->script_slug = $Afd->main_slug . '-setup';
		
		add_action( $Afd->ltd . '_after_init' , array( $this , 'init' ) , 20 );
		add_action( $Afd->ltd . '_before_admin_init' , array( $this , 'before_admin_init' ) , 20 );
		add_action( $Afd->ltd . '_before_not_admin_init' , array( $this , 'before_not_admin_init' ) );

	}
	
	public function init()
	{
		
		global $Afd;

		if( !$Afd->Env->is_admin ) {

			return false;
			
		}
		
		if( !$Afd->Env->is_ajax ) {
			
			$this->do_screen();
			
		} else {
			
			$this->do_ajax();
			
		}

	}
	
	public function before_admin_init()
	{
		
		global $Afd;

		if( !$Afd->Env->is_admin ) {

			return false;
			
		}
		
		if( !$Afd->Env->is_ajax ) {
			
			$this->do_before_admin_init();
			
		}

	}
	
	private function do_screen()
	{
		
		global $Afd;
		
		if( $Afd->Site->is_multisite ) {
				
			add_filter( 'network_admin_plugin_action_links_' . $this->base_plugin , array( $this , 'plugin_action_links' ) );
				
		} else {
				
			add_filter( 'plugin_action_links_' . $this->base_plugin , array( $this , 'plugin_action_links' ) );

		}

		add_filter( 'plugin_row_meta' , array( $this , 'plugin_row_meta' ) , 10 , 2 );
		add_action( 'admin_enqueue_scripts' , array( $this , 'admin_enqueue_scripts' ) );

	}
	
	public function plugin_action_links( $links )
	{
		
		global $Afd;

		$setting = sprintf( '<a href="%1$s">%2$s</a>' , $Afd->Links->setting , __( 'Settings' ) );

		array_unshift( $links , $setting );

		return $links;
		
	}
	
	public function plugin_row_meta( $links , $file )
	{
		
		global $Afd;

		if ( strpos( $file , $this->base_plugin ) !== false ) {
			
			$links[] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>' , $Afd->Links->forum , __( 'Support Forums' ) );

		}
		
		return $links;

	}
	
	public function admin_enqueue_scripts()
	{
		
		global $Afd;
		
		//wp_enqueue_style( $this->script_slug ,  $this->assets_url . 'css/setup.css', array() , $Afd->ver );

	}
	
	private function do_before_admin_init()
	{
		
		global $Afd;
		
	}
	
	public function before_not_admin_init()
	{
		
		global $Afd;

	}
	
	private function do_ajax() {}

}

new Afd_Admin_Setup();

endif;
