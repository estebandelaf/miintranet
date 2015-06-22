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

$navModulo[$i]['link'] = 'productos/';
$navModulo[$i]['name'] = 'Productos';
$navModulo[$i]['desc'] = 'Listado de productos y sus categorías';
$navModulo[$i]['imag'] = 'productos.png';
++$i;

$navModulo[$i]['link'] = 'stock/';
$navModulo[$i]['name'] = 'Stock';
$navModulo[$i]['desc'] = 'Control de stock de los productos';
$navModulo[$i]['imag'] = 'stock.png';
++$i;

$navModulo[$i]['link'] = 'movimientos/';
$navModulo[$i]['name'] = 'Movimientos';
$navModulo[$i]['desc'] = 'Control del movimiento de productos: entrada y salida';
$navModulo[$i]['imag'] = 'movimiento.png';
++$i;

?>
