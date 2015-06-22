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

/**
 * Archivo para login y bienvenida
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-15
 */

require('inc/web1.inc.php');

if(!$Usuario->logueado()) {
	echo MiSiTiO::generar('titulo.html', array('title'=>LANG_LOGIN_INDEX_TITLE));
	echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_LOGIN_INDEX_MSG));
        echo Form::bForm('login', 'validarFormulario');
        echo Form::input(LANG_LOGIN_USER, 'usuario', '', LANG_LOGIN_PASSHELP, 'class="fieldRequired"');
        echo Form::pass(LANG_LOGIN_PASS, 'clave', '', 'class="fieldRequired"');
        echo Form::submitButton(LANG_LOGIN_BUTTON);
	echo Form::text('&nbsp;', '<a href="/clave?recuperar">'.LANG_PASSRECOVERY_RECOVERY.'</a>');
        echo Form::eForm();
} else {
	// panel de informacion y noticias
	echo constant('TAB'.TAB),'<div id="info">',"\n";
	// INFO USUARIO
	echo constant('TAB'.(TAB+1)),'<div id="user">',"\n";
	echo constant('TAB'.(TAB+2)),'<img src="/rrhh/avatar" alt="" class="fright" />',"\n";
	echo constant('TAB'.(TAB+2)),'<strong>',LANG_INFO_ID,'</strong>:<br />',rut($Usuario->id.rutDV($Usuario->id)),'<br />',"\n";
	echo constant('TAB'.(TAB+2)),'<strong>',LANG_INFO_USER,'</strong>:<br />',$Usuario->usuario,'<br />',"\n";
	echo constant('TAB'.(TAB+2)),'<strong>',LANG_INFO_IP,'</strong>:<br />',$Usuario->ip(),"\n";
	echo constant('TAB'.(TAB+1)),'</div>',"\n";
	echo constant('TAB'.(TAB+1)),'<div class="clear"></div>',"\n";
	// NOTICIAS
	require(DIR.'/class/db/final/Noticia.class.php');
	$objNoticias = new Noticias();
	$noticias = $objNoticias->noticias(NEWS_LIMIT);
	if(count($noticias)) {
		echo constant('TAB'.(TAB+1)),'<div id="news">',"\n";
		echo constant('TAB'.(TAB+2)),'<h1>',LANG_INFO_NEWS,'</h1>',"\n";
		foreach($noticias as &$noticia) {
			echo constant('TAB'.(TAB+2)),'<h2>',$noticia['titulo'],'</h2>',"\n";
			echo constant('TAB'.(TAB+2)),'<p>',$noticia['resumen'],'</p>',"\n";
			echo constant('TAB'.(TAB+2)),'<div class="infoOfNews"><a href="/noticias#id-',$noticia['id'],'">'.LANG_INFO_NEWS_READMORE.'</a> / ',$noticia['usuario'],' / ',$noticia['fechahora'],'</div>',"\n";
		}
		echo constant('TAB'.(TAB+2)),'<div class="center"><a href="/noticias">',LANG_INFO_NEWS_VIEWALL,'</a></div>',"\n";
		echo constant('TAB'.(TAB+1)),'</div>',"\n";
	}
	// CUMPLEAÑOS
	$objUsuarios = new Usuarios();
	echo constant('TAB'.(TAB+1)),'<div class="clear"></div>',"\n";
	echo constant('TAB'.(TAB+1)),'<div id="birthday">',"\n";
	echo constant('TAB'.(TAB+2)),'<h1>',LANG_INFO_BIRTHDAY,'</h1>',"\n";
	foreach($objUsuarios->cumpleanios() as $cumpleanio) {
		list($mes, $dia) = explode('-', $cumpleanio['fecha']);
		echo constant('TAB'.(TAB+1)),'<li>',$dia,' ',mes($mes,true),': ',$cumpleanio['nombre'],'</li>',"\n";
	}
	echo constant('TAB'.(TAB+1)),'</div>',"\n";

	echo constant('TAB'.(TAB+1)),'<br /><br /><br />',"\n";
	echo constant('TAB'.TAB),'</div>',"\n";
	// iconos de navegación
	echo MiSiTiO::iconos(LANG_LOGIN_INDEX_TITLE, $nav);
}

require(DIR.'/inc/web2.inc.php');

?>
