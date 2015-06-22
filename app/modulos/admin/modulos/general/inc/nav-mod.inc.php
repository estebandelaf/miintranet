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

$navModulo[$i]['link'] = 'modulo';
$navModulo[$i]['name'] = LANG_MOD_GENERAL_NAV_MODULE;
$navModulo[$i]['desc'] = LANG_MOD_GENERAL_NAV_MODULE_DESC;
$navModulo[$i]['imag'] = 'modulo.png';
++$i;

$navModulo[$i]['link'] = 'parametro';
$navModulo[$i]['name'] = LANG_MOD_GENERAL_NAV_PARAMETER;
$navModulo[$i]['desc'] = LANG_MOD_GENERAL_NAV_PARAMETER_DESC;
$navModulo[$i]['imag'] = 'parametro.png';
++$i;

$navModulo[$i]['link'] = 'uf';
$navModulo[$i]['name'] = 'UF';
$navModulo[$i]['desc'] = '';
$navModulo[$i]['imag'] = 'uf.png';
++$i;

$navModulo[$i]['link'] = 'actividad_economica';
$navModulo[$i]['name'] = 'Actividad económica';
$navModulo[$i]['desc'] = '';
$navModulo[$i]['imag'] = 'actividad_economica.png';
++$i;

$navModulo[$i]['link'] = 'feriado';
$navModulo[$i]['name'] = 'Feriados';
$navModulo[$i]['desc'] = 'Días feriados';
$navModulo[$i]['imag'] = 'feriado.png';
++$i;

$navModulo[$i]['link'] = 'comuna';
$navModulo[$i]['name'] = LANG_MOD_GENERAL_NAV_CITY;
$navModulo[$i]['desc'] = LANG_MOD_GENERAL_NAV_CITY_DESC;
$navModulo[$i]['imag'] = 'ciudad.png';
++$i;

$navModulo[$i]['link'] = 'region';
$navModulo[$i]['name'] = LANG_MOD_GENERAL_NAV_REGION;
$navModulo[$i]['desc'] = LANG_MOD_GENERAL_NAV_REGION_DESC;
$navModulo[$i]['imag'] = 'region.png';
++$i;

?>
