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

// current ṕath and current page
var cPath = window.location.pathname;
var cPage = cPath.substring(cPath.lastIndexOf('/') + 1);

/**
 * Verifica si un objeto esta vacio
 * @param obj Objeto que se desea verificar que sea vacio
 * @return True si el objeto pasado es vacio
 * @author http://frugalcoder.us/post/2010/02/15/js-is-empty.aspx
 * @version 2011-04-17
 */
function vacio (obj) {
	if (typeof obj == 'undefined' || obj === null || obj === '') return true;
	if (typeof obj == 'number' && isNaN(obj)) return true;
	if (obj instanceof Date && isNaN(Number(obj))) return true;
	return false;
}

/**
 * Muestra un dialogo de confirmacion
 * @return True si se hace click en aceptar en el dialogo
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-05-23
 */
function estaSeguro() {
	if(confirm(LANG_FORM_SURE)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Confirma el borrado de un usuario
 * @param url Url indicando el archivo donde se enviara la solicitud de borrado
 * @param pk PK de la fila que se desea eliminar
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-28
 */
function eliminar (url, pk) {
	if(confirm(LANG_DELETE+"\n"+pk.replace('&', "\n"))) {
		document.location.href = url+'?delete&'+pk;
	}
}

/**
 * Envia un formulario buscar
 * @param formulario Formulario generico que se utilizara para enviar elementos de busqueda
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-29
 */
function buscar (formulario) {
	var total = formulario.elements.length;
	var search = new Array();
	for (i=0; i<total; ++i) {
		campo = formulario.elements[i].name;
		valor = formulario.elements[i].value;
		if(campo=='mantenedorLink') var link = valor;
		else if(!vacio(valor)) search.push(campo+'|'+valor);
	}
	search = search.join(",");
	if(vacio(search)) document.location.href= link;
	else document.location.href= link+'&search='+search;
	return false;
}

/**
 * Valida un formulario buscando los campos con la class fieldRequired
 * @param formulario Formulario generico que se utilizara para enviar a la aplicación
 * @return True si los campos necesarios han sido pasados
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-19
 */
function validarFormulario (formulario) {
	var status = true;
	$.each($('.fieldRequired'), function(key, field) {
		if(vacio(field.value)) {
			var div = $(field).parent().parent();
			var label = $(div).children('.label').text();
			//alert(label + ': '+ LANG_FORM_REQUIRED);
			alert(label.replace('*', '') + ': '+ LANG_FORM_REQUIRED);
			field.focus();
			status = false;
			return false;
		}
	});
	return status;
}

/**
 * Verificar que los campos para cambio de clave se hayan pasado
 * @param formulario formulario web que se revisará
 * @return true si se han ingresado los campos necesarios para el cambio de clave
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-20
 */
function validarCambioClave (formulario) {
	if(!validarFormulario(formulario)) {
		return false;
	}
	if(formulario.clave1.value != formulario.clave2.value) {
		alert(LANG_NEWPASS_ERROR_PASSDONTMATCH);
		formulario.clave1.value = '';
		formulario.clave2.value = '';
		formulario.clave1.focus();
		return false;
	}
	return true;
}

/**
 * Genera una nueva ventana, a modo de popup
 * @param url
 * @param ancho
 * @param alto
 * @param scroll
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-05-23
 */
function popup(url, ancho, alto, scroll) {
	window.open(
		url,
		window,
		'width='+ancho+',height='+alto+',directories=no,location=no,menubar=no,scrollbars='+scroll+',status=no,toolbar=no,resizable=no'
	);
}

/**
 * Permite mostrar/ocultar las paginas con tabs
 * @param id
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-12-15
 */
function tabMenu (id) {
	$('.tabs').css('visibility', 'hidden');
	$('.tabs').css('display', 'none');
	$('#tabmenu a').removeClass('on');
	$('#'+id+'Tab').addClass('on');
	$('#'+id).css('visibility', 'visible');
	$('#'+id).css('display', 'inline');
}

/**
 * Obtiene el dígito verificador a partir del rut sin este
 * @param numero Rut sin puntos ni digito verificador
 * @return char dígito verificador del rut ingresado
 * @author http://estebanfuentealba.wordpress.com/2009/09/25/comprobar-rut-digito-verificador-javascript/
 * @version 2011-04-21
 */
function rutDV (numero) {
	nuevo_numero = numero.toString().split("").reverse().join("");
	for(i=0,j=2,suma=0; i < nuevo_numero.length; i++, ((j==7) ? j=2 : j++)) {
		suma += (parseInt(nuevo_numero.charAt(i)) * j);
	}
	n_dv = 11 - (suma % 11);
	return ((n_dv == 11) ? 0 : ((n_dv == 10) ? "K" : n_dv));
}

/**
 * Configuración para TinyMCE estándar del sitio
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-29
 */
function tinyMceConf () {
	$('textarea').tinymce({
		script_url : '/js/tiny_mce/tiny_mce.js',
		theme : 'advanced',
		plugins : 'autolink,advimage,emotions,fullscreen',
		theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup',
		theme_advanced_buttons2 : 'formatselect,|,image,charmap,emotions,|,fullscreen,|,bullist,numlist',
		theme_advanced_buttons3 : '',
		theme_advanced_buttons4 : '',
		theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_statusbar_location : 'bottom',
		theme_advanced_resizing : false
	});
}

/**
 * Desactiva un campo de formulario
 * Dependiendo de si valor es o no vacio se habilitara o desabilitara
 * el campo del formulario
 * @param campo Nombre del campo a deshabilitar/habilitar
 * @param valor Valor del campo que controlara la deshabilitación
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-01
 */
function disableField (campo, valor) {
	if(vacio(valor)) {
		$('#'+campo+'Field').attr('disabled', false);
	} else {
		$('#'+campo+'Field').attr('disabled', true);
	}
}

/**
 * Redirecciona hacia una página web
 * Si existe el div seconds, se pondrá en él el valor actual de los
 * segundos que quedan para el redireccionamiento.
 * @param url Dirección url a donde redireccionar
 * @param seconds Segundos antes de redireccionar
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-01
 */
function redirect (url, seconds) {
	if(seconds>0) {
		$("#seconds").text(seconds+' [s]');
		--seconds;
		setTimeout("redirect('"+url+"',"+seconds+")", 1000);
	} else {
		document.location.href = url;
	}
}

/**
 * Envía los campos de un formulario mediante una sola variable 
 * (llamada campos) en la url
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-01
 */
function enviarPorUrl (formulario) {
	var total = formulario.elements.length;
	var campos = new Array();
	for (i=0; i<total; ++i) {
		campo = formulario.elements[i].name;
		valor = formulario.elements[i].value;
		if(!vacio(valor) && campo!='submit') campos.push(campo+'|'+valor);
	}
	campos = campos.join(",");
	document.location.href= cPage+'?campos='+campos;
	return false;
}

/**
 * Implementación sincrónica de $.getJSON, esto para poder recuperar
 * el objeto json fuera de la funcion que se ejecuta en success
 * @param url
 * @param data
 * @return json
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-02
 */
function getJSON (url, data) {
	var json;
	$.ajax({
		type: 'GET',
		url: url,
		dataType: 'json',
		success: function (data) {json = data;},
		data: data,
		async: false
	});
	return json;
}
