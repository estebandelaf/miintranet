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

if(!defined('__Grupo__')) {
define('__Grupo__', true);

require(DIR.'/class/db/abstract/BaseGrupo.class.php');

/**
 * Grupo para trabajo con un objeto de la tabla grupo
 * Perfiles del sistema para agrupar a los usuarios
 * Esta clase permite ampliar las funcionalidades provistas por BaseGrupo
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-01
 */
final class Grupo extends BaseGrupo {

	/**
	 * Constructor de la clase Grupo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-01
	 */
	final public function __construct () {
		// asignar objeto de acceso a la base de datos
		// si se quisiera utilizar otro objeto que permita el acceso
		// a otra base de datos que utiliza esta clase se cambia solo
		// en las lineas siguientes (ej: global $mysql o global $oracle)
		global $bd;
		parent::__construct ($bd);
	}
	
	/**
	 * Destructor de la clase Grupo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-01
	 */
	final public function __destruct() {
		parent::__destruct ();
	}

	/**
	 * Verifica que no existan usuarios asociados al grupo antes de borrar
	 * @return boolean =true en caso que sea posible borrar el registro
	 */
	final protected function beforeDelete() {
		require(DIR.'/class/db/final/Usuario_grupo.class.php');
		$objUsuario_grupos = new Usuario_grupos();
		$objUsuario_grupos->setWhereStatement("grupo_id = '".Usuario_grupos::$bd->proteger($this->id)."'");
		return !(boolean)$objUsuario_grupos->count();
	}
	
}

/**
 * Grupos para trabajo con listado de objetos de la tabla grupo
 * Perfiles del sistema para agrupar a los usuarios
 * Esta clase permite ampliar las funcionalidades provistas por BaseGrupos
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-01
 */
final class Grupos extends BaseGrupos {

	/**
	 * Constructor de la clase Grupo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-01
	 */
	final public function __construct () {
		// asignar objeto de acceso a la base de datos
		// si se quisiera utilizar otro objeto que permita el acceso
		// a otra base de datos que utiliza esta clase se cambia solo
		// en las lineas siguientes (ej: global $mysql o global $oracle)
		global $bd;
		parent::__construct ($bd);
	}
	
	/**
	 * Destructor de la clase Grupo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-01
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla grupo, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla grupo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-01
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement('id, glosa');
		$this->setOrderByStatement('glosa');
		return $this->getTabla();
	}
	
}

}

?>
