<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * System Initialization File
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Front-controller
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

/**
 * CodeIgniter Version
 *
 * @var string
 *
 */
	define('CI_VERSION', '2.1.4');

/**
 * CodeIgniter Branch (Core = TRUE, Reactor = FALSE)
 *
 * @var boolean
 *
 */
	define('CI_CORE', FALSE);

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
	require(BASEPATH.'core/Common.php');

/*
 * ------------------------------------------------------
 *  Load the framework constants
 * ------------------------------------------------------
 */
	if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/constants.php'))
	{
		require(APPPATH.'config/'.ENVIRONMENT.'/constants.php');
	}
	else
	{
		$constants_files = array(
			NTS_SYSTEM_APPPATH.'config/constants.php',
			APPPATH.'config/constants.php'
			);
		reset( $constants_files );
		foreach( $constants_files as $cf )
		{
			if( file_exists($cf) )
			{
				require($cf);
			}
		}
	}

/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
	// set_error_handler('_exception_handler');

	if ( ! is_php('5.3'))
	{
		@set_magic_quotes_runtime(0); // Kill magic quotes
	}

/*
 * ------------------------------------------------------
 *  Set the subclass_prefix
 * ------------------------------------------------------
 *
 * Normally the "subclass_prefix" is set in the config file.
 * The subclass prefix allows CI to know if a core class is
 * being extended via a library in the local application
 * "libraries" folder. Since CI allows config items to be
 * overriden via data set in the main index. php file,
 * before proceeding we need to know if a subclass_prefix
 * override exists.  If so, we will set this value now,
 * before any classes are loaded
 * Note: Since the config file data is cached it doesn't
 * hurt to load it here.
 */
	if (isset($assign_to_config['subclass_prefix']) AND $assign_to_config['subclass_prefix'] != '')
	{
		get_config(array('subclass_prefix' => $assign_to_config['subclass_prefix']));
	}

/*
 * ------------------------------------------------------
 *  Set a liberal script execution time limit
 * ------------------------------------------------------
 */
	if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
	{
		@set_time_limit(300);
	}

/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock...
 * ------------------------------------------------------
 */
	$BM =& load_class('Benchmark', 'core');
	$GLOBALS['BM'] =& $BM;

	$BM->mark('total_execution_time_start');
	$BM->mark('loading_time:_base_classes_start');

/*
 * ------------------------------------------------------
 *  Instantiate the hooks class
 * ------------------------------------------------------
 */
	$EXT =& load_class('Hooks', 'core');
	$GLOBALS['EXT'] =& $EXT;

/*
 * -----------------------------------------------------
 *  Is there a "pre_system" hook?
 * ------------------------------------------------------
 */
	$EXT->_call_hook('pre_system');

/*
 * ------------------------------------------------------
 *  Instantiate the config class
 * ------------------------------------------------------
 */
	$CFG =& load_class('Config', 'core');
	$GLOBALS['CFG'] =& $CFG;

	// Do we have any manually set config items in the index.php file?
	if (isset($assign_to_config))
	{
		$CFG->_assign_to_config($assign_to_config);
	}

/*
 * ------------------------------------------------------
 *  Instantiate the UTF-8 class
 * ------------------------------------------------------
 *
 * Note: Order here is rather important as the UTF-8
 * class needs to be used very early on, but it cannot
 * properly determine if UTf-8 can be supported until
 * after the Config class is instantiated.
 *
 */

	$UNI =& load_class('Utf8', 'core');
	$GLOBALS['UNI'] = $UNI;

/*
 * ------------------------------------------------------
 *  Instantiate the URI class
 * ------------------------------------------------------
 */
	$URI =& load_class('URI', 'core');

/*
 * ------------------------------------------------------
 *  Instantiate the routing class and set the routing
 * ------------------------------------------------------
 */

	$RTR =& load_class('Router', 'core');
	$RTR->_set_routing();

	// Set any routing overrides that may exist in the main index file
	if (isset($routing))
	{
		$RTR->_set_overrides($routing);
	}

/*
 * ------------------------------------------------------
 *  Instantiate the output class
 * ------------------------------------------------------
 */
	$OUT =& load_class('Output', 'core');
	$GLOBALS['OUT'] = $OUT;

/*
 * ------------------------------------------------------
 *	Is there a valid cache file?  If so, we're done...
 * ------------------------------------------------------
 */
	if ($EXT->_call_hook('cache_override') === FALSE)
	{
		if ($OUT->_display_cache($CFG, $URI) == TRUE)
		{
			exit;
		}
	}

/*
 * -----------------------------------------------------
 * Load the security class for xss and csrf support
 * -----------------------------------------------------
 */
	$SEC =& load_class('Security', 'core');
	$GLOBALS['SEC'] = $SEC;

/*
 * ------------------------------------------------------
 *  Load the Input class and sanitize globals
 * ------------------------------------------------------
 */
	$IN	=& load_class('Input', 'core');

/*
 * ------------------------------------------------------
 *  Load the Language class
 * ------------------------------------------------------
 */
	// don't ned that
// $LANG =& load_class('Lang', 'core');

/*
 * ------------------------------------------------------
 *  Load the app controller and local controller
 * ------------------------------------------------------
 *
 */

	// Load the base controller class
	require BASEPATH.'core/Controller.php';

	function &ci_get_instance()
	{
		// return CI::$APP;
		return CI_Controller::ci_get_instance();
	}

	$happ_core_dir = NTS_SYSTEM_APPPATH . 'core/';

	$base_controller_file = $happ_core_dir . $CFG->config['subclass_prefix'] . 'BaseController.php';
	$my_happ_controller_file = $happ_core_dir . $CFG->config['subclass_prefix'] . 'Controller.php';
	$my_controller_file = APPPATH.'core/'.$CFG->config['subclass_prefix'].'Controller.php';

	if( file_exists($base_controller_file) ){
		require( $base_controller_file );
	}
	if( file_exists($my_controller_file) ){
		require( $my_controller_file );
	}
	// else {
	elseif( file_exists($my_happ_controller_file) ) {
		require( $my_happ_controller_file );
	}

	include_once( $happ_core_dir . 'Front_controller.php' );
	include_once( $happ_core_dir . 'Backend_controller.php' );

	// Load the local application controller
	// Note: The Router class automatically validates the controller path using the router->_validate_request().
	// If this include fails it means that the default controller in the Routes.php file is not resolving to something valid.
//	$controller_file = APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().'.php';
	
	if( $RTR->fetch_module() )
	{
		$controller_file = $RTR->fetch_directory().$RTR->controller_file_name().'.php';
	}
	else
	{
		$controller_file = APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->controller_file_name().'.php';
	}

	if ( ! file_exists($controller_file))
	{
		// try system path
		$controller_file = NTS_SYSTEM_APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->controller_file_name().'.php';
		if ( ! file_exists($controller_file))
		{
			show_error("$controller_file<br>" . 'Unable to load your default controller. Please make sure the controller specified in your Routes.php file is valid.');
		}
	}

	include($controller_file);

	// Set a mark point for benchmarking
	$BM->mark('loading_time:_base_classes_end');

