<?php
global $NTS_TIME_WEEKDAYS_SHORT;
$NTS_TIME_WEEKDAYS_SHORT = array( 
/* translators: short Sunday */
	HCM::__('Sun'),
/* translators: short Monday */
	HCM::__('Mon'),
/* translators: short Tuesday */
	HCM::__('Tue'),
/* translators: short Wednesday */
	HCM::__('Wed'),
/* translators: short Thursday */
	HCM::__('Thu'),
/* translators: short Friday */
	HCM::__('Fri'),
/* translators: short Saturday */
	HCM::__('Sat')
	);

global $NTS_TIME_MONTH_NAMES;
$NTS_TIME_MONTH_NAMES = array(
/* translators: short January */
	HCM::__('Jan'),
/* translators: short February */
	HCM::__('Feb'),
/* translators: short March */
	HCM::__('Mar'),
/* translators: short April */
	HCM::__('Apr'),
/* translators: short May */
	HCM::__('May'),
/* translators: short June */
	HCM::__('Jun'),
/* translators: short July */
	HCM::__('Jul'),
/* translators: short August */
	HCM::__('Aug'),
/* translators: short September */
	HCM::__('Sep'),
/* translators: short October */
	HCM::__('Oct'),
/* translators: short November */
	HCM::__('Nov'),
/* translators: short December */
	HCM::__('Dec')
	);

global $NTS_TIME_MONTH_NAMES_REPLACE;
$NTS_TIME_MONTH_NAMES_REPLACE = array( 
/* translators: short January */
	'Jan'	=> HCM::__('Jan'),
/* translators: short February */
	'Feb'	=> HCM::__('Feb'),
/* translators: short March */
	'Mar'	=> HCM::__('Mar'),
/* translators: short April */
	'Apr'	=> HCM::__('Apr'),
/* translators: short May */
	'May'	=> HCM::__('May'),
/* translators: short June */
	'Jun'	=> HCM::__('Jun'),
/* translators: short July */
	'Jul'	=> HCM::__('Jul'),
/* translators: short August */
	'Aug'	=> HCM::__('Aug'),
/* translators: short September */
	'Sep'	=> HCM::__('Sep'),
/* translators: short October */
	'Oct'	=> HCM::__('Oct'),
/* translators: short November */
	'Nov'	=> HCM::__('Nov'),
/* translators: short December */
	'Dec'	=> HCM::__('Dec')
	);

/* new object oriented style */
class Hc_time extends DateTime {
	var $timeFormat = 'H:i';
	var $dateFormat = 'd/m/Y';
	var $weekdays = array();
	var $weekdaysShort = array();
	var $monthNames = array();
	var $timezone = '';
	var $weekStartsOn = 1;
	protected $disable_weekdays = array();

	function __construct( $time = 0, $tz = '' ){
//static $initCount;
//$initCount++;
//echo "<h2>init $initCount</h2>";
		if( strlen($time) == 0 )
			$ts = 0;
		if( ! $time )
			$time = time();
		if( is_array($time) )
			$time = $time[0];

		parent::__construct();
		if( $time > 0 ){
			$this->setTimestamp( $time );
		}
		else {
			$this->setNow();
		}

		$app_conf = HC_App::app_conf();

		if( ! $tz ){
			$tz = $app_conf ? $app_conf->get('timezone') : '';
		}
		if( $tz ){
			$this->setTimezone( $tz );
		}
		$this->weekStartsOn = $app_conf ? $app_conf->get('week_starts') : 0;

		$time_format = $app_conf ? $app_conf->get('time_format') : '';
		if( $time_format )
			$this->timeFormat = $time_format;
		$date_format = $app_conf ? $app_conf->get('date_format') : '';
		if( $date_format )
			$this->dateFormat = $date_format;
		}

	public function set_disable_weekdays( $disable_weekdays )
	{
		if( $disable_weekdays !== NULL ){
			if( ! is_array($disable_weekdays) ){
				$disable_weekdays = array($disable_weekdays);
			}
		}
		$this->disable_weekdays = $disable_weekdays;
		return $this;
	}
	public function disable_weekdays()
	{
		return $this->disable_weekdays;
	}

	public function modify( $modify )
	{
		parent::modify( $modify );
		return $this;
	}

	public function weekStartsOn()
	{
		return $this->weekStartsOn;
	}

