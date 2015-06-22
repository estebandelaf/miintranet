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
 * Mantenedor para la tabla parametro
 * Parámetros de la aplicación
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla parametro y sus fk
require(DIR.'/class/db/final/Parametro.class.php'); // principal para tabla parametro
require(DIR.'/class/db/final/Modulo.class.php'); // clase para fk de la tabla modulo


// crear objetos a utilizar por el mantenedor
$objParametro = new Parametro();
$objParametros = new Parametros();
$objModulo = new Modulo();
$objModulos = new Modulos();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('parametro'); // columnas pks de la tabla parametro
$arrFK = array('modulo_nombre'); // columnas fks de la tabla parametro
$arrNotNull = array('parametro', 'valor', 'descripcion', 'modulo_nombre'); // columnas que no pueden ser nulas de la tabla parametro

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' parametro'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Parámetros de la aplicación'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['parametro'])) {
		$arrSet['parametro'] = urldecode($_GET['parametro']);

	}
	// definir otros campos
	if(!isset($arrSet['parametro'])) $arrSet['parametro'] = (in_array('parametro', $arrFK)&&empty($_POST['parametro'])) ? null : $_POST['parametro'];
	if(!isset($arrSet['valor'])) $arrSet['valor'] = (in_array('valor', $arrFK)&&empty($_POST['valor'])) ? null : $_POST['valor'];
	if(!isset($arrSet['descripcion'])) $arrSet['descripcion'] = (in_array('descripcion', $arrFK)&&empty($_POST['descripcion'])) ? null : $_POST['descripcion'];
	if(!isset($arrSet['modulo_nombre'])) $arrSet['modulo_nombre'] = (in_array('modulo_nombre', $arrFK)&&empty($_POST['modulo_nombre'])) ? null : $_POST['modulo_nombre'];

	$objParametro->set($arrSet);
	// guardar registro
	if($objParametro->save()) { // en caso de exito
		echo MiSiTiO::success('parametro');
	} else { // en caso de error
		echo MiSiTiO::failure('parametro');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['parametro']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['parametro'] = urldecode($_GET['parametro']);

		$objParametro->set($arrSet);
		// obtener datos de $objParametro
		$objParametro->get();
		$link = '?edit&amp;'.'parametro='.urlencode($_GET['parametro']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('parametro'.$link, 'validarFormulario');
	$notNull = in_array('parametro', $arrNotNull);
	$isPK = in_array('parametro', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('parametro', $objParametro->parametro);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'parametro', 'parametro', ($objParametro->parametro===0 || $objParametro->parametro==='0' || !empty($objParametro->parametro)) ? $objParametro->parametro : '', 'Nombre del parámetro en mayúsculas, sin espacios ni caracteres especiales', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('valor', $arrNotNull);
	$isPK = in_array('valor', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('valor', $objParametro->valor);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'valor', 'valor', ($objParametro->valor===0 || $objParametro->valor==='0' || !empty($objParametro->valor)) ? $objParametro->valor : '', 'Valor del parámetro (int, string, etc)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="60"');
	$notNull = in_array('descripcion', $arrNotNull);
	$isPK = in_array('descripcion', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('descripcion', $objParametro->descripcion);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'descripcion', 'descripcion', ($objParametro->descripcion===0 || $objParametro->descripcion==='0' || !empty($objParametro->descripcion)) ? $objParametro->descripcion : '', 'Descripción del parámetro', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="100"');
	$notNull = in_array('modulo_nombre', $arrNotNull);
	$isPK = in_array('modulo_nombre', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('modulo_nombre', $arrFK)) {
			$objFK = $objParametro->getModulo();
			$text = $objFK->nombre;
		} else $text = $objParametro->modulo_nombre;
		echo Form::text('modulo_nombre', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'modulo_nombre', 'modulo_nombre', $objModulos->listado(), $objParametro->modulo_nombre, 'Módulo del sistema al que pertenece el parámetro', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla parametro en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['parametro'])) {
	// setear pk
	$arrSet = array();
		$arrSet['parametro'] = urldecode($_GET['parametro']);

	$objParametro->set($arrSet);
	// eliminar objeto de la base de datos
	if($objParametro->delete()) { // en caso de exito
		echo MiSiTiO::success('parametro');
	} else { // en caso de error
		echo MiSiTiO::failure('parametro');
	}
}

// tabla con datos de la tabla parametro
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla parametro
	$objParametros->setSelectStatement('parametro, valor, descripcion, modulo_nombre');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['parametro'])&&$columnas['parametro']!='') {
			array_push($filtros, Parametros::$bd->like('parametro', $columnas['parametro']));
			array_push($linkWhere, 'parametro|'.$columnas['parametro']);
		}
		if(isset($columnas['valor'])&&$columnas['valor']!='') {
			array_push($filtros, Parametros::$bd->like('valor', $columnas['valor']));
			array_push($linkWhere, 'valor|'.$columnas['valor']);
		}
		if(isset($columnas['descripcion'])&&$columnas['descripcion']!='') {
			array_push($filtros, Parametros::$bd->like('descripcion', $columnas['descripcion']));
			array_push($linkWhere, 'descripcion|'.$columnas['descripcion']);
		}
		if(isset($columnas['modulo_nombre'])&&$columnas['modulo_nombre']!='') {
			array_push($filtros, "modulo_nombre = '".Parametros::$bd->proteger($columnas['modulo_nombre'])."'");
			array_push($linkWhere, 'modulo_nombre|'.$columnas['modulo_nombre']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objParametros->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objParametros->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objParametros->setOrderByStatement('parametro');
		$linkOrderBy = 'orderby=parametro&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objParametros->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objParametros->getObjetos() as $objParametro) {
		$fila = array();
		// agregar datos de las columnas
		// agregar parametro a la fila
		array_push($fila, $objParametro->parametro);
		// agregar valor a la fila
		array_push($fila, $objParametro->valor);
		// agregar descripcion a la fila
		array_push($fila, $objParametro->descripcion);
		// agregar modulo_nombre a la fila
		if($objParametro->modulo_nombre!='') {
			$objFK = $objParametro->getModulo();
			$glosaFK = $objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('parametro?edit&amp;'.'parametro='.urlencode($objParametro->parametro).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'parametro\', \''.'parametro='.urlencode($objParametro->parametro).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('parametro', isset($columnas['parametro'])?$columnas['parametro']:'', 30),
		Form::input4table('valor', isset($columnas['valor'])?$columnas['valor']:'', 60),
		Form::input4table('descripcion', isset($columnas['descripcion'])?$columnas['descripcion']:'', 100),
		Form::select4table('modulo_nombre', $objModulos->listado(), !empty($columnas['modulo_nombre'])?$columnas['modulo_nombre']:''),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'parametro'=>Tabla::orderby('parametro', 'parametro?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=parametro'),
		'valor'=>Tabla::orderby('valor', 'parametro?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=valor'),
		'descripcion'=>Tabla::orderby('descripcion', 'parametro?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=descripcion'),
		'modulo_nombre'=>Tabla::orderby('modulo_nombre', 'parametro?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=modulo_nombre'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'parametro';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objParametros->count(), 'parametro?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'parametro?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'parametro?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'parametro?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
