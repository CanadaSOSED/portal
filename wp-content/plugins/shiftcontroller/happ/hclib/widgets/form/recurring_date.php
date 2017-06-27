<?php
class HC_Form_Input_Recurring_Date extends HC_Form_Input_Composite
{
	protected $enabled = array('single', 'recurring');

	function __construct( $name = '' )
	{
		parent::__construct( $name );

		$name = $this->name();

		$this->fields['recurring'] = HC_Html_Factory::input('hidden', $name . '_recurring' )
			// ->set_value('single')
			->set_value('recurring')
			;
		$this->fields['datesingle'] = HC_Html_Factory::input('date', $name . '_datesingle' );

		$this->fields['datestart'] = HC_Html_Factory::input('date', $name . '_datestart' );
		$this->fields['dateend'] = HC_Html_Factory::input('date', $name . '_dateend' );
		$this->fields['repeat'] = HC_Html_Factory::input('radio', $name . '_repeat' )
			->set_value('daily')
			;

		$this->fields['weeklycustom'] = HC_Html_Factory::input('checkbox_set', $name . '_weeklycustom' ); 
		$this->fields['biweeklycustom1'] = HC_Html_Factory::input('checkbox_set', $name . '_biweeklycustom1' ); 
		$this->fields['biweeklycustom2'] = HC_Html_Factory::input('checkbox_set', $name . '_biweeklycustom2' ); 
		$this->fields['inoutin'] = HC_Html_Factory::input('text', $name . '_inoutin' ); 
		$this->fields['inoutout'] = HC_Html_Factory::input('text', $name . '_inoutout' ); 
	}

	function set_enabled( $enabled )
	{
		if( ! is_array($enabled) ){
			$enabled = array( $enabled );
		}
		$this->enabled = $enabled;
	}
	function enabled()
	{
		return $this->enabled;
	}

	function set_value( $value = array() )
	{
		if( ! is_array($value) ){
			$value = $this->_unserialize( $value );
		}

		if( 
			isset($value['recurring']) && 
			($value['recurring'] == 'single') && 
			(! isset($value['datesingle'])) &&
			isset($value['datestart'])
			){
				$value['datesingle'] = $value['datestart'];
			}

		parent::set_value( $value );

		$value = $this->value();

		$value = $this->_serialize($value);
		$this->value = $value;
	}

