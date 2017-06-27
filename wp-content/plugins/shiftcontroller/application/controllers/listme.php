<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
include_once( dirname(__FILE__) . '/list.php' );

class Listme_HC_Controller extends List_HC_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->rootlink = 'listme';
		$this->fix['staff'] = array( $this->auth->user()->id );
		// $this->fix['location'] = array( 0 );
		// $this->fix['location'] = NULL;
		$this->fix['filter'] = NULL;
		// $this->fix['tab'] = 'browse';
		// $this->default_params['range'] = 'upcoming';
	}
}