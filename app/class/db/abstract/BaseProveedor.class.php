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
 * BaseProveedor para trabajo con un objeto de la tabla proveedor
 * Proveedores de la empresa
 * Esta clase entrega los métodos básicos para ser extendida por Proveedor
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseProveedor implements Base {

	public static $bd; ///< Objeto de acceso a la base de datos
	
	// Atributos de la clase, corresponden a las columnas
	// en la tabla proveedor
	public $id; ///< ID del proveedor (RUT sin puntos ni dv): integer(32) NOT NULL DEFAULT '' PK 
	public $razonsocial; ///< Razón social del proveedor: character varying(50) NOT NULL DEFAULT '' 
	public $nombrefantasia; ///< Nombre de fantasía del proveedor: character varying(30) NOT NULL DEFAULT '' 
	public $nacional; ///< Indica si es un proveedor nacional (1) o extranjero (0): smallint(16) NOT NULL DEFAULT '1' 
	public $actividad_economica_id; ///< Código de actividad económica: integer(32) NOT NULL DEFAULT '' FK:actividad_economica.id
	public $direccion; ///< Dirección principal utilizada: character varying(70) NOT NULL DEFAULT '' 
	public $comuna_id; ///< Comuna: integer(32) NOT NULL DEFAULT '' FK:comuna.id
	public $web; ///< Sitio web (incluyendo http://): character varying(30) NULL DEFAULT '' 
	public $telefono1; ///< Teléfono principal: character varying(20) NOT NULL DEFAULT '' 
	public $telefono2; ///< Teléfono secundario: character varying(20) NULL DEFAULT '' 
	public $contacto; ///< Nombre del contacto dentro de la empresa: character varying(30) NULL DEFAULT '' 
	public $email; ///< Correo del contacto: character varying(60) NULL DEFAULT '' 
	public $replegal; ///< Representante legal: character varying(30) NULL DEFAULT '' 
	public $reprut; ///< Rut del representante legal (sin puntos ni dv): integer(32) NULL DEFAULT '' 
	public $activo; ///< Indica si el proveedor está activo (1) o no (0): smallint(16) NOT NULL DEFAULT '1' 
	public $audit_programa; ///< Programa que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_usuario; ///< Usuario que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_fechahora; ///< Fecha y hora en que se realizó la última modificación a la fila: timestamp without time zone() NOT NULL DEFAULT 'now' 


	/**
	 * Constructor de la clase BaseProveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase Proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseProveedor, excepto $bd
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function clear () {
		$this->id = null;
		$this->razonsocial = null;
		$this->nombrefantasia = null;
		$this->nacional = null;
		$this->actividad_economica_id = null;
		$this->direccion = null;
		$this->comuna_id = null;
		$this->web = null;
		$this->telefono1 = null;
		$this->telefono2 = null;
		$this->contacto = null;
		$this->email = null;
		$this->replegal = null;
		$this->reprut = null;
		$this->activo = null;
		$this->audit_programa = null;
		$this->audit_usuario = null;
		$this->audit_fechahora = null;

	}
	
	/**
	 * Setea los atributos del objeto BaseProveedor mediante un arreglo,
	 * la key del arreglo es el nombre del atributo, si la key no existe
	 * el campo quedará seteado a null
	 * @param arreglo Arreglo con la relacion columna=>valor
	 * @param clear Flag para limpiar (o no) atributos antes de hacer el set
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function set ($arreglo, $clear = true) {
		if($clear) $this->clear();
		if(isset($arreglo['id']) && $arreglo['id']!='')
			$this->id = $arreglo['id'];
		if(isset($arreglo['razonsocial']) && $arreglo['razonsocial']!='')
			$this->razonsocial = $arreglo['razonsocial'];
		if(isset($arreglo['nombrefantasia']) && $arreglo['nombrefantasia']!='')
			$this->nombrefantasia = $arreglo['nombrefantasia'];
		if(isset($arreglo['nacional']) && $arreglo['nacional']!='')
			$this->nacional = $arreglo['nacional'];
		if(isset($arreglo['actividad_economica_id']) && $arreglo['actividad_economica_id']!='')
			$this->actividad_economica_id = $arreglo['actividad_economica_id'];
		if(isset($arreglo['direccion']) && $arreglo['direccion']!='')
			$this->direccion = $arreglo['direccion'];
		if(isset($arreglo['comuna_id']) && $arreglo['comuna_id']!='')
			$this->comuna_id = $arreglo['comuna_id'];
		if(isset($arreglo['web']) && $arreglo['web']!='')
			$this->web = $arreglo['web'];
		if(isset($arreglo['telefono1']) && $arreglo['telefono1']!='')
			$this->telefono1 = $arreglo['telefono1'];
		if(isset($arreglo['telefono2']) && $arreglo['telefono2']!='')
			$this->telefono2 = $arreglo['telefono2'];
		if(isset($arreglo['contacto']) && $arreglo['contacto']!='')
			$this->contacto = $arreglo['contacto'];
		if(isset($arreglo['email']) && $arreglo['email']!='')
			$this->email = $arreglo['email'];
		if(isset($arreglo['replegal']) && $arreglo['replegal']!='')
			$this->replegal = $arreglo['replegal'];
		if(isset($arreglo['reprut']) && $arreglo['reprut']!='')
			$this->reprut = $arreglo['reprut'];
		if(isset($arreglo['activo']) && $arreglo['activo']!='')
			$this->activo = $arreglo['activo'];

	}

	/**
	 * Recupera las columnas de una fila desde la tabla proveedor, seteando
	 * los valores de los atributos de la clase y además devolviendo un arreglo
	 * con dichos valores
	 * Se requiere que los campos de la PK (id) esten establecidos
	 * @return Array Arreglo con fila de la tabla proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function get () {
		$arrProveedor = self::$bd->getFila("SELECT * FROM proveedor WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."");
		if($arrProveedor) {
			foreach($arrProveedor as $key=>$value)
				$this->$key = $value;
		} else {
			$this->clear();
			$arrProveedor = null;
		}
		return $arrProveedor;
	}

	/**
	 * Verifica si la fila asociada a la PK (id) existe en la tabla proveedor
	 * @return boolean =true si el objeto existe en la base de datos
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function exist () {
		return (boolean) self::$bd->getValor("SELECT COUNT(*) FROM proveedor WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."");
	}

	/**
	 * Elimina un objeto del tipo BaseProveedor de la tabla proveedor
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function delete () {
		if(!$this->beforeDelete()) return false;
		self::$bd->consulta("DELETE FROM proveedor WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."");
		if(!$this->afterDelete()) return false;
		return true;
	}
	
	/**
	 * Guarda un objeto del tipo BaseProveedor en la tabla proveedor
	 * Inserta si el registro no existe, en caso contrario hace un update
	 * Campos que no pueden ser null: id, razonsocial, nombrefantasia, nacional, actividad_economica_id, direccion, comuna_id, telefono1, activo, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function save () {
		if($this->exist()) return $this->update();
		else return $this->insert();
	}

	/**
	 * Inserta un nuevo objeto del tipo BaseProveedor en la tabla proveedor
	 * Si hay campos definidos como auto no se utilizan en este método
	 * Campos que no pueden ser null: id, razonsocial, nombrefantasia, nacional, actividad_economica_id, direccion, comuna_id, telefono1, activo, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function insert () {
		if(!$this->beforeInsert()) return false;
		self::$bd->consulta("
			INSERT INTO proveedor (
				id,
				razonsocial,
				nombrefantasia,
				nacional,
				actividad_economica_id,
				direccion,
				comuna_id,
				web,
				telefono1,
				telefono2,
				contacto,
				email,
				replegal,
				reprut,
				activo
				, audit_programa
				, audit_usuario
				, audit_fechahora
			) VALUES (
				".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL').",
				".($this->razonsocial!==null?"'".self::$bd->proteger($this->razonsocial)."'":'NULL').",
				".($this->nombrefantasia!==null?"'".self::$bd->proteger($this->nombrefantasia)."'":'NULL').",
				".($this->nacional!==null?"'".self::$bd->proteger($this->nacional)."'":'NULL').",
				".($this->actividad_economica_id!==null?"'".self::$bd->proteger($this->actividad_economica_id)."'":'NULL').",
				".($this->direccion!==null?"'".self::$bd->proteger($this->direccion)."'":'NULL').",
				".($this->comuna_id!==null?"'".self::$bd->proteger($this->comuna_id)."'":'NULL').",
				".($this->web!==null?"'".self::$bd->proteger($this->web)."'":'NULL').",
				".($this->telefono1!==null?"'".self::$bd->proteger($this->telefono1)."'":'NULL').",
				".($this->telefono2!==null?"'".self::$bd->proteger($this->telefono2)."'":'NULL').",
				".($this->contacto!==null?"'".self::$bd->proteger($this->contacto)."'":'NULL').",
				".($this->email!==null?"'".self::$bd->proteger($this->email)."'":'NULL').",
				".($this->replegal!==null?"'".self::$bd->proteger($this->replegal)."'":'NULL').",
				".($this->reprut!==null?"'".self::$bd->proteger($this->reprut)."'":'NULL').",
				".($this->activo!==null?"'".self::$bd->proteger($this->activo)."'":'NULL')."
				, '".AUDIT_PROGRAMA."'
				, '".AUDIT_USUARIO."'
				, NOW()
			)
		");
		if(!$this->afterInsert()) return false;
		return true;
	}

	/**
	 * Actualiza un objeto del tipo BaseProveedor de la tabla proveedor
	 * No es posible actualizar la PK (id)
	 * Campos que no pueden ser null: id, razonsocial, nombrefantasia, nacional, actividad_economica_id, direccion, comuna_id, telefono1, activo, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function update () {
		if(!$this->beforeUpdate()) return false;
		self::$bd->consulta("
			UPDATE proveedor
			SET
				razonsocial=".($this->razonsocial!==null?"'".self::$bd->proteger($this->razonsocial)."'":'NULL').",
				nombrefantasia=".($this->nombrefantasia!==null?"'".self::$bd->proteger($this->nombrefantasia)."'":'NULL').",
				nacional=".($this->nacional!==null?"'".self::$bd->proteger($this->nacional)."'":'NULL').",
				actividad_economica_id=".($this->actividad_economica_id!==null?"'".self::$bd->proteger($this->actividad_economica_id)."'":'NULL').",
				direccion=".($this->direccion!==null?"'".self::$bd->proteger($this->direccion)."'":'NULL').",
				comuna_id=".($this->comuna_id!==null?"'".self::$bd->proteger($this->comuna_id)."'":'NULL').",
				web=".($this->web!==null?"'".self::$bd->proteger($this->web)."'":'NULL').",
				telefono1=".($this->telefono1!==null?"'".self::$bd->proteger($this->telefono1)."'":'NULL').",
				telefono2=".($this->telefono2!==null?"'".self::$bd->proteger($this->telefono2)."'":'NULL').",
				contacto=".($this->contacto!==null?"'".self::$bd->proteger($this->contacto)."'":'NULL').",
				email=".($this->email!==null?"'".self::$bd->proteger($this->email)."'":'NULL').",
				replegal=".($this->replegal!==null?"'".self::$bd->proteger($this->replegal)."'":'NULL').",
				reprut=".($this->reprut!==null?"'".self::$bd->proteger($this->reprut)."'":'NULL').",
				activo=".($this->activo!==null?"'".self::$bd->proteger($this->activo)."'":'NULL')."
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."
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
	 * Recupera un objeto de tipo Actividad_economica asociado al objeto BaseProveedor
	 * Se requiere que ya se haya usado BaseProveedor->get()
	 * @return Actividad_economica Objeto de tipo Actividad_economica con datos seteados o null en caso de que no existe la asociación
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getActividad_economica () {
		require(DIR.'/class/db/final/Actividad_economica.class.php');
		$objActividad_economica = new Actividad_economica();
		$objActividad_economica->set(array('id'=>$this->actividad_economica_id));
		if($objActividad_economica->exist()) {
			$objActividad_economica->get();
			return $objActividad_economica;
		}
		return null;
	}

	/**
	 * Recupera un objeto de tipo Comuna asociado al objeto BaseProveedor
	 * Se requiere que ya se haya usado BaseProveedor->get()
	 * @return Comuna Objeto de tipo Comuna con datos seteados o null en caso de que no existe la asociación
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getComuna () {
		require(DIR.'/class/db/final/Comuna.class.php');
		$objComuna = new Comuna();
		$objComuna->set(array('id'=>$this->comuna_id));
		if($objComuna->exist()) {
			$objComuna->get();
			return $objComuna;
		}
		return null;
	}



}

/**
 * BaseProveedors para trabajo con listado de objetos de la tabla proveedor
 * Proveedores de la empresa
 * Esta clase entrega los métodos básicos para ser extendida por Proveedors
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseProveedors implements Bases {

	public static $bd; ///< Objeto de acceso a la base de datos
	private $selectStatement; ///< Columnas a consultar
	private $whereStatement; ///< Condiciones para la consula
	private $groupByStatement; ///< Campos para agrupar
	private $havingStatement; ///< Condiciones de los campos agrupados
	private $orderByStatement; ///< Orden de los resultados
	private $limitStatementRecords; ///< registros que se seleccionaran
	private $limitStatementOffset; ///< desde que fila se seleccionaran

	/**
	 * Constructor de la clase BaseProveedors
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase BaseProveedors
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseProveedors, excepto $bd
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
	 * @param selectStatement Columna/s que se desea seleccionar de la tabla proveedor
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
	 * @param groupByStatement Columna/s por la que se desea agrupar la tabla proveedor
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
	 * @param orderByStatement Columna/s de la tabla proveedor por la cual se ordenara y si es ASC o DESC
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
	 * Entrega la cantidad de objetos de tipo BaseProveedor desde la base de datos,
	 * hará uso del whereStatement si no es null también de groupByStatement
	 * y havingStatement
	 * @return integer Cantidad de objetos encontrados
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
        final public function count () {
		$query = 'SELECT COUNT(*) FROM proveedor';
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
		$query = 'SELECT MAX('.self::$bd->proteger($campo).') FROM proveedor';
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
		$query = 'SELECT MIN('.self::$bd->proteger($campo).') FROM proveedor';
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
		$query = 'SELECT SUM('.self::$bd->proteger($campo).') FROM proveedor';
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
		$query = 'SELECT AVG('.self::$bd->proteger($campo).') FROM proveedor';
		if ($this->whereStatement) $query .= $this->whereStatement;
		return self::$bd->getValor($query);
	}
	
	/**
	 * Recupera objetos de tipo BaseProveedor desde la tabla proveedor,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Arreglo o valor según lo solicitado
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function select ($solicitado) {

		// preparar consulta inicial
		if($this->selectStatement) $query = 'SELECT '.$this->selectStatement.' FROM proveedor';
		else $query = 'SELECT * FROM proveedor';
		
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
					$objProveedor = new Proveedor();
					foreach($fila as $columna => $valor)
						$objProveedor->$columna = $valor;
					array_push($objetos, $objProveedor);
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
	 * Recupera objetos de tipo BaseProveedor desde la tabla proveedor,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con objetos de tipo BaseProveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getObjetos () {
		return $this->select('objetos');
	}

	/**
	 * Recupera una tabla con las columnas y filas de la tabla proveedor
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con filas y columnas de la tabla proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getTabla () {
		return $this->select('tabla');
	}

	/**
	 * Recupera una fila con las columnas de la tabla proveedor
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con columnas de la tabla proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getFila() {
		return $this->select('fila');
	}
	
	/**
	 * Recupera una columna de la tabla proveedor
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con la columna de la tabla proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getColumna() {
		return $this->select('columna');
	}
	
	/**
	 * Recupera un valor de la tabla proveedor
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Valor solicitado de la tabla proveedor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getValor() {
		return $this->select('valor');
	}

}

?>
