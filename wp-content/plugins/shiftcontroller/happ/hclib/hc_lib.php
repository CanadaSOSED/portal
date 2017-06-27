<?php
include_once( dirname(__FILE__) . '/hc_object_cache.php' );

/* gettext without WordPress */
if( ! function_exists('__') ){
	if( file_exists(dirname( __FILE__ ) . '/php-gettext/Gettext.php') ){
		include_once( dirname( __FILE__ ) . '/php-gettext/Gettext.php' );
		include_once( dirname( __FILE__ ) . '/php-gettext/PHP.php' );
	}
}

// --------------------------------------------------------------------

if( ! function_exists('hc_in_array') ){
	function hc_in_array( $needle, $haystack )
	{
		if( ! is_array($haystack) ){
			$haystack = array( $haystack );
		}
		return in_array( $needle, $haystack );
	}
}

if( ! function_exists('hc_ci_do_init') ){
	function hc_ci_do_init()
	{
		$ci =& ci_get_instance();
		$ci->load->database();

		$ci->load->library( 'hc_modules' );

	/* add module models paths for autoloading */
		$extensions = HC_App::extensions();
		$acl = HC_App::acl();

		$look_in_dirs = $ci->config->look_in_dirs();
		foreach( $look_in_dirs as $ldir ){
			if( class_exists('Datamapper') ){
				Datamapper::add_model_path( $ldir );
			}
			$ci->load->add_package_path( $ldir );
			$extensions->add_dir( $ldir );
			$acl->add_dir( $ldir );

			$bootstrap_file = $ldir . '/bootstrap.php';
			if( file_exists($bootstrap_file) ){
				$ci->load->file($bootstrap_file, TRUE);
			}
		}

		$extensions->init();
		$acl->init();

	/* reload config paths */
		$app_conf = HC_App::app_conf();
		$ci->load->library('hc_modules');

	/* events and notifiers */
		$ci->load->library( array('hc_events') );
		if( defined('WPINC') ){
			$ci->load->library('hc_email_wp', NULL, 'hc_email');
		}
		else {
			$ci->load->library('hc_email');
		}

		$ci->hc_email->from = $app_conf->get('email_from');
		$ci->hc_email->fromName = $app_conf->get('email_from_name');

		$bcc_email = $app_conf->get('bcc_email');
		if( strlen($bcc_email) ){
			$bcc_email = explode(',', $bcc_email);
			$ci->hc_email->bcc_to = $bcc_email;
		}
	}
}

if( ! class_exists('HCM') ){
class HCM
{
	static function __( $str )
	{
		if( function_exists('__') ){
			$domain = HC_App::app();
			return __($str, $domain);
		}
		else {
			$gettext_obj = hc_get_gettext();
			if( $gettext_obj === NULL ){
				return $str;
			}
			else {
				return $gettext_obj->gettext( $str );
			}
		}
	}

	static function _x( $str, $context )
	{
		if( function_exists('_x') ){
			$domain = HC_App::app();
			return _x($str, $context, $domain);
		}
		else {
			$gettext_obj = hc_get_gettext();
			if( $gettext_obj === NULL ){
				return $str;
			}
			else {
				return $gettext_obj->gettext( $str );
			}
		}
	}

	static function _n( $singular, $plural, $count )
	{
		if( function_exists('_n') ){
			$domain = HC_App::app();
			return _n($singular, $plural, $count, $domain);
		}
		else {
			$gettext_obj = hc_get_gettext();

			if( $gettext_obj === NULL ){
				return $plural;
			}
			else {
				return $gettext_obj->ngettext( $singular, $plural, $count );
			}
		}
	}
}
}

if ( ! function_exists('hc_get_gettext'))
{
	function hc_get_gettext(){
		global $NTS_GETTEXT_OBJ;
		$domain = HC_App::app();

		if( ! isset($NTS_GETTEXT_OBJ) ){
			$locale = "it_IT";
			$locale = "";
			// $locale = "ru_RU";

			if( $locale ){
				setlocale( LC_TIME, $locale );
			}

			if( $domain == "shiftexec" ){
				$domain = "shiftcontroller";
			}

			$modir = '';
			if( isset($GLOBALS["NTS_APPPATH"]) ){
				$modir = $GLOBALS["NTS_APPPATH"] . "/../languages";
			}
			$mofile = $modir . "/" . $domain . "-" . $locale . ".mo";
			// echo "mofile = $mofile<br>";

			global $NTS_GETTEXT_OBJ;
			if( class_exists('Gettext_PHP') ){
				$NTS_GETTEXT_OBJ = new Gettext_PHP( $mofile );
			}
			else {
				$NTS_GETTEXT_OBJ = NULL;
			}
		}
		return $NTS_GETTEXT_OBJ;
	}
}

if ( ! function_exists('hc_serialize'))
{
	function hc_serialize( $array )
	{
		$return = array();

		foreach( $array as $subarray ){
			foreach( $subarray as $k => $v ){
				if( is_object($v) ){
					if( isset($v->id) ){
						$v = array( $v->id );
					}
					else {
						$v = array();
					}
				}
				elseif( is_array($v) ){
				}
				else {
					$v = array( $v );
				}

				if( ! isset($return[$k]) ){
					$return[$k] = array();
				}
				$return[$k] = array_merge( $return[$k], $v );
				$return[$k] = array_unique( $return[$k] );
			}
		}
		$return = serialize( $return );
		return $return;
	}
}

/**
 * Plural
 *
 * Takes a singular word and makes it plural
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	str
 */
