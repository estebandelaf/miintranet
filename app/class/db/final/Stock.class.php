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

if(!defined('__Stock__')) {
define('__Stock__', true);

require(DIR.'/class/db/abstract/BaseStock.class.php');

/**
 * Stock para trabajo con un objeto de la tabla stock
 * Niveles de stock actuales
 * Esta clase permite ampliar las funcionalidades provistas por BaseStock
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-22
 */
final class Stock extends BaseStock {

	/**
	 * Constructor de la clase Stock
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-22
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
	 * Destructor de la clase Stock
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-22
	 */
	final public function __destruct() {
		parent::__destruct ();
	}
	
	/**
	 * Obtiene el nivel del stock de acuerdo a lo definido en Stock_nivel
	 * Si el stock no posee un nivel asociado se devolvera el valor por defecto
	 * Requiere que ya se haya llamado a $this->get()
	 * @param default Valor por defecto para el stock si no esta definido en el objeto Stock_nivel
	 * @return String con el nivel: critico, bajo, medio, normal, alto o exceso
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-25
	 */
	final public function getNivel($default = 'alto') {
		require(DIR.'/class/db/final/Stock_nivel.class.php');
		$objStock_nivel = new Stock_nivel();
		$objStock_nivel->set(array(
			'producto_id'=>$this->producto_id,
			'bodega_id'=>$this->bodega_id,
			'area_id'=>$this->area_id
		));
		$nivel = $default;
		// si existe una asociacion en Stock_nivel se busca
		if($objStock_nivel->exist()) {
			// obtener niveles
			$objStock_nivel->get();
			// determinar nivel de stock
			if((integer)$objStock_nivel->critico>0 && $this->nivel<=(integer)$objStock_nivel->critico) $nivel = 'critico';
			else if((integer)$objStock_nivel->bajo>0 && $this->nivel<=(integer)$objStock_nivel->bajo) $nivel = 'bajo';
			else if((integer)$objStock_nivel->medio>0 && $this->nivel<=(integer)$objStock_nivel->medio) $nivel = 'medio';
			else if((integer)$objStock_nivel->normal>0 && $this->nivel<=(integer)$objStock_nivel->normal) $nivel = 'normal';
			else if((integer)$objStock_nivel->alto>0 && $this->nivel<=(integer)$objStock_nivel->alto) $nivel = 'alto';
			else if((integer)$objStock_nivel->alto>0 && $this->nivel>(integer)$objStock_nivel->alto) $nivel = 'exceso';
		}
		return $nivel;
	}
	
}

/**
 * Stocks para trabajo con listado de objetos de la tabla stock
 * Niveles de stock actuales
 * Esta clase permite ampliar las funcionalidades provistas por BaseStocks
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-22
 */
final class Stocks extends BaseStocks {

	/**
	 * Constructor de la clase Stock
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-22
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
	 * Destructor de la clase Stock
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-22
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla stock, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla stock
	 * @todo Verificar que el método funcione y devuelva lo que se requiere
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-22
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement(self::$bd->concat('producto_id','|' ,'bodega_id','|','area_id').' AS id, '.self::$bd->concat('producto_id',' - ' ,'bodega_id',' - ','area_id').' AS glosa');
		$this->setOrderByStatement('producto_id, bodega_id, area_id');
		return $this->getTabla();
	}
	
}

}

?>