	function dates_details()
	{
		$value = $this->value(TRUE);
		$dates = $this->dates();

		$return = array();

		$t = HC_Lib::time();

		if( $value['recurring'] == 'single' ){
			if( isset($value['datesingle']) ){
				$t->setDateDb( $value['datesingle'] );
			}
			elseif( isset($value['datestart']) ){
				$t->setDateDb( $value['datestart'] );
			}
			$return[] = $t->formatDate();
		}
		else {
			$return[] = $t->formatDateRange( $value['datestart'], $value['dateend'] );
			$t->setDateDb( $value['datestart'] );

			if( $value['dateend'] > $value['datestart'] ){
				switch( $value['repeat'] ){
					case 'daily':
						$return[] = HCM::__('Daily');
						break;

					case 'weekday':
						$return[] = HCM::__('Every Weekday') . ' (' . $t->formatWeekdayShort(1) .  ' - ' . $t->formatWeekdayShort(5) . ')';
						break;

					case 'weekly':
						$return[] = HCM::__('Weekly') . ' (' . $t->formatWeekdayShort() . ')';
						break;

					case 'weeklycustom':
						$custom_days = array();
						foreach( $value['weeklycustom'] as $wkd ){
							$custom_days[] = $t->formatWeekdayShort($wkd);
						}
						$custom_days = join(', ', $custom_days);
						$return[] = HCM::__('Weekly') . ' (' . $custom_days . ')';
						break;

					case 'biweeklycustom':
						$custom_days1 = array();
						foreach( $value['biweeklycustom1'] as $wkd ){
							$custom_days1[] = $t->formatWeekdayShort($wkd);
						}
						if( ! $custom_days1 ){
							$custom_days1[] = '-';
						}
						$custom_days1 = join(', ', $custom_days1);
						$custom_days2 = array();
						foreach( $value['biweeklycustom2'] as $wkd ){
							$custom_days2[] = $t->formatWeekdayShort($wkd);
						}
						if( ! $custom_days2 ){
							$custom_days2[] = '-';
						}
						$custom_days2 = join(', ', $custom_days2);
						$return[] = HCM::__('Biweekly') . ' (' . $custom_days1 . ' / ' . $custom_days2 . ')';
						break;

					case 'inout':
						$line = array();

						$line[] = sprintf( HCM::_n('%s Day On', '%s Days On', $value['inoutin']), $value['inoutin']);
						$line[] = '/';
						$line[] = sprintf( HCM::_n('%s Day Off', '%s Days Off', $value['inoutout']), $value['inoutout']);

						$return[] = join(' ', $line);
						break;

					case 'monthlyday':
						$return[] = HCM::__('Monthly') . ' (' .  join(', ', array($t->formatWeekdayShort(), $t->formatWeekOfMonth())) . ')';
						break;

					case 'monthlydayend':
						$return[] = HCM::__('Monthly') . ' (' .  join(', ', array($t->formatWeekdayShort(), $t->formatWeekOfMonthFromEnd())) . ')';
						break;

					case 'monthlydate':
						$return[] = HCM::__('Monthly') . ' (' . HCM::__('Day') . ': ' . $t->getDay() . ')';
						break;
				}
			}
		}

		if( isset($value['dateend']) ){
			if( $value['dateend'] > $value['datestart'] ){
				$return[] = sprintf(HCM::_n('%d Day In Total', '%d Days In Total', count($dates)), count($dates));
			}
		}

		// $return = join(', ', $return);
		return $return;
	}

