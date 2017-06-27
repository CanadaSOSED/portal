<?php
$acl[] = new Logaudit_Shiftcontroller_HC_Acl;

class Logaudit_Shiftcontroller_HC_Acl
{
	function shift_logaudit_view( $u, $o )
	{
		if( $o->user_id && ($u->id == $o->user_id) ){
			return TRUE;
		}
	}
}