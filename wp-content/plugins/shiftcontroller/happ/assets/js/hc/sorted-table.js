jQuery(document).ready( function()
{

hc.SortedTable = {
	templates: {},
	Views: {},
	views: {},
	Models: {},
	models: {},
	Collections: {},
	collections: {},
};

hc.SortedTable.Models.Row = Backbone.Model.extend();
hc.SortedTable.Collections.Rows = Backbone.Collection.extend({
	model: hc.SortedTable.Models.Row,

	initialize: function(){
		this.sortby = ['hours', 'dsc'];
		},

	comparator: function(a, b)
	{
		var a = a.get(this.sortby[0]), b = b.get(this.sortby[0]);
		var ret = 0;

		if (a == b){
			ret = 0;
		}
		else {
			if( this.sortby[1] == "asc" ){
				ret = a < b ? -1 : 1;
			}
			else {
				ret = a > b ? -1 : 1;
			}
		}
		return ret;
	}
});

hc.SortedTable.Views.Table = Backbone.View.extend({
	initialize: function( options ){
		this.options = options || {};
		this.template = _.template( hc.SortedTable.templates.children('.hc-template-list').first().html() ),
		this.listenTo( this.collection, 'add change remove reset sort', this.render);
    },

	events: {
		'click .hc-sorter': 'sort'
	},

	sort: function( ev )
	{
		var new_sortby = jQuery(ev.target).data('sort');
		if( this.collection.sortby[0] == new_sortby ){
			this.collection.sortby[1] = (this.collection.sortby[1] == 'dsc') ? 'asc' : 'dsc';
		}
		else {
			this.collection.sortby = [new_sortby, 'dsc'];
		}
		this.collection.sort();
		return false;
	},

	render: function()
	{
		this.$el.empty();

		this.$el.html(
			this.template({
				'sortby'	: this.collection.sortby
				})
			);

		var header_container = this.$el.find('.hc-template-header-container');
		_.each( this.options.columns, function(model){
			var view = new hc.SortedTable.Views.HeaderCell({
				model			: model,
				current_sort	: this.collection.sortby,
			});
			header_container.append( view.render().$el );
		},this);

		var children_container = this.$el.find('tbody');

		this.collection.each( function(model){
			var view = new hc.SortedTable.Views.Row({
				model	: model,
				columns	: this.options.columns
			});
			children_container.append( view.render().$el );
		}, this);

		return this;
	}
});

hc.SortedTable.Views.Row = Backbone.View.extend({
	tagName: 'tr',

	initialize: function( options ){
		this.options = options || {};
		this.listenTo( this.model, 'change', this.render );
	},

	render: function()
	{
		this.$el.empty();
		_.each( this.options.columns, function(model){
			var cell_content = this.model.has(model.prop + '_view') ? this.model.get(model.prop + '_view') : this.model.get(model.prop);
			var view = new hc.SortedTable.Views.Cell({
				model : cell_content,
			});
			this.$el.append( view.render().$el );
		},this);
		return this;
	},
});

hc.SortedTable.Views.HeaderCell = Backbone.View.extend({
	initialize: function( options ){
		this.options = options || {};
		this.template = _.template( hc.SortedTable.templates.children('.hc-template-header-cell').first().html() );
	},

	render: function()
	{
		this.setElement( 
			jQuery(this.template({
				e				: this.model,
				current_sort	: this.options.current_sort,
			})
		));
		return this;
	},
});

hc.SortedTable.Views.Cell = Backbone.View.extend({
	initialize: function( options ){
		this.options = options || {};
		this.template = _.template( hc.SortedTable.templates.children('.hc-template-cell').first().html() );
	},

	render: function()
	{
		this.setElement( 
			jQuery(this.template({
				e: this.model,
			})
		));
		return this;
	},
});

});
