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

/**
 * Interface para clase de base de datos
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-25
 */

Interface BD {

	public function consulta ($sql);
	public function getTabla ($par1);
        public function getFila ($par1);
        public function getColumna($par1);
        public function getValor($par1);
        public function proteger($txt);
	public function setLimit($query, $records, $offset);
	public function concat($par1, $par2);
	public function like($col, $value);
        public function select4table($sql);
        public function tocsv ($consulta, $columnas, $archivo);
	public function tablas ($database);
	public function tablaInfo ($tabla, $database);
        public function view ($view, $limit, $offset);
        public function funcion ($function, $par1);
        public function procedure ($procedure, $par1);
	public function transaction ();
	public function commit ();
	public function rollback ();

}

?>
