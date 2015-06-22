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

require('../../../../inc/web1.inc.php');

// si se ha pasado el formulario se llama a la funcion que genera las clases
if(isset($_POST['submit'])) generarAuditoria($_POST['tablas'], 'DeLaF, esteban[at]delaf.cl');

// titulo de la pagina
echo TAB4,'<h1>',LANG_MOD_DB_AUDITGENERATOR_TITLE,'</h1>',"\n";

// seleccionar las tablas de la base de datos
$tablas = $bd->tablas();

// generar data para la tabla del formulario
$data = array(array(LANG_MOD_DB_TABLE_TABLE, LANG_MOD_DB_TABLE_COMMENT, Form::checkboxSwitch('tablas', true)));
foreach($tablas as &$tabla) {
	array_push($data, array($tabla['name'], $tabla['comment'], Form::checkbox4table('tablas', $tabla['name'], true)));
}
unset($tablas);

echo Form::bForm();
echo Tabla::generar($data);
echo Form::submitButton();
echo Form::eForm();
unset($data);

require(DIR.'/inc/web2.inc.php');

function generarAuditoria ($tablas, $autor = AUDIT_PROGRAMA, $fecha = HOY) {
	// limpiar buffer de salida
	ob_clean();
	// crear directorio de trabajo en el servidor
	$workdir = TMP.'/auditGenerator-'.date('YmdHis');
	mkdir($workdir);
	// procesar cada tabla con sus datos y generar columnas para auditoria
	foreach($tablas as &$tabla) {
		$file = fopen($workdir.'/audit.sql', 'a');
		fputs($file, src('src/sql/audit.sql', array(
			'author'=>$autor
			, 'date'=>$fecha
			, 'table'=>$tabla
		)));
		fclose($file);
	}
	// empaquetar y descargar
	require(DIR.'/class/Archivo.class.php');
	Archivo::targz($workdir, true);
	// limpiar variables
	unset($workdir);
	// terminar script
	exit;
}

?>
