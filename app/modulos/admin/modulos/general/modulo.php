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
 * Mantenedor para la tabla modulo
 * Módulos del sistema
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla modulo y sus fk
require(DIR.'/class/db/final/Modulo.class.php'); // principal para tabla modulo


// crear objetos a utilizar por el mantenedor
$objModulo = new Modulo();
$objModulos = new Modulos();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('nombre'); // columnas pks de la tabla modulo
$arrFK = array(''); // columnas fks de la tabla modulo
$arrNotNull = array('nombre', 'glosa'); // columnas que no pueden ser nulas de la tabla modulo

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' modulo'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Módulos del sistema'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['nombre'])) {
		$arrSet['nombre'] = urldecode($_GET['nombre']);

	}
	// definir otros campos
	if(!isset($arrSet['nombre'])) $arrSet['nombre'] = (in_array('nombre', $arrFK)&&empty($_POST['nombre'])) ? null : $_POST['nombre'];
	if(!isset($arrSet['glosa'])) $arrSet['glosa'] = (in_array('glosa', $arrFK)&&empty($_POST['glosa'])) ? null : $_POST['glosa'];

	$objModulo->set($arrSet);
	// guardar registro
	if($objModulo->save()) { // en caso de exito
		echo MiSiTiO::success('modulo');
	} else { // en caso de error
		echo MiSiTiO::failure('modulo');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['nombre']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['nombre'] = urldecode($_GET['nombre']);

		$objModulo->set($arrSet);
		// obtener datos de $objModulo
		$objModulo->get();
		$link = '?edit&amp;'.'nombre='.urlencode($_GET['nombre']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('modulo'.$link, 'validarFormulario');
	$notNull = in_array('nombre', $arrNotNull);
	$isPK = in_array('nombre', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nombre', $objModulo->nombre);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombre', 'nombre', ($objModulo->nombre===0 || $objModulo->nombre==='0' || !empty($objModulo->nombre)) ? $objModulo->nombre : '', 'Nombre del módulo', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="15"');
	$notNull = in_array('glosa', $arrNotNull);
	$isPK = in_array('glosa', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('glosa', $objModulo->glosa);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'glosa', 'glosa', ($objModulo->glosa===0 || $objModulo->glosa==='0' || !empty($objModulo->glosa)) ? $objModulo->glosa : '', 'Descripción del módulo', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="70"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla modulo en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['nombre'])) {
	// setear pk
	$arrSet = array();
		$arrSet['nombre'] = urldecode($_GET['nombre']);

	$objModulo->set($arrSet);
	// eliminar objeto de la base de datos
	if($objModulo->delete()) { // en caso de exito
		echo MiSiTiO::success('modulo');
	} else { // en caso de error
		echo MiSiTiO::failure('modulo');
	}
}

// tabla con datos de la tabla modulo
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla modulo
	$objModulos->setSelectStatement('nombre, glosa');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
			array_push($filtros, Modulos::$bd->like('nombre', $columnas['nombre']));
			array_push($linkWhere, 'nombre|'.$columnas['nombre']);
		}
		if(isset($columnas['glosa'])&&$columnas['glosa']!='') {
			array_push($filtros, Modulos::$bd->like('glosa', $columnas['glosa']));
			array_push($linkWhere, 'glosa|'.$columnas['glosa']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objModulos->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objModulos->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objModulos->setOrderByStatement('nombre');
		$linkOrderBy = 'orderby=nombre&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objModulos->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objModulos->getObjetos() as $objModulo) {
		$fila = array();
		// agregar datos de las columnas
		// agregar nombre a la fila
		array_push($fila, $objModulo->nombre);
		// agregar glosa a la fila
		array_push($fila, $objModulo->glosa);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('modulo?edit&amp;'.'nombre='.urlencode($objModulo->nombre).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'modulo\', \''.'nombre='.urlencode($objModulo->nombre).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('nombre', isset($columnas['nombre'])?$columnas['nombre']:'', 15),
		Form::input4table('glosa', isset($columnas['glosa'])?$columnas['glosa']:'', 70),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'nombre'=>Tabla::orderby('nombre', 'modulo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),
		'glosa'=>Tabla::orderby('glosa', 'modulo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=glosa'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'modulo';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objModulos->count(), 'modulo?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'modulo?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'modulo?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'modulo?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