	public function getDatesRange( $date, $range )
	{
		$save_ts = $this->getTimestamp();

		if( ! $date ){
			$date = $this->setNow()->formatDate_Db();
		}
		$disable_weekdays = $this->disable_weekdays();

		$t = HC_Lib::time();
		switch( $range ){
			case 'custom':
				if( strpos($date, '_') !== FALSE ){
					list( $start_date, $end_date ) = explode('_', $date);
				}
				else {
					$start_date = $end_date = $date;
				}
				break;

			case 'day':
				$start_date = $date;
				$end_date = 0;
				break;

			case 'all':
				$start_date = $end_date = 0;
				break;

			case 'upcoming':
				$start_date = $date;
				$end_date = NULL;
				break;

			case 'week':
			case 'month':
				$this->setDateDb( $date );
				list( $start_date, $end_date ) = $this->getDates( $range, TRUE );
				break;

			default:
				if( strpos($date, '_') !== FALSE ){
					list( $start_date, $end_date ) = explode('_', $date);
				}
				else {
					$start_date = $date;
					$t->setDateDb( $start_date );
					$t->modify( '+' . $range );
					$end_date = $t->formatDate_Db();
				}
				break;
		}

		$this->setTimestamp( $save_ts );
		$return = array( $start_date, $end_date );
		return $return;
	}

	function getDates( $range, $start_end = FALSE )
	{
		$save_ts = $this->getTimestamp();

		$disable_weekdays = $this->disable_weekdays();
		$start_date = $end_date = 0;

		switch( $range ){
			case 'day':
				$start_date = $end_date = $this->formatDate_Db();
				break;

			case 'week':
				$this->setStartWeek();
				$start_date = $this->formatDate_Db();
				$this->setEndWeek();
				$end_date = $this->formatDate_Db();
				break;

			case 'month':
				$this->setStartMonth();
				$start_date = $this->formatDate_Db();
				$this->setEndMonth();
				$end_date = $this->formatDate_Db();
				break;
		}

		$return = array();

	// start and end only
		if( $start_end ){
			if( $disable_weekdays && (count($disable_weekdays) < 7)){
				$this->setDateDb( $start_date );
				$this_weekday = $this->getWeekDay();
				while( in_array($this_weekday, $disable_weekdays) ){
					$this->modify('+1 day');
					$this_weekday = $this->getWeekDay();
					$start_date = $this->formatDate_Db();
				}

				$this->setDateDb( $end_date );
				$this_weekday = $this->getWeekDay();
				while( in_array($this_weekday, $disable_weekdays) ){
					$this->modify('-1 day');
					$this_weekday = $this->getWeekDay();
					$end_date = $this->formatDate_Db();
				}
			}

			$return[] = $start_date;
			$return[] = $end_date;
		}
	// all
		else {
			if( $start_date && $end_date ){
				$this->setDateDb( $start_date );
				$rex_date = $start_date;
				while( $rex_date <= $end_date ){
					if( $disable_weekdays ){
						$this_weekday = $this->getWeekDay();
						if( ! in_array($this_weekday, $disable_weekdays) ){
							$return[] = $rex_date;
						}
					}
					else {
						$return[] = $rex_date;
					}

					$this->modify('+1 day');
					$rex_date = $this->formatDate_Db();
				}
			}
		}

		$this->setTimestamp( $save_ts );
		return $return;
	}

	function formatTimeRange( $ts1, $ts2 )
	{
		$return = array();
		$this->setTimestamp( $ts1 );
		$date1 = $this->formatDate_Db();
		$return[] = $this->formatDateFull() . ' ' . $this->formatTime();

		if( $ts2 > $ts1 ){
			$this->setTimestamp( $ts2 );
			$date2 = $this->formatDate_Db();
			if( $date2 == $date1 ){
				$return[] = $this->formatTime();
			}
			else {
				$return[] = $this->formatDateFull() . ' ' . $this->formatTime();
			}
		}

		$return = join( ' - ', $return );
		return $return;
	}

