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

$camposRepartir = '';
if(!isset($_POST['submit'])) {
	$camposRepartir .= '<div>'.LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_ID.': <input type="txt" name="id[]" onfocus="$(this).select()" style="width:3em;" /> ';
	$camposRepartir .= LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_TIMES.': <input type="txt" name="veces[]" onfocus="$(this).select()"  style="width:2em;" /> ';
	$camposRepartir .= '<a href="#" onclick="$(this).parent().remove()">'.LANG_FORM_FROMJS_DELETE.'</a><br /></div>';
} else {
	$ids = count($_POST['id']);
	for($i=0; $i<$ids; ++$i) {
		$camposRepartir .= '<div>'.LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_ID.': <input type="txt" name="id[]" value="'.$_POST['id'][$i].'" onfocus="$(this).select()" style="width:3em;" /> ';
		$camposRepartir .= LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_TIMES.': <input type="txt" name="veces[]" value="'.$_POST['veces'][$i].'" onfocus="$(this).select()"  style="width:2em;" /> ';
		$camposRepartir .= '<a href="#" onclick="$(this).parent().remove()">'.LANG_FORM_FROMJS_DELETE.'</a><br /></div>';
	}
}

echo TAB4,'<h1>',LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_TITLE,'</h1>',"\n";

echo Form::bForm();
echo Form::fromJS(LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_FIELDS, 'repartir', 'repartirAgregar', $camposRepartir);
echo Form::submitButton(LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_SUBMIT);
echo Form::eForm();

if(isset($_POST['submit'])) {
	echo TAB4,'<p>',LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_MSG,':</p>',"\n";
	$maximo = max($_POST['veces']); // obtener el maximo numero de repeticiones que se ha indicado en el formulario
	$ids = count($_POST['id']); // contar los id que se pasaron
	for($i=1; $i<=$maximo; ++$i) { // repetir desde 1 hasta el mayor numero de veces encontrado
		for($j=0; $j<$ids; ++$j) { // recorrer la lista de id
			// si el id no se ha mostrado la cantidad de veces requerida se muestra
			if($i<=$_POST['veces'][$j]) echo TAB4,$_POST['id'][$j],'<br />',"\n";
		}
	}
}

require(DIR.'/inc/web2.inc.php');

?>