if ( ! function_exists('hc_plural'))
{
	function hc_plural($str, $force = FALSE)
	{
		$result = strval($str);

		$plural_rules = array(
			'/^(ox)$/'                 => '\1\2en',     // ox
			'/([m|l])ouse$/'           => '\1ice',      // mouse, louse
			'/(matr|vert|ind)ix|ex$/'  => '\1ices',     // matrix, vertex, index
			'/(x|ch|ss|sh)$/'          => '\1es',       // search, switch, fix, box, process, address
			'/([^aeiouy]|qu)y$/'       => '\1ies',      // query, ability, agency
			'/(hive)$/'                => '\1s',        // archive, hive
			'/(?:([^f])fe|([lr])f)$/'  => '\1\2ves',    // half, safe, wife
			'/sis$/'                   => 'ses',        // basis, diagnosis
			'/([ti])um$/'              => '\1a',        // datum, medium
			'/(p)erson$/'              => '\1eople',    // person, salesperson
			'/(m)an$/'                 => '\1en',       // man, woman, spokesman
			'/(c)hild$/'               => '\1hildren',  // child
			'/(buffal|tomat)o$/'       => '\1\2oes',    // buffalo, tomato
			'/(bu|campu)s$/'           => '\1\2ses',    // bus, campus
			'/(alias|status|virus)/'   => '\1es',       // alias
			'/(octop)us$/'             => '\1i',        // octopus
			'/(ax|cris|test)is$/'      => '\1es',       // axis, crisis
			'/s$/'                     => 's',          // no change (compatibility)
			'/$/'                      => 's',
		);

		foreach ($plural_rules as $rule => $replacement){
			if (preg_match($rule, $result)){
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}
		return $result;
	}
}

// --------------------------------------------------------------------

/**
 * Singular
 *
 * Takes a plural word and makes it singular
 *
 * @access	public
 * @param	string
 * @return	str
 */
if ( ! function_exists('hc_singular'))
{
	function hc_singular($str)
	{
		$result = strval($str);

		$singular_rules = array(
			'/(matr)ices$/'         => '\1ix',
			'/(vert|ind)ices$/'     => '\1ex',
			'/^(ox)en/'             => '\1',
			'/(alias)es$/'          => '\1',
			'/([octop|vir])i$/'     => '\1us',
			'/(cris|ax|test)es$/'   => '\1is',
			'/(shoe)s$/'            => '\1',
			'/(o)es$/'              => '\1',
			'/(bus|campus)es$/'     => '\1',
			'/([m|l])ice$/'         => '\1ouse',
			'/(x|ch|ss|sh)es$/'     => '\1',
			'/(m)ovies$/'           => '\1\2ovie',
			'/(s)eries$/'           => '\1\2eries',
			'/([^aeiouy]|qu)ies$/'  => '\1y',
			'/([lr])ves$/'          => '\1f',
			'/(tive)s$/'            => '\1',
			'/(hive)s$/'            => '\1',
			'/([^f])ves$/'          => '\1fe',
			'/(^analy)ses$/'        => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
			'/([ti])a$/'            => '\1um',
			'/(p)eople$/'           => '\1\2erson',
			'/(m)en$/'              => '\1an',
			'/(s)tatuses$/'         => '\1\2tatus',
			'/(c)hildren$/'         => '\1\2hild',
			'/(n)ews$/'             => '\1\2ews',
			'/([^u])s$/'            => '\1',
		);

		foreach ($singular_rules as $rule => $replacement){
			if (preg_match($rule, $result)){
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}
		return $result;
	}
}

if ( ! function_exists('hc_ci_before_exit'))
{
	function hc_ci_before_exit()
	{
	/* this is a hack to ensure that post controller and post system hooks are triggered */
		$GLOBALS['EXT']->_call_hook('post_controller');
		$GLOBALS['EXT']->_call_hook('post_system');
	}
}

if ( ! function_exists('hc_run_notifier'))
{
	function hc_run_notifier()
	{
		static $already_run = 0;
		if( $already_run ){
			return;
		}

		$already_run = 1;
		$notifier = HC_App::model('messages');
		if( isset($notifier) ){
			$notifier->run();
		}
	}
}

if ( ! function_exists('hc_parse_args'))
{
	function hc_parse_args( $args, $multiple_values = FALSE )
	{
		$return = array();
		$pass = array();

		$start_pass = 0;
		for( $ii = 0; $ii < count($args); $ii = $ii + 2 ){
			if( $args[$ii] == '_pass' ){
				$start_pass = $ii+1;
				break;
			}

			if( isset($args[$ii + 1]) ){
				$k = $args[$ii];
				$v = $args[$ii + 1];
				if( $multiple_values && is_string($v) && (strpos($v, '.') !== FALSE) ){
					$v = explode('.', $v);
				}
				$return[ $k ] = $v;
			}
		}

	/* process passthrough */
		if( $start_pass ){
			$pass = array_slice( $args, $start_pass );
		}

		if( $pass ){
			$return['_pass'] = $pass;
		}
		return $return;
	}
}

if ( ! function_exists('_print_r'))
{
	function _print_r( $thing )
	{
		echo '<pre>';
		print_r( $thing );
		echo '</pre>';
	}
}

if ( ! function_exists('hc_random'))
{
	function hc_random( $len = 8 )
	{
		$salt1 = '0123456789';
		$salt2 = 'abcdef';

//		$salt .= 'abcdefghijklmnopqrstuvxyz';
//		$salt .= 'ABCDEFGHIJKLMNOPQRSTUVXYZ';

		srand( (double) microtime() * 1000000 );
		$return = '';
		$i = 1;
		$array = array();

		while ( $i <= ($len - 1) ){
			$num = rand() % strlen($salt1 . $salt2);
			$tmp = substr($salt1 . $salt2, $num, 1);
			$array[] = $tmp;
			$i++;
			}
		shuffle( $array );

	// first is letter
		$num = rand() % strlen($salt2);
		$tmp = substr($salt2, $num, 1);
		array_unshift($array, $tmp);

		$return = join( '', $array );
		return $return;
	}
}

class HC_Presenter
{
	const VIEW_HTML = 2;
	const VIEW_HTML_ICON = 3;
	const VIEW_TEXT = 1;
	const VIEW_RAW = 0;

	public function errors( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$errors = $model->errors();

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$out = HC_Html_Factory::widget('list')
					;
				break;
			default:
				$out = array();
				break;
		}

		foreach( $errors as $pname => $text ){
			switch( $vlevel ){
				case HC_PRESENTER::VIEW_HTML:
					$this_out = HC_Html_Factory::widget('list')
						->add_children_style('inline')
						->add_children_style('margin', 'b1', 'r1')
						;
					$this_out->add_child( $model->present_property_name($pname) . ':' );
					$this_out->add_child( $text );
					$out->add_child( $this_out );
					break;
				default:
					$this_out = array();
					$this_out[] = $model->present_property_name($pname) . ': ';
					$this_out[] = $text;
					$out[] = join('', $this_out);
					break;
			}
		}

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$out = $out->render();
				break;
			default:
				$out[] = join("\n", $this_out);
				break;
		}

		return $out;
	}

	public function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = $model->my_class();
		return $return;
	}

	public function property_name( $model, $pname, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		switch( $pname ){
			case 'status':
				$return = HCM::__('Status');
				break;
			default:
				$return = $pname;
			}
		return $return;
	}

	public function color( $model )
	{
		$return = Hc_lib::random_html_color( $model->id );
		return $return;
	}
}

