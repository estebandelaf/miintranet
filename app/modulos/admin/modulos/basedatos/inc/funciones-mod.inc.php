<?php

/**
 * Esta método permite utilizar plantillas para la generacion de código fuente
 * @param archivoPlantilla Ruta hacia el archivo que contiene la plantilla (con extension)
 * @param variables Arreglo con las variables a reemplazar en la plantilla
 * @return String Plantilla ya formateada con las variables correspondientes
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-18
 */
function src ($archivoPlantilla, $variables = null) {

	// cargar plantilla
	$plantilla = file_get_contents($archivoPlantilla);

	// reemplazar variables en la plantilla
	if($variables) {
		foreach($variables as $key => $valor)
			$plantilla = str_replace('{'.$key.'}', $valor, $plantilla);
	}

	// retornar plantilla ya procesada
	return $plantilla;

}

?>
