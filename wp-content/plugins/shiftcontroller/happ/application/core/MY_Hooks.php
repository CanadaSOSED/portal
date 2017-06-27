<?php
class MY_Hooks extends CI_Hooks
{
	// --------------------------------------------------------------------

	/**
	 * Initialize the Hooks Preferences
	 *
	 * @access	private
	 * @return	void
	 */
	function _initialize()
	{
		$CFG =& load_class('Config', 'core');

		// If hooks are not enabled in the config file
		// there is nothing else to do

		if ($CFG->item('enable_hooks') == FALSE){
			return;
		}

		$look_in_dirs = $CFG->look_in_dirs();

		// Grab the "hooks" definition file.
		// If there are no hooks, we're done.

		if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/hooks.php')){
		    include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
		}
		else {
			$hook_files = array(
				NTS_SYSTEM_APPPATH.'config/hooks.php',
				APPPATH.'config/hooks.php'
				);

			reset( $look_in_dirs );
			foreach( $look_in_dirs as $dir ){
				$hf = $dir . '/config/hooks.php';
				if( file_exists($hf) ){
					require($hf);
				}
			}
		}

		if ( ! isset($hook) OR ! is_array($hook)){
			return;
		}

		$this->hooks =& $hook;
		$this->enabled = TRUE;
	}

	function _call_hook($which = '')
	{
		if ( ! $this->enabled OR ! isset($this->hooks[$which])){
			return FALSE;
		}

		if (isset($this->hooks[$which][0]) ){
			foreach ($this->hooks[$which] as $val){
				$this->_run_hook($val);
			}
		}
		else {
			$this->_run_hook($this->hooks[$which]);
		}

		return TRUE;
	}

	function _run_hook($data)
	{
		if( is_callable($data) ){
			call_user_func_array($data, array());
		}
		else {
			return parent::_run_hook($data);
		}
	}
}