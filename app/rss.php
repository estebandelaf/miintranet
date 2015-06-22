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

require('inc/inc.php');

require(DIR.'/class/db/final/Noticia.class.php');
$objNoticias = new Noticias();
$noticias = $objNoticias->noticias();

$rssItem = '';
foreach($noticias as &$noticia) {
        $rssItem .= MiSiTiO::generar('noticias/rssItem.xml', array(
                'title'=>$noticia['titulo']
                , 'author'=>$noticia['usuario']
                , 'date'=>$noticia['fechahora']
                , 'intro'=>$noticia['resumen']
                , 'pubDate'=>fechaRSS($noticia['fechahora'])
                , 'link'=>'http://'.$_SERVER['HTTP_HOST'].'/noticias#id-'.$noticia['id']
        ));
}

echo MiSiTiO::generar('noticias/rss.xml', array(
    'titulo'=>SITE_TITLE.' / '.LANG_NEWS_TITLE
    , 'url'=> 'http://'.$_SERVER['HTTP_HOST']
    , 'lang'=>LANG
    , 'descripcion'=>SITE_TITLE
    , 'generador'=>SITE_TITLE
    , 'copyright'=>$_SERVER['HTTP_HOST']
    , 'ttl'=>NEWS_RSS_TTL
    , 'rssItem'=>$rssItem
));

?>
