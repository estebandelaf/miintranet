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

// verificar que el formulario exista y aplicar cambios
if(isset($_POST['save']) && isset($_FILES['avatar'])) $Usuario->saveAvatar($_FILES['avatar']);

// titulo pagina
echo MiSiTiO::generar('titulo.html', array('title'=>LANG_MOD_PROFILE_AVATAR_TITLE));

// mensaje de ayuda
echo MiSiTiO::textbox(LANG_MOD_PROFILE_AVATAR_TEXTBOX_TITLE, LANG_MOD_PROFILE_AVATAR_TEXTBOX_MSG.' '.AVATAR_SIZE_W.'x'.AVATAR_SIZE_H.'[px] '.LANG_AND.' '.AVATAR_SIZE_KB.'[KB]');

// formulario de edición del avatar
echo Form::bFormUp('avatar');
echo Form::file(LANG_MOD_PROFILE_AVATAR_FORM_AVATAR, 'avatar');
echo Form::saveButton();
echo Form::eForm();

// mostrar avatar
echo MiSiTiO::generar('avatar.html', array('id'=>$Usuario->id));

require(DIR.'/inc/web2.inc.php');

?>
