<?php

if ( !class_exists( 'Afd_Admin_Abstract_Manager' ) ) :

abstract class Afd_Admin_Abstract_Manager
{

	protected $view_dir;
	protected $elements_dir;
	protected $assets_url;
	protected $script_slug;
	protected $errors;

	protected $name;
	protected $do_screen_slug;
	protected $menu_title;
	protected $MainModel;

	public function __construct()
	{
		
		global $Afd;

		$this->view_dir     = $Afd->plugin_dir . trailingslashit( basename( dirname( __FILE__ ) ) ) . trailingslashit( 'view' );
		$this->elements_dir = $this->view_dir . trailingslashit( 'elements' );
		$this->assets_url   = $Afd->plugin_url . trailingslashit( basename( dirname( __FILE__ ) ) ) . trailingslashit( 'assets' );
		$this->script_slug  = $Afd->main_slug . '_manager';
		$this->errors       = new WP_Error();
		
		if( $Afd->Plugin->is_manager ) {

			add_action( $Afd->ltd . '_before_admin_init' , array( $this , 'before_init' ) );
			add_action( $Afd->ltd . '_admin_screen' , array( $this , 'admin_screen' ) );
			add_action( $Afd->ltd . '_admin_screen_' . $this->do_screen_slug , array( $this , 'admin_current_screen' ) );
			add_action( $Afd->ltd . '_admin_ajax' , array( $this , 'admin_ajax' ) );
			
		}

	}
	
	public function before_init()
	{
		
		global $Afd;
		
		if( $Afd->Site->is_multisite ) {
			
			add_action( 'network_admin_menu' , array( $this , 'admin_menu' ) );

		} else {

			add_action( 'admin_menu' , array( $this , 'admin_menu' ) );
			
		}
		
	}

	public function admin_menu() {}

	public function admin_screen()
	{
		
		global $plugin_page;
		global $Afd;

		$this->check_post_data();

		if( !empty( $plugin_page ) && $plugin_page == $this->do_screen_slug ) {

			do_action( $Afd->ltd . '_admin_screen_' . $this->do_screen_slug );
			
		}

	}
	
	private function check_post_data()
	{
		
		global $Afd;

		if( empty( $_POST ) )
			return false;
		
		if( !$Afd->Helper->is_correctly_form( $_POST ) )
			return false;

		if( !$Afd->Plugin->is_manager )
			return false;
		
		$this->post_data();

	}
	
	protected function post_data() {}

	public function admin_current_screen()
	{

		global $Afd;
		
		if( $Afd->Site->is_multisite ) {
			
			add_action( 'network_admin_notices' , array( $this , 'update_notices' ) );
			add_action( 'network_admin_notices' , array( $this , 'error_notices' ) );

		} else {
			
			add_action( 'admin_notices' , array( $this , 'update_notices' ) );
			add_action( 'admin_notices' , array( $this , 'error_notices' ) );

		}
		add_action( 'admin_enqueue_scripts' , array( $this , 'admin_enqueue_scripts' ) );

	}
	
	public function update_notices()
	{
		
		global $Afd;
		
		if( empty( $_GET ) or empty( $_GET[$Afd->Plugin->msg_notice] ) )
			return false;
		
		$update_notice = strip_tags( $_GET[$Afd->Plugin->msg_notice] );
		
		if( $update_notice == 'update_' . $this->name or $update_notice == 'remove_' . $this->name ) {

			printf( '<div class="updated"><p><strong>%s</strong></p></div>' , __( 'Settings saved.' ) );
			
		}

	}
	
	public function error_notices()
	{
		
		if( empty( $this->errors ) )
			return false;
		
		$error_codes = $this->errors->get_error_codes();
		
		if( empty( $error_codes ) )
			return false;

		echo '<div class="error">';
			
		foreach ( $error_codes as $code ) {
					
			printf( '<p title="error_%1$s">%2$s</p>' , $code , $this->errors->get_error_message( $code ) );
					
		}
			
		echo '</div>';

	}
	
	public function admin_enqueue_scripts()
	{
		
		global $Afd;
		
		$include_files = array( 'jquery' , 'jquery-ui-sortable' , 'jquery-ui-datepicker' );

		wp_enqueue_script( $this->script_slug ,  $this->assets_url . 'js/manager.js' , $include_files , $Afd->ver );
		wp_enqueue_style( 'jquery-style' , 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css' );
		//wp_enqueue_style( $this->script_slug ,  $this->assets_url . 'css/manager.css' , array() , $Afd->ver );

	}
	
	public function admin_ajax() {}
	
}

endif;
