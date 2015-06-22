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
 * Mantenedor para la tabla noticia
 * Noticias para ser publicada en la portada de la app y en rss
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-29
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla noticia y sus fk
require(DIR.'/class/db/final/Noticia.class.php'); // principal para tabla noticia
require(DIR.'/class/db/final/Usuario.class.php'); // clase para fk de la tabla usuario


// crear objetos a utilizar por el mantenedor
$objNoticia = new Noticia();
$objNoticias = new Noticias();
$objUsuario = new Usuario();
$objUsuarios = new Usuarios();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla noticia
$arrFK = array('usuario_id'); // columnas fks de la tabla noticia
$arrNotNull = array('id', 'titulo', 'cuerpo', 'fechahora', 'expiracion', 'usuario_id', 'resumen'); // columnas que no pueden ser nulas de la tabla noticia

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' noticia'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Noticias para ser publicada en la portada de la app y en rss'));

// editar o crear nuevo registro
if(isset($_POST['save'])) {
	// arreglo para hacer set
	$arrSet = array();
	// si se esta editando setear pk
	if(isset($_GET['edit']) && !empty($_GET['id'])) {
		$arrSet['id'] = urldecode($_GET['id']);

	}
	// definir otros campos
	if(!isset($arrSet['titulo'])) $arrSet['titulo'] = (in_array('titulo', $arrFK)&&empty($_POST['titulo'])) ? null : $_POST['titulo'];
	if(!isset($arrSet['cuerpo'])) $arrSet['cuerpo'] = (in_array('cuerpo', $arrFK)&&empty($_POST['cuerpo'])) ? null : $_POST['cuerpo'];
	//if(!isset($arrSet['fechahora'])) $arrSet['fechahora'] = (in_array('fechahora', $arrFK)&&empty($_POST['fechahora'])) ? null : $_POST['fechahora'];
	if(!isset($arrSet['expiracion'])) $arrSet['expiracion'] = (in_array('expiracion', $arrFK)&&empty($_POST['expiracion'])) ? null : $_POST['expiracion'];
	//if(!isset($arrSet['usuario_id'])) $arrSet['usuario_id'] = (in_array('usuario_id', $arrFK)&&empty($_POST['usuario_id'])) ? null : $_POST['usuario_id'];
	if(!isset($arrSet['resumen'])) $arrSet['resumen'] = (in_array('resumen', $arrFK)&&empty($_POST['resumen'])) ? null : $_POST['resumen'];

	// forzar campos
	$arrSet['fechahora'] = FECHAHORA;
	$arrSet['usuario_id'] = $Usuario->id;
	
	$objNoticia->set($arrSet);
	// guardar registro
	if($objNoticia->save()) { // en caso de exito
		echo MiSiTiO::success('noticia');
	} else { // en caso de error
		echo MiSiTiO::failure('noticia');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objNoticia->set($arrSet);
		// obtener datos de $objNoticia
		$objNoticia->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('noticia'.$link, 'validarFormulario');
	$notNull = in_array('titulo', $arrNotNull);
	$isPK = in_array('titulo', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('titulo', $objNoticia->titulo);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'titulo', 'titulo', ($objNoticia->titulo===0 || $objNoticia->titulo==='0' || !empty($objNoticia->titulo)) ? $objNoticia->titulo : '', 'Tí­tulo de la noticia', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('resumen', $arrNotNull);
	$isPK = in_array('resumen', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('resumen', $objNoticia->resumen);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'resumen', 'resumen', ($objNoticia->resumen===0 || $objNoticia->resumen==='0' || !empty($objNoticia->resumen)) ? $objNoticia->resumen : '', 'Resumen de la noticia para ser mostrado en los links hacia esta', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="80"');
	$notNull = in_array('cuerpo', $arrNotNull);
	$isPK = in_array('cuerpo', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('cuerpo', $objNoticia->cuerpo);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::textarea(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'cuerpo', 'cuerpo', ($objNoticia->cuerpo===0 || $objNoticia->cuerpo==='0' || !empty($objNoticia->cuerpo)) ? $objNoticia->cuerpo : '', 'Texto de la noticia', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength=""');
	$notNull = in_array('expiracion', $arrNotNull);
	$isPK = in_array('expiracion', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('expiracion', $objNoticia->expiracion);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::inputDate(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'expiracion', 'expiracion', !empty($objNoticia->expiracion) ? $objNoticia->expiracion : '', true, 'Fecha hasta cuando la noticia es válida', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla noticia en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objNoticia->set($arrSet);
	// eliminar objeto de la base de datos
	if($objNoticia->delete()) { // en caso de exito
		echo MiSiTiO::success('noticia');
	} else { // en caso de error
		echo MiSiTiO::failure('noticia');
	}
}

// tabla con datos de la tabla noticia
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla noticia
	$objNoticias->setSelectStatement('id, titulo, expiracion, usuario_id');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='id') {
			array_push($filtros, 'id='.Noticias::$bd->proteger($columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['titulo'])&&$columnas['titulo']!='') {
			array_push($filtros, Noticias::$bd->like('titulo', $columnas['titulo']));
			array_push($linkWhere, 'titulo|'.$columnas['titulo']);
		}
		if(isset($columnas['expiracionDesde'])&&$columnas['expiracionDesde']!='') {
			
			array_push($filtros, "expiracion>='".Noticias::$bd->proteger($columnas['expiracionDesde'])."'");
			array_push($linkWhere, 'expiracionDesde|'.$columnas['expiracionDesde']);
		}
		if(isset($columnas['expiracionHasta'])&&$columnas['expiracionHasta']!='') {
			
			array_push($filtros, "expiracion<='".Noticias::$bd->proteger($columnas['expiracionHasta'])."'");
			array_push($linkWhere, 'expiracionHasta|'.$columnas['expiracionHasta']);
		}
		if(isset($columnas['usuario_id'])&&$columnas['usuario_id']!='') {
			array_push($filtros, "usuario_id = '".Noticias::$bd->proteger($columnas['usuario_id'])."'");
			array_push($linkWhere, 'usuario_id|'.$columnas['usuario_id']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objNoticias->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objNoticias->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objNoticias->setOrderByStatement('titulo');
		$linkOrderBy = 'orderby=titulo&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objNoticias->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objNoticias->getObjetos() as $objNoticia) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objNoticia->id);
		// agregar titulo a la fila
		array_push($fila, $objNoticia->titulo);
		// agregar expiracion a la fila
		array_push($fila, $objNoticia->expiracion);
		// agregar usuario_id a la fila
		if($objNoticia->usuario_id!='') {
			$objFK = $objNoticia->getUsuario();
			$glosaFK = $objFK->apellido.', '.$objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('noticia?edit&amp;'.'id='.urlencode($objNoticia->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'noticia\', \''.'id='.urlencode($objNoticia->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32),
		Form::input4table('titulo', isset($columnas['titulo'])?$columnas['titulo']:'', 30),
		Form::inputDate4table('expiracionDesde', isset($columnas['expiracionDesde'])?$columnas['expiracionDesde']:'', '').
		Form::inputDate4table('expiracionHasta', isset($columnas['expiracionHasta'])?$columnas['expiracionHasta']:'', ''),
		Form::select4table('usuario_id', $objUsuarios->listado(), isset($columnas['usuario_id'])?$columnas['usuario_id']:''),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'noticia?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'titulo'=>Tabla::orderby('titulo', 'noticia?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=titulo'),
		'expiracion'=>Tabla::orderby('expiracion', 'noticia?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=expiracion'),
		'usuario_id'=>Tabla::orderby('usuario_id', 'noticia?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=usuario_id'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'noticia';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objNoticias->count(), 'noticia?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'noticia?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'noticia?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'noticia?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
