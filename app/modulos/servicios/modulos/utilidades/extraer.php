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

echo TAB4,'<h1>',LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_TITLE,'</h1>',"\n";

echo MiSiTiO::textbox(LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_TEXTBOX_TITLE, LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_TEXTBOX_MSG);

echo Form::bForm();
echo Form::input(LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_D1, 'd1', isset($_POST['d1'])?$_POST['d1']:'');
echo Form::input(LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_D2, 'd2', isset($_POST['d2'])?$_POST['d2']:'');
echo Form::textarea(LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_TXT, 'texto', isset($_POST['texto'])?$_POST['texto']:'');
echo Form::submitButton(LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_SUBMIT);
echo Form::eForm();

// solo si se ha enviado el formulario procesar lo siguiente
if(isset($_POST['submit'])) {
	echo TAB4,'<p>',LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_MSG,':</p>',"\n";
	$lineas = lineas2array($_POST['texto']);
	foreach($lineas as $linea) {
		if(!empty($linea)) {
			$i = strpos($linea, $_POST['d1']);
			$j = strrpos($linea, $_POST['d2']);
			if($i!==false && $j!==false) echo TAB4,substr($linea, $i+1, ($j-$i-1)),'<br />',"\n";
			else echo TAB4,'<br />',"\n";
		}
	}
}

require(DIR.'/inc/web2.inc.php');

?>