	function dates( $serialized = NULL )
	{
		$return = array();

		if( $serialized === NULL ){
			$serialized = $this->value();
		}

		$value = $this->_unserialize( $serialized );

		$t = HC_Lib::time();
		
		if( $value['recurring'] == 'single' ){
			$return[] = $value['datesingle'];
		}
		else {
			$t->setDateDb( $value['datestart'] );
			$rex_date = $value['datestart'];
			while( $rex_date <= $value['dateend'] ){
				switch( $value['repeat'] ){
					case 'daily':
						$return[] = $rex_date;
						$t->modify( '+1 day' );
						break;

					case 'weekday':
						if( ! in_array($t->getWeekday(), array(0,6)) ){
							$return[] = $rex_date;
						}
						$t->modify( '+1 day' );
						while( in_array($t->getWeekday(), array(0,6)) ){
							$t->modify( '+1 day' );
						}
						break;

					case 'weekly':
						$return[] = $rex_date;
						$t->modify( '+1 week' );
						break;

					case 'weeklycustom':
						$custom_weekday = $value['weeklycustom'];
						if( in_array($t->getWeekday(), $custom_weekday) ){
							$return[] = $rex_date;
						}
						$t->modify( '+1 day' );
						if( $custom_weekday ){
							while( ! in_array($t->getWeekday(), $custom_weekday) ){
								$t->modify( '+1 day' );
							}
						}
						break;

					case 'biweeklycustom':
						if( ! isset($biweeklycustom_current_week) ){
							$biweeklycustom_current_week = 1;
						}

						$custom_weekday1 = $value['biweeklycustom1'];
						$custom_weekday2 = $value['biweeklycustom2'];

						if( $biweeklycustom_current_week == 1 ){
							$custom_weekday = $value['biweeklycustom1'];
						}
						else {
							$custom_weekday = $value['biweeklycustom2'];
						}

						$this_weekday = $t->getWeekday();
						if( in_array($this_weekday, $custom_weekday) ){
							$return[] = $rex_date;
						}
						$t->modify( '+1 day' );

						if( $custom_weekday1 OR $custom_weekday2 ){
							$this_weekday = $t->getWeekday();
							if( $this_weekday == $t->weekStartsOn() ){
								// switch week
								$biweeklycustom_current_week = ($biweeklycustom_current_week == 1) ? 2 : 1;
								if( $biweeklycustom_current_week == 1 ){
									$custom_weekday = $value['biweeklycustom1'];
								}
								else {
									$custom_weekday = $value['biweeklycustom2'];
								}
							}

							while( ! in_array($this_weekday, $custom_weekday) ){
								$t->modify( '+1 day' );
								$this_weekday = $t->getWeekday();

								if( $this_weekday == $t->weekStartsOn() ){
									// switch week
									$biweeklycustom_current_week = ($biweeklycustom_current_week == 1) ? 2 : 1;
									if( $biweeklycustom_current_week == 1 ){
										$custom_weekday = $value['biweeklycustom1'];
									}
									else {
										$custom_weekday = $value['biweeklycustom2'];
									}
								}
							}
						}
						break;

					case 'monthlyday':
						$return[] = $rex_date;
						$this_week = $t->getWeekOfMonth();
						$t->modify( '+4 weeks' );
						while( $t->getWeekOfMonth() != $this_week ){
							$t->modify( '+1 week' );
						}
						break;
					case 'monthlydayend':
						$return[] = $rex_date;
						$this_week = $t->getWeekOfMonthFromEnd();
						$t->modify( '+4 weeks' );
						while( $t->getWeekOfMonthFromEnd() != $this_week ){
							$t->modify( '+1 week' );
						}
						break;
					case 'monthlydate':
						$return[] = $rex_date;
						$t->modify( '+1 month' );
						break;
					case 'inout':
						$return[] = $rex_date;
						$in_out_in = $value['inoutin'];
						$in_out_out = $value['inoutout'];
						if( ! isset($in_out_count) )
							$in_out_count = 1;
						if( $in_out_count < $in_out_in ){
							$t->modify( '+1 day' );
							$in_out_count++;
						}
						else {
							$in_out_count = 1;
							$t->modify( '+' . ($in_out_out+1) . ' day' );
						}
						break;
				}
				$rex_date = $t->formatDate_Db();
			}
		}
		return $return;
	}

	public function unserialize( $value, $no_dates = FALSE )
	{
		$return = $this->_unserialize( $value, $no_dates );
		return $return;
	}

	private function _unserialize( $value, $no_dates = FALSE )
	{
		$return = array();
		if( (! $no_dates) && (strpos($value, '_') === FALSE) ){
			$return['recurring'] = 'single';
			if( ! $no_dates )
				$return['datesingle'] = $value;
		}
		elseif( $no_dates && (! strlen($value)) ){
			$return['recurring'] = 'single';
		}
		else {
			$value = explode('_', $value);

			$return['recurring'] = 'recurring';
			if( ! $no_dates ){
				$return['datestart'] = array_shift($value);
				$return['dateend'] = array_shift($value);
			}

			$repeat = array_shift($value);
			$return['repeat'] = $repeat;

			switch( $repeat ){
				case 'weeklycustom':
					$return['weeklycustom'] = $value;
					break;
				case 'biweeklycustom':
					$value1 = array();
					$value2 = array();
					$nowto = 1;
					foreach( $value as $v ){
						if( ! strlen($v) ){
							$nowto = 2;
							continue;
						}
						if( $nowto == 1 ){
							$value1[] = $v;
						}
						else {
							$value2[] = $v;
						}
					}

					$return['biweeklycustom1'] = $value1;
					$return['biweeklycustom2'] = $value2;
					break;
				case 'inout':
					$return['inoutin'] = array_shift( $value );
					$return['inoutout'] = array_shift( $value );
					break;
			}
		}
		return $return;
	}

