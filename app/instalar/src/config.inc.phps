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
 * Archivo global de configuración
 * En este archivo solo deberá ir la configuración para la base de
 * datos, cualquier otro parámetro debe ir en la tabla de parámetros de
 * la base de datos.
 * @author DeLaF, esteban[at]delaf.cl
 * @version {date}
 */

// parametros de acceso a la base de datos
define('DB_TYPE', '{db_type}'); // tipo de base de datos a utilizar (mysql, postgresql u oracle)
define('DB_HOST', '{db_host}'); // servidor base de datos intranet
define('DB_PORT', '{db_port}'); // nombre de la base de datos
define('DB_USER', '{db_user}'); // usuario bd
define('DB_PASS', '{db_pass}'); // clave bd
define('DB_NAME', '{db_name}'); // nombre de la base de datos
define('DB_CHAR', '{db_char}'); // juego de caracteres para la conexión a la base de datos

?>
