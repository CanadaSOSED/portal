<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ical_HC_Controller extends _Front_HC_Controller
{
	function index()
	{
		$timezone = HC_Lib::timezone();
		$current_user = $this->auth->user();

	/* render view */
		$this->layout->set_partial(
			'content', 
			$this->render( 
				'ical/index',
				array(
					'timezone'		=> $timezone,
					'current_user'	=> $current_user,
					)
				)
			);

		$this->layout();
	}

	function export( $token = '', $uid = '' )
	{
		// get user by token
		$current_user = HC_App::model('user');
		$current_user->default_order_by = NULL; // to speed up a bit

		if( strlen($token) ){
			$current_user
				->where('token', $token)
				->limit(1)
				;
			$current_user->get();

			if( ! $current_user->exists() ){
				echo 'user not found!';
				exit;
			}
		}
		else {
			$current_user->get_by_id(0);
		}

	/* 1 month before and 3 months after */
		$t = HC_Lib::time();
		$t->setNow();
		$t->modify('-1 month');
		$start_date = $t->formatDate_Db();

		$t->setNow();
		$t->modify('+3 months');
		$end_date = $t->formatDate_Db();

		$state = array();
		// $state['range'] = 'upcoming';
		// $state['date'] = $start_date;

		$state['range'] = 'custom';
		$state['date'] = $start_date . '_' . $end_date;

		if( $current_user->level < $current_user->_const("LEVEL_MANAGER") ){
			$uid = $current_user->id;
		}

		if( $uid ){
			$state['staff'] = $uid;
		}

		$model = HC_App::model('shift');
		$shifts = $model->load( $state );

		$acl = HC_App::acl();
		$shifts = $acl
			->set_user($current_user)
			->filter( $shifts, 'view' );

		$ical = HC_App::model('ical');
		foreach( $shifts as $sh ){
			$ical->add( $sh );
		}

		$this->output->set_content_type('text/calendar');
		// echo nl2br( $ical->print_out() );
		echo $ical->print_out();
		exit;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */