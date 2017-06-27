<?php
$acl[] = new Conflicts_Shiftcontroller_HC_Acl;

class Conflicts_Shiftcontroller_HC_Acl
{
	function shift_conflicts_view( $u, $o )
	{
		if( $o->user_id && ($u->id == $o->user_id) ){
			return TRUE;
		}
	}
}
