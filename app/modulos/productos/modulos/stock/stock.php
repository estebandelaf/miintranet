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
 * Mantenedor para la tabla stock
 * Niveles de stock actuales
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla stock y sus fk
require(DIR.'/class/db/final/Stock.class.php'); // principal para tabla stock
require(DIR.'/class/db/final/Producto.class.php'); // clase para fk de la tabla producto
require(DIR.'/class/db/final/Bodega.class.php'); // clase para fk de la tabla bodega
require(DIR.'/class/db/final/Area.class.php'); // clase para fk de la tabla area


// crear objetos a utilizar por el mantenedor
$objStock = new Stock();
$objStocks = new Stocks();
$objProducto = new Producto();
$objProductos = new Productos();
$objBodega = new Bodega();
$objBodegas = new Bodegas();
$objArea = new Area();
$objAreas = new Areas();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('producto_id', 'bodega_id', 'area_id'); // columnas pks de la tabla stock
$arrFK = array('producto_id', 'bodega_id', 'area_id'); // columnas fks de la tabla stock
$arrNotNull = array('producto_id', 'bodega_id', 'area_id', 'nivel'); // columnas que no pueden ser nulas de la tabla stock

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' stock'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Niveles de stock actuales'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['producto_id']) && !empty($_GET['bodega_id']) && !empty($_GET['area_id'])) {
		$arrSet['producto_id'] = urldecode($_GET['producto_id']);
		$arrSet['bodega_id'] = urldecode($_GET['bodega_id']);
		$arrSet['area_id'] = urldecode($_GET['area_id']);

	}
	// definir otros campos
	if(!isset($arrSet['producto_id'])) $arrSet['producto_id'] = (in_array('producto_id', $arrFK)&&empty($_POST['producto_id'])) ? null : $_POST['producto_id'];
	if(!isset($arrSet['bodega_id'])) $arrSet['bodega_id'] = (in_array('bodega_id', $arrFK)&&empty($_POST['bodega_id'])) ? null : $_POST['bodega_id'];
	if(!isset($arrSet['area_id'])) $arrSet['area_id'] = (in_array('area_id', $arrFK)&&empty($_POST['area_id'])) ? null : $_POST['area_id'];
	if(!isset($arrSet['nivel'])) $arrSet['nivel'] = (in_array('nivel', $arrFK)&&empty($_POST['nivel'])) ? null : $_POST['nivel'];

	$objStock->set($arrSet);
	// guardar registro
	if($objStock->save()) { // en caso de exito
		echo MiSiTiO::success('stock');
	} else { // en caso de error
		echo MiSiTiO::failure('stock');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['producto_id']) && !empty($_GET['bodega_id']) && !empty($_GET['area_id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['producto_id'] = urldecode($_GET['producto_id']);
		$arrSet['bodega_id'] = urldecode($_GET['bodega_id']);
		$arrSet['area_id'] = urldecode($_GET['area_id']);

		$objStock->set($arrSet);
		// obtener datos de $objStock
		$objStock->get();
		$link = '?edit&amp;'.'producto_id='.urlencode($_GET['producto_id']).'&amp;'.'bodega_id='.urlencode($_GET['bodega_id']).'&amp;'.'area_id='.urlencode($_GET['area_id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('stock'.$link, 'validarFormulario');
	$notNull = in_array('producto_id', $arrNotNull);
	$isPK = in_array('producto_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('producto_id', $arrFK)) {
			$objFK = $objStock->getProducto();
			$text = $objFK->nombre;
		} else $text = $objStock->producto_id;
		echo Form::text('producto_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'producto_id', 'producto_id', $objProductos->listado(), $objStock->producto_id, 'Producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('bodega_id', $arrNotNull);
	$isPK = in_array('bodega_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('bodega_id', $arrFK)) {
			$objFK = $objStock->getBodega();
			$text = $objFK->glosa;
		} else $text = $objStock->bodega_id;
		echo Form::text('bodega_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'bodega_id', 'bodega_id', $objBodegas->listado(), $objStock->bodega_id, 'Bodega', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('area_id', $arrNotNull);
	$isPK = in_array('area_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('area_id', $arrFK)) {
			$objFK = $objStock->getArea();
			$text = $objFK->glosa;
		} else $text = $objStock->area_id;
		echo Form::text('area_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'area_id', 'area_id', $objAreas->listado(), $objStock->area_id, 'Área', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('nivel', $arrNotNull);
	$isPK = in_array('nivel', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nivel', $objStock->nivel);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nivel', 'nivel', ($objStock->nivel===0 || $objStock->nivel==='0' || !empty($objStock->nivel)) ? $objStock->nivel : '', 'Nivel de stock por unidad de producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla stock en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['producto_id']) && !empty($_GET['bodega_id']) && !empty($_GET['area_id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['producto_id'] = urldecode($_GET['producto_id']);
		$arrSet['bodega_id'] = urldecode($_GET['bodega_id']);
		$arrSet['area_id'] = urldecode($_GET['area_id']);

	$objStock->set($arrSet);
	// eliminar objeto de la base de datos
	if($objStock->delete()) { // en caso de exito
		echo MiSiTiO::success('stock');
	} else { // en caso de error
		echo MiSiTiO::failure('stock');
	}
}

// tabla con datos de la tabla stock
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla stock
	$objStocks->setSelectStatement('producto_id, bodega_id, area_id, nivel');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['producto_id'])&&$columnas['producto_id']!='') {
			array_push($filtros, "producto_id = '".Stocks::$bd->proteger($columnas['producto_id'])."'");
			array_push($linkWhere, 'producto_id|'.$columnas['producto_id']);
		}
		if(isset($columnas['bodega_id'])&&$columnas['bodega_id']!='') {
			array_push($filtros, "bodega_id = '".Stocks::$bd->proteger($columnas['bodega_id'])."'");
			array_push($linkWhere, 'bodega_id|'.$columnas['bodega_id']);
		}
		if(isset($columnas['area_id'])&&$columnas['area_id']!='') {
			array_push($filtros, "area_id = '".Stocks::$bd->proteger($columnas['area_id'])."'");
			array_push($linkWhere, 'area_id|'.$columnas['area_id']);
		}
		if(isset($columnas['nivel'])&&$columnas['nivel']!='') {
			array_push($filtros, Stocks::$bd->like('nivel', $columnas['nivel']));
			array_push($linkWhere, 'nivel|'.$columnas['nivel']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objStocks->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objStocks->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objStocks->setOrderByStatement('producto_id,bodega_id,area_id');
		$linkOrderBy = 'orderby=producto_id,bodega_id,area_id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objStocks->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objStocks->getObjetos() as $objStock) {
		$fila = array();
		// agregar datos de las columnas
		// agregar producto_id a la fila
		if($objStock->producto_id!='') {
			$objFK = $objStock->getProducto();
			$glosaFK = $objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar bodega_id a la fila
		if($objStock->bodega_id!='') {
			$objFK = $objStock->getBodega();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar area_id a la fila
		if($objStock->area_id!='') {
			$objFK = $objStock->getArea();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar nivel a la fila
		array_push($fila, $objStock->nivel);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('stock?edit&amp;'.'producto_id='.urlencode($objStock->producto_id).'&amp;'.'bodega_id='.urlencode($objStock->bodega_id).'&amp;'.'area_id='.urlencode($objStock->area_id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'stock\', \''.'producto_id='.urlencode($objStock->producto_id).'&amp;'.'bodega_id='.urlencode($objStock->bodega_id).'&amp;'.'area_id='.urlencode($objStock->area_id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::select4table('producto_id', $objProductos->listado(), !empty($columnas['producto_id'])?$columnas['producto_id']:''),
		Form::select4table('bodega_id', $objBodegas->listado(), !empty($columnas['bodega_id'])?$columnas['bodega_id']:''),
		Form::select4table('area_id', $objAreas->listado(), !empty($columnas['area_id'])?$columnas['area_id']:''),
		Form::input4table('nivel', isset($columnas['nivel'])?$columnas['nivel']:'', 32),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'producto_id'=>Tabla::orderby('producto_id', 'stock?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=producto_id'),
		'bodega_id'=>Tabla::orderby('bodega_id', 'stock?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=bodega_id'),
		'area_id'=>Tabla::orderby('area_id', 'stock?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=area_id'),
		'nivel'=>Tabla::orderby('nivel', 'stock?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nivel'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'stock';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objStocks->count(), 'stock?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'stock?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'stock?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'stock?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
