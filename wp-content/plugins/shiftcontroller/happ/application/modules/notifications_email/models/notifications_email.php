<?php
class Notifications_Email_HC_Model
{
	protected $disabled = FALSE;

	public function run( $msgs )
	{
		reset( $msgs );
		foreach( $msgs as $uid => $user_msgs ){
			$user = HC_App::model('user')
				->where('id', $uid)
				->get()
				;

		/* group messages */
			reset( $user_msgs );
			foreach( $user_msgs as $key => $payloads ){
				$this->send( $key, $user, $payloads );
			}
		}
	}

	public function send( $key, $user, $payloads = array() )
	{
		if( ! $user->email ){
			return;
		}

		$app_conf = HC_App::app_conf();
		$conf_key = 'notifications_email:' . $key;
		$subject = $app_conf->conf( $conf_key );
		if( $subject === FALSE ){
			$subject = $key;
		}

		$msg = new stdClass();
		$msg->subject = $subject;
		if( count($payloads) > 1 ){
			$msg->subject .= ' (' . count($payloads) . ')'; 
		}

		$extensions = HC_App::extensions();

	/* build body */
		$body = array();
		foreach( $payloads as $payload ){
			foreach( $payload as $k => $obj ){
				/*
				$body = array_merge( 
					$body,
					array_values($obj->present_text(HC_PRESENTER::VIEW_TEXT, TRUE))
					);
				*/

				if( is_object($obj) ){
					$body = array_merge(
						$body,
						array_values(
							$obj->present_text(HC_PRESENTER::VIEW_RAW, TRUE))
						);
				}
				elseif( is_array($obj) ) {
					$body = array_merge(
						$body,
						$obj
						);
				}
				else {
				}

			// extensions
				$ext_key = 'notifications_email' . '/' . $key;
 				$more_content = $extensions->run($ext_key, $obj, $user);
				if( $more_content ){
					$body[] = '';
				}

				foreach( $more_content as $subtab => $subtext ){
					if( $subtext ){
						if( is_array($subtext) ){
							foreach( $subtext as $subtext2 ){
								if( is_array($subtext2) ){
									$body = array_merge( $body, $subtext2 );
								}
								else {
									$body[] = $subtext2;
								}
							}
						}
						else {
							$body[] = $subtext;
						}
					}
				}
			}

			$body[] = '';
		}

		$msg->body = $body;

	/* transport email */
		$CI =& ci_get_instance();
		$subj = $msg->subject;
		$body = join( "\n", $msg->body );

		$CI->hc_email->setSubject( $subj );
		$CI->hc_email->setBody( $body );
		$CI->hc_email->sendToOne( $user->email );
	}
}
