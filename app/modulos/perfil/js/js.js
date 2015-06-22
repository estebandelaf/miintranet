/**
 * \brief Verificar formulario del perfil
 * \param formulario formulario web que se revisará
 * \return true si los campos requeridos estan ingresados
 */
function validarPerfil (formulario) {
	if(vacio(formulario.nombre.value)) {
		alert(LANG_MOD_PROFILE_FORM_ERROR_NAME);
		formulario.nombre.focus();
		return false;
	}
	if(vacio(formulario.apellido.value)) {
		alert(LANG_MOD_PROFILE_FORM_ERROR_LASTNAME);
		formulario.apellido.focus();
		return false;
	}
	if(vacio(formulario.lang.value)) {
		alert(LANG_MOD_PROFILE_FORM_ERROR_LANG);
		formulario.lang.focus();
		return false;
	}
	return true;
}
