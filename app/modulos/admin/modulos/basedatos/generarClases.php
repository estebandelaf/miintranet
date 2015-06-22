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
if(isset($_POST['submit'])) generarClases($_POST['tablas'], 'DeLaF, esteban[at]delaf.cl');

// titulo de la pagina
echo TAB4,'<h1>',LANG_MOD_DB_CLASSGENERATOR_TITLE,'</h1>',"\n";

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

function generarClases ($tablas, $autor = AUDIT_PROGRAMA, $fecha = HOY) {
	// poner como global la conexion a la base de datos
	global $bd;
	// limpiar buffer de salida
	ob_clean();
	// crear directorio de trabajo en el servidor
	$workdir = TMP.'/classGenerator-'.date('YmdHis');
	mkdir($workdir);
	mkdir($workdir.'/abstract');
	mkdir($workdir.'/final');
	// procesar cada tabla con sus datos y generar clases
	foreach($tablas as &$tabla) {
		// obtener informacion de la tabla
		$info = $bd->tablaInfo($tabla);
		//echo '<pre>'; print_r($info); echo '</pre>'; exit;
		$tablaInfo = array_shift($info);
		$colsInfo = $info;
		unset($info);
		// definir nombres de las clases
		$class['abstract']['name'] = 'Base'.ucfirst($tabla);
		$class['abstract']['file'] = $class['abstract']['name'].'.class.php';
		$class['final']['name'] = ucfirst($tabla);
		$class['final']['file'] = $class['final']['name'].'.class.php';
		// procesar archivo para la clase abstracta
		$fields = '';
		$clear = '';
		$set = '';
		$columns = array();
		$values = array();
		$pk = array();
		$pk_where = array();
		$update = array();
		$notnull = array();
		$getObjectFKs = '';
		foreach($colsInfo as &$column) {
			// determinar si la columna es de audit
			$audit = array_search($column['name'], array('audit_programa', 'audit_usuario', 'audit_fechahora'))===false ? false : true;
			// generar atributos de la clase
			$fields .= src('src/class/abstract/field.phps', array(
				'column'=>$column['name']
				, 'comment'=>$column['comment']!=''?$column['comment'].': ':''
				, 'type'=>$column['type']
				, 'length'=>$column['length']
				, 'null'=>$column['null'].' '
				, 'default'=>"DEFAULT '".$column['default']."' "
				, 'auto'=>$column['auto']=='YES'?'AUTO ':''
				, 'pk'=>$column['pk']=='YES'?'PK ':''
				, 'fk'=>$column['fk_table']!=''?'FK:'.$column['fk_table'].'.'.$column['fk_column']:''
			));
			// generar clear
			$clear .= src('src/class/abstract/clear.phps', array('column'=>$column['name']));
			// generar set
			if(!$audit) {
				$set .= src('src/class/abstract/set.phps', array('column'=>$column['name']));
			}
			// generar columnas y valores para insert
			if($column['auto']=='NO' && !$audit) {
				array_push($columns, "\t\t\t\t".$column['name']);
				array_push($values, "\t\t\t\t".'".($this->'.$column['name'].'!==null?"\'".self::$bd->proteger($this->'.$column['name'].')."\'":\'NULL\')."');
			}
			// generar pk para consultas update y delete
			if($column['pk']=='YES') {
				array_push($pk, $column['name']);
				array_push($pk_where, $column['name'].'=".($this->'.$column['name'].'!==null?"\'".self::$bd->proteger($this->'.$column['name'].')."\'":\'NULL\')."');
			}
			// generar columnas y valores para el update
			if($column['pk']=='NO' && !$audit) {
				array_push($update, "\t\t\t\t".$column['name'].'=".($this->'.$column['name'].'!==null?"\'".self::$bd->proteger($this->'.$column['name'].')."\'":\'NULL\')."');
			}
			// generar listado de campos que no pueden ser nulos
			if($column['null']=='NOT NULL') {
				array_push($notnull, $column['name']);
			}
			// generar getObjectFK para la columna
			if($column['fk_table']!='') {
				$getObjectFKs .= src('src/class/abstract/getObjectFK.phps', array(
					'fk_class'=>ucfirst($column['fk_table'])
					, 'class'=>$class['final']['name']
					, 'author'=>$autor
					, 'date'=>$fecha
					, 'table'=>$column['fk_table']
					, 'column'=>$column['fk_column']
				));
			}
		}
		$columns = implode(",\n", $columns);
		$values = implode(",\n", $values);
		$pk = implode(', ', $pk);
		$pk_where = implode(' AND ', $pk_where);
		$update = implode(",\n", $update);
		$notnull = implode(', ', $notnull);
		$file = fopen($workdir.'/abstract/'.$class['abstract']['file'], 'w');
		fputs($file, src('src/class/abstract/abstract.phps', array(
			'author'=>$autor
			, 'date'=>$fecha
			, 'class'=>$class['final']['name']
			, 'table'=>$tablaInfo['name']
			, 'comment'=>$tablaInfo['comment']
			, 'fields'=>$fields
			, 'clear'=>$clear
			, 'set'=>$set
			, 'columns'=>$columns
			, 'values'=>$values
			, 'pk'=>$pk
			, 'pk_where'=>$pk_where
			, 'update'=>$update
			, 'notnull'=>$notnull
			, 'getObjectFKs'=>$getObjectFKs
		)));
		fclose($file);
		// procesar archivo para la clase final
		$file = fopen($workdir.'/final/'.$class['final']['file'], 'w');
		fputs($file, src('src/class/final/final.phps', array(
			'author'=>$autor
			, 'date'=>$fecha
			, 'class'=>$class['final']['name']
			, 'table'=>$tablaInfo['name']
			, 'comment'=>$tablaInfo['comment']
			, 'pk'=>$pk
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
