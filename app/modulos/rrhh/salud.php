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
 * Mantenedor para la tabla salud
 * Instituciones de salud: FONASA o Isapres
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla salud y sus fk
require(DIR.'/class/db/final/Salud.class.php'); // principal para tabla salud


// crear objetos a utilizar por el mantenedor
$objSalud = new Salud();
$objSaluds = new Saluds();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla salud
$arrFK = array(''); // columnas fks de la tabla salud
$arrNotNull = array('id', 'nombre'); // columnas que no pueden ser nulas de la tabla salud

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' salud'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Instituciones de salud: FONASA o Isapres'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['id'])) {
		$arrSet['id'] = urldecode($_GET['id']);

	}
	// definir otros campos
	if(!isset($arrSet['id'])) $arrSet['id'] = (in_array('id', $arrFK)&&empty($_POST['id'])) ? null : $_POST['id'];
	if(!isset($arrSet['nombre'])) $arrSet['nombre'] = (in_array('nombre', $arrFK)&&empty($_POST['nombre'])) ? null : $_POST['nombre'];

	$objSalud->set($arrSet);
	// guardar registro
	if($objSalud->save()) { // en caso de exito
		echo MiSiTiO::success('salud');
	} else { // en caso de error
		echo MiSiTiO::failure('salud');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objSalud->set($arrSet);
		// obtener datos de $objSalud
		$objSalud->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('salud'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objSalud->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', ($objSalud->id===0 || $objSalud->id==='0' || !empty($objSalud->id)) ? $objSalud->id : '', 'ID de la institución de salud (RUT sin puntos ni dv)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('nombre', $arrNotNull);
	$isPK = in_array('nombre', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nombre', $objSalud->nombre);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombre', 'nombre', ($objSalud->nombre===0 || $objSalud->nombre==='0' || !empty($objSalud->nombre)) ? $objSalud->nombre : '', 'Nombre de la institución de salud', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla salud en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objSalud->set($arrSet);
	// eliminar objeto de la base de datos
	if($objSalud->delete()) { // en caso de exito
		echo MiSiTiO::success('salud');
	} else { // en caso de error
		echo MiSiTiO::failure('salud');
	}
}

// tabla con datos de la tabla salud
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla salud
	$objSaluds->setSelectStatement('id, nombre');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, 'id='.Saluds::$bd->proteger($columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
			array_push($filtros, Saluds::$bd->like('nombre', $columnas['nombre']));
			array_push($linkWhere, 'nombre|'.$columnas['nombre']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objSaluds->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objSaluds->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objSaluds->setOrderByStatement('nombre');
		$linkOrderBy = 'orderby=nombre&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objSaluds->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objSaluds->getObjetos() as $objSalud) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objSalud->id);
		// agregar nombre a la fila
		array_push($fila, $objSalud->nombre);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('salud?edit&amp;'.'id='.urlencode($objSalud->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'salud\', \''.'id='.urlencode($objSalud->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('nombre', isset($columnas['nombre'])?$columnas['nombre']:'', 30),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'salud?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'nombre'=>Tabla::orderby('nombre', 'salud?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'salud';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objSaluds->count(), 'salud?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'salud?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'salud?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'salud?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
