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
 * Manejar planillas en formato CSV, ODS y XLS
 *
 * Esta clase permite leer y generar planillas de cálculo
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-10-13
 */
final class Planilla {

	/**
	 * Lee una planilla de cálculo (CSV, ODS o XLS)
	 * @param archivo arreglo pasado el archivo (ejemplo $_FILES['archivo']) o bien la ruta hacia el archivo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-12
	 */
	public static function leer ($archivo) {
		// si lo que se paso fue la ruta del archivo se debe construir el arreglo con los datos del mismo (igual a arreglo $_FILES)
		if(!is_array($archivo)) {
			// parche: al hacer $archivo['tmp_name'] = $archivo no queda como array, por lo que uso un auxiliar para resetear a archivo
			$aux = $archivo;
			$archivo = null;
			$archivo['tmp_name'] = $aux;
                        unset($aux);
			$archivo['name'] = basename($archivo['tmp_name']);
			switch(strtolower(substr($archivo['name'], strrpos($archivo['name'], '.')+1))) {
				case 'csv': { $archivo['type'] = 'text/csv'; break; }
				case 'txt': { $archivo['type'] = 'text/plain'; break; }
				case 'ods': { $archivo['type'] = 'application/vnd.oasis.opendocument.spreadsheet'; break; }
				case 'xls': { $archivo['type'] = 'application/vnd.ms-excel'; break; }
				case 'xlsx': { $archivo['type'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'; break; }
			}
			$archivo['size'] = filesize($archivo['tmp_name']);
		}
		// en caso que sea archivo CSV
		if($archivo['type']=='text/csv' || $archivo['type']=='text/plain') {
			require(DIR.'/class/CSV.class.php');
			return CSV::leer($archivo['tmp_name']);
		}
		// en caso que sea archivo ODS
		if($archivo['type']=='application/vnd.oasis.opendocument.spreadsheet') {
			require(DIR.'/class/ODS.class.php');
			$ods = new ODS();
			$ods->abrir($archivo['tmp_name']);
			return $ods->leer();
		}
		// en caso que sea archivo XLS
		if($archivo['type']=='application/vnd.ms-excel') {
			require(DIR.'/class/XLS.class.php');
			return XLS::leer($archivo['tmp_name']);
		}
		// en caso que sea archivo XLSX
		if($archivo['type']=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
			require(DIR.'/class/XLS.class.php');
			return XLS::leerXLSX($archivo['tmp_name']);
		}
	}

	/**
	 *  Crea una planilla de cálculo a partir de un arreglo
	 * @param data Arreglo utilizado para generar la planilla
	 * @param id Identificador de la planilla
	 * @param formato extension de la planilla para definir formato (ods o xls)
	 * @param horizontal Indica si la hoja estara horizontalmente (true) o verticalmente (false)
	 * @todo Generar archivo ods y csv
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-10-13
	 */
	public static function generar ($data, $id, $formato = 'ods', $horizontal = true) {
		// en caso que sea archivo CSV
		if($formato == 'csv') {
			require(DIR.'/class/CSV.class.php');
			CSV::generar($data, $id);
		}
		// en caso que sea archivo ODS
		if($formato == 'ods') {
			require(DIR.'/class/ODS.class.php');
			ODS::generar($data, $id);
		}
		// en caso que sea archivo XLS
		if($formato == 'xls') {
			require(DIR.'/class/XLS.class.php');
			XLS::generar($data, $id, $horizontal);
		}
	}

}

?>
