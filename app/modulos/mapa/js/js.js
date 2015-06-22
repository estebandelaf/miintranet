/**
 * Clase para manejar un mapa
 * Utiliza OpenLayers
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-04
 */
function Mapa (div) {
	this.map = null; ///< Objeto OpenLayers.Map
	this.zoom = null; ///< Zoom por defecto del mapa
	/**
	 * Obtener objeto LonLat acorde para utilizar en el mapa a
	 * partir de las coordenadas Longitud y Latitud
	 * @param lon Longitud
	 * @param lat Latitud
	 * @return OpenLayers.LonLat
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-02
	 */
	this.LonLat = function (lon, lat) {
		return new OpenLayers.LonLat(lon, lat).transform(
			new OpenLayers.Projection("EPSG:4326"),
			this.map.getProjectionObject()
		);
	};
	/**
	 * Define el centro del mapa
	 * @param lon Longitud
	 * @param lat Latitud
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-04
	 */
	this.setCenter = function (lon, lat) {
		if(this.zoom == null) this.zoom = 16; // zoom por defecto
		else this.zoom = this.map.getZoom();
		this.map.setCenter(this.LonLat(lon, lat), this.zoom);
	};
	/**
	 * Constructor de la clase
	 * @param div ID del div donde se pondrá el mapa
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-04
	 */
	this.__constructor = function(div) {
		// crear mapa usando OpenLayers
		this.map = new OpenLayers.Map(div);
		// agregar controles
		this.map.addControl(new OpenLayers.Control.LayerSwitcher({'ascending':false}));
		this.map.addControl(new OpenLayers.Control.OverviewMap());
		this.map.addControl(new OpenLayers.Control.KeyboardDefaults());
		this.map.addControl(new OpenLayers.Control.ScaleLine());
		this.map.addControl(new OpenLayers.Control.Navigation());
		//this.map.addControl(new OpenLayers.Control.MousePosition());
		// agregar layers que se utilizarán
		var l_osm = new OpenLayers.Layer.OSM("OpenStreetMap.org");
		l_osm.attribution = ""; // originalmente: Map Data CC-BY-SA Openstreetmap.org, pero molestaba a los popup
		this.map.addLayer(l_osm);
		// en realidad las de google no son necesarias, pero están por si el usuario desea cambiar
		this.map.addLayer(new OpenLayers.Layer.Google("Google Physical",{type: google.maps.MapTypeId.TERRAIN}));
		this.map.addLayer(new OpenLayers.Layer.Google("Google Streets",{numZoomLevels: 20}));
		this.map.addLayer(new OpenLayers.Layer.Google("Google Hybrid",{type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}));
		this.map.addLayer(new OpenLayers.Layer.Google("Google Satellite",{type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}));
	};
	this.__constructor(div);
}

/**
 * Clase para colocar un intem (marker y popup) en el mapa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-04
 */
MapaItem = function (mapa, lon, lat) {
	this.mapa = mapa; ///< Objeto Mapa
	this.markers = null; ///< Objeto OpenLayers.Layer.Markers
	this.marker = null; ///< Objeto OpenLayers.Marker
	this.popup = null; ///< Objeto OpenLayers.Popup
	/**
	 * Crea un marcador para el mapa, si ya existe lo borra y crea
	 * uno nuevo
	 * @param lon Longitud
	 * @param lat Latitud
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-04
	 */
	this.createMarker = function (lon, lat) {
		if(this.markers==null) {
			this.markers = new OpenLayers.Layer.Markers("markers");
			this.mapa.map.addLayer(this.markers);
		} else {
			this.markers.clearMarkers();
			this.marker.erase();
			this.marker.destroy();
		}
		this.marker = new OpenLayers.Marker(this.mapa.LonLat(lon, lat));
		this.markers.addMarker(this.marker);
	}
	/**
	 * Crea un nuevo popup para el mapa
	 * @param lon Longitud
	 * @param lat Latitud
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-04
	 */
	this.createPopup = function (lon, lat) {
		this.popup = new OpenLayers.Popup("popup", this.mapa.LonLat(lon, lat));
		this.popup.setBackgroundColor("white");
		this.popup.setOpacity(.8);
		this.popup.setBorder("1px solid");
		this.popup.autoSize = true;
		this.popup.padding = 5;
		this.popup.addCloseBox();
		this.popup.setContentHTML('Cargando información...');
		this.mapa.map.addPopup(this.popup);
	}
	/**
	 * Coloca el contenido (html) al popup
	 * @param html
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-04
	 */
	this.setContentHTML = function (html) {
		this.popup.setContentHTML(html);
	};
	/**
	 * Mueve el marker y el popup a la nueva coordenada
	 * @param lon Longitud
	 * @param lat Latitud
	 * @warning No actualiza la posición de this.marker
	 * @todo Corregir warning
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-04
	 */
	this.moveTo = function (lon, lat) {
		this.popup.lonlat = this.mapa.LonLat(lon, lat);
		this.popup.updatePosition();
		//this.marker.moveTo(this.map.getLayerPxFromLonLat(this.mapa.LonLat(lon, lat)));
		//this.marker.moveTo(this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(lon, lat)));
		this.createMarker(lon, lat);
	};
	/**
	 * Constructor de la clase
	 * @param lon Longitud
	 * @param lat Latitud
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-04
	 */
	this.__construct = function (lon, lat) {
		// agregar marcador
		this.createMarker(lon, lat);
		// agregar popup con la info de la coordenada
		this.createPopup(lon, lat);
	};
	this.__construct(lon, lat);
}

