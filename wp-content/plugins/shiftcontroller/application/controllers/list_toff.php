<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
include_once( dirname(__FILE__) . '/list.php' );

class List_Toff_HC_Controller extends List_HC_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->rootlink = 'list_toff';

		$temp_shift = HC_App::model('shift');
		// $this->fix['staff'] = array( $this->auth->user()->id );
		// $this->fix['location'] = array( 0 );
		// $this->fix['filter'] = NULL;
		$this->fix['type'] = array($temp_shift->_const('TYPE_TIMEOFF'));
		$this->fix['tab'] = 'browse';
		$this->default_params['range'] = 'upcoming';
		$this->default_params['by'] = '';
	}
}