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

require('inc/web1.inc.php');

if(isset($_GET['recuperar'])) {

	if(!empty($_GET['id']) && !empty($_GET['key'])) {
		if(isset($_POST['submit']) && !empty($_POST['clave1']) && $_POST['clave1']==$_POST['clave2']) {
			// actualizar clave
			echo MiSiTiO::generar('titulo.html', array('title'=>LANG_PASSRECOVERY_TITLE));
			$objUsuario = new Usuario();
			$objUsuario->set(array('id'=>$_GET['id']));
			if($objUsuario->exist()) {
				$objUsuario->get();
				if($objUsuario->clave==$_GET['key']) {
					// actualizar clave
					$objUsuario->saveClave($_POST['clave1']);
					echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_NEWPASS_SUCCESS));
				} else {
					echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_PASSRECOVERY_ERROR_KEY));
				}
			} else {
				echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_PASSRECOVERY_ERROR_USER));
			}
		} else {
			// mostrar formulario cambio clave
			echo MiSiTiO::generar('titulo.html', array('title'=>LANG_PASSRECOVERY_TITLE));
			echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_PASSRECOVERY_MSG2));
			echo Form::bForm('/clave?recuperar&id='.$_GET['id'].'&key='.$_GET['key'], 'validarCambioClave');
			echo Form::pass(LANG_NEWPASS_FORM_PASS1, 'clave1', '', 'class="fieldRequired"');
			echo Form::pass(LANG_NEWPASS_FORM_PASS2, 'clave2', '', 'class="fieldRequired"');
			echo Form::submitButton();
			echo Form::eForm();
		}
	} else {
		if(isset($_POST['submit']) && !empty($_POST['id'])) {
			// mostrar título
			echo MiSiTiO::generar('titulo.html', array('title'=>LANG_PASSRECOVERY_TITLE));
			// verificar que usuario exista en la base de datos
			$objUsuario = new Usuario();
			$objUsuario->set(array('id'=>$_POST['id']));
			if($objUsuario->exist()) {
				$objUsuario->get();
				if(!empty($objUsuario->email)) {
					// generar key y mensajes
					$key = md5($objUsuario->clave);
					Usuario::$bd->consulta("UPDATE usuario SET clave = '$key' WHERE id = '$objUsuario->id'");
					// enviar correo con link para cambiar clave
					require(DIR.'/class/Email.class.php');
					$Email = new Email();
					$Email->para($objUsuario->nombre, $objUsuario->email);
					$Email->asunto('Recuperar contraseña');
					$Email->mensaje(MiSiTiO::generar('claveRecuperarEmail.txt', array(
						'nombre'=>$objUsuario->nombre,
						'usuario'=>$objUsuario->usuario,
						'server'=>$_SERVER['SERVER_NAME'],
						'id'=>$objUsuario->id,
						'key'=>$key,
						'date'=>FECHAHORA
					)));
					$status = $Email->enviar();
					if($status[$objUsuario->email])
						echo MiSiTiO::generar('parrafo.html', array(
							'txt'=>str_replace('{email}', $objUsuario->email, LANG_PASSRECOVERY_EMAILSENT)
						));
					else echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_PASSRECOVERY_EMAILNOTSENT));
				} else {
					echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_PASSRECOVERY_ERROR_EMAIL));
				}
			} else {
				echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_PASSRECOVERY_ERROR_USER));
			}
		} else {
			// mostrar formulario para ingresar usuario
			echo MiSiTiO::generar('titulo.html', array('title'=>LANG_PASSRECOVERY_TITLE));
			echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_PASSRECOVERY_MSG1));
			echo Form::bForm('/clave?recuperar', 'validarFormulario');
			echo Form::input(LANG_PASSRECOVERY_FORM_ID, 'id', '', '', 'class="fieldRequired"');
			echo Form::submitButton();
			echo Form::eForm();
		}
	}
	
} else {
		
	// verificar que el formulario exista y las claves ingresadas sean iguales
	if(isset($_POST['submit']) && !empty($_POST['usuario']) && !empty($_POST['clave']) && !empty($_POST['clave1']) && $_POST['clave1']==$_POST['clave2']) {
		switch(Usuario::$bd->funcion('f_usuarioSetClave', $_POST['usuario'], $_POST['clave'], $_POST['clave1'])) {
			case 1: { MiSiTiO::error(LANG_LOGIN_ERROR_USER_TITLE, LANG_LOGIN_ERROR_USER_MSG); break; } // usuario no existe
			case 2: { MiSiTiO::error(LANG_LOGIN_ERROR_PASS_TITLE, LANG_LOGIN_ERROR_PASS_MSG); break; } // clave incorrecta
			default: { $claveCambiada = LANG_NEWPASS_SUCCESS; }
		}
	} else {
		$claveCambiada = '';
	}

	echo MiSiTiO::generar('clave.html', array('titulo'=>LANG_NEWPASS_TITLE, 'msg'=>LANG_NEWPASS_MSG, 'claveCambiada'=>$claveCambiada));
	echo Form::bForm('/clave', 'validarCambioClave');
	echo Form::input(LANG_NEWPASS_FORM_USER, 'usuario', $Usuario->autorizado('', false) ? $Usuario->usuario : '', '', 'class="fieldRequired"');
	echo Form::pass(LANG_NEWPASS_FORM_PASS, 'clave', '', 'class="fieldRequired"');
	echo Form::pass(LANG_NEWPASS_FORM_PASS1, 'clave1', '', 'class="fieldRequired"');
	echo Form::pass(LANG_NEWPASS_FORM_PASS2, 'clave2', '', 'class="fieldRequired"');
	echo Form::submitButton(LANG_FORM_SAVE);
	echo Form::eForm();

}

require(DIR.'/inc/web2.inc.php');

?>
