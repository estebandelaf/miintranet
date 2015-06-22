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

if(!defined('__Proveedor__')) {
define('__Proveedor__', true);

require(DIR.'/class/db/abstract/BaseProveedor.class.php');

/**
 * Proveedor para trabajo con un objeto de la tabla proveedor
 * Proveedores de la empresa
 * Esta clase permite ampliar las funcionalidades provistas por BaseProveedor
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-21
 */
final class Proveedor extends BaseProveedor {

	/**
	 * Constructor de la clase Proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-21
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
	 * Destructor de la clase Proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-21
	 */
	final public function __destruct() {
		parent::__destruct ();
	}
	
}

/**
 * Proveedors para trabajo con listado de objetos de la tabla proveedor
 * Proveedores de la empresa
 * Esta clase permite ampliar las funcionalidades provistas por BaseProveedors
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-21
 */
final class Proveedors extends BaseProveedors {

	/**
	 * Constructor de la clase Proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-21
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
	 * Destructor de la clase Proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-21
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla proveedor, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-21
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement('id, razonsocial');
		$this->setOrderByStatement('razonsocial');
		return $this->getTabla();
	}
	
}

}

?>
