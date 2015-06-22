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
 * Mantenedor para la tabla usuario
 * Usuarios del sistema y personal de la empresa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-03
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla usuario y sus fk
require(DIR.'/class/db/final/Usuario.class.php'); // principal para tabla usuario
require(DIR.'/class/db/final/Sucursal.class.php'); // clase para fk de la tabla sucursal
require(DIR.'/class/db/final/Cargo.class.php'); // clase para fk de la tabla cargo
require(DIR.'/class/db/final/Salud.class.php'); // clase para fk de la tabla salud
require(DIR.'/class/db/final/Afp.class.php'); // clase para fk de la tabla afp


// crear objetos a utilizar por el mantenedor
$objUsuario = new Usuario();
$objUsuarios = new Usuarios();
$objSucursal = new Sucursal();
$objSucursals = new Sucursals();
$objCargo = new Cargo();
$objCargos = new Cargos();
$objSalud = new Salud();
$objSaluds = new Saluds();
$objAfp = new Afp();
$objAfps = new Afps();

// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla usuario
$arrFK = array('sucursal_id', 'cargo_id', 'salud_id', 'afp_id'); // columnas fks de la tabla usuario
$arrNotNull = array('id', 'nombre', 'apellido', 'fechanacimiento', 'lang', 'activo', 'sucursal_id', 'cargo_id', 'ingreso'); // columnas que no pueden ser nulas de la tabla usuario

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' usuario'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Usuarios del sistema y personal de la empresa'));

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
	$arrSet['nombre'] = (in_array('nombre', $arrFK)&&empty($_POST['nombre'])) ? null : $_POST['nombre'];
	$arrSet['apellido'] = (in_array('apellido', $arrFK)&&empty($_POST['apellido'])) ? null : $_POST['apellido'];
	$arrSet['fechanacimiento'] = (in_array('fechanacimiento', $arrFK)&&empty($_POST['fechanacimiento'])) ? null : $_POST['fechanacimiento'];
	$arrSet['activo'] = (in_array('activo', $arrFK)&&empty($_POST['activo'])) ? null : $_POST['activo'];
	$arrSet['sucursal_id'] = (in_array('sucursal_id', $arrFK)&&empty($_POST['sucursal_id'])) ? null : $_POST['sucursal_id'];
	$arrSet['cargo_id'] = (in_array('cargo_id', $arrFK)&&empty($_POST['cargo_id'])) ? null : $_POST['cargo_id'];
	$arrSet['ingreso'] = (in_array('ingreso', $arrFK)&&empty($_POST['ingreso'])) ? null : $_POST['ingreso'];
	$arrSet['contratoinicio'] = (in_array('contratoinicio', $arrFK)&&empty($_POST['contratoinicio'])) ? null : $_POST['contratoinicio'];
	$arrSet['contratofin'] = (in_array('contratofin', $arrFK)&&empty($_POST['contratofin'])) ? null : $_POST['contratofin'];
	$arrSet['email'] = (in_array('email', $arrFK)&&empty($_POST['email'])) ? null : $_POST['email'];
	$arrSet['telefono1'] = (in_array('telefono1', $arrFK)&&empty($_POST['telefono1'])) ? null : $_POST['telefono1'];
	$arrSet['telefono2'] = (in_array('telefono2', $arrFK)&&empty($_POST['telefono2'])) ? null : $_POST['telefono2'];
	$arrSet['remuneracion'] = (in_array('remuneracion', $arrFK)&&empty($_POST['remuneracion'])) ? null : $_POST['remuneracion'];
	$arrSet['salud_id'] = (in_array('salud_id', $arrFK)&&empty($_POST['salud_id'])) ? null : $_POST['salud_id'];
	$arrSet['afp_id'] = (in_array('afp_id', $arrFK)&&empty($_POST['afp_id'])) ? null : $_POST['afp_id'];
	$arrSet['cv'] = !empty($_FILES['cv']['tmp_name']) ? $_FILES['cv'] : null;
	$arrSet['avatar'] = !empty($_FILES['avatar']['tmp_name']) ? $_FILES['avatar'] : null;

	// guardar registro
	if($objUsuario->savePersonal($arrSet)) { // en caso de exito
		echo MiSiTiO::success('usuario');
	} else { // en caso de error
		echo MiSiTiO::failure('usuario');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objUsuario->set($arrSet);
		// obtener datos de $objUsuario
		$objUsuario->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bFormUp('usuario'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objUsuario->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', !empty($objUsuario->id) ? $objUsuario->id : '', 'ID del usuario, utilizar RUN sin DV ni puntos ni guión', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="8"');
	$notNull = in_array('nombre', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('nombre', $arrPK))) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'nombre', 'nombre', !empty($objUsuario->nombre) ? $objUsuario->nombre : '', 'Nombres', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="20"');
	$notNull = in_array('apellido', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('apellido', $arrPK))) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'apellido', 'apellido', !empty($objUsuario->apellido) ? $objUsuario->apellido : '', 'Apellidos del usuario (paterno y materno)', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="30"');
	$notNull = in_array('fechanacimiento', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('fechanacimiento', $arrPK))) echo Form::inputDate(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'fechanacimiento', 'fechanacimiento', !empty($objUsuario->fechanacimiento) ? $objUsuario->fechanacimiento : '', true, 'Fecha de nacimiento', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('activo', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('activo', $arrPK))) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'activo', 'activo', !empty($objUsuario->activo) ? $objUsuario->activo : '0', 'Indica si el usuario se encuentra activo en el sistema', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="1"');
	$notNull = in_array('sucursal_id', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('sucursal_id', $arrPK))) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'sucursal_id', 'sucursal_id', $objSucursals->listado(), $objUsuario->sucursal_id, 'ID de la sucursal a la que pertenece el usuario', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('cargo_id', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('cargo_id', $arrPK))) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'cargo_id', 'cargo_id', $objCargos->listado(), $objUsuario->cargo_id, 'ID del cargo que posee el usuario', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('ingreso', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('ingreso', $arrPK))) echo Form::inputDate(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'ingreso', 'ingreso', !empty($objUsuario->ingreso) ? $objUsuario->ingreso : '', true, 'Fecha de ingreso a la empresa', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('contratoinicio', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('contratoinicio', $arrPK))) echo Form::inputDate(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'contratoinicio', 'contratoinicio', !empty($objUsuario->contratoinicio) ? $objUsuario->contratoinicio : '', true, 'Fecha en que se inicio su contrato', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('contratofin', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('contratofin', $arrPK))) echo Form::inputDate(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'contratofin', 'contratofin', !empty($objUsuario->contratofin) ? $objUsuario->contratofin : '', true, 'Fecha en que se puso fin a su contrato', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('email', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('email', $arrPK))) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'email', 'email', !empty($objUsuario->email) ? $objUsuario->email : '', 'Correo electrónico', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="60"');
	$notNull = in_array('telefono1', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('telefono1', $arrPK))) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'telefono1', 'telefono1', !empty($objUsuario->telefono1) ? $objUsuario->telefono1 : '', 'Teléfono primario', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="20"');
	$notNull = in_array('telefono2', $arrNotNull);
	if(isset($_GET['new']) || (isset($_GET['edit']) && !in_array('telefono2', $arrPK))) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'telefono2', 'telefono2', !empty($objUsuario->telefono2) ? $objUsuario->telefono2 : '', 'Teléfono alternativo', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="20"');
	
	// otros campos agregados a mano
	echo Form::input(MiSiTiO::generar('form/asterisco.html').'remuneracion', 'remuneracion', !empty($objUsuario->remuneracion) ? $objUsuario->remuneracion : '0', 'Remuneración mensual bruta, 0 en caso de trabajo a honorarios', MiSiTiO::generar('form/classObligatorio.html').' maxlength="8"');
	echo Form::select('salud_id', 'salud_id', $objSaluds->listado(), $objUsuario->salud_id, 'ID de la institución de salud a la que el usuario esta afiliado');
	echo Form::select('afp_id', 'afp_id', $objAfps->listado(), $objUsuario->afp_id, 'ID de la AFP a la que el usuario esta afiliado');
	echo Form::file('cv', 'cv');
	echo Form::file('avatar', 'avatar');
	
	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla usuario en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objUsuario->set($arrSet);
	// eliminar objeto de la base de datos
	if($objUsuario->delete()) { // en caso de exito
		echo MiSiTiO::success('usuario');
	} else { // en caso de error
		echo MiSiTiO::failure('usuario');
	}
}

