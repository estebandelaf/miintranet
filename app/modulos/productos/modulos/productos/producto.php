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
 * Mantenedor para la tabla producto
 * Productos, ya sean materias primas, productos finales o insumos de la empresa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla producto y sus fk
require(DIR.'/class/db/final/Producto.class.php'); // principal para tabla producto
require(DIR.'/class/db/final/Producto_categoria.class.php'); // clase para fk de la tabla producto_categoria
require(DIR.'/class/db/final/Unidad.class.php'); // clase para fk de la tabla unidad


// crear objetos a utilizar por el mantenedor
$objProducto = new Producto();
$objProductos = new Productos();
$objProducto_categoria = new Producto_categoria();
$objProducto_categorias = new Producto_categorias();
$objUnidad = new Unidad();
$objUnidads = new Unidads();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla producto
$arrFK = array('producto_categoria_id', 'unidad_id'); // columnas fks de la tabla producto
$arrNotNull = array('id', 'nombre', 'producto_categoria_id', 'unidad_id', 'valor'); // columnas que no pueden ser nulas de la tabla producto

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' producto'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Productos, ya sean materias primas, productos finales o insumos de la empresa'));

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
	if(!isset($arrSet['producto_categoria_id'])) $arrSet['producto_categoria_id'] = (in_array('producto_categoria_id', $arrFK)&&empty($_POST['producto_categoria_id'])) ? null : $_POST['producto_categoria_id'];
	if(!isset($arrSet['unidad_id'])) $arrSet['unidad_id'] = (in_array('unidad_id', $arrFK)&&empty($_POST['unidad_id'])) ? null : $_POST['unidad_id'];
	if(!isset($arrSet['valor'])) $arrSet['valor'] = (in_array('valor', $arrFK)&&empty($_POST['valor'])) ? null : $_POST['valor'];

	$objProducto->set($arrSet);
	// guardar registro
	if($objProducto->save()) { // en caso de exito
		echo MiSiTiO::success('producto');
	} else { // en caso de error
		echo MiSiTiO::failure('producto');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objProducto->set($arrSet);
		// obtener datos de $objProducto
		$objProducto->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('producto'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objProducto->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', ($objProducto->id===0 || $objProducto->id==='0' || !empty($objProducto->id)) ? $objProducto->id : '', 'Identificador', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="20"');
	$notNull = in_array('nombre', $arrNotNull);
	$isPK = in_array('nombre', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nombre', $objProducto->nombre);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombre', 'nombre', ($objProducto->nombre===0 || $objProducto->nombre==='0' || !empty($objProducto->nombre)) ? $objProducto->nombre : '', 'Nombre', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('producto_categoria_id', $arrNotNull);
	$isPK = in_array('producto_categoria_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('producto_categoria_id', $arrFK)) {
			$objFK = $objProducto->getProducto_categoria();
			$text = $objFK->glosa;
		} else $text = $objProducto->producto_categoria_id;
		echo Form::text('producto_categoria_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'producto_categoria_id', 'producto_categoria_id', $objProducto_categorias->listado(), $objProducto->producto_categoria_id, 'Categoría final del producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('unidad_id', $arrNotNull);
	$isPK = in_array('unidad_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('unidad_id', $arrFK)) {
			$objFK = $objProducto->getUnidad();
			$text = $objFK->glosa;
		} else $text = $objProducto->unidad_id;
		echo Form::text('unidad_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'unidad_id', 'unidad_id', $objUnidads->listado(), $objProducto->unidad_id, 'Tipo de unidad a utilizar por el producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('valor', $arrNotNull);
	$isPK = in_array('valor', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('valor', $objProducto->valor);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'valor', 'valor', ($objProducto->valor===0 || $objProducto->valor==='0' || !empty($objProducto->valor)) ? $objProducto->valor : '0', 'Valor de venta por unidad del producto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla producto en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objProducto->set($arrSet);
	// eliminar objeto de la base de datos
	if($objProducto->delete()) { // en caso de exito
		echo MiSiTiO::success('producto');
	} else { // en caso de error
		echo MiSiTiO::failure('producto');
	}
}

// tabla con datos de la tabla producto
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla producto
	$objProductos->setSelectStatement('id, nombre, producto_categoria_id, unidad_id, valor');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, Productos::$bd->like('id', $columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
			array_push($filtros, Productos::$bd->like('nombre', $columnas['nombre']));
			array_push($linkWhere, 'nombre|'.$columnas['nombre']);
		}
		if(isset($columnas['producto_categoria_id'])&&$columnas['producto_categoria_id']!='') {
			array_push($filtros, "producto_categoria_id = '".Productos::$bd->proteger($columnas['producto_categoria_id'])."'");
			array_push($linkWhere, 'producto_categoria_id|'.$columnas['producto_categoria_id']);
		}
		if(isset($columnas['unidad_id'])&&$columnas['unidad_id']!='') {
			array_push($filtros, "unidad_id = '".Productos::$bd->proteger($columnas['unidad_id'])."'");
			array_push($linkWhere, 'unidad_id|'.$columnas['unidad_id']);
		}
		if(isset($columnas['valor'])&&$columnas['valor']!='') {
			array_push($filtros, Productos::$bd->like('valor', $columnas['valor']));
			array_push($linkWhere, 'valor|'.$columnas['valor']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objProductos->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objProductos->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objProductos->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objProductos->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objProductos->getObjetos() as $objProducto) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objProducto->id);
		// agregar nombre a la fila
		array_push($fila, $objProducto->nombre);
		// agregar producto_categoria_id a la fila
		if($objProducto->producto_categoria_id!='') {
			$objFK = $objProducto->getProducto_categoria();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar unidad_id a la fila
		if($objProducto->unidad_id!='') {
			$objFK = $objProducto->getUnidad();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar valor a la fila
		array_push($fila, $objProducto->valor);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('producto?edit&amp;'.'id='.urlencode($objProducto->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'producto\', \''.'id='.urlencode($objProducto->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 20),
		Form::input4table('nombre', isset($columnas['nombre'])?$columnas['nombre']:'', 30),
		Form::select4table('producto_categoria_id', $objProducto_categorias->listado(), !empty($columnas['producto_categoria_id'])?$columnas['producto_categoria_id']:''),
		Form::select4table('unidad_id', $objUnidads->listado(), !empty($columnas['unidad_id'])?$columnas['unidad_id']:''),
		Form::input4table('valor', isset($columnas['valor'])?$columnas['valor']:'', 32),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'producto?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'nombre'=>Tabla::orderby('nombre', 'producto?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),
		'producto_categoria_id'=>Tabla::orderby('producto_categoria_id', 'producto?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=producto_categoria_id'),
		'unidad_id'=>Tabla::orderby('unidad_id', 'producto?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=unidad_id'),
		'valor'=>Tabla::orderby('valor', 'producto?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=valor'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'producto';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objProductos->count(), 'producto?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'producto?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'producto?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'producto?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
