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
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MOD_SERVICE_SITEMAP_TITLE));
echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_MOD_SERVICE_SITEMAP_MSG.':'));
foreach($nav as &$modulo) {
	if($Usuario->autorizado($modulo['link'], false)) {
		// mostrar titulo del modulo
		echo TAB4,'<h2>',$modulo['name'],'</h2>',"\n";
		modulos($modulo['link']);
	}
}
require(DIR.'/inc/web2.inc.php');

/**
 * Recorre recursivamente la lista de módulos y sus submódulos generando el
 * mapa del sitio
 * @param modulo modulo que se esta viendo (enlace)
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-17
 */
function modulos($modulo) {
		global $Usuario;
		// quitar / de adelante y final del $modulo
		$modulo = trim($modulo, '/');
		// determinar directorio del modulo (primero se copia el modulo como viene)
		$modulo_dir = $modulo;
		// si ya tiene un slash el modulo significa que es un submodulo asi que se reemplazan
		$modulo_dir = str_replace('/', '/modulos/', $modulo_dir);
		// agregar directorio principal de modulos
		$modulo_dir = 'modulos/'.$modulo_dir;
		// buscar archivos de idioma del modulo
		if(file_exists(DIR.'/'.$modulo_dir.'/lang/'.$Usuario->lang.'.php'))
			include(DIR.'/'.$modulo_dir.'/lang/'.$Usuario->lang.'.php');
		else if(file_exists(DIR.'/'.$modulo_dir.'/lang/'.LANG.'.php'))
			include (DIR.'/'.$modulo_dir.'/lang/'.LANG.'.php');
		// incluir menu del modulo
		require(DIR.'/'.$modulo_dir.'/inc/nav-mod.inc.php');
		// generar menu del modulo actual
		echo TAB4,'<ul>',"\n";
		// por cada elemento se procesara el item (si es submodulo se
		// hara recursivamente)
		foreach($navModulo as &$item) {
			// se verifica que el usuario tenga permisos para el item
			if($Usuario->autorizado($item['link'], false)) {
				$descripcion = !empty($item['desc'])?': '.$item['desc']:'';
				echo TAB5,'<li>',"\n";
				echo TAB6,'<a href="/',$modulo,'/',$item['link'],'">',$item['name'],'</a>',$descripcion,"<br />\n";
				// si es submodulo se revisa recursivamente
				if($item['link'][strlen($item['link'])-1]=='/')
					modulos($modulo.'/'.$item['link']);
				echo TAB5,'</li>',"\n";
			}
		}
		echo TAB4,'</ul>',"\n";
		unset($navModulo);
}

?>
