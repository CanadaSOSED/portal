<?php

if ( !class_exists( 'Afd_Init' ) ) :

final class Afd_Init
{

	private $framework_ver = '1.3.1';

    public function __construct()
	{

		global $Afd;
		
		add_action( $Afd->ltd . '_plugins_loaded' , array( $this , 'plugins_loaded' ) , 0 );
		add_action( $Afd->ltd . '_init' , array( $this , 'init' ) , 0 );
		
    }
	
	public function plugins_loaded()
	{
		
		global $Afd;
		
		$this->setup_Plugin();
		$this->setup_Form();
		$this->setup_Site();
		$this->setup_Env();
		
	}

	private function setup_Plugin()
	{
		
		global $Afd;

		$Afd->Plugin->url_admin_network = network_admin_url( 'admin.php?page=' . $Afd->main_slug );
		$Afd->Plugin->url_admin         = admin_url( 'admin.php?page=' . $Afd->main_slug );
		$Afd->Plugin->capability        = 'manage_options';
		$Afd->Plugin->is_manager        = false;
		
		$Afd->Plugin->msg_notice        = sprintf( '%s_msg' , $Afd->ltd );

	}

	private function setup_Form()
	{
		
		global $Afd;

		$Afd->Form->UPFN  = 'Y';
		$Afd->Form->field = $Afd->ltd . '_settings';
		$Afd->Form->nonce = $Afd->ltd . '_';

	}

	private function setup_Site()
	{
		
		global $Afd;

		$Afd->Site->is_multisite = is_multisite();
		$Afd->Site->blog_id = get_current_blog_id();
		$Afd->Site->main_blog = is_main_site();

	}

	private function setup_Env()
	{
		
		global $Afd;

		$Afd->Env->is_admin         = is_admin();
		$Afd->Env->is_network_admin = is_network_admin();
		$Afd->Env->is_ajax          = false;
		$Afd->Env->login_action     = false;

		if( defined( 'DOING_AJAX' ) )
			$Afd->Env->is_ajax = true;
			
		if( !strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) === false )
			$Afd->Env->login_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';

		$Afd->Env->schema = is_ssl() ? 'https://' : 'http://';

	}
	
	public function init()
	{
		
		$this->setup_User();
		
	}
	
	private function setup_User()
	{
		
		global $Afd;

		$Afd->User->user_login = is_user_logged_in();
		$Afd->User->user_role  = false;
		$Afd->User->user_id    = false;
		$Afd->User->superadmin = false;

		if( !$Afd->User->user_login )
			return false;

		$Afd->User->user_id = get_current_user_id();

		$User = wp_get_current_user();
	
		if( !empty( $User->roles ) ) {
	
			$user_roles = $User->roles;

			foreach( $user_roles as $role ) {
	
				$Afd->User->user_role = $role;
				break;
	
			}
	
		}

		if( $Afd->Site->is_multisite )
			$Afd->User->superadmin = is_super_admin();

	}

}

new Afd_Init();

endif;
