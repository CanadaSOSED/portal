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
 * CodeIgniter Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config {

	/**
	 * List of all loaded config values
	 *
	 * @var array
	 */
	var $config = array();
	/**
	 * List of all loaded config files
	 *
	 * @var array
	 */
	var $is_loaded = array();
	/**
	 * List of paths to search when trying to load a config file
	 *
	 * @var array
	 */
	var $_config_paths = array(APPPATH);

	/**
	 * Constructor
	 *
	 * Sets the $config data from the primary config.php file as a class variable
	 *
	 * @access   public
	 * @param   string	the config file name
	 * @param   boolean  if configuration values should be loaded into their own section
	 * @param   boolean  true if errors should just return false, false if an error message should be displayed
	 * @return  boolean  if the file was successfully loaded or not
	 */
	function __construct()
	{
		$this->config =& get_config();
		log_message('debug', "Config Class Initialized");

		// Set the base_url automatically if none was provided
		if ($this->config['base_url'] == '')
		{
			if (isset($_SERVER['HTTP_HOST']))
			{
				$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
				$base_url .= '://'. $_SERVER['HTTP_HOST'];
				$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
			}

			else
			{
				$base_url = 'http://localhost/';
			}

			$this->set_item('base_url', $base_url);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Load Config File
	 *
	 * @access	public
	 * @param	string	the config file name
	 * @param   boolean  if configuration values should be loaded into their own section
	 * @param   boolean  true if errors should just return false, false if an error message should be displayed
	 * @return	boolean	if the file was loaded correctly
	 */
	function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$file = ($file == '') ? 'config' : str_replace('.php', '', $file);
		$found = FALSE;
		$loaded = FALSE;

		$check_locations = defined('ENVIRONMENT')
			? array(ENVIRONMENT.'/'.$file, $file)
			: array($file);

		$these_files = array();
		foreach ($this->_config_paths as $path)
		{
			foreach ($check_locations as $location)
			{
				$file_path = $path.'config/'.$location.'.php';

				if (in_array($file_path, $this->is_loaded, TRUE))
				{
					$loaded = TRUE;
					continue 2;
				}

				if (file_exists($file_path))
				{
					$found = TRUE;
					$these_files[] = $file_path;
//					break;
				}
			}
		}

		if ($found === TRUE)
		{
			reset( $these_files );
			foreach( $these_files as $file_path )
			{
				if (in_array($file_path, $this->is_loaded, TRUE)){
					continue;
				}

				include($file_path);

				if ( ! isset($config) OR ! is_array($config))
				{
					if ($fail_gracefully === TRUE)
					{
						return FALSE;
					}
					show_error('Your '.$file_path.' file does not appear to contain a valid configuration array.');
				}

				if ($use_sections === TRUE)
				{
					if (! isset($this->config[$file]))
					{
						$this->config[$file] = array();
					}

					reset( $config );
					foreach( $config as $k => $v )
					{
						if( is_array($v) )
						{
							reset( $v );
							foreach( $v as $k2 => $v2 )
							{
								if (isset($this->config[$file][$k][$k2]))
								{
									

/*
echo "<h4>$k</h4>";
echo "config = <br>";
_print_r( $config );

echo "V = <br>";
_print_r( $v );

echo "V2 = <br>";
_print_r( $v2 );

echo "CURRENT VAL = <br>";
_print_r( $this->config[$file][$k][$k2] );
*/
									if( ! is_array($v2) ){
										if( ! is_array($this->config[$file][$k]) ){
											$this->config[$file][$k] = array( $this->config[$file][$k] );
										}
										$this->config[$file][$k][] = $v2;
/*
echo "SO FINAL VAL = <br>";
_print_r( $this->config[$file][$k] );
echo '<br>NEXT<br>';
*/
									}
									else {
										foreach( $v2 as $k3 => $v3 )
										{
											if( isset($this->config[$file][$k][$k2][$k3]) && is_array($v3) )
											{
												$this->config[$file][$k][$k2][$k3] = array_merge($this->config[$file][$k][$k2][$k3], $v3);
											}
											else
											{
												$this->config[$file][$k][$k2][$k3] = $v3;
											}
										}
	//									$this->config[$file][$k][$k2] = array_merge($this->config[$file][$k][$k2], $v2);
									}
								}
								else
								{
									$this->config[$file][$k][$k2] = $v2;
								}
							}
						}
						else
						{
							$this->config[$file][$k] = $v;
						}
					}
					//$this->config[$file] = array_merge($this->config[$file], $config);
				}
				else
				{
					$this->config = array_merge($this->config, $config);
				}

				$this->is_loaded[] = $file_path;
				unset($config);

				$loaded = TRUE;
				log_message('debug', 'Config file loaded: '.$file_path);
			}
		}

		if ($loaded === FALSE)
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.'.php does not exist.');
		}

		return TRUE;
	}

	function items( $index = '' )
	{
		$return = array();
		if ($index == '')
		{
			$keys = array_keys($this->config);
		}
		else
		{
			if ( ! isset($this->config[$index]))
			{
				return FALSE;
			}
			$keys = array_keys($this->config[$index]);
		}
		
		reset( $keys );
		foreach( $keys as $k )
		{
			$return[ $k ] = $this->item( $k, $index );
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item
	 *
	 *
	 * @access	public
	 * @param	string	the config item name
	 * @param	string	the index name
	 * @param	bool
	 * @return	string
	 */
	function item($item, $index = '')
	{
		if ($index == '')
		{
			if ( ! isset($this->config[$item]))
			{
				return FALSE;
			}

			$pref = $this->config[$item];
		}
		else
		{
			if ( ! isset($this->config[$index]))
			{
				return FALSE;
			}

			if ( ! isset($this->config[$index][$item]))
			{
				return FALSE;
			}

			$pref = $this->config[$index][$item];
		}

		return $pref;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item - adds slash after item (if item is not empty)
	 *
	 * @access	public
	 * @param	string	the config item name
	 * @param	bool
	 * @return	string
	 */
	function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return FALSE;
		}
		if( trim($this->config[$item]) == '')
		{
			return '';
		}

		return rtrim($this->config[$item], '/').'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Site URL
	 * Returns base_url . index_page [. uri_string]
	 *
	 * @access	public
	 * @param	string	the URI string
	 * @return	string
	 */
	function site_url($uri = '')
	{
		if ($uri == '')
		{
			return $this->slash_item('base_url').$this->item('index_page');
		}

		if ($this->item('enable_query_strings') == FALSE)
		{
			$suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
			return $this->slash_item('base_url').$this->slash_item('index_page').$this->_uri_string($uri).$suffix;
		}
		else
		{
			return $this->slash_item('base_url').$this->item('index_page').'?'.$this->_uri_string($uri);
		}
	}

	// -------------------------------------------------------------

	/**
	 * Base URL
	 * Returns base_url [. uri_string]
	 *
	 * @access public
	 * @param string $uri
	 * @return string
	 */
	function base_url($uri = '')
	{
		return $this->slash_item('base_url').ltrim($this->_uri_string($uri), '/');
	}

	function uri_string($uri = '')
	{
		return $this->_uri_string( $uri );
	}

	// -------------------------------------------------------------

	/**
	 * Build URI string for use in Config::site_url() and Config::base_url()
	 *
	 * @access protected
	 * @param  $uri
	 * @return string
	 */
	protected function _uri_string($uri)
	{
		if ($this->item('enable_query_strings') == FALSE)
		{
			if (is_array($uri))
			{
				$uri = implode('/', $uri);
			}
			$uri = trim($uri, '/');
		}
		else
		{
			if (is_array($uri))
			{
				$i = 0;
				$str = '';
				foreach ($uri as $key => $val)
				{
					$prefix = ($i == 0) ? '' : '&';
					$str .= $prefix.$key.'='.$val;
					$i++;
				}
				$uri = $str;
			}
		}
	    return $uri;
	}

	// --------------------------------------------------------------------

	/**
	 * System URL
	 *
	 * @access	public
	 * @return	string
	 */
	function system_url()
	{
		$x = explode("/", preg_replace("|/*(.+?)/*$|", "\\1", BASEPATH));
		return $this->slash_item('base_url').end($x).'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Set a config file item
	 *
	 * @access	public
	 * @param	string	the config item key
	 * @param	string	the config item value
	 * @return	void
	 */
	function set_item($item, $value, $index = '' )
	{
		if ($index == '')
		{
			$this->config[$item] = $value;
		}
		else
		{
			$this->config[$index][$item] = $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Assign to Config
	 *
	 * This function is called by the front controller (CodeIgniter.php)
	 * after the Config class is instantiated.  It permits config items
	 * to be assigned or overriden by variables contained in the index.php file
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _assign_to_config($items = array())
	{
		if (is_array($items))
		{
			foreach ($items as $key => $val)
			{
				$this->set_item($key, $val);
			}
		}
	}

	public function get_modules()
	{
		$return = array();

		$modules = $this->item('modules');
		if( ! is_array($modules) ){
			return $return;
		}

		reset( $modules );
		foreach( $modules as $name => $value ){
			if( ! is_string($name) ){
				$name = $value;
			}
			$return[] = $name;
		}
		return $return;
	}

	public function look_in_dirs()
	{
		static $return = NULL;

		if( $return === NULL ){
			$modules = $this->get_modules();
			$modules_locations = $this->item('modules_locations');

			$return = array();
			$return[] = NTS_SYSTEM_APPPATH;
			$return[] = APPPATH;

			if( is_array($modules) ){
				reset($modules);
				$modules2 = $modules;
				foreach( $modules as $module ){
					reset( $modules_locations );
					foreach( $modules_locations as $ml ){
						$mod_dir = $ml . $module;
						if( file_exists($mod_dir) ){
							$return[] = $mod_dir;

						/* also add path for config files for modules within modules */
							reset( $modules2 );
							foreach( $modules2 as $module2 ){
								if( $module2 == $module ){
									continue;
								}
								$mod2_dir = $mod_dir . '/modules/' . $module2;
								if( file_exists($mod2_dir) ){
									$return[] = $mod2_dir;
								}
							}
						}
					}
				}
			}
		}
		return $return;
	}
}

// END CI_Config class

/* End of file Config.php */
/* Location: ./system/core/Config.php */
