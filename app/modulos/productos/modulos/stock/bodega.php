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
 * Mantenedor para la tabla bodega
 * Bodegas de la empresa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla bodega y sus fk
require(DIR.'/class/db/final/Bodega.class.php'); // principal para tabla bodega
require(DIR.'/class/db/final/Sucursal.class.php'); // clase para fk de la tabla sucursal
require(DIR.'/class/db/final/Usuario.class.php'); // clase para fk de la tabla usuario


// crear objetos a utilizar por el mantenedor
$objBodega = new Bodega();
$objBodegas = new Bodegas();
$objSucursal = new Sucursal();
$objSucursals = new Sucursals();
$objUsuario = new Usuario();
$objUsuarios = new Usuarios();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla bodega
$arrFK = array('sucursal_id', 'usuario_id'); // columnas fks de la tabla bodega
$arrNotNull = array('id', 'glosa', 'sucursal_id'); // columnas que no pueden ser nulas de la tabla bodega

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' bodega'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Bodegas de la empresa'));

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
	if(!isset($arrSet['glosa'])) $arrSet['glosa'] = (in_array('glosa', $arrFK)&&empty($_POST['glosa'])) ? null : $_POST['glosa'];
	if(!isset($arrSet['sucursal_id'])) $arrSet['sucursal_id'] = (in_array('sucursal_id', $arrFK)&&empty($_POST['sucursal_id'])) ? null : $_POST['sucursal_id'];
	if(!isset($arrSet['usuario_id'])) $arrSet['usuario_id'] = (in_array('usuario_id', $arrFK)&&empty($_POST['usuario_id'])) ? null : $_POST['usuario_id'];

	$objBodega->set($arrSet);
	// guardar registro
	if($objBodega->save()) { // en caso de exito
		echo MiSiTiO::success('bodega');
	} else { // en caso de error
		echo MiSiTiO::failure('bodega');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objBodega->set($arrSet);
		// obtener datos de $objBodega
		$objBodega->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('bodega'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objBodega->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', ($objBodega->id===0 || $objBodega->id==='0' || !empty($objBodega->id)) ? $objBodega->id : '', 'ID de la bodega', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="20"');
	$notNull = in_array('glosa', $arrNotNull);
	$isPK = in_array('glosa', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('glosa', $objBodega->glosa);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'glosa', 'glosa', ($objBodega->glosa===0 || $objBodega->glosa==='0' || !empty($objBodega->glosa)) ? $objBodega->glosa : '', 'Descripción', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="50"');
	$notNull = in_array('sucursal_id', $arrNotNull);
	$isPK = in_array('sucursal_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('sucursal_id', $arrFK)) {
			$objFK = $objBodega->getSucursal();
			$text = $objFK->glosa;
		} else $text = $objBodega->sucursal_id;
		echo Form::text('sucursal_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'sucursal_id', 'sucursal_id', $objSucursals->listado(), $objBodega->sucursal_id, 'Sucursal donde la bodega esta ubicada', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('usuario_id', $arrNotNull);
	$isPK = in_array('usuario_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('usuario_id', $arrFK)) {
			$objFK = $objBodega->getUsuario();
			$text = $objFK->apellido.', '.$objFK->nombre;
		} else $text = $objBodega->usuario_id;
		echo Form::text('usuario_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'usuario_id', 'usuario_id', $objUsuarios->listado(), $objBodega->usuario_id, 'Usuario a cargo de la bodega (solo si existe uno)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla bodega en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objBodega->set($arrSet);
	// eliminar objeto de la base de datos
	if($objBodega->delete()) { // en caso de exito
		echo MiSiTiO::success('bodega');
	} else { // en caso de error
		echo MiSiTiO::failure('bodega');
	}
}

// tabla con datos de la tabla bodega
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla bodega
	$objBodegas->setSelectStatement('id, glosa, sucursal_id, usuario_id');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, Bodegas::$bd->like('id', $columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['glosa'])&&$columnas['glosa']!='') {
			array_push($filtros, Bodegas::$bd->like('glosa', $columnas['glosa']));
			array_push($linkWhere, 'glosa|'.$columnas['glosa']);
		}
		if(isset($columnas['sucursal_id'])&&$columnas['sucursal_id']!='') {
			array_push($filtros, "sucursal_id = '".Bodegas::$bd->proteger($columnas['sucursal_id'])."'");
			array_push($linkWhere, 'sucursal_id|'.$columnas['sucursal_id']);
		}
		if(isset($columnas['usuario_id'])&&$columnas['usuario_id']!='') {
			array_push($filtros, "usuario_id = '".Bodegas::$bd->proteger($columnas['usuario_id'])."'");
			array_push($linkWhere, 'usuario_id|'.$columnas['usuario_id']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objBodegas->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objBodegas->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objBodegas->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objBodegas->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objBodegas->getObjetos() as $objBodega) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objBodega->id);
		// agregar glosa a la fila
		array_push($fila, $objBodega->glosa);
		// agregar sucursal_id a la fila
		if($objBodega->sucursal_id!='') {
			$objFK = $objBodega->getSucursal();
			$glosaFK = $objFK->glosa;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar usuario_id a la fila
		if($objBodega->usuario_id!='') {
			$objFK = $objBodega->getUsuario();
			$glosaFK = $objFK->apellido.', '.$objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('bodega?edit&amp;'.'id='.urlencode($objBodega->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'bodega\', \''.'id='.urlencode($objBodega->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 20),
		Form::input4table('glosa', isset($columnas['glosa'])?$columnas['glosa']:'', 50),
		Form::select4table('sucursal_id', $objSucursals->listado(), !empty($columnas['sucursal_id'])?$columnas['sucursal_id']:''),
		Form::select4table('usuario_id', $objUsuarios->listado(), !empty($columnas['usuario_id'])?$columnas['usuario_id']:''),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'bodega?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'glosa'=>Tabla::orderby('glosa', 'bodega?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=glosa'),
		'sucursal_id'=>Tabla::orderby('sucursal_id', 'bodega?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=sucursal_id'),
		'usuario_id'=>Tabla::orderby('usuario_id', 'bodega?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=usuario_id'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'bodega';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objBodegas->count(), 'bodega?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'bodega?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'bodega?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'bodega?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
