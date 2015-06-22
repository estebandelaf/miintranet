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

require(DIR.'/class/other/class.ods_jlt.php');
require_once(DIR.'/class/Archivo.class.php');

/**
 * Manejo de planillas de cálculo de OpenDocumment
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-05
 */
final class ODS extends ODS_JLT {

	/**
	 * Constructor
	 *
	 * Setea atributos de la clase padre
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-16
	 */
	public function __construct () {
		$this->styles = array();
		$this->fonts = array();
		$this->sheets = array();
		$this->currentRow = 0;
		$this->currentSheet = 0;
		$this->currentCell = 0;
		$this->repeat = 0;
	}

	/**
	 * Abre una planilla y procesa el contenido XML
	 * @param archivo Archivo a abrir
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-16
	 */
	public function abrir ($archivo = null) {
		$aux = Archivo::ezip($archivo, 'content.xml');
		$this->parse($aux['data']);
		unset($archivo, $aux);
	}

	/**
	 * Lee el contenido de una hoja y lo devuelve como arreglo
	 * @param sheet Hoja a leer (0..n)
	 * @return Arreglo con los datos de la hoja (indices parten en 1)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-16
	 */
	public function leer ($sheet = 0) {
		// numero de filas y columnas
		$filas = count($this->sheets[$sheet]['rows']);
		$columnas = 0;
		foreach($this->sheets[$sheet]['rows'][0] as &$columna) {
			if(!isset($columna['value']))
				break;
			++$columnas;
		}
		// recorrer hoja de calculo
		$data = array();
		for($row=0; $row<$filas; ++$row) {
			$fila = array();
			for($cell=0; $cell<$columnas; ++$cell) {
				if(isset($this->sheets[$sheet]['rows'][$row][$cell]['value'])) {
					$fila[($cell+1)] = $this->sheets[$sheet]['rows'][$row][$cell]['value'];
				} else {
					$fila[($cell+1)] = '';
				}
			}
			$data[($row+1)] = $fila;
		}
		unset($sheet, $filas, $columnas, $columna, $row, $fila, $cell);
		return $data;
	}

	/**
	 *  Crea una planilla de cálculo a partir de un arreglo
	 * @param data Arreglo utilizado para generar la planilla
	 * @param id Identificador de la planilla
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-05
	 */
	public static function generar ($data, $id) {
		require(DIR.'/class/other/odsPhpGenerator/ods.php');
		$ods = new odsPhpGenerator();
		$table = new odsTable($id);
		foreach($data as &$fila) {
			$row = new odsTableRow();
			foreach($fila as &$celda)
				$row->addCell(new odsTableCellString(str_replace('<br />', "\n", $celda)));
			$table->addRow($row);
		}
		$ods->addTable($table);
		unset($data, $table, $fila, $celda, $row);
		$ods->downloadOdsFile($id.'.ods');
	}

}

?>
