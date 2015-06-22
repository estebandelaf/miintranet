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
 * Mantenedor para la tabla feriado
 * Días feriados
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-08
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla feriado y sus fk
require(DIR.'/class/db/final/Feriado.class.php'); // principal para tabla feriado


// crear objetos a utilizar por el mantenedor
$objFeriado = new Feriado();
$objFeriados = new Feriados();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('anio', 'mes', 'dia'); // columnas pks de la tabla feriado
$arrFK = array(''); // columnas fks de la tabla feriado
$arrNotNull = array('anio', 'mes', 'dia'); // columnas que no pueden ser nulas de la tabla feriado

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' feriado'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Días feriados'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['anio']) && !empty($_GET['mes']) && !empty($_GET['dia'])) {
		$arrSet['anio'] = urldecode($_GET['anio']);
		$arrSet['mes'] = urldecode($_GET['mes']);
		$arrSet['dia'] = urldecode($_GET['dia']);

	}
	// definir otros campos
	if(!isset($arrSet['anio'])) $arrSet['anio'] = (in_array('anio', $arrFK)&&empty($_POST['anio'])) ? null : $_POST['anio'];
	if(!isset($arrSet['mes'])) $arrSet['mes'] = (in_array('mes', $arrFK)&&empty($_POST['mes'])) ? null : $_POST['mes'];
	if(!isset($arrSet['dia'])) $arrSet['dia'] = (in_array('dia', $arrFK)&&empty($_POST['dia'])) ? null : $_POST['dia'];

	$objFeriado->set($arrSet);
	// guardar registro
	if($objFeriado->save()) { // en caso de exito
		echo MiSiTiO::success('feriado');
	} else { // en caso de error
		echo MiSiTiO::failure('feriado');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['anio']) && !empty($_GET['mes']) && !empty($_GET['dia']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['anio'] = urldecode($_GET['anio']);
		$arrSet['mes'] = urldecode($_GET['mes']);
		$arrSet['dia'] = urldecode($_GET['dia']);

		$objFeriado->set($arrSet);
		// obtener datos de $objFeriado
		$objFeriado->get();
		$link = '?edit&amp;'.'anio='.urlencode($_GET['anio']).'&amp;'.'mes='.urlencode($_GET['mes']).'&amp;'.'dia='.urlencode($_GET['dia']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('feriado'.$link, 'validarFormulario');
	$notNull = in_array('anio', $arrNotNull);
	$isPK = in_array('anio', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('anio', $objFeriado->anio);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'anio', 'anio', ($objFeriado->anio===0 || $objFeriado->anio==='0' || !empty($objFeriado->anio)) ? $objFeriado->anio : '', 'Año en caso de feriados que varien con los años, =0 en caso de otros (como 1 de ene o 25 de dic)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');
	$notNull = in_array('mes', $arrNotNull);
	$isPK = in_array('mes', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('mes', $objFeriado->mes);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'mes', 'mes', ($objFeriado->mes===0 || $objFeriado->mes==='0' || !empty($objFeriado->mes)) ? $objFeriado->mes : '', 'Mes del feriado', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');
	$notNull = in_array('dia', $arrNotNull);
	$isPK = in_array('dia', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('dia', $objFeriado->dia);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'dia', 'dia', ($objFeriado->dia===0 || $objFeriado->dia==='0' || !empty($objFeriado->dia)) ? $objFeriado->dia : '', 'Día feriado', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla feriado en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['anio']) && !empty($_GET['mes']) && !empty($_GET['dia'])) {
	// setear pk
	$arrSet = array();
		$arrSet['anio'] = urldecode($_GET['anio']);
		$arrSet['mes'] = urldecode($_GET['mes']);
		$arrSet['dia'] = urldecode($_GET['dia']);

	$objFeriado->set($arrSet);
	// eliminar objeto de la base de datos
	if($objFeriado->delete()) { // en caso de exito
		echo MiSiTiO::success('feriado');
	} else { // en caso de error
		echo MiSiTiO::failure('feriado');
	}
}

// tabla con datos de la tabla feriado
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla feriado
	$objFeriados->setSelectStatement('anio, mes, dia');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['anio'])&&$columnas['anio']!='') {
			array_push($filtros, 'anio='.Feriados::$bd->proteger($columnas['anio']));
			array_push($linkWhere, 'anio|'.$columnas['anio']);
		}
		if(isset($columnas['mes'])&&$columnas['mes']!='') {
			array_push($filtros, 'mes='.Feriados::$bd->proteger($columnas['mes']));
			array_push($linkWhere, 'mes|'.$columnas['mes']);
		}
		if(isset($columnas['dia'])&&$columnas['dia']!='') {
			array_push($filtros, 'dia='.Feriados::$bd->proteger($columnas['dia']));
			array_push($linkWhere, 'dia|'.$columnas['dia']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objFeriados->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objFeriados->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objFeriados->setOrderByStatement('anio,mes,dia');
		$linkOrderBy = 'orderby=anio,mes,dia&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objFeriados->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objFeriados->getObjetos() as $objFeriado) {
		$fila = array();
		// agregar datos de las columnas
		// agregar anio a la fila
		array_push($fila, $objFeriado->anio);
		// agregar mes a la fila
		array_push($fila, $objFeriado->mes);
		// agregar dia a la fila
		array_push($fila, $objFeriado->dia);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('feriado?edit&amp;'.'anio='.urlencode($objFeriado->anio).'&amp;'.'mes='.urlencode($objFeriado->mes).'&amp;'.'dia='.urlencode($objFeriado->dia).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'feriado\', \''.'anio='.urlencode($objFeriado->anio).'&amp;'.'mes='.urlencode($objFeriado->mes).'&amp;'.'dia='.urlencode($objFeriado->dia).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('anio', isset($columnas['anio'])?$columnas['anio']:'', 16),
		Form::input4table('mes', isset($columnas['mes'])?$columnas['mes']:'', 16),
		Form::input4table('dia', isset($columnas['dia'])?$columnas['dia']:'', 16),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'anio'=>Tabla::orderby('anio', 'feriado?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=anio'),
		'mes'=>Tabla::orderby('mes', 'feriado?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=mes'),
		'dia'=>Tabla::orderby('dia', 'feriado?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=dia'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'feriado';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objFeriados->count(), 'feriado?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'feriado?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'feriado?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'feriado?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
