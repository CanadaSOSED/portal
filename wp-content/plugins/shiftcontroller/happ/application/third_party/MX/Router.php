<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX core module class */
require dirname(__FILE__).'/Modules.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter router class.
 *
 * Install this file as application/third_party/MX/Router.php
 *
 * @copyright	Copyright (c) 2011 Wiredesignz
 * @version 	5.4
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MX_Router extends CI_Router
{
	protected $module;
	
	public function fetch_module() {
		return $this->module;
	}
	
	public function _validate_request($segments) {
		if (count($segments) == 0) return $segments;
		/* locate module controller */
		if ($located = $this->locate($segments))
		{
			return $located;
		}

		/* use a default 404_override controller */
		if (isset($this->routes['404_override']) AND $this->routes['404_override']) {
			$segments = explode('/', $this->routes['404_override']);
			if ($located = $this->locate($segments)) return $located;
		}

		/* no controller found */
		show_404();
	}
	
	/** Locate the controller **/
	public function locate($segments) {
		$this->module = '';
		$this->directory = '';
		$this->reldirectory = '';
		$ext = EXT;

		/* use module route if available */
		if (isset($segments[0]) AND $routes = Modules::parse_routes($segments[0], implode('/', $segments))) {
			$segments = $routes;
		}

		/* get the segments array elements */
		list($module, $directory, $controller) = array_pad($segments, 3, NULL);

		/* check modules */
		foreach( Modules::$locations as $location => $offset ){
			/* module exists? */
			if( is_dir($source = $location.$module.'/controllers/') ){
				$this->module = $module;
				$this->directory = $offset.$module.'/controllers/';
				$this->reldirectory = '';

				$check_file = $source.$directory.'/';
// echo "CHECK FILE 22 '$check_file'<br>";
				/* module sub-directory exists? */
				if( $directory AND is_dir($check_file) ){
					$source = $source.$directory.'/'; 

					$this->reldirectory .= $directory.'/';
					$this->directory .= $directory.'/';

					/* module sub-directory controller exists? */
					if( is_file($source.$directory.$ext) ){
						$add_to_controller = join('/', array_slice($segments, 0, 1));
						$return = array_slice($segments, 1);
						$return[0] = $add_to_controller . '/'. $return[0];
						return $return;
					}

					/* module sub-directory sub-controller exists? */
					if( $controller AND is_file($source.$controller.$ext) ){
						$add_to_controller = join('/', array_slice($segments, 0, 2));
						$return = array_slice($segments, 2);
						$return[0] = $add_to_controller . '/'. $return[0];
						return $return;
					}
				}

			/* module sub-controller exists? */
				$check_file = $source.$directory.$ext;
// echo "CHECK FILE 11 '$check_file'<br>";
				if( $directory AND is_file($check_file) ){
					$add_to_controller = join('/', array_slice($segments, 0, 1));
					$return = array_slice($segments, 1);
					$return[0] = $add_to_controller . '/'. $return[0];
					return $return;
				}


				/* module controller exists? */
				if(is_file($source.$module.$ext)) {
					return $segments;
				}
			}
		}

	/* APP PATH */
		/* application controller exists? */
		$check_file = APPPATH.'controllers/'.$module.$ext;
// echo "CHECK FILE 1 '$check_file'<br>";
		if (is_file($check_file)) {
			return $segments;
		}

		if( $directory && $controller ){
			$check_file = APPPATH.'controllers/'.$module.'/'.$directory.'/'.$controller.$ext;
// echo "CHECK FILE 3 '$check_file'<br>";
			if( is_file($check_file) ){
				$this->directory = $module.'/'.$directory.'/';

				$add_to_controller = join('/', array_slice($segments, 0, 2));
				$return = array_slice($segments, 2);
				$return[0] = $add_to_controller . '/'. $return[0];
				return $return;
			}
		}

		/* application sub-directory controller exists? */
		if( $directory ){
			$check_file = APPPATH.'controllers/'.$module.'/'.$directory.$ext;
// echo "CHECK FILE 2 '$check_file'<br>";
			if( is_file($check_file) ){
				$this->directory = $module.'/';

				$add_to_controller = join('/', array_slice($segments, 0, 1));
				$return = array_slice($segments, 1);
				$return[0] = $add_to_controller . '/'. $return[0];
				return $return;
			}
		}

		/* application sub-directory default controller exists? */
		$check_file = APPPATH.'controllers/'.$module.'/'.$this->default_controller.$ext;
// echo "CHECK FILE 4 '$check_file'<br>";
		if( is_file($check_file) ){
			$this->directory = $module.'/';
			return array($this->default_controller);
		}

	/* SYSTEM PATH */
		/* application controller exists? */
		$check_file = NTS_SYSTEM_APPPATH.'controllers/'.$module.$ext;
// echo "CHECK FILE 5 '$check_file'<br>";
		if (is_file($check_file)) {
			return $segments;
		}

		if( $directory && $controller ){
// echo "CHECK FILE 7 '$check_file'<br>";
			$check_file = NTS_SYSTEM_APPPATH.'controllers/'.$module.'/'.$directory.'/'.$controller.$ext;
			if( is_file($check_file) ){
				$this->directory = $module.'/'.$directory.'/';

				$add_to_controller = join('/', array_slice($segments, 0, 2));
				$return = array_slice($segments, 2);
				$return[0] = $add_to_controller . '/'. $return[0];
				return $return;
			}
		}

		/* application sub-directory controller exists? */
		if( $directory ){
// echo "CHECK FILE 6 '$check_file'<br>";
			$check_file = NTS_SYSTEM_APPPATH.'controllers/'.$module.'/'.$directory.$ext;
			if( is_file($check_file) ){
				$this->directory = $module.'/';

				$add_to_controller = join('/', array_slice($segments, 0, 1));
				$return = array_slice($segments, 1);
				$return[0] = $add_to_controller . '/'. $return[0];
				return $return;
			}
		}

		/* application sub-directory default controller exists? */
		$check_file = NTS_SYSTEM_APPPATH.'controllers/'.$module.'/'.$this->default_controller.$ext;
// echo "CHECK FILE 8 '$check_file'<br>";
		if (is_file($check_file)) {
			$this->directory = $module.'/';
			return array($this->default_controller);
		}
	}

	public function set_class($class) {
		$this->class = $class . $this->config->item('controller_suffix');
		// if( $this->module )
			// $this->class = $this->module . '_' . $this->class;
	}
}