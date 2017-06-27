<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$app = $this->config->item('nts_app');

$route['default_controller'] = isset($GLOBALS['NTS_CONFIG'][$app]['DEFAULT_CONTROLLER']) ? $GLOBALS['NTS_CONFIG'][$app]['DEFAULT_CONTROLLER'] : 'dispatcher';
$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */	