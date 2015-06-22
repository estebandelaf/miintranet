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
 * Mantenedor para la tabla uf
 * Valores diarios de la UF
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla uf y sus fk
require(DIR.'/class/db/final/Uf.class.php'); // principal para tabla uf


// crear objetos a utilizar por el mantenedor
$objUf = new Uf();
$objUfs = new Ufs();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('fecha'); // columnas pks de la tabla uf
$arrFK = array(''); // columnas fks de la tabla uf
$arrNotNull = array('fecha', 'valor'); // columnas que no pueden ser nulas de la tabla uf

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' uf'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Valores diarios de la UF'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['fecha'])) {
		$arrSet['fecha'] = urldecode($_GET['fecha']);

	}
	// definir otros campos
	if(!isset($arrSet['fecha'])) $arrSet['fecha'] = (in_array('fecha', $arrFK)&&empty($_POST['fecha'])) ? null : $_POST['fecha'];
	if(!isset($arrSet['valor'])) $arrSet['valor'] = (in_array('valor', $arrFK)&&empty($_POST['valor'])) ? null : $_POST['valor'];

	$objUf->set($arrSet);
	// guardar registro
	if($objUf->save()) { // en caso de exito
		echo MiSiTiO::success('uf');
	} else { // en caso de error
		echo MiSiTiO::failure('uf');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['fecha']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['fecha'] = urldecode($_GET['fecha']);

		$objUf->set($arrSet);
		// obtener datos de $objUf
		$objUf->get();
		$link = '?edit&amp;'.'fecha='.urlencode($_GET['fecha']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('uf'.$link, 'validarFormulario');
	$notNull = in_array('fecha', $arrNotNull);
	$isPK = in_array('fecha', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('fecha', $objUf->fecha);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::inputDate(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'fecha', 'fecha', !empty($objUf->fecha) ? $objUf->fecha : '', true, 'Día del valor', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('valor', $arrNotNull);
	$isPK = in_array('valor', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('valor', $objUf->valor);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'valor', 'valor', ($objUf->valor===0 || $objUf->valor==='0' || !empty($objUf->valor)) ? $objUf->valor : '', 'Valor de la UF', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="53"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla uf en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['fecha'])) {
	// setear pk
	$arrSet = array();
		$arrSet['fecha'] = urldecode($_GET['fecha']);

	$objUf->set($arrSet);
	// eliminar objeto de la base de datos
	if($objUf->delete()) { // en caso de exito
		echo MiSiTiO::success('uf');
	} else { // en caso de error
		echo MiSiTiO::failure('uf');
	}
}

// tabla con datos de la tabla uf
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla uf
	$objUfs->setSelectStatement('fecha, valor');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['fecha'])&&$columnas['fecha']!='') {
			array_push($filtros, Ufs::$bd->like('fecha', $columnas['fecha']));
			array_push($linkWhere, 'fecha|'.$columnas['fecha']);
		}
		if(isset($columnas['valor'])&&$columnas['valor']!='') {
			array_push($filtros, Ufs::$bd->like('valor', $columnas['valor']));
			array_push($linkWhere, 'valor|'.$columnas['valor']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objUfs->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objUfs->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objUfs->setOrderByStatement('fecha');
		$linkOrderBy = 'orderby=fecha&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objUfs->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objUfs->getObjetos() as $objUf) {
		$fila = array();
		// agregar datos de las columnas
		// agregar fecha a la fila
		array_push($fila, $objUf->fecha);
		// agregar valor a la fila
		array_push($fila, $objUf->valor);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('uf?edit&amp;'.'fecha='.urlencode($objUf->fecha).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'uf\', \''.'fecha='.urlencode($objUf->fecha).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('fecha', isset($columnas['fecha'])?$columnas['fecha']:'', ''),
		Form::input4table('valor', isset($columnas['valor'])?$columnas['valor']:'', 53),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'fecha'=>Tabla::orderby('fecha', 'uf?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=fecha'),
		'valor'=>Tabla::orderby('valor', 'uf?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=valor'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'uf';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objUfs->count(), 'uf?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'uf?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'uf?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'uf?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
