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

/**
 * Archivo de menú principal
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-08
 */

$i=0;

$nav[$i]['link'] = '/productos/';
$nav[$i]['name'] = 'Productos';
$nav[$i]['desc'] = 'Productos y stock de los mismos';
$nav[$i]['imag'] = 'productos.png';
$nav[$i]['akey'] = '1';
++$i;

$nav[$i]['link'] = '/rrhh/';
$nav[$i]['name'] = 'RRHH';
$nav[$i]['desc'] = 'Recursos humanos y administración de personal';
$nav[$i]['imag'] = 'rrhh.png';
$nav[$i]['akey'] = '0';
++$i;

?>
