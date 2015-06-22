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
 * Mantenedor para la tabla usuario
 * Usuarios del sistema y personal de la empresa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-03
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla usuario y sus fk
require(DIR.'/class/db/final/Usuario.class.php'); // principal para tabla usuario


// crear objetos a utilizar por el mantenedor
$objUsuario = new Usuario();
$objUsuarios = new Usuarios();

// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla usuario
$arrFK = array('sucursal_id', 'cargo_id', 'salud_id', 'afp_id'); // columnas fks de la tabla usuario
$arrNotNull = array('id', 'nombre', 'apellido', 'fechanacimiento', 'lang', 'activo', 'sucursal_id', 'cargo_id', 'ingreso'); // columnas que no pueden ser nulas de la tabla usuario

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' usuario'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Usuarios del sistema y personal de la empresa'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk (solo se pueden editar los usuarios aqui)
	$arrSet['id'] = urldecode($_GET['id']);
	// definir otros campos
	$arrSet['usuario'] = (in_array('usuario', $arrFK)&&empty($_POST['usuario'])) ? null : $_POST['usuario'];
	$arrSet['clave'] = (in_array('apellido', $arrFK)&&empty($_POST['apellido'])) ? null : $_POST['clave'];

	// guardar registro
	if($objUsuario->saveUsuario($arrSet)) { // en caso de exito
		echo MiSiTiO::success('usuario');
	} else { // en caso de error
		echo MiSiTiO::failure('usuario');
	}
}

// formulario para editar
else if(isset($_GET['edit']) && !empty($_GET['id'])) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objUsuario->set($arrSet);
		// obtener datos de $objUsuario
		$objUsuario->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('usuario'.$link, 'validarFormulario');
	
	// campos agregados a mano
	echo Form::text('id', $objUsuario->apellido.', '.$objUsuario->nombre);
	echo Form::input(MiSiTiO::generar('form/asterisco.html').'usuario', 'usuario', !empty($objUsuario->usuario) ? $objUsuario->usuario : '', 'Nombre de usuario', MiSiTiO::generar('form/classObligatorio.html').' maxlength="20"');
	echo Form::pass('clave', 'clave', 'Clave del usuario');
	
	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// tabla con datos de la tabla usuario
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla usuario
	$objUsuarios->setSelectStatement('id, nombre, apellido, usuario');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
	}
	// definir filtros y generar link
	$filtros = array();
	array_push($filtros, "activo = 1");
	$linkWhere = array();
	// mostrar solo usuarios activos
	if(isset($columnas['id'])&&$columnas['id']!='') {
		array_push($filtros, 'id='.Usuarios::$bd->proteger($columnas['id']));
		array_push($linkWhere, 'id|'.$columnas['id']);
	}
	if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
		array_push($filtros, Usuarios::$bd->like('nombre', $columnas['nombre']));
		array_push($linkWhere, 'nombre|'.$columnas['nombre']);
	}
	if(isset($columnas['apellido'])&&$columnas['apellido']!='') {
		array_push($filtros, Usuarios::$bd->like('apellido', $columnas['apellido']));
		array_push($linkWhere, 'apellido|'.$columnas['apellido']);
	}
	if(isset($columnas['usuario'])&&$columnas['usuario']!='') {
		array_push($filtros, Usuarios::$bd->like('usuario', $columnas['usuario']));
		array_push($linkWhere, 'usuario|'.$columnas['usuario']);
	}
	// agregar filtros al whereStatement
	$objUsuarios->setWhereStatement(implode(' AND ', $filtros));
	$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objUsuarios->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objUsuarios->setOrderByStatement('apellido,nombre');
		$linkOrderBy = 'orderby=apellido,nombre&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objUsuarios->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener tabla con los datos
	$tabla = $objUsuarios->getTabla();
	// agregar botones para editar y eliminar
	foreach($tabla as &$fila) {
		$acciones = array();
		array_push($acciones, Tabla::editar('usuario?edit&amp;'.'id='.urlencode($fila['id']).''));
		array_push($fila, implode(' ', $acciones));
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 10, 'style="width:70px"'),
		Form::input4table('nombre', isset($columnas['nombre'])?$columnas['nombre']:'', 20, 'style="width:120px"'),
		Form::input4table('apellido', isset($columnas['apellido'])?$columnas['apellido']:'', 30, 'style="width:150px"'),
		Form::input4table('usuario', isset($columnas['usuario'])?$columnas['usuario']:'', 20, 'style="width:100px"'),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'nombre'=>Tabla::orderby('nombre', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),
		'apellido'=>Tabla::orderby('apellido', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=apellido'),
		'usuario'=>Tabla::orderby('usuario', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=usuario'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'usuario';
	Tabla::$mantenedor = true;
	Tabla::$nuevo = false;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objUsuarios->count(), 'usuario?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'usuario?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'usuario?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'usuario?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
