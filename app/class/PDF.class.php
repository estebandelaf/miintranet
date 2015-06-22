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

// configuraciones
global $Parametros;
$Parametros->getByModulo('pdf');

require(DIR.'/class/other/pdf-ros/class.ezpdf.php');

/**
 * Clase para el manejo de PDF
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-06
 */
final class PDF {

	private $archivo; ///< Nombre del archivo para el pdf
	private $titulo; ///< Título del PDF
	private $tituloSize; /// Tamaño del título de la página
	private $hoja; ///< Tipo de hoja que se utilizara al general pdf
	private $orientacion; ///< Orientación del PDF (portrait o landscape)
	private $margen; ///< Margenes del PDF
	private $opcionesTablas; ///< Arreglo con las opciones para las tablas
	public $pdf; ///< Objeto con el pdf
	
	/**
	 * Constructor de la clase PDF
	 * @param archivo Nombre del archivo que se generará
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function __construct ($archivo) {
		$this->archivo = $archivo;
		$this->titulo = $archivo;
		$this->tituloSize = PDF_TITLE_SIZE;
		$this->hoja = PDF_PAGE_TYPE;
		$this->orientacion = PDF_ORIENTATION;
		list($top, $bottom, $left, $right) = explode(',', PDF_MARGIN);
		$this->margen = array('top'=>$top, 'bottom'=>$bottom, 'left'=>$left, 'right'=>$right);
		$this->opcionesTablas = array(
			'showLines'=>PDF_TABLE_SHOWLINES
			, 'fontSize'=>PDF_TABLE_FONTSIZE
			, 'titleFontSize'=>PDF_TABLE_TITLEFONTSIZE
			, 'shaded'=>PDF_TABLE_SHADED
			, 'shadeCol'=>explode(',', PDF_TABLE_SHADECOL)
			, 'shadeCol2'=>explode(',', PDF_TABLE_SHADECOL2)
			, 'textCol'=>explode(',', PDF_TABLE_TEXTCOL)
			, 'rowGap'=>PDF_TABLE_ROWGAP
			, 'colGap'=>PDF_TABLE_COLGAP
			, 'maxWidth'=>(PDF_PAGE_WIDTH - $this->margen['left'] - $this->margen['right'])
		);
	}

	/**
	 * Destructor de la clase PDF
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-04
	 */
	final public function __destruct() {
		unset($this->pdf);
	}

	/**
	 * Set para titulo
	 * @param titulo Título que se colocará al inicio del PDF
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-04
	 */
	final public function setTitulo ($titulo) {
		$this->titulo = $titulo;
	}
	
	/**
	 *
	 * @param type $size 
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-04
	 */
	final public function setTituloSize ($size) {
		$this->tituloSize = $size;
	}
	
	/**
	 *
	 * @param type $hoja 
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-04
	 */
	final public function setHoja ($hoja) {
		$this->hoja = $hoja;
	}
	
	/**
	 * Set para orientacion
	 * @param orientacion =portrait para vertical, =landscape para horizontal
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function setOrientacion ($orientacion) {
		if($orientacion=='portrait' || $orientacion=='landscape')
			$this->orientacion = $orientacion;
		else
			$this->orientacion = PDF_ORIENTATION;
	}
	
	/**
	 * Set para margen
	 * Si no existe un índice no se cambiará dicho margen
	 * @param margen Arreglo con los margenes a definir, indices: top, bottom, left y right
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-04
	 */
	final public function setMargen ($margen) {
		if(array_key_exists('top', $margen) && is_int($margen['top']) && $margen['top']>=0)
			$this->margen['top'] = (integer) $margen['top'];
		if(array_key_exists('bottom', $margen) && is_int($margen['bottom']) && $margen['bottom']>=0)
			$this->margen['bottom'] = (integer) $margen['bottom'];
		if(array_key_exists('left', $margen) && is_int($margen['left']) && $margen['left']>=0)
			$this->margen['left'] = (integer) $margen['left'];
		if(array_key_exists('right', $margen) && is_int($margen['right']) && $margen['right']>=0)
			$this->margen['right'] = (integer) $margen['right'];
	}
	
