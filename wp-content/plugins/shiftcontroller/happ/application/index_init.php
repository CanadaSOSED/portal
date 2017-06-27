<?php
define( 'NTS_SYSTEM_APPPATH', dirname(__FILE__) . '/../application/' );
$system_path = dirname(__FILE__) . '/../system';

$application_folder = $GLOBALS['NTS_APPPATH'] . '/../application';
$INDEX_FILE = $GLOBALS['NTS_APPPATH'] . '/../index.php';

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

// Set the current directory correctly for CLI requests
if (defined('STDIN'))
{
	chdir(dirname($INDEX_FILE));
}

if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path).'/';
}

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/').'/';

// Is the system path correct?
if ( ! is_dir($system_path))
{
	exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo($INDEX_FILE, PATHINFO_BASENAME));
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo($INDEX_FILE, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", $system_path));

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', $INDEX_FILE));

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

// The path to the "application" folder
if (is_dir($application_folder))
{
	define('APPPATH', $application_folder.'/');
}
else
{
	if ( ! is_dir(BASEPATH.$application_folder.'/'))
	{
		exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
	}

	define('APPPATH', BASEPATH.$application_folder.'/');
}

/* HCLIB */
include_once( NTS_SYSTEM_APPPATH . '/../hclib/_bootstrap.php' );

/* --------------------------------------------------------------------
 * LOAD THE DATAMAPPER BOOTSTRAP FILE
 * --------------------------------------------------------------------
 */
require_once NTS_SYSTEM_APPPATH.'third_party/datamapper/bootstrap.php';

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
// require_once BASEPATH.'core/CodeIgniter_.php';
require_once BASEPATH.'core/CodeIgniter_init.php';

hc_ci_do_init();

/* End of file index.php */
/* Location: ./index.php */