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

$navModulo[$i]['link'] = 'producto';
$navModulo[$i]['name'] = 'Productos';
$navModulo[$i]['desc'] = 'Agregar, editar o eliminar productos';
$navModulo[$i]['imag'] = 'productos.png';
++$i;

$navModulo[$i]['link'] = 'producto_categoria';
$navModulo[$i]['name'] = 'Categorías';
$navModulo[$i]['desc'] = 'Categorías y sub categorías de productos';
$navModulo[$i]['imag'] = 'productos.png';
++$i;

$navModulo[$i]['link'] = 'producto_proveedor';
$navModulo[$i]['name'] = 'Proveedores de productos';
$navModulo[$i]['desc'] = 'Relación entre productos y quienes los proveen';
$navModulo[$i]['imag'] = 'productos.png';
++$i;

$navModulo[$i]['link'] = 'unidad';
$navModulo[$i]['name'] = 'Unidades';
$navModulo[$i]['desc'] = 'Unidades para los productos';
$navModulo[$i]['imag'] = 'productos.png';
++$i;

?>
