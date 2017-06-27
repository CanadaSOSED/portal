<?php
class Logaudit_HC_Model extends MY_model
{
	var $table = 'logaudit';
	var $default_order_by = array('action_time' => 'DESC');

	var $has_one = array(
		'user' => array(
			'class'			=> 'user',
			'other_field'	=> 'logaudit',
			),
		);

	public function changes_by_time( $model, $one = FALSE )
	{
		$new_ones = array();
		$return = array();

		$my_class = $model->my_class();
		$my_id = $model->id;

		$raw = array();

	/* OWN */
		$this->include_related( 'user', array('id', 'first_name', 'last_name', 'active'), TRUE, TRUE );
		$this
			->where( 'object_class',	$my_class )
			->where( 'object_id',		$my_id )
			;

		$this->get();
		// $this->check_last_query();
		foreach( $this as $e ){
			$e->rel_name = '';
			$raw[] = $e;
		}

	/* HAS ONE */
		foreach( array_keys($model->has_one) as $other_class ){
			if( isset($model->has_one[$other_class]['class']) ){
				$other_class = $model->has_one[$other_class]['class'];
				}
			$other_class = HC_App::short_model( $other_class );
			$other_model = HC_App::model($other_class);

			$this
				->where('object_class', $other_class )
				->where_in_subquery(
					'object_id',
					$other_model
						->select('id')
						->where_related($my_class, 'id', $my_id)
					)
				;

			$this->get();
			foreach( $this as $e ){
				$e->rel_name = $my_class;
				$raw[] = $e;
			}
		}

	/* HAS MANY */
		$relations_already_seen = array();
		foreach( array_keys($model->has_many) as $other_class ){
			if( isset($model->has_many[$other_class]['class']) ){
				$other_class = $model->has_many[$other_class]['class'];
				}
			$other_class = HC_App::short_model( $other_class );
			$other_model = HC_App::model($other_class);
			if( ! isset($relations_already_seen[$other_class]) ){
				$relations_already_seen[$other_class] = array();
			}

			$my_relation_names = $model->my_relation_names($other_model);
			$my_relation_names = array_diff( $my_relation_names, $relations_already_seen[$other_class] );
			$relations_already_seen[$other_class] = array_merge( $relations_already_seen[$other_class], $my_relation_names );

			foreach( $my_relation_names as $my_relation_name ){
				$this->include_related( 'user', array('id', 'first_name', 'last_name', 'active'), TRUE, TRUE );
				$this
					->where('object_class', $other_class )
					->where_in_subquery(
						'object_id',
						$other_model
							->select('id')
							->where_related($my_relation_name, 'id', $my_id)
						)
					;

				$this->get();
				// $this->check_last_query();
				foreach( $this as $e ){
					$e->rel_name = $my_relation_name;
					$raw[] = $e;
				}
			}
		}

	/* sort by action time */
		usort( $raw, create_function('$a, $b', 'return ($b->action_time - $a->action_time);' ) );

		$objects = array();
		foreach( $raw as $e ){
			if( ! isset($return[$e->action_time]) ){
				if( $one && count($return) ){
					break;
				}
				$return[$e->action_time] = array();
			}

			$object_full_id = $e->object_class . '.' . $e->object_id . '.' . $e->rel_name;

			if( ! isset($return[$e->action_time][$object_full_id]) ){
				$return[$e->action_time][$object_full_id] = array();
			}
			if( ! isset($objects[$object_full_id]) ){
				$objects[$object_full_id] = HC_App::model($e->object_class)->get_by_id($e->object_id);
			}

			$pname = $e->property_name;

			$change = new stdClass();
			$change->old = $e->old_value;
			$change->user = $e->user;

			if( ! isset($new_ones[$object_full_id]) ){
				$new_ones[$object_full_id] = array();
			}

			if( array_key_exists($pname, $new_ones[$object_full_id]) ){
				$change->new = $new_ones[$object_full_id][$pname];
			}
			else {
				$change->new = $objects[$object_full_id]->{$pname};
			}
			// $new_ones[$object_full_id][$pname] = $e->old;
			$new_ones[$object_full_id][$pname] = $change->old;
			$return[$e->action_time][$object_full_id][$pname] = $change;

		/* add status for newly created objects */
			if( ($pname == 'id') && isset($objects[$object_full_id]->status) ){
				$add_pname = 'status';

				$add_change = new stdClass();
				$add_change->old = NULL;
				$add_change->user = $e->user;

				if( array_key_exists($add_pname, $new_ones[$object_full_id]) ){
					$add_change->new = $new_ones[$object_full_id][$add_pname];
				}
				else {
					$add_change->new = $objects[$object_full_id]->{$add_pname};
				}
				$return[$e->action_time][$object_full_id][$add_pname] = $add_change;
				}
		}

		if( $one && count($return) ){
			$return = array_shift( $return );
		}

		return $return;
	}

	public function log( $object, $keep_log = array() )
	{
		if( ! $keep_log )
			return;

		$log_changes = array();
		$changes = $object->get_changes();
		reset( $changes );
		foreach( $changes as $property_name => $old_value ){
			if( in_array($property_name, $keep_log) ){
				$log_changes[ $property_name ] = $old_value;
			}
		}

		if( ! $log_changes )
			return;

		$CI =& ci_get_instance();
		$user_id = (isset($CI->auth) && $CI->auth) ? $CI->auth->check() : -1;

		$defaults = array(
			'user_id'		=> $user_id,
			'action_time'	=> time(),
			'object_class'	=> $object->my_class(),
			'object_id'		=> $object->id,
			);
/*
		if( $log )
		{
			foreach( $log as $k => $v )
				$defaults[$k] = $v;
		}
*/

		/* JUST CREATED */
		if( array_key_exists('id', $log_changes) ){
			$this->clear();
			$this->from_array( $defaults );
			$this->property_name = 'id';
			$this->old_value = NULL;
			$this->save();
		}
		else {
			foreach( $log_changes as $property_name => $old_value ){
				$this->clear();
				$this->from_array( $defaults );
				$this->property_name = $property_name;
				$this->old_value = $old_value;
				$this->save();
			}
		}
		return TRUE;
	}
}