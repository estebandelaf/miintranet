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
 * Archivo que incluye clases globales que están siempre en uso
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2010-07-16
 */


// definir cual es la clase de base de datos que se utilizara en la base de la app
if(DB_TYPE == 'mysql') require(DIR.'/class/db/MySQL.class.php');
else if(DB_TYPE == 'postgresql') require(DIR.'/class/db/PostgreSQL.class.php');
else if(DB_TYPE == 'oracle') require(DIR.'/class/db/Oracle.class.php');

// requeridas siempre
require(DIR.'/class/MiSiTiO.class.php');
require(DIR.'/class/db/final/Usuario.class.php');
require(DIR.'/class/db/final/Parametro.class.php');

// requeridas solo cuando se usan formularios o mostrar datos
// se incluye aqui, ya que generalmente lo que se quiere hacer es
// una de estas dos acciones en cada pagina
require(DIR.'/class/Form.class.php');
require(DIR.'/class/Tabla.class.php');

?>