	function formatDateRange( $date1, $date2 )
	{
		$return = array();
		$skip = array();

		if( $date1 == $date2 ){
			$this->setDateDb( $date1 );
			$return = $this->formatDate();
			return $return;
			}

		$this->setDateDb( $date1 );
		$year1 = $this->getYear();
		$month1 = $this->getMonth();

		$this->setDateDb( $date2 );
		$year2 = $this->getYear();
		$month2 = $this->getMonth();

		if( $year2 == $year1 )
			$skip['year'] = TRUE;
		if( $month2 == $month1 )
			$skip['month'] = TRUE;

		if( $skip ){
			$date_format = $this->dateFormat;
			$date_format_short = $date_format;

			$tags = array('m', 'n', 'M');
			foreach( $tags as $t ){
				$pos_m_original = strpos($date_format_short, $t);
				if( $pos_m_original !== FALSE )
					break;
			}

			if( isset($skip['year']) ){
				$pos_y = strpos($date_format_short, 'Y');
				if( $pos_y == 0 ){
					$date_format_short = substr_replace( $date_format_short, '', $pos_y, 2 );
				}
				else {
					$date_format_short = substr_replace( $date_format_short, '', $pos_y - 1, 2 );
				}
			}
			if( isset($skip['month']) ){
				$tags = array('m', 'n', 'M');
				foreach( $tags as $t ){
					$pos_m = strpos($date_format_short, $t);
					if( $pos_m !== FALSE )
						break;
				}

				// month going first, do not replace
				if( $pos_m_original == 0 ){
//					$date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
				}
				else {
					// month going first, do not replace
					if( $pos_m == 0 ){
						$date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
					}
					else {
						$date_format_short = substr_replace( $date_format_short, '', $pos_m - 1, 2 );
					}
				}
			}

			if( $pos_y == 0 ){ // skip year in the second part
				$date_format1 = $date_format;
				$date_format2 = $date_format_short;
			}
			else {
				$date_format1 = $date_format_short;
				$date_format2 = $date_format;
			}

			$this->setDateDb( $date1 );
			$return[] = $this->formatDate( $date_format1 );
			$this->setDateDb( $date2 );
			$return[] = $this->formatDate( $date_format2 );
		}
		else {
			$this->setDateDb( $date1 );
			$return[] = $this->formatDate();
			$this->setDateDb( $date2 );
			$return[] = $this->formatDate();
		}
		$return = join( ' - ', $return );
		return $return;
	}

	function formatToDatepicker( $dateFormat = '' )
    {
		if( ! $dateFormat )
			$dateFormat = $this->dateFormat;

		$pattern = array(
			//day
			'd',	//day of the month
			'j',	//3 letter name of the day
			'l',	//full name of the day
			'z',	//day of the year

			//month
			'F',	//Month name full
			'M',	//Month name short
			'n',	//numeric month no leading zeros
			'm',	//numeric month leading zeros

			//year
			'Y', //full numeric year
			'y'	//numeric year: 2 digit
			);

		$replace = array(
			'dd','d','DD','o',
			'MM','M','m','mm',
			'yyyy','y'
		);
		foreach($pattern as &$p){
			$p = '/'.$p.'/';
		}
		return preg_replace( $pattern, $replace, $dateFormat );
	}

	function sortWeekdays( $wds ) // sort weekdays according to weekStartsOn
	{
		$return = array();
		$later = array();

		sort( $wds );
		reset( $wds );
		foreach( $wds as $wd ){
			if( $wd < $this->weekStartsOn )
				$later[] = $wd;
			else
				$return[] = $wd;
		}
		$return = array_merge( $return, $later );
		return $return;
	}

