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

require('../../../../inc/web1.inc.php');

echo TAB4,'<h1>',LANG_MOD_SERVICE_UTILITIES_FORMATRUT_TITLE,'</h1>',"\n";

echo MiSiTiO::textbox(LANG_MOD_SERVICE_UTILITIES_FORMATRUT_TEXTBOX_TITLE, LANG_MOD_SERVICE_UTILITIES_FORMATRUT_TEXTBOX_MSG);

echo Form::bForm();
echo Form::textarea(LANG_MOD_SERVICE_UTILITIES_FORMATRUT_FORM_RUTS, 'ruts', isset($_POST['ruts'])?$_POST['ruts']:'');
echo Form::checkbox(LANG_MOD_SERVICE_UTILITIES_FORMATRUT_FORM_DV, 'agregarDV', isset($_POST['agregarDV'])?1:0);
echo Form::submitButton(LANG_MOD_SERVICE_UTILITIES_FORMATRUT_FORM_SUBMIT);
echo Form::eForm();

// solo si se ha enviado el formulario procesar lo siguiente
if(isset($_POST['submit'])) {
	echo TAB4,'<p>',LANG_MOD_SERVICE_UTILITIES_FORMATRUT_MSG,':</p>',"\n";
	$ruts = lineas2array($_POST['ruts']);
	foreach($ruts as $rut) { // por cada rut que se ha pasado por el formulario
		if(!empty($rut)) { // solo aquellos que no sean vacios
			if(isset($_POST['agregarDV'])) $rut .= rutDV($rut); // agregar dv si se requiere
			echo TAB4,rut($rut),'<br />',"\n"; // mostrar rut formateado
		}
	}
}

require(DIR.'/inc/web2.inc.php');

?>
