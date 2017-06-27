<?php
class HC_Form_Input extends HC_Html_Element
{
	protected $type = 'text';
	protected $name = 'name';
	protected $id = '';
	protected $error = '';
	protected $value = NULL;
	protected $readonly = FALSE;
	protected $conf = array();

	function __construct( $name = '' )
	{
		parent::__construct();
		if( ! strlen($name) ){
			$name = 'nts_' . HC_Lib::generate_rand();
		}
		$this->set_name( $name );
	}

	function set_conf( $k, $v )
	{
		$this->conf[$k] = $v;
		return $this;
	}
	function conf($k)
	{
		$return = NULL;
		if( isset($this->conf[$k]) ){
			$return = $this->conf[$k];
		}
		return $return;
	}

	function set_readonly( $readonly = TRUE )
	{
		$this->readonly = $readonly;
		return $this;
	}
	function readonly()
	{
		return $this->readonly;
	}

	/* if fails should return the error message otherwise NULL */
	function _validate()
	{
		$return = NULL;
		return $return;
	}

	/* this will add error messages and help text if needed*/
	function decorate( $return )
	{
		if( $wrap = $this->wrap() ){
			foreach( $wrap as $wr ){
				$return = $wr->add_child($return)->render();
			}
		}

		$error = $this->error();
		if( $error ){
			$return = HC_Html_Factory::widget('container')
				->add_child( $return )
				;
			if( is_array($error) ){
				$error = join( ' ', $error );
			}
			$return->add_child(
				HC_Html_Factory::element('span')
					->add_child( $error )
					->add_style('inline')
					->add_style('margin')
					->add_style('error')
				);
			$return = $return->render();
		}

		return $return;
	}

	function set_type( $type )
	{
		$this->type = $type;
		return $this;
	}
	function type()
	{
		return $this->type;
	}

	function set_error( $error )
	{
		if( ! $this->error )
			$this->error = $error;
		return $this;
	}
	function error()
	{
		return $this->error;
	}

	function set_default( $value )
	{
		if( $this->value() === NULL ){
			$this->set_value($value);
		}
		return $this;
	}

	function set_value( $value )
	{
		$this->value = $value;
		if( $error = $this->_validate() ){
			$this->set_error( $error );
		}
		return $this;
	}
	function value()
	{
		return $this->value;
	}

	function set_name( $name )
	{
		$this->name = $name;
		if( ! strlen($this->id())){
			$id = 'nts_form_' . $name;
			$this->set_id( $id );
		}
		return $this;
	}
	function name()
	{
		return $this->name;
	}
	function set_id( $id )
	{
		$this->id = $id;
		return $this;
	}
	function id()
	{
		return $this->id;
	}
	
/* will be overwritten in child classes */
	function grab( $post )
	{
		$name = $this->name();
		$value = NULL;
		if( isset($post[$name]) ){
			$value = $post[$name];
		}
		$this->set_value( $value );
	}

	function to_array()
	{
		$return = array(
			'name'	=> $this->name(),
			'type'	=> $this->type(),
			'value'	=> $this->value(),
			'error'	=> $this->error(),
			);
		return $return;
	}
}

class HC_Form_Input_Textarea extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'textarea' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'id', $this->id() )
			->add_child( $this->value() )
			->add_style('form-control')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Radio extends HC_Form_Input
{
	protected $options = array();
	protected $more = array();
	protected $holder = NULL;
	protected $inline = FALSE;

	function add_option( $value, $label = NULL, $more = '' )
	{
		$this->options[$value] = $label;
		if( $more ){
			$this->more[$value] = $more;
		}
		return $this;
	}
	function options()
	{
		return $this->options;
	}
	function more()
	{
		return $this->more;
	}

	function set_inline( $inline = TRUE )
	{
		$this->inline = $inline;
		return $this;
	}
	function inline()
	{
		return $this->inline;
	}

	public function set_holder( $holder )
	{
		$this->holder = $holder;
		return $this;
	}
	public function holder()
	{
		return $this->holder;
	}

	function render()
	{
		$options = $this->options();
		$more = $this->more();
		$value = $this->value();
		$inline = $this->inline();

		$el = $this->holder();
		if( ! ($el && is_object($el) && method_exists($el, 'add_child')) ){
			$el = HC_Html_Factory::widget('list');
			if( $inline ){
				$el->add_children_style('inline');
				$el->add_children_style('margin', 'r1', 'b1');
			}
			else {
				$el->add_children_style('margin', 'b1');
			}
		}

		foreach( $options as $value => $label ){
			$wrap_el = HC_Html_Factory::element('label')
				->add_attr('style', 'padding-left: 0;')
				->add_style('nowrap')
				->add_children_style('display', 'inline-block')
				;

			if( count($options) > 1 ){
				$sub_el = HC_Html_Factory::element('input')
					->add_attr('type', 'radio')
					->add_attr('name', $this->name())
					->add_attr('id', $this->id())
					->add_attr('value', $value)
					;
				$sub_el->add_attr('class', 'hcj-radio-more-info');
				if( $value == $this->value() ){
					$sub_el->add_attr('checked', 'checked');
				}
			}
			else {
				$sub_el = HC_Html_Factory::element('input')
					->add_attr('type', 'hidden')
					->add_attr('name', $this->name())
					->add_attr('id', $this->id())
					->add_attr('value', $value)
					;
			}

			$attr = $this->attr();
			foreach( $attr as $k => $v ){
				$sub_el->add_attr($k, $v);
			}

			$wrap_el->add_child( $sub_el );
			if( $label !== NULL ){
				$wrap_el->add_child( $label );
			}

			if( isset($more[$value]) ){
				$this_more = HC_Html_Factory::element('div')
					->add_attr('class', 'hcj-radio-info')
					->add_child( $more[$value] )
					->add_style('margin', 'l3')
					;
				$wrap_el->add_child( $this_more );
			}

			if( $inline ){
				$wrap_el
					->add_style('padding', 2)
					->add_style('display', 'inline-block')
					// ->add_attr('style', 'border: green 1px solid;')
					;
			}
			else {
				$wrap_el
					->add_style('display', 'block')
					->add_attr('style', 'padding: 0 0;')
					;
			}
			$el->add_child( $wrap_el );
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Select extends HC_Form_Input
{
	protected $options = array();

	function add_option( $key, $label )
	{
		$this->options[ $key ] = $label;
		return $this;
	}

	function set_options( $options )
	{
		foreach( $options as $key => $label ){
			$this->add_option( $key, $label );
		}
		// $this->options = $options;
		return $this;
	}
	function options()
	{
		return $this->options;
	}

	function render()
	{
		$readonly = $this->readonly();
//$readonly = FALSE;
		$options = $this->options();
		$value = $this->value();

		if( is_array($options) ){
			if( $readonly ){
				$return = isset($options[$value]) ? $options[$value] : '';
			}
			else {
				$el = HC_Html_Factory::element( 'select' )
					->add_attr( 'id', $this->id() )
					->add_attr( 'name', $this->name() )
					->add_style('form-control')
					;

				reset( $options );
				foreach( $options as $key => $label ){
					$option = HC_Html_Factory::element('option');
					$option->add_attr( 'value', $key );
					$option->add_child( $label );
					if( $this->value() == $key ){
						$option->add_attr( 'selected', 'selected' );
					}
					$el->add_child( $option );
				}

				$attr = $this->attr();
				foreach( $attr as $k => $v ){
					$el->add_attr($k, $v);
				}

				$return = $el->render();
			}
		}

		$return = $this->decorate( $return );
		return $return;
	}
}

class HC_Form_Input_Dropdown extends HC_Form_Input_Select
{
}

class HC_Form_Input_Text extends HC_Form_Input
{
	function render()
	{
		$readonly = $this->readonly();
		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'text' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'id', $this->id() )
			->add_attr( 'value', $this->value() )
			->add_style('form-control')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		if( $readonly ){
			$el->add_attr('readonly', 'readonly');
		}
		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Label extends HC_Form_Input
{
	function render()
	{
		$return = $this->value();
		$return = $this->decorate( $return );
		return $return;
	}
}

class HC_Form_Input_Password extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'password' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'id', $this->id() )
			->add_attr( 'value', $this->value() )
			->add_style('form-control')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Checkbox extends HC_Form_Input
{
	protected $label = '';
	protected $value = 0;
	protected $my_value = '';

	function value()
	{
		return $this->value ? 1 : 0;
	}

	function set_label( $label = '' )
	{
		$this->label = $label;
		return $this;
	}
	function label()
	{
		return $this->label;
	}

	function set_my_value( $my_value = '' )
	{
		$this->my_value = $my_value;
		return $this;
	}
	function my_value()
	{
		return $this->my_value;
	}

	function render( $decorate = TRUE )
	{
		$label = $this->label();
		$value = $this->value();
		$my_value = $this->my_value();

		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'checkbox' )
			->add_attr( 'id', $this->id() )
			->add_attr( 'name', $this->name() )
			->add_attr( 'value', $this->my_value() )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		if( $this->readonly() ){
			// $el->add_attr('readonly', 'readonly' );
			$el->add_attr('disabled', 'disabled' );
		}

		if( $value ){
			$el->add_attr('checked', 'checked');
		}

		$el = HC_Html_Factory::widget('container')
			->add_child( $el );

		if( strlen($label) ){
			$label = HC_Html_Factory::widget('titled', 'span')
				->add_child($label)
				->add_style('nowrap')
				;
			$el->add_child($label);
		}
		if( $this->readonly() ){
			$hidden = HC_Html_Factory::input('hidden', $this->name() );
			if( $value )
				$hidden->set_value( $my_value );
			else
				$hidden->set_value( '' );
			$el->add_child($hidden);
		}

		$el = HC_Html_Factory::element('label')
			// ->add_attr('style', 'padding-left: 0;')
			->add_child( $el )
			// ->add_attr('style', 'padding: 0 0; border: red 1px solid; margin: 0 0 0 0; line-height: 1em;')
			;
		$return = $el->render();

		return $return;
	}
}

class HC_Form_Input_Hidden extends HC_Form_Input
{
	function render()
	{
		$el = HC_Html_Factory::element( 'input' )
			->add_attr( 'type', 'hidden' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'id', $this->id() )
			->add_attr( 'value', $this->value() )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el->render() );
		return $return;
	}
}

class HC_Form_Input_Composite extends HC_Form_Input
{
	protected $fields = array();

	function set_value( $value = array() )
	{
		reset( $this->fields );
		foreach( $this->fields as $k => $f ){
			if( array_key_exists($k, $value) ){
				$this->fields[$k]->set_value($value[$k]); 
			}
		}
		parent::set_value( $value );
	}

	function grab( $post )
	{
		$value = array();
		foreach( $this->fields as $k => $f ){
			$f->grab($post);
			$value[$k] = $f->value();
		}
		$this->set_value( $value );
	}
}
