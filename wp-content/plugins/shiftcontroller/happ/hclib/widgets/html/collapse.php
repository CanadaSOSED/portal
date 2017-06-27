<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Collapse extends HC_Html_Widget_Container
{
	private $title = '';
	private $more_title = '';
	private $content = '';
	private $default_in = FALSE;
	private $self_hide = FALSE;
	private $panel = NULL;
	protected $no_caret = TRUE;

	function __construct()
	{
		parent::__construct();
		$this->id = 'nts_' . hc_random();
	}

	public function add_more_title( $more )
	{
		$this->more_title[] = $more;
		return $this;
	}
	public function more_title()
	{
		return $this->more_title;
	}

	public function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	public function title()
	{
		return $this->title;
	}

	function set_no_caret( $no_caret = TRUE )
	{
		$this->no_caret = $no_caret;
		return $this;
	}
	function no_caret()
	{
		return $this->no_caret;
	}

	function set_panel( $panel = TRUE )
	{
		$this->panel = $panel;
		return $this;
	}
	public function panel()
	{
		return $this->panel;
	}

	public function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	public function content()
	{
		return $this->content;
	}

	public function set_default_in( $default_in = TRUE )
	{
		$this->default_in = $default_in;
		return $this;
	}
	public function default_in()
	{
		return $this->default_in;
	}

	public function set_self_hide( $self_hide = TRUE )
	{
		$this->self_hide = $self_hide;
		return $this;
	}
	public function self_hide()
	{
		return $this->self_hide;
	}

	public function render_content()
	{
		$return = HC_Html_Factory::element('div')
			->add_attr('class', 'hcj-collapse')
			->add_attr('id', $this->id)
			;
		$return->add_child( $this->content() );
		return $return;
	}

	public function render_trigger( $self = FALSE )
	{
		$panel = $this->panel();
	/* build trigger */
		$title = $this->title();
		if( 
			is_object($title) &&
			( $title->tag() == 'a' )
		){
			$trigger = $title;
		}
		else {
			$full_title = $title;
			$title = strip_tags($title);
			$title = trim($title);

			$trigger = HC_Html_Factory::widget('titled', 'a')
				->add_child( 
					$full_title
					)
				;
		}

		if( $self ){
			$trigger
				->add_attr('href', '#')
				->add_attr('class', 'hcj-collapse-next')
				;
		}
		else {
			$trigger
				// ->add_attr('href', '#' . $this->id)
				->add_attr('href', '#')
				->add_attr('data-target', $this->id)
				->add_attr('class', 'hcj-collapser')
				;
		}

		if( ! $this->no_caret() ){
			$trigger
				->add_child( ' ' )
				->add_child(
					HC_Html::icon('caret-down')
					)
				;
		}

		$trigger
			// ->add_style('display', 'block')
			;

		$more_title = $this->more_title();

		if( $more_title ){
			$trigger = HC_Html_Factory::widget('list')
				->add_children_style('display', 'inline-block')
				->add_child( 'trigger', $trigger )
				;

			foreach( $more_title as $mt ){
				$trigger->add_child( $mt );
			}
		}

		return $trigger;
	}

	public function render()
	{
		$panel = $this->panel();

		$out = HC_Html_Factory::element('ul')
			->add_attr('class', 'hcj-collapse-panel')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$trigger = $this->render_trigger('self');

		$self_hide = $this->self_hide();
		if( $self_hide ){
			$trigger->add_attr('class', 'hcj-collapser-hide');
		}

		$wrap_trigger = HC_Html_Factory::element('li')
			->add_child( $trigger )
			;
		if( $panel ){
			$wrap_trigger
				// ->add_style('bg-color', 'silver')
				->add_style('padding')
				;
		}

		$out->add_child(
			$wrap_trigger
			);

		$content = HC_Html_Factory::element('li')
			->add_attr('class', 'hcj-collapse')
			// ->add_style('margin', 't1')
			;
		if( $panel ){
			$content
				->add_attr('class', 'hcj-panel-collapse')
				->add_style('border', 'top');
				;
			
		}
		if( $this->default_in() ){
			$content->add_attr('class', 'hcj-open');
		}

		if( $panel ){
			$out
				->add_style('border')
				->add_style('rounded')
				;
		}

		if( $panel ){
			$content->add_child( 
				HC_Html_Factory::element('div')
					->add_child( $this->content() )
					->add_style('padding')
				);
		}
		else {
			$content->add_child( $this->content() );
		}

		$out->add_child( $content );
		return $out->render();
	}
}
?>