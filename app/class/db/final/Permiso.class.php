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

if(!defined('__Permiso__')) {
define('__Permiso__', true);

require(DIR.'/class/db/abstract/BasePermiso.class.php');

/**
 * Permiso para trabajo con un objeto de la tabla permiso
 * Relación entre grupos y recursos
 * Esta clase permite ampliar las funcionalidades provistas por BasePermiso
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-02
 */
final class Permiso extends BasePermiso {

	/**
	 * Constructor de la clase Permiso
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
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
	 * Destructor de la clase Permiso
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function __destruct() {
		parent::__destruct ();
	}
	
	final public function deletePermiso () {
		self::$bd->consulta("DELETE FROM permiso WHERE recurso=".($this->recurso!==null?"'".self::$bd->proteger($this->recurso)."'":'NULL')."");
		return true;
	}
	
	final public function grupos () {
		return self::$bd->getColumna("SELECT grupo_id FROM permiso WHERE recurso = ".($this->recurso!==null?"'".self::$bd->proteger($this->recurso)."'":'NULL')."");
	}
	
}

/**
 * Permisos para trabajo con listado de objetos de la tabla permiso
 * Relación entre grupos y recursos
 * Esta clase permite ampliar las funcionalidades provistas por BasePermisos
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-02
 */
final class Permisos extends BasePermisos {

	/**
	 * Constructor de la clase Permiso
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
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
	 * Destructor de la clase Permiso
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla permiso, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla permiso
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement(self::$bd->concat('grupo_id','|' ,'recurso').' AS id, '.self::$bd->concat('grupo_id',' - ' ,'recurso').' AS glosa');
		$this->setOrderByStatement('grupo_id, recurso');
		return $this->getTabla();
	}
	
}

}

?>
