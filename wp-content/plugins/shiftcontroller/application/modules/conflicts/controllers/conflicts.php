<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Conflicts_HC_Controller extends _Front_HC_Controller
{
	function todo()
	{
		$conflict_ids = array();
		$t = HC_Lib::time();
		$today = $t->setNow()->formatDate_Db();

	/* find conflicts in upcoming shifts */
		$shifts = HC_App::model('shift');
		$shifts
			->where('date_end >=',	$today)
			// ->where('status',	$shifts->_const('STATUS_ACTIVE')
			->where_related('user', 'id IS NOT ', 'NULL', FALSE)
			;
		$shifts->get();

		$acl = HC_App::acl();
		$cmm = HC_App::model('conflict_manager');

		$count = 0;
		foreach( $shifts as $obj ){
			if( ! $acl->set_object($obj)->can('conflicts_view') ){
				continue;
			}

			$entries = $cmm->get($obj, TRUE);
			if( $entries ){
				foreach( $entries as $e ){
					$conflict_ids[$e->id] = 1;
				}
			}
		}

		if( ! count($conflict_ids) ){
			return;
		}

	/* render view */
		$this->layout->set_partial(
			'content', 
			$this->render( 
				'conflicts/todo',
				array(
					'count'	=> count($conflict_ids),
					)
				)
			);

		$this->layout();
	}

	function index( $object, $object_id = NULL )
	{
		if( is_object($object) ){
			$object_class = $object->my_class();
			$object_id = $object->id;
		}
		else {
			$object_class = $object;
			$object = HC_App::model($object_class)
				->where('id', $object_id)
				->get()
				;
		}

		$acl = HC_App::acl();
		if( ! $acl->set_object($object)->can('conflicts_view') ){
			return;
		}

		$cmm = HC_App::model('conflict_manager');
		$entries = $cmm->get($object);

	/* render view */
		$this->layout->set_partial(
			'content', 
			$this->render( 
				'conflicts/index',
				array(
					'entries'	=> $entries,
					)
				)
			);

		$this->layout();
	}

	function quickicons( $object, $wrap = NULL )
	{
		$cmm = HC_App::model('conflict_manager');

		$show_this = FALSE;
		$count_ok = 0;
		$count_fail = 0;

		$conflicts = array();

		if( is_array($object) ){
			$show_this = TRUE;
			if( count($object) > 1 ){
				$show_this = TRUE;
			}
			foreach( $object as $obj ){
				if( $obj->start === NULL ){
					$show_this = FALSE;
					continue;
				}

				$entries = $cmm->get($obj, TRUE);
				$conflicts[$obj->date] = $entries;

				if( $entries ){
					$show_this = TRUE;
					$count_fail++;
				}
				else {
					$count_ok++;
				}
			}
		}
		else {
			$acl = HC_App::acl();
			if( ! $acl->set_object($object)->can('conflicts_view') ){
				return;
			}

			if( $object->start === NULL ){
				$show_this = FALSE;
				return;
			}

			$entries = $cmm->get($object, TRUE);
			$conflicts[$object->date] = $entries;

			if( $entries ){
				$show_this = TRUE;
				$count_fail++;
			}
		}

	/* render view */
		if( $show_this ){
			$this->layout->set_partial(
				'content', 
				$this->render( 
					'conflicts/quickicons',
					array(
						'count_fail'	=> $count_fail,
						'count_ok'		=> $count_ok,
						'conflicts'		=> $conflicts,
						'wrap'			=> $wrap,
						)
					)
				);

			$this->layout();
		}
	}

	function quickview( $object, $wrap = NULL )
	{
		$cmm = HC_App::model('conflict_manager');

		$show_this = FALSE;
		$count_ok = 0;
		$count_fail = 0;

		$conflicts = array();

		if( is_array($object) ){
			$show_this = TRUE;
			if( count($object) > 1 ){
				$show_this = TRUE;
			}
			foreach( $object as $obj ){
				if( $obj->start === NULL ){
					$show_this = FALSE;
					continue;
				}

				$entries = $cmm->get($obj, TRUE);
				$conflicts[$obj->date] = $entries;

				if( $entries ){
					$show_this = TRUE;
					$count_fail++;
				}
				else {
					$count_ok++;
				}
			}
		}
		else {
			$acl = HC_App::acl();
			if( ! $acl->set_object($object)->can('conflicts_view') ){
				return;
			}

			if( $object->start === NULL ){
				$show_this = FALSE;
				return;
			}

			$entries = $cmm->get($object, TRUE);
			$conflicts[$object->date] = $entries;

			if( $entries ){
				$show_this = TRUE;
				$count_fail++;
			}
		}

	/* render view */
		if( $show_this ){
			$this->layout->set_partial(
				'content', 
				$this->render( 
					'conflicts/quickview',
					array(
						'count_fail'	=> $count_fail,
						'count_ok'		=> $count_ok,
						'conflicts'		=> $conflicts,
						'wrap'			=> $wrap,
						)
					)
				);

			$this->layout();
		}
	}

	function shift_zoom_menubar( $object )
	{
		$return = '';
		if( ! $object->user_id ){
			return $return;
		}
		$acl = HC_App::acl();
		if( ! $acl->set_object($object)->can('conflicts_view') ){
			return;
		}

		$cmm = HC_App::model('conflict_manager');
		$entries = $cmm->get($object, FALSE);

		return $this->render(
			'conflicts/shift_zoom_menubar',
			array(
				'entries'	=> $entries,
				)
			);
	}

	function quickstats( $objects, $wrap = NULL )
	{
		$cmm = HC_App::model('conflict_manager');
		$acl = HC_App::acl();

		$show_this = FALSE;
		$count_ok = 0;
		$count_fail = 0;
		$conflict_ids = array();

		foreach( $objects as $obj ){
			if( $obj->start === NULL ){
				continue;
			}

			if( ! $acl->set_object($obj)->can('conflicts_view') ){
				continue;
			}

			$entries = $cmm->get($obj, TRUE);
			if( $entries ){
				foreach( $entries as $e ){
					$conflict_ids[$e->id] = 1;
				}
				$count_fail++;
			}
			else {
				$count_ok++;
			}
		}

	/* render view */
		if( count($conflict_ids) ){
			$this->layout->set_partial(
				'content', 
				$this->render( 
					'conflicts/quickstats',
					array(
						'count_fail'	=> count($conflict_ids),
						'count_ok'		=> $count_ok,
						'wrap'			=> $wrap,
						)
					)
				);

			$this->layout();
		}
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */