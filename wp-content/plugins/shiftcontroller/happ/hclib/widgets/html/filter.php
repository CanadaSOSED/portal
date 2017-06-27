<?php
include_once( dirname(__FILE__) . '/collapse.php' );

class HC_Html_Widget_Filter extends HC_Html_Widget_Collapse
{
	private $options = array();
	private $option_attr = array();
	private $selected = array();
	private $param_name = 'param';
	private $link = NULL;
	private $allow_multiple = TRUE;
	private $require_one = FALSE;
	private $fixed = FALSE;
	private $readonly = FALSE;

	private $inside = FALSE;

	function set_inside( $inside = TRUE )
	{
		$this->inside = $inside;
		return $this;
	}
	function inside()
	{
		return $this->inside;
	}

	function set_readonly( $readonly )
	{
		$this->readonly = $readonly;
		return $readonly;
	}
	function readonly()
	{
		return $this->readonly;
	}

	function set_fixed( $fixed )
	{
		$this->fixed = $fixed;
		return $this;
	}
	function fixed()
	{
		return $this->fixed;
	}

	function set_options( $options )
	{
		foreach( $options as $k => $v ){
			$this->set_option( $k, $v );
		}
		return $this;
	}
	function options()
	{
		return $this->options;
	}

	function set_option( $key, $value, $attr = array() )
	{
		$this->options[ $key ] = $value;
		$this->option_attr[ $key ] = $attr;
		return $this;
	}
	function option( $key )
	{
		$return = NULL;
		if( isset($this->options[$key]) ){
			$return = $this->options[$key];
		}
		return $return;
	}

	function set_allow_multiple( $allow_multiple )
	{
		$this->allow_multiple = $allow_multiple;
		return $this;
	}
	function allow_multiple()
	{
		return $this->allow_multiple;
	}

	function set_require_one( $require_one )
	{
		$this->require_one = $require_one;
		return $this;
	}
	function require_one()
	{
		return $this->require_one;
	}

	function set_param_name( $param_name )
	{
		$this->param_name = $param_name;
		return $this;
	}
	function param_name()
	{
		return $this->param_name;
	}

	function set_link( $link )
	{
		$this->link = $link;
		return $this;
	}
	function link()
	{
		return $this->link;
	}

	function set_selected( $selected )
	{
		if( ! is_array($selected) ){
			if( $selected !== NULL )
				$selected = array( $selected );
			else
				$selected = array();
		}
		$this->selected = $selected;
		return $this;
	}
	function selected()
	{
		return $this->selected;
	}

	function render_selected()
	{
		if( ! $link = $this->link() ){
			return 'HC_Html_Widget_Filter: link is not set!';
		}

		$require_one = $this->require_one();
		$selected = $this->selected();
		$fixed = $this->fixed();

		$return = HC_Html_Factory::widget('container');

		foreach( $selected as $sel ){
			if( $require_one ){
				$option_wrap = HC_Html_Factory::widget('titled', 'a')
					->add_attr('href', '#')
					->add_attr('class', 'hcj-collapse-next')
					;
			}
			else {
				$option_wrap = HC_Html_Factory::widget('titled','div');
			}

			$option_wrap
				->add_style('btn')
				->add_style('nowrap')
				->add_style('border')
				->add_style('rounded')
				->add_style('padding', 'x2', 'y1')
				->add_attr('style', 'max-width: 10em;')
				;

			$option_label = $this->option($sel);
			$option_wrap->add_child( $option_label );
			if( $require_one ){
				$option_wrap
					->add_child(
						HC_Html::icon('caret-down')
						)
					;
			}

			if( ! $require_one ){
				if( ! $fixed ){
					$option_wrap->prepend_child(
						HC_Html_Factory::element('a')
							->add_attr('href', $link->url(array($this->param_name() . '*' => $sel)))
							->add_child(
								HC_Html::icon('times')
									->add_style('color', 'maroon')
								)
							->add_style('closer')
							->add_style('font-size', -1)
						);
				}
			}

			$attr = isset($this->option_attr[$sel]) ? $this->option_attr[$sel] : array();
			foreach( $attr as $attr_k => $attr_v ){
				$option_wrap->add_attr($attr_k, $attr_v);
			}

			$option_wrap->add_style( 'margin', 'r1', 't1' );
			$return->add_child( $sel, $option_wrap );
			
		}
		return $return;
	}

	function remain_options()
	{
		$allow_multiple = $this->allow_multiple();
		$param_name = $this->param_name();

		$return = array();
		if( (! $this->selected()) OR $allow_multiple ){
			foreach( $this->options() as $id => $label ){
				if( ! in_array($id, $this->selected()) ){
					$return[$id] = $label;
				}
			}
		}
		return $return;
	}

	function render_options()
	{
		if( ! $link = $this->link() ){
			return 'HC_Html_Widget_Filter: link is not set!';
		}
		$param_name = $this->param_name();

		$content = HC_Html_Factory::widget('list')
			->add_children_style('display', 'inline-block')
			// ->add_children_style('inline')
			->add_children_style('margin', 'r1', 'b1')
			;

	/* remaining possible options */	
		$remain_options = $this->remain_options();

		foreach( $remain_options as $id => $label ){
			$option_wrap = HC_Html_Factory::widget('titled', 'a')
				->add_style('btn')
				->add_style('nowrap')
				->add_style('border')
				->add_style('rounded')
				->add_style('padding', 'x2', 'y1')
				->add_attr('style', 'max-width: 10em;')
				;

			if( $param_name === NULL ){
				$href = $link->url($id, array());
			}
			else {
				$href = $link->url(array($param_name . '+' => $id));
			}

			$option_wrap->add_attr('href', $href);
			$option_wrap->add_child( $label );

			$attr = isset($this->option_attr[$id]) ? $this->option_attr[$id] : array();
			foreach( $attr as $attr_k => $attr_v ){
				$option_wrap->add_attr($attr_k, $attr_v);
			}

			$content->add_child( $id, $option_wrap );
		}
		return $content;
	}

	function render()
	{
		if( ! $link = $this->link() ){
			return 'HC_Html_Widget_Filter: link is not set!';
		}

		// $this->set_panel('default');

		$inside = $this->inside();
		$add_title = $this->title();
		$readonly = $this->readonly();
		$allow_multiple = $this->allow_multiple();
		$require_one = $this->require_one();
		$fixed = $this->fixed();
		$param_name = $this->param_name();

		$remain_options = $this->remain_options();

		$title = $add_title;
		$selected = $this->selected();
		if( $selected ){
			$title = array();
			foreach( $selected as $sel ){
				$title[] = $this->option($sel);
			}
			$title = join( ' ', $title );
		}

		if( $readonly && (! $this->selected()) ){
			return NULL;
		}

		$content = $this->render_options();
		$this->set_content( $content );

		$panel = $this->panel();

		$out = HC_Html_Factory::widget('list')
			->add_attr('class', 'hcj-collapse-panel')
			;

	/* build trigger */
		$title = HC_Html_Factory::widget('list')
			->add_children_style('inline')
			->add_children_style('margin', 'r1', 'b1')
			;

	/* current selection */
		$selected = $this->selected();
		if( (! $inside) && $selected ){
			foreach( $selected as $sel ){
				if( $require_one ){
					$option_wrap = HC_Html_Factory::widget('titled', 'a')
						->add_attr('href', '#')
						->add_attr('class', 'hcj-collapse-next')
						;
				}
				else {
					$option_wrap = HC_Html_Factory::widget('titled','div');
				}

				$option_wrap
					->add_style('btn')
					->add_style('nowrap')
					->add_style('border')
					->add_style('rounded')
					->add_style('padding', 'x2', 'y1')
					->add_attr('style', 'max-width: 10em;')
					;

				$option_label = $this->option($sel);
				$option_wrap->add_child( $option_label );
				if( $require_one ){
					$option_wrap
						->add_child(
							HC_Html::icon('caret-down')
							)
						;
				}

				if( ! $require_one ){
					if( ! $fixed ){
						$option_wrap->prepend_child(
							HC_Html_Factory::element('a')
								->add_attr('href', $link->url(array($this->param_name() . '-' => $sel)))
								->add_child(
									HC_Html::icon('times')
										->add_style('color', 'maroon')
									)
								->add_style('closer')
							);
					}
				}

				$attr = isset($this->option_attr[$sel]) ? $this->option_attr[$sel] : array();
				foreach( $attr as $attr_k => $attr_v ){
					$option_wrap->add_attr($attr_k, $attr_v);
				}

				$title->add_child( $sel, $option_wrap );
			}
		}

		if( $remain_options && (! $require_one) ){
			$trigger = HC_Html_Factory::element('a')
				// ->add_child( HC_Html::icon('plus') . HCM::_x('Filter', 'noun') . ': ' . $add_title )
				->add_child( $add_title )
				->add_attr('href', '#')
				->add_attr('class', 'hcj-collapse-next')

				->add_style('hidden', 'print')
				->add_style('btn')
				->add_style('padding', 'x2', 'y1')
				;
			$title->add_child( $trigger );
		}

		if( $panel ){
			$title
				->add_style('padding')
				;
		}

		$out->add_child( 'header', $title );
		$out->add_child_attr('content', 'class', 'hcj-collapse');

		if( $panel ){
			$out
				->add_child_attr('content', 'class', 'hcj-panel-collapse')
				->add_child_style('content', 'border', 'top')
				;
		}
		if( $this->default_in() ){
			$out->add_child_attr('content', 'class', 'hcj-open');
		}

		if( $panel ){
			$out
				->add_style('border')
				->add_style('rounded')
				;
		}

		if( $panel ){
			// echo $this->content();
			// exit;
			$out->add_child('content', 
				HC_Html_Factory::element('div')
					->add_child( $this->content() )
					->add_style('padding')
				);
		}
		else {
			$out->add_child('content', $this->content());
		}

		if( ! $this->selected() ){
			$out->add_style('hidden', 'print');
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		return $out->render();
	}
}