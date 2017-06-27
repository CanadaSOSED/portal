<?php
$acl[] = new Shiftcontroller_Admin_HC_Acl;
$acl[] = new Shiftcontroller_HC_Acl;

class Shiftcontroller_Admin_HC_Acl
{
	public function __call( $what, $args )
	{
		$u = array_shift( $args );
		$o = array_shift( $args );

		if( $u->level >= $u->_const("LEVEL_MANAGER") ){
			return TRUE;
		}
		return;
	}

	// only active shifts are allowed
	function shift_validate_status2( $u, $o, $new_value )
	{
		if( $o->status == $new_value ){
			return TRUE;
		}

		$app_conf = HC_App::app_conf();
		if( $o->type == $o->_const('TYPE_TIMEOFF') ){
			return TRUE;
		}
		else {
			if( $new_value == $o->_const('STATUS_ACTIVE') ){
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
	}
}

class Shiftcontroller_HC_Acl
{
	private $_edit_delete = array(
		'view_active'	=> array(
			'edit_active'	=> 0,
			'edit_draft'	=> 0,
			'delete_active'	=> 0,
			'delete_draft'	=> 0
			),
		'view_draft'	=> array(
			'edit_active'	=> 0,
			'edit_draft'	=> 0,
			'delete_active'	=> 0,
			'delete_draft'	=> 0
			),
		'create_draft'	=> array(
			'edit_active'	=> 0,
			'edit_draft'	=> 1,
			'delete_active'	=> 0,
			'delete_draft'	=> 1
			),
		'confirm_draft'	=> array(
			'edit_active'	=> 0,
			'edit_draft'	=> 1,
			'delete_active'	=> 0,
			'delete_draft'	=> 0
			),
		'create_active'	=> array(
			'edit_active'	=> 1,
			'edit_draft'	=> 1,
			'delete_active'	=> 1,
			'delete_draft'	=> 1
			)
		);

	private function _shift_add( $u, $o )
	{
		$app_conf = HC_App::app_conf();
		$staff_shift_acl = $app_conf->get('staff:shift_acl');
		$staff_shift_acl = $app_conf->get('staff:shift_acl');
		if( ! is_array($staff_shift_acl) ){
			$staff_shift_acl = array( $staff_shift_acl );
		}

		if( array_intersect(array('create_draft', 'create_active'), $staff_shift_acl) ){
			return TRUE;
		}
		return;
	}

	private function _timeoff_add( $u, $o )
	{
		if( $o->user_id != $u->id ){
			return;
		}

		$app_conf = HC_App::app_conf();
		if( $o->status !== NULL ){
			$timeoff_approval_required = $app_conf->get("timeoff:approval_required");
			if( $timeoff_approval_required ){
				if( ! in_array($o->status, array($o->_const("STATUS_DRAFT"))) ){
					return FALSE;
				}
			}
			else {
				return TRUE;
			}
		}
		else {
			return TRUE;
		}
		return;
	}

	function shift_add( $u, $o )
	{
		if( ! $o->user_id ){
			return;
		}

		if( $o->user_id != $u->id ){
			return;
		}

		/* timeoffs */
		if( in_array($o->type, array($o->_const("TYPE_TIMEOFF"))) ){
			return $this->_timeoff_add($u, $o);
		}
		/* shifts */
		else {
			return $this->_shift_add($u, $o);
		}
	}

	function shift_view( $u, $o )
	{
		$app_conf = HC_App::app_conf();
		$staff_shift_acl = $app_conf->get("staff:shift_acl");

	/* can view own pending timeoffs, otherwise no */
		$show_draft = hc_in_array("view_draft", $staff_shift_acl);

		if( $o->user_id && ($u->id == $o->user_id) ){
			if( $o->status != $o->_const("STATUS_ACTIVE") ){
				if( $show_draft ){
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				return TRUE;
			}
		}
		elseif( $o->status != $o->_const("STATUS_ACTIVE") ){
			return FALSE;
		}

	/* if anonymous */
		if( ! $u->id ){
		/* cannot view timeoffs */
			if(in_array($o->type, array($o->_const('TYPE_TIMEOFF'))) ){
				return;
			}

		/* cannot view open shifts */
			if( ! $o->user_id ){
				$wall_show_open = $app_conf->get('wall:show_open');
				if( $wall_show_open ){
					return TRUE;
				}
				return;
			}
		}

	/* cannot view others timeoffs */
		if( ($u->id != $o->user_id) && (in_array($o->type, array($o->_const("TYPE_TIMEOFF")))) ){
			return FALSE;
		}

		$ri = HC_Lib::ri();
		if( $ri ){
			/* shortcode so can view it anyway */
			return TRUE;
		}
		else {
			$user_level = $u->level ? $u->level : 0;
			$wall_schedule_display = $app_conf->get("wall:schedule_display");

			if( $user_level >= $wall_schedule_display ){
				return TRUE;
			}
		}
		return;
	}

	private function _timeoff_edit( $u, $o )
	{
		$app_conf = HC_App::app_conf();

		if( $o->status !== NULL ){
			$timeoff_approval_required = $app_conf->get('timeoff:approval_required');
			if( $timeoff_approval_required ){
				if( ! in_array($o->status, array($o->_const('STATUS_DRAFT'))) ){
					return FALSE;
				}
			}
			return TRUE;
		}
	}

	private function _shift_edit( $u, $o, $what = 'edit' )
	{
		$app_conf = HC_App::app_conf();

		$staff_shift_acl = $app_conf->get('staff:shift_acl');
		if( ! is_array($staff_shift_acl) ){
			$staff_shift_acl = array( $staff_shift_acl );
		}

		if( $o->status == $o->_const('STATUS_DRAFT') ){
			$acl_key2 = $what . '_draft';
		}
		else {
			$acl_key2 = $what . '_active';
		}

		$return = NULL;
		foreach( $staff_shift_acl as $acl_key1 ){
			$return = NULL;
			if( isset($this->_edit_delete[$acl_key1][$acl_key2]) ){
				$return = $this->_edit_delete[$acl_key1][$acl_key2];
				if( $return ){
					$return = TRUE;
					break;
				}
			}
		}
		return $return;
	}

	function shift_edit( $u, $o )
	{
		if( ! $u->id ){
			return FALSE;
		}
		if( $o->user_id != $u->id ){
			return FALSE;
		}

		/* timeoffs */
		if( in_array($o->type, array($o->_const("TYPE_TIMEOFF"))) ){
			return $this->_timeoff_edit($u, $o);
		}
		/* shifts */
		else {
			return $this->_shift_edit($u, $o, 'edit');
		}
		return;
	}

	function shift_delete( $u, $o )
	{
		if( ! $u->id ){
			return FALSE;
		}
		if( $o->user_id != $u->id ){
			return FALSE;
		}

		/* timeoffs */
		if( in_array($o->type, array($o->_const("TYPE_TIMEOFF"))) ){
			return $this->_timeoff_edit($u, $o);
		}
		/* shifts */
		else {
			return $this->_shift_edit($u, $o, 'delete');
		}
		return;
	}

	private function _timeoff_validate( $u, $o )
	{
		$app_conf = HC_App::app_conf();

		if( $o->status !== NULL ){
			$timeoff_approval_required = $app_conf->get("timeoff:approval_required");
			if( $timeoff_approval_required ){
				if( ! in_array($o->status, array($o->_const("STATUS_DRAFT"))) ){
					return FALSE;
				}
			}
			else {
				if( ! in_array($o->status, array($o->_const("STATUS_ACTIVE"))) ){
					return FALSE;
				}
			}
		}
		else {
			return TRUE;
		}
	}

	function shift_validate_location( $u, $o, $new_value )
	{
		if( $o->location_id == $new_value ){
			return TRUE;
		}

		if( $o->status === NULL ){
			return TRUE;
		}

		$app_conf = HC_App::app_conf();
		$staff_shift_acl = $app_conf->get("staff:shift_acl");

	/* create draft shifts */
		if( hc_in_array('create_draft', $staff_shift_acl) ){
			if( $o->status == $o->_const('STATUS_DRAFT') ){
				return TRUE;
			}
		}

	/* create active shifts */
		if( hc_in_array('create_active', $staff_shift_acl) ){
			return TRUE;
		}
	}

	function shift_validate_status( $u, $o, $new_value )
	{
		if( $o->status == $new_value ){
			return TRUE;
		}

		$app_conf = HC_App::app_conf();
		if( $o->type == $o->_const('TYPE_TIMEOFF') ){
			$timeoff_approval_required = $app_conf->get("timeoff:approval_required");
			if( (! $timeoff_approval_required) OR ($new_value == $o->_const('STATUS_DRAFT')) ){
				return TRUE;
			}
		}
		else {
			$staff_shift_acl = $app_conf->get("staff:shift_acl");

		/* create draft shifts */
			if( hc_in_array('create_draft', $staff_shift_acl) ){
				if( ($o->status != $o->_const('STATUS_ACTIVE')) &&
					($new_value == $o->_const('STATUS_DRAFT')) ){
					return TRUE;
				}
			}

		/* approve draft shifts */
			if( hc_in_array('confirm_draft', $staff_shift_acl) ){
				if( ($o->id) && ($new_value == $o->_const('STATUS_ACTIVE')) ){
					return TRUE;
				}
			}

		/* create active shifts */
			if( hc_in_array('create_active', $staff_shift_acl) ){
				return TRUE;
			}
		}
	}

	function shift_validate_user( $u, $o, $new_value )
	{
		if( $new_value == $u->id ){
			return TRUE;
		}
	}

	function shift_edit_time( $u, $o )
	{
		$app_conf = HC_App::app_conf();
		$staff_shift_acl = $app_conf->get("staff:shift_acl");

	/* create draft shifts */
		if( hc_in_array('create_draft', $staff_shift_acl) ){
			if( $o->status == $o->_const('STATUS_DRAFT') ){
				return TRUE;
			}
		}

	/* create active shifts */
		if( hc_in_array('create_active', $staff_shift_acl) ){
			return TRUE;
		}
	}

	private function _shift_validate( $u, $o )
	{
		$app_conf = HC_App::app_conf();

		$staff_shift_acl = $app_conf->get("staff:shift_acl");

	/* approve existing graft shifts */
		if( hc_in_array('confirm_draft', $staff_shift_acl) ){
			if( $o->id && ($o->status !== NULL) ){
				return TRUE;
			}
		}

		$staff_add_shifts = 0;
		if( hc_in_array("create_draft", $staff_shift_acl) ){
			$staff_add_shifts = $o->_const("STATUS_DRAFT");
		}
		if( hc_in_array("create_active", $staff_shift_acl) ){
			$staff_add_shifts = $o->_const("STATUS_ACTIVE");
		}

		if( ! $staff_add_shifts ){
			return FALSE;
		}

		if( $o->status !== NULL ){
			switch( $staff_add_shifts ){
				case $o->_const("STATUS_ACTIVE"):
					break;

				case $o->_const("STATUS_DRAFT"):
					if( ! in_array($o->status, array($o->_const("STATUS_DRAFT"))) ){
						return FALSE;
					}
					break;
			}
		}
	}

	function shift_validate( $u, $o )
	{
		if( ! $u->id ){
			return FALSE;
		}
		if( $o->user_id != $u->id ){
			return FALSE;
		}

		$app_conf = HC_App::app_conf();

		/* timeoffs */
		if( in_array($o->type, array($o->_const("TYPE_TIMEOFF"))) ){
			return $this->_timeoff_validate($u, $o);
		}
		/* shifts */
		else {
			return $this->_shift_validate($u, $o);
		}
		return TRUE;
	}
}