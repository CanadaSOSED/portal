<?php
class MY_model extends DataMapper
{
	static $relations = array();
	static $me_all = NULL;

	var $table = 'users';
	var $allow_none = TRUE;
	var $_old = array();

	private $_presenter = NULL; 

	function __construct( $id = NULL )
	{
		parent::__construct( $id );
		$my_class = $this->my_class();

	/* runtime relations configuration */
		if( empty(self::$relations) ){
			$this->config->load('relations', TRUE, TRUE );
		}

		if( ! isset(self::$relations[$my_class]) ){
			self::$relations[$my_class] = array(
				'has_many' => array(),
				'has_one' => array(),
				);
		}

		$schema = $this->config->item( $my_class, 'relations' );

		if( $schema ){
			if( isset($schema['has_many']) ){
				self::$relations[$my_class]['has_many'] = array_merge( self::$relations[$my_class]['has_many'], $schema['has_many'] );
			}

			if( isset($schema['has_one']) ){
				self::$relations[$my_class]['has_one'] = array_merge( self::$relations[$my_class]['has_one'], $schema['has_one'] );
			}
		}

		reset( self::$relations[$my_class]['has_many'] );
		foreach( self::$relations[$my_class]['has_many'] as $c => $rel ){
			$this->has_many( $c, $rel );
		}

		reset( self::$relations[$my_class]['has_one'] );
		foreach( self::$relations[$my_class]['has_one'] as $c => $rel ){
			$this->has_one( $c, $rel );
		}
	}

	public function _const( $cname )
	{
		$name = get_class($this) . '::' . $cname;
		$return = constant($name);
		return $return;
	}

	public function update_relation( $relname, $new_rels = array() )
	{
		/* get current */
		$current = array();
		$this->{$relname}->select('id')->get();
		foreach( $this->{$relname} as $rel ){
			$current[ $rel->id ] = $rel;
		}

		$to_add = array();
		$to_delete = array();
		foreach( $new_rels as $new_rel ){
			if( isset($current[$new_rel->id]) ){
				// remain
				unset($current[$new_rel->id]);
			}
			else {
				// add new
				$to_add[$new_rel->id] = $new_rel;
			}
		}

		/* delete */
		if( $current ){
			foreach( $current as $rel ){
				$this->delete($rel, $relname);
			}
		}

		/* to add */
		$related = array(
			$relname	=> $to_add
			);
		return $this->save( $related );
	}

	public function my_relation_names( $other_model ){
		$return = array();
		$my_class = $this->my_class();

		foreach( $other_model->has_one as $class_test => $class_test_details ){
			$this_other_class = isset($class_test_details['class']) ? $class_test_details['class'] : $class_test;
			if( $this_other_class == $my_class ){
				$return[] = $class_test;
			}
		}
		foreach( $other_model->has_many as $class_test => $class_test_details ){
			$this_other_class = isset($class_test_details['class']) ? $class_test_details['class'] : $class_test;
			if( $this_other_class == $my_class ){
				$return[] = $class_test;
			}
		}
		return $return;
	}

	public function get_all()
	{
		if( self::$me_all === NULL ){
			self::$me_all = array();
			foreach( $this->get()->all as $m ){
				self::$me_all[$m->id] = $m;
			}
			// self::$me_all = $this->get()->all;
		}
		return self::$me_all;
	}

	function up()
	{
	/* my order */
		$my_order = $this->show_order;

	/* check which one is upper then flip */
		$other_one = clone $this;
		$other_one
			->where( 'show_order <=', $my_order )
			->where( 'id <>', $this->id )
			->order_by( 'show_order', 'desc' )
			->limit(1)
			->get();
		if( $other_one->exists() ){
			$new_order = $other_one->show_order;
			$other_id = $other_one->id;
			if( $new_order == $my_order ){
				$my_order = $new_order + 1;
			}
		/* update other_one */
			$other_one->show_order = $my_order;
			$other_one->save();
		/* update me */
			$this->show_order = $new_order;
			$this->save();
		}
		return TRUE;
	}

