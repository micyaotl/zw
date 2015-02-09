google.load("maps", "2.x");
var points = [];
var marker = null;
var highlighted_marker = null;
var point_markers = [];
function irDireccion() {
		var address = $('#direccion').attr('value');
		if (address.length > 0) {
			var geocoder = new GClientGeocoder();
			geocoder.getLatLng(address,
					function(point) {
						if (!point) {
							alert('Direccion "' + address + '" no encontrada');
						} else {
							document.map.setCenter(point, 15);
						}
					} );
		}
	}

function displayPoint(marker, index){
	$("#message").hide();
	var moveEnd = GEvent.addListener(document.map, "moveend", function(){
		var markerOffset = map.fromLatLngToDivPixel(marker.getLatLng());
		$("#message")
			.fadeIn()
			.css({ top:markerOffset.y, left:markerOffset.x });
	
		GEvent.removeListener(moveEnd);
	});
	map.panTo(marker.getLatLng());
}

function vLgr(nom, adr, pnt, tip, txt) {
	var icn = new GIcon();
	/*icn.image = "http://maps.google.com/mapfiles/ms/icons/"+tip+".png";
	icn.shadow = "http://maps.google.com/mapfiles/ms/icons/"+tip+".shadow.png";
	icn.iconSize = new GSize(32, 32);
	icn.shadowSize = new GSize(59, 32);
	icn.iconAnchor = new GPoint(16, 16);
	icn.infoWindowAnchor = new GPoint(16, 16);
	icn.infoShadowAnchor = new GPoint(18, 25);*/
	icn.image = "http://labs.google.com/ridefinder/images/mm_20_red.png";
	icn.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
	icn.iconSize = new GSize(12,20);
	icn.shadowSize = new GSize(22,20);
	icn.iconAnchor = new GPoint(6,20);
	icn.infoWindowAnchor = new GPoint(6,1);
	icn.infoShadowAnchor = new GPoint(13,13);
	var lgr = new GMarker(pnt, icn);
	if(nom != '') {
		if(adr != '') {
			var dir = "<div align=\"right\"><em>"+adr+"</em></div>";
		} else {
			var dir = '';
		}
		GEvent.addListener(lgr, "click", function() {
			GInfoWindowOptions.maxWidth(50);
			lgr.openInfoWindowHtml("<div style=\"padding:10px; margin:10px; width:150px;\"><h3>"+nom+"</h3>"+dir+txt+"</div>"); 
			});
	}
	return lgr;
}

function createMarker(point, color) {
	var f = new GIcon();
	f.image = "http://labs.google.com/ridefinder/images/mm_20_" + color + ".png";
	f.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
	f.iconSize = new GSize(12,20);
	f.shadowSize = new GSize(22,20);
	f.iconAnchor = new GPoint(6,20);
	f.infoWindowAnchor = new GPoint(6,1);
	f.infoShadowAnchor = new GPoint(13,13);

	marker = new GMarker(point,{draggable:true});
	return marker;
}

$(document).ready(function() {
	document.map = new GMap2($("#map").get(0));
	var tulumMX = new GLatLng(20.215009,-87.451469);
	/*var bounds = map.getBounds();
	var southWest = bounds.getSouthWest();
	var northEast = bounds.getNorthEast();
	var lngSpan = northEast.lng() - southWest.lng();
	var latSpan = northEast.lat() - southWest.lat();
	var markers = [];*/
	document.map.setCenter(tulumMX, 8);
	document.map.addControl(new GLargeMapControl3D());
	document.map.addControl(new GMapTypeControl());
	//document.map.addControl(new GOverviewMapControl());
	document.map.addControl(new GScaleControl());
	//document.map.setMapType(G_HYBRID_MAP);

	GEvent.addListener(document.map, "click", function(overlay, point) {
		$('#lat').attr('value', point.y);
		$('#lon').attr('value', point.x);
		if (marker == null) {
			marker = createMarker(point, 'green');
			//marker.enableDragging();
			GEvent.addListener(marker, "dragend", function() {
				$('#lat').attr('value', marker.getPoint().y);
				$('#lon').attr('value', marker.getPoint().x);
			});
			document.map.addOverlay(marker);
		} else {
			marker.setPoint(point);
		}
	});
	
	GDownloadUrl(siteurl+"xml/mapa", function(data, responseCode) {
		var xml = GXml.parse(data);
		var zihmap = xml.documentElement.getElementsByTagName("mark");
		for (var i = 0; i < zihmap.length; i++) {
			var nom = zihmap[i].getAttribute("nom");
			var dir = zihmap[i].getAttribute("dir");
			var pnt = new GLatLng(parseFloat(zihmap[i].getAttribute("lat")), parseFloat(zihmap[i].getAttribute("long")));
			var tip = zihmap[i].getAttribute("tip");
			var txt = zihmap[i].getAttribute("desc");
			var lgr = new vLgr(nom, dir, pnt, tip, txt);
			document.map.addOverlay(lgr);
		}
	});
});
//)(jQuery);

/*
for (var i = 0; i < 10; i++) {
    var point = new GLatLng(southWest.lat() + latSpan * Math.random(),
        southWest.lng() + lngSpan * Math.random());
	marker = new GMarker(point);
	map.addOverlay(marker);
	markers[i] = marker;
}

$(markers).each(function(i,marker){
	$("<li />")
		.html("Point "+i)
		.click(function(){
			displayPoint(marker, i);
		})
		.appendTo("#list");
	
	GEvent.addListener(marker, "click", function(){
		displayPoint(marker, i);
	});
});

$("#message").appendTo(map.getPane(G_MAP_FLOAT_SHADOW_PANE));
*/

