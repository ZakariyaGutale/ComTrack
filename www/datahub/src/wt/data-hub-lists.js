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

/*
*	SME List Class
*	
*
*	rev: 1.0
*	author: Michael Fallise (contact: michael dot fallise at gmail dot com) 
*/

var DataHubLists = function(container, options) { 
	this._el = null;
	this._lists = null;
	this._export = null;
	this._beneficiaries = null;
	this._projects = null;
	this._projectsCount = 0;
	this._projectsDisplayed = 0;
	this._buttons = null;
	this._title = null;
	this._sheetPrintButton = null;
	this._sheetExporteButton = null;
	this._data = null;
	
	this.options = { 
		pageItems: 256,
		useTooltip: false
	};
	
	this._construct = function() { 
		
		
		this._el = container;
		
		var self = this;
		
		// building interface 
		if(this._el) { 
			
			// adding tab selectors
			this._buttons = document.createElement('div');
			this._buttons.id = 'list-selector';
			
			var a = document.createElement('a');
			a.id = 'button-view-beneficiaries';
			a.href = '#';
			a.innerHTML = 'organisations';
			a.addEventListener('click', function() { self._displayBeneficiaries(); });
			this._buttons.appendChild(a);
			
			a = document.createElement('a');
			a.id = 'button-view-projects';
			a.href = '#';
			a.innerHTML = 'commitments';
			a.addEventListener('click', function() { self._displayProjects(); });
			this._buttons.appendChild(a);
			
			/*a = document.createElement('a');
			a.id = 'button-list-dl';
			a.href = '#';
			a.innerHTML = 'Download';
			a.addEventListener('click', function() { self._download(); });
			this._buttons.appendChild(a);*/
			
			// adding lists
			this._beneficiaries = document.createElement('div');
			this._beneficiaries.id = 'beneficiary-list';
			
			this._projects = document.createElement('div');
			this._projects.id = 'project-list';
			
			this._lists = document.createElement('div');
			this._lists.id = 'lists';
			this._lists.appendChild(this._beneficiaries);
			this._lists.appendChild(this._projects);
			
			this._el.appendChild(this._lists);
			this._el.appendChild(this._buttons);
		}
		
		if(options) { 
			for(var key in options) {
				if(this.options.hasOwnProperty(key)) { this.options[key] = options[key]; }
			}
		}
		
		this._displayBeneficiaries();
		
		this.hide();
	};
	
	this._indexOfProject = function(id, limit) {
		
		return document.getElementById('project-' + id) != null;
	};
	 
	this.refresh = function(data, filters, terminate) { 
		this._data = data;
		
		this._selection = [];
		this._filters = filters;
		
		this._projectsCount = 0;
		
		this._beneficiaries.innerHTML = '';
		this._projects.innerHTML = '';
		this._projectsDisplayed = 0;
		
		var projects = [];
		
		for(var i = 0; i < this._data.length; i++) { 
			var data = this._data[i];
			for(var p = 0; (p < data.projects.length); p++) {
				if(projects.indexOf(data.projects[p].id) === -1)  projects.push(data.projects[p].id);
			}
		}
		
		this._projectsCount = projects.length; 
		
		projects = [];
				
		if(terminate) this._displayResult(this.options.pageCurrent);
	};
	
	this._searchArray = function(id, array) { 
		
		for(var i = 0; i < array.length; i++) { 
			if(array[i].id == id) { 
				return i;
			}
		}
		
		return false;
	}
	
	this._displayResult = function() { 
		var self = this;
		
		this._beneficiaries.innerHTML = '';
		this._projects.innerHTML = '';
		this._projectsDisplayed = 0;
		
		var projectSkipped = false;
		var beneficiariesSkipped = false;
		
		this._data.sort(this._sortBeneficiaries);
		
		for(var i = 0; i < this._data.length; i++) { 
			
			if(!beneficiariesSkipped && i > this.options.pageItems) { 
			
				var warning = document.createElement('div');
				warning.className = 'list-limit';
				warning.innerHTML = "There are already " + this.options.pageItems + " items displayed and there are " + (this._data.length - this.options.pageItems) + " more to show. For better performance, we suggest that you refine your search. ";
				var button = document.createElement('a');
				button.innerHTML = 'No, show me everything!';
				button.href = '#';
				button.addEventListener('click', this._displayAllBeneficiaries.bind(this));
				warning.appendChild(button);
				this._beneficiaries.appendChild(warning);
				
				beneficiariesSkipped = true;
				
			}
			else {
				
				if(!beneficiariesSkipped) {
					this._beneficiaries.appendChild(this._buildPartner(this._data[i]));
				}
			}
		}
		
		
		var projects = [];
		
		for(var i = 0; i < this._data.length; i++) { 
			
			var data = this._data[i];
			
			for(var p = 0; (p < data.projects.length); p++) {
				var project =  data.projects[p];
				
				if(this._searchArray(project.id, projects) === false) { 
					projects.push(project);
				}
			}
		}
		
		projects.sort(this._sortProjects);
		
		for(i = 0; i < projects.length && !projectSkipped; i++) { 
			var project =  projects[i];
			
			
			this._projects.appendChild(this._buildProject(project));
			
			this._projectsDisplayed++;
			
			if(this._projectsDisplayed > this.options.pageItems) { 
				projectSkipped = true;
			}
		}
		
		if(projectSkipped) { 
			var warning = document.createElement('div');
			warning.className = 'list-limit';
			warning.innerHTML = "There are already " + this.options.pageItems + " items displayed and there are " + (this._projectsCount - this.options.pageItems) + " more to show. For better performance, we suggest that you refine your search. ";
			var button = document.createElement('a');
			button.innerHTML = 'No, show me everything!';
			button.href = '#';
			button.addEventListener('click', this._displayAllProjects.bind(this));
			warning.appendChild(button);
			this._projects.appendChild(warning);
		}
		
	};
	
	this._buildPartner = function(partner) { 
		
		var item = document.createElement('div');
		var name = document.createElement('span');
		var city = document.createElement('span');
		
		name.className = 'beneficiary-name';
		city.className = 'beneficiary-city';
		
		var link = document.createElement('a');
		link.href = '#';
		link.className = 'beneficiary-button';
		link.title = link.innerHTML = partner.name;
		link.setAttribute('data-id', partner.id);
		link.addEventListener('click', this._beneficiariesClickHandler.bind(this));
		
		name.appendChild(link);
		
		var link = document.createElement('a');
		link.href = '#';
		link.className = 'beneficiary-button';
		link.title = link.innerHTML = partner.city + ' (' + countries[partner.country] + ')';
		link.setAttribute('data-id', partner.id);
		link.addEventListener('click', this._beneficiariesClickHandler.bind(this));
		
		city.appendChild(link);
		
		item.className = 'item';
		item.appendChild(name);
		item.appendChild(city);
							
		
		
		return item;
	}
	
	this._buildProject = function(project) { 
		
		var item = document.createElement('div');
		var acronym = document.createElement('span');
		var title = document.createElement('span');
		var phase= document.createElement('span');
		
		/*
		acronym.className = 'project-acronym';
		acronym.id = 'project-' + project.id;
		title.className = 'project-title';
		
		link = document.createElement('a');
		link.href = '#';
		link.className = 'project-button';
		link.title = link.innerHTML = project.title;
		link.setAttribute('data-id', project.id);
		link.addEventListener('click', this._projectsClickHandler.bind(this));
		
		acronym.appendChild(link);
		*/
		var link = document.createElement('a');
		link.href = '#';
		link.className = 'project-button';
		link.title = link.innerHTML = project.title;
		link.setAttribute('data-id', project.id);
		link.addEventListener('click', this._projectsClickHandler.bind(this));
		
		title.appendChild(link);
		
		item.className = 'item';
		item.appendChild(acronym);
		item.appendChild(title);
		
		return item;
	}
	
	this._sortProjects = function (a,b) {
	  if (a.acronym < b.acronym) return -1;
	  if (a.acronym > b.acronym) return 1;
	  return 0;
	}
	
	this._sortBeneficiaries = function (a,b) {
	  if (a.name < b.name) return -1;
	  if (a.name > b.name) return 1;
	  return 0;
	}

	this._displayAllBeneficiaries = function() { 
		var self = this;
		
		this._beneficiaries.removeChild(this._beneficiaries.lastElementChild);
		
		for(var i = this.options.pageItems + 1; i < this._data.length; i++) { 
			this._beneficiaries.appendChild(this._buildPartner(this._data[i]));
		}
	};
	
	this._displayAllProjects = function() { 
		var self = this;
		var projectCount = 0;
		var projects = [];
		
		this._projects.innerHTML = '';
		
		for(var i = 0; i < this._data.length; i++) { 
			var data = this._data[i];
			for(var p = 0; (p < data.projects.length); p++) {
				var project =  data.projects[p];
				if(this._searchArray(project.id, projects) === false) { 
					projects.push(project);
				}
			}
		}
		
		projects.sort(this._sortProjects);
		
		for(i = 0; i < projects.length; i++) { 
			var project =  projects[i];
			this._projects.appendChild(this._buildProject(project));
		}
		
	};
	
	this._displayBeneficiaries = function() { 
		document.getElementById('button-view-beneficiaries').className = 'selected';
		document.getElementById('button-view-projects').className = '';
		
		this._beneficiaries.style.display = "inline";
		this._projects.style.display = "none";
	};
	
	this._displayProjects = function() { 
		document.getElementById('button-view-beneficiaries').className = '';
		document.getElementById('button-view-projects').className = 'selected';
		
		this._beneficiaries.style.display = "none";
		this._projects.style.display = "inline";
	};
	
	this.hide = function( ) { 
		this._el.style.visibility = "hidden";
	};
	
	this.show = function( ) { 
		this._el.style.visibility = "visible";
		
	};
	
	
	this._beneficiariesClickHandler = function(e) {
		this._triggerBeneficiarySheetEvent(e.target.getAttribute('data-id'));
	};
	
	this._projectsClickHandler = function(e) {
		this._triggerProjectSheetEvent(e.target.getAttribute('data-id'));
	};
	
	this._triggerBeneficiarySheetEvent = function(id) { 
		var event = new CustomEvent("displayParticipant", { 'detail': id });
		
		this._el.dispatchEvent(event);
		
	};
	
	this._triggerProjectSheetEvent = function(id) { 
		var event = new CustomEvent("displayProject", { 'detail': id });
		
		this._el.dispatchEvent(event);
	};
	
	this._buildExportMenu = function() { 
		var self = this;
		
		var menu = document.createElement('div');
		menu.className = "list-export-menu list-export-menu-top-right";
		
		var ul = document.createElement('ul');
		menu.appendChild(ul);
		
		var main = document.createElement('li');
		main.className = "export-main";
		ul.appendChild(main);
		
		var bt = document.createElement('a');
		bt.href = "#";
		main.appendChild(bt);
		
		var span = document.createElement('span');
		span.innerHTML = "menu.label.undefined";
		bt.appendChild(span);
		
		var ul = document.createElement('ul');
		main.appendChild(ul);
		
		var li = document.createElement('li');
		ul.appendChild(li);
		
		var bt = document.createElement('a');
		bt.href = "#";
		li.appendChild(bt);
		
		var span = document.createElement('span');
		span.innerHTML = "Download as...";
		bt.appendChild(span);
		
		var ul = document.createElement('ul');
		li.appendChild(ul);
		
		var li = document.createElement('li');
		ul.appendChild(li);
		
		var bt = document.createElement('a');
		bt.href = "#";
		bt.addEventListener('click', self._exportAsPDF.bind(this));
		li.appendChild(bt);
		
		var span = document.createElement('span');
		span.innerHTML = "PDF";
		bt.appendChild(span);
		
		var li = document.createElement('li');
		ul.appendChild(li);
		
		var bt = document.createElement('a');
		bt.addEventListener('click', self._exportAsXLSX.bind(this));
		bt.href = "#";
		
		li.appendChild(bt);
		
		var span = document.createElement('span');
		span.innerHTML = "XLSX";
		bt.appendChild(span);
		
		return menu;
	};
	
	this._exportAsPDF = function() { 
		
		var tb = null;
		var tables = [];
		
		// headers 
		var count= 0;
		
		for(var i = 0; i < this._data.length; i++) { 
			
			if(count % 100 == 0) { 
				if(tb) { 
					tables.push(tb);
				}
				
				tb = [];
				
				tb.push([
					{ text: 'Country', style: "tableHeader" } , 
					{ text: 'City', style: "tableHeader" }, 
					{ text: 'Beneficiary', style: "tableHeader" }, 
					{ text: 'Website', style: "tableHeader" }, 
					{ text: 'Proposal Acronym', style: "tableHeader" }, 
					{ text: 'Long name', style: "tableHeader" }, 
					{ text: 'Call Deadline Date', style: "tableHeader" }, 
					{ text: 'Topic', style: "tableHeader" }
				]);
			}
			
			var data = this._data[i];
			
			for(var p = 0; (p < data.projects.length); p++) {
				tb.push([ 
					{ text: countries[data.country], style: "cell" },  
					{ text: data.city, style: "cell" },
					{ text: data.name, style: "cell" },  
					{ text: "", style: "cell" },
					{ text: data.projects[p].acronym, style: "cell" },  
					{ text: data.projects[p].title, style: "cell" },
					{ text: data.projects[p].cutoff, alignment: 'center', style: "cell" },  
					{ text: this._getTopicLabel( data.projects[p].topic ), style: "cell" }
				]);
				
				count++;
				
			}
		}
		
		tables.push(tb);	
		
		var docDefinition = {
			pageOrientation: 'landscape',	
			content: [
			
				{ text: 'Horizon 2020 SME Instrument', style: "header" },
				this._selection.join(', '),
				
				
			],
			styles: { 
				header: {
					fontSize: 16,
					bold: true
				},
				tableHeader: { 
					fontSize: 9,
					bold: true,
            		fillColor: '#E6AEAE',
					hLineColor: 'red'
					
				},
				cell: {
					fontSize: 9,
					vLineColor: 'white',
					hLineColor: 'white',
					
				}
			}
		};
		
		for(var i=0; i < tables.length; i++) { 
			var obj = { 
				table: {
					headerRows: 1,
					body: tables[i]
				}
			};
			docDefinition.content.push(obj);
		}
		
		pdfMake.createPdf(docDefinition).open();
	};
	
	this._download = function() { 
	
		
		var content = "Participant;City;Country;Project acronym;Project title;Role;Project budget;Requested grant\n";
		
		for(var i = 0; i < this._data.length; i++) { 
			var item = this._data[i];
			
			var partner = item.name + ";" + item.city + ";" + item.country + ";";
			
			for(p = 0; p < item.projects.length; p++) { 
				var project = item.projects[p];
				
				content += partner + project.acronym + ";" + "\"" + project.title + "\"" + ";" + project.role + ";" + project.budget + ";" + project.request + "\n";
			} 
			
		}
		
		var blob = new Blob(["\ufeff", content], {type: 'text/csv'});
		
		if(window.navigator.msSaveOrOpenBlob) {
			window.navigator.msSaveBlob(blob, "easme_data_ie.csv");
		}
		else{
			var link = window.document.createElement('a');
			link.href = window.URL.createObjectURL(blob);
			link.download = "easme_data.csv";        
			document.body.appendChild(link);
			link.click();        
			document.body.removeChild(link);
		}
	
	};
	
	this._getTopicLabel = function(id) { 
	
		for(var i = 0; i < topics.length; i++) { 
			if(topics[i].id.match(id)) return topics[i].label;
		} 
		return '';
	}
	
	
	
	this._construct();
};


// Create the event
var event = new CustomEvent("serviceLoaded", { "detail": {"service": "list", "object": DataHubLists}});

// Dispatch/Trigger/Fire the event
document.dispatchEvent(event);