	function down()
	{
	/* my order */
		$my_order = $this->show_order;

	/* check which one is lower then flip */
		$other_one = clone $this;
		$other_one
			->where( 'show_order >=', $my_order )
			->where( 'id <>', $this->id )
			->order_by( 'show_order', 'asc' )
			->limit(1)
			->get();
		if( $other_one->exists() ){
			$new_order = $other_one->show_order;
			$other_id = $other_one->id;
			if( $new_order == $my_order ){
				$my_order = $new_order - 1;
			}
		/* update other_one */
			$other_one->show_order = $my_order;
			$other_one->save();
		/* update me */
			$this->show_order = $new_order;
			$this->save();
		}
		return TRUE;
	}

	protected function _prepare_get()
	{
		foreach( $this->has_many as $other => $info ){
			// echo "INCLUDE RELATED COUNT $other<br>";
			$this->include_related_count($other);
		}
	}

	public function get_slim( $limit = NULL, $offset = NULL )
	{
		return parent::get( $limit, $offset );
	}

	public function get_iterated_slim( $limit = NULL, $offset = NULL )
	{
		return parent::get_iterated( $limit, $offset );
	}

	function full_id()
	{
		$return = $this->my_class() . '.' . $this->id;
		return $return;
	}

	function valid()
	{
		return $this->valid;
	}

	function errors()
	{
		return $this->error->all;
	}

	function add_error( $field, $error )
	{
		return $this->error_message( $field, $error );
	}

	function remove_validation( $what )
	{
		unset( $this->validation[$what] );
	}

	function my_class()
	{
		$return = get_class($this);
		$return = strtolower($return);

		$CI =& ci_get_instance();
		$suffix = $CI->config->item('model_suffix');

		if( substr($return, -strlen($suffix)) == $suffix ){
			$return = substr( $return, 0, -strlen($suffix) );
		}
		return $return;
	}

	function prop_name( $pname )
	{
		if( substr($pname, -3) == '_id' ){
			$short_pname = substr($pname, 0, -3);
			if(
				isset($this->has_one[$short_pname])
				OR
				isset($this->has_many[$short_pname])
			){
				$pname = $short_pname;
			}
		}
		return $pname;
	}

	public function trigger_event( $event, $force_class = '' )
	{
		/* check if we also have a method here */
		$method = '_' . $event;
		if( method_exists($this, $method)){
			$this->{$method}();
		}

		$CI =& ci_get_instance();
		if( isset($CI->hc_events) ){
			$event_class = $force_class ? $force_class : $this->my_class();
			$event = $event_class . '.' . $event;
			$object = clone $this;
			$args = array( $event, $object );
			call_user_func_array( array($CI->hc_events, 'trigger'), $args );
		}
	}

	public function get_copy($force_db = FALSE)
	{
	/* reset changes */
		$this->_old = array();
		return parent::get_copy($force_db);
	}

/* with triggered events */
	public function save($object = '', $related_field = '')
	{
		$is_new = FALSE;
		if( $this->_force_save_as_new OR (! $this->id) ){
			$is_new = TRUE;
		}

		$this->trigger_event( 'before_save' );

	/* keep copy of the stored because it resets to new after save */
		$this->_keep_old();

		$return = parent::save($object, $related_field);

	/* if new then get it to load relations */
		if( 
			$return
			&&
			$is_new 
			&&
			( $this->has_one OR $this->has_many )
			){
			$this
				->where('id', $this->id)
				->get()
				;
		}

		if( $return ){
			$this->trigger_event( 'after_save' );
		}
		return $return;
	}

	public function delete($object = '', $related_field = '')
	{
		if( ! ($object OR $related_field) ){
			$this->trigger_event( 'before_delete' );
		}

		$return = parent::delete($object, $related_field);

		if( $return ){
			if( ! ($object OR $related_field) ){
				$this->trigger_event('after_delete');
			}
		}
		return $return;
	}

