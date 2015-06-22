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

$navModulo[$i]['link'] = 'cliente';
$navModulo[$i]['name'] = LANG_MOD_COMPANY_NAV_CUSTOMER;
$navModulo[$i]['desc'] = LANG_MOD_COMPANY_NAV_CUSTOMER_DESC;
$navModulo[$i]['imag'] = 'cliente.png';
++$i;

$navModulo[$i]['link'] = 'proveedor';
$navModulo[$i]['name'] = LANG_MOD_COMPANY_NAV_SUPPLIER;
$navModulo[$i]['desc'] = LANG_MOD_COMPANY_NAV_SUPPLIER_DESC;
$navModulo[$i]['imag'] = 'proveedor.png';
++$i;

$navModulo[$i]['link'] = 'transportista';
$navModulo[$i]['name'] = 'Transportistas';
$navModulo[$i]['desc'] = 'Transportistas internos o externos';
$navModulo[$i]['imag'] = 'transportista.png';
++$i;

$navModulo[$i]['link'] = 'sucursal';
$navModulo[$i]['name'] = LANG_MOD_COMPANY_NAV_OFFICE;
$navModulo[$i]['desc'] = LANG_MOD_COMPANY_NAV_OFFICE_DESC;
$navModulo[$i]['imag'] = 'sucursal.png';
++$i;

$navModulo[$i]['link'] = 'area';
$navModulo[$i]['name'] = 'Área';
$navModulo[$i]['desc'] = 'Áreas de la empresa';
$navModulo[$i]['imag'] = 'area.png';
++$i;

$navModulo[$i]['link'] = 'noticia';
$navModulo[$i]['name'] = LANG_MOD_COMPANY_NAV_NEWS;
$navModulo[$i]['desc'] = LANG_MOD_COMPANY_NAV_NEWS_DESC;
$navModulo[$i]['imag'] = 'noticia.png';
++$i;

?>
