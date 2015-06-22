<?php

/**
 * MiInTrAnEt
 * Copyright (C) 2008-2010 Esteban De La Fuente Rubio (esteban@delaf.cl)
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

/**
 * Manejar archivos csv
 *
 * Esta clase permite leer y generar archivos csv
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-05
 */
final class CSV {

	/**
	 * Lee un archivo CSV
	 * @param archivo archivo a leer (ejemplo celca tmp_name de un arreglo $_FILES)
	 * @param separador separador a utilizar para diferenciar entre una columna u otra
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-12
	 */
	public static function leer ($archivo = null, $separador = ';') {
		$i = 1;
		$lineas = file($archivo);
		foreach($lineas as &$linea) {
			// separar por columnas
			$aux = explode($separador, str_replace("\n", '', $linea));
                        unset($linea);
			// correr para que parta del indice 1
			$campos = count($aux);
			for($j=0; $j<$campos; $j++) {
				$data[$i][$j+1] = trim($aux[$j]); // trim quitara los espacios al inicio y final de la "celda"
			}
			// incrementar celda
			++$i;
                        // limpiar memoria
                        unset($aux, $campos);
		}
		return $data;
	}

	/**
	 * Crea un archivo CSV a partir de un arreglo
	 * @param data Arreglo utilizado para generar la planilla
	 * @param id Identificador de la planilla
	 * @param separador separador a utilizar para diferenciar entre una columna u otra
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-05
	 */
	public static function generar ($data, $id, $separador = ';') {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename='.$id.'.csv');
		header('Pragma: no-cache');
		header('Expires: 0');
		foreach($data as &$row) {
			foreach($row as &$col) {
				echo str_replace('<br />', ', ', $col),';';
			}
			echo "\n";
                        unset($row);
		}
                unset($data);
	}

}

?>
