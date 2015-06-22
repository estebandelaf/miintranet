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
 * Verifica si un objeto esta vacio
 * @param obj Objeto que se desea verificar que sea vacio
 * @return True si el objeto pasado es vacio
 * @author http://frugalcoder.us/post/2010/02/15/js-is-empty.aspx
 * @date 2011-04-17
 */
function vacio (obj) {
	if (typeof obj == 'undefined' || obj === null || obj === '') return true;
	if (typeof obj == 'number' && isNaN(obj)) return true;
	if (obj instanceof Date && isNaN(Number(obj))) return true;
	return false;
}

/**
 * Valida un formulario buscando los campos con la class fieldRequired
 * @param formulario Formulario generico que se utilizara para enviar a la aplicación
 * @return True si los campos necesarios han sido pasados
 * @author DeLaF, esteban[at]delaf.cl
 * @date 2011-04-19
 */
function validarFormulario (formulario) {
	var status = true;
	$.each($('.fieldRequired'), function(key, field) {
		if(vacio(field.value)) {
			var div = $(field).parent().parent();
			var label = $(div).children('.label').text();
			alert(label + ': '+ LANG_FORM_REQUIRED);
			field.focus();
			status = false;
			return false;
		}
	});
	return status;
}

function database (db) {
	alert('Verifique campos puerto y charset para el tipo de base de datos '+db);
}
