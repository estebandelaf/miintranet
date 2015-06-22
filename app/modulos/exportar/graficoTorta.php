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

require('../../inc/inc.php');

header('Content-type: image/png');
header('Pragma: no-cache');
header('Expires: 0');

require(DIR.'/class/other/libchart/classes/libchart.php');
$chart = new PieChart(960, 400);

// quitar leyenda de ejes
$leyendas = array_shift($_SESSION['grafico']['data']);

// generar datos para el grafico
$dataSet = new XYDataSet();
foreach($_SESSION['grafico']['data'] as &$fila) {
	$x = array_shift($fila);
	$valor = array_shift($fila);
	$dataSet->addPoint(new Point($x.' ('.$valor.')', $valor));
}
$chart->setDataSet($dataSet);

$chart->setTitle($_SESSION['grafico']['titulo']);
$chart->render();

?>
