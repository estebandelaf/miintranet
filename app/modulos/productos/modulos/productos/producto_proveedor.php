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
 * Mantenedor para la tabla producto_proveedor
 * Relación entre productos y los proveedores que los ofrecen
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla producto_proveedor y sus fk
require(DIR.'/class/db/final/Producto_proveedor.class.php'); // principal para tabla producto_proveedor
require(DIR.'/class/db/final/Producto.class.php'); // clase para fk de la tabla producto
require(DIR.'/class/db/final/Proveedor.class.php'); // clase para fk de la tabla proveedor


// crear objetos a utilizar por el mantenedor
$objProducto_proveedor = new Producto_proveedor();
$objProducto_proveedors = new Producto_proveedors();
$objProducto = new Producto();
$objProductos = new Productos();
$objProveedor = new Proveedor();
$objProveedors = new Proveedors();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('producto_id', 'proveedor_id'); // columnas pks de la tabla producto_proveedor
$arrFK = array('producto_id', 'proveedor_id'); // columnas fks de la tabla producto_proveedor
$arrNotNull = array('producto_id', 'proveedor_id', 'valor'); // columnas que no pueden ser nulas de la tabla producto_proveedor

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' producto_proveedor'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Relación entre productos y los proveedores que los ofrecen'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['producto_id']) && !empty($_GET['proveedor_id'])) {
		$arrSet['producto_id'] = urldecode($_GET['producto_id']);
		$arrSet['proveedor_id'] = urldecode($_GET['proveedor_id']);

	}
	// definir otros campos
	if(!isset($arrSet['producto_id'])) $arrSet['producto_id'] = (in_array('producto_id', $arrFK)&&empty($_POST['producto_id'])) ? null : $_POST['producto_id'];
	if(!isset($arrSet['proveedor_id'])) $arrSet['proveedor_id'] = (in_array('proveedor_id', $arrFK)&&empty($_POST['proveedor_id'])) ? null : $_POST['proveedor_id'];
	if(!isset($arrSet['valor'])) $arrSet['valor'] = (in_array('valor', $arrFK)&&empty($_POST['valor'])) ? null : $_POST['valor'];

	$objProducto_proveedor->set($arrSet);
	// guardar registro
	if($objProducto_proveedor->save()) { // en caso de exito
		echo MiSiTiO::success('producto_proveedor');
	} else { // en caso de error
		echo MiSiTiO::failure('producto_proveedor');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['producto_id']) && !empty($_GET['proveedor_id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['producto_id'] = urldecode($_GET['producto_id']);
		$arrSet['proveedor_id'] = urldecode($_GET['proveedor_id']);

		$objProducto_proveedor->set($arrSet);
		// obtener datos de $objProducto_proveedor
		$objProducto_proveedor->get();
		$link = '?edit&amp;'.'producto_id='.urlencode($_GET['producto_id']).'&amp;'.'proveedor_id='.urlencode($_GET['proveedor_id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('producto_proveedor'.$link, 'validarFormulario');
	$notNull = in_array('producto_id', $arrNotNull);
	$isPK = in_array('producto_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('producto_id', $arrFK)) {
			$objFK = $objProducto_proveedor->getProducto();
			$text = $objFK->nombre;
		} else $text = $objProducto_proveedor->producto_id;
		echo Form::text('producto_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'producto_id', 'producto_id', $objProductos->listado(), $objProducto_proveedor->producto_id, 'Código del producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('proveedor_id', $arrNotNull);
	$isPK = in_array('proveedor_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('proveedor_id', $arrFK)) {
			$objFK = $objProducto_proveedor->getProveedor();
			$text = $objFK->razonsocial;
		} else $text = $objProducto_proveedor->proveedor_id;
		echo Form::text('proveedor_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'proveedor_id', 'proveedor_id', $objProveedors->listado(), $objProducto_proveedor->proveedor_id, 'Rut del proveedor', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('valor', $arrNotNull);
	$isPK = in_array('valor', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('valor', $objProducto_proveedor->valor);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'valor', 'valor', ($objProducto_proveedor->valor===0 || $objProducto_proveedor->valor==='0' || !empty($objProducto_proveedor->valor)) ? $objProducto_proveedor->valor : '', 'Valor de compra por unidad definida en el producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla producto_proveedor en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['producto_id']) && !empty($_GET['proveedor_id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['producto_id'] = urldecode($_GET['producto_id']);
		$arrSet['proveedor_id'] = urldecode($_GET['proveedor_id']);

	$objProducto_proveedor->set($arrSet);
	// eliminar objeto de la base de datos
	if($objProducto_proveedor->delete()) { // en caso de exito
		echo MiSiTiO::success('producto_proveedor');
	} else { // en caso de error
		echo MiSiTiO::failure('producto_proveedor');
	}
}

// tabla con datos de la tabla producto_proveedor
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla producto_proveedor
	$objProducto_proveedors->setSelectStatement('producto_id, proveedor_id, valor');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['producto_id'])&&$columnas['producto_id']!='') {
			array_push($filtros, "producto_id = '".Producto_proveedors::$bd->proteger($columnas['producto_id'])."'");
			array_push($linkWhere, 'producto_id|'.$columnas['producto_id']);
		}
		if(isset($columnas['proveedor_id'])&&$columnas['proveedor_id']!='') {
			array_push($filtros, "proveedor_id = '".Producto_proveedors::$bd->proteger($columnas['proveedor_id'])."'");
			array_push($linkWhere, 'proveedor_id|'.$columnas['proveedor_id']);
		}
		if(isset($columnas['valor'])&&$columnas['valor']!='') {
			array_push($filtros, Producto_proveedors::$bd->like('valor', $columnas['valor']));
			array_push($linkWhere, 'valor|'.$columnas['valor']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objProducto_proveedors->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objProducto_proveedors->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objProducto_proveedors->setOrderByStatement('producto_id,proveedor_id');
		$linkOrderBy = 'orderby=producto_id,proveedor_id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objProducto_proveedors->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objProducto_proveedors->getObjetos() as $objProducto_proveedor) {
		$fila = array();
		// agregar datos de las columnas
		// agregar producto_id a la fila
		if($objProducto_proveedor->producto_id!='') {
			$objFK = $objProducto_proveedor->getProducto();
			$glosaFK = $objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar proveedor_id a la fila
		if($objProducto_proveedor->proveedor_id!='') {
			$objFK = $objProducto_proveedor->getProveedor();
			$glosaFK = $objFK->razonsocial;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar valor a la fila
		array_push($fila, $objProducto_proveedor->valor);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('producto_proveedor?edit&amp;'.'producto_id='.urlencode($objProducto_proveedor->producto_id).'&amp;'.'proveedor_id='.urlencode($objProducto_proveedor->proveedor_id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'producto_proveedor\', \''.'producto_id='.urlencode($objProducto_proveedor->producto_id).'&amp;'.'proveedor_id='.urlencode($objProducto_proveedor->proveedor_id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::select4table('producto_id', $objProductos->listado(), !empty($columnas['producto_id'])?$columnas['producto_id']:''),
		Form::select4table('proveedor_id', $objProveedors->listado(), !empty($columnas['proveedor_id'])?$columnas['proveedor_id']:''),
		Form::input4table('valor', isset($columnas['valor'])?$columnas['valor']:'', 32),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'producto_id'=>Tabla::orderby('producto_id', 'producto_proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=producto_id'),
		'proveedor_id'=>Tabla::orderby('proveedor_id', 'producto_proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=proveedor_id'),
		'valor'=>Tabla::orderby('valor', 'producto_proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=valor'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'producto_proveedor';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objProducto_proveedors->count(), 'producto_proveedor?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'producto_proveedor?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'producto_proveedor?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'producto_proveedor?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
