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

require('inc/web1.inc.php');

require(DIR.'/class/db/final/Noticia.class.php');
$objNoticias = new Noticias();
$noticias = $objNoticias->noticias();

$noticiasItem = '';
foreach($noticias as &$noticia) {
	$noticiasItem .= MiSiTiO::generar('noticias/noticiasItem.html', array(
		'id'=>$noticia['id']
		, 'titulo'=>$noticia['titulo']
		, 'cuerpo'=>str_replace("\r\n", '</p><p>', $noticia['cuerpo'])
		, 'usuario'=>$noticia['usuario']
		, 'fechahora'=>$noticia['fechahora']
	));

}
echo MiSiTiO::generar('noticias/noticias.html', array('titulo'=>LANG_NEWS_TITLE, 'noticiasItem'=>$noticiasItem));

require(DIR.'/inc/web2.inc.php');

?>
