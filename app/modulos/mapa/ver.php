<?php

/**
 * MiInTrAnEt
 * Copyright (C) 2008-2011 Esteban De La Fuente Rubio (esteban@delaf.cl)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada
 * por la Fundación para el Software Libre, ya sea la versión 3
 * de la Licencia, o (a su elección) cualquier versión posterior de la misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General GNU para obtener
 * una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/gpl.html>.
 *
 */

require('../../inc/inc.php');

// buscar geocode a partir de la direccion
$xml = simplexml_load_file('http://nominatim.openstreetmap.org/search?format=xml&q='.$_GET['q']);

// procesar solo si se encontro algun geocode
if(count($xml->place)) {
	// extraer primer geocode encontrado
	foreach($xml->place as $place) {
		$lat = $place->attributes()->lat;
		$lon = $place->attributes()->lon;
		$display_name = $place->attributes()->display_name;
		break;
	}
	// mostrar mapa
	echo MiSiTiO::generar('ver.html', array(
		'lon'=>$lon
		, 'lat'=>$lat
		, 'ubicacion'=>$_GET['q']
		, 'display_name'=>$display_name
	));
}

?>