	private function _serialize( $value, $no_dates = FALSE )
	{
		$return = array();
		$enabled = $this->enabled();

		if( ! is_array($value) ){
			$return = $value;
		}
		else {
			if( ! in_array('recurring', $enabled) ){
				$recurring = 'single';
			}
			else {
				$recurring = isset($value['recurring']) ? $value['recurring'] : 'single';
			}

			if( $recurring == 'single' ){
				if( $no_dates ){
					$return = '';
				}
				else {
					$return = isset($value['datesingle']) ? $value['datesingle'] : '';
				}
			}
			else {
				if( ! $no_dates ){
					$return[] = $value['datestart'];
					$return[] = $value['dateend'];
				}

				$repeat = $value['repeat'];
				$return[] = $repeat;

				switch( $repeat ){
					case 'weeklycustom':
						$return[] = join('_', $value['weeklycustom']);
						break;
					case 'biweeklycustom':
						$return[] = join('_', $value['biweeklycustom1']);
						$return[] = '';
						$return[] = join('_', $value['biweeklycustom2']);
						break;
					case 'inout':
						$return[] = $value['inoutin'];
						$return[] = $value['inoutout'];
						break;
				}
				$return = join('_', $return);
			}
		}
		return $return;
	}

	function value( $need_array = FALSE, $no_dates = FALSE )
	{
		$return = parent::value();

		if( $no_dates ){
			if( ! is_array($return) ){
				$return = $this->_unserialize($return);
				unset( $return['datestart'] );
				unset( $return['dateend'] );
				unset( $return['datesingle'] );
				$return = $this->_serialize($return, $no_dates);
			}
		}

		if( $need_array && (! is_array($return)) ){
			$return = $this->_unserialize($return);
		}
		return $return;
	}

