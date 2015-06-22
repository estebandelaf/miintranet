<?php

require('../../../../../inc/inc.php');

if(empty($_GET['query'])) exit;

switch($_GET['query']) {
	// consultar actividad economica en SII, se usa cURL
	case 'actividad_economica_id': {
		if(empty($_GET['rut'])||empty($_GET['dv'])) exit;
		$query = '
			curl -s -d "RUT='.$_GET['rut'].'&DV='.$_GET['dv'].'&PRG=STC&OPC=NOR" https://zeus.sii.cl/cvc_cgi/stc/getstc | egrep \'><TD width="70"\' | egrep -v digo | head -n 1 | awk -F \'>\' \'{print $2}\' | awk -F \'<\' \'{print $1}\'';
		exec($query, $salida);
		echo array_pop($salida);
		break;
	}
}

?>