	private function _keep_old()
	{
		$this->_old = (array) $this->stored;
	/* also include has_one */
		reset( $this->has_one );
		foreach( array_keys($this->has_one) as $k ){
			if( is_object($this->{$k}) )
				$this->_old[$k] = $this->{$k}->id;
			else
				$this->_old[$k] = $this->{$k};
		}
	}

/* gives the array of changed properties with their old values, useful after save */
	public function get_changes( $relations = NULL )
	{
		$return = array();
		$new = $this->to_array();
		if( $relations ){
			reset( $relations );
			foreach( $relations as $k => $o ){
				$new[ $k ] = $o->id;
			}
		}

		foreach( $new as $k => $v ){
			if( array_key_exists($k, $this->_old) ){
				if( $this->_old[$k] !== $v )
					$return[$k] = $this->_old[$k];
			}
			else {
				$return[$k] = NULL;
			}
		}
		return $return;
	}

/* rewrite from_array() to not to modify database */
	function from_array( $data )
	{
		// keep track of newly related objects
		$new_related_objects = array();
		$fields = array_keys( $data );

		// If $fields is provided, assume all $fields should exist.
		foreach($fields as $f){
			if(array_key_exists($f, $this->has_one)){
				// Store $has_one relationships
				$c = get_class($this->{$f});
				$rel = new $c();
				$id = isset($data[$f]) ? $data[$f] : 0;
				$rel->get_by_id($id);

				if($rel->exists()){
					// The new relationship exists, save it.
					$new_related_objects[$f] = $rel;
					// if( ! $this->id ){
						$this->{$f} = $rel;
					// }
				}
				else {
					// The new relationship does not exist, delete the old one.
//						$object->delete($object->{$f}->get());
				/* CHANGE */
//					$new_related_objects[$f] = NULL;
					$new_related_objects[$f] = NULL;
					// if( ! $this->id ){
						$this->{$f}->clear();
					// }
				}
				$idid = $f . '_id';
				$this->{$idid} = $id;
			}
			else if(array_key_exists($f, $this->has_many)) {
				// Store $has_many relationships
				$c = get_class($this->{$f});
				$rels = new $c();
				$ids = isset($data[$f]) ? $data[$f] : FALSE;
				if(empty($ids)) {
					// if no IDs were provided, delete all old relationships.
//						$object->delete($object->{$f}->select('id')->get()->all);
				/* CHANGE */
					$new_related_objects[$f] = array();
					// if( ! $this->id ){
						$this->{$f}->clear();
					// }
				}
				else {
					// Otherwise, get the new ones...
					$rels->where_in('id', $ids)->select('id')->get();
					// Store them...

					$new_related_objects[$f] = $rels->all;
					// if( ! $this->id ){
						$this->{$f} = $rels->all;
					// }

					// And delete any old ones that do not exist.
//						$old_rels = $object->{$f}->where_not_in('id', $ids)->select('id')->get();
//						$object->delete($old_rels->all);
				/* CHANGE */
				}
			}
			elseif(
				in_array($f, $this->fields) OR
				isset($this->validation[$f])
				){
					if( isset($data[$f])){
						$this->{$f} = $data[$f];
					}
			}
		}

		// return new objects
		return $new_related_objects;
	}

	public function set( $pname, $pvalue )
	{
		$this->{$pname} = $pvalue;
		return $this;
	}

/* prepare for presenters */
	public function presenter()
	{
		return $this->_presenter;
	}
	public function set_presenter( $presenter )
	{
		$this->_presenter = $presenter;
	}

	public function __call($key, $args = array())
	{
		$prfx = 'present_';
		if( substr($key, 0, strlen($prfx)) == $prfx ){
			$short_key = substr($key, strlen($prfx));
			$presenter = $this->presenter();

			if( $presenter === NULL ){
			// attempt to load presenter
				$presenter = HC_App::presenter( $this->my_class(), $this );
				if( $presenter ){
					$this->set_presenter( $presenter );
				}
				else {
					$this->set_presenter( FALSE );
				}
			}

			if( $presenter && method_exists($presenter, $short_key) ){
				array_unshift( $args, $this );
				return call_user_func_array( array($presenter, $short_key), $args );
				// return $presenter->{$short_key}( $this );
			}
			else {
				if( property_exists($this, $short_key) ){
					return $this->{$short_key};
				}
				else {
					return NULL;
				}
			}
		}
		return parent::__call($key, $args);
	}

/* validation */
	public function _save_array( $field )
	{
		if ( ! empty($this->{$field}) ){
			$this->{$field} = join( '||', $this->{$field} );
		}
		else {
			$this->{$field} = '';
		}
		return TRUE;
	}

	public function _load_array( $field )
	{
		if ( ! empty($this->{$field}) ){
			$this->{$field} = explode( '||', $this->{$field} );
		}
		else {
			$this->{$field} = array();
		}
	}

