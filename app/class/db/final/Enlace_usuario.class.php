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

if(!defined('__Enlace_usuario__')) {
define('__Enlace_usuario__', true);

require(DIR.'/class/db/abstract/BaseEnlace_usuario.class.php');

/**
 * Enlace_usuario para trabajo con un objeto de la tabla enlace_usuario
 * Enlaces personales de cada usuario
 * Esta clase permite ampliar las funcionalidades provistas por BaseEnlace_usuario
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-03
 */
final class Enlace_usuario extends BaseEnlace_usuario {

	/**
	 * Constructor de la clase Enlace_usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-03
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
	 * Destructor de la clase Enlace_usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-03
	 */
	final public function __destruct() {
		parent::__destruct ();
	}
	
	/**
	 * Verifica que el usuario que quiere borrar el enlace sea quien lo creo
	 * @return boolean =true en caso que sea posible borrar el registro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-03
	 */
	final public function beforeDelete() {
		global $Usuario;
		if($this->usuario_id!=$Usuario->id) return false;
		return true;
	}
	
}

/**
 * Enlace_usuarios para trabajo con listado de objetos de la tabla enlace_usuario
 * Enlaces personales de cada usuario
 * Esta clase permite ampliar las funcionalidades provistas por BaseEnlace_usuarios
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-03
 */
final class Enlace_usuarios extends BaseEnlace_usuarios {

	/**
	 * Constructor de la clase Enlace_usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-03
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
	 * Destructor de la clase Enlace_usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-03
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla enlace_usuario, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla enlace_usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-03
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement(self::$bd->concat('usuario_id','|' ,'url').' AS id, '.self::$bd->concat('usuario_id',' - ' ,'url').' AS glosa');
		$this->setOrderByStatement('usuario_id, url');
		return $this->getTabla();
	}
	
}

}

?>
