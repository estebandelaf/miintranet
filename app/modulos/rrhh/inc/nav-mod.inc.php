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

$navModulo[$i]['link'] = 'usuario';
$navModulo[$i]['name'] = 'Personal';
$navModulo[$i]['desc'] = '';
$navModulo[$i]['imag'] = 'personal.png';
++$i;

$navModulo[$i]['link'] = 'cargo';
$navModulo[$i]['name'] = 'Cargos';
$navModulo[$i]['desc'] = '';
$navModulo[$i]['imag'] = 'cargo.png';
++$i;

$navModulo[$i]['link'] = 'salud';
$navModulo[$i]['name'] = 'Salud';
$navModulo[$i]['desc'] = 'Instituciones de salud: FONASA e Isapres';
$navModulo[$i]['imag'] = 'salud.png';
++$i;

$navModulo[$i]['link'] = 'afp';
$navModulo[$i]['name'] = 'AFP';
$navModulo[$i]['desc'] = 'Administradoras de fondos de pensiones';
$navModulo[$i]['imag'] = 'afp.png';
++$i;

$navModulo[$i]['link'] = 'edad';
$navModulo[$i]['name'] = 'Grupo etario';
$navModulo[$i]['desc'] = '';
$navModulo[$i]['imag'] = 'edad.png';
++$i;

?>
