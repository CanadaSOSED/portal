<?php
class HC_Form_Input_Checkbox_Set extends HC_Form_Input
{
	protected $options = array();
	protected $dependencies = array();
	protected $more = array();
	protected $readonly = array();
	protected $value = array();
	protected $inline = TRUE;

	function add_option( $value, $label = NULL, $more = '' )
	{
		$this->options[$value] = $label;
		if( $more ){
			$this->more[$value] = $more;
		}
		return $this;
	}
	function set_value( $value )
	{
		if( ! is_array($value) ){
			if( strlen($value) ){
				$value = array( $value );
			}
			else {
				$value = array();
			}
		}
		parent::set_value( $value );
	}

	function set_options( $options )
	{
		$this->options = $options;
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

	function set_dependencies( $dependencies )
	{
		$this->dependencies = $dependencies;
	}
	function dependencies()
	{
		return $this->dependencies;
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

	function set_readonly( $value = TRUE )
	{
		$args = func_get_args();
		$value = array_shift( $args );
		$ro = array_shift( $args );

		$this->readonly[$value] = $ro;
		return $this;
	}
	function readonly( $value = NULL )
	{
		if( $value === NULL ){
			$return = $this->readonly;
		}
		else {
			$return = 
				( array_key_exists($value, $this->readonly) && $this->readonly[$value] )
				? TRUE
				: FALSE
			;
		}
		return $return;
	}

	function grab( $post )
	{
		$name = $this->name();
		$value = array();
		if( isset($post[$name]) ){
			$value = $post[$name];
		}
		$this->set_value( $value );
	}

	function render_one( $value, $decorate = TRUE )
	{
		$options = $this->options();
		$full_value = $this->value();
		$label = $options[$value];
		$inline = $this->inline();

		$sub_el = HC_Html_Factory::input('checkbox', $this->name() . '[]' )
			->set_my_value($value)
			;
		if( $this->readonly($value) ){
			$sub_el->set_readonly();
			}
		if( strlen($label) ){
			$sub_el->set_label( $label );
		}
		if( in_array($value, $full_value) ){
			$sub_el->set_value(1);
		}

		if( $inline ){
			// $sub_el->add_attr('style', 'height: 1.5rem;');
		}

		if( $decorate ){
			$return = $this->decorate( $sub_el->render() );
		}
		else {
			$return = $sub_el->render($decorate);
		}
		return $return;
	}

	function render()
	{
		$options = $this->options();
		$full_value = $this->value();
		$inline = $this->inline();
		$dependencies = $this->dependencies();

		$el = HC_Html_Factory::widget('list')
			->add_attr('class', 'hc-form-control-static')
			;

		if( $dependencies ){
			$this_id = 'nts' . hc_random();
			$el->add_attr('id', $this_id );
		}

		if( $inline ){
			$el
				->add_children_style('inline')
				->add_children_style('margin', 'r1', 'b1')
				;
		}
		else {
			$el
				->add_children_style('margin', 'b1')
				// ->add_children_style('border')
				;
		}

		$attr = $this->attr();
		foreach( $attr as $key => $val ){
			$el->add_attr($key, $val);
		}

		foreach( $options as $value => $label ){
			$el->add_child( $this->render_one($value) );
		}

		$return = $this->decorate( $el->render(), FALSE );

		$js = '';

		if( $dependencies ){
			$deps_view = array();
			foreach( $dependencies as $k => $deps ){
				foreach( $deps as $d ){
					$deps_view[] = "['" . $k . "', '" . $d . "']";
				}
			}
			$deps_view = join(', ', $deps_view);

			$js .= <<<EOT
<script language="JavaScript">
var {$this_id}_dependencies = [$deps_view];

jQuery('#{$this_id} input[type=checkbox]').on('change', function(){
	var my_val = jQuery(this).val();
	var me_checked = ( jQuery(this).is(":checked") ) ? true : false;

	/* check if we have dependent items */
	for( var ii = 0; ii < {$this_id}_dependencies.length; ii++ ){
		var this_pair = {$this_id}_dependencies[ii];

		/* if this is set then set dependant */
		if( this_pair[0] == my_val ){
			var dependent_items = jQuery('#{$this_id} input[value=' + this_pair[1] + ']');
			if( me_checked ){
				dependent_items.each( function()
				{
					jQuery(this).prop('checked', me_checked);
					jQuery(this).trigger('change');
				});
			}
		}

		/* if this is unset then unset parents */
		if( this_pair[1] == my_val ){
			var parent_items = jQuery('#{$this_id} input[value=' + this_pair[0] + ']');
			if( ! me_checked ){
				parent_items.each( function()
				{
					jQuery(this).prop('checked', me_checked);
					jQuery(this).trigger('change');
				});
			}
		}
	}
});
</script>

EOT;

		$return = $return . $js;
		}

		return $return;
	}
}