var PinEditor = new Class({
	Implements: [ Options ],
	options: {
		editable: true,
		zoom: 4,
		osmUrl: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		osmAttrib: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
		pins: [],
		origin: null,
		
	},
	initialize: function(map, table, options) {
		
		this.table = table;
		this.markers = [];
		
		this.options.origin = new L.LatLng(50.845, 4.376);
		this.setOptions(options);
		this.map = new L.Map(map);
		this.map.addLayer(
			new L.TileLayer(this.options.osmUrl, {
				minZoom: 1, 
				maxZoom: 18,
				attribution: this.options.osmAttrib
			})
		);
		this.map.setView(this.options.origin, this.options.zoom);
			
		for(var i = 0; i < this.options.pins.length; i++) {
			
			var pin = this.options.pins[i];
			this.addMarker(pin.id, pin.name, pin.lat, pin.lng);
		}	
		
	},
	addMarker: function(id, name, lat, lng) {
		
		if(!id) id = 0;
		if(!name) name = '';
		if(!lat) lat = this.options.origin.lat;
		if(!lng) lng = this.options.origin.lng;
		
		var marker = new L.marker(new L.LatLng(lat, lng),{
			draggable: this.options.editable, 
			title: name,
			index: this.markers.length
		});
		marker.addTo(this.map);
		
		this.markers.push(marker);
		
		var group = new L.featureGroup(this.markers);
 		this.map.fitBounds(group.getBounds());
		
		if(this.options.editable) {
			marker.on('drag', this.onMove.bind(this));
			
			var tr = new Element('tr', { marker: this.markers.length-1});
			
			var td = new Element('td');
			td.grab(new Element('input', { type: 'hidden', name: 'site_id[]', value: id}));
			td.grab(new Element('input', { type: 'text', name: 'site_name[]', value: name}));
			tr.grab(td); 
			
			var td = new Element('td');
			td.grab(new Element('input', { type: 'number', name: 'site_lat[]', value: lat}))
			tr.grab(td); 
			
			var td = new Element('td');
			td.grab(new Element('input', { type: 'number', name: 'site_lng[]', value: lng}))
			tr.grab(td); 
			
			var td = new Element('td');
			var del = new Element('a', { href: '#', class: 'button remove', text: 'remove'});
			del.addEvent('click', this.onRemove.bind(this));
			td.grab(del);
			tr.grab(td); 
			
			this.table.getElement('tbody').grab(tr);
		}
	},
	removeMarker: function(index) {
		this.map.removeLayer(this.markers[index]);
	},
	onRemove: function(event) {
		event.stop();
		
		var target = event.target.getParent('tr');
		
		this.removeMarker(target.getProperty('marker'));
		
		target.destroy();
	},
	onMove: function(event) {
		
		var marker = event.target;	
		var rows = this.table.getElements('tbody > tr');
		
		for(var i = 0; i < rows.length; i++) { 
			rows[i].removeClass('selected');
		}
		
		var row = rows[marker.options.index];
		
		row.addClass('selected');
		row.getElement('input[name=site_lat[]]').value = Math.round(marker.getLatLng().lat * 10000) / 10000;
		row.getElement('input[name=site_lng[]]').value = Math.round(marker.getLatLng().lng * 10000) / 10000;
	}
});