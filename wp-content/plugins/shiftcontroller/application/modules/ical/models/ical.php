<?php
include_once( dirname(__FILE__) . '/iCalcreator.class.php' );

// class Ical_HC_Model extends MY_Model_Virtual
class Ical_HC_Model
{
	protected $shifts = array();
	protected $timezone = '';

	public function __construct()
	{
		$tz = HC_Lib::timezone();
		$this->set_timezone( $tz );
	}

	public function set_timezone( $tz )
	{
		$this->timezone = $tz;
	}
	public function timezone()
	{
		return $this->timezone;
	}

	public function add( $sh )
	{
		$this->shifts[] = $sh;
	}

	public function print_out()
	{
		$t = HC_Lib::time();

		$t2 = HC_Lib::time();
		$t2->setTimezone('UTC');

		$timezone = $this->timezone();

		$web_dir = HC_Lib::web_dir_name( HC_Lib::link()->url() );
		$my_unique = md5($web_dir);
		$my_unique = 'hc-ical-' . $my_unique;

		$cal = new hc_vcalendar(); // initiate new CALENDAR

		// $cal->setConfig( 'unique_id', $web_dir );
		$cal->setConfig( 'unique_id', $my_unique );
//		$cal->setProperty( 'method', 'publish' );
		$cal->setProperty( 'method', 'request' );

		$cal->setProperty( 'x-wr-timezone', $timezone );

		$vtz = new hc_vtimezone();
		$vtz->setProperty( 'tzid', $timezone );
		$cal->addComponent( $vtz );

		reset( $this->shifts );
		foreach( $this->shifts as $sh ){
			$views = array(
				'location'	=> $sh->present_location(HC_PRESENTER::VIEW_RAW),
				'user'		=> $sh->present_user(HC_PRESENTER::VIEW_RAW),
				);

			$event = new hc_vevent(); // initiate a new EVENT
			$event->setProperty( 'uid', 'obj-' . $sh->id . '-' . $my_unique );

			// $event->setProperty( 'summary', $views['user'] . ' @ ' . $views['location'] );
			// $event->setProperty( 'description', $views['user'] . ' @ ' . $views['location']  );

			$summary = $views['location'];
			$summary = $views['user'] . ' @ ' . $views['location'];
			// $description = $views['location'];
			$description = $views['user'] . ' @ ' . $views['location'];

// $event->setAttendee( 'test@test.com' );
// $event->setOrganizer( 'test@test.com' );

			$t->setDateDb( $sh->date );
			$t->modify( '+' . $sh->start . ' seconds' );

			list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
			// $event->setProperty( 'dtstart', $year, $month, $day, $hour, $min, 00, 'Z' );  // 24 dec 2006 19.30
			$event->setProperty( 'dtstart', $year, $month, $day, $hour, $min, 00, $timezone );

			$t2->setTimestamp( $t->getTimestamp() );
			list( $year, $month, $day, $hour, $min ) = $t2->getParts(); 
			$event->setProperty( 'dtstart', $year, $month, $day, $hour, $min, 00, 'Z' );  // 24 dec 2006 19.30

			// $t->modify( '+' . $sh->get_duration() . ' seconds' );
			// list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
			// $event->setProperty( 'duration', 0,		0,		0,		0,		$a->getProp('duration') );
			$event->setProperty( 'duration', 0,		0,		0,		0,		$sh->get_duration(FALSE) );

			$t2->setTimestamp( $t->getTimestamp() );
			$t2->modify( '+' . $sh->get_duration() . ' seconds' );
			list( $year, $month, $day, $hour, $min ) = $t2->getParts(); 
			// $event->setProperty( 'dtend', $year, $month, $day, $hour, $min, 00, 'Z' );  // 24 dec 2006 19.30

			$add_notes = TRUE;

			if( ! $sh->note ){
				$add_notes = FALSE;
			}
			else {
				$acl = HC_App::acl();

				if( ! $acl->set_object($sh)->can('notes_view') ){
					$add_notes = FALSE;
				}
			}

			if( $add_notes ){
				$sh->note
					->include_related( 'author', array('id', 'first_name', 'last_name', 'active'), TRUE, TRUE )
					;
				$notes = $sh->note->get();

				$current_user = HC_App::model('user');
				$current_user->get_by_id(0);
				$acl->set_user( $current_user );

				$notes = $acl->set_user( $current_user )->filter( $notes, 'view' );

				if( $notes ){
					$comments = array();
					foreach( $notes as $note ){
						$noteText = $note->content;
						$noteUserView = $note->author->present_title( HC_PRESENTER::VIEW_RAW );
						$comments[] = $noteUserView . ': ' . $noteText;
					}
					$commentsView = join( ", ", $comments );
					$event->setProperty( 'comment', $commentsView );
					$description .= " (" . $commentsView . ')';

				// only comments
					// $comments = array();
					// reset( $notes );
					// foreach( $notes as $note ){
						// $noteText = $note->content;
						// $comments[] = $noteText;
					// }
					// $commentsView = join( ", ", $comments );
					// $description = $commentsView;
					// $summary = $commentsView;
				}
			}

			$event->setProperty( 'location', $views['location'] );
			$event->setProperty( 'description', $description );
			$event->setProperty( 'summary', $summary );

			$cal->addComponent( $event );

			continue;
		}

		$return = $cal->createCalendar();                   // generate and get output in string
		return $return;
	}
}