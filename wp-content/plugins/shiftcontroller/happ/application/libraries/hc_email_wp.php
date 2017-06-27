<?php
class Hc_email_wp {
	var $subject;
	var $body;
	var $from;
	var $fromName;
	var $debug;
	var $disabled = false;
	var $error;
	var $charSet = '';
	public $bcc_to = array();

	function __construct(){
		$this->subject = '';
		$this->body = '';
		$this->error = '';

		$this->disabled = false;
		$conf = HC_App::app_conf();
		$this->disabled = $conf->get('disable_email');

		$this->debug = false;

	/* logger */
		$loggerFile = dirname(__FILE__) . '/email/ntsEmailLogger.php';
		if( file_exists($loggerFile) ){
			$this->logger = true;
			include_once( $loggerFile );
		}
		else {
			$this->logger = false;
		}
	}

	function setSubject( $subject ){
		$this->subject = $subject;
	}
	function setBody( $body ){
		$this->body = $body;
	}
	function setFrom( $from ){
		$this->from = $from;
	}
	function setFromName( $fromName ){
		$this->fromName = $fromName;
	}

	function sendToOne( $toEmail ){
		$toArray = array( $toEmail );
		return $this->_send( $toArray );
	}

	function getBody(){
		return $this->body;
	}

	function getSubject(){
		return $this->subject;
	}

	function _send( $toArray = array() ){
		if( $this->disabled )
			return true;

		if( $this->bcc_to ){
			$toArray = array_merge( $toArray, $this->bcc_to );
			$toArray = array_unique( $toArray );
		}

		$text = $this->getBody();

		$headers = array();
		// $headers[] = 'From: Me Myself <me@example.net>';
		$headers[] = 'From: ' . $this->fromName . ' <' . $this->from . '>';

		$subject = $this->getSubject();
		$body = nl2br( $text );

		if( defined('NTS_DEVELOPMENT') && NTS_DEVELOPMENT ){
			$msg = array();
			$msg[] = 'Email to ' . join( ', ', $toArray );
			$msg[] = $this->getSubject();
			$msg[] = nl2br($this->getBody());
			$msg = join('<br>', $msg );
			$CI =& ci_get_instance();
			$CI->session->add_flashdata( 'debug_message', $msg );
		}
		else {
		// is html
			add_filter( 'wp_mail_content_type',	array($this, 'set_html_content_type') );
			add_filter( 'wp_mail_charset',		array($this, 'set_charset') ); 
			reset( $toArray );
			foreach( $toArray as $to ){
				wp_mail( $to, $subject, $body, $headers );
			}
			remove_filter( 'wp_mail_content_type',	array($this, 'set_html_content_type') );
			remove_filter( 'wp_mail_charset',		array($this, 'set_charset') ); 
		}

		/* add log */
		if( $this->logger ){
			$log = new ntsEmailLogger();
			$log->setParam( 'from_email', $this->from );
			$log->setParam( 'from_name', $this->fromName );
			$log->setParam( 'subject', $subject );
			$log->setParam( 'body', $body );

			reset( $toArray );
			foreach( $toArray as $to ){
				$log->setParam( 'to_email', $to );
				$log->add();
			}
		}
		return true;
	}

	function getError(){
		return $this->error;
	}

	public function set_html_content_type()
	{
		$return = 'text/html';
		return $return;
	}
	public function set_charset( $charset )
	{
		$return = 'utf-8';
		return $return;
	}
}
