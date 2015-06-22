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
 * BaseParametro para trabajo con un objeto de la tabla parametro
 * Parámetros de la aplicación
 * Esta clase entrega los métodos básicos para ser extendida por Parametro
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseParametro implements Base {

	public static $bd; ///< Objeto de acceso a la base de datos
	
	// Atributos de la clase, corresponden a las columnas
	// en la tabla parametro
	public $parametro; ///< Nombre del parámetro en mayúsculas, sin espacios ni caracteres especiales: character varying(30) NOT NULL DEFAULT '' PK 
	public $valor; ///< Valor del parámetro (int, string, etc): character varying(60) NOT NULL DEFAULT '' 
	public $descripcion; ///< Descripción del parámetro: character varying(100) NOT NULL DEFAULT '' 
	public $modulo_nombre; ///< Módulo del sistema al que pertenece el parámetro: character varying(15) NOT NULL DEFAULT '' FK:modulo.nombre
	public $audit_programa; ///< Programa que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_usuario; ///< Usuario que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_fechahora; ///< Fecha y hora en que se realizó la última modificación a la fila: timestamp without time zone() NOT NULL DEFAULT 'now' 


	/**
	 * Constructor de la clase BaseParametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase Parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseParametro, excepto $bd
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function clear () {
		$this->parametro = null;
		$this->valor = null;
		$this->descripcion = null;
		$this->modulo_nombre = null;
		$this->audit_programa = null;
		$this->audit_usuario = null;
		$this->audit_fechahora = null;

	}
	
	/**
	 * Setea los atributos del objeto BaseParametro mediante un arreglo,
	 * la key del arreglo es el nombre del atributo, si la key no existe
	 * el campo quedará seteado a null
	 * @param arreglo Arreglo con la relacion columna=>valor
	 * @param clear Flag para limpiar (o no) atributos antes de hacer el set
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function set ($arreglo, $clear = true) {
		if($clear) $this->clear();
		if(isset($arreglo['parametro']) && $arreglo['parametro']!='')
			$this->parametro = $arreglo['parametro'];
		if(isset($arreglo['valor']) && $arreglo['valor']!='')
			$this->valor = $arreglo['valor'];
		if(isset($arreglo['descripcion']) && $arreglo['descripcion']!='')
			$this->descripcion = $arreglo['descripcion'];
		if(isset($arreglo['modulo_nombre']) && $arreglo['modulo_nombre']!='')
			$this->modulo_nombre = $arreglo['modulo_nombre'];

	}

	/**
	 * Recupera las columnas de una fila desde la tabla parametro, seteando
	 * los valores de los atributos de la clase y además devolviendo un arreglo
	 * con dichos valores
	 * Se requiere que los campos de la PK (parametro) esten establecidos
	 * @return Array Arreglo con fila de la tabla parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function get () {
		$arrParametro = self::$bd->getFila("SELECT * FROM parametro WHERE parametro=".($this->parametro!==null?"'".self::$bd->proteger($this->parametro)."'":'NULL')."");
		if($arrParametro) {
			foreach($arrParametro as $key=>$value)
				$this->$key = $value;
		} else {
			$this->clear();
			$arrParametro = null;
		}
		return $arrParametro;
	}

	/**
	 * Verifica si la fila asociada a la PK (parametro) existe en la tabla parametro
	 * @return boolean =true si el objeto existe en la base de datos
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function exist () {
		return (boolean) self::$bd->getValor("SELECT COUNT(*) FROM parametro WHERE parametro=".($this->parametro!==null?"'".self::$bd->proteger($this->parametro)."'":'NULL')."");
	}

	/**
	 * Elimina un objeto del tipo BaseParametro de la tabla parametro
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function delete () {
		if(!$this->beforeDelete()) return false;
		self::$bd->consulta("DELETE FROM parametro WHERE parametro=".($this->parametro!==null?"'".self::$bd->proteger($this->parametro)."'":'NULL')."");
		if(!$this->afterDelete()) return false;
		return true;
	}
	
	/**
	 * Guarda un objeto del tipo BaseParametro en la tabla parametro
	 * Inserta si el registro no existe, en caso contrario hace un update
	 * Campos que no pueden ser null: parametro, valor, descripcion, modulo_nombre, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function save () {
		if($this->exist()) return $this->update();
		else return $this->insert();
	}

	/**
	 * Inserta un nuevo objeto del tipo BaseParametro en la tabla parametro
	 * Si hay campos definidos como auto no se utilizan en este método
	 * Campos que no pueden ser null: parametro, valor, descripcion, modulo_nombre, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function insert () {
		if(!$this->beforeInsert()) return false;
		self::$bd->consulta("
			INSERT INTO parametro (
				parametro,
				valor,
				descripcion,
				modulo_nombre
				, audit_programa
				, audit_usuario
				, audit_fechahora
			) VALUES (
				".($this->parametro!==null?"'".self::$bd->proteger($this->parametro)."'":'NULL').",
				".($this->valor!==null?"'".self::$bd->proteger($this->valor)."'":'NULL').",
				".($this->descripcion!==null?"'".self::$bd->proteger($this->descripcion)."'":'NULL').",
				".($this->modulo_nombre!==null?"'".self::$bd->proteger($this->modulo_nombre)."'":'NULL')."
				, '".AUDIT_PROGRAMA."'
				, '".AUDIT_USUARIO."'
				, NOW()
			)
		");
		if(!$this->afterInsert()) return false;
		return true;
	}

	/**
	 * Actualiza un objeto del tipo BaseParametro de la tabla parametro
	 * No es posible actualizar la PK (parametro)
	 * Campos que no pueden ser null: parametro, valor, descripcion, modulo_nombre, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function update () {
		if(!$this->beforeUpdate()) return false;
		self::$bd->consulta("
			UPDATE parametro
			SET
				valor=".($this->valor!==null?"'".self::$bd->proteger($this->valor)."'":'NULL').",
				descripcion=".($this->descripcion!==null?"'".self::$bd->proteger($this->descripcion)."'":'NULL').",
				modulo_nombre=".($this->modulo_nombre!==null?"'".self::$bd->proteger($this->modulo_nombre)."'":'NULL')."
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE parametro=".($this->parametro!==null?"'".self::$bd->proteger($this->parametro)."'":'NULL')."
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

	/**
	 * Recupera un objeto de tipo Modulo asociado al objeto BaseParametro
	 * Se requiere que ya se haya usado BaseParametro->get()
	 * @return Modulo Objeto de tipo Modulo con datos seteados o null en caso de que no existe la asociación
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getModulo () {
		require(DIR.'/class/db/final/Modulo.class.php');
		$objModulo = new Modulo();
		$objModulo->set(array('nombre'=>$this->modulo_nombre));
		if($objModulo->exist()) {
			$objModulo->get();
			return $objModulo;
		}
		return null;
	}



}

/**
 * BaseParametros para trabajo con listado de objetos de la tabla parametro
 * Parámetros de la aplicación
 * Esta clase entrega los métodos básicos para ser extendida por Parametros
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseParametros implements Bases {

	public static $bd; ///< Objeto de acceso a la base de datos
	private $selectStatement; ///< Columnas a consultar
	private $whereStatement; ///< Condiciones para la consula
	private $groupByStatement; ///< Campos para agrupar
	private $havingStatement; ///< Condiciones de los campos agrupados
	private $orderByStatement; ///< Orden de los resultados
	private $limitStatementRecords; ///< registros que se seleccionaran
	private $limitStatementOffset; ///< desde que fila se seleccionaran

	/**
	 * Constructor de la clase BaseParametros
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase BaseParametros
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseParametros, excepto $bd
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
	 * @param selectStatement Columna/s que se desea seleccionar de la tabla parametro
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
	 * @param groupByStatement Columna/s por la que se desea agrupar la tabla parametro
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
	 * @param orderByStatement Columna/s de la tabla parametro por la cual se ordenara y si es ASC o DESC
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
	 * Entrega la cantidad de objetos de tipo BaseParametro desde la base de datos,
	 * hará uso del whereStatement si no es null también de groupByStatement
	 * y havingStatement
	 * @return integer Cantidad de objetos encontrados
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
        final public function count () {
		$query = 'SELECT COUNT(*) FROM parametro';
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
		$query = 'SELECT MAX('.self::$bd->proteger($campo).') FROM parametro';
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
		$query = 'SELECT MIN('.self::$bd->proteger($campo).') FROM parametro';
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
		$query = 'SELECT SUM('.self::$bd->proteger($campo).') FROM parametro';
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
		$query = 'SELECT AVG('.self::$bd->proteger($campo).') FROM parametro';
		if ($this->whereStatement) $query .= $this->whereStatement;
		return self::$bd->getValor($query);
	}
	
	/**
	 * Recupera objetos de tipo BaseParametro desde la tabla parametro,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Arreglo o valor según lo solicitado
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function select ($solicitado) {

		// preparar consulta inicial
		if($this->selectStatement) $query = 'SELECT '.$this->selectStatement.' FROM parametro';
		else $query = 'SELECT * FROM parametro';
		
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
					$objParametro = new Parametro();
					foreach($fila as $columna => $valor)
						$objParametro->$columna = $valor;
					array_push($objetos, $objParametro);
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
	 * Recupera objetos de tipo BaseParametro desde la tabla parametro,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con objetos de tipo BaseParametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getObjetos () {
		return $this->select('objetos');
	}

	/**
	 * Recupera una tabla con las columnas y filas de la tabla parametro
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con filas y columnas de la tabla parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getTabla () {
		return $this->select('tabla');
	}

	/**
	 * Recupera una fila con las columnas de la tabla parametro
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con columnas de la tabla parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getFila() {
		return $this->select('fila');
	}
	
	/**
	 * Recupera una columna de la tabla parametro
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con la columna de la tabla parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getColumna() {
		return $this->select('columna');
	}
	
	/**
	 * Recupera un valor de la tabla parametro
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Valor solicitado de la tabla parametro
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getValor() {
		return $this->select('valor');
	}

}

?>
