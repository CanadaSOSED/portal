<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Delete_Shifts_HC_Controller extends _Front_HC_Controller
{
	function index( $id )
	{
		$model = HC_App::model('shift');
		$model
			->where('id', $id)
			->get()
			;
		$this->_check_model( $model );

		$acl = HC_App::acl();
		if( ! $acl->set_object($model)->can('delete') ){
			$msg = HCM::__('Permission Denied');
			$this->session->set_flashdata(
				'error_ajax',
				$msg
				);
			$referrer = ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
			$this->redirect( $referrer, array(), 3 );
			return;
		}

		$parent_refresh = $model->present_calendar_refresh();
		$parent_refresh = array_keys($parent_refresh);

		if( $model->delete() ){
			$this->session->set_flashdata( 
				'message',
				sprintf( HCM::_n('%d shift deleted', '%d shifts deleted', 1), 1 )
			);
		}
		else {
			$this->session->set_flashdata(
				'error',
				HCM::__('Error')
			);
		}

		$redirect_to = 'list/calendar';
		$this->redirect( $redirect_to, $parent_refresh );
		return;
	}

	function deleterel( $id, $relname, $relid )
	{
		$model = HC_App::model('shift');
		$model->get_by_id( $id );
		$this->_check_model( $model );

		$acl = HC_App::acl();
		if( ! $acl->set_object($model)->can($relname . '::delete') ){
			return;
		}

		$rel = $model->{$relname}->get_by_id($relid);
		if( $model->delete($rel, $relname) ){
			$this->session->set_flashdata( 
				'message',
				sprintf( HCM::_n('%d shift updated', '%d shifts updated', 1), 1 )
			);
		}
		else {
			$this->session->set_flashdata(
				'error',
				HCM::__('Error')
			);
		}

		$redirect_to = 'shifts/zoom/' . $id;
//			$redirect_to = '-referrer-';
		$this->redirect( $redirect_to );
		return;
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */