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
 * Mantenedor para la tabla permiso_login
 * Recursos que solo requieren al usuario logueado
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-29
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla permiso_login y sus fk
require(DIR.'/class/db/final/Permiso_login.class.php'); // principal para tabla permiso_login


// crear objetos a utilizar por el mantenedor
$objPermiso_login = new Permiso_login();
$objPermiso_logins = new Permiso_logins();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('recurso'); // columnas pks de la tabla permiso_login
$arrFK = array(''); // columnas fks de la tabla permiso_login
$arrNotNull = array('recurso'); // columnas que no pueden ser nulas de la tabla permiso_login

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' permiso_login'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Recursos que solo requieren al usuario logueado'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['recurso'])) {
		$arrSet['recurso'] = urldecode($_GET['recurso']);

	}
	// definir otros campos
	if(!isset($arrSet['recurso'])) $arrSet['recurso'] = (in_array('recurso', $arrFK)&&empty($_POST['recurso'])) ? null : $_POST['recurso'];

	$objPermiso_login->set($arrSet);
	// guardar registro
	if($objPermiso_login->save()) { // en caso de exito
		echo MiSiTiO::success('permiso_login');
	} else { // en caso de error
		echo MiSiTiO::failure('permiso_login');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['recurso']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['recurso'] = urldecode($_GET['recurso']);

		$objPermiso_login->set($arrSet);
		// obtener datos de $objPermiso_login
		$objPermiso_login->get();
		$link = '?edit&amp;'.'recurso='.urlencode($_GET['recurso']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('permiso_login'.$link, 'validarFormulario');
	$notNull = in_array('recurso', $arrNotNull);
	$isPK = in_array('recurso', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('recurso', $objPermiso_login->recurso);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'recurso', 'recurso', ($objPermiso_login->recurso===0 || $objPermiso_login->recurso==='0' || !empty($objPermiso_login->recurso)) ? $objPermiso_login->recurso : '', 'Generalmente una url, pero puede ser otro tipo de recurso como smb://servidor para utilizar con SAMBA y PAM', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="100"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla permiso_login en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['recurso'])) {
	// setear pk
	$arrSet = array();
		$arrSet['recurso'] = urldecode($_GET['recurso']);

	$objPermiso_login->set($arrSet);
	// eliminar objeto de la base de datos
	if($objPermiso_login->delete()) { // en caso de exito
		echo MiSiTiO::success('permiso_login');
	} else { // en caso de error
		echo MiSiTiO::failure('permiso_login');
	}
}

// tabla con datos de la tabla permiso_login
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla permiso_login
	$objPermiso_logins->setSelectStatement('recurso');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['recurso'])&&$columnas['recurso']!='') {
			array_push($filtros, Permiso_logins::$bd->like('recurso', $columnas['recurso']));
			array_push($linkWhere, 'recurso|'.$columnas['recurso']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objPermiso_logins->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objPermiso_logins->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objPermiso_logins->setOrderByStatement('recurso');
		$linkOrderBy = 'orderby=recurso&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objPermiso_logins->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objPermiso_logins->getObjetos() as $objPermiso_login) {
		$fila = array();
		// agregar datos de las columnas
		// agregar recurso a la fila
		array_push($fila, $objPermiso_login->recurso);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::eliminar('eliminar(\'permiso_login\', \''.'recurso='.urlencode($objPermiso_login->recurso).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('recurso', isset($columnas['recurso'])?$columnas['recurso']:'', 100),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'recurso'=>Tabla::orderby('recurso', 'permiso_login?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=recurso'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'permiso_login';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objPermiso_logins->count(), 'permiso_login?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'permiso_login?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'permiso_login?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'permiso_login?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
