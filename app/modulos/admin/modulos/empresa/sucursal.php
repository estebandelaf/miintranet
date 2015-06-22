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
 * Mantenedor para la tabla sucursal
 * Sucursales y casa matriz de la empresa
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */

// define si se utilizara o no el paginador (=true se usa)
define('PAGINAR', true);

// require general: incluye encabezado de la pagina, configuraciones, acciones, creacion de objetos, etc
require('../../../../inc/web1.inc.php');

// clases requeridas para ser utilizada por el mantenedor, relacionadas con la tabla sucursal y sus fk
require(DIR.'/class/db/final/Sucursal.class.php'); // principal para tabla sucursal
require(DIR.'/class/db/final/Comuna.class.php'); // clase para fk de la tabla comuna
require(DIR.'/class/db/final/Usuario.class.php'); // clase para fk de la tabla usuario


// crear objetos a utilizar por el mantenedor
$objSucursal = new Sucursal();
$objSucursals = new Sucursals();
$objComuna = new Comuna();
$objComunas = new Comunas();
$objUsuario = new Usuario();
$objUsuarios = new Usuarios();


// definir arreglo de pk, fk y campos no nulos
$arrPK = array('id'); // columnas pks de la tabla sucursal
$arrFK = array('comuna_id', 'usuario_id'); // columnas fks de la tabla sucursal
$arrNotNull = array('id', 'glosa', 'matriz', 'direccion', 'comuna_id', 'email', 'telefono'); // columnas que no pueden ser nulas de la tabla sucursal

// titulo del mantenedor
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MAINTAINER_TITLE.' sucursal'));

// comentario de la tabla
echo MiSiTiO::generar('parrafo.html', array('txt'=>'Sucursales y casa matriz de la empresa'));

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
	if(!isset($arrSet['matriz'])) $arrSet['matriz'] = (in_array('matriz', $arrFK)&&empty($_POST['matriz'])) ? null : $_POST['matriz'];
	if(!isset($arrSet['direccion'])) $arrSet['direccion'] = (in_array('direccion', $arrFK)&&empty($_POST['direccion'])) ? null : $_POST['direccion'];
	if(!isset($arrSet['comuna_id'])) $arrSet['comuna_id'] = (in_array('comuna_id', $arrFK)&&empty($_POST['comuna_id'])) ? null : $_POST['comuna_id'];
	if(!isset($arrSet['email'])) $arrSet['email'] = (in_array('email', $arrFK)&&empty($_POST['email'])) ? null : $_POST['email'];
	if(!isset($arrSet['telefono'])) $arrSet['telefono'] = (in_array('telefono', $arrFK)&&empty($_POST['telefono'])) ? null : $_POST['telefono'];
	if(!isset($arrSet['fax'])) $arrSet['fax'] = (in_array('fax', $arrFK)&&empty($_POST['fax'])) ? null : $_POST['fax'];
	if(!isset($arrSet['usuario_id'])) $arrSet['usuario_id'] = (in_array('usuario_id', $arrFK)&&empty($_POST['usuario_id'])) ? null : $_POST['usuario_id'];

	$objSucursal->set($arrSet);
	// guardar registro
	if($objSucursal->save()) { // en caso de exito
		echo MiSiTiO::success('sucursal');
	} else { // en caso de error
		echo MiSiTiO::failure('sucursal');
	}
}

// formulario para editar o crear nuevo registro
else if(isset($_GET['new']) || (isset($_GET['edit']) && !empty($_GET['id']))) {
	// si se esta editando se buscan los atributos del objeto
	if(isset($_GET['edit'])) {
		// setear pk
		$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

		$objSucursal->set($arrSet);
		// obtener datos de $objSucursal
		$objSucursal->get();
		$link = '?edit&amp;'.'id='.urlencode($_GET['id']);
	} else $link = '?new';
	// se muestra el formulario
	echo Form::bForm('sucursal'.$link, 'validarFormulario');
	$notNull = in_array('id', $arrNotNull);
	$isPK = in_array('id', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('id', $objSucursal->id);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'id', 'id', ($objSucursal->id===0 || $objSucursal->id==='0' || !empty($objSucursal->id)) ? $objSucursal->id : '', 'ID de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="5"');
	$notNull = in_array('glosa', $arrNotNull);
	$isPK = in_array('glosa', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('glosa', $objSucursal->glosa);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'glosa', 'glosa', ($objSucursal->glosa===0 || $objSucursal->glosa==='0' || !empty($objSucursal->glosa)) ? $objSucursal->glosa : '', 'Nombre de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="45"');
	$notNull = in_array('matriz', $arrNotNull);
	$isPK = in_array('matriz', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('matriz', $objSucursal->matriz);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'matriz', 'matriz', array(array('1','Si'), array('0','No')), ($objSucursal->matriz===0 || $objSucursal->matriz==='0' || !empty($objSucursal->matriz)) ? $objSucursal->matriz : '0', 'Indica si la sucursal es la casa matriz, 1 lo es, con 0 no', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="16"');
	$notNull = in_array('direccion', $arrNotNull);
	$isPK = in_array('direccion', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('direccion', $objSucursal->direccion);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'direccion', 'direccion', ($objSucursal->direccion===0 || $objSucursal->direccion==='0' || !empty($objSucursal->direccion)) ? $objSucursal->direccion : '', 'Dirección de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="100"');
	$notNull = in_array('comuna_id', $arrNotNull);
	$isPK = in_array('comuna_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('comuna_id', $arrFK)) {
			$objFK = $objSucursal->getComuna();
			$text = $objFK->id; // FIXME: cambiar atributo por la glosa/descripcion/nombre/etc que corresponda
		} else $text = $objSucursal->comuna_id;
		echo Form::text('comuna_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'comuna_id', 'comuna_id', $objComunas->listado(), $objSucursal->comuna_id, 'Comuna de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
	$notNull = in_array('email', $arrNotNull);
	$isPK = in_array('email', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('email', $objSucursal->email);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'email', 'email', ($objSucursal->email===0 || $objSucursal->email==='0' || !empty($objSucursal->email)) ? $objSucursal->email : '', 'Correo electrónico de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="50"');
	$notNull = in_array('telefono', $arrNotNull);
	$isPK = in_array('telefono', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('telefono', $objSucursal->telefono);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'telefono', 'telefono', ($objSucursal->telefono===0 || $objSucursal->telefono==='0' || !empty($objSucursal->telefono)) ? $objSucursal->telefono : '', 'Teléfono de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="25"');
	$notNull = in_array('fax', $arrNotNull);
	$isPK = in_array('fax', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('fax', $objSucursal->fax);
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'fax', 'fax', ($objSucursal->fax===0 || $objSucursal->fax==='0' || !empty($objSucursal->fax)) ? $objSucursal->fax : '', 'Fax de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="25"');
	$notNull = in_array('usuario_id', $arrNotNull);
	$isPK = in_array('usuario_id', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('usuario_id', $arrFK)) {
			$objFK = $objSucursal->getUsuario();
			$text = $objFK->id; // FIXME: cambiar atributo por la glosa/descripcion/nombre/etc que corresponda
		} else $text = $objSucursal->usuario_id;
		echo Form::text('usuario_id', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'usuario_id', 'usuario_id', $objUsuarios->listado(), $objSucursal->usuario_id, 'Usuario a cargo de la sucursal', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));

	echo Form::saveButton();
	echo Form::eForm();
	echo MiSiTiO::generar('form/requeridos.html', array('msg'=>LANG_FORM_REQUIRED));
}

// eliminar registro de la tabla sucursal en la base de datos
else if(isset($_GET['delete']) && !empty($_GET['id'])) {
	// setear pk
	$arrSet = array();
		$arrSet['id'] = urldecode($_GET['id']);

	$objSucursal->set($arrSet);
	// eliminar objeto de la base de datos
	if($objSucursal->delete()) { // en caso de exito
		echo MiSiTiO::success('sucursal');
	} else { // en caso de error
		echo MiSiTiO::failure('sucursal');
	}
}

// tabla con datos de la tabla sucursal
else if(!isset($_GET['new']) && !isset($_GET['edit']) && !isset($_GET['delete'])) {
	// definir si se usara o no paginador
	if(isset($_GET['nopages'])) {
		$paginar = false;
		$linkNoPages = 'nopages&amp;';
	} else {
		$paginar = PAGINAR;
		$linkNoPages = '';
	}
	// set select, si es vacio o se omite se seleccionarán todos los campos de la tabla sucursal
	$objSucursals->setSelectStatement('id, glosa, direccion, comuna_id, email, telefono');
	// set where
	if(!empty($_GET['search'])) {
		// obtener campos desde la variable search
		$columnas = extraerCampos($_GET['search']);
		// definir filtros y generar link
		$filtros = array();
		$linkWhere = array();
		if(isset($columnas['id'])&&$columnas['id']!='') {
			array_push($filtros, Sucursals::$bd->like('id', $columnas['id']));
			array_push($linkWhere, 'id|'.$columnas['id']);
		}
		if(isset($columnas['glosa'])&&$columnas['glosa']!='') {
			array_push($filtros, Sucursals::$bd->like('glosa', $columnas['glosa']));
			array_push($linkWhere, 'glosa|'.$columnas['glosa']);
		}
		if(isset($columnas['direccion'])&&$columnas['direccion']!='') {
			array_push($filtros, Sucursals::$bd->like('direccion', $columnas['direccion']));
			array_push($linkWhere, 'direccion|'.$columnas['direccion']);
		}
		if(isset($columnas['comuna_id'])&&$columnas['comuna_id']!='') {
			array_push($filtros, "comuna_id = '".Sucursals::$bd->proteger($columnas['comuna_id'])."'");
			array_push($linkWhere, 'comuna_id|'.$columnas['comuna_id']);
		}
		if(isset($columnas['email'])&&$columnas['email']!='') {
			array_push($filtros, Sucursals::$bd->like('email', $columnas['email']));
			array_push($linkWhere, 'email|'.$columnas['email']);
		}
		if(isset($columnas['telefono'])&&$columnas['telefono']!='') {
			array_push($filtros, Sucursals::$bd->like('telefono', $columnas['telefono']));
			array_push($linkWhere, 'telefono|'.$columnas['telefono']);
		}

		// agregar filtros al whereStatement
		if(count($filtros)) {
			$objSucursals->setWhereStatement(implode(' AND ', $filtros));
			$linkWhere = 'search='.implode(',', $linkWhere).'&amp;';
		} else $linkWhere = '';
	} else $linkWhere = '';
	// set order by
	if(!empty($_GET['orderby'])) {
		$objSucursals->setOrderByStatement(urldecode($_GET['orderby']).' '.(isset($_GET['d'])?'DESC':'ASC'));
		$linkOrderBy = 'orderby='.$_GET['orderby'].(isset($_GET['d'])?'&amp;d':'').'&amp;';
	}  else {
		$objSucursals->setOrderByStatement('glosa');
		$linkOrderBy = 'orderby=glosa&amp;';
	}
	// si se utiliza paginador se procesa lo relacionado a este
	if($paginar) {
		// obtener filas a mostrar y pagina actual
		$showRows = $Usuario->filasporpagina;
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		// set limit
		$objSucursals->setLimitStatement($showRows, ($page-1)*$showRows);
	}
	// obtener objetos con los datos solicitados y crear tabla
	$tabla = array();
	foreach($objSucursals->getObjetos() as $objSucursal) {
		$fila = array();
		// agregar datos de las columnas
		// agregar id a la fila
		array_push($fila, $objSucursal->id);
		// agregar glosa a la fila
		array_push($fila, $objSucursal->glosa);
		// agregar direccion a la fila
		array_push($fila, $objSucursal->direccion);
		// agregar comuna_id a la fila
		if($objSucursal->comuna_id!='') {
			$objFK = $objSucursal->getComuna();
			$glosaFK = $objFK->nombre;
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
		// agregar email a la fila
		array_push($fila, $objSucursal->email);
		// agregar telefono a la fila
		array_push($fila, $objSucursal->telefono);

		// agregar botones de acciones
		$acciones = array();
		array_push($acciones, Tabla::editar('sucursal?edit&amp;'.'id='.urlencode($objSucursal->id).''));
		array_push($acciones, Tabla::eliminar('eliminar(\'sucursal\', \''.'id='.urlencode($objSucursal->id).'\')'));
		array_push($fila, implode(' ', $acciones));
		// agregar la fila a la tabla
		array_push($tabla, $fila);
	}
	// colocar campos para busquedas, en caso que el ancho de los campos sea muy grande
	// se puede pasar como 4to aprametro un 'style="width:100px;"' con el tamaño del input,
	// en el ejemplo se uso 100px, pero puede ser otro para ajustarlos
	array_unshift($tabla, array(
		Form::input4table('id', isset($columnas['id'])?$columnas['id']:'', 5, 'style="width:80px;"'),
		Form::input4table('glosa', isset($columnas['glosa'])?$columnas['glosa']:'', 45),
		Form::input4table('direccion', isset($columnas['direccion'])?$columnas['direccion']:'', 100),
		Form::select4table('comuna_id', $objComunas->listado(), !empty($columnas['comuna_id'])?$columnas['comuna_id']:'', 'style="width:100px;"'),
		Form::input4table('email', isset($columnas['email'])?$columnas['email']:'', 50),
		Form::input4table('telefono', isset($columnas['telefono'])?$columnas['telefono']:'', 25, 'style="width:80px;"'),

		Form::buscar()
	));
	// colocar titulos de columnas
	array_unshift($tabla, array(
		'id'=>Tabla::orderby('id', 'sucursal?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=id'),
		'glosa'=>Tabla::orderby('glosa', 'sucursal?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=glosa'),
		'direccion'=>Tabla::orderby('direccion', 'sucursal?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=direccion'),
		'comuna_id'=>Tabla::orderby('comuna_id', 'sucursal?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=comuna_id'),
		'email'=>Tabla::orderby('email', 'sucursal?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=email'),
		'telefono'=>Tabla::orderby('telefono', 'sucursal?page=1&amp;'.$linkWhere.$linkNoPages.'orderby=telefono'),

		LANG_TABLA_ACTIONS
	));
	// mostrar tabla
	Tabla::$id = 'sucursal';
	Tabla::$mantenedor = true;
	Tabla::$paginator = $paginar ? MiSiTiO::paginador($page, $objSucursals->count(), 'sucursal?'.$linkWhere.$linkOrderBy.'page=', $showRows, TABLE_SHOW_PAGES) : '';
	Tabla::$ancho = array(null, null, null, null, null, null, null, null, null, 50);
	echo Form::bForm('', 'buscar', '');
	echo Form::hidden('mantenedorLink', 'sucursal?'.$linkNoPages.$linkOrderBy.'page=1');
	echo Tabla::generar($tabla);
	echo Form::eForm();
	if(PAGINAR) echo $paginar ? MiSiTiO::generar('paginador/usar.html', array('url'=>'sucursal?'.$linkWhere.$linkOrderBy.'nopages', 'msg'=>LANG_PAGINATOR_HIDE)) : MiSiTiO::generar('paginador/usar.html', array('url'=>'sucursal?'.$linkWhere.$linkOrderBy, 'msg'=>LANG_PAGINATOR_SHOW));
	
}

// require general: incluye fin de la pagina
require(DIR.'/inc/web2.inc.php');

?>
