<?php
class HC_Html_Widget_Main_Menu extends HC_Html_Element
{
	protected $menu = array();
	protected $disabled = array();
	protected $current = '';
	protected $engine = 'ci'; // can also be 'nts'
	protected $root = '';

	public function __construct( $engine = 'ci' )
	{
		parent::__construct();
		$this->set_engine( $engine );
	}

	public function set_engine( $engine )
	{
		$this->engine = $engine;
		return $this;
	}
	public function engine()
	{
		return $this->engine;
	}

	public function set_root( $root )
	{
		$this->root = $root;
		return $this;
	}
	public function root()
	{
		return $this->root;
	}

	public function set_menu( $menu )
	{
		$this->menu = $menu;
		return $this;
	}

	public function set_disabled( $disabled = array() )
	{
		if( $disabled )
			$this->disabled = $disabled;
		return $this;
	}

	public function set_current( $current )
	{	
		$this->current = $current;
		return $this;
	}

	private function _prepare_menu()
	{
		if( ! ($this->menu && is_array($this->menu)) ){
			return;
		}

		$order = 1;
		$menu_keys = array_keys($this->menu);
		reset( $menu_keys );
		$active_k = '';
 
		foreach( $menu_keys as $k )
		{
			if( ! is_array($this->menu[$k]) ){
				$this->menu[$k] = array(
					'title'	=> $this->menu[$k]
					);
			}
			if( ! isset($this->menu[$k]['order']) ){
				$this->menu[$k]['order'] = $order++;
			}
			if( ! isset($this->menu[$k]['icon']) ){
				$this->menu[$k]['icon'] = '';
			}

			if( ! 
				(
					(isset($this->menu[$k]['external']) && $this->menu[$k]['external']) OR 
					(isset($this->menu[$k]['href']) && $this->menu[$k]['href'])
				)
				){
				switch( $this->engine() ){
					case 'ci':
						$this->menu[$k]['slug'] = $this->menu[$k]['link'];
						$this->menu[$k]['href'] = HC_Lib::link( $this->menu[$k]['link'] );
						break;
					case 'nts':
						if( ! isset($this->menu[$k]['panel']) ){
							$this->menu[$k]['panel'] = $k;
						}

						$this->menu[$k]['slug'] = $this->menu[$k]['panel'];
						$this->menu[$k]['href'] = ntsLink::makeLink( $this->menu[$k]['panel'], '', array(), FALSE, TRUE );
						break;
				}
			}

			if( $this->disabled && ( ! ( isset($this->menu[$k]['external']) && $this->menu[$k]['external'] ) ) ){
				$this_slug = $this->menu[$k]['slug'];
				if( in_array($this_slug, $this->disabled) ){
//					echo "DISABLE " . $this->menu[$k]['slug'] . '<br>';
					unset( $this->menu[$k] );
				}
				else {
					/* also check if a parent is disabled */
					foreach( $this->disabled as $ds ){
						if( substr($this_slug, 0, strlen($ds)) == $ds ){
							unset( $this->menu[$k] );
							break;
						}
					}
				}
			}

			/* check if current */
			if( $this->current && (! $active_k) ){
				$slug = isset($this->menu[$k]['slug']) ? $this->menu[$k]['slug'] : '';
				$current = $this->current;

				if(
					(
						($current == $slug)
					)
					OR
					( 
						( substr($current, 0, strlen($slug)) == $slug ) &&
						( substr($current, strlen($slug), 1) == '/' )
					)
					){
					$active_k = $k;
				}
			}
		}

	/* set current */
		if( $active_k ){
			reset( $menu_keys );
			foreach( $menu_keys as $k ){
				if( 
					( $k == $active_k )
					OR
					(
						( substr($active_k, 0, strlen($k)) == $k ) &&
						( substr($active_k, strlen($k), 1) == '/' )
					)
				){
					$this->menu[$k]['active'] = TRUE;
				}
			}
		}

		uasort( $this->menu, create_function('$a, $b', 'return ($a["order"] - $b["order"]);' ) );
	}

	private function _filter_menu( $root )
	{
		if( $this->menu && is_array($this->menu) ){
			$menu_keys = array_keys($this->menu);
			foreach( $menu_keys as $k ){
				if( substr($k, 0, strlen($root)) != $root ){
					unset( $this->menu[$k] );
				}
			}
		}
	}

	private function _get_menu( $root )
	{
		$this->_filter_menu( $root );
		$this->_prepare_menu();
		$return = array();

		if( ! ($this->menu && is_array($this->menu)) ){
			return $return;
		}

		$menu_keys = array_keys($this->menu);
		reset( $menu_keys );
		foreach( $menu_keys as $k ){
			$this_level = substr_count( $k, '/' );
			if( $this_level > 1 )
				continue;
			if( substr($k, 0, strlen($root)) != $root )
				continue;

			$this_m = $this->menu[$k];

			$children = array();
			$has_children = FALSE;
			reset( $menu_keys );
			foreach( $menu_keys as $k2 ){
				if( $k == $k2 )
					continue;
				if( substr($k2, 0, strlen($k)) == $k ){
					$their_level = substr_count( $k2, '/' );
					if( $their_level == ($this_level + 1) ){
						$has_children = TRUE;
						$their_m = $this->menu[$k2];
						$children[$k2] = $their_m;
					}
				}
			}

			if( $children ){
				if( count($children) == 1 ){
					$chkeys = array_keys($children);
					$this_m = $children[ $chkeys[0] ];
				}
				else {
					$this_m['children'] = $children;
				}
			}
			$return[ $k ] = $this_m;
		}
		return $return;
	}

