<?php

// configurar plantilla
define('TEMPLATE', 'default');

// configurar requerimientos
define('REQ_PHP_VERSION', 5);

// activar errores
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL | E_STRICT);

// incluir funciones
require('inc/funciones.inc.php');

// template web1
echo generar('template/'.TEMPLATE.'/web1.html');

// verificar que no exista archivo de configuracion, si ya existe
// implica que el sistema ya esta instalado en el servidor
if(filesize('../inc/config.inc.php')) {
	echo generar('template/'.TEMPLATE.'/existe.html');
}

// pagina de bienvenida
else if(!count($_POST)) {
	echo generar('template/'.TEMPLATE.'/paso0.html');
}

// verificar requisitos de instalacion
else if(isset($_POST['paso1'])) {

	// imagenes
	define('OK', '<img src="template/'.TEMPLATE.'/img/ok.png" alt="ok" />');
	define('NOK', '<img src="template/'.TEMPLATE.'/img/nok.png" alt="nok" />');
	define('WARNING', '<img src="template/'.TEMPLATE.'/img/warning.png" alt="warning" />');
	
	// variables
	$req = array();

	// obtener datos desde phpinfo
	$phpinfo = phpinfo_array();
	
	// verificar base de datos postgresql
	if(array_key_exists('pgsql', $phpinfo)) $req['postgresql'] = true;
	else $req['postgresql'] = false;
	// verificar base de datos mysql
	if(array_key_exists('mysqli', $phpinfo)) $req['mysql'] = true;
	else $req['mysql'] = false;
	
	// modulos apache
	$modulosApache = explode(' ', $phpinfo['apache2handler']['Loaded Modules']);

	// usar function_exists para ver si una funcion existe
	//echo '<pre>'; print_r(get_defined_functions()); echo '</pre>';

	// mostrar pagina con paso1
	echo generar('template/'.TEMPLATE.'/paso1.html', array(
		'ok'=>OK,
		'nok'=>NOK,
		'warning'=>WARNING,
		'mod_rewrite'=>array_search('mod_rewrite', $modulosApache)!==false?OK:NOK,
		'mod_ssl'=>array_search('mod_ssl', $modulosApache)!==false?OK:WARNING,
		'php'=>$phpinfo['PHP Configuration']['PHP Version'][0]==REQ_PHP_VERSION?OK:NOK,
		'display_errors'=>$phpinfo['Core']['display_errors']=='Off'?OK:WARNING,
		'file_uploads'=>$phpinfo['Core']['file_uploads']=='On'?OK:NOK,
		'safe_mode'=>$phpinfo['Core']['safe_mode']=='On'?OK:WARNING,
		'php_gd'=>$phpinfo['gd']['GD Support']=='enabled'?OK:NOK,
		'pear_image_barcode'=>file_exists('/usr/share/php/Image/Barcode.php')?OK:NOK,
		'pear_spreadsheet_excel_writer'=>file_exists('/usr/share/php/Spreadsheet/Excel/Writer.php')?OK:NOK,
		'pear_mail'=>file_exists('/usr/share/php/Mail.php')?OK:NOK,
		'pear_mail_mime'=>file_exists('/usr/share/php/Mail/mime.php')?OK:NOK,
		'db_postgresql'=>$req['postgresql']?OK:WARNING,
		'db_mysql'=>$req['mysql']?OK:WARNING,
		'db_any'=>($req['postgresql']||$req['mysql'])?OK:NOK,
	));

}

// configurar aplicacion
else if(isset($_POST['paso2'])) {
	echo generar('template/'.TEMPLATE.'/paso2.html');
}

// instalar base de datos y datos predeterminados
else if(isset($_POST['paso3'])) {
	
	// crear objeto de la base de datos
	require('../class/interface/BD.interface.php');
	if($_POST['db_type']=='postgresql') {
		require('../class/db/PostgreSQL.class.php');
		$bd = new PostgreSQL($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_port'], $_POST['db_char']);
	}
	else if($_POST['db_type']=='mysql') {
		require('../class/db/MySQL.class.php');
		$bd = new MySQL($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_port'], $_POST['db_char']);
	}
	else if($_POST['db_type']=='oracle') {
		require('../class/db/Oracle.class.php');
		$bd = new Oracle($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_port'], $_POST['db_char']);
	}
	
	// cargar estructura de la base de datos
	$bd->consulta(file_get_contents('sql/'.$_POST['db_type'].'.sql'));
	
	// cargar datos predeterminados según pais
	$bd->consulta(file_get_contents('sql/datos/'.$_POST['pais'].'/region.sql'));
	$bd->consulta(file_get_contents('sql/datos/'.$_POST['pais'].'/comuna.sql'));
	$bd->consulta(file_get_contents('sql/datos/'.$_POST['pais'].'/afp.sql'));
	$bd->consulta(file_get_contents('sql/datos/'.$_POST['pais'].'/salud.sql'));
	$bd->consulta(file_get_contents('sql/datos/'.$_POST['pais'].'/actividad_economica.sql'));

	// listado de comunas listas para formulario select
	$comunas = $bd->getTabla("SELECT id, nombre FROM comuna ORDER BY nombre ASC");
	$comunasOption = "\t\t\t".'<option value="">Seleccione una comuna</option>'."\n";
	foreach($comunas as &$comuna) {
		$comunasOption .= "\t\t\t".'<option value="'.$comuna['id'].'">'.$comuna['nombre'].'</option>'."\n";
	}
	
	// listado de actividades economicas listas para formulario select
	$actecos = $bd->getTabla("SELECT id, glosa FROM actividad_economica ORDER BY glosa ASC");
	$actecosOption = "\t\t\t".'<option value="">Seleccione una actividad económica</option>'."\n";
	foreach($actecos as &$acteco) {
		$actecosOption .= "\t\t\t".'<option value="'.$acteco['id'].'">'.$acteco['id'].' - '.$acteco['glosa'].'</option>'."\n";
	}

	// mostrar formulario del paso 3
	echo generar('template/'.TEMPLATE.'/paso3.html', array(
		'comunas'=>$comunasOption,
		'actecos'=>$actecosOption,
		'db_type'=>$_POST['db_type'],
		'db_host'=>$_POST['db_host'],
		'db_port'=>$_POST['db_port'],
		'db_user'=>$_POST['db_user'],
		'db_pass'=>$_POST['db_pass'],
		'db_name'=>$_POST['db_name'],
		'db_char'=>$_POST['db_char'],
	));

}

// crear sucursales, cargos y areas del usuario root
else if(isset($_POST['paso4'])) {
	
	// crear objeto de la base de datos
	require('../class/interface/BD.interface.php');
	if($_POST['db_type']=='postgresql') {
		require('../class/db/PostgreSQL.class.php');
		$bd = new PostgreSQL($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_port'], $_POST['db_char']);
	}
	else if($_POST['db_type']=='mysql') {
		require('../class/db/MySQL.class.php');
		$bd = new MySQL($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_port'], $_POST['db_char']);
	}
	else if($_POST['db_type']=='oracle') {
		require('../class/db/Oracle.class.php');
		$bd = new Oracle($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_port'], $_POST['db_char']);
	}
	
	// generar consultas insert que se cargaran en la base de datos
	$sql = generar('sql/insert.sql', array(
		'usuario_id'=>$_POST['usuario_id'],
		'usuario_nombre'=>$_POST['usuario_nombre'],
		'usuario_apellido'=>$_POST['usuario_apellido'],
		'usuario_fechanacimiento'=>$_POST['usuario_fechanacimiento'],
		'usuario_ingreso'=>$_POST['usuario_ingreso'],
		'area_glosa'=>$_POST['area_glosa'],
		'cargo_glosa'=>$_POST['cargo_glosa'],
		'usuario_email'=>$_POST['usuario_email'],
		'usuario_lang'=>$_POST['usuario_lang'],
		'usuario_usuario'=>$_POST['usuario_usuario'],
		'usuario_clave'=>md5($_POST['usuario_clave']),
		'empresa_acteco'=>$_POST['empresa_acteco'],
		'empresa_rut'=>$_POST['empresa_rut'],
		'empresa_razon_social'=>$_POST['empresa_razon_social'],
		'empresa_nombre_fantasia'=>$_POST['empresa_nombre_fantasia'],
		'empresa_rlegal'=>$_POST['empresa_rlegal'],
		'sucursal_id'=>$_POST['sucursal_id'],
		'sucursal_glosa'=>$_POST['sucursal_glosa'],
		'sucursal_direccion'=>$_POST['sucursal_direccion'],
		'sucursal_comuna_id'=>$_POST['sucursal_comuna_id'],
		'sucursal_email'=>$_POST['sucursal_email'],
		'sucursal_telefono'=>$_POST['sucursal_telefono'],
		'site_title'=>$_POST['site_title'],
		'tmp'=>$_POST['tmp'],
		'zona_horaria'=>$_POST['zona_horaria'],
		'usuario_ip'=>$_SERVER['REMOTE_ADDR'],
	));
	$bd->consulta($sql);
	
	// mostrar mensaje final
	$config = generar('src/config.inc.phps', array(
		'date'=>date('Y-m-d'),
		'db_type'=>$_POST['db_type'],
		'db_host'=>$_POST['db_host'],
		'db_port'=>$_POST['db_port'],
		'db_user'=>$_POST['db_user'],
		'db_pass'=>$_POST['db_pass'],
		'db_name'=>$_POST['db_name'],
		'db_char'=>$_POST['db_char'],
	));
	echo generar('template/'.TEMPLATE.'/paso4.html', array(
		'config'=>$config
	));

}

// template web2
echo generar('template/'.TEMPLATE.'/web2.html');

?>
