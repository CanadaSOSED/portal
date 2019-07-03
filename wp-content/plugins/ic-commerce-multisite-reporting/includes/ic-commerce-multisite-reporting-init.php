<?php
if(!class_exists('IC_Commerce_Mutlisite_Reporting_Init')){
	
	/*
	 * Class Name IC_Commerce_Mutlisite_Reporting_Init 
	*/ 
    class IC_Commerce_Mutlisite_Reporting_Init extends IC_Commerce_Mutlisite_Reporting_Functions{
		
		/* variable declaration*/
		var $constants = array();
		
		/*
		 * Function Name __construct
		 *
		 * Initialize Class Default Settings, Assigned Variables
		 *
		 * @param $constants (array) settings
		*/
		function __construct($constants = array()){
			$this->constants = $constants;
			$ajax_action = $this->constants['ajax_action'];			
			add_action('network_admin_menu', 			array($this, 'admin_menu'));
			add_action('admin_menu', 					array($this, 'admin_menu'));
			add_action('wp_before_admin_bar_render', 	array($this, 'wp_before_admin_bar_render'));			
			add_action('wp_ajax_'.$ajax_action,  	 	array($this, 'call_ajax_action'));
			add_action('admin_enqueue_scripts',  		array($this, 'admin_enqueue_scripts'));	
			add_action('admin_init', 					array($this, 'admin_init'));		
		}
		
		/*
			* Function Name call_ajax_action
			*
		*/		
		function call_ajax_action(){
			global $wpdb;
			
			$sub_action = isset($_REQUEST["sub_action"]) ? $_REQUEST["sub_action"] : '';
			if($sub_action == 'dashboard_data'){
				
				include_once("ic-commerce-multisite-reporting-dashboard.php");
				$obj = new IC_Commerce_Mutlisite_Reporting_Dashboard($this->constants);
				
				$return = $obj->get_dashboard_data($this->constants);				
				echo json_encode($return);
				die;
				return '';
				
				
				
				$user = wp_get_current_user();
				$user_id = $user->ID;			
				$blogs = get_blogs_of_user($user_id, true);
				
				
				foreach($blogs as $blog){
					
					$userblog_id = $blog->userblog_id;
					if($userblog_id == 1){
						$this->constants['prefix'] = "{$base_prefix}";
					}else{
						$this->constants['prefix'] = "{$base_prefix}{$userblog_id}_";
					}				
					
					$dashboard_data = $obj->get_dashboard_data($this->constants);					
				}
				echo json_encode($dashboard_data);
				die;
			}
		}
		
		/*
			* Function Name admin_menu
			*
		*/
		function admin_menu(){
			$menu_slug = $this->constants['menu_slug'];
			$capability = $this->constants['capability'];
			$plugin_key = $this->constants['plugin_key'];
			
			add_menu_page(__('Mutltisite Reporing'), __('Multisite Reporing'), $capability, $menu_slug, array($this, 'add_page'), '', '58.95' );
			
			add_submenu_page($menu_slug,__( 'Dashboard','icwoocommerce_textdomains'),	__( 'Dashboard','icwoocommerce_textdomains'),$capability,$menu_slug,array( $this, 'add_page' ));
			
			add_submenu_page($menu_slug,__( 'Settings','icwoocommerce_textdomains'),	__( 'Settings', 	'icwoocommerce_textdomains'),$capability,$this->constants['plugin_key'].'_settings',		array( $this, 'add_page' ));
			
			//add_submenu_page($menu_slug,__( 'Details','icwoocommerce_textdomains'),	__( 'Details', 	'icwoocommerce_textdomains'),$capability,$this->constants['plugin_key'].'_details',		array( $this, 'add_page' ));
			
			$this->plugin_submenu_list(array(),$menu_slug);
		}
		
		/*
			* Function Name plugin_submenu_list
			*
			* @param array $admin_pages
			*
			* @param string $parent_menu
			*
			* return $admin_pages
		*/
		function plugin_submenu_list($admin_pages = array(),$parent_menu = 'product-centre'){
			global $submenu;
			
			$submenu_list = isset($submenu[$parent_menu]) ? $submenu[$parent_menu] : array();
			
			foreach($submenu_list as $key => $menu_list){
				$admin_pages[] = isset($menu_list[2]) ? $menu_list[2] : '';
			}
						
			$this->constants['plugin_submenu'] = $admin_pages;
			
			return $admin_pages;
		}
		
		/*
			* Function Name admin_enqueue_scripts
		*/
		function admin_enqueue_scripts(){
			global $wp_scripts;
			
			$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : '';
			
			$admin_pages = isset($this->constants['plugin_submenu']) ? $this->constants['plugin_submenu'] : array();
			
			if(!in_array($page,$admin_pages)){
				return false;
			}
			
			$ajax_action = $this->constants['ajax_action'];
			
			$this->constants['plugin_url'] = $asset_url = plugins_url('/assets/',$this->constants['__FILE__']);
						
			switch ($page) {
				case "multi_site_reporting":
				case $this->constants['plugin_key'].'_details':
				
					if(function_exists('get_woocommerce_currency_symbol')){
						$currency_symbol	=	get_woocommerce_currency_symbol();
					}else{
						$currency_symbol	=	"$";
					}
				
					wp_enqueue_style('normalize_styles', 								$asset_url.'css/normalize.css');
					wp_enqueue_style('admin_styles', 									$asset_url.'css/admin.css');
					wp_enqueue_style('fontawesome_styles', 								$asset_url.'css/lib/font-awesome.min.css');	
					wp_enqueue_style( $this->constants['plugin_key'].'_googlefont',	'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700');
									
					wp_enqueue_script('icmsr_script', 		$asset_url.'js/scripts.js', array('jquery') );
					wp_enqueue_script('ic-jquery-blockui',	$asset_url.'js/jquery.blockUI.js');
					
					wp_localize_script('icmsr_script','icmsr_ajax_object',
						array(
						'ajax_url'			=>	admin_url('admin-ajax.php'),
						'admin_url'			=>	admin_url("admin.php"),
						'admin_page'		=>	$page,
						'ajax_action'		=>	$ajax_action,
						'currency_symbol' 	=>	$currency_symbol
						)
					);					
					
					wp_enqueue_script('script-ic-amcharts',  $asset_url.'js/amcharts/amcharts.js');
					//wp_enqueue_script('script-ic-light',     $asset_url.'js/amcharts/light.js');
					//wp_enqueue_script('script-ic-serial',    $asset_url.'js/amcharts/serial.js');										
					wp_enqueue_script('script-ic-pie',    	 $asset_url.'js/amcharts/pie.js');
					wp_enqueue_script('ic-jquery-blockui',	 $asset_url.'js/jquery.blockUI.js');
					
					
					wp_enqueue_script('jquery-ui-datepicker');					
					$ui = $wp_scripts->query('jquery-ui-core');
					$protocol = is_ssl() ? 'https' : 'http';
					$url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
					wp_register_style('jquery-ui-smoothness', $url, false, null);
					wp_enqueue_style( 'jquery-ui-smoothness'); 
					
					break;
				case "icmsreporting_settings":
					wp_enqueue_style('normalize_styles', 								$asset_url.'css/normalize.css');
					wp_enqueue_style('admin_styles', 									$asset_url.'css/admin.css');
					break;					
				case "icwcprocentre_dashboard":
					wp_enqueue_script('jquery-ui-datepicker');					
					$ui = $wp_scripts->query('jquery-ui-core');
					$protocol = is_ssl() ? 'https' : 'http';
					$url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
					wp_register_style('jquery-ui-smoothness', $url, false, null);
					wp_enqueue_style( 'jquery-ui-smoothness'); 
					break;
				
			}
			
		}
		
		/*
			* Function Name add_page
		*/
		function add_page(){
			global $wpdb;
			$current_page	= $this->get_request('page',NULL,false);
			$title			= NULL;
			$intence		= NULL;
			
			switch($current_page){
				case 'multi_site_reporting':
					$title = __('Dashboard','icwoocommerce_textdomains');
					include_once("ic-commerce-multisite-reporting-dashboard.php");
					$intence = new IC_Commerce_Mutlisite_Reporting_Dashboard($this->constants);
					break;
				case 'icmsreporting_settings':
					$title = __('Settings','icwoocommerce_textdomains');
					include_once("ic-commerce-multisite-reporting-settings.php");
					$intence = new IC_Commerce_Mutlisite_Reporting_Settings($this->constants);
					break;
				case $this->constants['plugin_key'].'_details':
					$title = __('Details','icwoocommerce_textdomains');
					include_once("ic-commerce-multisite-reporting-details.php");
					$intence = new IC_Commerce_Mutlisite_Reporting_Details($this->constants);
					break;
				default:
						//include_once('ic_commerce_ultimate_report_dashboard.php');
						//$intence = new IC_Commerce_Ultimate_Woocommerce_Report_Dashboard($c);
						break;
			}
			
			
			/*include_once("ic-commerce-multisite-reporting-dashboard.php");
			$obj = new IC_Commerce_Mutlisite_Reporting_Dashboard($this->constants);
			
			include_once("ic-commerce-multisite-reporting-details.php");
			$obj = new IC_Commerce_Mutlisite_Reporting_Details($this->constants);*/
			
			
			echo "<div class=\"wrap ic_plugin_wrap\"><div class=\"container-liquid\">";
			//$obj->init();
			if($intence) $intence->init();
			echo "</div></div>";
			
			
			return false;
			
			$user = wp_get_current_user();
			$user_id = $user->ID;			
			$blogs = get_blogs_of_user($user_id, true);
			
			
			foreach($blogs as $blog){
				
				$userblog_id = $blog->userblog_id;
				if($userblog_id == 1){
					$this->constants['prefix'] = "{$base_prefix}";
				}else{
					$this->constants['prefix'] = "{$base_prefix}{$userblog_id}_";
				}				
				
				$dashboard_data = $obj->get_dashboard_data($this->constants);
				$this->print_array($dashboard_data);
			}
		}
		
		/*
			* Function Name wp_before_admin_bar_render
		*/
		function wp_before_admin_bar_render(){
			global $wp_admin_bar;
			if(!function_exists('switch_to_blog')){
				return true;
			}
			if(isset($wp_admin_bar->user->blogs)){
				foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
					$blog_id = $blog->userblog_id;					
					
					switch_to_blog( $blog_id);				
					
					$parent  	= 'blog-' . $blog_id;
					$menu_id  	= 'menu-' . $blog_id;
					
					if (current_user_can('manage_woocommerce')){
						$wp_admin_bar->add_node(array(
							'parent'=> $parent,
							'id'    => $menu_id,
							'title' => __('Multi-Site Reporting'),
							'href'  => admin_url('admin.php?page=multi_site_reporting')
						));						
						$this->constants['blogs'][$blog_id] = $blog;
					}
					restore_current_blog();
					
					if(!isset($this->constants['blogs'])){
						$this->constants['blogs'] = array();
					}
				}
			}
		}
		
		/*
			* Function Name admin_init
			*
		*/
		function admin_init(){
			$icmsreporting_setting   = $this->get_request("icmsreporting_setting");	
			if($icmsreporting_setting == 'icmsreporting_setting'){
				include_once("ic-commerce-multisite-reporting-settings.php");
				$intence = new IC_Commerce_Mutlisite_Reporting_Settings($this->constants);
				$intence->save_settings();
			}
		}
		
    }
}