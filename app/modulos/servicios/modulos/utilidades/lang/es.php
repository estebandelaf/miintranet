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

/**
 * Archivo de idioma español para el módulo servicios/utilidades
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-08-03
 */

define('LANG_MOD_SERVICE_UTILITIES_TITLE', 'Utilidades');

define('LANG_MOD_SERVICE_UTILITIES_NAV_FORMATRUT', 'Formatear rut');
define('LANG_MOD_SERVICE_UTILITIES_NAV_FORMATRUT_DESC', 'Agregar o quitar puntos y dígito verificador');
define('LANG_MOD_SERVICE_UTILITIES_NAV_EXTRACTSTR', 'Extraer string');
define('LANG_MOD_SERVICE_UTILITIES_NAV_EXTRACTSTR_DESC', 'Extraer un string entre dos delimitadores');
define('LANG_MOD_SERVICE_UTILITIES_NAV_DISTRIBUTE', 'Repartir');
define('LANG_MOD_SERVICE_UTILITIES_NAV_DISTRIBUTE_DESC', 'Repartir elementos \'n\' veces');

define('LANG_MOD_SERVICE_UTILITIES_FORMATRUT_TITLE', 'Formatear rut');
define('LANG_MOD_SERVICE_UTILITIES_FORMATRUT_TEXTBOX_TITLE', 'Ejemplos');
define('LANG_MOD_SERVICE_UTILITIES_FORMATRUT_TEXTBOX_MSG', '99.888.777-6 => 99888777<br />998887776 => 99.888.777-6<br />99888777 => 99.888.777-6 (Agregar DV activado)');
define('LANG_MOD_SERVICE_UTILITIES_FORMATRUT_FORM_RUTS', 'Ruts');
define('LANG_MOD_SERVICE_UTILITIES_FORMATRUT_FORM_DV', 'Agregar DV');
define('LANG_MOD_SERVICE_UTILITIES_FORMATRUT_FORM_SUBMIT', 'Formatear');
define('LANG_MOD_SERVICE_UTILITIES_FORMATRUT_MSG', 'Ruts formateados');

define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_TITLE', 'Extraer string');
define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_TEXTBOX_TITLE', 'Nota');
define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_TEXTBOX_MSG', 'Se extraerá el texto que se encuentre entre los delimitadores.');
define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_D1', 'Delimitador inicial');
define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_D2', 'Delimitador final');
define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_TXT', 'Texto');
define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_FORM_SUBMIT', 'Extraer');
define('LANG_MOD_SERVICE_UTILITIES_EXTRACTSTR_MSG', 'Texto extraído');

define('LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_TITLE', 'Repartir');
define('LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_FIELDS', 'Campos');
define('LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_ID', 'id');
define('LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_TIMES', 'veces');
define('LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_FORM_SUBMIT', 'Repartir');
define('LANG_MOD_SERVICE_UTILITIES_DISTRIBUTE_MSG', 'Elementos repartidos');

?>
