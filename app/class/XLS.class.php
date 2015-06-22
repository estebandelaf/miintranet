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

// desactivar Strict Mode (errores con la clase de Excel)
ini_set('error_reporting', E_ALL);

/**
 * Manejar archivos en excel
 *
 * Esta clase permite leer y generar archivos en excel
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-05
 */
final class XLS {

	/**
	 * Lee una planilla de cálculo
	 * @param archivo archivo a leer (ejemplo celda tmp_name de un arreglo $_FILES)
         * @param hoja Hoja que se quiere devolver, comenzando por la 0
	 * @todo Parchar clase Spreadsheet_Excel_Reader y quitar el parche de este método
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-12
	 */
	public static function leer ($archivo = null, $hoja = 0) {
		require(DIR.'/class/other/excel_reader.php');
		$xls = new Spreadsheet_Excel_Reader();
		$xls->read($archivo);
                // recupear solo la hoja de interes
                $hoja = $xls->sheets[$hoja]['cells'];
                // limpiar memoria
                unset($archivo, $xls);
		// FIXME: TERRIBLE PARCHE!!
		// Spreadsheet_Excel_Reader cuando lee un excel si hay una celda vacia no
		// crea la "casilla" en el arreglo, por lo cual una fila de 10 celdas si hay 2
		// vacias en el arreglo generado tendria solo 8 celdas (pero si respeta el indice)
		// por lo cual se buscan las celdas/indices que faltan en cada fila (asumiendo que
		// la primera siempre estara completa) y se rellena el arreglo
		$filas = count($hoja);
		$columnas = count($hoja[1]);
		for($i=2; $i<=$filas; ++$i) {
			for($j=1;$j<=$columnas; ++$j) {
				if(!isset($hoja[$i][$j]))
					$hoja[$i][$j] = '';
			}
			// se debe ordenar el arreglo por indices, ya que lo anterior agrego las
			// celdas al final del arreglo
			ksort($hoja[$i]);
		}
		// FIN DEL TERRIBLE PARCHE!!
		return $hoja;
	}

	/**
	 * Lee una planilla de cálculo en formato XLSX
	 * @param archivo archivo a leer (ejemplo celda tmp_name de un arreglo $_FILES)
	 * @todo Implementar metodo (se uso alguna vez PHPExcel pero daba problemas con archivos grandes)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-02-16
	 */
	public static function leerXLSX ($archivo = null) {
		return null;
	}

	/**
	 * Crea una planilla de cálculo a partir de un arreglo, requiere clase
         * Spreadsheet_Excel_Writer del repositorio de Pear
	 * @param tabla Arreglo utilizado para generar la planilla
	 * @param id Identificador de la planilla
	 * @param horizontal Indica si la hoja estara horizontalmente (true) o verticalmente (false)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-05
	 */
	public static function generar ($tabla, $id, $horizontal = true) {
		
		// codificar de utf8 a iso8859-1
		foreach($tabla as &$fila)
			foreach($fila as &$columna)
				$columna = utf8_decode($columna);
		
		require('/usr/share/php/Spreadsheet/Excel/Writer.php');
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setVersion(8); // Use Excel97/2000 Format
		$worksheet =& $workbook->addWorksheet($id);
		$worksheet->setPaper(1); // leter
		if($horizontal)	$worksheet->setLandscape(); // horizontal
		else $worksheet->setPortrait(); // vertical
		$worksheet->centerHorizontally();
		$worksheet->setMargins(0.4); // poner margen a 1.02 cm
		$worksheet->hideGridlines(); // eliminar lineas negras
		// inicio de la tabla (margen superior e izquierdo)
		$y=0; // fila
		$x=0; // columna
		// cargar datos
		$fmt = $workbook->addFormat(array('TextWrap' => 1)); // para permitir saltos de linea
		foreach($tabla as &$fila) {
			foreach($fila as &$celda)
				$worksheet->write($y, $x++, str_replace('<br />', "\n", $celda), $fmt);
			$x=0;
			++$y;
		}
		// enviar HTTP headers
		$workbook->send($id.'.xls');
		// enviar archivo
		$workbook->close();
	}

}

?>
