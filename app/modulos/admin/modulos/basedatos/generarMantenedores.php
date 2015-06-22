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
if(isset($_POST['submit'])) generarMantenedores($_POST['tablas'], 'DeLaF, esteban[at]delaf.cl');

// titulo de la pagina
echo TAB4,'<h1>',LANG_MOD_DB_MAINTAINERGENERATOR_TITLE,'</h1>',"\n";

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

function generarMantenedores ($tablas, $autor = AUDIT_PROGRAMA, $fecha = HOY) {
	// poner como global la conexion a la base de datos
	global $bd;
	// limpiar buffer de salida
	ob_clean();
	// crear directorio de trabajo en el servidor
	$workdir = TMP.'/maintainerGenerator-'.date('YmdHis');
	mkdir($workdir);
	// procesar cada tabla con sus datos y generar clases
	foreach($tablas as &$tabla) {
		// obtener informacion de la tabla
		$info = $bd->tablaInfo($tabla);
		//echo '<pre>'; print_r($info); echo '</pre>'; exit;
		$tablaInfo = array_shift($info);
		$colsInfo = $info;
		unset($info);
		// definir nombre para el archivo del mantenedor
		$mantenedor = $tabla.'.php';
		// procesar mantenedor para cada columna de la tabla
		$class_fk = '';
		$obj_fk = '';
		$nempty_pk = array();
		$set_pk = '';
		$get_pk = array();
		$pk_fila = array();
		$pk = array();
		$array_fk = array();
		$array_notNull = array();
		$set = '';
		$form = '';
		$filter = '';
		$search = '';
		$orderby = '';
		$null = array();
		$select = array();
		$tableData = '';
		foreach($colsInfo as &$column) {
			// no procesar columnas para audit
			if(array_search($column['name'], array('audit_programa', 'audit_usuario', 'audit_fechahora'))!==false) continue;
			// generar columnas para select
			array_push($select, $column['name']);
			// generar codigo para insertar datos de columnas en la fila de la tabla a mostrar
			if(empty($column['fk_table'])) {
				$tableData .= src('src/mantenedor/tableData.phps', array('class'=>ucfirst($tablaInfo['name']), 'column'=>$column['name']));
			} else {
				$tableData .= src('src/mantenedor/tableDataFK.phps', array('class'=>ucfirst($tablaInfo['name']), 'column'=>$column['name'], 'fk_class'=>ucfirst($column['fk_table']), 'fk_column'=>$column['fk_column']));
			}
			// buscar campos que no pueden sr nulos
			if($column['null']=='NOT NULL') {
				array_push($array_notNull, $column['name']);
			}
			// buscar clases que son necesarias por las fk
			if(!empty($column['fk_table'])) {
				$class_fk .= src('src/mantenedor/class_fk.phps', array(
					'class'=>ucfirst($column['fk_table'])
					, 'table'=>$column['fk_table']
				));
				$obj_fk .= src('src/mantenedor/obj_fk.phps', array('class'=>ucfirst($column['fk_table'])));
				array_push($array_fk, $column['name']);
			}
			// generar variables relacionadas con la pk
			if($column['pk']=='YES') {
				array_push($nempty_pk, '!empty($_GET[\''.$column['name'].'\'])');
				$set_pk .= src('src/mantenedor/set_pk.phps', array('pk'=>$column['name']));
				array_push($get_pk, '.\''.$column['name'].'=\'.urlencode($_GET[\''.$column['name'].'\'])');
				array_push($pk_fila, '.\''.$column['name'].'=\'.urlencode($obj'.ucfirst($tablaInfo['name']).'->'.$column['name'].').');
				array_push($pk, $column['name']);
			}
			if($column['auto']=='NO') {
				// si no es llave foranea se muestra un input normal
				if(empty($column['fk_table'])) {
					if($column['type']=='date') {
						$form .= src('src/mantenedor/formInputDate.phps', array('col'=>$column['name'], 'class'=>ucfirst($tablaInfo['name']), 'default'=>$column['default'], 'help'=>$column['comment']));
					} else {
						$form .= src('src/mantenedor/formInput.phps', array('col'=>$column['name'], 'class'=>ucfirst($tablaInfo['name']), 'default'=>$column['default'], 'help'=>$column['comment'], 'length'=>$column['length']));
					}
				} else { // si es llave foranea se muestra un campo select
					$form .= src('src/mantenedor/formSelect.phps', array('col'=>$column['name'], 'class'=>ucfirst($tablaInfo['name']), 'fk_class'=> ucfirst($column['fk_table']), 'fk_column'=> $column['fk_column'], 'help'=>$column['comment']));
				}
				$set .= src('src/mantenedor/set.phps', array('col'=>$column['name']));
			}
			// genear campos para filtro, busquda y ordenamiento
			if(!empty($column['fk_table'])) $filter .= src('src/mantenedor/filterFK.phps', array('col'=>$column['name'], 'class'=>ucfirst($tablaInfo['name'])));
			else $filter .= src('src/mantenedor/filter.phps', array('col'=>$column['name'], 'class'=>ucfirst($tablaInfo['name'])));
			$orderby .= src('src/mantenedor/orderby.phps', array('col'=>$column['name'], 'table'=>$tablaInfo['name']));
			// definir campos de busqueda en tabla
			if(!empty($column['fk_table'])) {
				$search .= src('src/mantenedor/searchSelect.phps', array('col'=>$column['name'], 'fk_class'=>  ucfirst($column['fk_table'])));
			} else {
				$search .= src('src/mantenedor/searchInput.phps', array('col'=>$column['name'], 'length'=>!empty($column['length'])?$column['length']:"''"));
			}
			// agregar null para la tabla, asi el tamaño de la columna acciones se define
			array_push($null, 'null');
		}
		$nempty_pk = implode(' && ', $nempty_pk);
		$get_pk = implode('.\'&amp;\'', $get_pk);
		$pk_fila = implode('\'&amp;\'', $pk_fila);
		$array_pk = implode("', '", $pk);
		$array_fk = implode("', '", $array_fk);
		$array_notNull = implode("', '", $array_notNull);
		$pk = implode(',', $pk);
		$null = implode(', ', $null);
		$select = implode(', ', $select);
		// guardar mantenedor
		$file = fopen($workdir.'/'.$mantenedor, 'w');
		fputs($file, src('src/mantenedor/mantenedor.phps', array(
			'author'=>$autor
			, 'date'=>$fecha
			, 'table'=>$tablaInfo['name']
			, 'comment'=>$tablaInfo['comment']
			, 'class'=>ucfirst($tablaInfo['name'])
			, 'class_fk'=>$class_fk
			, 'obj_fk'=>$obj_fk
			, 'nempty_pk'=>$nempty_pk
			, 'set_pk'=>$set_pk
			, 'set'=>$set
			, 'get_pk'=>$get_pk
			, 'form'=>$form
			, 'filter'=>$filter
			, 'pk'=>$pk
			, 'pk_fila'=>$pk_fila
			, 'search'=>$search
			, 'orderby'=>$orderby
			, 'array_pk'=>$array_pk
			, 'array_fk'=>$array_fk
			, 'array_notNull'=>$array_notNull
			, 'null'=>$null
			, 'select'=>$select
			, 'tableData'=>$tableData
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
