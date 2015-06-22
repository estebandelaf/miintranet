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
 * Mantenedor para la tabla producto_categoria
 * Categorías y sub categorías para clasificar productos
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla producto_categoria y sus fk
require(DIR.'/class/db/final/Producto_categoria.class.php'); // principal para tabla producto_categoria
require(DIR.'/class/db/final/Producto_categoria.class.php'); // clase para fk de la tabla producto_categoria


// crear objetos a utilizar por el mantenedor
$objProducto_categoria = new Producto_categoria();
$objProducto_categorias = new Producto_categorias();
$objProducto_categoria = new Producto_categoria();
$objProducto_categorias = new Producto_categorias();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla producto_categoria
$arrFK = array('producto_categoria_id'); // columnas fks de la tabla producto_categoria
$arrNotNull = array('id', 'glosa'); // columnas que no pueden ser nulas de la tabla producto_categoria

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' producto_categoria'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Categorías y sub categorías para clasificar productos'));

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
	if(!isset($arrSet['producto_categoria_id'])) $arrSet['producto_categoria_id'] = (in_array('producto_categoria_id', $arrFK)&&empty($_POST['producto_categoria_id'])) ? null : $_POST['producto_categoria_id'];

	$objProducto_categoria->set($arrSet);
	// guardar registro
	if($objProducto_categoria->save()) { // en caso de exito
		echo MiSiTiO::success('producto_categoria');
	} else { // en caso de error
		echo MiSiTiO::failure('producto_categoria');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objProducto_categoria->set($arrSet);
		// obtener datos de $objProducto_categoria
		$objProducto_categoria->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('producto_categoria'.$link, 'validarFormulario');
	$notNull = in_array('glosa', $arrNotNull);
	$isPK = in_array('glosa', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('glosa', $objProducto_categoria->glosa);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'glosa', 'glosa', ($objProducto_categoria->glosa===0 || $objProducto_categoria->glosa==='0' || !empty($objProducto_categoria->glosa)) ? $objProducto_categoria->glosa : '', 'Glosa', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="40"');
	$notNull = in_array('producto_categoria_id', $arrNotNull);
	$isPK = in_array('producto_categoria_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('producto_categoria_id', $arrFK)) {
			$objFK = $objProducto_categoria->getProducto_categoria();
			$text = $objFK->glosa;
		} else $text = $objProducto_categoria->producto_categoria_id;
		echo Form::text('producto_categoria_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'producto_categoria_id', 'producto_categoria_id', $objProducto_categorias->listado(), $objProducto_categoria->producto_categoria_id, 'Categoría de producto padre (si es el nivel más alto no especificar)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla producto_categoria en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objProducto_categoria->set($arrSet);
	// eliminar objeto de la base de datos
	if($objProducto_categoria->delete()) { // en caso de exito
		echo MiSiTiO::success('producto_categoria');
	} else { // en caso de error
		echo MiSiTiO::failure('producto_categoria');
	}
}

// tabla con datos de la tabla producto_categoria
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla producto_categoria
	$objProducto_categorias->setSelectStatement('id, glosa, producto_categoria_id');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, Producto_categorias::$bd->like('id', $columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['glosa'])&&$columnas['glosa']!='') {
			array_push($filtros, Producto_categorias::$bd->like('glosa', $columnas['glosa']));
			array_push($linkWhere, 'glosa|'.$columnas['glosa']);
		}
		if(isset($columnas['producto_categoria_id'])&&$columnas['producto_categoria_id']!='') {
			array_push($filtros, "producto_categoria_id = '".Producto_categorias::$bd->proteger($columnas['producto_categoria_id'])."'");
			array_push($linkWhere, 'producto_categoria_id|'.$columnas['producto_categoria_id']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objProducto_categorias->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objProducto_categorias->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objProducto_categorias->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objProducto_categorias->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objProducto_categorias->getObjetos() as $objProducto_categoria) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objProducto_categoria->id);
		// agregar glosa a la fila
		array_push($fila, $objProducto_categoria->glosa);
		// agregar producto_categoria_id a la fila
		if($objProducto_categoria->producto_categoria_id!='') {
			$objFK = $objProducto_categoria->getProducto_categoria();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('producto_categoria?edit&amp;'.'id='.urlencode($objProducto_categoria->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'producto_categoria\', \''.'id='.urlencode($objProducto_categoria->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('glosa', isset($columnas['glosa'])?$columnas['glosa']:'', 40),
		Form::select4table('producto_categoria_id', $objProducto_categorias->listado(), !empty($columnas['producto_categoria_id'])?$columnas['producto_categoria_id']:''),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'producto_categoria?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'glosa'=>Tabla::orderby('glosa', 'producto_categoria?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=glosa'),
		'producto_categoria_id'=>Tabla::orderby('producto_categoria_id', 'producto_categoria?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=producto_categoria_id'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'producto_categoria';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objProducto_categorias->count(), 'producto_categoria?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'producto_categoria?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'producto_categoria?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'producto_categoria?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
