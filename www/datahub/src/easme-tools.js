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
var EASME_TOOLS_COMPLETE = 7;
var EASME_TOOLS_MAP = 1;
var EASME_TOOLS_LIST = 2;
var EASME_TOOLS_STATS = 4;

var EasmeTools = function(id, options) { 
	
	this.options = { 
		width: 1045,
		height: 540,
		border: 1,
		content: 'http://sc5.easme.nightshift.pro/?',
		mode: EASME_TOOLS_COMPLETE,
		statInit: 0,
		theme: '',
		mobile: 0,
		allowFS: 1,
		// search parms
		search: '',
		phases: [],
		countries: [],
		topics: [],
		cutoff: '',
		budget_min: 0,
		budget_max: 0,
		cutoff: '',
		from_date: '',
		to_date: ''
	};
	
	this.el = document.getElementById(id);
	this.frame = null;
		
	// init function 
	this.init = function() {
		// building frame 
		// setting options 
		if(options) { 
			for(var key in options) {
				
				if(this.options.hasOwnProperty(key)) {
					
					this.options[key] = options[key];
				}
			}
		}
		
		if(this.el) { 
			this.frame = document.createElement("IFRAME");	
			this.frame.setAttribute('width', this.options.width);
			this.frame.setAttribute('height', this.options.height);
			this.frame.setAttribute('scrolling', 'no');
			this.frame.setAttribute('frameborder', '0');
			
			var parms = [ 
				'w=' + this.options.width,
				'h=' + this.options.height,
				'mode=' + this.options.mode,
				'mobile=' + this.options.mobile,
				'allowFS=' + this.options.allowFS
			];
			
			if(this.options.theme != '') parms.push('theme=' + this.options.theme);
			if(this.options.statInit != '') parms.push('statInit=' + this.options.theme);
			if(this.options.search != '') parms.push('search=' + encodeURIComponent(this.options.search));
			if(this.options.phases.length > 0) parms.push('phases=' + this.options.phases.join(','));
			if(this.options.countries.length > 0) parms.push('countries=' + this.options.countries.join(','));
			if(this.options.topics.length > 0) parms.push('topics=' + this.options.topics.join(','));
			if(this.options.cutoff != '') parms.push('cutoff=' + this.options.cutoff);
			if(this.options.budget_min != '') parms.push('budget_min=' + this.options.budget_min);
			if(this.options.budget_max != '') parms.push('budget_max=' + this.options.budget_max);
			if(this.options.from_date != '') parms.push('from_date=' + this.options.from_date);
			if(this.options.to_date != '') parms.push('to_date=' + this.options.to_date);
			
			
			this.frame.setAttribute('src', this.options.content + parms.join('&'));
			this.el.appendChild(this.frame);
		}	
	};
	
	this.init();
	
};
