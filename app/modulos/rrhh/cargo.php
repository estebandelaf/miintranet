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
 * Mantenedor para la tabla cargo
 * Cargos del personal de la empresa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla cargo y sus fk
require(DIR.'/class/db/final/Cargo.class.php'); // principal para tabla cargo
require(DIR.'/class/db/final/Area.class.php'); // clase para fk de la tabla area


// crear objetos a utilizar por el mantenedor
$objCargo = new Cargo();
$objCargos = new Cargos();
$objArea = new Area();
$objAreas = new Areas();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla cargo
$arrFK = array('area_id'); // columnas fks de la tabla cargo
$arrNotNull = array('id', 'glosa', 'area_id', 'cardinalidad'); // columnas que no pueden ser nulas de la tabla cargo

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' cargo'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Cargos del personal de la empresa'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['id'])) {
		$arrSet['id'] = urldecode($_GET['id']);

	}
	// definir otros campos
	if(!isset($arrSet['glosa'])) $arrSet['glosa'] = (in_array('glosa', $arrFK)&&empty($_POST['glosa'])) ? null : $_POST['glosa'];
	if(!isset($arrSet['area_id'])) $arrSet['area_id'] = (in_array('area_id', $arrFK)&&empty($_POST['area_id'])) ? null : $_POST['area_id'];
	if(!isset($arrSet['cardinalidad'])) $arrSet['cardinalidad'] = (in_array('cardinalidad', $arrFK)&&empty($_POST['cardinalidad'])) ? null : $_POST['cardinalidad'];

	$objCargo->set($arrSet);
	// guardar registro
	if($objCargo->save()) { // en caso de exito
		echo MiSiTiO::success('cargo');
	} else { // en caso de error
		echo MiSiTiO::failure('cargo');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objCargo->set($arrSet);
		// obtener datos de $objCargo
		$objCargo->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('cargo'.$link, 'validarFormulario');
	$notNull = in_array('glosa', $arrNotNull);
	$isPK = in_array('glosa', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('glosa', $objCargo->glosa);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'glosa', 'glosa', ($objCargo->glosa===0 || $objCargo->glosa==='0' || !empty($objCargo->glosa)) ? $objCargo->glosa : '', 'Nombre/descripción del cargo', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="45"');
	$notNull = in_array('area_id', $arrNotNull);
	$isPK = in_array('area_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('area_id', $arrFK)) {
			$objFK = $objCargo->getArea();
			$text = $objFK->id; // FIXME: cambiar atributo por la glosa/descripcion/nombre/etc que corresponda
		} else $text = $objCargo->area_id;
		echo Form::text('area_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'area_id', 'area_id', $objAreas->listado(), $objCargo->area_id, 'Área a la que pertenece el cargo', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('cardinalidad', $arrNotNull);
	$isPK = in_array('cardinalidad', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('cardinalidad', $objCargo->cardinalidad);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'cardinalidad', 'cardinalidad', ($objCargo->cardinalidad===0 || $objCargo->cardinalidad==='0' || !empty($objCargo->cardinalidad)) ? $objCargo->cardinalidad : '0', 'Indica cuantos pueden tener el cargo, =0 infinitos', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla cargo en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objCargo->set($arrSet);
	// eliminar objeto de la base de datos
	if($objCargo->delete()) { // en caso de exito
		echo MiSiTiO::success('cargo');
	} else { // en caso de error
		echo MiSiTiO::failure('cargo');
	}
}

// tabla con datos de la tabla cargo
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla cargo
	$objCargos->setSelectStatement('id, glosa, area_id, cardinalidad');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, 'id='.Cargos::$bd->proteger($columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['glosa'])&&$columnas['glosa']!='') {
			array_push($filtros, Cargos::$bd->like('glosa', $columnas['glosa']));
			array_push($linkWhere, 'glosa|'.$columnas['glosa']);
		}
		if(isset($columnas['area_id'])&&$columnas['area_id']!='') {
			array_push($filtros, "area_id = '".Cargos::$bd->proteger($columnas['area_id'])."'");
			array_push($linkWhere, 'area_id|'.$columnas['area_id']);
		}
		if(isset($columnas['cardinalidad'])&&$columnas['cardinalidad']!='') {
			array_push($filtros, Cargos::$bd->like('cardinalidad', $columnas['cardinalidad']));
			array_push($linkWhere, 'cardinalidad|'.$columnas['cardinalidad']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objCargos->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objCargos->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objCargos->setOrderByStatement('glosa');
		$linkOrderBy = 'orderby=glosa&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objCargos->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objCargos->getObjetos() as $objCargo) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objCargo->id);
		// agregar glosa a la fila
		array_push($fila, $objCargo->glosa);
		// agregar area_id a la fila
		if($objCargo->area_id!='') {
			$objFK = $objCargo->getArea();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar cardinalidad a la fila
		array_push($fila, $objCargo->cardinalidad);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('cargo?edit&amp;'.'id='.urlencode($objCargo->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'cargo\', \''.'id='.urlencode($objCargo->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('glosa', isset($columnas['glosa'])?$columnas['glosa']:'', 45),
		Form::select4table('area_id', $objAreas->listado(), !empty($columnas['area_id'])?$columnas['area_id']:''),
		Form::input4table('cardinalidad', isset($columnas['cardinalidad'])?$columnas['cardinalidad']:'', 16),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'cargo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'glosa'=>Tabla::orderby('glosa', 'cargo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=glosa'),
		'area_id'=>Tabla::orderby('area_id', 'cargo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=area_id'),
		'cardinalidad'=>Tabla::orderby('cardinalidad', 'cargo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=cardinalidad'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'cargo';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objCargos->count(), 'cargo?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'cargo?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'cargo?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'cargo?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
