<?php

/**
 * MiInTrAnEt
 * Copyright (C) 2008-2010 Esteban De La Fuente Rubio (esteban@delaf.cl)
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
 * Mantenedor para la tabla permiso
 * Relación entre grupos y recursos
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-02
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla permiso y sus fk
require(DIR.'/class/db/final/Permiso.class.php'); // principal para tabla permiso
require(DIR.'/class/db/final/Grupo.class.php'); // clase para fk de la tabla grupo


// crear objetos a utilizar por el mantenedor
$objPermiso = new Permiso();
$objPermisos = new Permisos();
$objGrupo = new Grupo();
$objGrupos = new Grupos();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('grupo_id', 'recurso'); // columnas pks de la tabla permiso
$arrFK = array('grupo_id'); // columnas fks de la tabla permiso
$arrNotNull = array('grupo_id', 'recurso'); // columnas que no pueden ser nulas de la tabla permiso

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' permiso'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Relación entre grupos y recursos'));

// editar o crear nuevo registro
if(isset($_POST['save']) && isset($_POST['grupo_id'])) {

	// limpiar tabla de permisos para el recurso indicado
	$arrSet = array();
	if(isset($_GET['edit']) && !empty($_GET['recurso'])) {
			$arrSet['recurso'] = urldecode($_GET['recurso']);	
	}
	if(!isset($arrSet['recurso'])) $arrSet['recurso'] = (in_array('recurso', $arrFK)&&empty($_POST['recurso'])) ? null : $_POST['recurso'];
	$objPermiso->set($arrSet);
	$objPermiso->deletePermiso();
	
	// agregar nuevos permisos
	foreach($_POST['grupo_id'] as $grupo) {

		// arreglo para hacer set
		$arrSet = array();

		// si se esta editando setear pk
		if(isset($_GET['edit']) && !empty($_GET['recurso'])) {
			$arrSet['recurso'] = urldecode($_GET['recurso']);	
		}
		
		// definir otros campos
		$arrSet['grupo_id'] = $grupo;
		if(!isset($arrSet['recurso'])) $arrSet['recurso'] = (in_array('recurso', $arrFK)&&empty($_POST['recurso'])) ? null : $_POST['recurso'];

		$objPermiso->set($arrSet);
	
		// guardar registro
		$objPermiso->save();
		
	}
	
	echo MiSiTiO::success('permiso');

}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['recurso']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['recurso'] = urldecode($_GET['recurso']);

		$objPermiso->set($arrSet);
		$link = '?edit&amp;'.'recurso='.urlencode($_GET['recurso']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('permiso'.$link, 'validarFormulario');
	$notNull = in_array('grupo_id', $arrNotNull);
	
	$objGrupos->setSelectStatement('id, glosa');
	$objGrupos->setWhereStatement('id!=1');
	
	$notNull = in_array('recurso', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('recurso', $arrPK))) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'recurso', 'recurso', !empty($objPermiso->recurso) ? $objPermiso->recurso : '', 'Generalmente una url, pero puede ser otro tipo de recurso como smb://servidor para utilizar con SAMBA y PAM', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="100"');
	
	echo Form::checkList(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'grupo_id', 'grupo_id', $objGrupos->getTabla(), $objPermiso->grupos());

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla permiso en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['recurso'])) {
	// setear pk
	$arrSet = array();
		$arrSet['recurso'] = urldecode($_GET['recurso']);
	$objPermiso->set($arrSet);
	// eliminar objeto de la base de datos
	if($objPermiso->deletePermiso()) { // en caso de exito
		echo MiSiTiO::success('permiso');
	} else { // en caso de error
		echo MiSiTiO::failure('permiso');
	}
}

// tabla con datos de la tabla permiso
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla permiso
	$objPermisos->setSelectStatement('recurso');
	// agrupar para mostrar por recurso
	$objPermisos->setGroupByStatement('recurso');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['recurso'])&&$columnas['recurso']!='') {
			array_push($filtros, Permisos::$bd->like('recurso', $columnas['recurso']));
			array_push($linkWhere, 'recurso|'.$columnas['recurso']);
		}
		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objPermisos->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objPermisos->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objPermisos->setOrderByStatement('recurso');
		$linkOrderBy = 'orderby=recurso&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objPermisos->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener tabla con los dtos
	$tabla = $objPermisos->getTabla();
	// agregar botones para editar y eliminar
	foreach($tabla as &$fila) {
		$acciones = array();
		array_push($acciones, Tabla::editar('permiso?edit&amp;'.'recurso='.urlencode($fila['recurso']).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'permiso\', \''.'recurso='.urlencode($fila['recurso']).'\')'));
		array_push($fila, implode(' ', $acciones));
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
		'recurso'=>Tabla::orderby('recurso', 'permiso?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=recurso'),
		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'permiso';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objPermisos->count(), 'permiso?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'permiso?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'permiso?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'permiso?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
