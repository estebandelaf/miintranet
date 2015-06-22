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

require(DIR.'/class/db/final/Geoposicionamiento.class.php');
$objGeoposicionamientos = new Geoposicionamientos();

$campos = !empty($_GET['campos']) ? extraerCampos($_GET['campos']) : array();

if(empty($campos['id'])) {
	echo MiSiTiO::generar('titulo.html', array('title'=>'Ver última posición'));
	echo Form::bForm(AKI, 'enviarPorUrl');
	echo Form::select('Quién/que', 'id', $objGeoposicionamientos->listado());
	echo Form::submitButton();
	echo Form::eForm();
	require(DIR.'/inc/web2.inc.php');
} else {
	$objGeoposicionamiento = new Geoposicionamiento();
	$objGeoposicionamiento->set(array('id'=>$campos['id'],));
	if($objGeoposicionamiento->exist()) {
		ob_clean();
		echo MiSiTiO::generar('geoposicionamiento.html', array(
			'id'=>$objGeoposicionamiento->id
		));
	}
}

?>