	function render()
	{
		$enabled = $this->enabled();

		$value = $this->value(TRUE);

		$t = HC_Lib::time();
		if( isset($value['datestart']) ){
			$t->setDateDb( $value['datestart'] );
		}
		elseif( isset($value['datesingle']) ){
			$t->setDateDb( $value['datesingle'] );
		}

	/* single date part */
		$wrap_single = HC_Html_Factory::widget('list')
			->add_children_style('margin', 'b1')
			;
		$wrap_single->add_child( $this->fields['datesingle'] );

	/* recurring part */
		$wrap = HC_Html_Factory::widget('list')
			->add_attr('class', 'hcj-radio-info-container')
			->add_children_style('margin', 'b1')
			;

	/* DATES */
		$item_dates = HC_Html_Factory::widget('list')
			->add_children_style('inline')
			->add_children_style('margin', 'r1')
			->add_style('nowrap')
			;

		$item_dates = HC_Html_Factory::widget('grid')
			;

		$item_dates
			->add_child(
				$this->fields['datestart']
				, 5
				)
			->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'hc-form-control-static')
					->add_style('text-align', 'center')
					->add_style('display', 'block')
					->add_child('-')
				, 2
				)
			->add_child(
				$this->fields['dateend']
				, 5
				)
			;

		$wrap->add_child( $item_dates );

	/* RECURRING OPTIONS */
		$repeat = clone $this->fields['repeat'];

		$repeat->add_option( 'daily', 
			HCM::__('Daily')
			);
		$repeat->add_option( 'weekday',
			HCM::__('Every Weekday') . ' (' . $t->formatWeekdayShort(1) .  ' - ' . $t->formatWeekdayShort(5) . ')'
			);
		$repeat->add_option( 'weekly',
			HCM::__('Weekly') . ' (' . $t->formatWeekdayShort() . ')'
			);

		$wkds = array( 0, 1, 2, 3, 4, 5, 6 );
		$wkds = $t->sortWeekdays( $wkds );

	/* weekly custom */
		$weekly_custom = clone $this->fields['weeklycustom'];
		$this_weekday = $t->getWeekday();
		if( ! $weekly_custom->value() ){
			$weekly_custom->set_value( array($this_weekday) );
		}
		reset( $wkds );
		foreach( $wkds as $wkd ){
			$weekly_custom->add_option($wkd, $t->formatWeekdayShort($wkd));
		}
		// $weekly_custom->set_readonly($this_weekday);
		$repeat->add_option( 'weeklycustom',
			HCM::__('Weekly') . ' (' . HCM::__('Selected Days') . ')',
			$weekly_custom
			);

	/* biweekly custom */
		$this_weekday = $t->getWeekday();
		$biweekly_custom1 = clone $this->fields['biweeklycustom1'];
		if( ! $biweekly_custom1->value() ){
			$biweekly_custom1->set_value( array($this_weekday) );
		}
		$biweekly_custom2 = clone $this->fields['biweeklycustom2'];
		if( ! $biweekly_custom2->value() ){
			$biweekly_custom2->set_value( array($this_weekday) );
		}
		reset( $wkds );
		foreach( $wkds as $wkd ){
			$biweekly_custom1->add_option($wkd, $t->formatWeekdayShort($wkd));
			$biweekly_custom2->add_option($wkd, $t->formatWeekdayShort($wkd));
		}
		// $weekly_custom->set_readonly($this_weekday);
		$repeat->add_option( 'biweeklycustom',
			HCM::__('Biweekly') . ' (' . HCM::__('Selected Days') . ')',
			array( $biweekly_custom1, '<br>', $biweekly_custom2 )
			);

	/* in/out */
		if( ! $this->fields['inoutin']->value() ){
			$this->fields['inoutin']->set_value(2);
		}
		if( ! $this->fields['inoutout']->value() ){
			$this->fields['inoutout']->set_value(2);
		}

		$repeat->add_option( 'inout',
/* translators: this is a rotating shift cycle, for example work for 2 days then 2 days off. */
			join( ' / ',
				array(
					sprintf( HCM::_n('%s Day On', '%s Days On', 100), 'X'),
					sprintf( HCM::_n('%s Day Off', '%s Days Off', 100), 'Y')
					)
				),

			HC_Html_Factory::widget('list')
				->add_children_style('inline')
				->add_children_style('margin', 'b1', 'r1')
				->add_child( $this->fields['inoutin']->add_attr('size', 2) )
				->add_child( '/' )
				->add_child( $this->fields['inoutout']->add_attr('size', 2) )
			);

		$repeat->add_option( 'monthlyday',
			HCM::__('Monthly') . ' (' .  join(', ', array($t->formatWeekdayShort(), $t->formatWeekOfMonth())) . ')'
			);
		$repeat->add_option( 'monthlydayend',
			HCM::__('Monthly') . ' (' .  join(', ', array($t->formatWeekdayShort(), $t->formatWeekOfMonthFromEnd())) . ')'
			);
		$repeat->add_option( 'monthlydate',
			HCM::__('Monthly') . ' (' . HCM::__('Day') . ': ' . $t->getDay() . ')'
			);

		$wrap->add_child( $repeat );

	/* build output */
		// $recurring_part = $wrap->render();
		// $recurring_part = $this->decorate( $recurring_part );

		// $single_part = $wrap_single->render();
		// $single_part = $this->decorate( $wrap_single );

		$return = HC_Html_Factory::widget('container');

		if( count($enabled) > 1 ){
			$tabs = HC_Html_Factory::widget('tabs');
			$tabs_id = 'nts' . hc_random();
			$tabs->set_id( $tabs_id );

			$tabs->add_tab( 'single', HCM::__('Single Day'), $wrap_single );
			$tabs->add_tab( 'recurring', HCM::__('Multiple Days'), $wrap );

			$value_recurring = $value['recurring'];
			$tabs->set_active( $value_recurring );

			$return->add_child( $this->fields['recurring'] );
			$return->add_child( $tabs );

			$name_recurring = $this->fields['recurring']->name();
		}
		else {
			if( in_array('single', $enabled) ){
				$return->add_child( $wrap_single );
			}
			if( in_array('recurring', $enabled) ){
				$return->add_child( $wrap );
			}
		}

		$return = $return->render();

		// return $return;
		return $this->decorate( $return );
	}
}