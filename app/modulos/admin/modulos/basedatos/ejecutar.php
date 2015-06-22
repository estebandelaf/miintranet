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
 */
require('../../../../inc/web1.inc.php');

echo TAB4,'<h1>',LANG_MOD_DB_SQL_TITLE,'</h1>',"\n";

echo MiSiTiO::textbox(LANG_MOD_DB_SQL_TEXTBOX_TITLE, LANG_MOD_DB_SQL_TEXTBOX_MSG);

$sql = !empty($_POST['sql']) ? $_POST['sql'] : '';

echo Form::bForm();
echo Form::textarea(LANG_MOD_DB_SQL_FORM_QUERY, 'sql', $sql);
echo Form::resetButton();
echo Form::submitButton();
echo Form::eForm();

if(!empty($_POST['sql'])) {
	$data = $bd->select4table($sql);
	echo TAB4,'<p>',LANG_MOD_DB_SQL_ROWSFOUND,': ',(count($data)-1),'</p>',"\n";
	Tabla::$id = 'consultaSQL';
	echo Tabla::generar($data);
}

require(DIR.'/inc/web2.inc.php');

?>
