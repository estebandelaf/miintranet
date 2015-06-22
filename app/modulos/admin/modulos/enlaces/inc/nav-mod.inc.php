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

$navModulo[$i]['link'] = 'enlace';
$navModulo[$i]['name'] = LANG_MOD_LINK_NAV_LINK;
$navModulo[$i]['desc'] = LANG_MOD_LINK_NAV_LINK_DESC;
$navModulo[$i]['imag'] = 'enlaces.png';
++$i;

$navModulo[$i]['link'] = 'enlace_categoria';
$navModulo[$i]['name'] = LANG_MOD_LINK_NAV_CATEGORY;
$navModulo[$i]['desc'] = LANG_MOD_LINK_NAV_CATEGORY_DESC;
$navModulo[$i]['imag'] = 'enlaces.png';
++$i;

$navModulo[$i]['link'] = 'enlace_usuario';
$navModulo[$i]['name'] = 'Enlaces de usuarios';
$navModulo[$i]['desc'] = 'Enlaces personales definidos por los usuarios';
$navModulo[$i]['imag'] = 'enlaces.png';
++$i;

?>