	function formatWeekdays( $wds = array() )
	{
		$wds = $this->sortWeekdays( $wds );

		$weekdays_order = array();
		$weekdays_order_index = array();
		$order = 0;
		for( $ii = 0; $ii <= 6; $ii++ ){
			$wi = $this->weekStartsOn + $ii;
			if( $wi >= 7 ){
				$wi = $wi - 7;
			}
			$weekdays_order_index[ $wi ] = $order;
			$weekdays_order[ $order ] = $wi;
			$order++;
		}

		$weekdays = array();
		$wdi = 0;
		reset( $wds );
		foreach( $wds as $wd ){
			if( ! isset($weekdays[$wdi]) ){
				$weekdays[$wdi] = $wd;
			}
			elseif( is_array($weekdays[$wdi]) ){
				$my_index = $weekdays_order_index[$wd];
				$previous_wd = isset($weekdays_order[($my_index - 1)]) ? $weekdays_order[($my_index - 1)] : -1;
				if( $weekdays[$wdi][1] == $previous_wd ){
					$weekdays[$wdi][1] = $wd;
				}
				else {
					$wdi++;
					$weekdays[$wdi] = $wd;
				}
			}
			else {
				$my_index = $weekdays_order_index[$wd];
				$previous_wd = isset($weekdays_order[($my_index - 1)]) ? $weekdays_order[($my_index - 1)] : -1;
				if( $weekdays[$wdi] == $previous_wd ){
					$weekdays[$wdi] = array($weekdays[$wdi], $wd);
				}
				else {
					$wdi++;
					$weekdays[$wdi] = $wd;
				}
			}
		}

	/* build view */
		$weekday_view = array();
		reset( $weekdays );
		foreach( $weekdays as $wd ){
			if( is_array($wd) ){
				$weekday_view[] = $this->formatWeekdayShort($wd[0]) . ' - ' . $this->formatWeekdayShort($wd[1]);
			}
			else {
				$weekday_view[] = $this->formatWeekdayShort($wd);
			}
		}

		return $weekday_view;
	}

	function setNow(){
		$this->setTimestamp( time() );
		return $this;
		}

	function differ( $other )
	{
		if( ! is_object($other) ){
			$other_date = $other;
			$other = HC_Lib::time();
			$other->setDateDb( $other_date );
		}
		else {
			$other_date = $other->formatDate_Db();
		}

		$this_date = $this->formatDate_Db();
		if( $this_date == $other_date ){
			$delta = 0;
		}
		elseif( $this_date > $other_date ){
			$delta = $this->getTimestamp() - $other->getTimestamp();
		}
		else {
			$delta = $other->getTimestamp() - $this->getTimestamp();
		}

		$return = 0;
		if( $delta ){
			$return = floor( $delta / (24 * 60 * 60) );
		}
		return $return;
	}

	function getDatesOfMonth(){
		$return = array();

		$this->setEndMonth();
		$end_month = $this->formatDate_Db();

		$this->setStartMonth();
		$rex_date = $this->formatDate_Db();
		while( $rex_date <= $end_month ){
			$return[] = $rex_date;
			$this->modify( '+1 day' );
			$rex_date = $this->formatDate_Db();
			}
		return $return;
		}
		
	static function expandPeriodString( $what, $multiply = 1 ){
		$string = '';
		switch( $what ){
			case 'd':
				$string = '+' . 1 * $multiply . ' days';
				break;
			case '2d':
				$string = '+' . 2 * $multiply . ' days';
				break;
			case 'w':
				$string = '+' . 1 * $multiply . ' weeks';
				break;
			case '2w':
				$string = '+' . 2 * $multiply . ' weeks';
				break;
			case '3w':
				$string = '+' . 3 * $multiply . ' weeks';
				break;
			case '6w':
				$string = '+' . 6 * $multiply . ' weeks';
				break;
			case 'm':
				$string = '+' . 1 * $multiply . ' months';
				break;
			}
		return $string;
		}

	function setTimezone( $tz ){
		if( is_array($tz) )
			$tz = $tz[0];

//		if( preg_match('/^-?[\d\.]$/', $tz) ){
//			$currentTz = ($tz >= 0) ? '+' . $tz : $tz;
//			$tz = "Etc/GMT$currentTz";
//			echo "<br><br>Setting timezone as Etc/GMT$currentTz<br><br>";
//			}
		if( ! $tz )
			$tz = date_default_timezone_get();

		$this->timezone = $tz;
		$tz = new DateTimeZone($tz);
		parent::setTimezone( $tz );
		}

	function getLastDayOfMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();