	/**
	 * Set para opcionesTablas
	 * Si no existe un índice no se cambiará dicha opción, si no se pasa
	 * un arreglo se colocaran las opciones por defecto (permite hacer reset)
	 * @param opciones Arreglo con las opciones para la tabla
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function setOpcionesTablas ($opciones = null) {
		if($opciones) {
			if(array_key_exists('showLines', $opciones) && is_int($opciones['showLines']) && $opciones['showLines']>=0)
				$this->opcionesTablas['showLines'] = (integer) $opciones['showLines'];
			if(array_key_exists('fontSize', $opciones) && is_int($opciones['fontSize']) && $opciones['fontSize']>=0)
				$this->opcionesTablas['fontSize'] = (integer) $opciones['fontSize'];
			if(array_key_exists('titleFontSize', $opciones) && is_int($opciones['titleFontSize']) && $opciones['titleFontSize']>=0)
				$this->opcionesTablas['titleFontSize'] = (integer) $opciones['titleFontSize'];
			if(array_key_exists('shaded', $opciones) && is_int($opciones['shaded']) && $opciones['shaded']>=0)
				$this->opcionesTablas['shaded'] = (integer) $opciones['shaded'];
			if(array_key_exists('shadeCol', $opciones) && is_int($opciones['shadeCol']) && $opciones['shadeCol']>=0)
				$this->opcionesTablas['shadeCol'] = (integer) $opciones['shadeCol'];
			if(array_key_exists('shadeCol2', $opciones) && is_int($opciones['shadeCol2']) && $opciones['shadeCol2']>=0)
				$this->opcionesTablas['shadeCol2'] = (integer) $opciones['shadeCol2'];
			if(array_key_exists('textCol', $opciones) && is_int($opciones['textCol']) && $opciones['textCol']>=0)
				$this->opcionesTablas['textCol'] = (integer) $opciones['textCol'];
			if(array_key_exists('rowGap', $opciones) && is_int($opciones['rowGap']) && $opciones['rowGap']>=0)
				$this->opcionesTablas['rowGap'] = (integer) $opciones['rowGap'];
			if(array_key_exists('colGap', $opciones) && is_int($opciones['colGap']) && $opciones['colGap']>=0)
				$this->opcionesTablas['colGap'] = (integer) $opciones['colGap'];
			if(array_key_exists('maxWidth', $opciones) && is_int($opciones['maxWidth']) && $opciones['maxWidth']>=0)
				$this->opcionesTablas['maxWidth'] = (integer) $opciones['maxWidth'];
		} else {
			$this->opcionesTablas = array(
				'showLines'=>PDF_TABLE_SHOWLINES
				, 'fontSize'=>PDF_TABLE_FONTSIZE
				, 'titleFontSize'=>PDF_TABLE_TITLEFONTSIZE
				, 'shaded'=>PDF_TABLE_SHADED
				, 'shadeCol'=>explode(',', PDF_TABLE_SHADECOL)
				, 'shadeCol2'=>explode(',', PDF_TABLE_SHADECOL2)
				, 'textCol'=>explode(',', PDF_TABLE_TEXTCOL)
				, 'rowGap'=>PDF_TABLE_ROWGAP
				, 'colGap'=>PDF_TABLE_COLGAP
				, 'maxWidth'=>(PDF_PAGE_WIDTH - $this->margen['left'] - $this->margen['right'])
			);
		}
	}

	/**
	 * Inicia el PDF, coloca titulos, header y footer
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function begin () {
		global $Usuario;
		// crear pdf con sus propiedades
		$this->pdf = new Cezpdf($this->hoja, $this->orientacion); // tamaño y orientacion
		$this->pdf->selectFont(DIR.'/class/other/pdf-ros/fonts/Helvetica.afm');
		$this->pdf->ezSetMargins($this->margen['top'], $this->margen['bottom'], $this->margen['left'], $this->margen['right']);
		// información del pdf
		$this->pdf->addInfo(array(
			'Title'=>$this->titulo,
			'Author'=>$Usuario->id,
			'Subject'=>$this->titulo,
			'Creator'=>AUDIT_PROGRAMA,
			'Producer'=>$_SERVER['SERVER_NAME']
		));
		// colocar logo de la empresa
		if(file_exists(DIR.'/img/logo_empresa.png')) {
			$logo = getimagesize(DIR.'/img/logo_empresa.png');
			$this->pdf->addPngFromFile(
				DIR.'/img/logo_empresa.png'
				, $this->margen['left']
				, PDF_PAGE_HEIGHT-$this->margen['top']-$logo[1]
				, $logo[0]
				, $logo[1]
			);
		} else {
			$logo = getimagesize(DIR.'/img/logo_empresa.jpg');
			$this->pdf->addJpegFromFile(
				DIR.'/img/logo_empresa.jpg'
				, $this->margen['left']
				, PDF_PAGE_HEIGHT-$this->margen['top']-$logo[1]
				, $logo[0]
				, $logo[1]
			);
		}
		// colocar fecha y hora
		$this->pdf->ezText('<i>'.utf8_decode(dia(date('N'))).', '.date(DATE_FORMAT.' H:i').'</i>', 10, array('justification'=>'right'));
		// colocar titulo
		$this->pdf->ezSetDy(-20);
		$this->pdf->ezText('<b>'.utf8_decode($this->titulo).'</b>', $this->tituloSize, array('justification'=>'right'));
		// colocar info de la empresa (debajo del logo)
		$this->pdf->ezSetY(PDF_PAGE_HEIGHT-$this->margen['top']-$logo[1]);
		$this->pdf->ezText(utf8_decode(EMPRESA_RAZON_SOCIAL), 11, array('justification'=>'left'));
		$this->pdf->ezText(utf8_decode(LANG_PDF_RUT.': '.EMPRESA_RUT), 11, array('justification'=>'left'));
		// mover cursor
		$this->pdf->ezSetDy(-20);
		// colocar header
		$this->header();
		// colocar footer
		$this->footer();
		// colocar número de página
		$this->pdf->ezStartPageNumbers(PDF_PAGE_WIDTH-$this->margen['right'], $this->margen['bottom']-25, 9, 'left', '{PAGENUM}/{TOTALPAGENUM}', 1);
	}

	/**
	 * Genera el encabezado para el pdf
	 * @todo Generar header sin que moleste a la página de inicio (o sea desde página 2)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final private function header () {
		// crear objeto para el header
		$header = $this->pdf->openObject();
		//$this->pdf->setLineStyle(3);
		//$yLinea = PDF_PAGE_HEIGHT - $this->margen['top'];
		//$this->pdf->line(0, $yLinea, PDF_PAGE_WIDTH, $yLinea);
		// cerrar objeto header y colocar en todas las páginas
		$this->pdf->closeObject();
		$this->pdf->addObject($header, 'all');
	}
	
	/**
	 * Genera el pie de página para el pdf
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final private function footer () {
		// crear objeto para el footer
		$footer = $this->pdf->openObject();
		// colocar fin del documento
		$this->pdf->setLineStyle(3);
		$yLinea = $this->margen['bottom'] - 10;
		$yTexto = $this->margen['bottom'] - 25;
		$this->pdf->line(0, $yLinea, PDF_PAGE_WIDTH, $yLinea);
		$this->pdf->addText($this->margen['left'], $yTexto, 9, utf8_decode(EMPRESA_RAZON_SOCIAL.' - '.EMPRESA_NOMBRE_FANTASIA));
		// datos de la casa matriz
		require(DIR.'/class/db/final/Sucursal.class.php');
		$objSucursals = new Sucursals();
		$objSucursal = $objSucursals->matriz();
		require(DIR.'/class/db/final/Comuna.class.php');
		$objComuna = new Comuna();
		$objComuna->set(array('id'=>$objSucursal->comuna_id));
		$objComuna->get();
		$this->pdf->addText($this->margen['left'], $yTexto-10, 9, utf8_decode(LANG_PDF_MAINOFFICE.': '.$objSucursal->direccion.', '.$objComuna->nombre));
		$this->pdf->addText($this->margen['left'], $yTexto-20, 9, utf8_decode(LANG_PDF_EMAIL.': '.$objSucursal->email.' - '.LANG_PDF_TELEPHONE.': '.$objSucursal->telefono.(!empty($objSucursal->fax)?' - Fax: '.$objSucursal->fax:'')));
		// datos del generador
		$this->pdf->addText($this->margen['left'], $yTexto-30, 9, utf8_decode(LANG_PDF_FILE.' <i>'.$this->archivo.'.pdf</i> '.LANG_PDF_FILEBY.' <c:alink:http://'.$_SERVER['SERVER_NAME'].'>'.$_SERVER['SERVER_NAME'].'</c:alink>'));
		// cerrar objeto footer y colocar en todas las páginas
		$this->pdf->closeObject();
		$this->pdf->addObject($footer, 'all');
	}

	/**
	 * Finaliza y muestra el PDF
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function end () {
		// detener número de páginas
		$this->pdf->ezStopPageNumbers();
		// enviar documento al navegador
		$this->pdf->ezStream(array('Content-Disposition'=>$this->archivo.'.pdf'));
	}
	
	/**
	 * Genera un archivo PDF con una tabla dentro
	 * @param data Arreglo con los datos para generar la tabla
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function generar ($tabla) {

		// codificar de utf8 a iso8859-1
		foreach($tabla as &$fila)
			foreach($fila as &$columna)
				$columna = utf8_decode($columna);
		
		// generar inicio, encabezado y footer del pdf
		$this->begin();
		
		// saber si la tabla parte con 0 o con 1 en los indices de filas y columnas
		$inicio = isset($tabla[0]) ? 0 : 1;
		
		// procesar titulos (indices deben coincidir con los de la tabla, asi que se ajustan)
		$titulos = array();
		$aux = array_shift($tabla); // guardar titulo de columna
		$keys = array_keys($tabla[$inicio]);
		foreach($keys as &$key) $titulos[$key] = array_shift($aux);
		unset($aux, $keys);
		
		// generar tabla para el pdf
		$this->pdf->ezTable($tabla, $titulos, $this->archivo, $this->opcionesTablas);
		unset($tabla, $titulos);

		// finalizar y mostrar pdf
		$this->end();

	}
	
}

?>
