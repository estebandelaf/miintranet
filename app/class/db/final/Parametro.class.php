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

if(!defined('__Parametro__')) {
define('__Parametro__', true);

require(DIR.'/class/db/abstract/BaseParametro.class.php');

/**
 * Parametro para trabajo con un objeto de la tabla parametro
 * Parámetros de la aplicación
 * Esta clase permite ampliar las funcionalidades provistas por BaseParametro
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-02
 */
final class Parametro extends BaseParametro {

	/**
	 * Constructor de la clase Parametro
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
	 * Destructor de la clase Parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function __destruct() {
		parent::__destruct ();
	}
	
	/**
	 * No permite que se borren parametros
	 * @return Siempre false
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final protected function beforeDelete() {
		return false;
	}
}

/**
 * Parametros para trabajo con listado de objetos de la tabla parametro
 * Parámetros de la aplicación
 * Esta clase permite ampliar las funcionalidades provistas por BaseParametros
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-02
 */
final class Parametros extends BaseParametros {

	/**
	 * Constructor de la clase Parametro
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
	 * Destructor de la clase Parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla parametro, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-02
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement('parametro, '.self::$bd->concat('parametro',' - ','glosa').' AS glosa');
		$this->setOrderByStatement('parametro');
		return $this->getTabla();
	}
	
	/**
	 * Recupera todos los parametros para un módulo y los crea como constantes
	 * @param modulo Módulo al que se quieren buscar los parámetros
	 */
	final public function getByModulo ($modulo) {
		$parametros = self::$bd->getTabla("SELECT parametro, valor FROM parametro WHERE modulo_nombre = '".self::$bd->proteger($modulo)."'");
		foreach($parametros as &$parametro)
			if(!defined($parametro['parametro']))
				define($parametro['parametro'], $parametro['valor']);
	}
	
}

}

?>
