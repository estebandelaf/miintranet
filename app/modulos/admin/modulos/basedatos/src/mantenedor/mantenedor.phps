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
 * Mantenedor para la tabla {table}
 * {comment}
 * @author {author}
 * @version {date}
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla {table} y sus fk
require(DIR.'/class/db/final/{class}.class.php'); // principal para tabla {table}
{class_fk}

// crear objetos a utilizar por el mantenedor
$obj{class} = new {class}();
$obj{class}s = new {class}s();
{obj_fk}

// definir arreglo de pk, fk y campos no nulos
$arrPK = array('{array_pk}'); // columnas pks de la tabla {table}
$arrFK = array('{array_fk}'); // columnas fks de la tabla {table}
$arrNotNull = array('{array_notNull}'); // columnas que no pueden ser nulas de la tabla {table}

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' {table}'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'{comment}'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && {nempty_pk}) {
{set_pk}
	}
	// definir otros campos
{set}
	$obj{class}->set($arrSet);
	// guardar registro
	if($obj{class}->save()) { // en caso de exito
		echo MiSiTiO::success('{table}');
	} else { // en caso de error
		echo MiSiTiO::failure('{table}');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && {nempty_pk})) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
{set_pk}
		$obj{class}->set($arrSet);
		// obtener datos de $obj{class}
		$obj{class}->get();
		$link = '?edit&amp;'{get_pk};
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('{table}'.$link, 'validarFormulario');
{form}
	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla {table} en la base de datos
else if(isset($_GET['delete']) && {nempty_pk}) {
	// setear pk
	$arrSet = array();
{set_pk}
	$obj{class}->set($arrSet);
	// eliminar objeto de la base de datos
	if($obj{class}->delete()) { // en caso de exito
		echo MiSiTiO::success('{table}');
	} else { // en caso de error
		echo MiSiTiO::failure('{table}');
	}
}

// tabla con datos de la tabla {table}
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla {table}
	$obj{class}s->setSelectStatement('{select}');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
{filter}
		// agregar filtros al whereStatement
		if(count($filtros)) {
			$obj{class}s->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$obj{class}s->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$obj{class}s->setOrderByStatement('{pk}');
		$linkOrderBy = 'orderby={pk}&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$obj{class}s->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($obj{class}s->getObjetos() as $obj{class}) {
		$fila = array();
		// agregar datos de las columnas
{tableData}
		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('{table}?edit&amp;'{pk_fila}''));
		array_push($acciones, Tabla::eliminar('eliminar(\'{table}\', \''{pk_fila}'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
{search}
		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
{orderby}
		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = '{table}';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $obj{class}s->count(), '{table}?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array({null}, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', '{table}?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'{table}?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'{table}?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
