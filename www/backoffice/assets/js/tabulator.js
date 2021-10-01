var Tabulator = new Class({
	implements: [Options],
	options: {},
	initialize: function(el, options) {
		this.container = el;
		
		this.header = el.getElement('.tabs-header');
		var items = this.header.getElements('li')
		for(var i = 0; i < items.length ; i++) { 
			var li = items[i];
			li.addEvent('click', this.onTabSelect.bind(this));
		}
		
		this.tabs = el.getElements('.tab-container');
		this.tabs[0].show();
		
	},	
	onTabSelect: function(event) { 
		var target = event.target.nodeName == 'LI' ? event.target: event.target.getParent('LI');
		
		var items = this.header.getElements('li')
		
		for(var i = 0; i < items.length ; i++) { 
			var li = items[i];
			
			if(li.hasClass('selected')) {
				li.removeClass('selected');
				this.tabs[i].hide();
			}
			
			target.addClass('selected');
			this.tabs[target.getProperty('data-index')].show();
		}
	}
});