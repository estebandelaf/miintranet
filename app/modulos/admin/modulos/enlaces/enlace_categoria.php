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
 * Mantenedor para la tabla enlace_categoria
 * Categorí­as de los enlaces generales de la aplicación
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla enlace_categoria y sus fk
require(DIR.'/class/db/final/Enlace_categoria.class.php'); // principal para tabla enlace_categoria


// crear objetos a utilizar por el mantenedor
$objEnlace_categoria = new Enlace_categoria();
$objEnlace_categorias = new Enlace_categorias();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla enlace_categoria
$arrFK = array(''); // columnas fks de la tabla enlace_categoria
$arrNotNull = array('id', 'nombre', 'orden'); // columnas que no pueden ser nulas de la tabla enlace_categoria

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' enlace_categoria'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Categorí­as de los enlaces generales de la aplicación'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['id'])) {
		$arrSet['id'] = urldecode($_GET['id']);

	}
	// definir otros campos
	if(!isset($arrSet['nombre'])) $arrSet['nombre'] = (in_array('nombre', $arrFK)&&empty($_POST['nombre'])) ? null : $_POST['nombre'];
	if(!isset($arrSet['orden'])) $arrSet['orden'] = (in_array('orden', $arrFK)&&empty($_POST['orden'])) ? null : $_POST['orden'];

	$objEnlace_categoria->set($arrSet);
	// guardar registro
	if($objEnlace_categoria->save()) { // en caso de exito
		echo MiSiTiO::success('enlace_categoria');
	} else { // en caso de error
		echo MiSiTiO::failure('enlace_categoria');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objEnlace_categoria->set($arrSet);
		// obtener datos de $objEnlace_categoria
		$objEnlace_categoria->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('enlace_categoria'.$link, 'validarFormulario');
	$notNull = in_array('nombre', $arrNotNull);
	$isPK = in_array('nombre', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nombre', $objEnlace_categoria->nombre);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombre', 'nombre', ($objEnlace_categoria->nombre===0 || $objEnlace_categoria->nombre==='0' || !empty($objEnlace_categoria->nombre)) ? $objEnlace_categoria->nombre : '', 'Nombre de la categorí­a', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="40"');
	$notNull = in_array('orden', $arrNotNull);
	$isPK = in_array('orden', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('orden', $objEnlace_categoria->orden);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'orden', 'orden', ($objEnlace_categoria->orden===0 || $objEnlace_categoria->orden==='0' || !empty($objEnlace_categoria->orden)) ? $objEnlace_categoria->orden : '99', 'Order en que serán mostradas las categorías', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla enlace_categoria en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objEnlace_categoria->set($arrSet);
	// eliminar objeto de la base de datos
	if($objEnlace_categoria->delete()) { // en caso de exito
		echo MiSiTiO::success('enlace_categoria');
	} else { // en caso de error
		echo MiSiTiO::failure('enlace_categoria');
	}
}

// tabla con datos de la tabla enlace_categoria
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla enlace_categoria
	$objEnlace_categorias->setSelectStatement('id, nombre, orden');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, Enlace_categorias::$bd->like('id', $columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
			array_push($filtros, Enlace_categorias::$bd->like('nombre', $columnas['nombre']));
			array_push($linkWhere, 'nombre|'.$columnas['nombre']);
		}
		if(isset($columnas['orden'])&&$columnas['orden']!='') {
			array_push($filtros, Enlace_categorias::$bd->like('orden', $columnas['orden']));
			array_push($linkWhere, 'orden|'.$columnas['orden']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objEnlace_categorias->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objEnlace_categorias->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objEnlace_categorias->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objEnlace_categorias->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objEnlace_categorias->getObjetos() as $objEnlace_categoria) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objEnlace_categoria->id);
		// agregar nombre a la fila
		array_push($fila, $objEnlace_categoria->nombre);
		// agregar orden a la fila
		array_push($fila, $objEnlace_categoria->orden);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('enlace_categoria?edit&amp;'.'id='.urlencode($objEnlace_categoria->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'enlace_categoria\', \''.'id='.urlencode($objEnlace_categoria->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('nombre', isset($columnas['nombre'])?$columnas['nombre']:'', 40),
		Form::input4table('orden', isset($columnas['orden'])?$columnas['orden']:'', 16),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'enlace_categoria?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'nombre'=>Tabla::orderby('nombre', 'enlace_categoria?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),
		'orden'=>Tabla::orderby('orden', 'enlace_categoria?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=orden'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'enlace_categoria';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objEnlace_categorias->count(), 'enlace_categoria?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'enlace_categoria?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'enlace_categoria?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'enlace_categoria?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
