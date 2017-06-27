<?php
/* don't forget to enqueue underscore.js, backbone.js, hc/sorted-table.js */
class HC_Html_Widget_Sorted_Table extends HC_Html_Element
{
	protected $entries = array();
	protected $columns = array();
	protected $sortby = array();

	public function columns()
	{
		return $this->columns;
	}
	public function set_columns( $columns )
	{
		$this->columns = $columns;
		return $this;
	}

	public function sortby()
	{
		return $this->sortby;
	}
	public function set_sortby( $sortby )
	{
		$this->sortby = $sortby;
		return $this;
	}

	public function entries()
	{
		return $this->entries;
	}
	public function set_entries( $entries )
	{
		$this->entries = $entries;
		return $this;
	}

	public function render()
	{
		$out = HC_Html_Factory::widget('container');
		$template_container_id = 'hc' . HC_Lib::generate_rand();
		$template_container_id = 'hc_sorted_table';
		$out_container_id = 'hc' . HC_Lib::generate_rand();

		$templates = array();
		
		$td = HC_Html_Factory::element('td')
			->add_child(
				'<%= e %>'
				)
			;

		$children_attr = $this->children_attr();
		foreach( $children_attr as $k => $v ){
			$td->add_attr( $k, $v );
		}
		$children_styles = $this->children_styles();
		foreach( $children_styles as $k => $v ){
			$pass_arg = array_merge(array($k), $v);
			call_user_func_array( array($td, 'add_style'), $pass_arg );
		}

		$templates[] = HC_Html_Factory::element('script')
			->add_attr('type', 'text/template')
			->add_attr('class', 'template-cell')
			->add_child( $td )
			;

		$templates[] = HC_Html_Factory::element('script')
			->add_attr('type', 'text/template')
			->add_attr('class', 'template-header-cell')

			->add_child(
				HC_Html_Factory::element('td')
					->add_child(
						'<%
							if( e.sort ){
								print( \'<a href="#" class="hc-sorter" data-sort="\' + e.prop + \'">\' + e.label + \'</a>\' );
								if( current_sort[0] == e.prop ){
									if( current_sort[1] == "asc" ){
										print( \'' .  
										HC_Html::icon('sort-asc')
											->add_attr('class', 'hc-all-show')
										. '\');
									}
									else {
										print( \'' .  
										HC_Html::icon('sort-desc')
											->add_attr('class', 'hc-all-show')
										. '\');
									}
								}
							}
							else {
								print( e.label );
							}
						%>'
						)
				)
			;

		$templates[] = HC_Html_Factory::element('script')
			->add_attr('type', 'text/template')
			->add_attr('class', 'template-list')
			->add_child(
				HC_Html_Factory::element('table')
					->add_style('table', 'border')
					->add_attr('style', 'table-layout: fixed;')

					->add_child(
						HC_Html_Factory::element('tr')
							->add_attr('class', 'hc-template-header-container')
						)
				)
			;

		$template_container = HC_Html_Factory::element('div')
			->add_attr('id', $template_container_id )
			->add_child( $templates )
			;

		$sortby = $this->sortby();
		if( ! $sortby ){
			foreach( $this->columns() as $col ){
				if( isset($col['sort']) && $col['sort'] ){
					$sortby = array($col['label'], 'dsc');
					break;
				}
			}
		}
		if( ! $sortby ){
			$sortby = array('id', 'dsc');
		}

		$entries = json_encode( $this->entries() );
		$columns = json_encode( $this->columns() );
		$sortby = json_encode( $sortby );

		$js = <<<EOT

<script language="JavaScript">
jQuery(document).ready( function()
{
	hc.SortedTable.templates = jQuery('#$template_container_id');

	var hc_SortedTable_entries = new hc.SortedTable.Collections.Rows;
	hc_SortedTable_entries.sortby = $sortby;
	hc_SortedTable_entries.reset( $entries );

	var view = new hc.SortedTable.Views.Table({
		collection: hc_SortedTable_entries,
		columns: $columns,
	});

	jQuery("#$out_container_id").empty().append( view.render().\$el );
});
</script>

EOT;

		$out_container = HC_Html_Factory::element('div')
			->add_attr('id', $out_container_id )
			;

		$out->add_child( $template_container );
		$out->add_child( $out_container );
		$out->add_child( $js );

		return $out->render();
	}
}
?>