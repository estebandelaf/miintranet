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
 * Archivo de definiciones generales
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-02
 */

// nombre del programa para auditar, para AUDIT_USUARIO ver acciones.inc.php
// fecha y hora seran tomadas mediante funcion NOW() de la base de datos
define('AUDIT_PROGRAMA', 'miintranet');

// recursos que no requieren estar logueado para verlos
define('RECURSOS_SIN_BLOQUEO', '/ /login /logout /clave /error /noticias /rss /mapa/geoposicionamiento_update');

// directorio raiz de la aplicacion
define('DIR', str_replace('\\', '/', dirname(dirname(__FILE__))));

// errores
define('DB_ERROR', 1);
define('ACCESS_DENIED', 2);
define('ACCESS_INCORRECT', 3);
define('SITE_OFFLINE', 4);

// tabuladores
define('TAB0', '');
define('TAB1', "\t");
define('TAB2', "\t\t");
define('TAB3', "\t\t\t");
define('TAB4', "\t\t\t\t");
define('TAB5', "\t\t\t\t\t");
define('TAB6', "\t\t\t\t\t\t");
define('TAB7', "\t\t\t\t\t\t\t");
define('TAB8', "\t\t\t\t\t\t\t\t");
define('TAB9', "\t\t\t\t\t\t\t\t\t");
define('TAB10', "\t\t\t\t\t\t\t\t\t\t");
define('TAB11', "\t\t\t\t\t\t\t\t\t\t\t");
define('TAB12', "\t\t\t\t\t\t\t\t\t\t\t\t");
define('TAB13', "\t\t\t\t\t\t\t\t\t\t\t\t\t");
define('TAB14', "\t\t\t\t\t\t\t\t\t\t\t\t\t\t");
define('TAB15', "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t");

?>