// variable globale para el item de geoposicionamiento y actualizacion
// esto se hizo necesario ya que no se como pasar por el setInterval
// la variable, al probar con como se hizo con id no funciono
var item;

/**
 * Muestra en un mapa las coordenadas indicadas (usando OpenLayers)
 * @param id Identificador a mostrar ubicación
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-04
 */
function mostrarGeoposicionamiento (id) {
	var mapa = new Mapa('map');
	var geoposicionamiento = getJSON('/mapa/geoposicionamiento_get', {id:id});
	mapa.setCenter(geoposicionamiento.longitud, geoposicionamiento.latitud);
	item = new MapaItem(mapa, geoposicionamiento.longitud, geoposicionamiento.latitud);
	item.popup.minSize = new OpenLayers.Size(300, 50);
	item.popup.maxSize = new OpenLayers.Size(300, 150);
	// actualizar posición del elemento
	actualizarGeoposicionamiento(id);
}

/**
 * Recarga los datos de geoposicionamiento para un elemento
 * @param id Identificador a mostrar ubicación
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-04
 */
function actualizarGeoposicionamiento (id) {
	// recuperar informacion de geoposicionamiento para el id indicado
	geoposicionamiento = getJSON('/mapa/geoposicionamiento_get', {id:id});
	// mover item/mapa
	item.mapa.setCenter(geoposicionamiento.longitud, geoposicionamiento.latitud);
	item.moveTo(geoposicionamiento.longitud, geoposicionamiento.latitud)
	item.setContentHTML(
		'<div class="glosa">'+
			geoposicionamiento.glosa+
		'</div>'+
		'<div class="ubicacion">'+
			geocodeReverse(geoposicionamiento.longitud, geoposicionamiento.latitud)+
		'</div>'+
		'<div class="coordenadas">'+
			geoposicionamiento.longitud+'[Lon], '+geoposicionamiento.latitud+'[Lat]'+
		'</div>'
	);
	// programar para ser ejecutada en X segundos mas
	setTimeout("actualizarGeoposicionamiento('"+id+"')", 2000);
}

/**
 * Muestra en un mapa las coordenadas indicadas (usando OpenLayers)
 * @param lon Longitud
 * @param lat Latitud
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-03
 */
function mostrarCoordenadas (lon, lat) {
	var mapa = new Mapa('map');
	mapa.setCenter(lon, lat);
	var item = new MapaItem(mapa, lon, lat);
	item.popup.maxSize = new OpenLayers.Size(300, 150);
	item.setContentHTML('<div class="ubicacion">'+geocodeReverse(lon, lat)+'</div><div class="coordenadas">'+lon+'[Lon],'+lat+'[Lat]');
}

/**
 * Obtiene las coordenadas a partir de una ubicación
 * Ej: geocode('Echaurren 424, Santiago, Chile');
 * @param ubicacion Ubicación a buscar
 * @return Array Arreglo con indices lon y lat
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-02
 */
function geocode (ubicacion) {
	var json = getJSON('http://nominatim.openstreetmap.org/search', {format:'json',q:ubicacion});
	var coordenadas = new Array();
	coordenadas['lon'] = json[0].lon;
	coordenadas['lat'] = json[0].lat;
	return coordenadas;
}

/**
 * Obtiene la ubicación a partir de las coordenadas
 * Ej: geocodeReverse(-70.6661019346939, -33.4534492265306);
 * @param lon Longitud
 * @param lat Latitud
 * @return String Ubicación más cercada a las coordenadas
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-02
 */
function geocodeReverse (lon, lat) {
	var json = getJSON('http://nominatim.openstreetmap.org/reverse', {format:'json',lon:lon,lat:lat});
	return json.display_name;
}
