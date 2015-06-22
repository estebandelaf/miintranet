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

require('../../inc/web1.inc.php');

echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MOD_PROFILE_EDITPROFILE_TITLE));

// verificar que el formulario exista y aplicar cambios
if(isset($_POST['save'])) {
	
	$Usuario->savePerfil(array(
		'nombre'=>$_POST['nombre'],
		'apellido'=>$_POST['apellido'],
		'lang'=>$_POST['lang'],
		'email'=>$_POST['email'],
		'telefono1'=>$_POST['telefono1'],
		'telefono2'=>$_POST['telefono2'],
		'filasporpagina'=>$_POST['filasporpagina']
	));
	echo MiSiTiO::success('usuario');

} else {

	// mensaje de ayuda
	echo MiSiTiO::textbox(LANG_MOD_PROFILE_EDITPROFILE_TEXTBOX_TITLE, LANG_MOD_PROFILE_EDITPROFILE_TEXTBOX_MSG);
	
	// idiomas
	require(DIR.'/class/Archivo.class.php');
	$archivosLang = Archivo::examinarDirectorio(DIR.'/lang');
	$idiomas = array();
	foreach($archivosLang as $archivo) {
		if($archivo[0]!='.') {
			$idioma = substr($archivo, 0, 2);
			array_push($idiomas, array($idioma, $idioma));
		}
	}

	// formulario de edición del perfil
	echo Form::bForm('usuario', 'validarPerfil');
	echo Form::text(LANG_MOD_PROFILE_EDITPROFILE_FORM_ID, $Usuario->id);
	echo Form::text(LANG_MOD_PROFILE_EDITPROFILE_FORM_USER, $Usuario->usuario);
	echo Form::input(LANG_MOD_PROFILE_EDITPROFILE_FORM_NAME, 'nombre', $Usuario->nombre, '', 'maxlength=20');
	echo Form::input(LANG_MOD_PROFILE_EDITPROFILE_FORM_LASTNAME, 'apellido', $Usuario->apellido, '', 'maxlength=30');
	echo Form::input(LANG_MOD_PROFILE_EDITPROFILE_FORM_EMAIL, 'email', $Usuario->email, '', 'maxlength=60');
	echo Form::input(LANG_MOD_PROFILE_EDITPROFILE_FORM_TELEPHONE1, 'telefono1', $Usuario->telefono1, '', 'maxlength=20');
	echo Form::input(LANG_MOD_PROFILE_EDITPROFILE_FORM_TELEPHONE2, 'telefono2', $Usuario->telefono2, '', 'maxlength=20');
	echo Form::select(LANG_MOD_PROFILE_EDITPROFILE_FORM_LANG, 'lang', $idiomas, $Usuario->lang);
	echo Form::input(LANG_MOD_PROFILE_EDITPROFILE_FORM_ROWSPERPAGE, 'filasporpagina', $Usuario->filasporpagina, '', 'maxlength=9');
	echo Form::saveButton();
	echo Form::eForm();

	// grupos
	$grupos = $Usuario->grupos();
	echo TAB4,'<h2>',LANG_MOD_PROFILE_EDITPROFILE_GROUPS,'</h2>',"\n";
	echo TAB4,'<ul>',"\n";
	foreach($grupos as &$grupo)
		echo TAB4,'<li>',$grupo,'</li>',"\n";
	echo TAB4,'</ul>',"\n";

}
	
require(DIR.'/inc/web2.inc.php');

?>
