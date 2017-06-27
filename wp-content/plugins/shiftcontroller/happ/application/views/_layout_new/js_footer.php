<?php
$conf = HC_App::app_conf();
$time_min = $conf->get('time_min');
$time_max = $conf->get('time_max');

$t = HC_Lib::time();
$time_format = $t->timeFormat();

$cal_language = array(
	'days'			=> array( HCM::__('Sun'), HCM::__('Mon'), HCM::__('Tue'), HCM::__('Wed'), HCM::__('Thu'), HCM::__('Fri'), HCM::__('Sat'), HCM::__('Sun') ),
	'daysShort'		=> array( HCM::__('Sun'), HCM::__('Mon'), HCM::__('Tue'), HCM::__('Wed'), HCM::__('Thu'), HCM::__('Fri'), HCM::__('Sat'), HCM::__('Sun') ),
	'daysMin'		=> array( HCM::__('Sun'), HCM::__('Mon'), HCM::__('Tue'), HCM::__('Wed'), HCM::__('Thu'), HCM::__('Fri'), HCM::__('Sat'), HCM::__('Sun') ),
	'months'		=> array( HCM::__('Jan'), HCM::__('Feb'), HCM::__('Mar'), HCM::__('Apr'), HCM::__('May'), HCM::__('Jun'), HCM::__('Jul'), HCM::__('Aug'), HCM::__('Sep'), HCM::__('Oct'), HCM::__('Nov'), HCM::__('Dec') ),
	'monthsShort'	=> array( HCM::__('Jan'), HCM::__('Feb'), HCM::__('Mar'), HCM::__('Apr'), HCM::__('May'), HCM::__('Jun'), HCM::__('Jul'), HCM::__('Aug'), HCM::__('Sep'), HCM::__('Oct'), HCM::__('Nov'), HCM::__('Dec') ),
	'today'			=> 'Today',
	'clear'			=> 'Clear',
	);

$cal_language_js_code = array();
foreach( $cal_language as $k => $v ){
	$cal_language_js_code_line = '';

	$cal_language_js_code_line .= $k . ': ';
	if( is_array($v) ){
		$cal_language_js_code_line .= '[';
		$cal_language_js_code_line .= join(', ', array_map(create_function('$v', 'return "\"" . $v . "\"";'), $v));
		$cal_language_js_code_line .= ']';
	}
	else {
		$cal_language_js_code_line .= '"' . $v . '"';
	}
	$cal_language_js_code[] = $cal_language_js_code_line;
}
$cal_language_js_code = join(",\n", $cal_language_js_code);

$disable_weekdays = '';
$disable_weekdays_conf = $conf->get('disable_weekdays');
if( $disable_weekdays_conf ){
	if( ! is_array($disable_weekdays_conf) ){
		$disable_weekdays_conf = array($disable_weekdays_conf);
	}
	$disable_weekdays = join(',', $disable_weekdays_conf);
}
?>
<script language="JavaScript">
;(function($){
	$.fn.hc_datepicker.defaults.autoclose = true;
	$.fn.hc_datepicker.dates['en'] = {
<?php echo $cal_language_js_code; ?>
	};
<?php if( $disable_weekdays ) : ?>
	$.fn.hc_datepicker.defaults.daysOfWeekDisabled = [<?php echo $disable_weekdays; ?>];
<?php endif; ?>

}(jQuery));
</script>