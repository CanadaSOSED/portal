<?php
/* default decorator making use of hc- utility css classes */
class HC_Html_Element_Styler
{
	function table( $el, $more = array() )
	{
		$el->add_attr('class', 'hc-table');

		if( $more && (! is_array($more)) ){
			$more = array($more);
		}
		foreach( $more as $mr ){
			switch( $mr ){
				case 'border':
					$el->add_attr('class', 'hc-table-light');
					break;
			}
		}
		return $el;
	}

	function submit_bar( $el )
	{
		$el
//			->add_style('padding', 'y2')
			->add_style('margin', 't2')
			// ->add_style('border', 'top')
			->add_children_style('inline')
			->add_children_style('margin', 'r2', 'b1')
			;
		return $el;
	}
	function btn( $el )
	{
		$el->add_attr('class', 'hc-btn');
		return $el;
	}

	function btn_primary( $el )
	{
		$el
			->add_style('rounded')
			;
		return $el;
	}

	function btn_submit( $el, $padding = 2 )
	{
		$el
			->add_style('padding', $padding)
			->add_style('rounded')
			->add_style('border')
//			->add_style('border-color', 'current')
			;
		return $el;
	}

	function badge( $el )
	{
		$el
			->add_style('font-size', -1)
			->add_style('padding', 'x2', 'y1')
			->add_style('display', 'inline-block')
			->add_style('rounded')
			->add_style('nowrap')
			;
		return $el;
	}

	function label( $el )
	{
		$el
			->add_style('font-size', -1)
			->add_style('padding', 'x2', 'y1')
			->add_style('rounded')
			->add_style('nowrap')
			;
		return $el;
	}

	function inline( $el )
	{
		$el->add_attr('class', 'hc-inline');
		return $el;
	}

	function grid( $el, $gutter = 0 )
	{
		$class = array();
		$class[] = 'hc-clearfix';
		if( $gutter ){
			$class[] = 'hc-mxn' . $gutter;
		}
		$el->add_attr('class', $class);
		return $el;
	}

	function col( $el, $scale, $width, $offset = 0, $gutter = 0, $right = 0 )
	{
		$class = array();

		$manual = FALSE;
		$check_manual = array('%', 'em', 'px', 'rem');
		/* check if width contains %% then we need to set it manually */
		foreach( $check_manual as $check ){
			if( substr($width, -strlen($check)) == $check ){
				$manual = TRUE;
				break;
			}
		}

		switch( $scale ){
			case 'xs':
				$class = array('hc-col');
				if( ! $manual ){
					$class[] = 'hc-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-col-right';
				}
				break;
			case 'sm':
				$class = array('hc-sm-col');
				if( ! $manual ){
					$class[] = 'hc-sm-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-sm-col-right';
				}
				break;
			case 'md':
				$class = array('hc-md-col');
				if( ! $manual ){
					$class[] = 'hc-md-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-md-col-right';
				}
				break;
			case 'lg':
				$class = array('hc-lg-col');
				if( ! $manual ){
					$class[] = 'hc-ld-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-ld-col-right';
				}
				break;
		}

		if( $manual ){
			$el->add_attr('style', 'width: ' . $width . ';');
		}
		if( $offset ){
			$el->add_attr('style', 'margin-left: ' . $offset . ';');
		}

		if( $gutter ){
			$class[] = 'hc-px' . $gutter;
		}
		$el->add_attr('class', $class);
		return $el;
	}

	function error( $el )
	{
		$el
			->add_style('color', 'red')
			;
		return $el;
	}

	function form_control( $el )
	{
		$el->add_attr('class', 'hc-field');
		return $el;
	}

	function form_error( $el )
	{
		$el->add_children_attr('class', 'hc-is-error');
		return $el;
	}

	function border( $el, $where = '' )
	{
		if( $where === 0 ){
			$where = 'none';
		}
		$class = $where ? 'hc-border-' . $where : 'hc-border';
		$el->add_attr('class', $class);
		return $el;
	}