class HC_Page_Params
{
	private $params = array();
	private $skip = array();
	private $options = array();

	public function slug()
	{
		$array = $this->to_array();
		$return = array();
		foreach( $array as $k => $v )
		{
			$return[] = $k;
			$return[] = $v;
		}
		return $return;
	}

	public function skip( $skip = array() )
	{
		$this->skip = $skip;
		return $this;
	}

	public function set( $key, $value )
	{
		$this->params[$key] = $value;
	}

	public function set_options( $key, $options ){
		$this->options[$key] = $options;
	}
	public function get_options( $key ){
		$return = NULL;
		if( isset($this->options[$key]) ){
			$return = $this->options[$key];
		}
		return $return;
	}

	public function reset( $key )
	{
		unset( $this->params[$key] );
	}

	public function get( $key )
	{
		$return = NULL;
		if( isset($this->params[$key]) ){
			$return = $this->params[$key];
		}
		return $return;
	}

	public function to_array()
	{
		$return = array();
		foreach( $this->params as $k => $v )
		{
			if( in_array($k, $this->skip) )
				continue;
			$return[ $k ] = $v;
		}
		$this->skip( array() );
		return $return;
	}

	public function get_keys()
	{
		return array_keys($this->params);
	}
}

class HC_App
{
	static function app()
	{
		$return = '';
		if( isset($GLOBALS['NTS_APP'])){
			$return = $GLOBALS['NTS_APP'];
		}
		return $return;
	}

	static function acl()
	{
		$return = HC_Acl::get_instance();
		return $return;
	}

	static function filter_ui()
	{
		$return = HC_Filter_UI::get_instance();
		return $return;
	}

	static function extensions()
	{
		$return = HC_Extensions::get_instance();
		return $return;
	}

	static function notifier()
	{
		$return = HC_Notifier::get_instance();
		return $return;
	}

	static function app_conf()
	{
		return HC_App::model('app_conf');
	}

	static function csrf()
	{
		$csrf_name = '';
		$csrf_value = '';
		$CI =& ci_get_instance();
		if ($CI->config->item('csrf_protection') )
		{
			$csrf_name = $CI->security->get_csrf_token_name();
			$csrf_value = $CI->security->get_csrf_hash();
		}
		return array( $csrf_name, $csrf_value );
	}

	static function presenter( $model )
	{
		$return = NULL;

		$class = ucfirst($model) . '_HC_Presenter';
		if( class_exists($class) ){
			if( method_exists($class, 'get_instance')){
				$return = call_user_func(array($class, 'get_instance'));
				// $return = $model::get_instance();
			}
			else {
				$return = new $class;
			}
		}

		return $return;
	}

	static function short_model( $model ){
		$model = strtolower($model);
		if( substr($model, -strlen('_hc_model')) == '_hc_model' ){
			$model = substr($model, 0, -strlen('_hc_model'));
		}
		return $model;
	}

	static function full_model( $model ){
		$model = strtolower($model);
		if( substr($model, -strlen('_hc_model')) != '_hc_model' ){
			$model = $model . '_hc_model';
		}
		return $model;
	}

	static function model( $model )
	{
		$return = NULL;
		$model = HC_App::full_model( $model );

		if( class_exists($model) ){
			if( method_exists($model, 'get_instance')){
				$return = call_user_func(array($model, 'get_instance'));
				// $return = $model::get_instance();
			}
			else {
				$return = new $model;
			}
		}
		return $return;
	}

	static function icon_for( $class )
	{
		$return = '';
		$conf = array(
			'date'		=> 'calendar',
			'time'		=> 'clock',
			'shift'		=> 'clock',
			'shift'		=> 'work',
			'break'		=> 'coffee',
			'timeoff'	=> 'reply',
			'user'		=> 'user',
			'users'		=> 'users',
			'location'	=> 'home',
			'trade'		=> 'exchange',
			'trade'		=> 'refresh',
			'conflict'	=> 'exclamation',
			'comment'	=> 'comment',
			);
		if( isset($conf[$class]) )
			$return = $conf[$class];
		return $return;
	}

