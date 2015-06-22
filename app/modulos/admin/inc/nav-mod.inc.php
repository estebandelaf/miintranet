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

$i=0;

$navModulo[$i]['link'] = 'usuarios/';
$navModulo[$i]['name'] = LANG_MOD_ADMIN_NAV_USER;
$navModulo[$i]['desc'] = LANG_MOD_ADMIN_NAV_USER_DESC;
$navModulo[$i]['imag'] = 'usuario.png';
++$i;

$navModulo[$i]['link'] = 'empresa/';
$navModulo[$i]['name'] = LANG_MOD_ADMIN_NAV_COMPANY;
$navModulo[$i]['desc'] = LANG_MOD_ADMIN_NAV_COMPANY_DESC;
$navModulo[$i]['imag'] = 'empresa.png';
++$i;

$navModulo[$i]['link'] = 'enlaces/';
$navModulo[$i]['name'] = LANG_MOD_ADMIN_NAV_LINK;
$navModulo[$i]['desc'] = LANG_MOD_ADMIN_NAV_LINK_DESC;
$navModulo[$i]['imag'] = 'enlaces.png';
++$i;

$navModulo[$i]['link'] = 'general/';
$navModulo[$i]['name'] = LANG_MOD_ADMIN_NAV_GENERAL;
$navModulo[$i]['desc'] = LANG_MOD_ADMIN_NAV_GENERAL_DESC;
$navModulo[$i]['imag'] = 'general.png';
++$i;

$navModulo[$i]['link'] = 'estadisticas/';
$navModulo[$i]['name'] = LANG_MOD_ADMIN_NAV_STATISTICS;
$navModulo[$i]['desc'] = LANG_MOD_ADMIN_NAV_STATISTICS_DESC;
$navModulo[$i]['imag'] = 'estadisticas.png';
++$i;

$navModulo[$i]['link'] = 'basedatos/';
$navModulo[$i]['name'] = LANG_MOD_ADMIN_NAV_DB;
$navModulo[$i]['desc'] = LANG_MOD_ADMIN_NAV_DB_DESC;
$navModulo[$i]['imag'] = 'database.png';
++$i;

?>
