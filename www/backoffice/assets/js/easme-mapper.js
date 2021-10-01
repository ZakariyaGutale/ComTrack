var cfg = { 
	minZoom: 8,
	maxZoom: 14,
	mapHeight: 480,
	mapWidth: 640
};

var map;
var ajaxRequest;
var plotlist;
var plotlayers=[];

function initmap(id, lat, lng) {
	// set up the map
	map = new L.Map(id);

	// create the tile layer with correct attribution
	var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
	var osm = new L.TileLayer(osmUrl, {minZoom: cfg.minZoom, maxZoom: cfg.maxZoom, attribution: osmAttrib});

	// position map
	map.setView(new L.LatLng(lat, lng),9);
	map.addLayer(osm);
}

function setView(lat, lng, zoom = 9) { 
	if(map) map.setView(new L.LatLng(lat, lng),zoom);
}

function setMarker(lat, lng, message) { 
	if(map) {
		var marker = L.marker([lat, lng]).addTo(map);
		marker.bindPopup(message).openPopup();
	}
}