	static function widget_locations()
	{
		static $return = NULL;
		
		if( $return === NULL ){
			$return = array();
			$return['HC'] = dirname(__FILE__) . '/widgets';
			if( defined('APPPATH') ){
				$return['SFT'] = array();
				$return['SFT'][] = APPPATH . 'widgets';

				$CI =& ci_get_instance();
				$look_in_dirs = $CI->config->look_in_dirs();
				foreach( $look_in_dirs as $ld ){
					$return['SFT'][] = $ld . '/widgets';
				}
			}
		}

		return $return;
	}
}

class HC_Link
{
	private $controller = '';
	private $params = array();
	private $force_frontend = FALSE;
	private $force_ajax = FALSE;
	private $force_site_url = NULL;

	function __construct( $controller = '', $params = array() )
	{
		if( is_array($controller) ){
			$controller = join('/', $controller);
		}
		$this->controller = $controller;
		$this->params = $params;
	}

	function from_url( $full_url )
	{
	/* sort of hack
		index.php?/list
		admin.php?page=shiftcontroller&/list
		/?hcs=shiftcontroller&hca=/list
		/shiftcontroller/?/list
		?page_id=67&/list
	 */

		$look_for = array('hca=/', '&/', '?/');
		$remain = '';
		foreach( $look_for as $l4 ){
			$pos = strpos($full_url, $l4);
			if( $pos === FALSE ){
				continue;
			}
			$remain = substr($full_url, $pos + strlen($l4));
			$prefix = substr($full_url, 0, $pos + strlen($l4));
		}

		if( $remain ){
			$remain = explode('/', $remain);
		/* remove 2 first items - controller and method */
			$append_to_prefix = array();
			$controller = array_shift($remain);
			$append_to_prefix[] = $controller;

			$method = array_shift($remain);
			if( ! strlen($method) ){
				$method = 'index';
			}
			$append_to_prefix[] = $method;
			$prefix .= join('/', $append_to_prefix) . '/';

			$remain = hc_parse_args($remain);

			$this->force_site_url = $prefix;
			$this->params = $remain;
		}

		return $this;
	}

	function set_force_frontend( $force = TRUE )
	{
		$this->force_frontend = $force;
		return $this;
	}
	function force_frontend()
	{
		return $this->force_frontend;
	}

	function set_force_ajax( $force = TRUE )
	{
		$this->force_ajax = $force;
		return $this;
	}
	function force_ajax()
	{
		return $this->force_ajax;
	}

	function build_url_params( $args )
	{
		$append_controller = '';
		$change_params = array();

		$slug = array();

		if( count($args) == 1 ){
			list( $change_params ) = $args;
		}
		elseif( count($args) == 2 ){
			list( $append_controller, $change_params ) = $args;
		}

		$slug = array();
		if( $this->controller ){
			$slug[] = $this->controller;
		}
		if( $append_controller ){
			if( ! is_array($append_controller) ){
				$append_controller = array($append_controller);
			}
			foreach( $append_controller as $ac ){
				$slug[] = $ac;
			}
		}

		$params = array_merge( $this->params, $change_params );
		$params = $this->params;
		foreach( $change_params as $k => $v ){
			if( (substr($k, -1) == '+') OR (substr($k, -1) == '-') OR (substr($k, -1) == '*') ){
				$operation = substr($k, -1);
				$k = substr($k, 0, -1);
				if( isset($params[$k]) ){
					if( ! is_array($params[$k]) ){
						$params[$k] = array( $params[$k] );
					}
				}
				else {
					$params[$k] = array();
				}
				if( $operation == '+' ){
					$params[$k][] = $v;
				}
				else {
					$params[$k] = HC_Lib::remove_from_array( $params[$k], $v );
					if( $operation == '*' ){
						if( ! $params[$k] ){
							$params[$k] = array(-1);
						}
					}
				}
			}
			else {
				$params[$k] = $v;
			}

			if( count($params[$k]) > 1 ){
				$params[$k] = HC_Lib::remove_from_array( $params[$k], -1 );
				$params[$k] = HC_Lib::remove_from_array( $params[$k], '_1' );
			}
		}

	/* unset hide-ui */
		unset( $params['hide-ui'] );
		unset( $params['filter-ui'] );

		foreach( $params as $k => $v ){
			if( is_array($v) ){
				if( ! $v ){
					continue;
				}
				$v = join('.', $v);
			}

			if( $v !== NULL ){
				$slug[] = $k;
				$slug[] = $v;
			}
		}

		return $slug;
	}

