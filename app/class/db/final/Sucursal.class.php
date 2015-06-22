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

if(!defined('__Sucursal__')) {
define('__Sucursal__', true);

require(DIR.'/class/db/abstract/BaseSucursal.class.php');

/**
 * Sucursal para trabajo con un objeto de la tabla sucursal
 * Sucursales y casa matriz de la empresa
 * Esta clase permite ampliar las funcionalidades provistas por BaseSucursal
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-02
 */
final class Sucursal extends BaseSucursal {

	/**
	 * Constructor de la clase Sucursal
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
	 * Destructor de la clase Sucursal
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function __destruct() {
		parent::__destruct ();
	}
	
	/**
	 * No se permtie el borrado de sucursales
	 * @return Siempre false
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final protected function beforeDelete() {
		return false;
	}

}

/**
 * Sucursals para trabajo con listado de objetos de la tabla sucursal
 * Sucursales y casa matriz de la empresa
 * Esta clase permite ampliar las funcionalidades provistas por BaseSucursals
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-02
 */
final class Sucursals extends BaseSucursals {

	/**
	 * Constructor de la clase Sucursal
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
	 * Destructor de la clase Sucursal
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla sucursal, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla sucursal
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement('id, glosa');
		$this->setOrderByStatement('glosa');
		return $this->getTabla();
	}
	
	/**
	 * Devuelve un objeto del tipo Sucursal con los datos de la casa matriz
	 * @return Sucursal objeto con los datos de la casa matriz
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-04
	 */
	final public function matriz () {
		$this->clear();
		$this->setWhereStatement('matriz=1');
		$aux = $this->getObjetos();
		return array_shift($aux);
	}
	
}

}

?>
