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

require('../../inc/web1.inc.php');

// clases para trabajar con los enlaces
require(DIR.'/class/db/final/Enlace.class.php');
require(DIR.'/class/db/final/Enlace_categoria.class.php');

echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MOD_SERVICE_LINKS_TITLE));

$objEnlace_categorias = new Enlace_categorias();
$objEnlace_categorias->setOrderByStatement('orden, nombre');
$categorias = $objEnlace_categorias->getObjetos();
foreach($categorias as &$categoria) {
	echo TAB4.'<h2>',$categoria->nombre,'</h2>',"\n";
	$objEnlaces = new Enlaces();
	$objEnlaces->setWhereStatement("enlace_categoria_id='".$categoria->id."'");
	$objEnlaces->setOrderByStatement('nombre');
	$enlaces = $objEnlaces->getObjetos();
	echo TAB4,'<ul>',"\n";
	foreach($enlaces as &$enlace) {
		echo TAB5,'<li><a href="',$enlace->url,'">',$enlace->nombre,'</a></li>',"\n";
	}
	echo TAB4,'</ul>',"\n";
}

require(DIR.'/inc/web2.inc.php');

?>
