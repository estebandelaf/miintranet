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
 * Mantenedor para la tabla enlace
 * Enlaces generales de la aplicación
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla enlace y sus fk
require(DIR.'/class/db/final/Enlace.class.php'); // principal para tabla enlace
require(DIR.'/class/db/final/Enlace_categoria.class.php'); // clase para fk de la tabla enlace_categoria


// crear objetos a utilizar por el mantenedor
$objEnlace = new Enlace();
$objEnlaces = new Enlaces();
$objEnlace_categoria = new Enlace_categoria();
$objEnlace_categorias = new Enlace_categorias();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('url'); // columnas pks de la tabla enlace
$arrFK = array('enlace_categoria_id'); // columnas fks de la tabla enlace
$arrNotNull = array('url', 'nombre', 'enlace_categoria_id'); // columnas que no pueden ser nulas de la tabla enlace

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' enlace'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Enlaces generales de la aplicación'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['url'])) {
		$arrSet['url'] = urldecode($_GET['url']);

	}
	// definir otros campos
	if(!isset($arrSet['url'])) $arrSet['url'] = (in_array('url', $arrFK)&&empty($_POST['url'])) ? null : $_POST['url'];
	if(!isset($arrSet['nombre'])) $arrSet['nombre'] = (in_array('nombre', $arrFK)&&empty($_POST['nombre'])) ? null : $_POST['nombre'];
	if(!isset($arrSet['enlace_categoria_id'])) $arrSet['enlace_categoria_id'] = (in_array('enlace_categoria_id', $arrFK)&&empty($_POST['enlace_categoria_id'])) ? null : $_POST['enlace_categoria_id'];

	$objEnlace->set($arrSet);
	// guardar registro
	if($objEnlace->save()) { // en caso de exito
		echo MiSiTiO::success('enlace');
	} else { // en caso de error
		echo MiSiTiO::failure('enlace');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['url']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['url'] = urldecode($_GET['url']);

		$objEnlace->set($arrSet);
		// obtener datos de $objEnlace
		$objEnlace->get();
		$link = '?edit&amp;'.'url='.urlencode($_GET['url']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('enlace'.$link, 'validarFormulario');
	$notNull = in_array('url', $arrNotNull);
	$isPK = in_array('url', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('url', $objEnlace->url);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'url', 'url', ($objEnlace->url===0 || $objEnlace->url==='0' || !empty($objEnlace->url)) ? $objEnlace->url : '', 'Dirección url completa', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="200"');
	$notNull = in_array('nombre', $arrNotNull);
	$isPK = in_array('nombre', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nombre', $objEnlace->nombre);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombre', 'nombre', ($objEnlace->nombre===0 || $objEnlace->nombre==='0' || !empty($objEnlace->nombre)) ? $objEnlace->nombre : '', 'Nombre o descripción del enlace', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="60"');
	$notNull = in_array('enlace_categoria_id', $arrNotNull);
	$isPK = in_array('enlace_categoria_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('enlace_categoria_id', $arrFK)) {
			$objFK = $objEnlace->getEnlace_categoria();
			$text = $objFK->nombre;
		} else $text = $objEnlace->enlace_categoria_id;
		echo Form::text('enlace_categoria_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'enlace_categoria_id', 'enlace_categoria_id', $objEnlace_categorias->listado(), $objEnlace->enlace_categoria_id, 'Categorí­a del enlace', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla enlace en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['url'])) {
	// setear pk
	$arrSet = array();
		$arrSet['url'] = urldecode($_GET['url']);

	$objEnlace->set($arrSet);
	// eliminar objeto de la base de datos
	if($objEnlace->delete()) { // en caso de exito
		echo MiSiTiO::success('enlace');
	} else { // en caso de error
		echo MiSiTiO::failure('enlace');
	}
}

// tabla con datos de la tabla enlace
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla enlace
	$objEnlaces->setSelectStatement('url, nombre, enlace_categoria_id');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['url'])&&$columnas['url']!='') {
			array_push($filtros, Enlaces::$bd->like('url', $columnas['url']));
			array_push($linkWhere, 'url|'.$columnas['url']);
		}
		if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
			array_push($filtros, Enlaces::$bd->like('nombre', $columnas['nombre']));
			array_push($linkWhere, 'nombre|'.$columnas['nombre']);
		}
		if(isset($columnas['enlace_categoria_id'])&&$columnas['enlace_categoria_id']!='') {
			array_push($filtros, "enlace_categoria_id = '".Enlaces::$bd->proteger($columnas['enlace_categoria_id'])."'");
			array_push($linkWhere, 'enlace_categoria_id|'.$columnas['enlace_categoria_id']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objEnlaces->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objEnlaces->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objEnlaces->setOrderByStatement('url');
		$linkOrderBy = 'orderby=url&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objEnlaces->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objEnlaces->getObjetos() as $objEnlace) {
		$fila = array();
		// agregar datos de las columnas
		// agregar url a la fila
		array_push($fila, $objEnlace->url);
		// agregar nombre a la fila
		array_push($fila, $objEnlace->nombre);
		// agregar enlace_categoria_id a la fila
		if($objEnlace->enlace_categoria_id!='') {
			$objFK = $objEnlace->getEnlace_categoria();
			$glosaFK = $objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('enlace?edit&amp;'.'url='.urlencode($objEnlace->url).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'enlace\', \''.'url='.urlencode($objEnlace->url).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('url', isset($columnas['url'])?$columnas['url']:'', 200),
		Form::input4table('nombre', isset($columnas['nombre'])?$columnas['nombre']:'', 60),
		Form::select4table('enlace_categoria_id', $objEnlace_categorias->listado(), !empty($columnas['enlace_categoria_id'])?$columnas['enlace_categoria_id']:''),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'url'=>Tabla::orderby('url', 'enlace?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=url'),
		'nombre'=>Tabla::orderby('nombre', 'enlace?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),
		'enlace_categoria_id'=>Tabla::orderby('enlace_categoria_id', 'enlace?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=enlace_categoria_id'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'enlace';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objEnlaces->count(), 'enlace?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'enlace?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'enlace?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'enlace?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
