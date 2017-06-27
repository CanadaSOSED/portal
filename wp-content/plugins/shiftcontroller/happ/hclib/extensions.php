<?php
class HC_Extensions
{
	private $dirs = array();
	private $extensions = array();
	private $skip = array();

	protected function __construct()
	{
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new HC_Extensions();
		}
		return $instance;
	}

	public function add_dir( $dir )
	{
		if( ! in_array($dir, $this->dirs) ){
			$this->dirs[] = $dir;
		}
	}

	public function init()
	{
		$extensions = array();
		reset( $this->dirs );
		foreach( $this->dirs as $dir ){
			$file = $dir . '/config/extensions.php';
			/* should define the $extensions array */
			if( file_exists($file) ){
				require( $file );
			}
		}
		$this->extensions = $extensions;
	}

	public function has( $which )
	{
		$return = FALSE;
		if( is_array($which) ){
			if( isset($this->extensions[$which[0]][$which[1]]) ){
				$return = TRUE;
			}
		}
		else {
			if( isset($this->extensions[$which]) ){
				$return = TRUE;
			}

			/* contains wildcard */
			reset( $this->extensions );
			foreach( array_keys($this->extensions) as $ext_key ){
				$star_pos = strpos($ext_key, '*');
				if( $star_pos === FALSE ){
					continue;
				}
				$ext_prefix = substr( $ext_key, 0, $star_pos );
				if( substr($which, 0, strlen($ext_prefix)) == $ext_prefix ){
					$return = TRUE;
				}
			}
		}
		return $return;
	}

	public function set_skip( $skip = array() )
	{
		$this->skip = $skip;
		return $this;
	}
	public function skip()
	{
		return $this->skip;
	}

	public function extensions( $which ){
		$return = isset($this->extensions[$which]) ? array_keys($this->extensions[$which]) : array();
		return $return;
	}

	public function run( $which )
	{
		$return = array();
		$skip = $this->skip();
		$this->set_skip( array() );

		$params = func_get_args();
		$which = array_shift( $params );

		if( ! $this->has($which) ){
			return $return;
		}

		$calling_parent = '';
		if( is_array($which) ){
			$this_extensions = $this->extensions[$which[0]][$which[1]];
			if( isset($which[2]) ){
				$calling_parent = $which[2];
			}
		}
		elseif( isset($this->extensions[$which]) ){
			$this_extensions = $this->extensions[$which];
		}
		else {
			$this_extensions = array();
			/* contains wildcard */
			reset( $this->extensions );
			foreach( array_keys($this->extensions) as $ext_key ){
				$star_pos = strpos($ext_key, '*');
				if( $star_pos === FALSE ){
					continue;
				}
				$ext_prefix = substr( $ext_key, 0, $star_pos );
				if( substr($which, 0, strlen($ext_prefix)) == $ext_prefix ){
					$this_extensions = array_merge( $this_extensions, $this->extensions[$ext_key] );
				}
			}
		}

		$run_array = TRUE;
		if( ! is_array($this_extensions) ){
			$this_extensions = array( $this_extensions );
			$run_array = FALSE;
		}

		foreach( $this_extensions as $hk => $hinfo ){
			if( in_array($hk, $skip) ){
				continue;
			}

			if( $hinfo === NULL ){
				if( $run_array ){
					$return[$hk] = NULL;
				}
				else {
					$return = NULL;
				}
				continue;
			}

			/* hinfo is a path to module */
			// Modules::run( $hinfo, $model)
			if( ! is_array($hinfo) ){
				$hinfo = array($hinfo);
			}

			// if substitute
			if( strpos($hinfo[0], '#') !== FALSE ){
				if( count($params) && (count($params) % 2) ){
					$substitute = array_shift($params);
					if( $substitute != 'index' ){
						$substitute = $substitute . '/index';
					}
				}
				else {
					if( isset($params[1]) && ($params[1] == 'index') ){
						$substitute = array();
						$substitute[] = array_shift($params);
						$substitute[] = array_shift($params);
						$substitute = join('/', $substitute);
					}
					else {
						$substitute = 'index';
					}
				}
				// if( $substitute != 'index' ){
					// $substitute = $substitute . '/index';
				// }
// echo "SUBSTITUTE FOR '$substitute'<br>";
				$hinfo[0] = str_replace('#', $substitute, $hinfo[0]);
			}

			if( $calling_parent ){
				$hinfo[0] = array( $hinfo[0], $calling_parent );
			}

			$this_params = array_merge( $hinfo, $params );

			$this_return = call_user_func_array( 'Modules::run', $this_params );

			if( is_string($this_return) ){
				if( strlen($this_return) ){
					if( $run_array ){
						$return[$hk] = $this_return;
					}
					else {
						$return = $this_return;
					}
				}
			}
			else {
				if( $run_array ){
					$return[$hk] = $this_return;
				}
				else {
					$return = $this_return;
				}
			}
		}
		return $return;
	}
}