	function rounded( $el )
	{
		$el->add_attr('class', 'hc-rounded');
		return $el;
	}

	function box( $el, $padding = 2, $border = 1 )
	{
		$el
			->add_style('padding', $padding)
			->add_style('rounded')
			->add_style('display', 'block')
			;
		if( $border ){
			$el->add_style('border');
		}
		return $el;
	}

	function display( $el, $how )
	{
		$class = 'hc-' . $how;
		$el->add_attr('class', $class);
		return $el;
	}

/* xs, sm, md, lg */
	function visible( $el, $when )
	{
		$class = 'hc-' . $when . '-' . 'show';
		$el->add_attr('class', $class);
		return $el;
	}
	function hidden( $el, $when )
	{
		$class = 'hc-' . $when . '-' . 'hide';
		$el->add_attr('class', $class);
		return $el;
	}

/* from 1 to 3 */
	function mute( $el, $change = 2 )
	{
		$class = 'hc-muted-' . $change;
		$el->add_attr('class', $class);
		return $el;
	}

	function font_style( $el, $style = 'italic' ) // italic, bold, caps, regular
	{
		$class = 'hc-' . $style;
		$el->add_attr('class', $class);
		return $el;
	}

/* from -2 to +2 */
	function font_size( $el, $change_size = 0 )
	{
		$size = 3 + $change_size;

		$class = array();
		switch( $size ){
			case 1:
				$class = array('hc-fs1');
				break;
			case 2:
				$class = array('hc-fs2');
				break;
			case 3:
				$class = array('hc-fs3');
				break;
			case 4:
				$class = array('hc-fs4');
				break;
			case 5:
				$class = array('hc-fs5');
				break;
		}
		$el->add_attr('class', $class);
		return $el;
	}

/* from 0 to 3, or x1, y2 etc */
	function padding( $el, $scale = 1 )
	{
		$scale = func_get_args();
		array_shift( $scale );
		if( ! $scale ){
			$scale = array(1);
		}

		foreach( $scale as $sc ){
			$el->add_attr('class', 'hc-p' . $sc);
		}

		return $el;
	}
	function margin( $el, $scale = 1 )
	{

		$scale = func_get_args();
		array_shift( $scale );
		if( ! $scale ){
			$scale = array(1);
		}

		foreach( $scale as $sc ){
			$el->add_attr('class', 'hc-m' . $sc);
		}
		return $el;
	}

	function closer( $el )
	{
		$el->add_attr('class', 'hc-close');
		return $el;
	}

	function left( $el )
	{
		$el->add_attr('class', 'hc-left');
		return $el;
	}
	function right( $el )
	{
		$el->add_attr('class', 'hc-right');
		return $el;
	}

	function text_align( $el, $how )
	{
		$class = 'hc-align-' . $how;
		$el->add_attr('class', $class);
		return $el;
	}

	function nowrap( $el )
	{
		$class = 'hc-nowrap';
		$el->add_attr('class', $class);
		return $el;
	}

	function color( $el, $color )
	{
		$class = 'hc-' . $color;
		$el->add_attr('class', $class);
		return $el;
	}
	function bg_color( $el, $color )
	{
		$class = 'hc-bg-' . $color;
		$el->add_attr('class', $class);
		return $el;
	}
	function border_color( $el, $color )
	{
		$class = 'hc-border-' . $color;
		$el->add_attr('class', $class);
		return $el;
	}
	/* up to 4 */
	function bg_lighten( $el, $scale = 1 )
	{
		$class = 'hc-bg-lighten-' . $scale;
		$el->add_attr('class', $class);
		return $el;
	}
	/* up to 4 */
	function bg_darken( $el, $scale = 1 )
	{
		$class = 'hc-bg-darken-' . $scale;
		$el->add_attr('class', $class);
		return $el;
	}
}
?>