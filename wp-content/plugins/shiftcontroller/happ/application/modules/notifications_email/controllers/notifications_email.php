<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notifications_Email_HC_Controller extends _Front_HC_Controller
{
	function add_form_inputs( $parent_object = NULL )
	{
		$acl = HC_App::acl();
		if( $parent_object ){
			if( ! $acl->set_object($parent_object)->can('notification_email_skip') ){
				return;
			}
		}

		$form = HC_Lib::form();
		$form->set_inputs(
			array(
				'notifications_email_skip'	=> 'checkbox',
				)
			);

		$default_values = array(
			'notifications_email_skip'	=> 0
			);

	/* extensions */
		$extensions = HC_App::extensions();
		$change_values = $extensions->run(
			'notifications_email/insert/defaults'
			);
		foreach( $change_values as $change_array ){
			foreach( $change_array as $k => $v ){
				$default_values[$k] = $v;
			}
		}

		$form->set_values( $default_values );

		return $this->render(
			'notifications_email/add_form_inputs',
			array(
				'form'	=> $form,
				)
			);
	}

	function api_insert( $post )
	{
		$extensions = HC_App::extensions();

		$notifications_email_skip = isset($post['notifications_email_skip']) ? $post['notifications_email_skip'] : FALSE;
		if( $notifications_email_skip ){
			$messages = HC_App::model('messages');
			$messages->remove_engine('email');
		}

	/* extensions */
		$extensions->run(
			'notifications_email/insert',
			$post
			);

		$return = TRUE;
		return $return;
	}
}