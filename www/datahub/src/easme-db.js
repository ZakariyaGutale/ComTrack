/*
*	SME DB Class
*	The main search engine engine driving result for project list and beneficiaries map.
*
*	rev: 1.0
*	author: Michael Fallise (contact: michael dot fallise at gmail dot com) 
*/

var EASME_TOOLS_COMPLETE = 7;
var EASME_TOOLS_MAP = 1;
var EASME_TOOLS_LIST = 2;
var EASME_TOOLS_STATS = 4;

var SmeDB = function(id, options) {
	var now = Date.now();

	this.options = { 
		mode: EASME_TOOLS_COMPLETE,
		statInit: 0,
		mobile: 0,
		expandButtonId: 'filter-collapser',
		closeButtonId: 'button-close',
		lookupButtonId: 'button-lookup',
		lookupField: null,
		countryFields: null,
		regionFields: null,
		topicFields: null,
		phaseFields: null,
		roleFields: null,
		cutoffFields: null,
		fromDeadlineField: '',
		toDeadlineField: '',
		budgetMinField: null,
		budgetMaxField: null,
		width: 450,
		height: 0,
		speed: 5,
		useMap: false,
		map: null,
		lat: 50.51,
		lon: 4.21,
		useList: false,
		list: null,
		pageItems: 12,
		urlJSDataCatalog: '/json/list-*.json?nocache=' + now,
		urlJSBeneficiarySheet: '/json/beneficiaries/beneficiary-*.json?nocache=' + now,
		urlJSProjectSheet: '/json/projects/project-*.json?nocache=' + now,
		beneficiary: 0,
		project: 0,
		
	};
	
	// db properties
	this._busy = false; // is a request running ?
	this._currentTool = 0;
	
	this.srcQueue = []; // data file queue
	this._filters = { 
		fulltext: '',
		phases: [],
		countries: [],
		regions: [],
		topics: [],
		budgetMin: 0,
		budgetMax: 0,
		from: '',
		to: '',
		cutoffs: [],
		roles: [],
	};
	this._results = [];
	
	// ui properties
	this._container = null;
	this._ui = null;
	this._toolbar = null;
	this._footer = null;
	this._map = null;
	this._list = null;
	this._stats = null;
	this._currentTool = null;
	this._buttonLookup = null;
	this._buttonExpand = null;
	this._buttonClose = null;
	this.opened = false;
	this.lastUpdate = '';
	
	// constructor
	this._construct = function() { 
		// setting options 
		var self = this;
		
		if(options) { 
			for(var key in options) {
				
				if(this.options.hasOwnProperty(key)) {
					
					this.options[key] = options[key];
				}
			}
		}
		
		this._container = document.getElementById(id);
		this._ui = document.getElementById('filters');
		this._toolbar = document.getElementById('toolbar');
		this._footer = document.getElementById('footer');
		this._content = document.getElementById('filters-content');
		this._buttonExpand = document.getElementById(this.options.expandButtonId);
		this._buttonSearch = document.getElementById(this.options.lookupButtonId);
		this._buttonCopyright = document.getElementById('copyright-button');
		
		
		var mode = 0;
		
		if((this.options.mode & EASME_TOOLS_LIST) == EASME_TOOLS_LIST) {
			
			var div = document.createElement('div');
			div.id = 'list';
			this._container.appendChild(div);
			this._list = new SmeList(div.id, {
				beneficiary: this.options.beneficiary,
				project: this.options.project
			});
			this._list.hide();
			div.addEventListener("displayParticipant", this._beneficiaryHandler.bind(this));
			div.addEventListener("displayProject", this._projectHandler.bind(this));
			mode = EASME_TOOLS_LIST;
		}
		
		if((this.options.mode & EASME_TOOLS_STATS) == EASME_TOOLS_STATS) {
			var div = document.createElement('div');
			div.id = 'stats';
			this._container.appendChild(div);
			this._stats = new SmeStats(div.id, { initGraph: this.options.statInit});
			this._stats.hide();
			
			mode = EASME_TOOLS_STATS;
		}
		
		if((this.options.mode & EASME_TOOLS_MAP) == EASME_TOOLS_MAP) {
			var div = document.createElement('div');
			div.id = 'map';
			this._container.appendChild(div);
			this._map = new SmeMap(div.id, { 
				beneficiary: this.options.beneficiary,
				lat: this.options.lat,
				lon: this.options.lon
			});
			this._map.hide();
			div.addEventListener("displayParticipant", this._beneficiaryHandler.bind(this));
			div.addEventListener("displayProject", this._projectHandler.bind(this));
			mode = EASME_TOOLS_MAP;
		}
		this.setMode(mode,false);
		
		
		this._sheetCloseButton = document.createElement('a');
		this._sheetCloseButton.className = 'close-button';	
		this._sheetCloseButton.addEventListener('click', function() {
			self._closeSheet();
		});
		
		this._sheetContent = document.createElement('div');
		this._sheetContent.id = 'marker-sheet-content';
		
		this._sheet = document.createElement('div');
		this._sheet.id = 'marker-sheet';
		
		this._sheet.appendChild(this._sheetCloseButton);
		this._sheet.appendChild(this._sheetContent);
		
		this._container.parentNode.appendChild(this._sheet);
		
		
		
		if(this._ui) { 
		
			
			var expanders = document.getElementsByClassName('expander');
			
			for(var i = 0; i < expanders.length; i++) {
				var button = expanders[i];
				if(button.parentElement.nodeName == 'H2') {
					var container = button.parentElement.parentElement;
				} else {
					var container = button.parentElement;
				}
				var ul = container.getElementsByTagName('UL')[0];
				button.addEventListener('click', this._toggleExpander); 
				ul.style.display = 'none';
			}
			/**/
			
			this._buttonExpand.addEventListener('click', function(e) { 
				console.log('expander');
				var evt = e ? e:window.event;
				if (evt.stopPropagation) evt.stopPropagation();
				if (evt.cancelBubble!=null) evt.cancelBubble = true;
				
				if(!self.opened) 
					self.show();
				else
					self.hide();
				
				return false;
			});
			
			this._buttonCopyright.addEventListener('click', function(e) { 
				
				var evt = e ? e:window.event;
				if (evt.stopPropagation) evt.stopPropagation();
				if (evt.cancelBubble!=null) evt.cancelBubble = true;
								
				var footer = document.getElementById('footer');
				
				if(hasClass(footer, 'opened')) { 
					footer.className = '';
				} else { 
					footer.className = 'opened';
				}
				return false;
			});
			
			this.setOrientation();
			
		}
		
		if(this.options.beneficiary != 0) { 
			this._showBeneficiary(this.options.beneficiary);
		} else 
		if(this.options.project != 0) { 
			this._showProject(this.options.project);
		} else 
			this._closeSheet();
	};
	this._beneficiaryHandler = function(event) { this._showBeneficiary(event.detail); };
	this._projectHandler = function(event) { this._showProject(event.detail); };
	
	this._showBeneficiary = function(id) { 
	
		var self = this;
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				self._sheetContent.innerHTML = xmlhttp.responseText;
				self._registerLinks(self._sheetContent);
				self._openSheet();
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
	
		var self = this;
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				self._sheetContent.innerHTML = xmlhttp.responseText;
				self._registerLinks(self._sheetContent);
				self._openSheet();
			}
		};
		
		xmlhttp.open("GET", this.options.urlJSProjectSheet.replace('*',id), true);
		xmlhttp.send();
	};
	
	this._registerLinks = function(container) { 
		
		var self = this;
		
		var beneficiaries = container.getElementsByClassName("beneficiary-link");
		
		for(var i = 0; i < beneficiaries.length; i++) { 
			var beneficiary = beneficiaries[i];
			beneficiary.addEventListener('click', function(e) { 
				self._showBeneficiary(e.target.getAttribute('data-id'));
			});
		}	
		
		var projects = container.getElementsByClassName("project-link");
		
		for(var i = 0; i < projects.length; i++) { 
			var project = projects[i];
			project.addEventListener('click', function(e) { 
				self._showProject(e.target.getAttribute('data-id'));
			});
		}	
	}
	
	this.refresh = function() { 
		if(!this._busy) { 
			if(this._portrait) this.hide();
			this._refreshFilters(); 
			
		}
	};
	
	this._refreshFilters = function() { 
		
		this._lockUI();
		
		var self  = this;
		
		var lookup = self.options.lookupField ? self.options.lookupField.value: '';
				
		var countries = [];
		var regions = [];
		var topics = [];
		var phases = [];
		var roles = [];
		var cutoffs = [];
		var from = '';
		var to = '';
		var budgetMin = 0;
		var budgetMax = 0;
		
		if(self.options.countryFields) { 
			for(var i=0; i < self.options.countryFields.length; i++) { 
				var field = self.options.countryFields[i];
				if(field.checked) countries.push(field.value);
			}
		}
		
		if(self.options.regionFields) { 
			for(var i=0; i < self.options.regionFields.length; i++) { 
				var field = self.options.regionFields[i];
				if(field.checked) regions.push(field.value);
			}
		}
		
		if(self.options.topicFields) { 
			for(var i=0; i < self.options.topicFields.length; i++) { 
				var field = self.options.topicFields[i];
				
				if(field.checked) topics.push(field.value);
			}
		}
		
		if(self.options.phaseFields) { 
			for(var i=0; i < self.options.phaseFields.length; i++) { 
				var field = self.options.phaseFields[i];
				if(field.checked) phases.push(field.value);
			}
		}
		
		if(self.options.roleFields) { 
			for(var i=0; i < self.options.roleFields.length; i++) { 
				var field = self.options.roleFields[i];
				if(field.checked) roles.push(field.value);
			}
		}
		
		if(self.options.cutoffFields) {  
			for(var i=0; i < self.options.cutoffFields.length; i++) { 
				var field = self.options.cutoffFields[i];
				if(field.checked) cutoffs.push(field.value);
			}
		}
		
		if(self.options.budgetMinField) { 
			budgetMin = Number(self.options.budgetMinField.value) > Number(self.options.budgetMinField.min) ? Number(self.options.budgetMinField.value): 0;
		}
		
		if(self.options.budgetMaxField) { 
			budgetMax = Math.round(Number(self.options.budgetMaxField.value)) < Math.round(Number(self.options.budgetMaxField.max)) ?  Math.round(Number(self.options.budgetMaxField.value)): 0;
		}
		
		if(self.options.fromDeadlineField) {  
			from = self.options.fromDeadlineField.value;
		}
		if(self.options.toDeadlineField) {  
			to = self.options.toDeadlineField.value;
		}
		
		self.search(lookup, topics, countries, regions, phases, budgetMin, budgetMax, cutoffs, from, to, roles);
	};
	
	this.setOrientation  = function() { 
		
		
		var self = this;
		
		if(screenOrientation == 'landscape') {
			/*
			if(this.options.height == 0) this.options.height = parseInt(document.body.clientHeight);
			this._ui.style.width = (this.options.width + this._buttonExpand.offsetWidth) + "px";
			this._content.style.width = this.options.width + "px";
			this._ui.style.left = -this.options.width + "px";
			this._ui.style.top = 50 + "px";
			*/
			this._portrait = false;
			if(this._map) this._map.options.useTooltip = false;
		
		} else { 
			/*
			this._ui.style.width = 'auto';
			this._content.style.width = 'auto';
			this._ui.style.left = 0;
			this._ui.style.top = 91 + "vh";
			*/
			this._portrait = true;
			
			if(this._map) this._map.options.useTooltip = true;
		}
		
	};
	
	this._toggleExpander = function(e) {
		var button = e.target;
		if(button.parentElement.nodeName == 'H2') {
			var container = button.parentElement.parentElement;
		} else {
			var container = button.parentElement;
		}
		var ul = container.getElementsByTagName('UL')[0];
		
		if(screenOrientation == 'landscape') {
			if(button.className.match(/(?:^|\s)opened(?!\S)/)) {
				ul.style.display = 'none';
				button.className = button.className.replace(/(?:^|\s)opened(?!\S)/g, '');
			} else { 
				button.className += ' opened';
				ul.style.display = 'inline-block';
			}
		} else { 
			if(button.className.match(/(?:^|\s)opened(?!\S)/)) {
				//ul.style.display = 'none';
				button.className = button.className.replace(/(?:^|\s)opened(?!\S)/g, '');
			} else { 
				button.className += ' opened';
				//ul.style.display = 'inline-block';
			}
		}
	};
	
	this._lockUI = function() { this._busy = true; };
	
	this._unlockUI = function() { this._busy = false; };
	
	this.search = function(fulltext, topics, countries, regions, phases, budgetMin, budgetMax, cutoffs, from, to, roles ) { 
				
		this._filters.fulltext = fulltext == null ? '': fulltext;
		this._filters.topics = topics == null ? []: topics;
		this._filters.countries = countries == null ? []: countries;
		this._filters.regions = regions == null ? []: regions;
		this._filters.phases = phases == null ? []: phases;
		this._filters.roles = roles == null ? []: roles;
		this._filters.cutoffs = cutoffs == null ? []: cutoffs;
		this._filters.from = from == null ? '': from;
		this._filters.to = to == null ? '': to;
		this._filters.budgetMin = budgetMin == null ? '': budgetMin;
		this._filters.budgetMax = budgetMax == null ? '': budgetMax;
		this._results = [];
		
		this._lockUI();
		
		this._query();
	};
	
	this._query = function() { 
		this._queue = [];
		this._queue.push(this.options.urlJSDataCatalog.replace('*', 1));
		this._processQueue();
	};
	
	this._processQueue = function() { 
		
		while(this._queue.length > 0) {
			this._fetch(this._queue.shift()); 
		}
		
		this._triggerRefreshEvent();
		this._unlockUI();
	};
	
	this._fetch = function(src) { 
		var self = this;
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var data = JSON.parse(xmlhttp.responseText);
				
				self._filter(data.items);
				
				if(data.linkage != '') {
					self._queue.push(data.linkage);
				}
				
				self.lastUpdate = data.created;
				
				if(self._currentTool) self._currentTool.refresh(self._results, self._filters, data.linkage == '');
			}
			
			self._processQueue();
		};
		
		xmlhttp.open("GET", src, true);
		xmlhttp.send();
	};
	
	this._filter = function(data) { 
		
		for(var i=0; i < data.length; i++) { 
			var item = data[i];
			var valid = true;
			
			if(valid && this._filters.topics.length > 0) { 
				var found = false;
								
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					
					
					if(this._filters.topics.join(',').indexOf(project.topic) != -1) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p,1);
					}
				}
				
				valid = valid && found;
			}
			
			if(valid && (this._filters.countries.length > 0 || this._filters.regions.length > 0)) { 
				
				if(this._filters.countries.indexOf(item.country) == -1) { 
					
					if(this._filters.regions.length > 0 && item.nuts != '') {
						valid = valid && this._filters.regions.indexOf(item.nuts) != -1;
					}
					else { 
						valid = false;
					}
				}
			}
						
			if(valid && this._filters.phases.length > 0) { 
				
				var found = false;
				
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					
					if(this._filters.phases.join(",").indexOf(project.phase) != -1) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p, 1);
					}
				}
				
				valid = valid && found;
			}
			
			if(valid && this._filters.roles.length > 0) { 
				
				var found = false;
				
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					
					if(this._filters.roles.indexOf(project.role) != -1) { 
						found = true;
						p++;
					}
					else { 
						if(this._filters.roles.indexOf('site') != -1) { 
							if(project.sites.length > 0) {
								found = true;
								p++;	
							} else { 
								item.projects.splice(p, 1);
							}
						} else { 
							item.projects.splice(p, 1);
						}
						
					}
				}
				
				valid = valid && found;
			}
			
			if(valid && this._filters.cutoffs.length > 0) { 
				var found = false;
				
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					if(this._filters.cutoffs.join(",").indexOf(project.cutoff) != -1) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p,1);
					}
				}
				
				valid = valid && found;
			}
			
			if(valid && this._filters.budgetMin > 0) { 
				var found = false;
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					if(this._filters.budgetMin <= Number(project.budget)) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p,1);
					}
				}
				
				valid = valid && found;
			}
			
			if(valid && this._filters.budgetMax > 0) { 
				var found = false;
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					
					if(this._filters.budgetMax >= Number(project.budget)) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p,1);
					}
				}
				valid = valid && found;
			}
			
			if(valid && this._filters.from != '') { 
				var found = false;
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					var deadline = new Date(project.deadline);
					var from = this._filters.from.split('/');
										
					if(deadline.getFullYear() > from[1] || (deadline.getFullYear() == from[1] && deadline.getMonth() >= from[0] - 1)) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p,1);
					}
				}
				valid = valid && found;
			}
			
			if(valid && this._filters.to != '') { 
				var found = false;
				p = 0;
				while(p < item.projects.length) { 
					var project = item.projects[p];
					var deadline = new Date(project.deadline);
					var to = this._filters.to.split('/');
						
					if(project.deadline == '' || deadline.getFullYear() < Number(to[1]) || (deadline.getFullYear() == Number(to[1]) && deadline.getMonth() <= Number(to[0] - 1))) { 
						found = true;
						p++;
					}
					else { 
						item.projects.splice(p,1);
					}
				}
				valid = valid && found;
			}
			
			if(valid && this._filters.fulltext != '') { 
				var needle = new RegExp(this._filters.fulltext,'i');
				
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
						
						if(project.acronym.search(needle) != -1  || project.title.search(needle) != -1  || project.number.search(needle) != -1 || project.abstract.search(needle) != -1 || project.topic.toUpperCase() == this._filters.fulltext.toUpperCase()) { 
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
			if(valid ) this._results.push(item);
		}
	};
	
	this._getBeneficiaryById = function(id) { 
		for(var i = 0; i < this._results.length; i++) { 
			if(this._results[i].id == id) {
				return this._results[i];
			}
		}
		return null;
	}
	
	this.hide = function(options) { 
		
		var step = this.options.speed;
		var self = this;
		
		this.opened = false;
		
		this._ui.className = '';
		
		if(screenOrientation == 'landscape') {
			this._ui.style.left = '-450px'; 
			
		} else { 
			this._ui.style.top = '90%'; 			
			
		}
	};
	
	this.show = function(options) { 
		var self = this;
		
		this.opened = true;
		
		this._ui.className = "opened";
		
		if(screenOrientation == 'landscape') {
			
			self._ui.style.left = 0;
						
		} else { 
			this._ui.style.top = '8%'; 
			
		}
	};
	
	this.setMode = function(mode, refresh) { 
		
		if(refresh == null) refresh = true;
		
		mode = parseInt(mode);
		
		if(this._currentTool) this._currentTool.hide();
		
		switch(mode) { 
			case EASME_TOOLS_MAP: 
				this._currentTool = this._map;
				break;
			case EASME_TOOLS_LIST: 
				this._currentTool = this._list;
				break;
			case EASME_TOOLS_STATS: 
				this._currentTool = this._stats;
				break;
		}
		
		this._currentTool.show();
		
		if(refresh) this._currentTool.refresh(this._results, this._filters, true);
	};
	
	this._triggerRefreshEvent = function() { 
		var event; // The custom event that will be created

		if (document.createEvent) {
			event = document.createEvent("HTMLEvents");
			event.initEvent("updated", true, true);
		} else {
			event = document.createEventObject();
			event.eventType = "updated";
		}
		
		event.eventName = "updated";
		
		if (document.createEvent) {
			this._container.dispatchEvent(event);
		} else {
			this._container.fireEvent("on" + event.eventType, event);
		}
	};
	
	this._openSheet = function() { this._sheet.style.visibility = 'visible'; }
	
	this._closeSheet = function() { this._sheet.style.visibility = 'hidden'; }
	
	this._displaySheet = function(id) { 
		
		var self = this;
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				self._sheetContent.innerHTML = xmlhttp.responseText;
				self._openSheet();
			}
		};
		
		xmlhttp.open("GET", this.options.urlJSDataSheet.replace('*',id), true);
		xmlhttp.send();
	};
	
	this._construct();	
};

function hasClass(element, cls) { return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1; }