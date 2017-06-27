<?php
// ini_set( 'session.save_path', '.' );
global $wp_version;
if (version_compare($wp_version, "3.3", "<"))
{
	exit('This plugin requires WordPress 3.3 or newer, yours is ' . $wp_version);
}

if( ! function_exists('_print_r') )
{
	function _print_r($thing)
	{
		echo '<pre>';
		print_r( $thing );
		echo '</pre>';
	}
}

if( ! class_exists('hcWpPost') )
{
class hcWpPost {
	var $post_title;
	var $post_content;
	var $post_status;
	var $post_author;    /* author user id (optional) */
	var $post_name;      /* slug (optional) */
	var $post_type;      /* 'page' or 'post' (optional, defaults to 'post') */
	var $comment_status; /* open or closed for commenting (optional) */
	}
}

if( ! class_exists('hcWpBase5') )
{
class hcWpBase5
{
	protected $no_session = FALSE;

	var $app = '';
	var $slug = '';
	var $db_prefix = '';
	var $types = array();
	var $dir = '';
	var $_admin_styles = array();
	var $_admin_scripts = array();
	var $pages = array();
	var $page_param = '';

	var $require_shortcode = FALSE;
//	var $query_prefix = '?/'; // for CI based apps
	var $query_prefix = ''; // for NTS based apps

	var $hc_product = '';
	var $full_path = '';
	var $system_type = '';

	var $happ_path = '';
	var $happ_web_dir = '';
	var $deactivate_other = array();

	var $premium = NULL;
	var $wrap_output = array();

	var $happ_files_file = NULL;
	var $is_boss = FALSE;  // if boss is on so it does that without shortcode, used for example for ical and cron links
	var $hcs = 'hcs'; // get/post param to intercept
	var $hca = 'hca'; // get/post param to pass our action

	public function __construct( 
		$app,
		$full_path,
		$hc_product = '',
		$system_type = 'nts', // ci or nts
		$types = array(),
		$slug = '',
		$db_prefix = ''
		)
	{
		$this->wrap_output = array( '<!-- START OF NTS -->', '<!-- END OF NTS -->' );
		$this->system_type = $system_type;

		$GLOBALS['NTS_APPPATH'] = dirname($full_path) . '/application';
		$GLOBALS['NTS_IS_PLUGIN'] = 'wordpress';

		if( defined('NTS_DEVELOPMENT') ){
			$this->happ_path = NTS_DEVELOPMENT;
			$localhost = defined('NTS_LOCALHOST') ? NTS_LOCALHOST : 'localhost';
			$this->happ_web_dir = 'http://' . $localhost . '/wp/wp-content/plugins';
		}
		else {
			switch( $this->system_type ){
				case 'nts':
					$this->happ_path = dirname($full_path) . '/core6/happ';
					$this->happ_web_dir = plugins_url('core6', $full_path);
					break;
				case 'ci':
					$this->happ_path = dirname($full_path) . '/happ';
					$this->happ_web_dir = plugins_url('', $full_path);
					break;
			}
		}

		$dir = dirname( $full_path );
		$this->hc_product = $hc_product;
		$this->full_path = $full_path;

		$this->app = $app;
		$GLOBALS['NTS_APP'] = $app;

		$this->slug = $slug ? $slug : $this->app;

		$this->db_prefix = $db_prefix ? $db_prefix : $this->slug;
		$this->dir = $dir;
		$this->types = array();
		$this->page_param = 'page_id';

		reset( $types );
		foreach( $types as $t ){
			$full_type = $this->app . '-' . $t;
			$this->types[ $t ] = $full_type;
		}

		$file = $this->dir . '/' . $app . '.php';
		if( file_exists($file) ){
			register_activation_hook( $file, array($this, '_install') );
		}

		add_action(	'init',	array($this, '_init') );
		add_action( 'init', array($this, 'check_intercept') );

		add_action('user_register',			array($this, '_user_sync'), 10);
		add_action('added_existing_user',	array($this, '_user_sync'), 10);
		add_action('profile_update',		array($this, '_user_sync'), 10);
		add_action('deleted_user',			array($this, '_user_sync'), 10);
		add_action('remove_user_from_blog',	array($this, '_user_sync'), 10);
	}

	public function _user_sync( $user_id )
	{
		$this->intercept('init');

		$ci = ci_get_instance();
		if( ! $ci->db->table_exists('conf') ){
			// not setup yet
			return;
		}

		$wum = HC_App::model('wordpress_user');
		$result = $wum->sync( $user_id );
	}

	public function _continue_init()
	{
		$this->_admin_styles = array();
		$this->_admin_scripts = array();
		$this->_localize_scripts = array();

		$nts_is_wordpress = TRUE;

		if( ! $this->happ_files_file ){
			$this->happ_files_file = $this->happ_path . '/assets/files.php';
		}
		$ri = 'wordpress';
		require( $this->happ_files_file );

		reset( $css_files );
		foreach( $css_files as $f ){
			$this->register_admin_style($f);

		/* add wp overwriter */
/*
			if( substr($f, -strlen('/hc-hitcode.css')) == '/hc-hitcode.css' ){
				$f2 = str_replace( '/hc-hitcode.css', '/hc-hitcode-wp.css', $f );
				$this->register_admin_style($f2);
			}
*/
		}

		reset( $js_files );
		foreach( $js_files as $f ){
			$this->register_admin_script($f);
		}

		if( $this->is_me_admin() ){
			add_action( 'admin_enqueue_scripts',	array($this, 'admin_scripts') );
			add_action( 'admin_head', 				array($this, 'admin_head') );
		}

		if( $this->is_me_front() ){
			add_action( 'wp_enqueue_scripts',	array($this, 'admin_scripts') );
			add_action( 'wp_head', 				array($this, 'admin_head') );
		}

		add_action( 'save_post',				array($this, 'save_meta'));
		add_action( 'wp_logout',				array($this, 'logout'));

		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'wp', array($this, 'front_init') );

		add_shortcode( $this->app, array($this, 'front_view'));
		add_action( 'admin_menu', array($this, 'admin_menu') );

		$submenu = is_multisite() ? 'network_admin_menu' : 'admin_menu';
		add_action( $submenu, array($this, 'admin_submenu') );

	/* save a ref to the plugin object */
		$GLOBALS['NTS_CONFIG'][$this->app]['_SELF_'] = $this;

		// add_action('wp_ajax_' . $this->slug, array($this, 'ajax_action'));
		// add_action('wp_ajax_nopriv_' . $this->slug, array($this, 'ajax_action'));
	}

	function ajax_action()
	{
		$base_url = get_admin_url();
		$index_page = 'admin-ajax.php?action=' . $this->slug . '&';

		$GLOBALS['NTS_CONFIG'][$this->app]['BASE_URL'] = $base_url;
		$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = $index_page;

		switch( $this->system_type ){
			case 'ci':
				$file = $this->happ_path . '/application/index_ci.php';
				require( $file );
				break;

			case 'nts':
				$file = $this->dir . '/../controller.php';
				require( $file );
				$file = $this->dir . '/../view.php';
				require( $file );
				break;
		}
		wp_die();
	}

	static function uninstall( $prefix, $watch_other = array() )
	{
		global $wpdb, $table_prefix;

		if( ! strlen($prefix) ){
			return;
		}

		$stop = FALSE;
		if( $watch_other ){
			if( ! function_exists('get_plugins')){
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$all_plugins = get_plugins();
			foreach( $all_plugins as $pl => $pinfo ){
				reset( $watch_other );
				foreach( $watch_other as $w ){
					if( strpos($pl, $w) !== FALSE ){
						$stop = TRUE;
						$stop = $pl;
						break;
					}
				}
			}
		}

		if( $stop ){
//			echo "STOP AS I ENCOUNTERED '$stop'<br>";
			return;
		}

		$mypref = $table_prefix . $prefix . '_';
		$sql = "SHOW TABLES LIKE '$mypref%'";
		$results = $wpdb->get_results( $sql );
		foreach( $results as $index => $value ){
			foreach( $value as $tbl ){
				$sql = "DROP TABLE IF EXISTS $tbl";
				$e = $wpdb->query($sql);
			}
		}
	}

	public function admin_menu()
	{
	}

	public function admin_submenu()
	{
		if( $this->premium ){
			$this->premium->admin_submenu();
		}
	}

	public function deactivate_other( $plugins = array() )
	{
		$this->deactivate_other = $plugins;
		add_action( 'admin_init', array($this, 'run_deactivate'), 999 );
	}

	public function run_deactivate()
	{
		if( ! $this->deactivate_other )
			return;

		/* check if we have other activated */
		$deactivate = array();
		$plugins = get_option('active_plugins');
		foreach( $plugins as $pl ){
			reset( $this->deactivate_other );
			foreach( $this->deactivate_other as $d ){
				if( strpos($pl, $d) !== FALSE ){
					$deactivate[] = $pl;
				}
			}
		}

		foreach( $deactivate as $d ){
			if( is_plugin_active($d) ){
				deactivate_plugins($d);
			}
		}
	}

	public function admin_view()
	{
		switch( $this->system_type ){
			case 'ci':
				$file = $this->happ_path . '/application/index_view.php';
				require( $file );
				break;
		}
	}

	function strip_p($content)
	{
		// strip only within our output
		$start = stripos( $content, $this->wrap_output[0] );
		if( $start !== FALSE ){
			$end = stripos( $content, $this->wrap_output[1], $start );
			if( $end !== FALSE ){
				$my_content = substr( $content, $start, ($end - $start) );
				$my_content = str_replace( '</p>', '', $my_content );
				$my_content = str_replace( '<p>', '', $my_content );
				$my_content = str_replace( '<br />', '', $my_content );
				$my_content = str_replace( array("&#038;","&amp;"), "&", $my_content ); 

				$content = substr_replace( $content, $my_content, $start, ($end - $start) );
			}
		}

		return $content;
	}

	private function _init_db()
	{
		global $table_prefix;
		$mypref = $table_prefix . $this->db_prefix . '_';

		$GLOBALS['NTS_CONFIG'][$this->app]['DB_HOST'] = DB_HOST;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_USER'] = DB_USER;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_PASS'] = DB_PASSWORD;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_NAME'] = DB_NAME;
		$GLOBALS['NTS_CONFIG'][$this->app]['DB_TABLES_PREFIX'] = $mypref;
	}

	function register_admin_style( $f )
	{
		$file = is_array($f) ? $f[0] : $f;

		$fid = $file;
		if( isset($this->_admin_styles[$fid]) ){
			return;
		}

		$full = FALSE;
		$prfx = array('http://', 'https://', '//');
		reset( $prfx );
		foreach( $prfx as $prf ){
			if( substr($file, 0, strlen($prf)) == $prf ){
				$full = TRUE;
				break;
			}
		}

		if( $full ){
			$full_file = $file;
		}
		else {
			if( substr($file, 0, strlen('happ/')) == 'happ/' ){
				$full_file = $this->happ_web_dir . '/' . $file;
			}
			else {
				$full_file = plugins_url($file, $this->full_path);
			}
		}

		$full_file = str_replace( 'https://', '//', $full_file );
		$full_file = str_replace( 'http://', '//', $full_file );

		if( is_array($f) )
			$f[0] = $full_file;
		else
			$f = $full_file;

		$id = $this->app . '-style-admin-' . (count($this->_admin_styles) + 1);

		$this->_admin_styles[$fid] = array( $id, $f );
	}

	function localize_script( $id, $var, $options = array() )
	{
		$this->_localize_scripts[ $id ] = array( $var, $options );
	}

	function register_admin_script( $f, $id = '' )
	{
		if( ! $id ){
			$id = $this->app . '-script-admin-' . ( count($this->_admin_scripts) + 1);
		}

		$skip = FALSE;

		$file = is_array($f) ? $f[0] : $f;

		$fid = $file;
		if( isset($this->_admin_scripts[$fid]) ){
			return;
		}

		if( substr($file, -strlen('hc.js')) == 'hc.js' ){
			$id = 'hc-core-script';
		}

		$file_id = '';

		$full = FALSE;
		$prfx = array('http://', 'https://', '//');
		reset( $prfx );
		foreach( $prfx as $prf ){
			if( substr($file, 0, strlen($prf)) == $prf ){
				$full = TRUE;
				break;
			}
		}

		if( $full ){
			$full_file = $file;
			/* check if jquery should be required */
			if(
				preg_match('/\/jquery\-\d/', $file)
			){
//				$id = $file_id = 'jquery';
			}
		}
		else {
			/* check jquery */
			if(
				preg_match('/\/jquery\-\d/', $file)
			){
				$file_id = 'jquery';
				$full_file = '';
			}
			elseif(
				preg_match('/\/underscore\-/', $file)
			){
				$file_id = 'underscore';
				$full_file = '';
			}
			elseif(
				preg_match('/\/jquery\-ui\-sortable/', $file)
			){
				$file_id = 'jquery-ui-sortable';
				$full_file = '';
			}
			elseif(
				preg_match('/\/backbone\-/', $file)
			){
				$file_id = 'backbone';
				$full_file = '';
			}
			else {
				$full_file = (substr($file, 0, strlen('happ/')) == 'happ/') ? $this->happ_web_dir . '/' . $file : plugins_url($file, $this->full_path);
			}
		}

		if( $file_id ){
			$id = $file_id;
			$f = '';
		}
		else {
			$full_file = str_replace( 'https://', '//', $full_file );
			$full_file = str_replace( 'http://', '//', $full_file );

			if( is_array($f) )
				$f[0] = $full_file;
			else
				$f = $full_file;
		}

		if( ! $skip )
			$this->_admin_scripts[$fid] = array( $id, $f );
	}

	public function admin_total_init()
	{
		if( $this->premium ){
			$this->premium->admin_total_init();
		}
	}

	public function admin_init()
	{
		$this->admin_total_init();

		if( $this->is_me_admin() ){
			ini_set( 'memory_limit', '512M' );
			$GLOBALS['NTS_APP'] = $this->app;

			$current_user = wp_get_current_user();
			$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_ID'] = $current_user->ID;
			$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_NAME'] = $current_user->user_email;

			$GLOBALS['NTS_CONFIG'][$this->app]['BASE_URL'] = get_admin_url();
			$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = 'admin.php?page=' . $this->slug . '&';
			$GLOBALS['NTS_CONFIG'][$this->app]['ADMIN_PANEL'] = 1;

			$this->_front_boss_init();

			$session_name = $GLOBALS['NTS_CONFIG'][$this->app]['SESSION_NAME'];
			session_name( $session_name );
			if( session_id() == '' ){
				if( ! $this->no_session ){
					@session_start();
				}
			}

			switch( $this->system_type ){
				case 'ci':
					require( $this->happ_path . '/application/index_action.php' );
					break;
			}
		}
	}

	public function check_intercept()
	{
		if( isset($_GET[$this->hcs]) && ($_GET[$this->hcs] == $this->slug) ){
			$this->intercept();
			// $this->is_boss = TRUE;
			// $this->front_init();
		}
		else {
			// continue init
			$this->_continue_init();
		}
	}
	
	protected function _front_boss_init()
	{
		$url = parse_url( site_url('/') );
		$base_url = $url['scheme'] . '://'. $url['host'] . $url['path'];
		$index_page = (isset($url['query']) && $url['query']) ? '?' . $url['query'] . '&' : '?';
		$index_page .= $this->hcs . '=' . $this->slug;
		$index_page .= '&' . $this->hca . '=';

		$GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_BASE_URL'] = $base_url;
		$GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_INDEX_PAGE'] = $index_page;

	/* also ajax */
		// $base_url = get_admin_url();
		// $index_page = 'admin-ajax.php?action=' . $this->slug . '&';

		$GLOBALS['NTS_CONFIG'][$this->app]['AJAX_BASE_URL'] = $base_url;
		$GLOBALS['NTS_CONFIG'][$this->app]['AJAX_INDEX_PAGE'] = $index_page;
	}

	public function intercept( $just_init = FALSE )
	{
		static $already_init = 0;
		if( $already_init ){
			return;
		}
		if( $just_init ){
			$already_init = 1;
		}

		$GLOBALS['NTS_APP'] = $this->app;

		$url = parse_url( site_url('/') );
		$base_url = $url['scheme'] . '://'. $url['host'] . $url['path'];
		$index_page = (isset($url['query']) && $url['query']) ? '?' . $url['query'] . '&' : '?';
		$index_page .= $this->hcs . '=' . $this->slug;
		$index_page .= '&' . $this->hca . '=';

		$GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_BASE_URL'] = $base_url;
		$GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_INDEX_PAGE'] = $index_page;

	/* also ajax */
		$GLOBALS['NTS_CONFIG'][$this->app]['AJAX_BASE_URL'] = $base_url;
		$GLOBALS['NTS_CONFIG'][$this->app]['AJAX_INDEX_PAGE'] = $index_page;

		$current_user = wp_get_current_user();
		$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_ID'] = $current_user->ID;
		$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_NAME'] = $current_user->user_email;

		$GLOBALS['NTS_CONFIG'][$this->app]['BASE_URL'] = $base_url;
		$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = $index_page;

		$GLOBALS['NTS_CONFIG'][$this->app]['IS_INTERCEPT'] = 1;

		if( ! $just_init ){
			$session_name = $GLOBALS['NTS_CONFIG'][$this->app]['SESSION_NAME'];
			session_name( $session_name );
			if( session_id() == '' ){
				if( ! $this->no_session ){
					@session_start();
				}
			}
		}

		switch( $this->system_type ){
			case 'ci':
				if( $just_init ){
					$GLOBALS['NTS_CLI_REQUEST'] = 1;
					require( $this->happ_path . '/application/index_init.php' );
				}
				else {
					require( $this->happ_path . '/application/index_ci.php' );
				}

				break;
			case 'nts':
				$file = $this->dir . '/../controller.php';
				require( $file );
				$file = $this->dir . '/../view.php';
				require( $file );
				break;
		}
		if( ! $just_init ){
			exit;
		}
	}

	public function front_init()
	{
		global $post;
		$return = FALSE;
		$boss = $this->is_boss;

		if( ! is_admin() ){
			if( $boss OR $this->is_me_front() ){
				$this->_front_boss_init();

				$GLOBALS['NTS_APP'] = $this->app;

				add_filter('the_content', array($this, 'strip_p'), 1000);

				add_action( 'wp_enqueue_scripts',	array($this, 'admin_scripts') );
				add_action( 'wp_head', 				array($this, 'admin_head') );

				$current_user = wp_get_current_user();
				$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_ID'] = $current_user->ID;
				$GLOBALS['NTS_CONFIG'][$this->app]['FORCE_LOGIN_NAME'] = $current_user->user_email;

				if( $boss ){
					$base_url = $GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_BASE_URL'];
					$index_page = $GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_INDEX_PAGE'];
				}
				else {
					$this_link = get_permalink($post);
					if( ! $this_link ){
						if( isset($_POST['hc_home_url']) ){
							$this_link = $_POST['hc_home_url'];
						}
					}

					$url = parse_url( $this_link );

					switch( $this->system_type ){
						case 'nts':
							$base_url = $url['scheme'] . '://'. $url['host'] . $url['path'];
							break;

						case 'ci':
							$base_url = $url['path'];
							break;
					}
					$index_page = (isset($url['query']) && $url['query']) ? '?' . $url['query'] . '&' : $this->query_prefix;
				}

				$GLOBALS['NTS_CONFIG'][$this->app]['BASE_URL'] = $base_url;
				$GLOBALS['NTS_CONFIG'][$this->app]['INDEX_PAGE'] = $index_page;

				$return = TRUE;

				if( ! $boss ){
					// might be shortcode with params
					$pattern = '\[' . $this->slug . '\s+(.+)\]';
					$post_content = ( isset($post) && isset($post->post_content) ) ? $post->post_content : '';
					if(
						preg_match('/'. $pattern .'/sU', $post_content, $matches)
						){
						$GLOBALS['NTS_CONFIG'][$this->app]['DEFAULT_PARAMS'] = shortcode_parse_atts( $matches[1] );
					}
				}

				$session_name = $GLOBALS['NTS_CONFIG'][$this->app]['SESSION_NAME'];
				session_name( $session_name );
				if( session_id() == '' ){
					if( ! $this->no_session ){
						@session_start();
					}
				}

				switch( $this->system_type ){
					case 'ci':
						// $GLOBALS['NTS_CONFIG'][$this->app]['SIMULATE_USER_ID'] = 0;
					// action
						require( $this->happ_path . '/application/index_action.php' );
						$GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED'] = 1;
						break;
				}
			}
		}
		return $return;
	}

	public function front_view()
	{
		if( 
			isset($GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED']) && 
			$GLOBALS['NTS_CONFIG'][$this->app]['ACTION_STARTED']
			){
			ob_start();

			switch( $this->system_type ){
				case 'ci':
					$file = $this->happ_path . '/application/index_view.php';
					require( $file );
					break;

				case 'nts':
					$file = $this->dir . '/../view.php';
					require( $file );
					break;
			}

			$return = '';
			$return .= "\n" . $this->wrap_output[0] . "\n";
			$return .= ob_get_contents();
			$return .= "\n" . $this->wrap_output[1] . "\n";

			ob_end_clean();
			return $return;
		}
	}

	public function is_me_front()
	{
		global $post;
		$return = FALSE;

		if( is_admin() )
			return $return;

		if( $_POST && isset($_POST['hc_csrf_token']) ){
			$return = TRUE;
			return $return;
		}

		if( ! (isset($post) && $post) )
			return $return;

		$pattern = '\[' . $this->slug . '\]';


		if(
			preg_match('/'. $pattern .'/s', $post->post_content, $matches)
			){
			$return = TRUE;
		}
		else {
			// might be shortcode with params
			$pattern = '\[' . $this->slug . '\s+(.+)\]';
			if(
				preg_match('/'. $pattern .'/s', $post->post_content, $matches)
				){
//				$atts = shortcode_parse_atts( $matches[1] );
				$return = TRUE;
			}
		}
		return $return;
	}

	public function is_me_front_()
	{
		global $post;
		$return = FALSE;

		if( is_admin() )
			return $return;
		$pattern = get_shortcode_regex();
		if(
			$post && 
			preg_match_all('/'. $pattern .'/s', $post->post_content, $matches) &&
			array_key_exists(2, $matches) &&
			in_array($this->slug, $matches[2])
			){
			$return = TRUE;
		}
		return $return;
	}

	function is_me_admin()
	{
		global $post;
		if(
			( isset($post) && in_array($post->post_type, $this->types) )
			OR
			( isset($_REQUEST['post_type']) && in_array($_REQUEST['post_type'], $this->types) )
			){
			$return = TRUE;
		}
		else {
			$page = isset($_GET['page']) ? $_GET['page'] : '';
			if( isset($_REQUEST['page']) ){
				$page = $_REQUEST['page'];
			}
			if( $page && ($page == $this->slug) ){
				$return = TRUE;
			}
			else {
				$return = FALSE;
			}
		}
		return $return;
	}

	function admin_scripts()
	{
		$css_files = $this->_admin_styles;

	/* make hc-hitcode.css goes after all */
		$append_files = array('hc-hitcode.css', 'hc-hitcode-wp.css');

		$final_css_files = array();
		$append = array();
		foreach( $css_files as $f ){
			$fname = is_array($f) ? $f[1] : $f;
			$append_this = FALSE;
			reset( $append_files );
			foreach( $append_files as $af ){
				if( substr($fname, -strlen('/' . $af)) == '/' . $af ){
					$append_this = TRUE;
					break;
				}
			}

			if( $append_this ){
				$append[] = $f;
			}
			else {
				$final_css_files[] = $f;
			}
		}
		foreach( $append as $f ){
			$final_css_files[] = $f;
		}
		$css_files = $final_css_files;

		reset( $css_files );
		foreach( $css_files as $sa ){
			if( is_array($sa[1]) ){
				// processed later in head
			}
			else {
				wp_enqueue_style( $sa[0], $sa[1] );
			}
		}

		reset( $this->_admin_scripts );

		foreach( $this->_admin_scripts as $sa ){
			if( $sa[1] ){
				if( is_array($sa[1]) ){
					// processed later in head
				}
				else {
					$check = array('jquery', 'backbone', 'underscore', 'jquery-ui-sortable');
					if( ( in_array($sa[0], $check)) && ($sa[1]) ){
						// deregister jquery
						wp_deregister_script($sa[0]);
						wp_register_script(
							$sa[0],
							$sa[1]
							);
						wp_enqueue_script($sa[0]);
					}
					else {
						wp_enqueue_script(
							$sa[0],
							$sa[1],
							array(),
							''
							// TRUE // in footer
							);
					}
				}
			}
			else {
				wp_enqueue_script(
					$sa[0],
					'',
					array(),
					''
					// TRUE // in footer
					);
			}
		}

		reset( $this->_localize_scripts );
		foreach( $this->_localize_scripts as $sid => $soptions ){
			list( $var, $options ) = $soptions;

			wp_localize_script( $sid, $var, $options );
		}
	}

	function admin_head()
	{
	/* this is here because damn WP can't handle confitional scripts for damn IE */
		$return = array();

		reset( $this->_admin_styles );
		foreach( $this->_admin_styles as $sa ){
			if( $sa[1] && is_array($sa[1]) ){
				$return[] = 
					'<!--[if ' . $sa[1][1] . ']>' .
					"\n" . 
					'<link rel="stylesheet" id="' . $sa[0] . '" href="' . $sa[1][0] . ' type="text/css" media="all" />' .
					"\n" . 
					'<![endif]-->' .
					"\n";
			}
		}

		reset( $this->_admin_scripts );
		foreach( $this->_admin_scripts as $sa ){
			if( $sa[1] && is_array($sa[1]) ){
				$return[] = 
					'<!--[if ' . $sa[1][1] . ']>' .
					"\n" . 
					'<script src="' . $sa[1][0] . '"></script>' . 
					"\n" . 
					'<![endif]-->' .
					"\n";
			}
		}

		$return = join( "\n", $return );
		echo $return;
	}

// normally overwritten by child classes
	function _install()
	{
	// own database
		$this->_init_db();
	}

	function _init()
	{
	// custom types and taxonimies
		$file = $this->dir . '/conf/cpt.php';
		if( file_exists($file) ){
			require( $file );
		}

	// own database
		$this->_init_db();

	// load shortcode
		global $wpdb;

		$shortcode = '[' . $this->slug . '';
		$this->pages = array();
		$pages = $wpdb->get_results( 
			"
			SELECT 
				ID 
			FROM $wpdb->posts 
			WHERE 
				( post_type = 'post' OR post_type = 'page' ) 
				AND 
				(
				post_content LIKE '%" . $shortcode . "%]%'
				)
				AND 
				( post_status <> 'trash' )
			"
			);
		foreach( $pages as $p ){
			$this->pages[] = $p->ID;
		}

		if( $this->pages ){
			$web_page = get_permalink($this->pages[0]);

			if( strlen($this->query_prefix) ){
				$url = parse_url( $web_page );
				if( isset($url['query']) && $url['query'] ){
					$web_page .= '&';
				}
				else {
					$web_page .= $this->query_prefix;
				}
			}
		}
		else
		{
			$web_page = get_bloginfo('wpurl');
		}

		$GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_WEBPAGE'] = $web_page;

	// other config
		$GLOBALS['NTS_CONFIG'][$this->app]['REMOTE_INTEGRATION'] = 'wordpress';
		$session_name = 'ntssess_' . $this->app;
		$GLOBALS['NTS_CONFIG'][$this->app]['SESSION_NAME'] = $session_name;

	// text domain
		$lang_domain = $this->app;
		$lang_dir = plugin_basename($this->dir) . '/languages';
		load_plugin_textdomain( $lang_domain, '', $lang_dir );

//		session_name( $session_name );
//		@session_start();
		ob_start();
	}

	function get_options( $defaults = array() )
	{
		$options = get_option($this->app);
		$return = array_merge( $defaults, $options );
		return $return;
	}

	function get_option( $key )
	{
		$options = $this->get_options();
		$return = isset($options[$key]) ? $options[$key] : NULL;
		return $return;
	}

	function render( $view, $vars )
	{
		$file = $this->dir . '/views/' . $view . '.php';
		if( ! file_exists($file) ){
			$content = 'File "' . $view . '" does not exist<br>';
		}
		else {
			extract( $vars );
			ob_start();
			require( $file );
			$content = ob_get_contents();
			ob_end_clean();
		}
		return $content;
	}

	function save_option( $key, $value )
	{
		$options = $this->get_options();
		$options[$key] = $value;
		update_option($this->app, $options);
	}

	function check_post( $post_id )
	{
		global $post;
	/* Check if the current user has permission to edit the post. */
		if( $post ){
			$post_type = get_post_type_object( $post->post_type );
			if ( ! current_user_can($post_type->cap->edit_post, $post_id) )
				return FALSE;
		}
		return TRUE;
	}

	function save_meta( $post_id )
	{
		// normally overwritten by child classes
		return $post_id;
	}

	function make_input( $start, $props )
	{
		$display = array();
		$display[] = $start;

		if( ! isset($props['id']) ){
			$id = $props['name'];
			$id = str_replace( '[', '_', $id );
			$id = str_replace( ']', '', $id );
			$props['id'] = $id;
		}

		reset( $props );
		foreach( $props as $k => $v ){
			$display[] = $k . '="' . $v . '"';
		}
		$return = '<' . join( ' ', $display ) . '>';
		return $return;
	}

	public function logout()
	{
		$session_name = $GLOBALS['NTS_CONFIG'][$this->app]['SESSION_NAME'];
		session_name( $session_name );
		if( session_id() == '' ){
			if( ! $this->no_session ){
				@session_start();
			}
		}

		if( isset($_SESSION['NTS_SESSION_REF']) ){
			unset( $_SESSION['NTS_SESSION_REF'] );
		}
		session_write_close();
	}

	public function dev_options()
	{
		if( $this->premium ){
			$this->premium->dev_options();
		}
	}
}
}
?>