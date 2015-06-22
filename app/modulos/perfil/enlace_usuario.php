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
 * Mantenedor para la tabla enlace_usuario
 * Enlaces personales de cada usuario
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-29
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla enlace_usuario y sus fk
require(DIR.'/class/db/final/Enlace_usuario.class.php'); // principal para tabla enlace_usuario
require(DIR.'/class/db/final/Usuario.class.php'); // clase para fk de la tabla usuario


// crear objetos a utilizar por el mantenedor
$objEnlace_usuario = new Enlace_usuario();
$objEnlace_usuarios = new Enlace_usuarios();
$objUsuario = new Usuario();
$objUsuarios = new Usuarios();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('usuario_id', 'url'); // columnas pks de la tabla enlace_usuario
$arrFK = array('usuario_id'); // columnas fks de la tabla enlace_usuario
$arrNotNull = array('usuario_id', 'url', 'nombre'); // columnas que no pueden ser nulas de la tabla enlace_usuario

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' enlace_usuario'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Enlaces personales de cada usuario'));

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

	// definidos a mano
	$arrSet['usuario_id'] = $Usuario->id;
	
	$objEnlace_usuario->set($arrSet);
	// guardar registro
	if($objEnlace_usuario->save()) { // en caso de exito
		echo MiSiTiO::success('enlace_usuario');
	} else { // en caso de error
		echo MiSiTiO::failure('enlace_usuario');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['url']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['usuario_id'] = $Usuario->id;
		$arrSet['url'] = urldecode($_GET['url']);

		$objEnlace_usuario->set($arrSet);
		// obtener datos de $objEnlace_usuario
		$objEnlace_usuario->get();
		$link = '?edit&amp;'.'url='.urlencode($_GET['url']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('enlace_usuario'.$link, 'validarFormulario');
	$notNull = in_array('url', $arrNotNull);
	$isPK = in_array('url', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('url', $objEnlace_usuario->url);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'url', 'url', ($objEnlace_usuario->url===0 || $objEnlace_usuario->url==='0' || !empty($objEnlace_usuario->url)) ? $objEnlace_usuario->url : '', 'Dirección url completa', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="200"');
	$notNull = in_array('nombre', $arrNotNull);
	$isPK = in_array('nombre', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nombre', $objEnlace_usuario->nombre);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombre', 'nombre', ($objEnlace_usuario->nombre===0 || $objEnlace_usuario->nombre==='0' || !empty($objEnlace_usuario->nombre)) ? $objEnlace_usuario->nombre : '', 'Nombre o descripción del enlace', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="60"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla enlace_usuario en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['url'])) {
	// setear pk
	$arrSet = array();
		$arrSet['usuario_id'] = $Usuario->id;
		$arrSet['url'] = urldecode($_GET['url']);

	$objEnlace_usuario->set($arrSet);
	// eliminar objeto de la base de datos
	if($objEnlace_usuario->delete()) { // en caso de exito
		echo MiSiTiO::success('enlace_usuario');
	} else { // en caso de error
		echo MiSiTiO::failure('enlace_usuario');
	}
}

// tabla con datos de la tabla enlace_usuario
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla enlace_usuario
	$objEnlace_usuarios->setSelectStatement('url, nombre');
	// set where
	// obtener campos desde la variable search
	$columnas = !empty($_GET['search']) ? extraerCampos($_GET['search']) : array();
	// definir filtros y generar link
	$filtros = array();
	$linkWhere = array();
	if(isset($columnas['url'])&&$columnas['url']!='') {
		array_push($filtros, Enlace_usuarios::$bd->like('url', $columnas['url']));
		array_push($linkWhere, 'url|'.$columnas['url']);
	}
	if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
		array_push($filtros, Enlace_usuarios::$bd->like('nombre', $columnas['nombre']));
		array_push($linkWhere, 'nombre|'.$columnas['nombre']);
	}
	// agregar filtro para usuario
	array_push($filtros, 'usuario_id = '.$Usuario->id);
	// agregar filtros al whereStatement
	$objEnlace_usuarios->setWhereStatement(implode(' AND ', $filtros));
	$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objEnlace_usuarios->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objEnlace_usuarios->setOrderByStatement('nombre');
		$linkOrderBy = 'orderby=nombre&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objEnlace_usuarios->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objEnlace_usuarios->getObjetos() as $objEnlace_usuario) {
		$fila = array();
		// agregar datos de las columnas
		// agregar url a la fila
		array_push($fila, $objEnlace_usuario->url);
		// agregar nombre a la fila
		array_push($fila, $objEnlace_usuario->nombre);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('enlace_usuario?edit&amp;'.'url='.urlencode($objEnlace_usuario->url).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'enlace_usuario\', \''.'url='.urlencode($objEnlace_usuario->url).'\')'));
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

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'url'=>Tabla::orderby('url', 'enlace_usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=url'),
		'nombre'=>Tabla::orderby('nombre', 'enlace_usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'enlace_usuario';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objEnlace_usuarios->count(), 'enlace_usuario?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'enlace_usuario?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'enlace_usuario?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'enlace_usuario?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
