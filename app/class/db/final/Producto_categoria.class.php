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

if(!defined('__Producto_categoria__')) {
define('__Producto_categoria__', true);

require(DIR.'/class/db/abstract/BaseProducto_categoria.class.php');

/**
 * Producto_categoria para trabajo con un objeto de la tabla producto_categoria
 * Categorías y sub categorías para clasificar productos
 * Esta clase permite ampliar las funcionalidades provistas por BaseProducto_categoria
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-22
 */
final class Producto_categoria extends BaseProducto_categoria {

	/**
	 * Constructor de la clase Producto_categoria
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
	 * Destructor de la clase Producto_categoria
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-22
	 */
	final public function __destruct() {
		parent::__destruct ();
	}
	
}

/**
 * Producto_categorias para trabajo con listado de objetos de la tabla producto_categoria
 * Categorías y sub categorías para clasificar productos
 * Esta clase permite ampliar las funcionalidades provistas por BaseProducto_categorias
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-22
 */
final class Producto_categorias extends BaseProducto_categorias {

	/**
	 * Constructor de la clase Producto_categoria
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
	 * Destructor de la clase Producto_categoria
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-22
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla producto_categoria, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla producto_categoria
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-23
	 */
	final public function listado ($categoria = '') {
		// obtener categorias principales (sin categoria padre)
		$this->clear();
		$this->setSelectStatement('id, glosa');
		if(empty($categoria)) $this->setWhereStatement("producto_categoria_id IS NULL");
		else $this->setWhereStatement("producto_categoria_id = '".self::$bd->proteger($categoria)."'");
		$this->setOrderByStatement('glosa');
		$categoriasAux = $this->getTabla();
		// procesar cada categoria para ir obteniendo hijos
		$categorias = array();
		foreach($categoriasAux as &$categoria) {
			// agregar categoria padre al arreglo categorias
			array_push($categorias, $categoria);
			$this->buscarCategoriasHijas($categoria['id'], $categorias, 1);
		}
		return $categorias;
	}

	/**
	 * Buscar recursivamente las categorias hijas de una categoria
	 * @param categoria ID de la categoria a la que se buscan hijos
	 * @param categorias Arreglo donde se esta guardando el listado
	 * @param nivel Nivel en el que se esta buscando (para indentar glosa)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-23
	 */
	final private function buscarCategoriasHijas ($categoria, &$categorias, $nivel) {
		// realizar consulta por categorias hijas
		$this->clear();
		$this->setSelectStatement('id, glosa');
		$this->setWhereStatement("producto_categoria_id = '".$categoria."'");
		$this->setOrderByStatement('glosa');
		$categoriasHijas = $this->getTabla();
		// determinar espacios para el nivel
		$espacios = '';
		for($i=0; $i<$nivel; $i++) $espacios .= '&nbsp;&nbsp;&nbsp;';
		// agregar categorias hijas al arreglo categorias
		foreach($categoriasHijas as &$categoria) {
			$categoria['glosa'] = $espacios.$categoria['glosa'];
			array_push($categorias, $categoria);
			$this->buscarCategoriasHijas($categoria['id'], $categorias, $nivel+1);
		}
	}
	
	/**
	 * Busca toda la familia de categorías a partir de la padre
	 * @param categoriaPadre ID de la categoría padre
	 * @return array Listado de categorías (incluyendo la padre)
	 */
	final public function getFamilia ($categoriaPadre) {
		$familia = array();
		$categorias = $this->listado($categoriaPadre);
		foreach($categorias as &$categoria)
			array_push($familia, $categoria['id']);
		array_unshift($familia, $categoriaPadre);
		return $familia;
	}
	
}

}

?>
