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

$i=0;

$navModulo[$i]['link'] = 'generarClases';
$navModulo[$i]['name'] = LANG_MOD_DB_NAV_CLASSGENERATOR;
$navModulo[$i]['desc'] = LANG_MOD_DB_NAV_CLASSGENERATOR_DESC;
$navModulo[$i]['imag'] = 'database.png';
++$i;

$navModulo[$i]['link'] = 'generarMantenedores';
$navModulo[$i]['name'] = LANG_MOD_DB_NAV_MAINTAINERGENERATOR;
$navModulo[$i]['desc'] = LANG_MOD_DB_NAV_MAINTAINERGENERATOR_DESC;
$navModulo[$i]['imag'] = 'database.png';
++$i;

$navModulo[$i]['link'] = 'generarAuditoria';
$navModulo[$i]['name'] = LANG_MOD_DB_NAV_AUDITGENERATOR;
$navModulo[$i]['desc'] = LANG_MOD_DB_NAV_AUDITGENERATOR_DESC;
$navModulo[$i]['imag'] = 'database.png';
++$i;

$navModulo[$i]['link'] = 'ejecutar';
$navModulo[$i]['name'] = LANG_MOD_DB_NAV_SQL;
$navModulo[$i]['desc'] = LANG_MOD_DB_NAV_SQL_DESC;
$navModulo[$i]['imag'] = 'database.png';
++$i;

?>
