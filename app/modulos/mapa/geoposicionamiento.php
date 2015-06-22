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
 * Mantenedor para la tabla geoposicionamiento
 * Ubicación actual geográfica para diferentes fines
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla geoposicionamiento y sus fk
require(DIR.'/class/db/final/Geoposicionamiento.class.php'); // principal para tabla geoposicionamiento


// crear objetos a utilizar por el mantenedor
$objGeoposicionamiento = new Geoposicionamiento();
$objGeoposicionamientos = new Geoposicionamientos();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla geoposicionamiento
$arrFK = array(''); // columnas fks de la tabla geoposicionamiento
$arrNotNull = array('id', 'longitud', 'latitud', 'fechahora', 'glosa'); // columnas que no pueden ser nulas de la tabla geoposicionamiento

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' geoposicionamiento'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Ubicación actual geográfica para diferentes fines'));

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
	if(!isset($arrSet['longitud'])) $arrSet['longitud'] = (in_array('longitud', $arrFK)&&empty($_POST['longitud'])) ? null : $_POST['longitud'];
	if(!isset($arrSet['latitud'])) $arrSet['latitud'] = (in_array('latitud', $arrFK)&&empty($_POST['latitud'])) ? null : $_POST['latitud'];
	if(!isset($arrSet['fechahora'])) $arrSet['fechahora'] = (in_array('fechahora', $arrFK)&&empty($_POST['fechahora'])) ? null : $_POST['fechahora'];
	if(!isset($arrSet['glosa'])) $arrSet['glosa'] = (in_array('glosa', $arrFK)&&empty($_POST['glosa'])) ? null : $_POST['glosa'];

	$objGeoposicionamiento->set($arrSet);
	// guardar registro
	if($objGeoposicionamiento->save()) { // en caso de exito
		echo MiSiTiO::success('geoposicionamiento');
	} else { // en caso de error
		echo MiSiTiO::failure('geoposicionamiento');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objGeoposicionamiento->set($arrSet);
		// obtener datos de $objGeoposicionamiento
		$objGeoposicionamiento->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('geoposicionamiento'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objGeoposicionamiento->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', ($objGeoposicionamiento->id===0 || $objGeoposicionamiento->id==='0' || !empty($objGeoposicionamiento->id)) ? $objGeoposicionamiento->id : '', 'Identificador, recomendado hash MD5', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('longitud', $arrNotNull);
	$isPK = in_array('longitud', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('longitud', $objGeoposicionamiento->longitud);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'longitud', 'longitud', ($objGeoposicionamiento->longitud===0 || $objGeoposicionamiento->longitud==='0' || !empty($objGeoposicionamiento->longitud)) ? $objGeoposicionamiento->longitud : '0', 'Longitud geográfica', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="10"');
	$notNull = in_array('latitud', $arrNotNull);
	$isPK = in_array('latitud', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('latitud', $objGeoposicionamiento->latitud);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'latitud', 'latitud', ($objGeoposicionamiento->latitud===0 || $objGeoposicionamiento->latitud==='0' || !empty($objGeoposicionamiento->latitud)) ? $objGeoposicionamiento->latitud : '0', 'Latitud geográfica', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="10"');
	$notNull = in_array('fechahora', $arrNotNull);
	$isPK = in_array('fechahora', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('fechahora', $objGeoposicionamiento->fechahora);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'fechahora', 'fechahora', ($objGeoposicionamiento->fechahora===0 || $objGeoposicionamiento->fechahora==='0' || !empty($objGeoposicionamiento->fechahora)) ? $objGeoposicionamiento->fechahora : 'now', 'Fecha y hora de la última actualización', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength=""');
	$notNull = in_array('glosa', $arrNotNull);
	$isPK = in_array('glosa', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('glosa', $objGeoposicionamiento->glosa);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'glosa', 'glosa', ($objGeoposicionamiento->glosa===0 || $objGeoposicionamiento->glosa==='0' || !empty($objGeoposicionamiento->glosa)) ? $objGeoposicionamiento->glosa : '', 'Descripción de a quién/que se le hace el geoposicionamiento', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="100"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla geoposicionamiento en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objGeoposicionamiento->set($arrSet);
	// eliminar objeto de la base de datos
	if($objGeoposicionamiento->delete()) { // en caso de exito
		echo MiSiTiO::success('geoposicionamiento');
	} else { // en caso de error
		echo MiSiTiO::failure('geoposicionamiento');
	}
}

// tabla con datos de la tabla geoposicionamiento
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla geoposicionamiento
	$objGeoposicionamientos->setSelectStatement('id, longitud, latitud, fechahora, glosa');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, Geoposicionamientos::$bd->like('id', $columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['longitud'])&&$columnas['longitud']!='') {
			array_push($filtros, Geoposicionamientos::$bd->like('longitud', $columnas['longitud']));
			array_push($linkWhere, 'longitud|'.$columnas['longitud']);
		}
		if(isset($columnas['latitud'])&&$columnas['latitud']!='') {
			array_push($filtros, Geoposicionamientos::$bd->like('latitud', $columnas['latitud']));
			array_push($linkWhere, 'latitud|'.$columnas['latitud']);
		}
		if(isset($columnas['fechahora'])&&$columnas['fechahora']!='') {
			array_push($filtros, Geoposicionamientos::$bd->like('fechahora', $columnas['fechahora']));
			array_push($linkWhere, 'fechahora|'.$columnas['fechahora']);
		}
		if(isset($columnas['glosa'])&&$columnas['glosa']!='') {
			array_push($filtros, Geoposicionamientos::$bd->like('glosa', $columnas['glosa']));
			array_push($linkWhere, 'glosa|'.$columnas['glosa']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objGeoposicionamientos->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objGeoposicionamientos->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objGeoposicionamientos->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objGeoposicionamientos->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objGeoposicionamientos->getObjetos() as $objGeoposicionamiento) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objGeoposicionamiento->id);
		// agregar longitud a la fila
		array_push($fila, $objGeoposicionamiento->longitud);
		// agregar latitud a la fila
		array_push($fila, $objGeoposicionamiento->latitud);
		// agregar fechahora a la fila
		array_push($fila, $objGeoposicionamiento->fechahora);
		// agregar glosa a la fila
		array_push($fila, $objGeoposicionamiento->glosa);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('geoposicionamiento?edit&amp;'.'id='.urlencode($objGeoposicionamiento->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'geoposicionamiento\', \''.'id='.urlencode($objGeoposicionamiento->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('longitud', isset($columnas['longitud'])?$columnas['longitud']:'', 10),
		Form::input4table('latitud', isset($columnas['latitud'])?$columnas['latitud']:'', 10),
		Form::input4table('fechahora', isset($columnas['fechahora'])?$columnas['fechahora']:'', ''),
		Form::input4table('glosa', isset($columnas['glosa'])?$columnas['glosa']:'', 100),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'geoposicionamiento?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'longitud'=>Tabla::orderby('longitud', 'geoposicionamiento?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=longitud'),
		'latitud'=>Tabla::orderby('latitud', 'geoposicionamiento?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=latitud'),
		'fechahora'=>Tabla::orderby('fechahora', 'geoposicionamiento?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=fechahora'),
		'glosa'=>Tabla::orderby('glosa', 'geoposicionamiento?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=glosa'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'geoposicionamiento';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objGeoposicionamientos->count(), 'geoposicionamiento?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'geoposicionamiento?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'geoposicionamiento?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'geoposicionamiento?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
