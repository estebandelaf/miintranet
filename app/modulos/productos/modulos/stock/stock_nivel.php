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
 * Mantenedor para la tabla stock_nivel
 * Niveles de stock requeridos
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla stock_nivel y sus fk
require(DIR.'/class/db/final/Stock_nivel.class.php'); // principal para tabla stock_nivel
require(DIR.'/class/db/final/Producto.class.php'); // clase para fk de la tabla producto
require(DIR.'/class/db/final/Bodega.class.php'); // clase para fk de la tabla bodega
require(DIR.'/class/db/final/Area.class.php'); // clase para fk de la tabla area


// crear objetos a utilizar por el mantenedor
$objStock_nivel = new Stock_nivel();
$objStock_nivels = new Stock_nivels();
$objProducto = new Producto();
$objProductos = new Productos();
$objBodega = new Bodega();
$objBodegas = new Bodegas();
$objArea = new Area();
$objAreas = new Areas();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('producto_id', 'bodega_id', 'area_id'); // columnas pks de la tabla stock_nivel
$arrFK = array('producto_id', 'bodega_id', 'area_id'); // columnas fks de la tabla stock_nivel
$arrNotNull = array('producto_id', 'bodega_id', 'area_id', 'bajo'); // columnas que no pueden ser nulas de la tabla stock_nivel

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' stock_nivel'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Niveles de stock requeridos'));

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
	if(!isset($arrSet['critico'])) $arrSet['critico'] = (in_array('critico', $arrFK)&&empty($_POST['critico'])) ? null : $_POST['critico'];
	if(!isset($arrSet['bajo'])) $arrSet['bajo'] = (in_array('bajo', $arrFK)&&empty($_POST['bajo'])) ? null : $_POST['bajo'];
	if(!isset($arrSet['medio'])) $arrSet['medio'] = (in_array('medio', $arrFK)&&empty($_POST['medio'])) ? null : $_POST['medio'];
	if(!isset($arrSet['normal'])) $arrSet['normal'] = (in_array('normal', $arrFK)&&empty($_POST['normal'])) ? null : $_POST['normal'];
	if(!isset($arrSet['alto'])) $arrSet['alto'] = (in_array('alto', $arrFK)&&empty($_POST['alto'])) ? null : $_POST['alto'];

	$objStock_nivel->set($arrSet);
	// guardar registro
	if($objStock_nivel->save()) { // en caso de exito
		echo MiSiTiO::success('stock_nivel');
	} else { // en caso de error
		echo MiSiTiO::failure('stock_nivel');
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

		$objStock_nivel->set($arrSet);
		// obtener datos de $objStock_nivel
		$objStock_nivel->get();
		$link = '?edit&amp;'.'producto_id='.urlencode($_GET['producto_id']).'&amp;'.'bodega_id='.urlencode($_GET['bodega_id']).'&amp;'.'area_id='.urlencode($_GET['area_id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('stock_nivel'.$link, 'validarFormulario');
	$notNull = in_array('producto_id', $arrNotNull);
	$isPK = in_array('producto_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('producto_id', $arrFK)) {
			$objFK = $objStock_nivel->getProducto();
			$text = $objFK->nombre;
		} else $text = $objStock_nivel->producto_id;
		echo Form::text('producto_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'producto_id', 'producto_id', $objProductos->listado(), $objStock_nivel->producto_id, 'Producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('bodega_id', $arrNotNull);
	$isPK = in_array('bodega_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('bodega_id', $arrFK)) {
			$objFK = $objStock_nivel->getBodega();
			$text = $objFK->glosa;
		} else $text = $objStock_nivel->bodega_id;
		echo Form::text('bodega_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'bodega_id', 'bodega_id', $objBodegas->listado(), $objStock_nivel->bodega_id, 'Bodega', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('area_id', $arrNotNull);
	$isPK = in_array('area_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('area_id', $arrFK)) {
			$objFK = $objStock_nivel->getArea();
			$text = $objFK->glosa;
		} else $text = $objStock_nivel->area_id;
		echo Form::text('area_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'area_id', 'area_id', $objAreas->listado(), $objStock_nivel->area_id, 'Área', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('critico', $arrNotNull);
	$isPK = in_array('critico', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('critico', $objStock_nivel->critico);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'critico', 'critico', ($objStock_nivel->critico===0 || $objStock_nivel->critico==='0' || !empty($objStock_nivel->critico)) ? $objStock_nivel->critico : '', 'Nivel por unidad de producto para estado crítico', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('bajo', $arrNotNull);
	$isPK = in_array('bajo', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('bajo', $objStock_nivel->bajo);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'bajo', 'bajo', ($objStock_nivel->bajo===0 || $objStock_nivel->bajo==='0' || !empty($objStock_nivel->bajo)) ? $objStock_nivel->bajo : '', 'Nivel por unidad de producto para estado bajo', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('medio', $arrNotNull);
	$isPK = in_array('medio', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('medio', $objStock_nivel->medio);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'medio', 'medio', ($objStock_nivel->medio===0 || $objStock_nivel->medio==='0' || !empty($objStock_nivel->medio)) ? $objStock_nivel->medio : '', 'Nivel por unidad de producto para estado medio', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('normal', $arrNotNull);
	$isPK = in_array('normal', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('normal', $objStock_nivel->normal);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'normal', 'normal', ($objStock_nivel->normal===0 || $objStock_nivel->normal==='0' || !empty($objStock_nivel->normal)) ? $objStock_nivel->normal : '', 'Nivel por unidad de producto para estado normal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('alto', $arrNotNull);
	$isPK = in_array('alto', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('alto', $objStock_nivel->alto);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'alto', 'alto', ($objStock_nivel->alto===0 || $objStock_nivel->alto==='0' || !empty($objStock_nivel->alto)) ? $objStock_nivel->alto : '', 'Nivel por unidad de producto para estado alto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla stock_nivel en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['producto_id']) && !empty($_GET['bodega_id']) && !empty($_GET['area_id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['producto_id'] = urldecode($_GET['producto_id']);
		$arrSet['bodega_id'] = urldecode($_GET['bodega_id']);
		$arrSet['area_id'] = urldecode($_GET['area_id']);

	$objStock_nivel->set($arrSet);
	// eliminar objeto de la base de datos
	if($objStock_nivel->delete()) { // en caso de exito
		echo MiSiTiO::success('stock_nivel');
	} else { // en caso de error
		echo MiSiTiO::failure('stock_nivel');
	}
}

// tabla con datos de la tabla stock_nivel
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla stock_nivel
	$objStock_nivels->setSelectStatement('producto_id, bodega_id, area_id, critico, bajo, medio, normal, alto');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['producto_id'])&&$columnas['producto_id']!='') {
			array_push($filtros, "producto_id = '".Stock_nivels::$bd->proteger($columnas['producto_id'])."'");
			array_push($linkWhere, 'producto_id|'.$columnas['producto_id']);
		}
		if(isset($columnas['bodega_id'])&&$columnas['bodega_id']!='') {
			array_push($filtros, "bodega_id = '".Stock_nivels::$bd->proteger($columnas['bodega_id'])."'");
			array_push($linkWhere, 'bodega_id|'.$columnas['bodega_id']);
		}
		if(isset($columnas['area_id'])&&$columnas['area_id']!='') {
			array_push($filtros, "area_id = '".Stock_nivels::$bd->proteger($columnas['area_id'])."'");
			array_push($linkWhere, 'area_id|'.$columnas['area_id']);
		}
		if(isset($columnas['critico'])&&$columnas['critico']!='') {
			array_push($filtros, Stock_nivels::$bd->like('critico', $columnas['critico']));
			array_push($linkWhere, 'critico|'.$columnas['critico']);
		}
		if(isset($columnas['bajo'])&&$columnas['bajo']!='') {
			array_push($filtros, Stock_nivels::$bd->like('bajo', $columnas['bajo']));
			array_push($linkWhere, 'bajo|'.$columnas['bajo']);
		}
		if(isset($columnas['medio'])&&$columnas['medio']!='') {
			array_push($filtros, Stock_nivels::$bd->like('medio', $columnas['medio']));
			array_push($linkWhere, 'medio|'.$columnas['medio']);
		}
		if(isset($columnas['normal'])&&$columnas['normal']!='') {
			array_push($filtros, Stock_nivels::$bd->like('normal', $columnas['normal']));
			array_push($linkWhere, 'normal|'.$columnas['normal']);
		}
		if(isset($columnas['alto'])&&$columnas['alto']!='') {
			array_push($filtros, Stock_nivels::$bd->like('alto', $columnas['alto']));
			array_push($linkWhere, 'alto|'.$columnas['alto']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objStock_nivels->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objStock_nivels->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objStock_nivels->setOrderByStatement('producto_id,bodega_id,area_id');
		$linkOrderBy = 'orderby=producto_id,bodega_id,area_id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objStock_nivels->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objStock_nivels->getObjetos() as $objStock_nivel) {
		$fila = array();
		// agregar datos de las columnas
		// agregar producto_id a la fila
		if($objStock_nivel->producto_id!='') {
			$objFK = $objStock_nivel->getProducto();
			$glosaFK = $objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar bodega_id a la fila
		if($objStock_nivel->bodega_id!='') {
			$objFK = $objStock_nivel->getBodega();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar area_id a la fila
		if($objStock_nivel->area_id!='') {
			$objFK = $objStock_nivel->getArea();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar critico a la fila
		array_push($fila, $objStock_nivel->critico);
		// agregar bajo a la fila
		array_push($fila, $objStock_nivel->bajo);
		// agregar medio a la fila
		array_push($fila, $objStock_nivel->medio);
		// agregar normal a la fila
		array_push($fila, $objStock_nivel->normal);
		// agregar alto a la fila
		array_push($fila, $objStock_nivel->alto);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('stock_nivel?edit&amp;'.'producto_id='.urlencode($objStock_nivel->producto_id).'&amp;'.'bodega_id='.urlencode($objStock_nivel->bodega_id).'&amp;'.'area_id='.urlencode($objStock_nivel->area_id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'stock_nivel\', \''.'producto_id='.urlencode($objStock_nivel->producto_id).'&amp;'.'bodega_id='.urlencode($objStock_nivel->bodega_id).'&amp;'.'area_id='.urlencode($objStock_nivel->area_id).'\')'));
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
		Form::input4table('critico', isset($columnas['critico'])?$columnas['critico']:'', 32),
		Form::input4table('bajo', isset($columnas['bajo'])?$columnas['bajo']:'', 32),
		Form::input4table('medio', isset($columnas['medio'])?$columnas['medio']:'', 32),
		Form::input4table('normal', isset($columnas['normal'])?$columnas['normal']:'', 32),
		Form::input4table('alto', isset($columnas['alto'])?$columnas['alto']:'', 32),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'producto_id'=>Tabla::orderby('producto_id', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=producto_id'),
		'bodega_id'=>Tabla::orderby('bodega_id', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=bodega_id'),
		'area_id'=>Tabla::orderby('area_id', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=area_id'),
		'critico'=>Tabla::orderby('critico', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=critico'),
		'bajo'=>Tabla::orderby('bajo', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=bajo'),
		'medio'=>Tabla::orderby('medio', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=medio'),
		'normal'=>Tabla::orderby('normal', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=normal'),
		'alto'=>Tabla::orderby('alto', 'stock_nivel?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=alto'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'stock_nivel';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objStock_nivels->count(), 'stock_nivel?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'stock_nivel?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'stock_nivel?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'stock_nivel?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
