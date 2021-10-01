/*
    Copyright 2021 European Commission
    
    Licensed under the EUPL, Version 1.2 only (the "Licence");
    You may not use this work except in compliance with the Licence.
    
    You may obtain a copy of the Licence at:
        https://joinup.ec.europa.eu/software/page/eupl5
    
    Unless required by applicable law or agreed to in writing, software 
    distributed under the Licence is distributed on an "AS IS" basis, 
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either 
    express or implied.
    
    See the Licence for the specific language governing permissions 
    and limitations under the Licence.
*/
var DataHub = function(id, options) { 
	
	var self  = this;
	var now = Date.now();

	this.options = { 
		host: '',
		themes: [],
		services: [],
		urlJSDataCatalog: '/json/list-*.json?nocache=' + now,
		urlJSBeneficiarySheet: '/json/beneficiaries/beneficiary-*.json?nocache=' + now,
		urlJSProjectSheet: '/json/projects/project-*.json?nocache=' + now,
		urlJSFilters: '/json/filters.json?nocache=' + now,
		urlDisclaimer: '/json/disclaimer.json?nocache=' + now,
		partner: 0,
		project: 0,
	};
	
	this.plugins = {};
	this.activePlugin = null;
	this.screenOrientation = 'landscape';
	this.countryCombo = null;
	this.orgCombo = null;

	this.data = [];
	this.results = [];
	this.filters = {
		fullText: '',
		topics: [],
		selExclusiveFilter: 'country',
		countries: [],
		orgs: [],
		regions: [],
		phases: [],
		roles: [],
		cutoffs: [],
		from: '',
		to: '',
		budgetMin: 0,
		budgetMax: 0, 
		project: 0,
		beneficiary: 0,
	};
	this.dom = {
		container: document.getElementById(id)
	};
	
	this.initialize = function(options) {
		if(options.hasOwnProperty('config')) { 
			for(var key in options.config) {
				
				if(this.options.hasOwnProperty(key)) {
					
					this.options[key] = options.config[key];
				}
			}
		}
		if(options.hasOwnProperty('filters')) { 
			for(var param in options.filters) {
				this.filters[param] = options.filters[param];
				if (param === 'countries' && options.filters[param].length > 0){
					this.filters.selExclusiveFilter = 'country'
				}
				if (param === 'orgs' && options.filters[param].length > 0){
					this.filters.selExclusiveFilter = 'organisation'
				}
			}
		}

		if(options.hasOwnProperty('registerServices') && Array.isArray(options.registerServices)) { 
			this.options.services = options.registerServices;
			document.addEventListener('serviceLoaded', this.onReadyService.bind(this));
		}
		// building dom
		this.dom.filterControl = Element('DIV', {id: 'filter-control'});
		var div = Element('div', {id: 'button-expand'});
		
		var a  = Element('a', {title: 'Filters', href: '#', class: 'button filter'});
		a.appendChild(Element('span', {text:'Filter'}));
		a.addEventListener('click', this.toggleFilters.bind(this));
		div.appendChild(a);
		div.appendChild(Element('div', {id: 'plugin-control'}));
		var btEmbed = new Element('a', {id: 'embed-control', title: 'Embed this app to your website'});
		btEmbed.addEventListener('click', this.toggleEmbed.bind(this)); 
		div.appendChild(btEmbed);
		
		var lastChar = this.options.host.charAt(this.options.host.length -1)
		var downUrl = this.options.host
		if (lastChar !== '/'){
			downUrl += '/';
		}
		div.appendChild(Element('a', {id: 'help-button', title: 'Help', href: downUrl + 'src/docs/user_manual.pdf', download: 'user_manual'}));
		this.dom.filterControl.appendChild(div);
		
		this.dom.filterContent = Element('div', {id: 'filters-content'});
		var el = new Element('input', {type: 'text', id: 'lookup'});
		el.addEventListener('keypress', this.onChangeLookup.bind(this));
		this.dom.filterContent.appendChild(el);
		// var el = new Element('div', {id: 'clear-container'});
		// var a = new Element('a', {href: '#', id: 'button-select-all', title: 'Select all filters', text: 'Select all'});
		// var b = new Element('a', {href: '#', id: 'button-clear', title: 'Clear all filters', text: 'Clear all'});
		// el.appendChild(a);
		// el.appendChild(b);
		this.dom.filterContent.appendChild(el);
		this.dom.filterList = new Element('div', {id: 'filters-list' });
		this.dom.filterContent.appendChild(this.dom.filterList);
		this.dom.filterControl.appendChild(this.dom.filterContent);
		
		// embed window
		this.dom.embedContent = Element('div', {id: 'embed-content'});
		this.dom.filterControl.appendChild(this.dom.embedContent);
		this.dom.embedContent.appendChild(new Element('label', {text: 'Copy and paste this text in the source code of your page:'}));
		this.dom.embedText = new Element('textarea',{id: 'embed-text'});
		this.dom.embedContent.appendChild(this.dom.embedText);
		var ul = new Element('ul');
		this.dom.embedContent.appendChild(ul);
		var li = new Element('li');
		ul.appendChild(li);
		var ul2 = new Element('ul');
		li.appendChild(ul2);
		var li2 = new Element('li');
		li2.appendChild(new Element('input', {type: 'checkbox', id: 'embed-selection', checked: 'true'}));
		li2.appendChild(new Element('label',{text: 'Use current search criteria', class: 'checked'}));
		ul2.appendChild(li2);
		var li2 = new Element('li');
		li2.appendChild(new Element('input', {type: 'checkbox', id: 'embed-iframe', checked: 'true'}));
		li2.appendChild(new Element('label',{text: 'Embed as iFrame', class: 'checked'}));
		ul2.appendChild(li2);
		var li2 = new Element('li');
		li2.appendChild(new Element('label',{text: 'Size', class: 'checked'}));
		var span = new Element('span',{class: 'right'});
		span.appendChild(new Element('input', {type: 'number', id: 'iframe-w'}));
		span.appendChild(new Element('label',{text: 'x&nbsp;', class: 'checked'}));
		span.appendChild(new Element('input', {type: 'number', id: 'iframe-h'}));
		li2.appendChild(span);
		ul2.appendChild(li2);
		
		var ul = new Element('ul');
		this.dom.embedContent.appendChild(ul);
		var li = new Element('li');
		ul.appendChild(li);
		li.appendChild(new Element('h2', {text: 'Include modules'}));
		var ul2 = new Element('ul');
		li.appendChild(ul2);
		var li2 = new Element('li');
		li2.appendChild(new Element('input', {type: 'checkbox', class: 'embed-module', value: 1, checked: 'true'}));
		li2.appendChild(new Element('label',{text: 'Map', class: 'checked'}));
		ul2.appendChild(li2);
		var li2 = new Element('li');
		li2.appendChild(new Element('input', {type: 'checkbox', class: 'embed-module', value: 2, checked: 'true'}));
		li2.appendChild(new Element('label',{text: 'Lists', class: 'checked'}));
		ul2.appendChild(li2);
		var li2 = new Element('li');
		li2.appendChild(new Element('input', {type: 'checkbox', class: 'embed-module', value: 4, checked: 'true'}));
		li2.appendChild(new Element('label',{text: 'Charts', class: 'checked'}));
		ul2.appendChild(li2);
		/*var li2 = new Element('li');
		li2.appendChild(new Element('label',{text: 'Show'}));
		var charts = new Element('select', {id: 'embed-chart'});
		li2.appendChild(charts);
		ul2.appendChild(li2);*/
		/*charts.appendChild(new Element('option', {value: '', text: 'Dashboard'}));
		charts.appendChild(new Element('option', {value: 1, text: 'Number of participants'}));
		charts.appendChild(new Element('option', {value: 3, text: 'Number of projects funded'}));
		charts.appendChild(new Element('option', {value: 2, text: 'Money allocated per country'}));
		charts.appendChild(new Element('option', {value: 5, text: 'Money allocated per topics'}));
		charts.appendChild(new Element('option', {value: 4, text: 'Budgets: Topics per country'}));*/
		
		
		var li = new Element('li');
		ul.appendChild(li);
		li.appendChild(new Element('h2', {text: 'Theme'}));
		var ul2 = new Element('ul');
		li.appendChild(ul2);
		var li2 = new Element('li');
		var themes = new Element('select', {id: 'embed-theme'});
		li2.appendChild(themes);
		themes.appendChild(new Element('option', {value: '', text: ''}))
		for(var i = 0 ; i < this.options.themes.length; i++) { 
			themes.appendChild(new Element('option', {value: this.options.themes[i], text: this.options.themes[i]}));
		}
		ul2.appendChild(li2);
		
		this.dom.container.appendChild(this.dom.filterControl);
		
		this.dom.plugins = new Element('div', {id: 'plugins'});
		this.dom.container.appendChild(this.dom.plugins);
		this.dom.sheet = new Element('div', {id: 'marker-sheet'});
		this.dom.sheetContent = new Element('div', {id: 'marker-sheet-content'});
		var sheetButton = new Element('a', {href:'#', class: 'close-button'});
		sheetButton.addEventListener('click', this.closeSheet.bind(this));
		this.dom.sheet.appendChild(sheetButton);
		this.dom.sheet.appendChild(this.dom.sheetContent);
		this.dom.container.appendChild(this.dom.sheet);
		this.closeSheet();
		this.dom.loader = new Element('div', {class: 'loader', text: 'loading'});
		this.dom.container.appendChild(this.dom.loader);
		
		this.loadFilters();
			
		if(this.isMobileDevice()) { 
			window.addEventListener('orientationchange', this.onOrientationChange);
		}
		
		this.onOrientationChange();
	};
	
	this.isMobileDevice = function() { return (typeof window.orientation != "undefined") || (navigator.userAgent.indexOf('IEMobile') != -1); };
	
	this.onOrientationChange = function() {
		
		if(this.isMobileDevice()) {		
			switch(window.orientation) {  
			  case -90:
			  case  90:
				this.screenOrientation = 'landscape';
				break; 
			  default:
				this.screenOrientation = 'portrait';
				break; 
			}
			document.body.className = 'mobile ' + this.screenOrientation;
		} 
		
		this.setOrientation();
	}
	
	this.setOrientation  = function() { 
		
		var self = this;
		
		for(var id in this.plugins) { 
			
			if(this.screenOrientation == 'landscape') {
				this.plugins[id].object.options.useTooltip = false;
			
			} else { 
				this.plugins[id].object.options.useTooltip = true;
			}
		}
		
		
	};

	this.appendPluginBtn = function(type, btn){
		var container = $('plugin-control');
		var order = {
			map: 0,
			list: 1,
			charts: 2
		};

		if (order[type] > container.children.length - 1){
			container.appendChild(btn);
		} else {
			container.insertBefore(btn, container.children[order[type]]);
		}
	};
	
	this.onReadyService = function(event) { 
		var index = -1;
		
		for(var i = 0; i < self.options.services.length; i++) { 
			if(this.options.services[i].id == event.detail.service) {
				index = i;
				break;
			}
		}

		if(index > -1) { 
			var controls = document.getElementById('plugin-control');

			var button = document.createElement('A');
			button.href = '#';
			button.className = 'button ' + event.detail.service;
			
			/*$('plugin-control').appendChild(button);*/
			this.appendPluginBtn(event.detail.service, button)
			button.addEventListener('click', this.onSelectPlugin);
			var container = Element('div', {id: event.detail.service});
			this.dom.plugins.appendChild(container)
			this.plugins[event.detail.service] = {
				activator: button,
				object: new event.detail.object(container, this.options.services[index].options),
			}
			if(this.screenOrientation == 'landscape') {
				this.plugins[event.detail.service].object.options.useTooltip = false;
			
			} else { 
				this.plugins[event.detail.service].object.options.useTooltip = true;
			}
			container.addEventListener('displayParticipant', this._beneficiaryHandler.bind(this));
			container.addEventListener("displayProject", this._projectHandler.bind(this));
			this.options.services.splice(index,1);
			// if all services loaded, start app
			if(this.options.services.length == 0) this.load();
		}
	};
	
	this.loadFilters = function() { 
		var self = this;
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				self.dom.filterList.innerHTML = xmlhttp.responseText;
				self.linkFilters();
			}
		};
		
		xmlhttp.open("GET", this.options.urlJSFilters, true);
		xmlhttp.send();
	};

	this.validateCbSelection = function(cssClass){
		var checkboxs=document.getElementsByClassName(cssClass);
		var okay=false;
		for(var i=0,l=checkboxs.length;i<l;i++)
		{
			if(checkboxs[i].checked)
			{
				okay=true;
				break;
			}
		}
		return okay;
	}

	this.toggleExclusiveFilter = function(target, fromLoad){
		var orgVis = target === 'organisation' ?  true : false;
		var orgEl = document.getElementsByClassName('filter-organisation')[0];
		var countryEl = document.getElementsByClassName('filter-country')[0];

		if (orgVis){
			countryEl.style.display = 'none';
			orgEl.style.display = 'block';
			this.filters.selExclusiveFilter = 'organisation';
			if (fromLoad === undefined || fromLoad === null || fromLoad === false){
				this.orgCombo.setValue('all')
			}
		} else {
			orgEl.style.display = 'none';
			countryEl.style.display = 'block';
			this.filters.selExclusiveFilter = 'country';
			if (fromLoad === undefined || fromLoad === null || fromLoad === false){
				this.countryCombo.setValue('all');
			}
		}
	}

	this.registerOrgCombo = function(){
		var self = this;
		this.orgCombo = new vanillaSelectBox('.org-combo', {"maxHeight": 300, "search": true});

		document.getElementById('org-btn-select-all').addEventListener('click', function(){
			self.orgCombo.setValue('all');
		});

        document.getElementById('org-btn-clear').addEventListener('click', function(){
			self.orgCombo.empty();
        });
	}

	this.registerCountryCombo = function(){
		var self = this;
		this.countryCombo = new vanillaSelectBox('.country-combo', {"maxHeight": 300, "search": true});

		document.getElementById('country-btn-select-all').addEventListener('click', function(){
			self.countryCombo.setValue('all');
		});

		document.getElementById('country-btn-clear').addEventListener('click', function(){
			self.countryCombo.empty();
		});
	}

	this.registerCheckBoxSelectClear = function(){
		document.getElementById('themes-btn-select-all').addEventListener('click', function(){
			self.toggleFiltersFromButton(true, 'phase');
		});

		document.getElementById('themes-btn-clear').addEventListener('click', function(){
			self.toggleFiltersFromButton(false, 'phase');
		});

		document.getElementById('year-btn-select-all').addEventListener('click', function(){
			self.toggleFiltersFromButton(true, 'year');
		});

		document.getElementById('year-btn-clear').addEventListener('click', function(){
			self.toggleFiltersFromButton(false, 'year');
		});
	}

	
	this.linkFilters = function() {
		var self = this;
		// clear button 
		// var clear = document.getElementById('button-clear').addEventListener('click', this.clearFilters.bind(this));
		// var select = document.getElementById('button-select-all').addEventListener('click', this.selectAllFilters.bind(this));

		// radio button behaviour
		var radios = document.getElementsByName('exclusive-filter');
		if (radios.length > 0){
			this.registerCountryCombo();
			this.registerOrgCombo();

			for (var i = 0; i < radios.length; i++){
				radios[i].addEventListener('click', function(e){
					self.toggleExclusiveFilter(this.value, false);
				});
			}
			this.countryCombo.setValue('all');
		}

		this.registerCheckBoxSelectClear();
		
		// checkboxes behaviour
		var checkboxes = this.dom.filterList.getElementsByTagName('LABEL');
		for(var i=0; i < checkboxes.length; i++) { 
			var label = checkboxes[i];

			var inputs = label.parentElement.getElementsByTagName('INPUT');

			if(inputs.length > 0 && inputs[0].type == 'checkbox') {
				
				label.addEventListener('click', function(e) { 
					var input =  e.target.parentElement.getElementsByTagName('INPUT')[0];
					
					input.checked = !input.checked;
					var proceed = true
					/*if (input.className === 'country-input'){
						proceed = self.validateCbSelection('country-input');
					}*/

					/*if (input.className === 'cutoff-input'){
						proceed = self.validateCbSelection('cutoff-input');
					}*/

					if (!proceed){
						input.checked = !input.checked;
					} else {
						e.target.className = input.checked ? 'checked': '';

						if(hasClass(input, 'country-input')) {
							if(input.checked) {
								var regions = input.parentElement.getElementsByClassName('nuts-input');
								for(var r=0; r < regions.length; r++) {
									regions[r].checked = false;
									regions[r].parentElement.getElementsByTagName('LABEL')[0].className = '';
								}
							}
						}

						if(hasClass(input, 'nuts-input')) {
							var country = input.parentElement.parentElement.parentElement.getElementsByClassName('country-input');
							country[0].checked = false;
							country[0].parentElement.getElementsByTagName('LABEL')[0].className = '';
						}

						self.refresh();

						if(self.screenOrientation == 'portrait') {
							self.toggleFilters(e);
						}
					}
				});
			}
		}
		
		// embed checkboxes behaviour
		var checkboxes = this.dom.embedContent.getElementsByTagName('LABEL');
		for(var i=0; i < checkboxes.length; i++) { 
			var label = checkboxes[i];
			
			var inputs = label.parentElement.getElementsByTagName('INPUT');
			
			if(inputs.length > 0 && inputs[0].type == 'checkbox') {
				
				label.addEventListener('click', function(e) { 
					var input =  e.target.parentElement.getElementsByTagName('INPUT')[0];
					
					input.checked = !input.checked;
					
					e.target.className = input.checked ? 'checked': '';
					
					self.updateEmbedUrl();
					
				});
			}
		}
		
		// inputs behaviour
		var inputs = this.dom.filterList.getElementsByTagName('INPUT');
		
		for(var i=0; i < inputs.length; i++) { 
			var input = inputs[i];
			if(input.type == 'checkbox') { 
				input.addEventListener('change', function(e) { 
					var button = e.target;
					
					
					var spans = button.parentElement.parentElement.parentElement.getElementsByTagName('SPAN')
					if(spans) { 
						var total = spans[0]; 
						if(total) {
							var value = parseInt(total.innerHTML);
							if(button.checked) 
								value++;
							else
								value--;
							total.innerHTML = value;
						}
						update_embed_link();
					}
					
					if(self.screenOrientation == 'portrait') {
						self.toggleFilters(e);
					}
					
					db.refresh();
				});
			}
			
			if(input.type == 'range') { 
				input.addEventListener('change', function(e) { 
					var button = e.target;
					var value = button.parentElement.getElementsByTagName('SPAN')[0];
					
					if(button.id == "budget_min") { 
						if(Number(button.value) > Number(document.getElementById("budget_max").value)) button.value = Number(document.getElementById("budget_max").value);
						value.innerHTML = Number(button.value).toLocaleString("de-DE", {style: "currency", currency: "EUR"});
					};
					if(button.id == "budget_max") {
						if(Number(button.value) < Number(document.getElementById("budget_min").value)) button.value = Number(document.getElementById("budget_min").value);
						value.innerHTML = Number(button.value).toLocaleString("de-DE", {style: "currency", currency: "EUR"});
					};
					
					if(self.screenOrientation == 'portrait') {
						self.toggleFilters(e);
					}
					
					self.refresh();
				});
			}
		}
		
		// selects behaviour
		var selects = this.dom.filterList.getElementsByTagName('SELECT');
		for(var i=0; i < selects.length; i++) { 
			selects[i].addEventListener('change', function(e) { 
				self.refresh(); 
				if(self.screenOrientation == 'portrait') {
					self.toggleFilters(e);
				}
			}); 
		}
		
		// embed selects behaviour
		var selects = this.dom.embedContent.getElementsByTagName('SELECT');
		for(var i=0; i < selects.length; i++) { 
			selects[i].addEventListener('change', this.updateEmbedUrl.bind(this)); 
		}
		
		// input selects behaviour
		var inputs = this.dom.embedContent.getElementsByTagName('INPUT');
		for(var i=0; i < inputs.length; i++) { 
			if(inputs[i].type == 'number')
				inputs[i].addEventListener('change', this.updateEmbedUrl.bind(this)); 
		}
		
		// collapsers
		var expanders = this.dom.filterList.getElementsByClassName('expander');
			
		for(var i = 0; i < expanders.length; i++) {
			var button = expanders[i];
			if(button.parentElement.nodeName == 'H2') {
				var container = button.parentElement.parentElement;
			} else {
				var container = button.parentElement;
			}
			var ul = container.getElementsByTagName('UL')[0];
			button.addEventListener('click', this.onToggleExpander.bind(this)); 
			ul.style.display = 'none';
		}	
		// set up passed filter values
		if(this.filters.fullText != '') { 
			document.getElementById('lookup').value = this.filters.fullText;
		}
		
		if(this.filters.budgetMin > 0) {
			document.getElementById('budget_min').value = this.filters.budgetMin;
			document.getElementById('budget_min').parentElement.getElementsByTagName('span')[0].innerHTML = Number(this.filters.budgetMin).toLocaleString("de-DE", {style: "currency", currency: "EUR"});
		}
		
		if(this.filters.budgetMax > 0) {
			document.getElementById('budget_max').value = this.filters.budgetMax;
			document.getElementById('budget_max').parentElement.getElementsByTagName('span')[0].innerHTML = Number(this.filters.budgetMax).toLocaleString("de-DE", {style: "currency", currency: "EUR"});
		}
		
		if(this.filters.from) document.getElementById('from_date').value = this.filters.from;
		
		if(this.filters.to) document.getElementById('to_date').value = this.filters.to;
		
		if(this.filters.phases.length > 0) { 
			for(var i=0; i  < this.dom.filterContent.getElementsByClassName('phase-input').length; i ++) { 
				var input = this.dom.filterContent.getElementsByClassName('phase-input')[i];
				if(this.filters.phases.indexOf(input.value) === -1) {
					input.checked = false;
					input.parentElement.getElementsByTagName('label')[0].className = '';
				}
			}
		}
		// if(this.filters.countries.length > 0) {
		// 	this.filters.selExclusiveFilter = 'country';
		// 	this.toggleExclusiveFilter('country', true);
		// 	this.countryCombo.setValue(this.filters.countries);
		// }
		// if(this.filters.orgs.length > 0) {
		// 	this.filters.selExclusiveFilter = 'organisation';
		// 	this.toggleExclusiveFilter('organisation', true);
		// 	this.orgCombo.setValue(this.filters.orgs);
		// }
		if(this.filters.regions.length > 0) {
			for(var i=0; i  < this.dom.filterContent.getElementsByClassName('nuts-input').length; i ++) { 
				var input = this.dom.filterContent.getElementsByClassName('nuts-input')[i];
				if(this.filters.regions.indexOf(input.value) === -1) {
					input.checked = false;
					input.parentElement.getElementsByTagName('label')[0].className = '';
				}
			} 
		}
		if(this.filters.roles.length > 0) {
			for(var i=0; i  < this.dom.filterContent.getElementsByClassName('role-input').length; i ++) { 
				var input = this.dom.filterContent.getElementsByClassName('role-input')[i];
				if(this.filters.roles.indexOf(input.value) === -1) {
					input.checked = true;
					input.parentElement.getElementsByTagName('label')[0].className = '';
				}
			} 
		}
		
		if(this.filters.cutoffs.length > 0) {
			for(var i=0; i  < this.dom.filterContent.getElementsByClassName('cutoff-input').length; i ++) { 
				var input = this.dom.filterContent.getElementsByClassName('cutoff-input')[i];
				if(this.filters.cutoffs.indexOf(input.value) === -1) {
					input.checked = false;
					input.parentElement.getElementsByTagName('label')[0].className = '';
				}
			} 
		}
		
		if(this.filters.topics.length > 0) { 
			for(var i=0; i  < this.dom.filterContent.getElementsByClassName('topic-input').length; i ++) { 
				
				for(var j=0; j < this.filters.topics.length; j++) {
					var topic = this.filters.topics[j];
					var input = this.dom.filterContent.getElementsByClassName('topic-input')[i];
					if(input.value.search(topic) === -1) {
						input.checked = false;
						input.parentElement.getElementsByTagName('label')[0].className = '';
					}
				}
			}
		}
		if(this.filters.countries.length > 0) {
			this.filters.selExclusiveFilter = 'country';
			this.toggleExclusiveFilter('country', true);
			this.countryCombo.setValue(this.filters.countries);
		}
		if(this.filters.orgs.length > 0) {
			this.filters.selExclusiveFilter = 'organisation';
			this.toggleExclusiveFilter('organisation', true);
			this.orgCombo.setValue(this.filters.orgs);
		}

	};
	
	this.onChangeLookup = function(e) {
		if (e.keyCode == 13) {
			this.refresh();
			if(this.screenOrientation == 'portrait') {
				this.toggleFilters(e);
			}
		}
	};


	// this.selectAllFilters = function(event){
	// 	this.toggleFiltersFromButton(true);
	// };
	//
	// this.clearFilters = function(event){
	// 	this.toggleFiltersFromButton(false);
	// }

	this.toggleFiltersFromButton = function(selected, name) {
		
		// var selects = this.dom.filterList.getElementsByTagName('SELECT');
		//
		// for(var i=0; i < selects.length; i++) {
		// 	selects[i].selectedIndex = 0;
		// }
		 
		var inputs = this.dom.filterList.getElementsByTagName('INPUT');
		
		for(var i=0; i < inputs.length; i++) { 
			var input = inputs[i];
			if (input.name === name){
				if(input.type == 'checkbox') {
					input.checked = selected;
					if(input.parentElement.parentElement.getElementsByTagName('SPAN').length > 0)
						input.parentElement.parentElement.getElementsByTagName('SPAN')[0].innerHTML = 0;
					var labels = input.parentElement.getElementsByTagName('LABEL');
					if(labels.length > 0 ) {
						if (selected) {
							labels[0].className = 'checked';
						} else {
							labels[0].className = '';
						}
					}
				}
				if(input.type == 'range') {
					if(input.id == "budget_min") {
						input.value = input.min;
					};
					if(input.id == "budget_max") {
						input.value = input.max;
					};
				}
			}
		}
		
		document.getElementById('lookup').value = '';
		
		this.refresh();
		if(this.screenOrientation == 'portrait') {
			this.toggleFilters();
		}
	
	}
	
	this.load = function() { 
		this.query();
		this.activatePlugin('map');
	};
	
	this.query = function() { 
		this._queue = [];
		this._queue.push(this.options.urlJSDataCatalog.replace('*', 1));
		this.processQueue();
	};
	
	this.processQueue = function() { 
		while(this._queue.length > 0) {
			this._fetch(this._queue.shift()); 
		}
	};
	
	this._fetch = function(src) { 
		
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var data = JSON.parse(xmlhttp.responseText);
				self.data = self.data.concat(data.items)
				if(data.linkage != '') {
					if (data.linkage.indexOf('nocache') === -1){
						data.linkage += '?nocache=' + now;
					}
					self._queue.push(data.linkage);
				} else { 
					self.lastUpdate = data.created;
					self.dom.loader.style.display = 'none';
					// self.search();
					self.refresh();
				}
			}
			
			self.processQueue();
		};
		
		xmlhttp.open("GET", src, true);
		xmlhttp.send();
	};
	
	this.search = function( ) { 
		
		this.results = [];
		
		for(var i=0; i < this.data.length; i++) { 
			var item = JSON.parse(JSON.stringify(this.data[i]));
			var valid = true;

			if (this.filters.selExclusiveFilter === 'country'){
				if(valid && this.filters.countries.length > 0) {
					if (this.filters.countries.indexOf(item.country) === -1){
						valid = false
					}
				} else {
					valid = false;
				}
			} else {
				if (valid && this.filters.orgs.length > 0){
					if (this.filters.orgs.indexOf(item.id) === -1){
						valid = false;
					}
				} else {
					valid = false;
				}
			}

						
			if(valid && this.filters.phases.length > 0 && (this.filters.countries.length > 0 || this.filters.orgs.length > 0) && this.filters.cutoffs.length > 0) {
				
				var found = false;
				
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					
					if(this.filters.phases.indexOf(project.theme) != -1) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p, 1);
					}
				}
				
				valid = valid && found;
			} else {
				item.projects = [];
			}

			/*if (valid && this.filters.countries.length > 0 && item.projects.length > 0){
				var found = false;

				p = 0;
				while(p < item.projects.length) {
					var project = item.projects[p];

					if(this.filters.cutoffs.indexOf(project.year) != -1) {
						found = true;
						p++;
					}
					else {
						item.projects.splice(p, 1);
					}
				}

				valid = valid && found;
			}*/

			if(valid && this.filters.cutoffs.length > 0 && item.projects.length > 0) {
				
				var found = false;
				
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					
					if(this.filters.cutoffs.indexOf(project.year) != -1) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p, 1);
					}
				}
				
				valid = valid && found;
			}
			
			
			
			if(valid && this.filters.fullText != '') { 
				var needle = new RegExp(this.filters.fullText,'i');
				
				var base_name = item.name;
				
				base_name = base_name.replace(/[áàãâä]/gi,"a")
				.replace(/[éè¨ê]/gi,"e")
				.replace(/[íìïî]/gi,"i")
				.replace(/[óòöôõ]/gi,"o")
				.replace(/[úùüû]/gi, "u")
				.replace(/[ç]/gi, "c")
				.replace(/[ñ]/gi, "n")
				.replace(/[^a-zA-Z0-9]/g," ");
				
				var found = base_name.search(needle) != -1 || item.id.search(needle) != -1;
				
				if(!found) {
					p = 0;
					while(p < item.projects.length) { 
						var project = item.projects[p];
						
						if(project.id.search(needle) != -1 || project.title.search(needle) != -1  || project.abstract.search(needle) != -1 || project.abstract.toUpperCase() == this.filters.fullText.toUpperCase() || project.title.toUpperCase() == this.filters.fullText.toUpperCase()) {
							found = true;
							p++;
						}
						else { 
							item.projects.splice(p,1);
						}
					}
				}	
				valid = valid && found;
			}	
			if(valid ) {
				this.results.push(item);
			}
		}
		
		/*
		if(this.results.length == 1) { 
			this._showBeneficiary(this.results[0].id);	 
		}
		*/
		
		if(this.options.partner != 0) { 
			this._showProject(this.options.partner);	 
		}
		
		if(self.activePlugin) self.activePlugin.object.refresh(self.results, self.filters, true);
		
		this.updateEmbedUrl();
	};
	
	
	this.refresh = function() { 
		
		if(!this._busy) {
			var selExclusiveFilter = this.filters.selExclusiveFilter;
			
			this.filters = {
				fullText: '',
				topics: [],
				selExclusiveFilter: '',
				countries: [],
				orgs: [],
				regions: [],
				phases: [],
				roles: [],
				cutoffs: [],
				from: '',
				to: '',
				budgetMin: 0,
				budgetMax: 0	
			};

			var radios = document.getElementsByName('exclusive-filter');
			for (var i = 0; i < radios.length; i++){
				if (radios[i].value === selExclusiveFilter){
					radios[i].checked = true;
					this.filters.selExclusiveFilter = radios[i].value;
				} else {
					radios[i].checked = false;
				}
			}
			
			if(document.getElementById('lookup')) { 
				this.filters.fullText = document.getElementById('lookup').value;
			}

			if (this.filters.selExclusiveFilter === 'country'){
				var self = this;
				var collection = document.querySelectorAll('.country-combo option');
				for (var i = 0; i < collection.length; i++){
					if (collection[i].selected){
						self.filters.countries.push(collection[i].value);
					}
				}
			} else {
				var self = this;
				var collection = document.querySelectorAll('.org-combo option');
				for (var i = 0; i < collection.length; i++){
					if (collection[i].selected){
						self.filters.orgs.push(collection[i].value);
					}
				}
			}

			
			var elements = this.dom.filterList.getElementsByClassName('nuts-input');
			for(var i=0; i < elements.length; i++) { 
				var field = elements[i];
				if(field.checked) this.filters.regions.push(field.value);
			}
			
			var elements = this.dom.filterList.getElementsByClassName('topic-input');
			for(var i=0; i < elements.length; i++) { 
				var field = elements[i];
				if(field.checked) this.filters.topics.push(field.value);
			}
			
			var elements = this.dom.filterList.getElementsByClassName('phase-input');
			for(var i=0; i < elements.length; i++) { 
				var field = elements[i];
				if(field.checked) this.filters.phases.push(field.value);
			}
			
			var elements = this.dom.filterList.getElementsByClassName('role-input');
			for(var i=0; i < elements.length; i++) { 
				var field = elements[i];
				if(field.checked) this.filters.roles.push(field.value);
			}
			
			var elements = this.dom.filterList.getElementsByClassName('cutoff-input');
			for(var i=0; i < elements.length; i++) { 
				var field = elements[i];
				if(field.checked) this.filters.cutoffs.push(field.value);
			}
			
			
			if(document.getElementById('budget_min')) { 
				this.filters.budgetMin = Number(document.getElementById('budget_min').value) > Number(document.getElementById('budget_min').min) ? Number(document.getElementById('budget_min').value): 0;
			}
			
			if(document.getElementById('budget_max')) { 
				this.filters.budgetMax = Math.ceil(Number(document.getElementById('budget_max').value)) < Number(document.getElementById('budget_max').max) ? Number(document.getElementById('budget_max').value): 0;
			}
			
			if(document.getElementById('from_date')) { 
				this.filters.from = document.getElementById('from_date').value
			}
			
			if(document.getElementById('to_date')) { 
				this.filters.to = document.getElementById('to_date').value
			}
			
			this.search();
		}
	};
	
	this.hide = function(options) { 
		
		this.opened = false;
		
		this.dom.filterControl.className = '';
		
		if(this.screenOrientation == 'landscape') {
			this.dom.filterControl.style.left = '-450px'; 
			
		} else { 
			this.dom.filterControl.style.top = '90%'; 			
			
		}
	};
	
	this.toggleFilters = function(event) { 
		if(event) event.stop();
		
		if(!this.opened || this.dom.embedContent.style.display == 'block') { 
			
			this.opened = true;
			
			this.dom.filterContent.style.display = 'block';
			this.dom.embedContent.style.display = 'none';
			this.dom.filterControl.className = 'opened';
			
			if(this.screenOrientation == 'landscape') {
				self.dom.filterControl.style.left = 0;		
			} else { 
				this.dom.filterControl.style.top = '8%'; 
			}
			
		} else { 
			this.hide();
		}
	};
	
	this.toggleEmbed = function(event) { 
		event.stop();
		if(!this.opened || this.dom.filterContent.style.display == 'block') { 
			this.opened = true;
			this.dom.filterContent.style.display = 'none';
			this.dom.embedContent.style.display = 'block';
			this.dom.filterControl.className = 'opened';
			
			if(this.screenOrientation == 'landscape') {
				self.dom.filterControl.style.left = 0;
			} else { 
				this.dom.filterControl.style.top = '8%'; 
			}
		} else { 
			this.hide();
		}
		
	}
	
	this.activatePlugin = function(id) {
		if (id.indexOf('selected') === -1){
			if(this.activePlugin) {
				this.activePlugin.activator.className = this.activePlugin.activator.className.replace(' selected', '');
				this.activePlugin.object.hide();
			}

			this.activePlugin = this.plugins[id];
			this.activePlugin.activator.className += ' selected';
			this.activePlugin.object.show();
			this.activePlugin.object.refresh(this.results, this.filters, true);
			this.hide();
			this.closeSheet();
		}
	}
	
	this.onSelectPlugin = function(event) { 
		event = event ? event: window.event;
		if (event.stopPropagation) event.stopPropagation();
		if (event.cancelBubble!=null) event.cancelBubble = true;
		
		var id = event.target.className.replace('button ','');
		
		self.activatePlugin(id);
		
	};
	
	this._beneficiaryHandler = function(event) { this._showBeneficiary(event.detail); };
	this._projectHandler = function(event) { this._showProject(event.detail); };
	
	this._getBeneficiaryById = function(id) { 
		for(var i = 0; i < this.results.length; i++) { 
			if(this.results[i].id == id) {
				return this.results[i];
			}
		}
		return null;
	}
	
	this._showBeneficiary = function(id) { 
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				self.dom.sheetContent.innerHTML = xmlhttp.responseText;
				self._registerSheetLinks();
				self.openSheet();
			}
		};
		
		var item = this._getBeneficiaryById(id);
		
		if(this._map && item) { 
			this._map.setView(item.geo[0], item.geo[1]);
		}
		
		xmlhttp.open("GET", this.options.urlJSBeneficiarySheet.replace('*',id), true);
		xmlhttp.send();
	};
	
	this._showProject = function(id) { 
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				self.dom.sheetContent.innerHTML = xmlhttp.responseText;
				self._registerSheetLinks();
				self.openSheet();
			}
		};
		
		xmlhttp.open("GET", this.options.urlJSProjectSheet.replace('*',id), true);
		xmlhttp.send();
	};
	
	this._registerSheetLinks = function() { 
		var beneficiaries = this.dom.sheetContent.getElementsByClassName("beneficiary-link");
		for(var i = 0; i < beneficiaries.length; i++) { 
			var beneficiary = beneficiaries[i];
			beneficiary.addEventListener('click', function(e) { 
				self._showBeneficiary(e.target.getAttribute('data-id'));
			});
		}	
		
		var projects = this.dom.sheetContent.getElementsByClassName("project-link");
		for(var i = 0; i < projects.length; i++) { 
			var project = projects[i];
			project.addEventListener('click', function(e) { 
				self._showProject(e.target.getAttribute('data-id'));
			});
		}	
	}
	
	this.openSheet = function() { this.dom.sheet.style.visibility = 'visible'; }
	
	this.closeSheet = function() { this.dom.sheet.style.visibility = 'hidden'; }
	
	this.onToggleExpander = function(e) { 
		var button = e.target;
		if(button.parentElement.nodeName == 'H2') {
			var container = button.parentElement.parentElement;
		} else {
			var container = button.parentElement;
		}
		var ul = container.getElementsByTagName('UL')[0];
		
		if(this.screenOrientation == 'landscape') {
			if(button.className.match(/(?:^|\s)opened(?!\S)/)) {
				ul.style.display = 'none';
				button.className = button.className.replace(/(?:^|\s)opened(?!\S)/g, '');
			} else { 
				button.className += ' opened';
				ul.style.display = 'inline-block';
			}
		} else { 
			if(button.className.match(/(?:^|\s)opened(?!\S)/)) {
				button.className = button.className.replace(/(?:^|\s)opened(?!\S)/g, '');
				ul.style.display = 'none';
			} else { 
				button.className += ' opened';
				ul.style.display = 'inline-block';
			}
		}
	};
	
	this.updateEmbedUrl = function() { 
		var parms = {};
		
		parms.mode = 0
		
		if($('embed-theme').value != '') { 
			parms.theme = document.getElementById('embed-theme').value;
		}
		
		/*if($('embed-chart').value != '') {
			parms.statInit = document.getElementById('embed-chart').value;
		}*/
		
		ifwidth = $('iframe-w').value;
		ifheight = $('iframe-h').value;
		
		for(var i = 0; i < document.getElementsByClassName('embed-module').length; i++) { 
			if(this.dom.embedContent.getElementsByClassName('embed-module')[i].checked)
				parms.mode += parseInt(document.getElementsByClassName('embed-module')[i].value);
		}
		
		var get = [];
		
		for(var key in parms) { 
			get.push(key + '=' + parms[key]);
		}
		
		var criteria = [];
		
		if($('embed-selection')) { 
			
			for(var key in this.filters) { 
				if(Array.isArray(this.filters[key])) { 
					if(this.filters[key].length > 0)
						get.push(key + '=' + this.filters[key].join(','));
				} else {
					if((this.filters[key] != '' || this.filters[key] != 0) && key !== 'selExclusiveFilter')
						get.push(key + '=' + this.filters[key]);
				}
			}	
		
		}
		
		this.embedUrl = this.options.host + '?' + get.join('&');
		
		if($('embed-iframe').checked) {
			this.dom.embedText.innerText = '<iframe ' + (ifwidth != '' ? 'width="' + ifwidth + '" ': '') + (ifheight != '' ? 'height="' + ifheight + '" ':'') +  'src="' + this.embedUrl + '"></iframe>';
		} else {
			this.dom.embedText.innerText = '<a href="' + this.embedUrl + '" title="EASME Data Hub">Data Hub</a>';
		}
		
		
	}
	
	this.openDisclaimer = function() { 
		this.dom.sheetContent.innerHTML = $('disclaimer').innerHTML;
		this.openSheet();	
	}
		
	this.openFullScreen = function() { 
			var parms = this.makeEmbedURL(true);
			var urlparms = [];
			
			for(var parm in parms) {
				urlparms.push(parm + '=' + (parms[parm].constructor === Array ? parms[parm].join(','): parms[parm]));
			}
			
			window.open('index.php?' + urlparms.join('&'), '_EASME_DATA_HUB');
	};
	
	this.initialize(options);
}


// Helper functions

 
function $(id) { return document.getElementById(id); }


function Element(type, options) { 
	var el = document.createElement(type);
	
	for(var prop in options) {
		switch(prop) { 
			case 'html':
			case 'text': 
				el.innerHTML = options[prop];
				break;
			case 'class': 
				el.className = options[prop];
				break;
			case 'download':
				el.setAttribute('download', options[prop]);
			default: 
				el[prop] = options[prop];		
		} 
	}
	
	return el;
}

function hasClass(element, cls) { return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1; }

window.Event.prototype.stop = function(event) { 
	var evt = event ? event: window.event;
	if (evt.stopPropagation) evt.stopPropagation();
	if (evt.cancelBubble!=null) evt.cancelBubble = true;
};
/*
*	CustomEvent polyfill for IE9+
*/
(function () {

  if ( typeof window.CustomEvent === "function" ) return false;

  function CustomEvent ( event, params ) {
	params = params || { bubbles: false, cancelable: false, detail: undefined };
	var evt = document.createEvent( 'CustomEvent' );
	evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
	return evt;
   }

  CustomEvent.prototype = window.Event.prototype;

  window.CustomEvent = CustomEvent;
})();
