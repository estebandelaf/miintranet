/**
 * Busca el codigo de actividad económica de un rut en SII.cl y lo
 * coloca en el formulario en el campo actividad_economica_id
 * @param rut Rut sin puntos ni dígito verificador
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-21
 */
function actividadEconomica (rut) {
	$.get(
		'/modulos/admin/modulos/empresa/js/js.php',
		{ query: 'actividad_economica_id', rut: rut, dv: rutDV(rut) },
		function(data){
			$('#actividad_economica_idField').val(data);
		}
	);
}

if(cPage=='cliente' || cPage=='proveedor') {
	$().ready(function() {
		$('#idField').blur(function() {
			actividadEconomica($('#idField').val());
		});
	});
}

if(cPage=='noticia') {
	$().ready(tinyMceConf);
}
