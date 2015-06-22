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

$navModulo[$i]['link'] = 'formatearRut';
$navModulo[$i]['name'] = LANG_MOD_SERVICE_UTILITIES_NAV_FORMATRUT;
$navModulo[$i]['desc'] = LANG_MOD_SERVICE_UTILITIES_NAV_FORMATRUT_DESC;
$navModulo[$i]['imag'] = 'calcular.jpg';
++$i;

$navModulo[$i]['link'] = 'extraer';
$navModulo[$i]['name'] = LANG_MOD_SERVICE_UTILITIES_NAV_EXTRACTSTR;
$navModulo[$i]['desc'] = LANG_MOD_SERVICE_UTILITIES_NAV_EXTRACTSTR_DESC;
$navModulo[$i]['imag'] = 'extraer.png';
++$i;

$navModulo[$i]['link'] = 'repartir';
$navModulo[$i]['name'] = LANG_MOD_SERVICE_UTILITIES_NAV_DISTRIBUTE;
$navModulo[$i]['desc'] = LANG_MOD_SERVICE_UTILITIES_NAV_DISTRIBUTE_DESC;
$navModulo[$i]['imag'] = 'repartir.png';
++$i;

?>
