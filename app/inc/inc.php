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
 * Archivo que incluye a los archivos globales necesarios
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-02-15
 */

require('config.inc.php');
require('define.inc.php'); // aqui recien se define DIR
require(DIR.'/class/interface/interface.php');
require(DIR.'/class/class.php');
require(DIR.'/inc/funciones.inc.php');
require(DIR.'/inc/acciones.inc.php');
require(DIR.'/inc/nav.inc.php');

// incluye aspectos especificos de la aplicacion, pero que son globales en la intranet
require(DIR.'/inc/global/inc.php');

?>
