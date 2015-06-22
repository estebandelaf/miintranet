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
 * Mantenedor para la tabla usuario_grupo
 * Relación entre usuarios y los grupos a los que pertenecen
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-29
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla usuario_grupo y sus fk
require(DIR.'/class/db/final/Usuario_grupo.class.php'); // principal para tabla usuario_grupo
require(DIR.'/class/db/final/Usuario.class.php'); // clase para fk de la tabla usuario
require(DIR.'/class/db/final/Grupo.class.php'); // clase para fk de la tabla grupo


// crear objetos a utilizar por el mantenedor
$objUsuario_grupo = new Usuario_grupo();
$objUsuario_grupos = new Usuario_grupos();
$objUsuario = new Usuario();
$objUsuarios = new Usuarios();
$objGrupo = new Grupo();
$objGrupos = new Grupos();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('usuario_id', 'grupo_id'); // columnas pks de la tabla usuario_grupo
$arrFK = array('usuario_id', 'grupo_id'); // columnas fks de la tabla usuario_grupo
$arrNotNull = array('usuario_id', 'grupo_id'); // columnas que no pueden ser nulas de la tabla usuario_grupo

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' usuario_grupo'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Relación entre usuarios y los grupos a los que pertenecen'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['usuario_id']) && !empty($_GET['grupo_id'])) {
		$arrSet['usuario_id'] = urldecode($_GET['usuario_id']);
		$arrSet['grupo_id'] = urldecode($_GET['grupo_id']);

	}
	// definir otros campos
	if(!isset($arrSet['usuario_id'])) $arrSet['usuario_id'] = (in_array('usuario_id', $arrFK)&&empty($_POST['usuario_id'])) ? null : $_POST['usuario_id'];
	if(!isset($arrSet['grupo_id'])) $arrSet['grupo_id'] = (in_array('grupo_id', $arrFK)&&empty($_POST['grupo_id'])) ? null : $_POST['grupo_id'];

	$objUsuario_grupo->set($arrSet);
	// guardar registro
	if($objUsuario_grupo->save()) { // en caso de exito
		echo MiSiTiO::success('usuario_grupo');
	} else { // en caso de error
		echo MiSiTiO::failure('usuario_grupo');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['usuario_id']) && !empty($_GET['grupo_id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['usuario_id'] = urldecode($_GET['usuario_id']);
		$arrSet['grupo_id'] = urldecode($_GET['grupo_id']);

		$objUsuario_grupo->set($arrSet);
		// obtener datos de $objUsuario_grupo
		$objUsuario_grupo->get();
		$link = '?edit&amp;'.'usuario_id='.urlencode($_GET['usuario_id']).'&amp;'.'grupo_id='.urlencode($_GET['grupo_id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('usuario_grupo'.$link, 'validarFormulario');
	$notNull = in_array('usuario_id', $arrNotNull);
	$isPK = in_array('usuario_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('usuario_id', $objUsuario_grupo->usuario_id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'usuario_id', 'usuario_id', $objUsuarios->listado(), $objUsuario_grupo->usuario_id, 'ID del usuario', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('grupo_id', $arrNotNull);
	$isPK = in_array('grupo_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('grupo_id', $objUsuario_grupo->grupo_id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'grupo_id', 'grupo_id', $objGrupos->listado(), $objUsuario_grupo->grupo_id, 'ID del grupo', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla usuario_grupo en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['usuario_id']) && !empty($_GET['grupo_id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['usuario_id'] = urldecode($_GET['usuario_id']);
		$arrSet['grupo_id'] = urldecode($_GET['grupo_id']);

	$objUsuario_grupo->set($arrSet);
	// eliminar objeto de la base de datos
	if($objUsuario_grupo->delete()) { // en caso de exito
		echo MiSiTiO::success('usuario_grupo');
	} else { // en caso de error
		echo MiSiTiO::failure('usuario_grupo');
	}
}

// tabla con datos de la tabla usuario_grupo
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla usuario_grupo
	$objUsuario_grupos->setSelectStatement('usuario_id, grupo_id');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['usuario_id'])&&$columnas['usuario_id']!='') {
			array_push($filtros, "usuario_id = '".Usuario_grupos::$bd->proteger($columnas['usuario_id'])."'");
			array_push($linkWhere, 'usuario_id|'.$columnas['usuario_id']);
		}
		if(isset($columnas['grupo_id'])&&$columnas['grupo_id']!='') {
			array_push($filtros, "grupo_id = '".Usuario_grupos::$bd->proteger($columnas['grupo_id'])."'");
			array_push($linkWhere, 'grupo_id|'.$columnas['grupo_id']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objUsuario_grupos->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objUsuario_grupos->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objUsuario_grupos->setOrderByStatement('usuario_id,grupo_id');
		$linkOrderBy = 'orderby=usuario_id,grupo_id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objUsuario_grupos->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objUsuario_grupos->getObjetos() as $objUsuario_grupo) {
		$fila = array();
		// agregar datos de las columnas
		// agregar usuario_id a la fila
		if($objUsuario_grupo->usuario_id!='') {
			$objFK = $objUsuario_grupo->getUsuario();
			$glosaFK = $objFK->apellido.', '.$objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar grupo_id a la fila
		if($objUsuario_grupo->grupo_id!='') {
			$objFK = $objUsuario_grupo->getGrupo();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::eliminar('eliminar(\'usuario_grupo\', \''.'usuario_id='.urlencode($objUsuario_grupo->usuario_id).'&amp;'.'grupo_id='.urlencode($objUsuario_grupo->grupo_id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::select4table('usuario_id', $objUsuarios->listado(), isset($columnas['usuario_id'])?$columnas['usuario_id']:''),
		Form::select4table('grupo_id', $objGrupos->listado(), isset($columnas['grupo_id'])?$columnas['grupo_id']:''),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'usuario_id'=>Tabla::orderby('usuario_id', 'usuario_grupo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=usuario_id'),
		'grupo_id'=>Tabla::orderby('grupo_id', 'usuario_grupo?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=grupo_id'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'usuario_grupo';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objUsuario_grupos->count(), 'usuario_grupo?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'usuario_grupo?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'usuario_grupo?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'usuario_grupo?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
