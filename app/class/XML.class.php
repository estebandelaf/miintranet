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

/**
 * Clase para el manejo de XML
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-23
 */
final class XML {
	
	private $titulo; ///< titulo para el archivo xml
	
	/**
	 * Constructor de la clase XML
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-23
	 */
	final public function __construct () {
		$this->titulo = '';
	}

	/**
	 * Destructor de la clase XML
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-23
	 */
	final public function __destruct() {
		unset($this->pdf);
	}

	/**
	 * Asigna el atributo titulo
	 * @param titulo Título a setear
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-23
	 */
	final public function setTitulo ($titulo) {
		$this->titulo = $titulo;
	}
	
	/**
	 * Genera un archivo XML con una tabla dentro
	 * @param data Arreglo con los datos para generar la tabla
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-23
	 */
	final public function generar ($tabla) {
		
		// generar titulo ths
		$ths = '';
		$titulos = array_shift($tabla);
		foreach($titulos as $th)
			$ths .= MiSiTiO::generar('exportar/xml/th.xml', array('th'=>$th));
		// generar tabla xml
		$trs = '';
		foreach($tabla as &$fila) {
			$tds = '';
			foreach($fila as &$celda)
				$tds .= MiSiTiO::generar('exportar/xml/td.xml', array(
					'td'=>str_replace('<br />', "\n", $celda)
				));
			$trs .= MiSiTiO::generar('exportar/xml/tr.xml', array('tds'=>$tds));
		}
		$tabla = MiSiTiO::generar('exportar/xml/table.xml', array(
			'ths'=>$ths,
			'trs'=>$trs
		));
		
		// generar documento xml
		echo MiSiTiO::generar('exportar/xml/xml.xml', array(
			'url'=>$_SERVER['SERVER_NAME'],
			'type'=>'table',
			'content'=>$tabla
		));

	}

	/**
	 * Lee un archivo XML
	 * @return Archivo XML parseado
	 * @todo Crear método que lea el XML y lo devuelva
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-23
	 */
	public static function leer () {
		$xml = null;
		return $xml;
	}
	
}

?>
