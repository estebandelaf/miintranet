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
 * Generar un arreglo en javascript a partir de una tabla (arreglo de php)
 * @param array arreglo con la tabla a convertir
 * @param name nombre del arreglo en javascript
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-08
 */
function arrayPHP2arrayJS ($array, $name) {
        $arrayJSItem = '';
	$n = count($array);
	for($i=0; $i<$n; ++$i) {
		$key = array_shift($array[$i]);
		$value = array_shift($array[$i]);
		$arrayJSItem .= MiSiTiO::generar('arrayJSItem.html', array('name'=>$name, 'i'=>$i, 'key'=>$key, 'value'=>$value));
	}
	echo MiSiTiO::generar('arrayJS.html', array('name'=>$name, 'arrayJSItem'=>$arrayJSItem));
}

/**
 * Generar un grafico a partir de una tabla (arreglo de arreglos)
 * @param data arreglo con la tabla (se usaran primeras 2 columnas: x y valor)
 * @param title título para el gráfico
 * @param tab Desde donde se tabulará
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-08-23
 */
function graficar (&$data, $title, $tab=TAB) {
	unset($_SESSION['grafico']);
	$_SESSION['grafico']['data'] = $data;
	$_SESSION['grafico']['titulo'] = $title;
	echo MiSiTiO::generar('grafico.html');
}

/**
 * Transforma un título para ser usado en una url, quitando caracteres especiales
 * @param titulo Texto a transformar
 * @return String
 * @author Desconocido
 * @version 2010-05-21
 */
function titulo2url ($titulo) {
	// tranformamos todo a minúsculas
	$titulo = strtolower($titulo);
	// rememplazamos carácteres especiales latinos
	$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
	$repl = array('a', 'e', 'i', 'o', 'u', 'n');
	$titulo = str_replace($find, $repl, $titulo);
	// añadimos los guiones
	$find = array(' ', '&', '\r\n', '\n', '+');
	$titulo = str_replace($find, '-', $titulo);
	// eliminamos y reemplazamos otros caracteres especiales
	$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
	$repl = array('', '-', '');
	$titulo = preg_replace($find, $repl, $titulo);
	unset($find, $repl);
	return $titulo;
}

/**
 * Transforma un rut a un formato con solo los numeros
 * @param rut Rut que se quiere transformar (puede venir con puntos, comas, si tiene digito verificador DEBE tener guion)
 * @param quitarDV Si es true el digito verificador se quita, sino se mantiene
 * @return Rut formateado
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-07-17
 */
function rut ($rut, $quitarDV = true) {
	$rutNew = '';
	if(strpos($rut, '-')) {
		$aux = explode('-', $rut); // aux porque estamos con Strict Mode
		if($quitarDV) $rutNew = array_shift($aux);
		else $rutNew = str_replace('-', '', $rut);
		$rutNew = str_replace('.', '', $rutNew);
		$rutNew = str_replace(',', '', $rutNew);
	} else {
		$flag = false; // para controlar ceros iniciales
		$j=0;
		$largoRut = strlen($rut)-1;
		for($i=0; $i<$largoRut; ++$i) {
			if($flag || $rut[$i]) {
				$flag = true;
				$rutNew .= $rut[$i];
				++$j;
			}
		}
		$rutNew = number_format($rutNew, 0, '', '.');
		$rutNew .= '-'.$rut[$largoRut];
		unset($flag, $j, $largoRut, $i);
	}
	unset($rut, $quitarDV);
	return $rutNew;
}

/**
 * Calcula el dígito verificador de un rut
 * @param r Rut al que se calculará el dígito verificador
 * @return Dígito verificar
 * @author Desconocido
 * @version 2010-05-23
 */
function rutDV ($r) {
	$r = str_replace('.', '', $r);
	$r = str_replace(',', '', $r);
	$s=1;
	for($m=0;$r!=0;$r/=10)
		$s=($s+$r%10*(9-$m++%6))%11;
	return strtoupper(chr($s?$s+47:75));
}

/**
 * Convierte un texto separado por saltos de línea a un arreglo
 * @param lineas Texto que incluye los saltos de línea
 * @return Arreglo con cada línea
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-07-17
 */
function lineas2array(&$lineas) {
	return explode("\n", str_replace("\r", '', $lineas));
}

/**
 * Formatea un número según estándar latino
 * @param n Número a formatear
 * @param d Número de decimales
 * @return Número formateado, ex: num(1234,5638, 2) => 1.234,56
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-05-23
 */
function num ($n, $d=0) {
	return number_format($n, $d, ',', '.');
}

/**
 * Redireccionar página web
 * @param url Dirección a donde queremos ir
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-01
 */
function redirect ($url) {
	ob_end_clean();
	header('location: '.$url);
	exit();
}

/**
 * Restar 2 fechas
 * @param fechaF Fecha final, formato YYYY-MM-DD
 * @param fechaI Fecha inicial, formato YYYY-MM-DD
 * @return Número de días entre las fechas
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-05-23
 */
