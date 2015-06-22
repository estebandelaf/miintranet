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
 * Mantenedor para la tabla transportista
 * Transportistas externos o internos que se utilizan para el movimiento de productos
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla transportista y sus fk
require(DIR.'/class/db/final/Transportista.class.php'); // principal para tabla transportista


// crear objetos a utilizar por el mantenedor
$objTransportista = new Transportista();
$objTransportistas = new Transportistas();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla transportista
$arrFK = array(''); // columnas fks de la tabla transportista
$arrNotNull = array('id', 'razonsocial', 'interno'); // columnas que no pueden ser nulas de la tabla transportista

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' transportista'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Transportistas externos o internos que se utilizan para el movimiento de productos'));

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
	if(!isset($arrSet['razonsocial'])) $arrSet['razonsocial'] = (in_array('razonsocial', $arrFK)&&empty($_POST['razonsocial'])) ? null : $_POST['razonsocial'];
	if(!isset($arrSet['interno'])) $arrSet['interno'] = (in_array('interno', $arrFK)&&empty($_POST['interno'])) ? null : $_POST['interno'];

	$objTransportista->set($arrSet);
	// guardar registro
	if($objTransportista->save()) { // en caso de exito
		echo MiSiTiO::success('transportista');
	} else { // en caso de error
		echo MiSiTiO::failure('transportista');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objTransportista->set($arrSet);
		// obtener datos de $objTransportista
		$objTransportista->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('transportista'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objTransportista->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', ($objTransportista->id===0 || $objTransportista->id==='0' || !empty($objTransportista->id)) ? $objTransportista->id : '', 'ID del transportista (RUT sin puntos ni dv)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('razonsocial', $arrNotNull);
	$isPK = in_array('razonsocial', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('razonsocial', $objTransportista->razonsocial);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'razonsocial', 'razonsocial', ($objTransportista->razonsocial===0 || $objTransportista->razonsocial==='0' || !empty($objTransportista->razonsocial)) ? $objTransportista->razonsocial : '', 'Razón social del transportista', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="50"');
	$notNull = in_array('interno', $arrNotNull);
	$isPK = in_array('interno', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('interno', $objTransportista->interno);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'interno', 'interno', array(array('1','Si'), array('0','No')), ($objTransportista->interno===0 || $objTransportista->interno==='0' || !empty($objTransportista->interno)) ? $objTransportista->interno : '1', 'Indica si es un transportista de la empresa (=1) o externo (=0)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla transportista en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objTransportista->set($arrSet);
	// eliminar objeto de la base de datos
	if($objTransportista->delete()) { // en caso de exito
		echo MiSiTiO::success('transportista');
	} else { // en caso de error
		echo MiSiTiO::failure('transportista');
	}
}

// tabla con datos de la tabla transportista
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla transportista
	$objTransportistas->setSelectStatement('id, razonsocial, interno');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, 'id='.Transportistas::$bd->proteger($columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['razonsocial'])&&$columnas['razonsocial']!='') {
			array_push($filtros, Transportistas::$bd->like('razonsocial', $columnas['razonsocial']));
			array_push($linkWhere, 'razonsocial|'.$columnas['razonsocial']);
		}
		if(isset($columnas['interno'])&&$columnas['interno']!='') {
			array_push($filtros, 'interno='.Transportistas::$bd->proteger($columnas['interno']));
			array_push($linkWhere, 'interno|'.$columnas['interno']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objTransportistas->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objTransportistas->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objTransportistas->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objTransportistas->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objTransportistas->getObjetos() as $objTransportista) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objTransportista->id);
		// agregar razonsocial a la fila
		array_push($fila, $objTransportista->razonsocial);
		// agregar interno a la fila
		array_push($fila, $objTransportista->interno);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('transportista?edit&amp;'.'id='.urlencode($objTransportista->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'transportista\', \''.'id='.urlencode($objTransportista->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('razonsocial', isset($columnas['razonsocial'])?$columnas['razonsocial']:'', 50),
		Form::select4table('interno', array(array('1','Si'), array('0','No')), isset($columnas['interno'])?$columnas['interno']:''),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'transportista?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'razonsocial'=>Tabla::orderby('razonsocial', 'transportista?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=razonsocial'),
		'interno'=>Tabla::orderby('interno', 'transportista?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=interno'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'transportista';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objTransportistas->count(), 'transportista?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'transportista?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'transportista?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'transportista?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