	public function _check_show_order( $field )
	{
		if( (! $this->id) && (! $this->show_order) ){
			$max_show_order = 0;
			$query = $this->db
				->select_max('show_order')
				->get($this->table)
				;
			if( $row = $query->row() ){
				$max_show_order = $row->show_order;
				}
			$this->show_order = $max_show_order + 1;
		}
		return TRUE;
	}

	public function _enum( $field, $compare )
	{
		if (
			( isset($this->{$field}) )
			)
		{
			return in_array( $this->{$field}, $compare );
		}
		return FALSE;
	}

	public function _greater_equal_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} >= $this->{$other};
	}

	public function _less_equal_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} <= $this->{$other};
	}

	public function _greater_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} > $this->{$other};
	}

	public function _less_than_field($me, $other)
	{
		if ( (! is_numeric($this->{$me})) OR (! is_numeric($this->{$other})) )
			return FALSE;
		return $this->{$me} < $this->{$other};
	}

	protected function _differs($field, $other_field)
	{
		return ($this->{$field} !== $this->{$other_field}) ? TRUE : FALSE;
	}

	protected function _after_save()
	{
/*
		$CI =& ci_get_instance();
		if( $this->keep_log && $CI->hc_modules->exists('logaudit') )
		{
			$log_changes = array();
			$changes = $this->get_changes();
			reset( $changes );
			foreach( $changes as $property_name => $old_value )
			{
				if( in_array($property_name, $this->keep_log) )
				{
					$log_changes[ $property_name ] = $old_value;
				}
			}

			if( $log_changes )
			{
				$log = HC_App::model('logaudit');
				$log->log( $this, $log_changes );
			}
		}
*/
	}
}

class MY_Model_Virtual
{
	private $_presenter = NULL; 

    function my_class()
	{
		$return = get_class($this);
		$return = strtolower($return);

		$CI =& ci_get_instance();
		$suffix = $CI->config->item('model_suffix');

		if( substr($return, -strlen($suffix)) == $suffix ){
			$return = substr( $return, 0, -strlen($suffix) );
		}
		return $return;
	}

	public function trigger_event( $event )
	{
		$CI =& ci_get_instance();
		if( isset($CI->hc_events) ){
			$event = $this->my_class() . '.' . $event;
			$object = clone $this;
			$args = array( $event, $object );
			call_user_func_array( array($CI->hc_events, 'trigger'), $args );
		}
	}

	public function save($object = '', $related_field = '')
	{
		$this->trigger_event( 'before_save' );

	/* keep copy of the stored because it resets to new after save */
		$this->_keep_old();

		$return = parent::save($object, $related_field);

		if( $return ){
			$this->trigger_event( 'after_save' );
		}
		return $return;
	}

/* prepare for presenters */
	public function presenter()
	{
		return $this->_presenter;
	}
	public function set_presenter( $presenter )
	{
		$this->_presenter = $presenter;
	}

	public function __call($key, $args = array())
	{
		$prfx = 'present_';
		if( substr($key, 0, strlen($prfx)) == $prfx ){
			$short_key = substr($key, strlen($prfx));
			$presenter = $this->presenter();
			if( $presenter === NULL ){
			// attempt to load presenter
				$presenter = HC_App::presenter( $this->my_class(), $this );
				if( $presenter ){
					$this->set_presenter( $presenter );
				}
				else {
					$this->set_presenter( FALSE );
				}
			}

			if( $presenter && method_exists($presenter, $short_key) ){
				array_unshift( $args, $this );
				return call_user_func_array( array($presenter, $short_key), $args );
				// return $presenter->{$short_key}( $this );
			}
			else {
				if( isset($this->{$short_key}) ){
					return $this->{$short_key};
				}
			}
		}
		return parent::__call($key, $args);
	}
}

function hc_presenter_autoload( $class )
{
	$class = strtolower($class);
	$suffix = '_hc_presenter';
	if( substr($class, -strlen($suffix)) != $suffix ){
		return;
	}
//echo "TRYING '$class'<br>";

	$CI =& ci_get_instance();
	$look_in_dirs = $CI->config->look_in_dirs();

	foreach( $look_in_dirs as $path ){
		// Prepare file
		$class_file = substr($class, 0, -strlen($suffix));
		$file = $path . '/presenters/' . $class_file . EXT;
			// echo $file .' <br>';

		// Check if file exists, require_once if it does
		if (file_exists($file)){
			include_once($file);
			break;
		}
	}
}

spl_autoload_register('hc_presenter_autoload');