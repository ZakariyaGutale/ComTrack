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
    Copyright 2021 European Commission
    
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
    Copyright 2021 European Commission
    
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

var DataHubMap = function(container, options) { 
	this.options = { 
		minZoom: 2,
		maxZoom: 24,
		height: 540,
		width: 1045,
		lat: 50.51,
		lon: 4.21,
		iconCoordinator: null,
		iconPartner: null,
		beneficiary: 0,
		useTooltip: false,
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
		
		var markerIcon = L.Icon.extend({
			options: {
				shadowUrl: '/src/images/marker-shadow.png',
				iconSize:     [30, 30], 
				shadowSize:   [30, 30], 
				iconAnchor:   [15, 15],
				shadowAnchor: [12, 13]
			}
		});
		
		this.options.iconPartner =  new markerIcon({iconUrl: '/src/images/partner-sample.png'});
		this.options.iconCoordinator = new markerIcon({iconUrl: '/src/images/coordinator-sample.png'});
		
		if(options) { 
			for(var key in options) {
				if(this.options.hasOwnProperty(key)) { this.options[key] = options[key]; }
			}
		}
				
		
		this._map = new L.Map(this._container, { 
			"center": [47, 12],
    		"zoom": 1,
			"background" : "osmec",
		});
		
		this._clusters = null;
		this.markers = null;
		this._map.setView(new L.LatLng(this.options.lat, this.options.lon),3);
		this._map._layersMaxZoom = 15;
		
		this.nutsLayer = L.geoJson(world, {
			style: {weight: 0, fillOpacity: 0},
			
		}).addTo(this._map);
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
		this.legendSite = item;
		
		this._container.parentNode.style.position = 'absolute';
		this._container.parentNode.style.border = 'none';
		this._container.parentNode.parentNode.style.position = 'absolute';
		this._container.parentNode.parentNode.appendChild(this._legend);
		
		this._pinButton = document.createElement('div');
		this._pinButton.id = 'button-pin';
		this._pinButton.innerHTML = "";
		this._pinButton.title = "Show Participants";
		this._pinButton.addEventListener('click', function() {  
			self.mode = 'pins';
			self.showMode();
		});
		this._legend.appendChild(this._pinButton);
		
		this._moneyButton = document.createElement('div');
		this._moneyButton.id = 'button-money';
		this._moneyButton.innerHTML = "";
		this._moneyButton.title = "Show Money Allocated";
		this._moneyButton.addEventListener('click', function() {  
			self.mode = 'money';
			self.showMode();
		});
		this._legend.appendChild(this._moneyButton);
		
		this.heatIsOn = false;
		
		this.mode = 'pins';
		
		this._zoomInButton = document.createElement('div');
		this._zoomInButton.id = 'button-zoom-in';
		this._zoomInButton.innerHTML = "+";
		this._zoomInButton.title = "Zoom in";
		this._zoomInButton.addEventListener('click', function() { self._map.zoomIn(); });
		this._legend.appendChild(this._zoomInButton);
		
		this._zoomOutButton = document.createElement('div');
		this._zoomOutButton.id = 'button-zoom-out';
		this._zoomOutButton.innerHTML = "-";
		this._zoomOutButton.title = "Zoom out";
		this._zoomOutButton.addEventListener('click', function() { self._map.zoomOut(); });
		this._legend.appendChild(this._zoomOutButton);
		
		this.hide();
		
		$wt._queue("next"); // process next components
	};
	
	this.showMode = function() { 
		
		switch(this.mode) { 
			case 'pins':
				this._pinButton.className = 'selected';
				this._moneyButton.className = '';
				this.setPinClusters();
				
				break;
			case 'money': 
				this._pinButton.className = '';
				this._moneyButton.className = 'selected';
				this.setMoneyClusters(this._data);
				break;
		}
		
		
		this._map.fitBounds(this.bounds);
		this._map.fitBounds(this.bounds);
	}
	
	this.setPinClusters = function() {
		
		var self = this;
		var registeredSites = [];
		
		this.bounds = [
			[90, 180],
			[-90,-180]
		];
		
		var hasSites = false;
		
		if(this.markers) { 
			this.markers.remove(); 
		}
		
		var collection = { 
			type: "FeatureCollection",
			features: [],
		};
		
		for(var i = 0; i < this._data.length; i++) { 
			var item = this._data[i];
			
			var coordinator = false;
			
			for(var j=0; j < item.projects.length; j++) { 
				
				//if(item.projects[j].role.toUpperCase() == 'COORDINATOR') coordinator = true;	
				
				
				for(var d=0; d < item.projects[j].sites.length; d++) {
					var site = item.projects[j].sites[d];
					
					if(registeredSites.indexOf(site.id) == -1 && Math.abs(site.lat) <= 90 && Math.abs(site.lng) <= 180) {
						collection.features.push( {
							properties: {
								name: site.name,
								country: '', //site.nuts,
								city: '',
								description: "",
								type: "site",
								id: item.projects[j].id
							},
							type: "Feature",
							geometry:{
								type: "Point",
								coordinates: [Number(site.lng), Number(site.lat)]
							}
						});
						
						if(Number(site.lat) < this.bounds[0][0]) this.bounds[0][0] = Number(site.lat);
						if(Number(site.lng) < this.bounds[0][1]) this.bounds[0][1] = Number(site.lng);
						if(Number(site.lat) > this.bounds[1][0]) this.bounds[1][0] = Number(site.lat);
						if(Number(site.lng) > this.bounds[1][1]) this.bounds[1][1] = Number(site.lng);
						registeredSites.push(site.id);
					}
					
					hasSites = true;
				}
			}
			collection.features.push( {
				properties: {
					name: item.name,
					country: item.country,
					city: item.city,
					description: "",
					type: "partner",
					id: item.id
				},
				type: "Feature",
				geometry:{
					type: "Point",
					coordinates: [Number(item.geo[1]), Number(item.geo[0])]
				}
			});
			
			if(Number(item.geo[0]) < this.bounds[0][0]) this.bounds[0][0] = Number(item.geo[0]);
			if(Number(item.geo[1]) < this.bounds[0][1]) this.bounds[0][1] = Number(item.geo[1]);
			if(Number(item.geo[0]) > this.bounds[1][0]) this.bounds[1][0] = Number(item.geo[0]);
			if(Number(item.geo[1]) > this.bounds[1][1]) this.bounds[1][1] = Number(item.geo[1]);
		}
		
		this.legendSite.style.display = hasSites ? 'inline-block': 'none';
		
		this.markers = L.wt.markers( collection, {
			color: "#c0c0c0",
			icon: function() { return L.icon({
				iconUrl: '/src/images/marker-icon-red.png',
				shadowUrl: '/src/images/marker-shadow.png',
			
				iconSize:     [38, 95], // size of the icon
				shadowSize:   [50, 64], // size of the shadow
				iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
				shadowAnchor: [4, 62],  // the same for the shadow
				popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
			})},
			properties  : {
				name:"name",
				country: "country",
				description:"description",
			},
			
			cluster : {
				radius: 120,
				margin: 20,
				disableClusteringAtZoom: 8,	
				small: 40,
				medium: 50,
				large: 60,
				icon: function( cluster, color ) {
					var z = 40;
						z = (cluster.population >= 10) ? 50 : z;
						z = (cluster.population >= 15) ? 60 : z;
						z = (cluster.population >= 25) ? 70 : z;
					
					return new L.DivIcon({
						html: "<div style='background-color:rgba(255,255,255,0.7);color:rgb(42,42,42);border:3px solid rgba(42,42,42,0.5);-webkit-background-clip: padding-box;background-clip: padding-box;font-size:20px; height:"+z+"px;width:"+z+"px;' class='cluster'><span>" + cluster.population + "</span></div>",
						className: "prunecluster",
						iconSize: L.point(z, z)
					});

				},
				by: function( feature ){
					
					return {
					  name : feature.properties.country,
					  color: feature.properties.type == 'partner' ? '#55cc55': feature.properties.type == 'site' ? '#5555cc':'#cc5555',
					  
					};
				},
				
				category: function( feature ) {
					
					return feature.properties.country;
				} 
			},
			onEachFeature:function( feature, layer ){
				
				
				layer.on({
					click: function(e) { 
						var type = e.target.data.feature.properties.type;
						var props = e.target.data.feature.properties;
						
						switch(type) { 
							case 'partner':
							case 'coordinator': 
								self._callBeneficiaryDetail(props);
								break;
							case 'site': 
								self._callProjectDetail(props);
								break;
						}
						return false;
					}
				});
		  }
		}).addTo(this._map);
		var marker = this.markers._markerLayer._markersCluster[0]
		
	}
	
	this.setMoneyClusters = function(data) {
		
		var self = this;
		
		this.bounds = [
			[90, 180],
			[-90,-180]
		];
		
		var hasSites = false;
		
		if(this.markers) { 
			this.markers.remove(); 
		}
		
		var collection = { 
			type: "FeatureCollection",
			features: [],
		};
		
		for(var i = 0; i < this._data.length; i++) { 
			var item = this._data[i];
			
			var coordinator = false;
			var money = 0;
			
			for(var j=0; j < item.projects.length; j++) { 
				
				money += Number(item.projects[j].request)
				
				if(item.projects[j].role.toUpperCase() == 'COORDINATOR') coordinator = true;	
				
			}
			if(money > 0) {
				collection.features.push( {
					properties: {
						name: item.name,
						country: item.country,
						description: "",
						type: coordinator ? "coordinator": "partner",
						id: item.id,
						money: money
					},
					type: "Feature",
					geometry:{
						type: "Point",
						coordinates: [Number(item.geo[1]), Number(item.geo[0])]
					}
				});
			}
			if(Number(item.geo[0]) < this.bounds[0][0]) this.bounds[0][0] = Number(item.geo[0]);
			if(Number(item.geo[1]) < this.bounds[0][1]) this.bounds[0][1] = Number(item.geo[1]);
			if(Number(item.geo[0]) > this.bounds[1][0]) this.bounds[1][0] = Number(item.geo[0]);
			if(Number(item.geo[1]) > this.bounds[1][1]) this.bounds[1][1] = Number(item.geo[1]);
		}
		
		this.legendSite.style.display = hasSites ? 'inline-block': 'none';
		
		this.markers = L.wt.markers( collection, {
			color: "#c0c0c0",
			properties  : {
				name:"name",
				country: "country",
				description:"description",
			},
			
			cluster : {
				radius: 120,
				margin: 20,
				disableClusteringAtZoom: 8,	
				small: 40,
				medium: 50,
				large: 60,
				icon: function( cluster, color ) {
					var total = 0;
					for(var i = 0; i < cluster._clusterMarkers.length; i++) { 
						total += Number(cluster._clusterMarkers[i].feature.properties.money);
					}
					
					var z = 40;
						z = (total >= 1000000) ? 50 : z;
						z = (total >= 10000000) ? 60 : z;
						z = (total >= 100000000) ? 70 : z;
					
					
					var display = total > 1000000 ? Math.round(total / 1000000) + 'M€': Math.round(total / 1000) + 'K€'
					return new L.DivIcon({
						html: "<div style='background-color:rgba(255,255,255,0.7);color:rgb(42,42,42);border:3px solid rgba(42,42,42,0.5);-webkit-background-clip: padding-box;background-clip: padding-box;font-size:14px; height:"+z+"px;width:"+z+"px;' class='cluster'><span>" + display + "</span></div>",
						className: "prunecluster",
						iconSize: L.point(z, z)
					});

				},
				by: function( feature ){
					
					return {
					  name : feature.properties.country,
					  color: feature.properties.type == 'partner' ? '#55cc55': feature.properties.type == 'site' ? '#5555cc':'#cc5555',
					};
				},
				
				category: function( feature ) {
					
					return feature.properties.country;
				} 
			},
			onEachFeature:function( feature, layer ){
				layer.on({
					click: function(e) { 
						var type = e.target.data.feature.properties.type;
						var props = e.target.data.feature.properties;
						
						switch(type) { 
							case 'partner':
							case 'coordinator': 
								self._callBeneficiaryDetail(props);
								break;
							case 'site': 
								self._callProjectDetail(props);
								break;
						}
						return false;
					}
				});
		  }
		}).addTo(this._map);
	}
		
	this.hide = function( ) { 
		this._container.parentNode.parentNode.style.visibility = 'hidden';
		if(this._tooltip) { 
			this._tooltip.style.visibility = 'hidden';
		}
	}
	
	this.show = function( ) { 
		this._container.parentNode.parentNode.style.visibility = 'visible';
	}
	
	this.setView = function(lat, lng, zoom) { 
		
		if(zoom == null) zoom = this._map.getZoom();
		
		if(this._map) this._map.setView(new L.LatLng(lat, lng),zoom);
		
		this.options.lat = lat;
		this.options.lon = lng;
	};
	
	this.refresh = function(data, filters, completed) { 
		var self = this;
		
		this._data = data;
		
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
	
	this.createTooltip = function() { 
		var self = this;
		this._tooltip = document.createElement('div'); 
		this._tooltip.className = 'map-tooltip'; 
		this._tooltip.setAttribute('data-type', '');
		this._tooltip.setAttribute('data-id', '');
		
		
		this._tooltip_title = document.createElement('span');
		this._tooltip_title.className = 'title';
		this._tooltip.appendChild(this._tooltip_title);
		
		this._tooltip_city = document.createElement('span');
		this._tooltip_city.className = 'city';
		this._tooltip.appendChild(this._tooltip_city);
		
		this._tooltip_title.addEventListener('click', function(event) { 
			event.stop();
			console.log(self._tooltip.getAttribute('data-id'))
			if(self._tooltip.getAttribute('data-type') == 'site') { 
				self._triggerProjectSheetEvent(self._tooltip.getAttribute('data-id'));
			} else { 
				self._triggerBeneficiarySheetEvent(self._tooltip.getAttribute('data-id'));
			}
		});
		this._container.parentNode.appendChild(this._tooltip);
		
	};
	
	this._callBeneficiaryDetail = function(data) { 
		
		if(this.options.useTooltip) {
			if(!this._tooltip) { 
				this.createTooltip();
			}
			this._tooltip_title.innerHTML = data.name;
			this._tooltip_city.innerHTML = data.city;
			this._tooltip.style.visibility = 'visible';
			this._tooltip.setAttribute('data-type', 'partner');
			this._tooltip.setAttribute('data-id', data.id);
			
		} else {
			this._triggerBeneficiarySheetEvent(data.id);		
		}
	};

	this._triggerBeneficiarySheetEvent = function(id) { 
		var event = new CustomEvent("displayParticipant", { 'detail': id });
		this._container.dispatchEvent(event);
	};
	
	this._callProjectDetail = function(data) { 
		
		if(this.options.useTooltip) {
			if(!this._tooltip) { 
				this.createTooltip();
			}
			this._tooltip_title.innerHTML = data.name;
			this._tooltip_city.innerHTML = data.city;
			this._tooltip.style.visibility = 'visible';
			this._tooltip.setAttribute('data-type', 'site');
			this._tooltip.setAttribute('data-id', data.id);
		} else {
			this._triggerProjectSheetEvent(data.id);
		}
	};
	
	this._triggerProjectSheetEvent = function(id) { 
		var event = new CustomEvent("displayProject", { 'detail': id });
			this._container.dispatchEvent(event);
	};
	
	
	this._construct();
};

// Create the event
var event = new CustomEvent("serviceLoaded", { "detail": {"service": "map", "object": DataHubMap}});

// Dispatch/Trigger/Fire the event
document.dispatchEvent(event);
