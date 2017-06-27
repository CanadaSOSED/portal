<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class List_Conflicts_HC_controller extends _Front_HC_controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function filter( $what = 'label', $shifts = NULL )
	{
		switch( $what ){
			case 'label':
				return $this->filter_label();
				break;
			case 'pre':
				return $this->filter_pre($shifts);
				break;
			case 'post':
				return $this->filter_post($shifts);
				break;
		}
	}

	function filter_label()
	{
		return $this->render(
			'conflicts/filter_label'
			);
	}

	function filter_pre( $shifts )
	{
		return $shifts;
	}

	function filter_post( $shifts )
	{
		$return = array();
		$cmm = HC_App::model('conflict_manager');
		$acl = HC_App::acl();

		$show_this = FALSE;
		$count_ok = 0;
		$count_fail = 0;

		foreach( $shifts as $obj ){
			if( $obj->start === NULL ){
				continue;
			}

			if( ! $acl->set_object($obj)->can('conflicts_view') ){
				continue;
			}

			$entries = $cmm->get($obj, TRUE);
			if( $entries ){
				$return[] = $obj;
			}
		}

		return $return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */