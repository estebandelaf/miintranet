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

/**
 * \brief Agregar input para el formulario
 * \author DeLaF, esteban[at]delaf.cl
 * \date 2010-07-21
 */
function repartirAgregar () {
	$("#repartir").append(
	'<div>'+LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_ID+': <input type="txt" name="id[]" onfocus="$(this).select()" style="width:3em;" /> '+
	LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_TIMES+': <input type="txt" name="veces[]" onfocus="$(this).select()"  style="width:2em;" /> '+
	'<a href="#" onclick="$(this).parent().remove()">'+LANG_FORM_FROMJS_DELETE+'</a><br /></div>'
	);
}