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
 * Archivo de acciones globales del sitio
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-02-15
 */

// cambiar el tiempo de ejecucion a infinito xD
ini_set('max_execution_time', '0');

// cambiar la memoria maxima a utilizar por el script
ini_set('memory_limit', '128M');

// habilitar el uso de bufferes de salida
ob_start();

// tiempo de inicio del script
define('INICIO', microtime(true));

// crear sesion web
session_start();

// crear objeto para la conexion global de la base de datos, solo asigna datos para la conexion
if(DB_TYPE == 'mysql') $bd = new MySQL(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT, DB_CHAR);
else if(DB_TYPE == 'postgresql') $bd = new PostgreSQL(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT, DB_CHAR);
else if(DB_TYPE == 'oracle') $bd = new Oracle(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT, DB_CHAR);

// crear objeto para rescatar los parametros de la base de datos
$Parametros = new Parametros();

// parametros de configuración que se encuentran relacionados con la base de la app
$Parametros->getByModulo('base');
// parametros de configuración que se encuentran relacionados con la empresa
$Parametros->getByModulo('empresa');

// activar/desactivar modo debug
if(DEBUG) {
	ini_set('display_errors', 'On');
	ini_set('error_reporting', E_ALL | E_STRICT);
} else {
	ini_set('display_errors', 'Off');
	ini_set('error_reporting', false);
}

// definicion zona horaria y fecha
date_default_timezone_set(ZONA_HORARIA); // asignar zona horaria para usar con funcion date
define('HOY', date('Y-m-d')); // fecha de hoy en el formato YYYY-MM-DD
define('FECHAHORA', date('Y-m-d H:i')); // fecha y hora

// definicion url que se esta viendo
define('AKI', $_SERVER['REQUEST_URI']); // incluye valores pasados por GET

// crear objeto usuario e inicializar sesion del usuario activo
$Usuario = new Usuario();
$Usuario->start();

// definir modulo
define('MODULO', MiSiTiO::modulo());
define('MODULO_DIR', implode('/modulos/', explode('/', MODULO)));

// cargar archivo de idioma global y de módulos (si es que este último existe)
if(file_exists(DIR.'/lang/'.$Usuario->lang.'.php')) require(DIR.'/lang/'.$Usuario->lang.'.php');
else require(DIR.'/lang/'.LANG.'.php');
if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/lang/'.$Usuario->lang.'.php')) require(DIR.'/modulos/'.MODULO_DIR.'/lang/'.$Usuario->lang.'.php');
else if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/lang/'.LANG.'.php')) require(DIR.'/modulos/'.MODULO_DIR.'/lang/'.LANG.'.php');

// verificar que el sitio este online
if(OFFLINE && $Usuario->ip()!=ADMIN_IP) MiSiTiO::error(SITE_OFFLINE);

// verificar permisos para la pagina que se esta accediendo
if($aux = strpos(AKI, '?')) define('RECURSO', substr(AKI, 0, $aux));
else define('RECURSO', AKI);
$Usuario->autorizado(RECURSO);

// incluir archivo que incluye configuraciones/parametros/funciones/menu/etc por modulos
if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/inc/mod.inc.php')) require(DIR.'/modulos/'.MODULO_DIR.'/inc/mod.inc.php');

// incluir archivo que incluye clases por modulos
if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/class/mod.class.php')) require(DIR.'/modulos/'.MODULO_DIR.'/class/mod.class.php');

// determina usuario para auditar
define('AUDIT_USUARIO', $Usuario->id);

?>
