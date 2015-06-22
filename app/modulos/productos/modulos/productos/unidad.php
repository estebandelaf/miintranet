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
 * Mantenedor para la tabla unidad
 * Unidades de medida para productos
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla unidad y sus fk
require(DIR.'/class/db/final/Unidad.class.php'); // principal para tabla unidad


// crear objetos a utilizar por el mantenedor
$objUnidad = new Unidad();
$objUnidads = new Unidads();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla unidad
$arrFK = array(''); // columnas fks de la tabla unidad
$arrNotNull = array('id', 'unidad', 'glosa'); // columnas que no pueden ser nulas de la tabla unidad

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' unidad'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Unidades de medida para productos'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['id'])) {
		$arrSet['id'] = urldecode($_GET['id']);

	}
	// definir otros campos
	if(!isset($arrSet['unidad'])) $arrSet['unidad'] = (in_array('unidad', $arrFK)&&empty($_POST['unidad'])) ? null : $_POST['unidad'];
	if(!isset($arrSet['glosa'])) $arrSet['glosa'] = (in_array('glosa', $arrFK)&&empty($_POST['glosa'])) ? null : $_POST['glosa'];

	$objUnidad->set($arrSet);
	// guardar registro
	if($objUnidad->save()) { // en caso de exito
		echo MiSiTiO::success('unidad');
	} else { // en caso de error
		echo MiSiTiO::failure('unidad');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objUnidad->set($arrSet);
		// obtener datos de $objUnidad
		$objUnidad->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('unidad'.$link, 'validarFormulario');
	$notNull = in_array('unidad', $arrNotNull);
	$isPK = in_array('unidad', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('unidad', $objUnidad->unidad);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'unidad', 'unidad', ($objUnidad->unidad===0 || $objUnidad->unidad==='0' || !empty($objUnidad->unidad)) ? $objUnidad->unidad : '', 'Unidad (ej: unidad, kg, m, etc)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="10"');
	$notNull = in_array('glosa', $arrNotNull);
	$isPK = in_array('glosa', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('glosa', $objUnidad->glosa);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'glosa', 'glosa', ($objUnidad->glosa===0 || $objUnidad->glosa==='0' || !empty($objUnidad->glosa)) ? $objUnidad->glosa : '', 'Glosa (ej: unidad, kilogramo, metro, etc)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla unidad en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objUnidad->set($arrSet);
	// eliminar objeto de la base de datos
	if($objUnidad->delete()) { // en caso de exito
		echo MiSiTiO::success('unidad');
	} else { // en caso de error
		echo MiSiTiO::failure('unidad');
	}
}

// tabla con datos de la tabla unidad
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla unidad
	$objUnidads->setSelectStatement('id, unidad, glosa');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, Unidads::$bd->like('id', $columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['unidad'])&&$columnas['unidad']!='') {
			array_push($filtros, Unidads::$bd->like('unidad', $columnas['unidad']));
			array_push($linkWhere, 'unidad|'.$columnas['unidad']);
		}
		if(isset($columnas['glosa'])&&$columnas['glosa']!='') {
			array_push($filtros, Unidads::$bd->like('glosa', $columnas['glosa']));
			array_push($linkWhere, 'glosa|'.$columnas['glosa']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objUnidads->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objUnidads->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objUnidads->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objUnidads->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objUnidads->getObjetos() as $objUnidad) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objUnidad->id);
		// agregar unidad a la fila
		array_push($fila, $objUnidad->unidad);
		// agregar glosa a la fila
		array_push($fila, $objUnidad->glosa);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('unidad?edit&amp;'.'id='.urlencode($objUnidad->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'unidad\', \''.'id='.urlencode($objUnidad->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('unidad', isset($columnas['unidad'])?$columnas['unidad']:'', 10),
		Form::input4table('glosa', isset($columnas['glosa'])?$columnas['glosa']:'', 30),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'unidad?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'unidad'=>Tabla::orderby('unidad', 'unidad?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=unidad'),
		'glosa'=>Tabla::orderby('glosa', 'unidad?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=glosa'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'unidad';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objUnidads->count(), 'unidad?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'unidad?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'unidad?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'unidad?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