		$this->setDateTime( $thisYear, ($thisMonth + 1), 0, 0, 0, 0 );
		$return = $this->format( 'j' );
		return $return;
		}

	function getTimestamp(){
		if( function_exists('date_timestamp_get') ){
			return parent::getTimestamp();
			}
		else {
			$return = $this->format('U');
			return $return;
			}
		}

	function setTimestamp( $ts )
	{
		if( function_exists('date_timestamp_set') ){
			parent::setTimestamp( $ts );
		}
		else {
			$strTime = '@' . $ts;
			parent::__construct( $strTime );
			$this->setTimezone( $this->timezone );
		}
		return $this;
	}

	static function splitDate( $string ){
		$year = substr( $string, 0, 4 );
		$month = substr( $string, 4, 2 );
		$day = substr( $string, 6, 4 );
		$return = array( $year, $month, $day );
		return $return;
		}

	function timestampFromDbDate( $date ){
		list( $year, $month, $day ) = Hc_time::splitDate( $date );
		$this->setDateTime( $year, $month, $day, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function getParts(){
		$return = array( $this->format('Y'), $this->format('m'), $this->format('d'), $this->format('H'), $this->format('i') );
		return $return;
		}

	function getYear(){
		$return = $this->format('Y');
		return $return;
		}

	function getMonth(){
		$return = $this->format('m');
		return $return;
		}

	function getMonthName(){
		global $NTS_TIME_MONTH_NAMES;
		$thisMonth = (int) $this->getMonth();
		$return = $NTS_TIME_MONTH_NAMES[ $thisMonth - 1 ];
		return $return;
		}

	function getDay(){
		$return = $this->format('d');
		return $return;
		}

	function getDayShort()
	{
		$return = $this->format('j');
		return $return;
	}

	function getStartDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, $thisDay, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function setStartDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, $thisDay, 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function setNextDay(){
		$this->setStartDay();
		$this->modify( '+1 day' );
		}

	function getEndDay(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$thisDay = $this->getDay();

		$this->setDateTime( $thisYear, $thisMonth, ($thisDay + 1), 0, 0, 0 );
		$return = $this->getTimestamp();
		return $return;
		}

	function setStartWeek(){
		$this->setStartDay();
		$weekDay = $this->getWeekday();

		while( $weekDay != $this->weekStartsOn ){
			$this->modify( '-1 day' );
			$weekDay = $this->getWeekday();
			}
		return $this;
		}

	function setEndWeek(){
		$this->setStartDay();
		$this->modify( '+1 day' );
		$weekDay = $this->getWeekday();

		while( $weekDay != $this->weekStartsOn ){
			$this->modify( '+1 day' );
			$weekDay = $this->getWeekday();
			}
		$this->modify( '-1 day' );
		return $this;
		}

	function setStartMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, $thisMonth, 1, 0, 0, 0 );
		return $this;
		}

	function setEndMonth(){
		$thisYear = $this->getYear(); 
		$thisMonth = $this->getMonth();
		$this->setDateTime( $thisYear, ($thisMonth + 1), 1, 0, 0, -1 );
		return $this;
		}

	function setStartYear(){
		$thisYear = $this->getYear(); 
		$this->setDateTime( $thisYear, 1, 1, 0, 0, 0 );
		return $this;
		}

	function timezoneShift(){
		$return = 60 * 60 * $this->timezone;
		return $return;
		}

	function setDateTime( $year, $month, $day, $hour, $minute, $second ){
		$this->setDate( $year, $month, $day );
		$this->setTime( $hour, $minute, $second );
		return $this;
		}

	function setDateDb( $date ){
		list( $year, $month, $day ) = Hc_time::splitDate( $date );
		$this->setDateTime( $year, $month, $day, 0, 0, 0 );
		return $this;
		}

	function formatPeriodOfDay( $start, $end )
	{
		if( 
			( $start == 0 ) &&
			( ( $end == 0 ) OR ( $end == 24*60*60 ) )
			){
/* translators: duration, during all day */
			$return = HCM::__('All Day');
		}
		else {
			$return = $this->formatTimeOfDay($start) . ' - ' .  $this->formatTimeOfDay($end);
		}
		return $return;
	}

	function formatTimeOfDay( $ts ){
		$this->setDateDb('20130315');
		if( $ts ){
			$this->modify( '+' . $ts . ' seconds' );
		}
		return $this->formatTime();
		}

	public function timeFormat()
	{
		return $this->timeFormat;
	}

	function formatTime( $duration = 0, $displayTimezone = 0 )
	{
		$return = $this->format( $this->timeFormat );
		if( $duration ){
			$this->modify( '+' . $duration . ' seconds' );
			$return .= ' - ' . $this->format( $this->timeFormat );
		}

		if( $displayTimezone ){
			$return .= ' [' . Hc_time::timezoneTitle($this->timezone) . ']';
		}
		return $return;
	}

	function formatDate( $format = '' ){
		global $NTS_TIME_MONTH_NAMES_REPLACE;
		if( ! $format )
			$format = $this->dateFormat;

		$return = $this->format( $format );
	// replace months 
		$return = str_replace( array_keys($NTS_TIME_MONTH_NAMES_REPLACE), array_values($NTS_TIME_MONTH_NAMES_REPLACE), $return );
		return $return;
		}

	static function formatDateParam( $year, $month, $day ){
		$return = sprintf("%04d%02d%02d", $year, $month, $day);
		return $return;
		}

	function formatDate_Db(){
		$dateFormat = 'Ymd';
		$return = $this->format( $dateFormat );
		return $return;
		}

	function formatTime_Db(){
		$dateFormat = 'Hi';
		$return = $this->format( $dateFormat );
		return $return;
		}

	function getWeekday(){
		$return = $this->format('w');
		return $return;
		}

	function formatWeekdayShort( $wd = -1 )
	{
		global $NTS_TIME_WEEKDAYS_SHORT;
		if( $wd == -1 )
			$wd = $this->format('w');
		$return = $NTS_TIME_WEEKDAYS_SHORT[ $wd ];
		return $return;
	}

	function formatFull(){
		$return = $this->formatWeekdayShort() . ', ' . $this->formatDate() . ' ' . $this->formatTime();
		return $return;
		}

	function formatDateFull(){
		$return = $this->formatWeekdayShort() . ', ' . $this->formatDate();
		return $return;
		}

	static function timezoneTitle( $tz ){
		if( is_array($tz) )
			$tz = $tz[0];
		$tzobj = new DateTimeZone( $tz );
		$dtobj = new DateTime();
		$dtobj->setTimezone( $tzobj );
		$offset = $tzobj->getOffset($dtobj);

		$offsetString = 'GMT';
		$offsetString .= ($offset >= 0) ? '+' : '';
		$offsetString = $offsetString . ( $offset/(60 * 60) );

		$return = $tz . ' (' . $offsetString . ')';
		return $return;
		}

	static function getTimezones(){
		$skipStarts = array('Brazil/', 'Canada/', 'Chile/', 'Etc/', 'Mexico/', 'US/');
		$return = array();
		$timezones = timezone_identifiers_list();
		reset( $timezones );
		foreach( $timezones as $tz ){
			if( strpos($tz, "/") === false )
				continue;
			$skipIt = false;
			reset( $skipStarts );
			foreach( $skipStarts as $skip ){
				if( substr($tz, 0, strlen($skip)) == $skip ){
					$skipIt = true;
					break;
					}
				}
			if( $skipIt )
				continue;

			$tzTitle = Hc_time::timezoneTitle( $tz );
			$return[] = array( $tz, $tzTitle );
			}
		return $return;
		}

	static function formatPeriodExtraShort( $ts, $limit = 'day' )
	{
		if( $limit == 'day' )
			$day = (int) ($ts/(24 * 60 * 60));
		else
			$day = 0;
		$hour = (int) ( ($ts - (24 * 60 * 60)*$day)/(60 * 60));
		$minute = (int) ( $ts - (24 * 60 * 60)*$day - (60 * 60)*$hour ) / 60;

		switch( $limit ){
			case 'day':
				$return = $day + ($hour/24) + ($minute/(24*60));
				$return = sprintf('%.2f', $return );
				$return = $return + 0;

/* translators: short duration format in days, for example 4 d */
				$return = sprintf( HCM::__('%s d'), $return );
				break;
			case 'hour':
				$return = $day * 24 + $hour + ($minute/60);
				$return = sprintf('%.2f', $return );
				$return = $return + 0;

/* translators: short duration format in hours, for example 1.25 hr */
				$return = sprintf( HCM::__('%s hr'), $return );
				break;
			case 'min':
				$return = $day * 24 *60 + $hour * 60 + $minute;
				$return = sprintf('%.2f', $return );
				$return = $return + 0;

/* translators: short duration format in minutes, for example 25 min */
				$return = sprintf( HCM::__('%s min'), $return );
				break;
		}

		return $return;
	}

	static function formatPeriodShort( $ts, $limit = 'day' )
	{
		if( $limit == 'day' )
			$day = (int) ($ts/(24 * 60 * 60));
		else
			$day = 0;
		$hour = (int) ( ($ts - (24 * 60 * 60)*$day)/(60 * 60));
		$minute = (int) ( $ts - (24 * 60 * 60)*$day - (60 * 60)*$hour ) / 60;

		$formatArray = array();
		if( $day > 0 ){
			$formatArray[] = $day;
			}
		$formatArray[] = sprintf( "%02d", $hour );
		$formatArray[] = sprintf( "%02d", $minute );

		$verbose = join( ':', $formatArray );
		return $verbose;
	}

	static function formatPeriod( $ts, $limitMeasure = '' ){
//		$conf =& ntsConf::getInstance();
//		$limitMeasure = $conf->get('limitTimeMeasure');
//		$limitMeasure = '';

		switch( $limitMeasure ){
			case 'minute':
				$day = 0;
				$hour = 0;
				$minute = (int) ( $ts ) / 60;
				break;
			case 'hour':
				$day = 0;
				$hour = (int) ( ($ts)/(60 * 60));
				$minute = (int) ( $ts - (60 * 60)*$hour ) / 60;
				break;
			default:
				$day = (int) ($ts/(24 * 60 * 60));
				$hour = (int) ( ($ts - (24 * 60 * 60)*$day)/(60 * 60));
				$minute = (int) ( $ts - (24 * 60 * 60)*$day - (60 * 60)*$hour ) / 60;
				break;
			}

		$formatArray = array();
		if( $day > 0 ){
			$formatArray[] = sprintf( HCM::_n('%d Day', '%d Days', $day), $day );
		}
		if( $hour > 0 ){
			$formatArray[] = sprintf( HCM::_n('%d Hour', '%d Hours', $hour), $hour );
		}
		if( $minute > 0 ){
			$formatArray[] = sprintf( HCM::_n('%d Minute', '%d Minutes', $minute), $minute );
		}

		$verbose = join( ' ', $formatArray );
		return $verbose;
		}

	function getWeekOfMonth()
	{
		$return = 0;
		$keepDate = $this->formatDate_Db();
		$thisMonth = $this->getMonth();
		$testMonth = $thisMonth;
		while( $testMonth == $thisMonth )
		{
			$return++;
			$this->modify( '-1 week' );
			$testMonth = $this->getMonth();
		}
		$this->setDateDb( $keepDate );
		return $return;
	}

	function formatWeekOfMonth()
	{
		$week = $this->getWeekOfMonth();
		$text = array(
			1	=> HCM::__('1st'),
			2	=> HCM::__('2nd'),
			3	=> HCM::__('3rd'),
			4	=> HCM::__('4th'),
			5	=> HCM::__('5th'),
			);
		return $text[$week];
	}

	function getWeekOfMonthFromEnd()
	{
		$return = 0;
		$keepDate = $this->formatDate_Db();
		$thisMonth = $this->getMonth();
		$testMonth = $thisMonth;
		while( $testMonth == $thisMonth )
		{
			$return++;
			$this->modify( '+1 week' );
			$testMonth = $this->getMonth();
		}
		$this->setDateDb( $keepDate );
		return $return;
	}

	function formatWeekOfMonthFromEnd()
	{
		$week = $this->getWeekOfMonthFromEnd();
		$text = array(
			1	=> HCM::__('1st'),
			2	=> HCM::__('2nd'),
			3	=> HCM::__('3rd'),
			4	=> HCM::__('4th'),
			5	=> HCM::__('5th'),
			);
		$add = HCM::__('From End');
		return $text[$week] . ' ' . $add;
	}

	function getMonthMatrix( $endDate = '' ){
		$matrix = array();
		$currentMonthDay = 0;
		$startDate = $this->formatDate_Db();

		if( $endDate )
			$this->setDateDb( $endDate );
		else
			$this->setEndMonth();
		$this->setEndWeek();
		$endDate = $this->formatDate_Db();

		$this->setDateDb( $startDate );
		$this->setStartWeek();
		$rexDate = $this->formatDate_Db();

		while( $rexDate <= $endDate )
		{
			$week = array();
			for( $weekDay = 0; $weekDay <= 6; $weekDay++ )
			{
				$thisWeekday = $this->getWeekday();
				$week[ $thisWeekday ] = $rexDate;
				$this->modify('+1 day');
				$rexDate = $this->formatDate_Db();
			}
			$matrix[] = $week;
		}
		return $matrix;
		}
	}
?>