	public function render()
	{
		$colors = array(
			// 'bg'		=> 'silver',
			// 'active'	=> 'gray',
			// 'bg'		=> '',
			// 'active'	=> 'silver',

/*
			'bg'		=> 'black',
			'font'		=> 'silver',
			'active-bg'	=> 'silver',
			'active'	=> 'black',
*/
			'bg'		=> 'darkgray',
			'font'		=> 'silver',
			'active-bg'	=> 'black',
			'active'	=> 'silver',
			);

		$root = $this->root();

		$menu = $this->_get_menu( $root );
		$return = '';
		if( ! $menu ){
			return $return;
		}

		$nav = HC_Html_Factory::element('ul')
			->add_style('margin', 'b2')
			// ->add_style('border')
			->add_style('rounded')
			->add_children_style('display', 'inline-block')
			->add_children_style('margin', 'r2')
			;

		if( $colors['bg'] ){
			$nav->add_style('bg-color', $colors['bg']);
		}
		if( $colors['font'] ){
			$nav->add_style('color', $colors['font']);
		}

		foreach( $menu as $mk => $m ){
			if( isset($m['children']) && $m['children'] ){
				$item = HC_Html_Factory::widget('dropdown')
					->set_wrap(FALSE)
					// ->add_style('padding', 2)
					->add_style('padding', 0)
					;

				if( $colors['bg'] ){
					$item->add_style('bg-color', $colors['bg']);
				}
				if( $colors['font'] ){
					$item->add_style('color', $colors['font']);
				}

				$title = HC_Html_Factory::widget('titled', 'a')
					;
				if( isset($m['icon']) ){
					$title->add_child( HC_Html::icon($m['icon']) );
				}
				$title->add_child( 
					HC_Html_Factory::element('span')
						->add_child( $m['title'] )
						->add_style('hidden', 'xs')
					);

				$title
					->add_style('btn')
					->add_style('padding', 'x2', 'y3')
					;

				if( isset($m['active']) && $m['active'] ){
					// $title->add_style('btn-primary');
					if( $colors['active-bg'] ){
						$title->add_style('bg-color', $colors['active-bg']);
					}
					if( $colors['active'] ){
						$title->add_style('color', $colors['active']);
					}
				}

				$item->set_title( $title );

				reset( $m['children'] );
				foreach( $m['children'] as $submenu ){
					$subitem = HC_Html_Factory::widget('titled', 'a')
						->add_attr('href', $submenu['href'])
						->add_style('btn')
						->add_style('padding', 1)
						->add_style('display', 'block')
						;
					if( isset($submenu['icon']) ){
						$subitem
							->add_child( HC_Html::icon($submenu['icon']) )
							;
					}
					$subitem
						->add_child( $submenu['title'] )
						;

					if( isset($submenu['active']) && $submenu['active'] ){
						// $subitem->add_style('btn-primary');
						if( $colors['active'] ){
							$subitem->add_style('color', $colors['active']);
						}
						if( $colors['active-bg'] ){
							$subitem->add_style('bg-color', $colors['active-bg']);
						}
					}
					else {
						if( $colors['bg'] ){
							$subitem->add_style('bg-color', $colors['bg']);
						}
						if( $colors['font'] ){
							$subitem->add_style('color', $colors['font']);
						}
					}

					$item->add_child( $subitem );
				}
			}
			else
			{
				if( isset($m['external']) && $m['external'] ){
					$item = HC_Html_Factory::element('a')
						->add_attr('href', $m['link'])
						->add_attr('title', $m['title'])
						->add_attr('target', '_blank')
						->add_child( 
							HC_Html_Factory::element('span')
								->add_style('box')
								->add_style('border-color', 'olive')
								->add_child( $m['title'] )
							)
						;
				}
				else {
					$item = HC_Html_Factory::element('a')
						->add_attr('href', $m['href'])
						->add_attr('title', $m['title'])
						->add_child(
							HC_Html::icon($m['icon'])
							)
						->add_child(
							HC_Html_Factory::element('span')
								->add_child( $m['title'] )
								->add_style('hidden', 'xs')
							)
						;
				}

				$item
					->add_style('btn')
					->add_style('padding', 'x2', 'y3')
					;

				if( isset($m['active']) && $m['active'] ){
					// $item->add_style('btn-primary');
					if( $colors['active'] ){
						$item->add_style('color', $colors['active']);
					}
					if( $colors['active-bg'] ){
						$item->add_style('bg-color', $colors['active-bg']);
					}
				}
			}

			$item->add_style('nowrap');
			$item = HC_Html_Factory::element('li')
				->add_child( $item )
				;


			if( isset($m['children']) && $m['children'] ){
				$item->add_attr('class', 'hcj-dropdown');
			}

			$nav->add_child( 
				$mk,
				$item
				);

		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$nav->add_attr( $k, $v );
		}

		return $nav->render();
	}
}