	function url()
	{
		$return = NULL;

		$args = func_get_args();
		$ri = HC_Lib::ri();
		if( $ri && (count($args) == 0) && (count($this->params) == 0) ){
			switch( $this->controller ){
				case 'auth/login':
					$return = Modules::run( $ri . '/auth/login_url' );
					break;
				case 'auth/logout':
					$return = Modules::run( $ri . '/auth/logout_url' );
					break;
			}
		}
		if( $return ){
			return $return;
		}

		$slug = $this->build_url_params( $args );

		$CI =& ci_get_instance();
		if( $this->force_site_url !== NULL ){
			$uri_string = $CI->config->uri_string( $slug );
			$return = $this->force_site_url . $uri_string;
		}
		else {
			if( $this->force_frontend() ){
				$app = HC_Lib::app();
				if( isset($GLOBALS['NTS_CONFIG'][$app]['FRONTEND_BASE_URL']) ){
					$remember_base_url = $CI->config->item('base_url');
					$remember_index_page = $CI->config->item('index_page'); 
					$CI->config->set_item('base_url', $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_BASE_URL']);
					$CI->config->set_item('index_page', $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_INDEX_PAGE']);
				}
			}
			if( $this->force_ajax() ){
				$app = HC_Lib::app();
				if( isset($GLOBALS['NTS_CONFIG'][$app]['AJAX_BASE_URL']) ){
					$remember_base_url = $CI->config->item('base_url');
					$remember_index_page = $CI->config->item('index_page'); 
					$CI->config->set_item('base_url', $GLOBALS['NTS_CONFIG'][$app]['AJAX_BASE_URL']);
					$CI->config->set_item('index_page', $GLOBALS['NTS_CONFIG'][$app]['AJAX_INDEX_PAGE']);
				}
			}

			$return = $CI->config->site_url( $slug );

			if( $this->force_frontend() ){
				if( isset($GLOBALS['NTS_CONFIG'][$app]['FRONTEND_BASE_URL']) ){
					$CI->config->set_item('base_url', $remember_base_url);
					$CI->config->set_item('index_page', $remember_index_page);
				}
			}
			if( $this->force_ajax() ){
				if( isset($GLOBALS['NTS_CONFIG'][$app]['AJAX_BASE_URL']) ){
					$CI->config->set_item('base_url', $remember_base_url);
					$CI->config->set_item('index_page', $remember_index_page);
				}
			}
		}

		return $return;
	}

	function _old_url()
	{
		$return = NULL;

		$append_controller = '';
		$change_params = array();

		$args = func_get_args();
		$ri = HC_Lib::ri();
		if( $ri && (count($args) == 0) && (count($this->params) == 0) ){
			switch( $this->controller ){
				case 'auth/login':
					$return = Modules::run( $ri . '/auth/login_url' );
					break;
				case 'auth/logout':
					$return = Modules::run( $ri . '/auth/logout_url' );
					break;
			}
		}
		if( $return ){
			return $return;
		}

		if( count($args) == 1 ){
			list( $change_params ) = $args;
		}
		elseif( count($args) == 2 ){
			list( $append_controller, $change_params ) = $args;
		}

		$slug = array();
		if( $this->controller ){
			$slug[] = $this->controller;
		}
		if( $append_controller ){
			if( ! is_array($append_controller) ){
				$append_controller = array($append_controller);
			}
			foreach( $append_controller as $ac ){
				$slug[] = $ac;
			}
		}

		$params = array_merge( $this->params, $change_params );
		$params = $this->params;
		foreach( $change_params as $k => $v ){
			if( (substr($k, -1) == '+') OR (substr($k, -1) == '-') ){
				$operation = substr($k, -1);
				$k = substr($k, 0, -1);
				if( isset($params[$k]) ){
					if( ! is_array($params[$k]) ){
						$params[$k] = array( $params[$k] );
					}
				}
				else {
					$params[$k] = array();
				}
				if( $operation == '+' ){
					$params[$k][] = $v;
				}
				else {
					$params[$k] = HC_Lib::remove_from_array( $params[$k], $v );
				}
			}
			else {
				$params[$k] = $v;
			}
		}

	/* unset hide-ui */
		unset( $params['hide-ui'] );
		unset( $params['filter-ui'] );

		foreach( $params as $k => $v ){
			if( is_array($v) ){
				if( ! $v ){
					continue;
				}
				$v = join('.', $v);
			}
			if( $v !== NULL ){
				$slug[] = $k;
				$slug[] = $v;
			}
		}

		$CI =& ci_get_instance();

		if( $this->force_frontend() ){
			$app = HC_Lib::app();
			if( isset($GLOBALS['NTS_CONFIG'][$app]['FRONTEND_BASE_URL']) ){
				$remember_base_url = $CI->config->item('base_url');
				$remember_index_page = $CI->config->item('index_page'); 
				$CI->config->set_item('base_url', $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_BASE_URL']);
				$CI->config->set_item('index_page', $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_INDEX_PAGE']);
			}
		}
		if( $this->force_ajax() ){
			$app = HC_Lib::app();
			if( isset($GLOBALS['NTS_CONFIG'][$app]['AJAX_BASE_URL']) ){
				$remember_base_url = $CI->config->item('base_url');
				$remember_index_page = $CI->config->item('index_page'); 
				$CI->config->set_item('base_url', $GLOBALS['NTS_CONFIG'][$app]['AJAX_BASE_URL']);
				$CI->config->set_item('index_page', $GLOBALS['NTS_CONFIG'][$app]['AJAX_INDEX_PAGE']);
			}
		}

		$return = $CI->config->site_url( $slug );

		if( $this->force_frontend() ){
			if( isset($GLOBALS['NTS_CONFIG'][$app]['FRONTEND_BASE_URL']) ){
				$CI->config->set_item('base_url', $remember_base_url);
				$CI->config->set_item('index_page', $remember_index_page);
			}
		}
		if( $this->force_ajax() ){
			if( isset($GLOBALS['NTS_CONFIG'][$app]['AJAX_BASE_URL']) ){
				$CI->config->set_item('base_url', $remember_base_url);
				$CI->config->set_item('index_page', $remember_index_page);
			}
		}

		return $return;
	}

	public function __toString()
	{
		return $this->url();
    }
}

class HC_lib {
	static function web_dir_name( $fullWebPage )
	{
		preg_match( "/(.+)\/.*$/", $fullWebPage, $matches );
		if ( isset($matches[1]) )
			$webDir = $matches[1];
		else
			$webDir = '';
		return $webDir;
	}

	static function get_combinations( $a )
	{
		$return = array();
		if( count($a) > 3 ){
			echo 'get combinations is not supported for ' . count($a) . ' entries';
			return;
		}

		// dumb one
		sort( $a );
		switch( count($a) ){
			case 3:
				$return[] = array($a[0], $a[1], $a[2]);
				$return[] = array($a[0], $a[1]);
				$return[] = array($a[0], $a[2]);
				$return[] = array($a[1], $a[2]);
				$return[] = array($a[0]);
				$return[] = array($a[1]);
				$return[] = array($a[2]);
				break;
			case 2:
				$return[] = array($a[0], $a[1]);
				$return[] = array($a[0]);
				$return[] = array($a[1]);
				break;
			case 1:
				$return = $a;
				break;
		}

		return $return;
	}
	
	static function redirect( $uri = '', $method = 'location', $http_response_code = 302 )
	{
		// if( ! ( (! is_array($uri)) && preg_match('#^https?://#i', $uri) ) ){
		if( ! ( (! is_array($uri)) && HC_Lib::is_full_url($uri) ) ){
			$uri = HC_Lib::link($uri)->url();
		}

	/* this is a hack to ensure that post controller and post system hooks are triggered */
		hc_ci_before_exit();

		switch($method){
			case 'refresh'	: header("Refresh:0;url=".$uri);
				break;
			default			: header("Location: ".$uri, TRUE, $http_response_code);
				break;
		}
		return;
	}

	static function build_csv( $array, $separator = ',' )
	{
		$processed = array();
		reset( $array );
		foreach( $array as $a ){
			if( strpos($a, '"') !== false ){
				$a = str_replace( '"', '""', $a );
				}
			if( strpos($a, $separator) !== false ){
				$a = '"' . $a . '"';
				}
			$processed[] = $a;
			}

		$return = join( $separator, $processed );
		return $return;
	}

	static function array_skip_after( $src, $after, $include = TRUE )
	{
		$return = array();
		foreach( $src as $k ){
			if( $k == $after ){
				if( $include )
					$return[] = $k;
				break;
			}
			$return[] = $k;
		}
		return $return;
	}

	static function array_remain_after( $src, $after, $include = TRUE )
	{
		$return = array();
		$ok = FALSE;
		foreach( $src as $k ){
			if( $k == $after ){
				$ok = TRUE;
				if( ! $include )
					continue;
			}
			if( $ok )
				$return[] = $k;
		}
		return $return;
	}

	static function array_intersect_by_key( $src, $keys )
	{
		$out = array();
		foreach( $keys as $k ){
			if( array_key_exists($k, $src) ){
				$out[ $k ] = $src[ $k ];
			}
		}
		return $out;
	}

	static function generate_rand( $len = 12, $conf = array() )
	{
		$useLetters = isset($conf['letters']) ? $conf['letters'] : TRUE;
		$useHex = isset($conf['hex']) ? $conf['hex'] : FALSE;
		$useDigits = isset($conf['digits']) ? $conf['digits'] : TRUE;
		$useCaps = isset($conf['caps']) ? $conf['caps'] : FALSE;

		$salt = '';
		if( $useHex )
			$salt .= '0123456789abcdef';
		if( $useLetters )
			$salt .= 'abcdefghijklmnopqrstuvxyz';
		if( $useDigits )
			$salt .= '0123456789';
		if( $useCaps )
			$salt .= 'ABCDEFGHIJKLMNOPQRSTUVXYZ';

		srand( (double) microtime() * 1000000 );
		$return = '';
		$i = 1;
		$array = array();
		while ( $i <= $len ){
			$num = rand() % strlen($salt);
			$tmp = substr($salt, $num, 1);
			$array[] = $tmp;
			$i++;
			}
		shuffle( $array );
		$return = join( '', $array );
		return $return;
	}

	static function is_ajax()
	{
		$return = FALSE;
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ){
			$return = TRUE;
		}
		return $return;
	}

	static function app()
	{
		$return = '';
		if( isset($GLOBALS['NTS_APP'])){
			$return = $GLOBALS['NTS_APP'];
		}
		return $return;
	}

	static function nts_config()
	{
		$return = array();
		$app = HC_Lib::app();
		if( isset($GLOBALS['NTS_CONFIG'][$app]) ){
			$return = $GLOBALS['NTS_CONFIG'][$app];
		}
		return $return;
	}

	static function ri()
	{
		$return = '';
		$nts_config = HC_Lib::nts_config();
		if( isset($nts_config['REMOTE_INTEGRATION']) ){
			$return = $nts_config['REMOTE_INTEGRATION'];
		}
		return $return;
	}

	static function is_full_url( $url )
	{
		$full = FALSE;
		if( is_array($url)){
			return $full;
		}

		$prfx = array('http://', 'https://', '//', '/');
		reset( $prfx );
		foreach( $prfx as $prf ){
			if( substr($url, 0, strlen($prf)) == $prf ){
				$full = TRUE;
				break;
			}
		}
		return $full;
	}

	static function cache()
	{
		$return = new HC_Object_Cache();
		return $return;
	}

	static function link( $start = '', $params = array() )
	{
		$return = new HC_Link( $start, $params );
		return $return;
	}

	static function form()
	{
		$return = new HC_Form2;
		return $return;
	}

	static function time()
	{
		$return = new HC_Time;

		$conf = HC_App::app_conf();
		$disable_weekdays_conf = $conf->get('disable_weekdays');
		$return->set_disable_weekdays( $disable_weekdays_conf );

		return $return;
	}

	static function timezone()
	{
		$return = '';

		$ri = HC_Lib::ri();
		switch( $ri ){
			case 'wordpress':
				$return = get_option('timezone_string');
				if( ! strlen($return) ){
					$offset = get_option('gmt_offset');
					if( $offset ){
						$return = 'GMT';
						if( $offset > 0 ){
							$return .= '+' . $offset;
						}
						else {
							$return .= '-' . -$offset;
						}
					}
				}
				break;
		}

		if( ! strlen($return) ){
			$return = date_default_timezone_get();
		}
		if( ! strlen($return) ){
			$return = 'America/Los_Angeles';
		}

		return $return;
	}

	static function ob_start()
	{
		ob_start();
	}
	static function ob_end()
	{
		$return = ob_get_contents();
		ob_end_clean();
		return $return;
	}

	static function sort_array_by_array( $array, $orderArray )
	{
		$return = array();
		reset( $orderArray );
		foreach( $orderArray as $o ){
			if( in_array($o, $array) ){
				$return[] = $o;
			}
		}
		reset( $array );
		foreach( $array as $a ){
			if( ! in_array($a, $return) )
				$return[] = $a;
		}
		return $return;
	}

	static function ksort_array_by_array( $array, $orderArray )
	{
		$return = array();
		reset( $orderArray );
		foreach( $orderArray as $o ){
			if( array_key_exists($o, $array) ){
				$return[$o] = $array[$o];
			}
		}
		reset( $array );
		foreach( $array as $k => $k ){
			if( ! array_key_exists($k, $return) )
				$return[$k] = $v;
		}
		return $return;
	}

	static function get_color_brightness( $hex )
	{
		// strip off any leading #
		$hex = str_replace('#', '', $hex);

		$c_r = hexdec(substr($hex, 0, 2));
		$c_g = hexdec(substr($hex, 2, 2));
		$c_b = hexdec(substr($hex, 4, 2));

		$return = (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
		return $return;
	}
	
	static function random_html_color( $i, $bright = 0 )
	{
		$out = array(
			'#dcf174',
			'#0000dd',
			'#dd0000',
			'#7F5417',
			'#21B6A8',
			'#87907D',
			'#ec6d66',
			'#177F75',
			'#B6212D',
			'#B67721',
			'#da2d8b',
			'#FF8000',
			'#61e94c',
			'#FFAABF',
			// '#91C3DC',
			'#FFCC00',
			'#E5E0C1',
			'#68BD66',
			'#179CE8',
			// '#BBFF20',
			'#30769E',
			// '#FFE500',
			'#C8E9FC',
			'#758a09',
			'#00CCFF',
			'#FFC080',
			'#4086AA',

			'#FFAABF',
			'#0000AA',
			'#AA6363',
			'#AA9900',
			'#1A8BC0',
			'#ECF8FF',
			'#758a09',
			'#dd3100',
			'#dea04a',
			'#af2a30',
			'#EECC99',
			'#179999',
			'#a92e03',
			'#dd9cc9',
			'#f30320',
			'#579108',
			'#ce9135',
			'#acd622',
			'#e46e46',
			'#53747d',
			'#36a62a',
			'#83877e',
			'#e82385',
			'#73f2f2',
			'#cb9fa4',
			'#12c639',
			'#f51b2b',
			'#985d27',
			'#3595d5',
			'#cb9987',
			'#d52192',
			'#695faf',
			'#de2426',
			'#295d5a',
			'#824b2d',
			'#08ccf6',
			'#e82a3c',
			'#fcd11a',
			'#2b4c04',
			'#3011fd',
			'#1df37b',
			'#af2a30',
			'#c456d1',
			'#025df6',
			'#0ab24f',
			'#c0d962',
			'#62369f',
			'#73faa9',
			'#fb453c',
			'#0487a4',
			'#ce9e07',
			'#2b407e',
			'#c28551',
			);

		$out = array(
			'#dcedc8',
			'#ffcdd2',
			'#e1bee7',
			'#d1c4e9',
			'#bbdefb',
			'#b2dfdb',
			'#f0f4c3',
			'#ffe0b2',
			'#fff9c4',
			'#d7ccc8',
			'#cfd8dc',
			'#e57373',
			'#9575cd',
			'#64b5f6',
			'#81c784',
			'#ffb74d',
			'#ff8a65',
		);

		$out = array(
			'#FFB3A7',	// 1
			'#CBE86B',	// 2
			'#89C4F4',	// 3
			'#F5D76E',	// 4
			'#BE90D4',	// 5
			'#fcf13a',	// 6
			'#ffffbb',	// 7
			'#fbf',		// 8
			'#87D37C',	// 9
			'#FF8000',	// 12
			'#73faa9',	// 13
			'#C8E9FC',	// 14
			'#cb9987',	// 15
			'#cfd8dc',	// 16
			'#9b9',		// 17
			'#9bb',		// 18
			'#bbf',		// 19
			'#dcedc8',	// 20
		);

		/* filter brightness */
		if( 0 && $bright > 0 ){
			$new_out = array();
			foreach( $out as $o ){
				$this_brightness = HC_Lib::get_color_brightness( $o );
				if( $this_brightness > $bright ){
					$new_out[] = $o;
				}
			}
			$out = $new_out;
		}

		if( $i > count($out) ){
			$i = $i % count($out);
		}

		if( $i > 0 ){
			$return = $out[$i - 1];
		}
		else {
			$return = '#bbb';
		}

		return $return;
	}

	static function adjust_color_brightness( $hex, $steps )
	{
		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max(-255, min(255, $steps));

		// Normalize into a six character long hex string
		$hex = str_replace('#', '', $hex);
		if( strlen($hex) == 3 ){
			$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		}

		// Split into three parts: R, G and B
		$color_parts = str_split($hex, 2);
		$return = '#';

		foreach( $color_parts as $color ){
			$color = hexdec($color); // Convert to decimal
			$color = max(0,min(255,$color + $steps)); // Adjust color
			$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
		}
    return $return;
	}

	static function pick_random( $array, $many = 1 )
	{
		if( $many > 1 ){
			$return = array();
			$ids = array_rand($array, $many );
			foreach( $ids as $id )
				$return[] = $array[$id];
		}
		else {
			$id = array_rand($array);
			$return = $array[$id];
		}
		return $return;
	}

	static function list_files( $dirName, $extension = '' )
	{
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$files = array();
		foreach( $dirName as $thisDirName ){
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;
					if( is_file( $thisDirName . '/' . $f ) ){
						if( (! $extension ) || ( substr($f, - strlen($extension)) == $extension ) )
							$files[] = $f;
					}
				}
				closedir($handle);
			}
		}
		sort( $files );
		return $files;
	}

	static function list_recursive( $dirname )
	{
		$return = array();
		$this_subfolders = HC_Lib::list_subfolders( $dirname );
		foreach( $this_subfolders as $sf ){
			$subfolder_return = HC_Lib::list_recursive( $dirname . '/' . $sf );
			foreach( $subfolder_return as $sfr ){
				$return[] = $sf . '/' . $sfr;
			}
		}

		$this_files = HC_Lib::list_files( $dirname );
		$return = array_merge( $return, $this_files );
		return $return;
	}

	static function list_subfolders( $dirName )
	{
		if( ! is_array($dirName) )
			$dirName = array( $dirName );

		$return = array();
		reset( $dirName );
		foreach( $dirName as $thisDirName ){
			if ( file_exists($thisDirName) && ($handle = opendir($thisDirName)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;
					if( is_dir( $thisDirName . '/' . $f ) ){
						if( ! in_array($f, $return) )
							$return[] = $f;
					}
				}
				closedir($handle);
			}
		}

		sort( $return );
		return $return;
	}

	static function format_price( $amount, $calculated_price = '' )
	{
		$app_conf = HC_App::app_conf();

		$before_sign = $app_conf->get( 'currency_sign_before' );
		$currency_format = $app_conf->get( 'currency_format' );
		list( $dec_point, $thousand_sep ) = explode( '||', $currency_format );
		$after_sign = $app_conf->get( 'currency_sign_after' );

		$amount = number_format( $amount, 2, $dec_point, $thousand_sep );
		$return = $before_sign . $amount . $after_sign;

		if( strlen($calculated_price) && ($amount != $calculated_price) ){
			$calc_format = $before_sign . number_format( $calculated_price, 2, $dec_point, $thousand_sep ) . $after_sign;
			$return = $return . ' <span style="text-decoration: line-through;">' . $calc_format . '</span>';
		}
		return $return;
	}

	static function insert_after( $what, $array, $after )
	{
		$inserted = FALSE;
		$return = array();
		foreach( $array as $e ){
			$return[] = $e;
			if( $e == $after ){
				$return[] = $what;
				$inserted = TRUE;
			}
		}
		if( ! $inserted ){
			$return[] = $what;
		}
		return $return;
	}

	static function remove_from_array( $array, $what, $all = TRUE )
	{
		$return = $array;
		for( $ii = count($return) - 1; $ii >= 0; $ii-- ){
			if( $return[$ii] === $what ){
				array_splice( $return, $ii, 1 );
				if( ! $all ){
					break;
				}
			}
		}
		return $return;
	}

	static function debug( $text )
	{
		$fname = FCPATH . '/debug.txt';
		$text = $text . "\n";
		HC_Lib::file_set_contents( $fname, $text, TRUE );
	}

	static function file_get_contents( $fileName )
	{
		$content = join( '', file($fileName) );
		return $content;
	}

	static function file_set_contents( $fileName, $content, $append = FALSE )
	{
		$length = strlen( $content );
		$return = 1;

		if( $append ){
			if(! $fh = fopen($fileName, 'a') ){
				echo "can't open file <B>$fileName</B> for appending.";
				exit;
			}
		}
		else {
			if(! $fh = fopen($fileName, 'w') ){
				echo "can't open file <B>$fileName</B> for wrinting.";
				exit;
			}
			rewind( $fh );
		}
		$writeResult = fwrite($fh, $content, $length);
		if( $writeResult === FALSE )
			$return = 0;

		return $return;
	}

	static function parse_icon( $title, $add_fw = TRUE )
	{
		$icon_start = strpos( $title, '<i' );
		if( $icon_start !== FALSE )
		{
			$icon_end = strpos( $title, '</i>' ) + 4; 
			$link_icon = substr( $title, 0, $icon_end );
			$link_title = substr( $title, $icon_end );
		}
		else
		{
			$link_title = strip_tags( $title );
			$link_icon = '';
		}

		if( $link_icon && $add_fw )
		{
			$icon_class_start = strpos( $link_icon, 'class=' ) + 6;
			if( $icon_class_start !== FALSE )
			{
				$icon_start = substr( $link_icon, 0, $icon_class_start + 1 );
				$icon_end = substr( $link_icon, $icon_class_start + 1 );
				if( strpos($link_icon, 'fa-fw') === FALSE )
				{
					$link_icon = $icon_start . 'fa-fw ' . $icon_end;
				}
			}
		}

		$link_icon = trim( $link_icon );
		$return = array( $link_title, $link_icon );
		return $return;
	}

	static function replace_in_array( $array, $from, $to ){
		$return = array();
		foreach( $array as $item ){
			if( $item == $from )
				$return[] = $to;
			else
				$return[] = $item;
		}
		return $return;
	}

	static function parse_icon_old( $title, $add_fw = TRUE )
	{
		if( preg_match('/(\<i.+\>.*\<\/i\>\s*)(.+)/', $title, $ma) )
		{
			$link_title = $ma[2];
			$link_icon = $ma[1];
		}
		else
		{
			$link_title = strip_tags( $title );
			$link_icon = '';
		}

		if( $link_icon && $add_fw )
		{
			if( preg_match('/\<i.+class\=[\'\"](.+)[\'\"]\>\<\/i\>/', $title, $ma2) )
			{
				$class = $ma2[1];
				if( strpos($class, 'fa-fw') === FALSE )
				{
					$new_class = 'fa-fw ' . $class;
					$link_icon = str_replace( $class, $new_class, $link_icon );
				}
			}
		}

		$link_icon = trim( $link_icon );
		$return = array( $link_title, $link_icon );
		return $return;
	}
}
