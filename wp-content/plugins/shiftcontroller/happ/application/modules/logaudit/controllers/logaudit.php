<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logaudit_HC_Controller extends _Front_HC_Controller
{
	function object_zoom_menubar( $object )
	{
		$acl = HC_App::acl();
		if( ! $acl->set_object($object)->can('logaudit_view') ){
			return;
		}

		return $this->render(
			'logaudit/object_zoom_menubar',
			array(
				'object'	=> $object,
				)
			);
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

	/* load */
		$model = HC_App::model('logaudit');

		$acl = HC_App::acl();
		if( ! $acl->set_object($object)->can('logaudit_view') ){
			return;
		}

		$entries = $model->changes_by_time( $object );

		$objects = array();
		foreach( $entries as $action_time => $obj_changes ){
			foreach( array_keys($obj_changes) as $object_full_id ){
				if( array_key_exists($object_full_id, $objects) ){
					continue;
				}
				list( $obj_class, $obj_id ) = explode('.', $object_full_id);
				$child_object = HC_App::model($obj_class)->get_by_id($obj_id);
				if( ! $acl->set_object($child_object)->can('view') ){
					unset( $entries[$action_time][$object_full_id] );
					continue;
				}
				$objects[ $object_full_id ] = $child_object;
			}
		}

	/* render view */
		$this->layout->set_partial(
			'content', 
			$this->render( 
				'logaudit/index',
				array(
					'object'	=> $object,
					'objects'	=> $objects,
					'entries' 	=> $entries,
					)
				)
			);

		$this->layout();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */