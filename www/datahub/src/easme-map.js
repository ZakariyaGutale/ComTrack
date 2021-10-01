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
*	SME Map Class
*	
*
*	rev: 1.0
*	author: Michael Fallise (contact: michael dot fallise at gmail dot com) 
*/

var SmeMap = function(container, options) { 
	this.options = { 
		minZoom: 2,
		maxZoom: 19,
		useTooltip: false,
		height: 540,
		width: 1045,
		lat: 5,
		lon: 0,
		// osmUrl: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
		//osmUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		osmUrl: 'https://europa.eu/webtools/maps/tiles/osm-ec/{z}/{x}/{y}.png',
		osmAttrib: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
		token: 'pk.eyJ1IjoibWZhbGxpc2UiLCJhIjoiY2pwc2dmd2x6MTNydjQzbjltZnUybXhtaiJ9.akD0UR5u-6iJdVOpNG9WGQ',
		iconCoordinator: null,
		iconPartner: null,
		iconSite: null,
		beneficiary: 0
	};
	
	this._map = null;
	this._sheet = null;
	this._sheetContent = null;
	this._sheetCloseButton = null;
	this._legend = null;
	this._zoomInButton = null;
	this._zoomOutButton = null;
	this._data = [];
	
	this._clusters = null;	
		
	this._construct = function() { 
		
		var self = this;
		
		this._container = container;
		this._container.className = 'wtmap';
		this._container.id = '';
		
		var div = document.createElement('div'); 
		div.className = "wtcontent";
		this._container.appendChild(div);
		
		this._map = document.createElement('div'); 
		this._map.id = "map";
		div.appendChild(this._map);
		
		
		var markerIcon = L.Icon.extend({
			options: {
				shadowUrl: '/src/images/marker-shadow.png',
				iconSize:     [20, 20],
				shadowSize:   [20, 20],
				iconAnchor:   [15, 15],
				shadowAnchor: [12, 13]
			}
		});
		
		this.options.iconPartner =  new markerIcon({iconUrl: '/src/images/partner-sample.png'});
		this.options.iconCoordinator = new markerIcon({iconUrl: '/src/images/coordinator-sample.png'});
		this.options.iconSite = new markerIcon({iconUrl: '/src/images/site-sample.png'});
		
		if(options) { 
			for(var key in options) {
				if(this.options.hasOwnProperty(key)) { this.options[key] = options[key]; }
			}
		}
		
		this._map = new L.Map(this._map, {zoomAnimation: false,});
		
		// create the tile layer with correct attribution
		/*var osm = new L.TileLayer(this.options.osmUrl, {
			minZoom: this.options.minZoom, 
			maxZoom: this.options.maxZoom, 
			attribution: this.options.osmAttrib,
			accessToken: this.options.token,
			id: 'map-title'
		});
		this._map.addLayer(osm);*/

		var osm = new L.TileLayer(this.options.osmUrl, {
			minZoom: this.options.minZoom,
			maxZoom: this.options.maxZoom,
			attribution: this.options.osmAttrib,
			accessToken: this.options.token,
			//subdomains: 'abcd',
			id: 'map-title'
		});
		this._map.addLayer(osm);
		
		this._clusters = null;
				
		// position map
		this._map.setView(new L.LatLng(this.options.lat, this.options.lon),0);
		this._map._layersMaxZoom = this.options.maxZoom;
		/*
		this.nutsLayer = L.geoJson(world, {
			style: {weight: 0, fillOpacity: 0},
			
		}).addTo(this._map);
		*/
		this._legend = document.createElement('div'); 
		this._legend.id= "legend";
		var coordinator = document.createElement('span');
		coordinator.className = 'coordinator';
		coordinator.innerHTML = 'Coordinator';
		this._legend.appendChild(coordinator);
		var participant = document.createElement('span');
		participant.className = 'partner';
		participant.innerHTML = 'Participant';
		this._legend.appendChild(participant);
		var item = document.createElement('span');
		item.className = 'site';
		item.innerHTML = 'Demo site';
		this._legend.appendChild(item);
		this.legendDemo = item;
		this._container.parentNode.appendChild(this._legend);
		
		this._pinButton = document.createElement('div');
		this._pinButton.id = 'button-pin';
		this._pinButton.innerHTML = "";
		this._pinButton.title = "Show Participants";
		this._pinButton.addEventListener('click', function() {  
			self.mode = 'pins';
			self.showMode();
		});
		this._container.parentNode.appendChild(this._pinButton);
		
		this._moneyButton = document.createElement('div');
		this._moneyButton.id = 'button-money';
		this._moneyButton.innerHTML = "";
		this._moneyButton.title = "Show Money Allocated";
		this._moneyButton.addEventListener('click', function() {  
			self.mode = 'money';
			self.showMode();
		});
		this._container.parentNode.appendChild(this._moneyButton);
		
		this.heatIsOn = false;
		
		this.mode = 'pins';
		
		this._zoomInButton = document.createElement('div');
		this._zoomInButton.id = 'button-zoom-in';
		this._zoomInButton.title = "Zoom in";
		this._zoomInButton.addEventListener('click', function() { self._map.zoomIn(); });
		this._container.parentNode.appendChild(this._zoomInButton);
		
		this._zoomOutButton = document.createElement('div');
		this._zoomOutButton.id = 'button-zoom-out';
		this._zoomOutButton.title = "Zoom out";
		this._zoomOutButton.addEventListener('click', function() { self._map.zoomOut(); });
		this._container.parentNode.appendChild(this._zoomOutButton);
		
	};
	
	this.showMode = function() { 
		
		switch(this.mode) { 
			case 'pins':
				this._pinButton.className = 'selected';
				this._moneyButton.className = '';
				this.setPinClusters(this._data);
				
				break;
			case 'money': 
				this._pinButton.className = '';
				this._moneyButton.className = 'selected';
				this.setMoneyClusters(this._data);
				break;
		}
		
		//this._map.fitBounds(this.bounds);
	}
	
	this.setPinClusters = function(data) {
		
		var self = this;
		var registeredSites = [];
		
		if(this._clusters) { 
			this._map.removeLayer(this._clusters);
			this._clusters.clearLayers(); 
		}
		
		this.bounds = [
			[90, 180],
			[-90,-180]
		];
		
		this._clusters = new L.MarkerClusterGroup( {
			maxClusterRadius: 80,
			//disableClusteringAtZoom: 8,
			
			iconCreateFunction: function (cluster) {
				var markers 	= cluster.getAllChildMarkers();
				var items 		= markers.length;
				var className 	= '';
				var size 		= 0;
				
				if(items < 25) { 
					className = 'marker-cluster-small';
					size = 30;
				} else 
				if(items < 500) { 
					className = 'marker-cluster-medium';
					size = 40;
				} else {
					className = 'marker-cluster-large';
					size = 50;
				}
				
				return L.divIcon({ html: '<div><span>' + items + '</span></div>', className: 'marker-cluster ' + className, iconSize: L.point(size, size) });
			}
		});
		
		for(var i=0; i < data.length; i++) { 
			var item = data[i];
			if(item.geo[0] != 0 || item.geo[1] != 0) {
				
				var budget = 0;
				
				for(var p=0; p < item.projects.length;p++) { 
					var project = item.projects[p];
					
					budget += parseInt(project.request);
					if(project.sites.length > 0) { 
						
						for(var s = 0 ; s < project.sites.length; s++) { 
							
							if(registeredSites.indexOf(project.sites[s].id) == -1 && Math.abs(project.sites[s].lat) <= 90 && Math.abs(project.sites[s].lng) <= 180) {
								
								var site = project.sites[s];

								var icon = L.icon({
									iconUrl: 'src/images/ooc/' + project.theme + '.png',
									iconSize: [25,25]
								});
								
								var marker = L.marker([project.sites[s].lat, project.sites[s].lng] , {
									icon: icon,
									title: project.sites[s].name,
									id: project.sites[s].fk_proposal_id,
									budget: 0
								});
								
								marker.on('click', function (e) { 
									self._triggerSiteEvent(this.options.id);
								});
								
								this._clusters.addLayer(marker);	
								registeredSites.push(project.sites[s].id);
								
								if(Number(site.lat) < this.bounds[0][0]) this.bounds[0][0] = Number(site.lat);
								if(Number(site.lng) < this.bounds[0][1]) this.bounds[0][1] = Number(site.lng);
								if(Number(site.lat) > this.bounds[1][0]) this.bounds[1][0] = Number(site.lat);
								if(Number(site.lng) > this.bounds[1][1]) this.bounds[1][1] = Number(site.lng);
							}
						}
					}
				}
				
				var marker = L.marker([item.geo[0], item.geo[1]] , {
					icon: this.options.iconPartner,
					icon: this.options.iconPartner,
					title: item.name,
					id: item.id,
					budget: budget
				});
				
				marker.on('click', function (e) { 
					if(self.options.useTooltip) { 
						self._triggerTipEvent(this.options.id);
					} else {
						self._triggerSheetEvent(this.options.id);
					}
				});
				
				this._clusters.addLayer(marker);
				
				if(Number(item.geo[0]) < this.bounds[0][0]) this.bounds[0][0] = Number(item.geo[0]);
				if(Number(item.geo[1]) < this.bounds[0][1]) this.bounds[0][1] = Number(item.geo[1]);
				if(Number(item.geo[0]) > this.bounds[1][0]) this.bounds[1][0] = Number(item.geo[0]);
				if(Number(item.geo[1]) > this.bounds[1][1]) this.bounds[1][1] = Number(item.geo[1]);
			}
		}
		
		if(!self.options.useTooltip) this.legendDemo.style.display = registeredSites.length == 0 ? 'none': 'inline-block';
		
		this._map.addLayer(this._clusters);
	}
	
	this.setMoneyClusters = function(data) {
		
		var self = this;
		var registeredSites = [];
		
		if(this._clusters) { 
			this._map.removeLayer(this._clusters);
			this._clusters.clearLayers(); 
		}
					
		this._clusters = new L.MarkerClusterGroup( {
			maxClusterRadius: 80,
			
			iconCreateFunction: function (cluster) {
				var markers = cluster.getAllChildMarkers();
				var items = markers.length;
				
				var n = 0;
				for (var i = 0; i < markers.length; i++) {
					n += markers[i].options.budget;
				}
				
				var className = '';
				var size = 0;
				var display = '0';
				
				if(n < 1000000) { 
					className = 'marker-cluster-medium';
					size = 40;
					display = Math.round(n / 1000) + 'K$';
				} else 
				if(n < 10000000) { 
					className = 'marker-cluster-large';
					size = 50;
					display = Math.round(n / 1000000) + 'M$';
				} else {
					className = 'marker-cluster-xlarge';
					size = 70;
					display = Math.round(n / 1000000) + 'M$';
				}
				
				return L.divIcon({ html: '<div><span>' + display + '</span></div>', className: 'marker-cluster nobold ' + className, iconSize: L.point(size, size) });
			}
		});
		
		for(var i=0; i < data.length; i++) { 
			var item = data[i];
			
			if(item.geo[0] != 0 || item.geo[1] != 0) {
				
				var isCoordinator = false;
				var budget = 0;
				
				for(var p=0; p < item.projects.length;p++) { 
					var project = item.projects[p];
					/*
					if(!isCoordinator && item.projects[p].role.toUpperCase() == 'COORDINATOR') { 
						isCoordinator = true;
					}
					*/
					budget += parseInt(item.projects[p].budget);
					
					if(project.sites.length > 0) {
					
						for(var s = 0 ; s < project.sites.length; s++) { 
							if(registeredSites.indexOf(project.sites[s].id) == -1) {
								var icon = L.icon({
									iconUrl: 'src/images/ooc/' + project.theme + '.png',
									iconSize: [25,25]
								});
								var marker = L.marker([project.sites[s].lat, project.sites[s].lng] , {
									icon: icon,
									title: project.title,
									id: project.id,
									budget: parseFloat(project.budget)
								});
								
								marker.on('click', function (e) { 
									self._triggerSiteEvent(this.options.id);
									
								});
								
								this._clusters.addLayer(marker);	
								
								registeredSites.push(project.sites[s].id);
							}
						
						}
					
					}
				}
				//if(this._filters.roles.length == 0 || this._filters.roles.indexOf('Partner') != -1 || this._filters.roles.indexOf('Coordinator') != -1) {
					var marker = L.marker([item.geo[0], item.geo[1]] , {
						icon: isCoordinator ? this.options.iconCoordinator: this.options.iconPartner,
						title: item.name,
						id: item.id,
						budget: budget
					});
					
					marker.on('click', function (e) { 
						self._triggerSheetEvent(this.options.id);
					});
					
					this._clusters.addLayer(marker);
				//}
			}
		}
		
		if(!self.options.useTooltip) this.legendDemo.style.display = registeredSites.length == 0 ? 'none': 'inline-block';
		
		this._map.addLayer(this._clusters);
		
		//this.redrawAreas();
	}
		
	this.hide = function( ) { 
		
		this._container.style.visibility = 'hidden';
		this._legend.style.visibility = 'hidden';
		this._zoomInButton.style.visibility = 'hidden';
		this._zoomOutButton.style.visibility = 'hidden';
		this._pinButton.style.visibility = 'hidden';
		this._moneyButton.style.visibility = 'hidden';
		if(this._tooltip) this._tooltip.style.visibility = 'hidden';
		//this._heatButton.style.visibility = 'hidden';
		/**/
	}
	
	this.show = function( ) { 
		
		this._container.style.visibility = 'visible';
		this._legend.style.visibility = 'visible';
		this._zoomInButton.style.visibility = 'visible';
		this._zoomOutButton.style.visibility = 'visible';
		this._pinButton.style.visibility = 'visible';
		this._moneyButton.style.visibility = 'visible';
		//this._heatButton.style.visibility = 'visible';
		/**/
	}
	
	this.setView = function(lat, lng, zoom) { 
		
		if(zoom == null) zoom = this._map.getZoom();
		
		if(this._map) this._map.setView(new L.LatLng(lat, lng),zoom);
		
		this.options.lat = lat;
		this.options.lon = lng;
	};
	
	this.setTooltip = function(type, data) { 
		var self = this;
		
		if(!this._tooltip) { 
			
			this._tooltip = document.createElement('div'); 
			this._tooltip.className = 'map-tooltip'; 
			
			this._tooltip_title = document.createElement('span');
			this._tooltip_title.className = 'title';
			this._tooltip.appendChild(this._tooltip_title);
			
			this._tooltip_city = document.createElement('span');
			this._tooltip_city.className = 'city';
			this._tooltip.appendChild(this._tooltip_city);
			
			this._tooltip_title.addEventListener('click', function(event) { self.openTipHandler(event); });
			this._container.parentNode.appendChild(this._tooltip);
			
		}
		
		this._tooltip_title.innerHTML = data.name;
		this._tooltip_city.innerHTML = data.city;
		this._tooltip_title.setAttribute('data-id', data.id);
		this._tooltip.style.visibility = 'visible';
	}
	
	this.refresh = function(data, filters, completed) { 
		
		var self = this;
		this._data = data;
		this._filters = filters;
		
		if(completed) {
			this.regions = {};
						
			for(var i = 0; i < this._data.length; i++) { 
				var id =  this._data[i].nuts != '' ? this._data[i].nuts: this._data[i].country;
				 				
				if(!this.regions.hasOwnProperty(id)) { 
					this.regions[id] = { 
						count: 1,
						budget: 0
					};
					for(var j = 0; j < this._data[i].projects.length; j++) { 
						this.regions[id].budget += +this._data[i].projects[j].request;
					}
					
				}
				else { 
					this.regions[id].count++;
					for(var j = 0; j < this._data[i].projects.length; j++) { 
						this.regions[id].budget += +this._data[i].projects[j].request;
					}
				}
								
			}
						
			this.showMode();
			
			//if(this._clusters) this._map.fitBounds(this._clusters.getBounds());
		}
	};
	
	this.redrawAreas = function() { 
		
		var m = 0;
		var self = this;
		for(var id in this.regions) { 
			m =  (this.mode == 'pins' ? Math.max(m,this.regions[id].count): Math.max(m,this.regions[id].budget));
		}
		var palette = [ '#3672c7', '#336abb', '#2f62af', '#2b58a1', '#274d90', '#224380', '#1e3870', '#1a2e61', '#162554', '#121c47','#121c47', ];
		
		this.nutsLayer.eachLayer(function (layer) {  
			
		  if(!self.regions.hasOwnProperty(layer.feature.id)) {    
			layer.setStyle({fillOpacity :0, weight: 0}) 
		  } else { 
			var color = self.mode == 'pins' ? Math.floor( self.regions[layer.feature.id].count * 10 / m ): Math.floor( self.regions[layer.feature.id].budget * 10 / m );
			layer.setStyle({fillColor: palette[color],fillOpacity : 0.6, weight: 1, color: '#aaaaaa'}) 
		  }
		});
	};
	this.openTipHandler = function(event) { 
		var tip = event.target;
		this._triggerSheetEvent(tip.getAttribute('data-id'));
	};
	
	this._triggerTipEvent = function(id) { 
		
		for(var i = 0; i < this._data.length; i++) { 
			if(this._data[i].id == id) { 
				this.setTooltip('partner', this._data[i]);
				return;
			}
		}
	};
	this._triggerSheetEvent = function(id) { 
		var event = new CustomEvent("displayParticipant", { 'detail': id });
		this._container.dispatchEvent(event);
	};
	this._triggerSiteEvent = function(id) { 
		var event = new CustomEvent("displayProject", { 'detail': id });
		this._container.dispatchEvent(event);
	};
	
	
	
	this._construct();
};

// Create the event
var event = new CustomEvent("serviceLoaded", { "detail": {"service": "map", "object": SmeMap}});

// Dispatch/Trigger/Fire the event
document.dispatchEvent(event);