// tabla con datos de la tabla usuario
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla usuario
	$objUsuarios->setSelectStatement('id, nombre, apellido, usuario, activo, sucursal_id, cargo_id');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$campos = explode(',', $_GET['search']);
		// recorrer cada campo obtenido y armar arreglo
		$columnas = array();
		foreach($campos as &$campo) {
			list($key, $value) = explode('|', $campo);
			$columnas[$key] = $value;
		}
	}
	// mostrar por defecto solo usuarios activos
	if(!isset($columnas['activo'])) $columnas['activo'] = '1';
	// definir filtros y generar link
	$filtros = array();
	$linkWhere = array();
	if(isset($columnas['id'])&&$columnas['id']!='') {
		array_push($filtros, 'id='.Usuarios::$bd->proteger($columnas['id']));
		array_push($linkWhere, 'id|'.$columnas['id']);
	}
	if(isset($columnas['nombre'])&&$columnas['nombre']!='') {
		array_push($filtros, Usuarios::$bd->like('nombre', $columnas['nombre']));
		array_push($linkWhere, 'nombre|'.$columnas['nombre']);
	}
	if(isset($columnas['apellido'])&&$columnas['apellido']!='') {
		array_push($filtros, Usuarios::$bd->like('apellido', $columnas['apellido']));
		array_push($linkWhere, 'apellido|'.$columnas['apellido']);
	}
	if(isset($columnas['usuario'])&&$columnas['usuario']!='') {
		array_push($filtros, Usuarios::$bd->like('usuario', $columnas['usuario']));
		array_push($linkWhere, 'usuario|'.$columnas['usuario']);
	}
	if(isset($columnas['activo'])&&$columnas['activo']!='') {
		array_push($filtros, "activo = '".Usuarios::$bd->proteger($columnas['activo'])."'");
		array_push($linkWhere, 'activo|'.$columnas['activo']);
	}
	if(isset($columnas['sucursal_id'])&&$columnas['sucursal_id']!='') {
		array_push($filtros, "sucursal_id = '".Usuarios::$bd->proteger($columnas['sucursal_id'])."'");
		array_push($linkWhere, 'sucursal_id|'.$columnas['sucursal_id']);
	}
	if(isset($columnas['cargo_id'])&&$columnas['cargo_id']!='') {
		array_push($filtros, "cargo_id = '".Usuarios::$bd->proteger($columnas['cargo_id'])."'");
		array_push($linkWhere, 'cargo_id|'.$columnas['cargo_id']);
	}
	// agregar filtros al whereStatement
	$objUsuarios->setWhereStatement(implode(' AND ', $filtros));
	$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objUsuarios->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objUsuarios->setOrderByStatement('apellido,nombre');
		$linkOrderBy = 'orderby=apellido,nombre&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objUsuarios->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener tabla con los datos
	$tabla = array();
	foreach($objUsuarios->getObjetos() as $objUsuario) {
		$fila = array();
		// agregar campos a la fila
		array_push($fila, $objUsuario->id);
		array_push($fila, $objUsuario->nombre);
		array_push($fila, $objUsuario->apellido);
		array_push($fila, $objUsuario->usuario);
		array_push($fila, $objUsuario->activo);
		$objFK = $objUsuario->getSucursal();
		array_push($fila, $objFK->glosa);
		$objFK = $objUsuario->getCargo();
		array_push($fila, $objFK->glosa);
		// agregar botones para editar y eliminar
		$acciones = array();
		array_push($acciones, Tabla::icono("javascript:popup('tarjeta?id=".urlencode($objUsuario->id)."', 700, 250)", '/modulos/'.MODULO_DIR.'/img/tarjeta.png', 'Ver tarjeta'));
		array_push($acciones, Tabla::icono('ficha?id='.urlencode($objUsuario->id), '/modulos/'.MODULO_DIR.'/img/ficha.png', 'Ver ficha'));
		array_push($acciones, Tabla::icono('cv?id='.urlencode($objUsuario->id), '/modulos/'.MODULO_DIR.'/img/cv.png', 'Ver curriculum'));
		array_push($acciones, Tabla::editar('usuario?edit&amp;'.'id='.urlencode($objUsuario->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'usuario\', \''.'id='.urlencode($objUsuario->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 10, 'style="width:70px"'),
		Form::input4table('nombre', isset($columnas['nombre'])?$columnas['nombre']:'', 20, 'style="width:120px"'),
		Form::input4table('apellido', isset($columnas['apellido'])?$columnas['apellido']:'', 30, 'style="width:150px"'),
		Form::input4table('usuario', isset($columnas['usuario'])?$columnas['usuario']:'', 20, 'style="width:100px"'),
		Form::select4table('activo', array(array('1','Si'), array('0','No')), isset($columnas['activo'])?$columnas['activo']:'', 'style="width:50px"'),
		Form::select4table('sucursal_id', $objSucursals->listado(), isset($columnas['sucursal_id'])?$columnas['sucursal_id']:'', 'style="width:100px"'),
		Form::select4table('cargo_id', $objCargos->listado(), isset($columnas['cargo_id'])?$columnas['cargo_id']:'', 'style="width:100px"'),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'nombre'=>Tabla::orderby('nombre', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=nombre'),
		'apellido'=>Tabla::orderby('apellido', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=apellido'),
		'usuario'=>Tabla::orderby('usuario', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=usuario'),
		'activo'=>Tabla::orderby('activo', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=activo'),
		'sucursal_id'=>Tabla::orderby('sucursal_id', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=sucursal_id'),
		'cargo_id'=>Tabla::orderby('cargo_id', 'usuario?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=cargo_id'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'usuario';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objUsuarios->count(), 'usuario?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array( null, null, null, null, null, null, null, 100);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'usuario?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'usuario?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'usuario?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
