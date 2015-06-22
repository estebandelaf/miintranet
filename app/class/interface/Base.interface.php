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
 * Interface para la clase abstracta que trabaja con un objeto de la BD
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-26
 */
Interface Base {
	public function clear();
	public function set($arreglo);
	public function get();
	public function exist();
	public function delete();
	public function save();
}

/**
 * Interface para la clase abstracta que trabaja con un listado de objetos de la BD
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-26
 */
Interface Bases {
	public function clear();
	public function setSelectStatement($selectStatement);
	public function setWhereStatement($whereStatement);
	public function setGroupByStatement($groupByStatement);
	public function setHavingStatement($havingStatement);
	public function setOrderByStatement($orderByStatement);
	public function setLimitStatement($records, $offset);
	public function count();
	public function getMax($campo);
	public function getMin($campo);
	public function getSum($campo);
	public function getAvg($campo);
	public function getObjetos();
	public function getTabla();
	public function getFila();
	public function getColumna();
	public function getValor();
	public function listado(); // se implementa en la clase final
}

?>
