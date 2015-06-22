<?php

/**
 * MiInTrAnEt
 * Copyright (C) 2008-2010 Esteban De La Fuente Rubio (esteban@delaf.cl)
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

require('../../inc/web1.inc.php');

echo TAB4,'<h1>',LANG_MOD_SERVICE_DIRECTORY_TITLE,'</h1>',"\n";

$objUsuarios = new Usuarios();
$directorio = $objUsuarios->directorio();

array_unshift($directorio, array(LANG_MOD_SERVICE_DIRECTORY_TABLE_USER, LANG_MOD_SERVICE_DIRECTORY_TABLE_NAME, LANG_MOD_SERVICE_DIRECTORY_TABLE_OFFICE, LANG_MOD_SERVICE_DIRECTORY_TABLE_POSITION, LANG_MOD_SERVICE_DIRECTORY_TABLE_EMAIL, LANG_MOD_SERVICE_DIRECTORY_TABLE_TELEPHONE1, LANG_MOD_SERVICE_DIRECTORY_TABLE_TELEPHONE2));

Tabla::$id = 'directorio';
echo Tabla::generar($directorio);

require(DIR.'/inc/web2.inc.php');

?>
