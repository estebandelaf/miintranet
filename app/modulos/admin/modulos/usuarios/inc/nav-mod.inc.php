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
$navModulo[$i]['name'] = LANG_MOD_USERS_NAV_USER;
$navModulo[$i]['desc'] = LANG_MOD_USERS_NAV_USER_DESC;
$navModulo[$i]['imag'] = 'usuario.png';
++$i;

$navModulo[$i]['link'] = 'grupo';
$navModulo[$i]['name'] = LANG_MOD_USERS_NAV_GROUP;
$navModulo[$i]['desc'] = LANG_MOD_USERS_NAV_GROUP_DESC;
$navModulo[$i]['imag'] = 'grupo.png';
++$i;

$navModulo[$i]['link'] = 'usuario_grupo';
$navModulo[$i]['name'] = LANG_MOD_USERS_NAV_USERGROUP;
$navModulo[$i]['desc'] = LANG_MOD_USERS_NAV_USERGROUP_DESC;
$navModulo[$i]['imag'] = 'grupo.png';
++$i;

$navModulo[$i]['link'] = 'permiso';
$navModulo[$i]['name'] = LANG_MOD_USERS_NAV_PERM;
$navModulo[$i]['desc'] = LANG_MOD_USERS_NAV_PERM_DESC;
$navModulo[$i]['imag'] = 'permiso.png';
++$i;

$navModulo[$i]['link'] = 'permiso_login';
$navModulo[$i]['name'] = LANG_MOD_USERS_NAV_PERMLOGIN;
$navModulo[$i]['desc'] = LANG_MOD_USERS_NAV_PERMLOGIN_DESC;
$navModulo[$i]['imag'] = 'permiso.png';

?>
