<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Todo_Admin_HC_Controller extends _Backend_HC_controller
{
	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_MANAGER );
	}

	function index()
	{
	/* final layout */
		$this->layout->set_partial(
			'content',
			$this->render(
				'admin/todo/index',
				array(
					)
				)
			);
		$this->layout();
	}

	public function pending_timeoffs()
	{
		$t = HC_Lib::time();
		$today = $t->setNow()->formatDate_Db();

		$shifts = HC_App::model('shift');
		$shifts
			->where('date_end >=',	$today)
			->where('status',		$shifts->_const('STATUS_DRAFT'))
			->where('type',			$shifts->_const('TYPE_TIMEOFF'))
			;
		$shifts->get();
		$acl = HC_App::acl();

		$count = 0;
		foreach( $shifts as $obj ){
			// if( ! $acl->set_object($obj)->can('view') ){
				// continue;
			// }
			$count++;
		}

	/* view */
		$this->layout->set_partial(
			'content',
			$this->render(
				'admin/todo/pending_timeoffs',
				array(
					'count'	=> $count
					)
				)
			);
		$this->layout();
	}

	/* draft upcoming shifts */
	function draft()
	{
		$t = HC_Lib::time();
		$today = $t->setNow()->formatDate_Db();

		$shifts = HC_App::model('shift');
		$shifts
			// ->where('date_end >=',	$today)
			->where('status',		$shifts->_const('STATUS_DRAFT'))
			->where('type',			$shifts->_const('TYPE_SHIFT'))
			;
		$shifts->get();

		$acl = HC_App::acl();

		$count = 0;
		foreach( $shifts as $obj ){
			if( ! $acl->set_object($obj)->can('view') ){
				continue;
			}
			$count++;
		}

	/* view */
		$this->layout->set_partial(
			'content',
			$this->render(
				'admin/todo/draft',
				array(
					'count'	=> $count
					)
				)
			);
		$this->layout();
	}

	/* open, active, upcoming shifts */
	function open()
	{
		$t = HC_Lib::time();
		$today = $t->setNow()->formatDate_Db();

		$shifts = HC_App::model('shift');
		$shifts
			->where('date_end >=',	$today)
			->where('status',		$shifts->_const('STATUS_ACTIVE'))
			->where_related('user', 'id', NULL, TRUE)
			;
		$shifts->get();

		$acl = HC_App::acl();

		$count = 0;
		foreach( $shifts as $obj ){
			if( ! $acl->set_object($obj)->can('view') ){
				continue;
			}
			$count++;
		}

	/* view */
		$this->layout->set_partial(
			'content',
			$this->render(
				'admin/todo/open',
				array(
					'count'	=> $count
					)
				)
			);
		$this->layout();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */