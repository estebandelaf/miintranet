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

require('../../../../inc/web1.inc.php');

echo TAB4,'<h1>',LANG_MOD_STATISTICS_LASTACCESS_TITLE,'</h1>',"\n";
$objUsuarios = new Usuarios();
$lastaccess = $objUsuarios->ultimoAcceso();
array_unshift($lastaccess, array(LANG_MOD_STATISTICS_LASTACCESS_TABLE_USER, LANG_MOD_STATISTICS_LASTACCESS_TABLE_DATETIME, LANG_MOD_STATISTICS_LASTACCESS_TABLE_WEB));
Tabla::$id = 'ultimoAcceso';
echo Tabla::generar($lastaccess);

require(DIR.'/inc/web2.inc.php');

?>
