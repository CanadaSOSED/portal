<?php
class SFT_Html_Widget_Shift_View extends HC_Html_Element
{
	private $shift = NULL;
	private $iknow = array();
	private $wide = TRUE;
	private $nolink = FALSE;
	private $new_window = FALSE;

	function init( $shift = NULL )
	{
		$this->set_shift( $shift );
	}

	function set_shift( $shift )
	{
		$this->shift = $shift;
		return $this;
	}
	function shift()
	{
		return $this->shift;
	}

	function set_nolink( $nolink = TRUE )
	{
		$this->nolink = $nolink;
		return $this;
	}
	function nolink()
	{
		return $this->nolink;
	}

	function set_new_window( $new_window = TRUE )
	{
		$this->new_window = $new_window;
		return $this;
	}
	function new_window()
	{
		return $this->new_window;
	}

	function set_wide( $wide = TRUE )
	{
		$this->wide = $wide;
		return $this;
	}
	function wide()
	{
		return $this->wide;
	}

	function set_iknow( $iknow )
	{
		$this->iknow = $iknow;
		return $this;
	}
	function iknow()
	{
		return $this->iknow;
	}

	function render()
	{
		$sh = $this->shift();
		$t = HC_Lib::time();

		$titles = array();

		$iknow = $this->iknow();
		$wide = $this->wide();

		$use_color = FALSE;
		$use_color = TRUE;
		if( $wide && ($wide === 'mini') ){
			$use_color = TRUE;
		}

		if( in_array($sh->type, array($sh->_const("TYPE_TIMEOFF"))) ){
			$display = array( 'date', 'time', 'user', 'location' );
		}
		else {
			if( (! $wide) OR ($wide === 'mini') ){
				$display = array( 'date', 'time', 'location', 'user' );
			}
			elseif( $wide ){
				$display = array( 'date', 'time', 'user', 'location' );
			}
		}

		foreach( $iknow as $ik ){
			$display = HC_Lib::remove_from_array($display, $ik);
		}

		foreach( $display as $ds ){
			$title_view = '';

			switch( $ds ){
				case 'date':
					$title_view = $sh->present_date(HC_PRESENTER::VIEW_RAW);
					break;

				case 'time':
					$title_view = $sh->present_time();
					break;

				case 'location':
					if( in_array($sh->type, array($sh->_const("TYPE_TIMEOFF"))) ){
						$title_view = '';
						// $title_view = HCM::__('Timeoff');
						// $title_view = $sh->present_location();
					}
					else {
						$title_view = $sh->present_location();
					}
					break;

				case 'user':
					if( ($sh->type == $sh->_const('TYPE_TIMEOFF')) && (! in_array('time', $display)) ){
						$title_view = $sh->present_type(HC_PRESENTER::VIEW_HTML_ICON) . $sh->present_user(HC_PRESENTER::VIEW_RAW);
					}
					else {
						if( $sh->user_id ){
							$title_view = $sh->present_user();
						}
						else {
							if( count($display) == 1 ){
								$title_view = HC_Html_Factory::element('span')
									->add_child('&nbsp;')
									->add_style('display', 'block')
									->add_attr('title', HCM::__('Open Shift'))
									;
							}
						}
					}
					break;
			}

			if( $title_view ){
				$titles[ $ds ] = $title_view;
			}
		}

		$wrap = HC_Html_Factory::element('div')
			->add_style('padding', 1)
			->add_style('nowrap')
			;

		if( ! $sh->user_id ){
			$wrap
				->add_style('border')
				->add_style('border-color', 'orange')
				;
		}

//		echo $color;

	/* ID */
		if( in_array('id', $iknow) ){
			$wrap->add_child($sh->present_id());
		}

		$final_display = HC_Html_Factory::widget('list')
			// ->add_children_style('margin', 'b1')
			// ->add_children_style('border')
			// ->add_children_style('border-color', 'blue')
			;

	/* final wrap */
		$final_wrap = HC_Html_Factory::element('div')
			->add_style('border')
			->add_style('rounded')
			->add_style('padding', 0)
			->add_style('nowrap')
			;

		$extensions = HC_App::extensions();

	/* QUICK ICONS */
		$quick_icons = array();
		if( ! $sh->user_id ){
			$quick_icons['open'] = HC_Html::icon(HC_App::icon_for('user'))
				->add_attr('title', HCM::__('Open Shift'))
				->add_style('bg-color', 'orange')
				->add_style('color', 'white')
				->add_style('rounded')
				;
		}

		$more_quick_icons = $extensions
			->set_skip($iknow)
			->run(
				'shifts/quickicons',
				$sh,
				$final_wrap
			);

		$quick_icons = array_merge( $quick_icons, $more_quick_icons );

		if( $quick_icons ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_children_style('display', 'inline-block')
				->add_children_style('margin', 'l1')
				;
			$added = 0;
			foreach( $quick_icons as $mck => $mc ){
				if( $mck && in_array($mck, $iknow) ){
					continue;
				}

				$more_wrap->add_child($mc);
				$added++;
			}

			if( $added ){
				$final_wrap
					->add_attr('style', 'position: relative;')
					;
				$more_wrap
					->add_attr('style', 'position: absolute; right: .125em; top: .125em; z-index: 100;')
					;
				$final_wrap->add_child($more_wrap);
			}
		}

	/* build link title */
		$nolink = $this->nolink();
		$new_window = $this->new_window();

		$a_link = HC_Html_Factory::widget('titled', 'a');
		$link_to = 'shifts/zoom/index/id/' . $sh->id;
		$a_link->add_attr('href', HC_Lib::link($link_to)->url());
		if( ! $new_window ){
			$a_link->add_attr('class', 'hcj-flatmodal-loader');
		}
		else {
			$a_link->add_attr('target', '_blank');
			$a_link->add_attr('class', 'hcj-parent-loader');
		}

		$a_link
			->add_style('btn')
			->add_style('padding', 0)
			->add_style('margin', 0)
			;

		// $a_title = HC_Html_Factory::widget('titled', 'span')
		$a_title = HC_Html_Factory::widget('titled', 'div')
			->add_style('nowrap')
			;
		// $a_title->add_attr('style', 'border: red 1px solid;');
		// $a_title->add_attr('style', 'border-color: ' . $sh->location->present_color());

		if( count($display) > 1 ){
			if( $wide ){
				$titles2 = HC_Html_Factory::widget('grid');
				$grid_width = array(
					2	=> 6,
					3	=> 4,
					4	=> 3,
					5	=> 2,
					6	=> 2
					);
				$grid_width = isset($grid_width[count($display)]) ? $grid_width[count($display)] : 2;

				$ti = -1;
				$ttis = array_keys($titles);
				foreach( $titles as $tti => $ttl ){
					$ti++;
					if( $ti >= count($titles) ){
						continue;
					}

					// next title is empty?
					if( ($ti < count($titles)-1) && (! strlen($titles[$ttis[$ti+1]])) ){
						$ti++;
						$grid_width += $grid_width;
					}

					$final_ttl = $ttl;
					if( ! $nolink ){
						$final_ttl = clone $a_link;
						$final_ttl
							->add_child( $ttl )
							->add_attr('class', 'sfc-shift-view-' . $tti)
							;
					}
					$titles2->add_child( 
						$final_ttl,
						$grid_width
						);
				}
			}
			else {
				$titles2 = HC_Html_Factory::widget('list')
					// ->add_children_style('margin', 'b1')
					// ->add_children_style('border')
					// ->add_children_style('border-color', 'blue')
					;
				$this_index = 0;

				foreach( $titles as $tti => $ttl ){
					if( ! strlen($ttl) ){
						continue;
					}

					$final_ttl = $ttl;
					if( ! $nolink ){
						$final_ttl = clone $a_link;
						$final_ttl->add_child( $ttl );
					}

					$final_ttl
						->add_style('display', 'block')
						->add_attr('class', 'sfc-shift-view-' . $tti)
						;

					$titles2->add_child( $this_index, $final_ttl );
					$titles2->add_child_style( $this_index, 'nowrap');
					$this_index++;
				}
			}
			$a_title->add_attr('title', join(' ', $titles));
			$a_title->add_child( $titles2 );
		}
		else {
			$final_ttl = $titles;
			if( ! $nolink ){
				$final_ttl = clone $a_link;
				$final_ttl->add_child( $titles );
			}
			$final_ttl
				->add_attr('title', join(' ', $titles))
				->add_style('display', 'block')
				;

			$a_title->add_child( $final_ttl );
		}

	/* main view */
		$final_display->add_child($a_title);

	/* EXTENSIONS */
		$more_content = $extensions
			->set_skip($iknow)
			->run(
				'shifts/quickview',
				$sh,
				// $wrap
				$final_wrap
			);

		if( $more_content ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_children_style('font-size', -1)
				;
			$added = 0;
			foreach($more_content as $mck => $mc ){
				if( $mck && in_array($mck, $iknow) ){
					continue;
				}

				$more_wrap->add_child($mc);
				$added++;
			}
			if( $added ){
				// $wrap->add_child($more_wrap);
				$final_display->add_child($more_wrap);
			}
		}

	/* THIS CHILDREN */
		$children = $this->children();
		foreach( $children as $child ){
			// $wrap->add_child($child);
			$final_display->add_child($child);
		}

	/* add summary for mini displays in month calendars */
		if( $wide === 'mini' ){
			$a_title = HC_Html_Factory::widget('titled', 'span')
				->add_style('nowrap')
				;
			if( ! $nolink ){
				$final_ttl = clone $a_link;
				$final_ttl
					->add_child('&nbsp;')
					->add_style('display', 'block')
					->add_attr('style', 'position: relative; z-index: 200;')
					;
				$final_ttl->add_attr('title', join(' ', $titles));
			}
			$a_title->add_child( $final_ttl );

			$final_display = HC_Html_Factory::widget('list')
				->add_child('summary', $a_title )
				->add_child_attr('summary', 'class', 'schecal-cell-summary' )
				->add_child('full', $final_display )
				->add_child_attr('full', 'class', 'schecal-cell-details' )
				;
		}
		$wrap->add_child( $final_display );


	/* background color depends on location */
		if( $use_color ){
			$color = $sh->location->present_color();
		}
		else {
			$type = $sh->type; 
			switch( $type ){
				case $sh->_const('TYPE_TIMEOFF'):
					$final_wrap->add_style('bg-color', 'silver');
					$color = '#ddd';
					break;
				default:
					$final_wrap->add_style('border-color', 'olive');
					$color = '#dff0d8';
					break;
			}
		}

		// if( ! $sh->user_id ){
			// $color = '';
		// }

		if( $color ){
			if( $sh->status == $sh->_const('STATUS_DRAFT') ){
				$color1 = HC_Lib::adjust_color_brightness( $color, 0 );
				$color2 = HC_Lib::adjust_color_brightness( $color, 20 );

				// $color1 = '#fff';
				// $color2 = '#eee';

				$final_wrap->add_attr('style',
					"background: repeating-linear-gradient(
						-45deg,
						$color1,
						$color1 6px,
						$color2 6px,
						$color2 12px
						);
					"
					);
			}
			else { 
				$final_wrap->add_attr('style', 'background-color: ' . $color . ';');
			}
			$wrap->add_style('bg-lighten', 2);
		}

		$wrap->add_attr( 'class', 'hcj-common-link-parent' );
		$wrap->add_attr( 'class', 'hcj-target' );
		// $final_wrap->add_attr( 'class', 'hcj-common-link-parent' );
		// $final_wrap->add_attr( 'class', 'hcj-target' );
		$final_wrap->add_child( $wrap );

/*
$final_wrap = HC_Html_Factory::widget('table')
	->set_cell( 0, 0, 
		'<input type="checkbox" style="">'
		)
	->add_cell_attr(0, 0, array('style' => 'width: 1em;'))
	->set_cell( 0, 1, 
		$final_wrap
		)
	;
*/
		return $final_wrap->render();
	}
}