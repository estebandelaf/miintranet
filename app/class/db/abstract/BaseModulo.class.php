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
 * BaseModulo para trabajo con un objeto de la tabla modulo
 * Módulos del sistema
 * Esta clase entrega los métodos básicos para ser extendida por Modulo
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseModulo implements Base {

	public static $bd; ///< Objeto de acceso a la base de datos
	
	// Atributos de la clase, corresponden a las columnas
	// en la tabla modulo
	public $nombre; ///< Nombre del módulo: character varying(15) NOT NULL DEFAULT '' PK 
	public $glosa; ///< Descripción del módulo: character varying(70) NOT NULL DEFAULT '' 
	public $audit_programa; ///< Programa que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_usuario; ///< Usuario que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_fechahora; ///< Fecha y hora en que se realizó la última modificación a la fila: timestamp without time zone() NOT NULL DEFAULT 'now' 


	/**
	 * Constructor de la clase BaseModulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase Modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseModulo, excepto $bd
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function clear () {
		$this->nombre = null;
		$this->glosa = null;
		$this->audit_programa = null;
		$this->audit_usuario = null;
		$this->audit_fechahora = null;

	}
	
	/**
	 * Setea los atributos del objeto BaseModulo mediante un arreglo,
	 * la key del arreglo es el nombre del atributo, si la key no existe
	 * el campo quedará seteado a null
	 * @param arreglo Arreglo con la relacion columna=>valor
	 * @param clear Flag para limpiar (o no) atributos antes de hacer el set
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function set ($arreglo, $clear = true) {
		if($clear) $this->clear();
		if(isset($arreglo['nombre']) && $arreglo['nombre']!='')
			$this->nombre = $arreglo['nombre'];
		if(isset($arreglo['glosa']) && $arreglo['glosa']!='')
			$this->glosa = $arreglo['glosa'];

	}

	/**
	 * Recupera las columnas de una fila desde la tabla modulo, seteando
	 * los valores de los atributos de la clase y además devolviendo un arreglo
	 * con dichos valores
	 * Se requiere que los campos de la PK (nombre) esten establecidos
	 * @return Array Arreglo con fila de la tabla modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function get () {
		$arrModulo = self::$bd->getFila("SELECT * FROM modulo WHERE nombre=".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL')."");
		if($arrModulo) {
			foreach($arrModulo as $key=>$value)
				$this->$key = $value;
		} else {
			$this->clear();
			$arrModulo = null;
		}
		return $arrModulo;
	}

	/**
	 * Verifica si la fila asociada a la PK (nombre) existe en la tabla modulo
	 * @return boolean =true si el objeto existe en la base de datos
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function exist () {
		return (boolean) self::$bd->getValor("SELECT COUNT(*) FROM modulo WHERE nombre=".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL')."");
	}

	/**
	 * Elimina un objeto del tipo BaseModulo de la tabla modulo
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function delete () {
		if(!$this->beforeDelete()) return false;
		self::$bd->consulta("DELETE FROM modulo WHERE nombre=".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL')."");
		if(!$this->afterDelete()) return false;
		return true;
	}
	
	/**
	 * Guarda un objeto del tipo BaseModulo en la tabla modulo
	 * Inserta si el registro no existe, en caso contrario hace un update
	 * Campos que no pueden ser null: nombre, glosa, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function save () {
		if($this->exist()) return $this->update();
		else return $this->insert();
	}

	/**
	 * Inserta un nuevo objeto del tipo BaseModulo en la tabla modulo
	 * Si hay campos definidos como auto no se utilizan en este método
	 * Campos que no pueden ser null: nombre, glosa, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function insert () {
		if(!$this->beforeInsert()) return false;
		self::$bd->consulta("
			INSERT INTO modulo (
				nombre,
				glosa
				, audit_programa
				, audit_usuario
				, audit_fechahora
			) VALUES (
				".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL').",
				".($this->glosa!==null?"'".self::$bd->proteger($this->glosa)."'":'NULL')."
				, '".AUDIT_PROGRAMA."'
				, '".AUDIT_USUARIO."'
				, NOW()
			)
		");
		if(!$this->afterInsert()) return false;
		return true;
	}

	/**
	 * Actualiza un objeto del tipo BaseModulo de la tabla modulo
	 * No es posible actualizar la PK (nombre)
	 * Campos que no pueden ser null: nombre, glosa, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function update () {
		if(!$this->beforeUpdate()) return false;
		self::$bd->consulta("
			UPDATE modulo
			SET
				glosa=".($this->glosa!==null?"'".self::$bd->proteger($this->glosa)."'":'NULL')."
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE nombre=".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL')."
		");
		if(!$this->afterUpdate()) return false;
		return true;
	}
	
	/**
	 * Se ejecuta automáticamente antes del delete
	 * Si se quiere modificar este método deberá ser redefinido en la clase final
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function beforeDelete () {
		return true;
	}

	/**
	 * Se ejecuta automáticamente después del delete
	 * Si se quiere modificar este método deberá ser redefinido en la clase final
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function afterDelete () {
		return true;
	}
	
	/**
	 * Se ejecuta automáticamente antes del insert
	 * Si se quiere modificar este método deberá ser redefinido en la clase final
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function beforeInsert () {
		return true;
	}
	
	/**
	 * Se ejecuta automáticamente después del insert
	 * Si se quiere modificar este método deberá ser redefinido en la clase final
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function afterInsert () {
		return true;
	}
	
	/**
	 * Se ejecuta automáticamente antes del update
	 * Si se quiere modificar este método deberá ser redefinido en la clase final
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function beforeUpdate () {
		return true;
	}
	
	/**
	 * Se ejecuta automáticamente después del update
	 * Si se quiere modificar este método deberá ser redefinido en la clase final
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function afterUpdate () {
		return true;
	}



}

/**
 * BaseModulos para trabajo con listado de objetos de la tabla modulo
 * Módulos del sistema
 * Esta clase entrega los métodos básicos para ser extendida por Modulos
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseModulos implements Bases {

	public static $bd; ///< Objeto de acceso a la base de datos
	private $selectStatement; ///< Columnas a consultar
	private $whereStatement; ///< Condiciones para la consula
	private $groupByStatement; ///< Campos para agrupar
	private $havingStatement; ///< Condiciones de los campos agrupados
	private $orderByStatement; ///< Orden de los resultados
	private $limitStatementRecords; ///< registros que se seleccionaran
	private $limitStatementOffset; ///< desde que fila se seleccionaran

	/**
	 * Constructor de la clase BaseModulos
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase BaseModulos
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseModulos, excepto $bd
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function clear () {
		$this->selectStatement = null;
		$this->whereStatement = null;
		$this->groupByStatement = null;
		$this->havingStatement = null;
		$this->orderByStatement = null;
		$this->limitStatementRecords = null;
		$this->limitStatementOffset = null;
	}
	
	/**
	 * Ingresa las columnas que se seleccionaran en el select
	 * No se "protege" el parametro pasado, debera hacerse eso desde fuera por cada
	 * campo del select
	 * @param selectStatement Columna/s que se desea seleccionar de la tabla modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function setSelectStatement ($selectStatement) {
		$this->selectStatement = $selectStatement;
	}

	/**
	 * Ingresa las condiciones para utilizar en el where de la consulta sql
	 * No se "protege" el parametro pasado, debera hacerse eso desde fuera por cada
	 * condición del where
	 * @param whereStatement Condiciones para el where de la consulta sql
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function setWhereStatement ($whereStatement) {
		$this->whereStatement = ' WHERE '.$whereStatement;
	}

	/**
	 * Ingresa las columnas por las que se agrupara la consulta
	 * Se "protege" el parametro pasado
	 * @param groupByStatement Columna/s por la que se desea agrupar la tabla modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function setGroupByStatement ($groupByStatement) {
		$this->groupByStatement = ' GROUP BY '.self::$bd->proteger($groupByStatement);
	}

	/**
	 * Ingresa las condiciones para utilizar en el having de la consulta sql
	 * No se "protege" el parametro pasado, debera hacerse eso desde fuera por cada
	 * condición del having
	 * @param havingStatement Condiciones para el having de la consulta sql
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function setHavingStatement ($havingStatement) {
		$this->havingStatement = ' HAVING '.$havingStatement;
	}
	
	/**
	 * Ingresa los campos por los que se deberá ordenar
	 * Se "protege" el parametro pasado
	 * @param orderByStatement Columna/s de la tabla modulo por la cual se ordenara y si es ASC o DESC
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function setOrderByStatement ($orderByStatement) {
		$this->orderByStatement = ' ORDER BY '.self::$bd->proteger($orderByStatement);
	}

	/**
	 * Ingresa las condiciones para hacer una seleccion de solo cierta cantidad de filas
	 * @param records Cantidad de filas a mostrar (mayor que 0)
	 * @param offset Desde que registro se seleccionara (default: 0)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function setLimitStatement ($records, $offset = 0) {
		if ($records > 0) {
			$this->limitStatementRecords = self::$bd->proteger($records);
			$this->limitStatementOffset = self::$bd->proteger($offset);
		}
	}

	/**
	 * Entrega la cantidad de objetos de tipo BaseModulo desde la base de datos,
	 * hará uso del whereStatement si no es null también de groupByStatement
	 * y havingStatement
	 * @return integer Cantidad de objetos encontrados
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
        final public function count () {
		$query = 'SELECT COUNT(*) FROM modulo';
		if ($this->whereStatement) $query .= $this->whereStatement;
		// en caso que se quiera usar el group by se hace una subconsulta
		if ($this->groupByStatement) {
			$query .= $this->groupByStatement;
			if ($this->havingStatement) $query .= $this->havingStatement;
			return self::$bd->getValor("SELECT COUNT(*) FROM ($query) AS t");
		}
		return self::$bd->getValor($query);
        }

	/**
	 * Entrega el valor máximo del campo solicitado,
	 * hará uso del whereStatement si no es null,
	 * @param campo Campo que se consultará
	 * @return integer Valor máximo del campo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getMax ($campo) {
		$query = 'SELECT MAX('.self::$bd->proteger($campo).') FROM modulo';
		if ($this->whereStatement) $query .= $this->whereStatement;
		return self::$bd->getValor($query);
	}

	/**
	 * Entrega el valor mínimo del campo solicitado,
	 * hará uso del whereStatement si no es null
	 * @param campo Campo que se consultará
	 * @return integer Valor mínimo del campo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getMin ($campo) {
		$query = 'SELECT MIN('.self::$bd->proteger($campo).') FROM modulo';
		if ($this->whereStatement) $query .= $this->whereStatement;
		return self::$bd->getValor($query);
        }

	/**
	 * Entrega la suma del campo solicitado,
	 * hará uso del whereStatement si no es null
	 * @param campo Campo que se consultará
	 * @return integer Valor mínimo del campo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getSum ($campo) {
		$query = 'SELECT SUM('.self::$bd->proteger($campo).') FROM modulo';
		if ($this->whereStatement) $query .= $this->whereStatement;
		return self::$bd->getValor($query);
	}
	
	/**
	 * Entrega el promedio del campo solicitado,
	 * hará uso del whereStatement si no es null
	 * @param campo Campo que se consultará
	 * @return integer Valor mínimo del campo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getAvg ($campo) {
		$query = 'SELECT AVG('.self::$bd->proteger($campo).') FROM modulo';
		if ($this->whereStatement) $query .= $this->whereStatement;
		return self::$bd->getValor($query);
	}
	
	/**
	 * Recupera objetos de tipo BaseModulo desde la tabla modulo,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Arreglo o valor según lo solicitado
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function select ($solicitado) {

		// preparar consulta inicial
		if($this->selectStatement) $query = 'SELECT '.$this->selectStatement.' FROM modulo';
		else $query = 'SELECT * FROM modulo';
		
		// agregar where
		if ($this->whereStatement) $query .= $this->whereStatement;

		// agregar group by
		if ($this->groupByStatement) $query .= $this->groupByStatement;
		
		// agregar having
		if ($this->havingStatement) $query .= $this->havingStatement;
		
		// agregar order by
		if ($this->orderByStatement) $query .= $this->orderByStatement;
		
		// agregar limit
		if ($this->limitStatementRecords) $query = self::$bd->setLimit($query, $this->limitStatementRecords, $this->limitStatementOffset);
		
		// ejecutar
		if($solicitado=='objetos' || $solicitado=='tabla') {
			$tabla = self::$bd->getTabla($query);
			if($solicitado=='objetos') {
				// procesar tabla y asignar valores al objeto
				$objetos = array();
				foreach($tabla as &$fila) {
					$objModulo = new Modulo();
					foreach($fila as $columna => $valor)
						$objModulo->$columna = $valor;
					array_push($objetos, $objModulo);
				}
				return $objetos;
			} else {
				return $tabla;
			}
		}
		else if($solicitado=='fila') return self::$bd->getFila($query);
		else if($solicitado=='columna') return self::$bd->getColumna($query);
		else if($solicitado=='valor') return self::$bd->getValor($query);
		
	}

	/**
	 * Recupera objetos de tipo BaseModulo desde la tabla modulo,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con objetos de tipo BaseModulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getObjetos () {
		return $this->select('objetos');
	}

	/**
	 * Recupera una tabla con las columnas y filas de la tabla modulo
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con filas y columnas de la tabla modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getTabla () {
		return $this->select('tabla');
	}

	/**
	 * Recupera una fila con las columnas de la tabla modulo
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con columnas de la tabla modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getFila() {
		return $this->select('fila');
	}
	
	/**
	 * Recupera una columna de la tabla modulo
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con la columna de la tabla modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getColumna() {
		return $this->select('columna');
	}
	
	/**
	 * Recupera un valor de la tabla modulo
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Valor solicitado de la tabla modulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getValor() {
		return $this->select('valor');
	}

}

?>