function restarFechas ($fechaF, $fechaI) {
	list ($anioF, $mesF, $diaF) = explode('-', $fechaF);
	list ($anioI, $mesI, $diaI) = explode('-', $fechaI);
	$fechaF = mktime(0, 0, 0,$mesF, $diaF, $anioF);
	$fechaI = mktime(0, 0, 0,$mesI, $diaI, $anioI);
	return round(($fechaF - $fechaI) / (60 * 60 * 24));
}

/**
 * Añadir n dias (corridos) a una fecha
 * @param fecha Fecha desde donde se sumará, formato YYYY-MM-DD
 * @param dias Cantidad de días (corridos) a sumar
 * @return Nueva fecha en formato YYYY-MM-DD
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-07-18
 */
function sumarFechas ($fecha, $dias) {
	list ($anio, $mes, $dia) = explode('-', $fecha);
	return date('Y-m-d', (mktime(0, 0, 0, $mes, $dia, $anio) + (60 * 60 * 24) * $dias));
}

/**
 * Genera la fecha en el formato necesario para el campo pubDate en rss
 * @param fechahora Fecha y hora en formato YYYY-MM-DD HH:MM:SS o YYYY-MM-DD HH:MM
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-09
 */
function fechaRSS ($fechahora) {
        list($fecha, $hora) = explode(' ', $fechahora);
        list ($anio, $mes, $dia) = explode('-', $fecha);
        list ($hora, $minutos, $segundos) = explode(':', strlen($hora)==5 ? $hora.':00' : $hora);
	return date('D, d M Y H:i:s O', mktime($hora, $minutos, $segundos, $mes, $dia, $anio));
}

/**
 * Transformar un string a una fecha
 *
 * @param fecha String a transformar (20100523)
 * @return String trasnformado (2010-05-23)
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-07-18
 */
function todate ($fecha = '0000-00-00') {
	if(strlen($fecha)==6) return $fecha[0].$fecha[1].$fecha[2].$fecha[3].'-'.$fecha[4].$fecha[5];
	else if(strlen($fecha)==8) return $fecha[0].$fecha[1].$fecha[2].$fecha[3].'-'.$fecha[4].$fecha[5].'-'.$fecha[6].$fecha[7];
	return $fecha;
}

/**
 * Cambia una fecha desde el formato YYYY-MM-DD al formato estándar definido en
 * config.inc.php o bien a un formato especificaco en el segundo argumento
 * @param Date $fecha (AAAA-MM-DD)
 * @param String $formato (ej: d-m-Y o m-Y)
 * @return String fecha formateada
 */
function formatearFecha ($fecha = '', $formato = DATE_FORMAT) {
	if($fecha=='') return '';
	return date_format(date_create($fecha), $formato);
}

/**
 * Retorna el nombre del mes según su número
 * @param i número del mes buscado
 * @param abreviado indica si se quiere el mes en formato de 3 caracteres o nombre completo
 * @return String con el nombre del mes
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-08
 */
function mes ($i, $abreviado = false) {
	if(!$abreviado) $meses = array(LANG_MONTH_JANUARY, LANG_MONTH_FEBRUARY, LANG_MONTH_MARCH, LANG_MONTH_APRIL, LANG_MONTH_MAY, LANG_MONTH_JUNE, LANG_MONTH_JULY, LANG_MONTH_AUGUST, LANG_MONTH_SEPTEMBER, LANG_MONTH_OCTOBER, LANG_MONTH_NOVEMBER, LANG_MONTH_DECEMBER);
	else  $meses = array(LANG_MONTH_JANUARY_SHORT, LANG_MONTH_FEBRUARY_SHORT, LANG_MONTH_MARCH_SHORT, LANG_MONTH_APRIL_SHORT, LANG_MONTH_MAY_SHORT, LANG_MONTH_JUNE_SHORT, LANG_MONTH_JULY_SHORT, LANG_MONTH_AUGUST_SHORT, LANG_MONTH_SEPTEMBER_SHORT, LANG_MONTH_OCTOBER_SHORT, LANG_MONTH_NOVEMBER_SHORT, LANG_MONTH_DECEMBER_SHORT);
	return $meses[($i-1)];
}

/**
 * Retorna el día de la semana según su número
 * @param i número del día buscado
 * @return String con el nombre del día
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-05
 */
function dia ($i) {
	$dias = array(LANG_DAY_MONDAY, LANG_DAY_TUESDAY, LANG_DAY_WEDNESDAY, LANG_DAY_THURSDAY, LANG_DAY_FRIDAY, LANG_DAY_SATURDAY, LANG_DAY_SUNDAY);
	return $dias[($i-1)];
}

/**
 *
 * @param variable Variable de tipo string que contiene las variables y sus valores
 * @param separador Caracter usado como separador entre variables
 * @param igual Caracter usado como separador entre variable y valor
 * @return Array
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-01
 */
function extraerCampos ($variable, $separador = ',', $igual='|') {
	$campos = array();
	$camposAux = !empty($variable) ? explode($separador, $variable) : array();
	foreach($camposAux as &$campo) {
		list($key, $value) = explode($igual, $campo);
		$campos[$key] = $value;
	}
	return $campos;
}

?>
