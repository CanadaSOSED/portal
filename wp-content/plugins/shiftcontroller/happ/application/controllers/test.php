<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test_HC_controller extends MY_HC_Controller {
//class Test_HC_controller extends MY_HC_Controller {
	function __construct()
	{
		parent::__construct();
		if( defined('NTS_DEVELOPMENT') ){
			$this->output->enable_profiler(TRUE);
		}
	}

	function index()
	{
		$count = 100000;
		for( $ii = 1; $ii <= $count; $ii++ ){
			$div = HC_Html_Factory::element('div');
			$div->render();
		}

/*
		$div = HC_Html_Factory::element('div');
		for( $ii = 1; $ii <= $count; $ii++ ){
			$div2 = clone $div;
		}
*/
		echo 'this is a test';
	}
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */