<?php
include_once( dirname(__FILE__) . '/html/styler.php' );
include_once( dirname(__FILE__) . '/html/styler_theme.php' );

class Hc_Renderer
{
	function render( $view_file, $view_params = array() )
	{
		if( $view_params ){
			extract($view_params);
		}

		ob_start();
		require( $view_file );
		$output = ob_get_contents();
		ob_end_clean();
		$output = trim( $output );
		return $output;
	}
}

class HC_View_Layout
{
	protected $partials = array();
	protected $template = '';
	protected $params = array();

	function set_partial( $key, $value )
	{
		$this->partials[$key] = $value;
	}
	function partial( $key )
	{
		$return = '';
		if( isset($this->partials[$key]) ){
			if( is_array($this->partials[$key]) ){
				$return = join( '', $this->partials[$key] );
			}
			else {
				$return = $this->partials[$key];
			}
		}
		return $return;
	}

	function has_partial( $key )
	{
		return (isset($this->partials[$key]) && $this->partials[$key]) ? TRUE : FALSE;
	}

	function set_template( $template )
	{
		$this->template = $template;
	}
	function template()
	{
		return $this->template;
	}

	function set_params( $params )
	{
		foreach( $params as $param => $value )
		{
			$this->set_param( $param, $value );
		}
	}
	function set_param( $param, $value )
	{
		$this->params[ $param ] = $value;
	}
	function params()
	{
		return $this->params;
	}
	function param( $key )
	{
		$return = isset($this->params[$key]) ? $this->params[$key] : '';
		return $return;
	}

	public function __toString()
	{
		return $this->render();
    }
}

class HC_Html_Factory
{
	public static function element( $element )
	{
		$return = new HC_Html_Element( $element );
		return $return;

/*
		static $el_obj = array();
		if( ! isset($el_obj[$element]) ){
			echo 'INIT ELEMENT';
			$el_obj[$element] = new HC_Html_Element( $element );
		}
		$return = clone $el_obj[$element];
		$return->set_tag( $element );
		return $return;
*/
	}

	public static function input( $element, $name = '' )
	{
		static $classes = array();
		$class_key = 'input_' . $element;

		if( isset($classes[$class_key]) ){
			$class = $classes[$class_key];
		}
		else {
			$widget_locations = HC_App::widget_locations();
			foreach( $widget_locations as $prfx => $locations ){
				$class = strtoupper($prfx) . '_Form_Input_' . ucfirst($element);
				if( ! class_exists($class) ){
					/* attempt to load the file */
					if( ! is_array($locations) ){
						$locations = array( $locations );
					}
					foreach( $locations as $location ){
						$file = $location . '/form/' . $element . '.php';
// echo "ATTEMPT TO LOAD '$class' IN '$file'<br>";
						if( file_exists($file) ){
							include_once( $file );
							break;
						}
					}
				}
				if( class_exists($class) ){
					$classes[$class_key] = $class;
					break;
				}
			}
		}

		if( class_exists($class) ){
			if( $name )
				$return = new $class( $name );
			else
				$return = new $class;
			return $return;
		}
		else {
			throw new Exception( "No class defined: '$class'" );
		}
	}

	public static function widget( $element )
	{
		static $classes = array();
		$class_key = 'widget_' . $element;

		if( isset($classes[$class_key]) ){
			$class = $classes[$class_key];
		}
		else
		{
			$widget_locations = HC_App::widget_locations();
			foreach( $widget_locations as $prfx => $locations ){
				$class = strtoupper($prfx) . '_Html_Widget_' . ucfirst($element);
				if( ! class_exists($class) ){
					/* attempt to load the file */
					if( ! is_array($locations) ){
						$locations = array( $locations );
					}
					foreach( $locations as $location ){
//echo "ATTEMPT TO LOAD '$class'<br>";
						$file = $location . '/html/' . $element . '.php';
						if( file_exists($file) ){
							include_once( $file );
							break;
						}
					}
				}
				if( class_exists($class) ){
					$classes[$class_key] = $class;
					break;
				}
			}
		}

		$args = func_get_args();
		if( class_exists($class) ){
			$return = new $class();
			array_shift( $args );
			if( $args ){
				call_user_func_array( array($return, "init"), $args );
			}
			return $return;
		}
		else {
			throw new Exception( "No class defined: '$class'" );
		}
	}
}

class HC_Html_Element_Attr
{
	protected $attr = array();

	function get( $key = '' )
	{
		if( $key === '' ){
			$return = $this->attr;
		}
		elseif( isset($this->attr[$key]) ){
			$return = $this->attr[$key];
		}
		else {
			$return = array();
		}
		return $return;
	}
	
	function reset()
	{
		$this->attr = array();
		return $this;
	}

	function add( $key, $value = NULL )
	{
		if( count(func_get_args()) == 1 ){
			// supplied as array
			foreach( $key as $key => $value ){
				$this->add( $key, $value );
			}
		}
		else {
			if( is_array($value) ){
				foreach( $value as $v ){
					$this->add( $key, $v );
				}
			}
			else {
				$value = $this->prep( $key, $value );
				if( isset($this->attr[$key]) ){
					$this->attr[$key][] = $value;
				}
				else {
					if( ! is_array($value) )
						$value = array( $value ); 
					$this->attr[$key] = $value;
				}
			}
		}
		return $this;
	}

	protected function prep( $key, $value )
	{
		switch( $key ){
			case 'title':
				if( is_string($value) ){
					$value = strip_tags($value);
					$value = trim($value);
				}
				break;
		}
		return $value;
	}
}

class HC_Html_Element
{
	protected $tag = 'input';
	public $attr = NULL;
	protected $children = array();
	protected $addon = array();
	protected $wrap = array();

	protected $stylers = array();

	protected $child_styles = array();
	protected $children_styles = array();

	protected $child_attr = array();
	protected $children_attr = NULL;

	protected $skip_css_pref = FALSE;

	function __clone()
	{
		$this->attr = clone $this->attr;
		$this->children_attr = clone $this->children_attr;
	}

	function __construct( $tag = '' )
	{
		if( strlen($tag) )
			$this->set_tag( $tag );
		
		static $attr = NULL;
		if( ! $attr ){
			$attr = new HC_Html_Element_Attr;
		}
		$this->attr = clone $attr;
		$this->children_attr = clone $attr;

		static $stylers = array();
		if( ! $stylers ){
			if( class_exists('HC_Html_Element_Styler_Theme_Rewrite') ){
				$stylers[] = new HC_Html_Element_Styler_Theme_Rewrite;
			}
			$stylers[] = new HC_Html_Element_Styler_Theme;
			$stylers[] = new HC_Html_Element_Styler;
		}
		foreach( $stylers as $styler ){
			$this->add_styler( $styler );
		}

		if( defined('NTS_DEVELOPMENT') OR defined('NTS_PROFILER') ){
			global $NTS_COUNT_HTML_ELEMENTS;
			if( ! isset($NTS_COUNT_HTML_ELEMENTS) ){
				$NTS_COUNT_HTML_ELEMENTS = 0;
			}
			$NTS_COUNT_HTML_ELEMENTS++;
		}
	}

	public function __toString()
	{
		return $this->render();
    }

	public function skip_css_pref()
	{
		return $this->skip_css_pref;
	}

	public function set_skip_css_pref( $skip_css_pref = 1 )
	{
		$this->skip_css_pref = $skip_css_pref;
		return $this;
	}

	public function add_styler( $styler )
	{
		$this->stylers[] = $styler;
	}

	public function add_style( $style ){
		$args = func_get_args();
		$style = array_shift( $args );
		$method = str_replace( '-', '_', $style );

		reset( $this->stylers );
		foreach( $this->stylers as $styler ){
			if( method_exists($styler, $method) ){
				$el = call_user_func_array( array($styler, $method), array_merge(array($this), $args) );
				$this->attr = $el->attr;
				break;
			}
		}

		return $this;
	}

	public function add_child_style( $key, $style ){
		$args = func_get_args();
		$key = array_shift( $args );
		$style = array_shift( $args );
		if( ! isset($this->child_styles[$key]) ){
			$this->child_styles[$key] = array();
		}

		$value = count($args) ? array_shift($args) : 1;
		$this->child_styles[$key][$style] = $value;
		return $this;
	}
	public function child_styles($key){
		$return = isset($this->child_styles[$key]) ? $this->child_styles[$key] : array();
		return $return;
	}

	/* add to all items */
	function add_children_attr( $key, $value )
	{
/*
		if( ! isset($this->children_attr) ){
			$this->children_attr = HC_Html_Factory::element('a');
		}
		$this->children_attr->add_attr( $key, $value );
*/
		$this->children_attr->add( $key, $value );
		return $this;
	}
	function children_attr( $key = '' )
	{
/*
		if( ! isset($this->children_attr) ){
			$this->children_attr = HC_Html_Factory::element('a');
		}
		return $this->children_attr->attr( $key );
*/
		return $this->children_attr->get( $key );
	}

	/* add to all items */
	public function add_children_style( $style, $value = NULL ){
		if( ! isset($this->children_styles[$style]) ){
			$this->children_styles[$style] = array();
		}

		$args = func_get_args();
		$style = array_shift( $args );
		$this->children_styles[$style] = array_merge( $this->children_styles[$style], $args );

		return $this;
	}
	function children_styles()
	{
		return $this->children_styles;
	}

	function add_child_attr( $item, $key, $value )
	{
		if( ! isset($this->child_attr[$item]) ){
			$this->child_attr[$item] = HC_Html_Factory::element('a');
		}
		$this->child_attr[$item]->add_attr( $key, $value );
		return $this;
	}
	function child_attr( $item, $key = '' )
	{
		if( ! isset($this->child_attr[$item]) ){
			$this->child_attr[$item] = HC_Html_Factory::element('a');
		}
		return $this->child_attr[$item]->attr( $key );
	}

	function init( $smth = NULL )
	{
	}

	function set_tag( $tag )
	{
		$this->tag = $tag;
		return $this;
	}
	function tag()
	{
		return $this->tag;
	}

	function attr( $key = '' )
	{
		$return = $this->attr->get($key);
		return $return;
	}

	function add_attr( $key, $val = NULL )
	{

		switch( $key ){
			case 'class':
				if( $this->skip_css_pref() OR (defined('HC_SKIP_CSS_PREFIX') && HC_SKIP_CSS_PREFIX) ){
					continue;
				}
				$skip = array('fa', 'hc-', 'hcj-', 'pure-', 'oi', 'bass-', 'ion', 'typcn', 'icomoon');
				$append = 'hc-';

				if( ! is_array($val) ){
					$val = array($val);
				}

				for( $ii = 0; $ii < count($val); $ii++ ){
					if( substr($val[$ii], 0, strlen($append)) != $append ){
						$append_this = TRUE;
						reset( $skip );
						foreach( $skip as $sk ){
							if( substr($val[$ii], 0, strlen($sk)) == $sk ){
								$append_this = FALSE;
								break;
							}
						}
						if( $append_this ){
							$val[$ii] = $append . $val[$ii];
						}
					}
				}
				break;
		}

		$this->attr->add( $key, $val );
		return $this;
	}

	function add_child( $child, $child_value = NULL )
	{
		if( $child_value === NULL )
			$this->children[] = $child;
		else
			$this->children[$child] = $child_value;
		return $this;
	}
	function remove_child( $key )
	{
		unset($this->children[$key]);
		return $this;
	}

	function child( $key )
	{
		return isset($this->children[$key]) ? $this->children[$key] : NULL;
	}

	function prepend_child( $child )
	{
		array_unshift( $this->children, $child );
		return $this;
	}
	function remove_children()
	{
		$this->children = array();
		return $this;
	}
	function set_children( $children )
	{
		$this->children = $children;
		return $this;
	}
	function children()
	{
		return $this->children;
	}

	function add_wrap( $wrap )
	{
		$this->wrap[] = $wrap;
		return $this;
	}
	function wrap()
	{
		return $this->wrap;
	}

	function add_addon( $addon )
	{
		$this->addon[] = $addon;
		return $this;
	}
	function addon()
	{
		return $this->addon;
	}

	protected function _prepare_children()
	{
		$return = '';
		$children = $this->children();

		if( $children ){
			$children_attr = $this->children_attr();

			reset( $children );
			foreach( $children as $key => $child ){
//				$return .= "\n";
				if( is_array($child) ){
					foreach( $child as $subchild ){
						if( is_object($subchild) ){
							$return .= $subchild->render();
						}
						else {
							$return .= $subchild;
						}
					}
				}
				elseif( is_object($child) ){
					reset( $children_attr );
					foreach( $children_attr as $k => $v ){
						$child->add_attr( $k, $v );
					}

					$child_attr = $this->child_attr($key);
					foreach( $child_attr as $k => $v ){
						$child->add_attr( $k, $v );
					}

					$children_styles = $this->children_styles();
					foreach( $children_styles as $k => $v ){
						call_user_func_array( array($child, 'add_style'), array_merge(array($k), $v) );
					}

					$child_styles = $this->child_styles($key);
					foreach( $child_styles as $k => $v ){
						$child->add_style( $k, $v );
					}

					$return .= $child->render();
				}
				else {
					$return .= $child;
				}
			}
		}
		return $return;
	}

	function render()
	{
		$return = '';
		$return .= '<' . $this->tag();

		$attr = $this->attr();
		if( $attr ){
			foreach( $attr as $key => $val ){
				switch( $key ){
					/*
					case 'class':
						if( defined('HC_SKIP_CSS_PREFIX') && HC_SKIP_CSS_PREFIX ){
							continue;
						}
						$skip = array('fa', 'hc-', 'hcj-', 'pure-', 'oi', 'bass-', 'ion', 'typcn', 'icomoon');
						$append = 'hc-';

						for( $ii = 0; $ii < count($val); $ii++ ){
							if( substr($val[$ii], 0, strlen($append)) != $append ){
								$append_this = TRUE;
								reset( $skip );
								foreach( $skip as $sk ){
									if( substr($val[$ii], 0, strlen($sk)) == $sk ){
										$append_this = FALSE;
										break;
									}
								}
								if( $append_this ){
									$val[$ii] = $append . $val[$ii];
								}
							}
						}
						break;
					*/

					case 'value':
						for( $ii = 0; $ii < count($val); $ii++ ){
							$val[$ii] = htmlspecialchars( $val[$ii] );
							$val[$ii] = str_replace( array("'", '"'), array("&#39;", "&quot;"), $val[$ii] );
						}
						break;
				}

				$val = join(' ', $val);
				if( strlen($val) ){
					$return .= ' ' . $key . '="' . $val . '"';
				}
			}
		}

		$children_return = $this->_prepare_children();
		if( strlen($children_return) ){
			$return .= '>';
			$return .= $children_return;
//			$return .= "\n";
			$return .= '</' . $this->tag() . '>';
		}
		else {
			if( in_array($this->tag(), array('br', 'input')) ){
				$return .= '/>';
			}
			else {
				$return .= '></' . $this->tag() . '>';
			}
		}

		$addon = $this->addon();
		if( $addon ){
			reset( $addon );
			foreach( $addon as $ao ){
				if( is_object($ao) ){
					$return .= $ao->render();
				}
				else {
					$return .= $ao;
				}
			}
		}

		if( $wrap = $this->wrap() ){
			foreach( $wrap as $wr ){
				$return = $wr->add_child($return)->render();
			}
		}

		return $return;
	}
}

class HC_Html
{
	static function icon( $icon, $inside = '' )
	{
		$return = NULL;
		if( substr($icon, 0, 2) == '<i' )
			return $icon;

		$presenter = HC_App::presenter('html_icon');
		if( $presenter ){
			$return = $presenter->icon( $icon, $inside );
			// echo 'PRESENTER EXSTS';
		}

		return $return;
	}

	static function page_header( $header )
	{
		$wrap = HC_Html_Factory::element('div')
			->add_child( $header )
			->add_style('border', 'bottom')
			->add_style('margin', 'b3', 't1')
			->add_style('padding', 'y2')
			;
		$wrap
			->add_children_style('padding', 0)
			->add_children_style('margin', 0)
			;
		return $wrap->render();
	}

	/**
	* input
	*
	* Outputs HTML code for input
	*
	* @param	array $input ('value', 'error', 'type', 'name')
	* @return	string
	*/
	static function input( $input_array, $more = array() )
	{
		$return = '';

		$value = isset($input_array['value']) ? $input_array['value'] : '';
		$el = HC_Html_Factory::input(
			$input_array['type'],
			$input_array['name'],
			$value,
			$more
			);

		$error = isset($input_array['error']) ? $input_array['error'] : '';
		if( $error ){
			$el->set_error( $error );
		}

		return $el->render();
	}
}

include_once( dirname(__FILE__) . '/widgets/form/basic.php' );
