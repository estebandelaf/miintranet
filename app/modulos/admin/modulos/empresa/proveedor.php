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
 * Mantenedor para la tabla proveedor
 * Proveedores de la empresa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-29
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla proveedor y sus fk
require(DIR.'/class/db/final/Proveedor.class.php'); // principal para tabla proveedor
require(DIR.'/class/db/final/Actividad_economica.class.php'); // clase para fk de la tabla actividad_economica
require(DIR.'/class/db/final/Comuna.class.php'); // clase para fk de la tabla comuna


// crear objetos a utilizar por el mantenedor
$objProveedor = new Proveedor();
$objProveedors = new Proveedors();
$objActividad_economica = new Actividad_economica();
$objActividad_economicas = new Actividad_economicas();
$objComuna = new Comuna();
$objComunas = new Comunas();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla proveedor
$arrFK = array('actividad_economica_id', 'comuna_id'); // columnas fks de la tabla proveedor
$arrNotNull = array('id', 'razonsocial', 'nombrefantasia', 'nacional', 'actividad_economica_id', 'direccion', 'comuna_id', 'telefono1', 'activo'); // columnas que no pueden ser nulas de la tabla proveedor

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' proveedor'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Proveedores de la empresa'));

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
	if(!isset($arrSet['nombrefantasia'])) $arrSet['nombrefantasia'] = (in_array('nombrefantasia', $arrFK)&&empty($_POST['nombrefantasia'])) ? null : $_POST['nombrefantasia'];
	if(!isset($arrSet['nacional'])) $arrSet['nacional'] = (in_array('nacional', $arrFK)&&empty($_POST['nacional'])) ? null : $_POST['nacional'];
	if(!isset($arrSet['actividad_economica_id'])) $arrSet['actividad_economica_id'] = (in_array('actividad_economica_id', $arrFK)&&empty($_POST['actividad_economica_id'])) ? null : $_POST['actividad_economica_id'];
	if(!isset($arrSet['direccion'])) $arrSet['direccion'] = (in_array('direccion', $arrFK)&&empty($_POST['direccion'])) ? null : $_POST['direccion'];
	if(!isset($arrSet['comuna_id'])) $arrSet['comuna_id'] = (in_array('comuna_id', $arrFK)&&empty($_POST['comuna_id'])) ? null : $_POST['comuna_id'];
	if(!isset($arrSet['web'])) $arrSet['web'] = (in_array('web', $arrFK)&&empty($_POST['web'])) ? null : $_POST['web'];
	if(!isset($arrSet['telefono1'])) $arrSet['telefono1'] = (in_array('telefono1', $arrFK)&&empty($_POST['telefono1'])) ? null : $_POST['telefono1'];
	if(!isset($arrSet['telefono2'])) $arrSet['telefono2'] = (in_array('telefono2', $arrFK)&&empty($_POST['telefono2'])) ? null : $_POST['telefono2'];
	if(!isset($arrSet['contacto'])) $arrSet['contacto'] = (in_array('contacto', $arrFK)&&empty($_POST['contacto'])) ? null : $_POST['contacto'];
	if(!isset($arrSet['email'])) $arrSet['email'] = (in_array('email', $arrFK)&&empty($_POST['email'])) ? null : $_POST['email'];
	if(!isset($arrSet['replegal'])) $arrSet['replegal'] = (in_array('replegal', $arrFK)&&empty($_POST['replegal'])) ? null : $_POST['replegal'];
	if(!isset($arrSet['reprut'])) $arrSet['reprut'] = (in_array('reprut', $arrFK)&&empty($_POST['reprut'])) ? null : $_POST['reprut'];
	if(!isset($arrSet['activo'])) $arrSet['activo'] = (in_array('activo', $arrFK)&&empty($_POST['activo'])) ? null : $_POST['activo'];

	$objProveedor->set($arrSet);
	// guardar registro
	if($objProveedor->save()) { // en caso de exito
		echo MiSiTiO::success('proveedor');
	} else { // en caso de error
		echo MiSiTiO::failure('proveedor');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objProveedor->set($arrSet);
		// obtener datos de $objProveedor
		$objProveedor->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('proveedor'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objProveedor->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', ($objProveedor->id===0 || $objProveedor->id==='0' || !empty($objProveedor->id)) ? $objProveedor->id : '', 'ID del proveedor (RUT sin puntos ni dv)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('razonsocial', $arrNotNull);
	$isPK = in_array('razonsocial', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('razonsocial', $objProveedor->razonsocial);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'razonsocial', 'razonsocial', ($objProveedor->razonsocial===0 || $objProveedor->razonsocial==='0' || !empty($objProveedor->razonsocial)) ? $objProveedor->razonsocial : '', 'Razón social del proveedor', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="50"');
	$notNull = in_array('nombrefantasia', $arrNotNull);
	$isPK = in_array('nombrefantasia', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nombrefantasia', $objProveedor->nombrefantasia);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombrefantasia', 'nombrefantasia', ($objProveedor->nombrefantasia===0 || $objProveedor->nombrefantasia==='0' || !empty($objProveedor->nombrefantasia)) ? $objProveedor->nombrefantasia : '', 'Nombre de fantasía del proveedor', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('nacional', $arrNotNull);
	$isPK = in_array('nacional', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('nacional', $objProveedor->nacional);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nacional', 'nacional', array(array('1', 'Si'), array('0', 'No')), ($objProveedor->nacional===0 || $objProveedor->nacional==='0' || !empty($objProveedor->nacional)) ? $objProveedor->nacional : '1', 'Indica si es un proveedor nacional (1) o extranjero (0)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');
	$notNull = in_array('actividad_economica_id', $arrNotNull);
	$isPK = in_array('actividad_economica_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('actividad_economica_id', $objProveedor->actividad_economica_id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'actividad_economica_id', 'actividad_economica_id', $objActividad_economicas->listado(), $objProveedor->actividad_economica_id, 'Código de actividad económica', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('direccion', $arrNotNull);
	$isPK = in_array('direccion', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('direccion', $objProveedor->direccion);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'direccion', 'direccion', ($objProveedor->direccion===0 || $objProveedor->direccion==='0' || !empty($objProveedor->direccion)) ? $objProveedor->direccion : '', 'Dirección principal utilizada', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="70"');
	$notNull = in_array('comuna_id', $arrNotNull);
	$isPK = in_array('comuna_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('comuna_id', $objProveedor->comuna_id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'comuna_id', 'comuna_id', $objComunas->listado(), $objProveedor->comuna_id, 'Comuna', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('web', $arrNotNull);
	$isPK = in_array('web', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('web', $objProveedor->web);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'web', 'web', ($objProveedor->web===0 || $objProveedor->web==='0' || !empty($objProveedor->web)) ? $objProveedor->web : '', 'Sitio web (incluyendo http://)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('telefono1', $arrNotNull);
	$isPK = in_array('telefono1', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('telefono1', $objProveedor->telefono1);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'telefono1', 'telefono1', ($objProveedor->telefono1===0 || $objProveedor->telefono1==='0' || !empty($objProveedor->telefono1)) ? $objProveedor->telefono1 : '', 'Teléfono principal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="20"');
	$notNull = in_array('telefono2', $arrNotNull);
	$isPK = in_array('telefono2', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('telefono2', $objProveedor->telefono2);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'telefono2', 'telefono2', ($objProveedor->telefono2===0 || $objProveedor->telefono2==='0' || !empty($objProveedor->telefono2)) ? $objProveedor->telefono2 : '', 'Teléfono secundario', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="20"');
	$notNull = in_array('contacto', $arrNotNull);
	$isPK = in_array('contacto', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('contacto', $objProveedor->contacto);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'contacto', 'contacto', ($objProveedor->contacto===0 || $objProveedor->contacto==='0' || !empty($objProveedor->contacto)) ? $objProveedor->contacto : '', 'Nombre del contacto dentro de la empresa', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('email', $arrNotNull);
	$isPK = in_array('email', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('email', $objProveedor->email);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'email', 'email', ($objProveedor->email===0 || $objProveedor->email==='0' || !empty($objProveedor->email)) ? $objProveedor->email : '', 'Correo del contacto', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="60"');
	$notNull = in_array('replegal', $arrNotNull);
	$isPK = in_array('replegal', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('replegal', $objProveedor->replegal);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'replegal', 'replegal', ($objProveedor->replegal===0 || $objProveedor->replegal==='0' || !empty($objProveedor->replegal)) ? $objProveedor->replegal : '', 'Representante legal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('reprut', $arrNotNull);
	$isPK = in_array('reprut', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('reprut', $objProveedor->reprut);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'reprut', 'reprut', ($objProveedor->reprut===0 || $objProveedor->reprut==='0' || !empty($objProveedor->reprut)) ? $objProveedor->reprut : '', 'Rut del representante legal (sin puntos ni dv)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="32"');
	$notNull = in_array('activo', $arrNotNull);
	$isPK = in_array('activo', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('activo', $objProveedor->activo);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'activo', 'activo', array(array('1', 'Si'), array('0', 'No')), ($objProveedor->activo===0 || $objProveedor->activo==='0' || !empty($objProveedor->activo)) ? $objProveedor->activo : '1', 'Indica si el proveedor está activo (1) o no (0)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla proveedor en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objProveedor->set($arrSet);
	// eliminar objeto de la base de datos
	if($objProveedor->delete()) { // en caso de exito
		echo MiSiTiO::success('proveedor');
	} else { // en caso de error
		echo MiSiTiO::failure('proveedor');
	}
}

// tabla con datos de la tabla proveedor
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla proveedor
	$objProveedors->setSelectStatement('id, razonsocial, direccion, comuna_id, telefono1, email, activo, web');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, 'id='.Proveedors::$bd->proteger($columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['razonsocial'])&&$columnas['razonsocial']!='') {
			array_push($filtros, Proveedors::$bd->like('razonsocial', $columnas['razonsocial']));
			array_push($linkWhere, 'razonsocial|'.$columnas['razonsocial']);
		}
		if(isset($columnas['direccion'])&&$columnas['direccion']!='') {
			array_push($filtros, Proveedors::$bd->like('direccion', $columnas['direccion']));
			array_push($linkWhere, 'direccion|'.$columnas['direccion']);
		}
		if(isset($columnas['comuna_id'])&&$columnas['comuna_id']!='') {
			array_push($filtros, "comuna_id = '".Proveedors::$bd->proteger($columnas['comuna_id'])."'");
			array_push($linkWhere, 'comuna_id|'.$columnas['comuna_id']);
		}
		if(isset($columnas['telefono1'])&&$columnas['telefono1']!='') {
			array_push($filtros, Proveedors::$bd->like('telefono1', $columnas['telefono1']));
			array_push($linkWhere, 'telefono1|'.$columnas['telefono1']);
		}
		if(isset($columnas['email'])&&$columnas['email']!='') {
			array_push($filtros, Proveedors::$bd->like('email', $columnas['email']));
			array_push($linkWhere, 'email|'.$columnas['email']);
		}
		if(isset($columnas['activo'])&&$columnas['activo']!='') {
			array_push($filtros, 'activo='.Proveedors::$bd->proteger($columnas['activo']));
			array_push($linkWhere, 'activo|'.$columnas['activo']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objProveedors->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objProveedors->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objProveedors->setOrderByStatement('id');
		$linkOrderBy = 'orderby=id&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objProveedors->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objProveedors->getObjetos() as $objProveedor) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objProveedor->id);
		// agregar razonsocial a la fila
		array_push($fila, $objProveedor->razonsocial);
		// agregar direccion a la fila
		array_push($fila, $objProveedor->direccion);
		// agregar comuna_id a la fila
		if($objProveedor->comuna_id!='') {
			$objFK = $objProveedor->getComuna();
			$comuna = $glosaFK = $objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar telefono1 a la fila
		array_push($fila, $objProveedor->telefono1);
		// agregar email a la fila
		array_push($fila, $objProveedor->email);
		// agregar activo a la fila
		array_push($fila, $objProveedor->activo?'Si':'No');

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::icono($objProveedor->web, '/modulos/admin/modulos/empresa/img/url.png', 'Ir a '.$objProveedor->web));
		array_push($acciones, Tabla::icono('javascript:popup(\'/mapa/ver?q='.urlencode($objProveedor->direccion.', '.$comuna).'\', 650, 380, false)', '/modulos/admin/modulos/empresa/img/mapa.png'));
		array_push($acciones, Tabla::editar('proveedor?edit&amp;'.'id='.urlencode($objProveedor->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'proveedor\', \''.'id='.urlencode($objProveedor->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 32, 'style="width:55px;"'),
		Form::input4table('razonsocial', isset($columnas['razonsocial'])?$columnas['razonsocial']:'', 50, 'style="width:100px;"'),
		Form::input4table('direccion', isset($columnas['direccion'])?$columnas['direccion']:'', 70, 'style="width:90px;"'),
		Form::select4table('comuna_id', $objComunas->listado(), isset($columnas['comuna_id'])?$columnas['comuna_id']:'', 'style="width:100px;"'),
		Form::input4table('telefono1', isset($columnas['telefono1'])?$columnas['telefono1']:'', 20, 'style="width:90px;"'),
		Form::input4table('email', isset($columnas['email'])?$columnas['email']:'', 60, 'style="width:100px;"'),
		Form::select4table('activo', array(array('1','Si'), array('0','No')), isset($columnas['activo'])?$columnas['activo']:'', 'style="width:70px;"'),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'razonsocial'=>Tabla::orderby('razonsocial', 'proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=razonsocial'),
		'direccion'=>Tabla::orderby('direccion', 'proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=direccion'),
		'comuna_id'=>Tabla::orderby('comuna_id', 'proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=comuna_id'),
		'telefono1'=>Tabla::orderby('telefono1', 'proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=telefono1'),
		'email'=>Tabla::orderby('email', 'proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=email'),
		'activo'=>Tabla::orderby('activo', 'proveedor?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=activo'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'proveedor';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objProveedors->count(), 'proveedor?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, null, null, null, 80);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'proveedor?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'proveedor?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'proveedor?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
