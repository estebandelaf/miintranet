<?php

/**
 * Esta método permite utilizar plantillas para la generacion de código fuente
 * @param archivoPlantilla Ruta hacia el archivo que contiene la plantilla (con extension)
 * @param variables Arreglo con las variables a reemplazar en la plantilla
 * @return String Plantilla ya formateada con las variables correspondientes
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-18
 */
function generar ($archivoPlantilla, $variables = null) {

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

/**
 * Procesa la información que genera phpinfo() y la devuelve como array
 * @return Array Arreglo con los datos de phpinfo
 * @author usuario en php.net (modificada por DeLaF)
 * @version 2011-04-17
 */
function phpinfo_array(){
	// Andale!  Andale!  Yee-Hah!
	ob_start(); 
	phpinfo(-1);
 
	$pi = preg_replace(
		array(
			'#^.*<body>(.*)</body>.*$#ms', '#<h2>PHP License</h2>.*$#ms',
			'#<h1>Configuration</h1>#',  "#\r?\n#", "#</(h1|h2|h3|tr)>#", '# +<#',
			"#[ \t]+#", '#&nbsp;#', '#  +#', '# class=".*?"#', '%&#039;%',
			'#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a>'
			.'<h1>PHP Version (.*?)</h1>(?:\n+?)</td></tr>#',
			'#<h1><a href="(?:.*?)\?=(.*?)">PHP Credits</a></h1>#',
			'#<tr>(?:.*?)" src="(?:.*?)=(.*?)"(?:.*?)Zend Engine (.*?),(?:.*?)</tr>#',
			"# +#", '#<tr>#', '#</tr>#'
		),
		array(
			'$1', '', '', '', '</$1>' . "\n", '<', ' ', ' ', ' ', '', ' ',
			'<h2>PHP Configuration</h2>'."\n".'<tr><td>PHP Version</td><td>$2</td></tr>'.
			"\n".'<tr><td>PHP Egg</td><td>$1</td></tr>',
			'<tr><td>PHP Credits Egg</td><td>$1</td></tr>',
			'<tr><td>Zend Engine</td><td>$2</td></tr>' . "\n" .
			'<tr><td>Zend Egg</td><td>$1</td></tr>', ' ', '%S%', '%E%'
		),
		ob_get_clean()
	);

	$sections = explode('<h2>', strip_tags($pi, '<h2><th><td>'));
	unset($sections[0]);

	$pi = array();
	foreach($sections as $section) {
		$n = substr($section, 0, strpos($section, '</h2>'));
		preg_match_all(
			'#%S%(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?%E%#',
			$section, $askapache, PREG_SET_ORDER
		);
		foreach($askapache as $m)
			// modificacion por delaf aqui
			$pi[$n][$m[1]] = (isset($m[2]) && (!isset($m[3]) || $m[2]==$m[3])) ? $m[2] :array_slice($m, 2);
	}	
	return $pi;
}

?>
