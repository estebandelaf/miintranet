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
 * BaseUsuario para trabajo con un objeto de la tabla usuario
 * Usuarios del sistema y personal de la empresa
 * Esta clase entrega los métodos básicos para ser extendida por Usuario
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseUsuario implements Base {

	public static $bd; ///< Objeto de acceso a la base de datos
	
	// Atributos de la clase, corresponden a las columnas
	// en la tabla usuario
	public $id; ///< ID del usuario, utilizar RUN sin DV ni puntos ni guión: integer(32) NOT NULL DEFAULT '' PK 
	public $clave; ///< Clave encriptada usando MD5: character(32) NULL DEFAULT '' 
	public $hash; ///< Hash generado usando MD5: character(32) NULL DEFAULT '' 
	public $ultimoacceso; ///< Fecha y hora del último recurso utilizado: timestamp without time zone() NULL DEFAULT '' 
	public $ultimapagina; ///< íšltimo recurso utilizado: character varying(250) NULL DEFAULT '' 
	public $nombre; ///< Nombres: character varying(20) NOT NULL DEFAULT '' 
	public $apellido; ///< Apellidos del usuario (paterno y materno): character varying(30) NOT NULL DEFAULT '' 
	public $fechanacimiento; ///< Fecha de nacimiento: date() NOT NULL DEFAULT '' 
	public $lang; ///< Lenguaje en que deberá ser mostrado el sistema: character(2) NOT NULL DEFAULT ''es'' 
	public $usuario; ///< Nombre de usuario: character varying(20) NULL DEFAULT '' 
	public $activo; ///< Indica si el usuario se encuentra activo en el sistema: smallint(16) NOT NULL DEFAULT '0' 
	public $avatardata; ///< Datos para el avatar/fotografí­a: bytea() NULL DEFAULT '' 
	public $avatarname; ///< Nombre del avatar: character varying(50) NULL DEFAULT '' 
	public $avatartype; ///< Mimetype de la imágen: character varying(10) NULL DEFAULT '' 
	public $avatarsize; ///< Tamaño de la imágen: integer(32) NULL DEFAULT '' 
	public $sucursal_id; ///< ID de la sucursal a la que pertenece el usuario: character varying(5) NOT NULL DEFAULT '' FK:sucursal.id
	public $cargo_id; ///< ID del cargo que posee el usuario: integer(32) NOT NULL DEFAULT '' FK:cargo.id
	public $ingreso; ///< Fecha de ingreso a la empresa: date() NOT NULL DEFAULT '' 
	public $contratoinicio; ///< Fecha en que se inicio su contrato: date() NULL DEFAULT '' 
	public $contratofin; ///< Fecha en que se puso fin a su contrato: date() NULL DEFAULT '' 
	public $cvdata; ///< Datos para el curriculum: bytea() NULL DEFAULT '' 
	public $cvname; ///< Nombre del curriculum: character varying(50) NULL DEFAULT '' 
	public $cvtype; ///< Mimetype del curriculum: character varying(20) NULL DEFAULT '' 
	public $cvsize; ///< Tamaño del curriculum: integer(32) NULL DEFAULT '' 
	public $email; ///< Correo electrónico: character varying(60) NULL DEFAULT '' 
	public $telefono1; ///< Teléfono primario: character varying(20) NULL DEFAULT '' 
	public $telefono2; ///< Teléfono alternativo: character varying(20) NULL DEFAULT '' 
	public $filasporpagina; ///< Filas por página que el usuario verá en las búsquedas: integer(32) NULL DEFAULT '20' 
	public $remuneracion; ///< Remuneración mensual bruta, 0 en caso de trabajo a honorarios: integer(32) NOT NULL DEFAULT '0' 
	public $salud_id; ///< ID de la institución de salud a la que el usuario esta afiliado: integer(32) NULL DEFAULT '' FK:salud.id
	public $afp_id; ///< ID de la AFP a la que el usuario esta afiliado: integer(32) NULL DEFAULT '' FK:afp.id
	public $audit_programa; ///< Programa que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_usuario; ///< Usuario que realizó la última modificación a la fila: character varying(20) NOT NULL DEFAULT ''miintranet'' 
	public $audit_fechahora; ///< Fecha y hora en que se realizó la última modificación a la fila: timestamp without time zone() NOT NULL DEFAULT 'now' 


	/**
	 * Constructor de la clase BaseUsuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase Usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseUsuario, excepto $bd
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function clear () {
		$this->id = null;
		$this->clave = null;
		$this->hash = null;
		$this->ultimoacceso = null;
		$this->ultimapagina = null;
		$this->nombre = null;
		$this->apellido = null;
		$this->fechanacimiento = null;
		$this->lang = null;
		$this->usuario = null;
		$this->activo = null;
		$this->avatardata = null;
		$this->avatarname = null;
		$this->avatartype = null;
		$this->avatarsize = null;
		$this->sucursal_id = null;
		$this->cargo_id = null;
		$this->ingreso = null;
		$this->contratoinicio = null;
		$this->contratofin = null;
		$this->cvdata = null;
		$this->cvname = null;
		$this->cvtype = null;
		$this->cvsize = null;
		$this->email = null;
		$this->telefono1 = null;
		$this->telefono2 = null;
		$this->filasporpagina = null;
		$this->remuneracion = null;
		$this->salud_id = null;
		$this->afp_id = null;
		$this->audit_programa = null;
		$this->audit_usuario = null;
		$this->audit_fechahora = null;

	}
	
	/**
	 * Setea los atributos del objeto BaseUsuario mediante un arreglo,
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
		if(isset($arreglo['clave']) && $arreglo['clave']!='')
			$this->clave = $arreglo['clave'];
		if(isset($arreglo['hash']) && $arreglo['hash']!='')
			$this->hash = $arreglo['hash'];
		if(isset($arreglo['ultimoacceso']) && $arreglo['ultimoacceso']!='')
			$this->ultimoacceso = $arreglo['ultimoacceso'];
		if(isset($arreglo['ultimapagina']) && $arreglo['ultimapagina']!='')
			$this->ultimapagina = $arreglo['ultimapagina'];
		if(isset($arreglo['nombre']) && $arreglo['nombre']!='')
			$this->nombre = $arreglo['nombre'];
		if(isset($arreglo['apellido']) && $arreglo['apellido']!='')
			$this->apellido = $arreglo['apellido'];
		if(isset($arreglo['fechanacimiento']) && $arreglo['fechanacimiento']!='')
			$this->fechanacimiento = $arreglo['fechanacimiento'];
		if(isset($arreglo['lang']) && $arreglo['lang']!='')
			$this->lang = $arreglo['lang'];
		if(isset($arreglo['usuario']) && $arreglo['usuario']!='')
			$this->usuario = $arreglo['usuario'];
		if(isset($arreglo['activo']) && $arreglo['activo']!='')
			$this->activo = $arreglo['activo'];
		if(isset($arreglo['avatardata']) && $arreglo['avatardata']!='')
			$this->avatardata = $arreglo['avatardata'];
		if(isset($arreglo['avatarname']) && $arreglo['avatarname']!='')
			$this->avatarname = $arreglo['avatarname'];
		if(isset($arreglo['avatartype']) && $arreglo['avatartype']!='')
			$this->avatartype = $arreglo['avatartype'];
		if(isset($arreglo['avatarsize']) && $arreglo['avatarsize']!='')
			$this->avatarsize = $arreglo['avatarsize'];
		if(isset($arreglo['sucursal_id']) && $arreglo['sucursal_id']!='')
			$this->sucursal_id = $arreglo['sucursal_id'];
		if(isset($arreglo['cargo_id']) && $arreglo['cargo_id']!='')
			$this->cargo_id = $arreglo['cargo_id'];
		if(isset($arreglo['ingreso']) && $arreglo['ingreso']!='')
			$this->ingreso = $arreglo['ingreso'];
		if(isset($arreglo['contratoinicio']) && $arreglo['contratoinicio']!='')
			$this->contratoinicio = $arreglo['contratoinicio'];
		if(isset($arreglo['contratofin']) && $arreglo['contratofin']!='')
			$this->contratofin = $arreglo['contratofin'];
		if(isset($arreglo['cvdata']) && $arreglo['cvdata']!='')
			$this->cvdata = $arreglo['cvdata'];
		if(isset($arreglo['cvname']) && $arreglo['cvname']!='')
			$this->cvname = $arreglo['cvname'];
		if(isset($arreglo['cvtype']) && $arreglo['cvtype']!='')
			$this->cvtype = $arreglo['cvtype'];
		if(isset($arreglo['cvsize']) && $arreglo['cvsize']!='')
			$this->cvsize = $arreglo['cvsize'];
		if(isset($arreglo['email']) && $arreglo['email']!='')
			$this->email = $arreglo['email'];
		if(isset($arreglo['telefono1']) && $arreglo['telefono1']!='')
			$this->telefono1 = $arreglo['telefono1'];
		if(isset($arreglo['telefono2']) && $arreglo['telefono2']!='')
			$this->telefono2 = $arreglo['telefono2'];
		if(isset($arreglo['filasporpagina']) && $arreglo['filasporpagina']!='')
			$this->filasporpagina = $arreglo['filasporpagina'];
		if(isset($arreglo['remuneracion']) && $arreglo['remuneracion']!='')
			$this->remuneracion = $arreglo['remuneracion'];
		if(isset($arreglo['salud_id']) && $arreglo['salud_id']!='')
			$this->salud_id = $arreglo['salud_id'];
		if(isset($arreglo['afp_id']) && $arreglo['afp_id']!='')
			$this->afp_id = $arreglo['afp_id'];

	}

	/**
	 * Recupera las columnas de una fila desde la tabla usuario, seteando
	 * los valores de los atributos de la clase y además devolviendo un arreglo
	 * con dichos valores
	 * Se requiere que los campos de la PK (id) esten establecidos
	 * @return Array Arreglo con fila de la tabla usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function get () {
		$arrUsuario = self::$bd->getFila("SELECT * FROM usuario WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."");
		if($arrUsuario) {
			foreach($arrUsuario as $key=>$value)
				$this->$key = $value;
		} else {
			$this->clear();
			$arrUsuario = null;
		}
		return $arrUsuario;
	}

	/**
	 * Verifica si la fila asociada a la PK (id) existe en la tabla usuario
	 * @return boolean =true si el objeto existe en la base de datos
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function exist () {
		return (boolean) self::$bd->getValor("SELECT COUNT(*) FROM usuario WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."");
	}

	/**
	 * Elimina un objeto del tipo BaseUsuario de la tabla usuario
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function delete () {
		if(!$this->beforeDelete()) return false;
		self::$bd->consulta("DELETE FROM usuario WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."");
		if(!$this->afterDelete()) return false;
		return true;
	}
	
	/**
	 * Guarda un objeto del tipo BaseUsuario en la tabla usuario
	 * Inserta si el registro no existe, en caso contrario hace un update
	 * Campos que no pueden ser null: id, nombre, apellido, fechanacimiento, lang, activo, sucursal_id, cargo_id, ingreso, remuneracion, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function save () {
		if($this->exist()) return $this->update();
		else return $this->insert();
	}

	/**
	 * Inserta un nuevo objeto del tipo BaseUsuario en la tabla usuario
	 * Si hay campos definidos como auto no se utilizan en este método
	 * Campos que no pueden ser null: id, nombre, apellido, fechanacimiento, lang, activo, sucursal_id, cargo_id, ingreso, remuneracion, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function insert () {
		if(!$this->beforeInsert()) return false;
		self::$bd->consulta("
			INSERT INTO usuario (
				id,
				clave,
				hash,
				ultimoacceso,
				ultimapagina,
				nombre,
				apellido,
				fechanacimiento,
				lang,
				usuario,
				activo,
				avatardata,
				avatarname,
				avatartype,
				avatarsize,
				sucursal_id,
				cargo_id,
				ingreso,
				contratoinicio,
				contratofin,
				cvdata,
				cvname,
				cvtype,
				cvsize,
				email,
				telefono1,
				telefono2,
				filasporpagina,
				remuneracion,
				salud_id,
				afp_id
				, audit_programa
				, audit_usuario
				, audit_fechahora
			) VALUES (
				".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL').",
				".($this->clave!==null?"'".self::$bd->proteger($this->clave)."'":'NULL').",
				".($this->hash!==null?"'".self::$bd->proteger($this->hash)."'":'NULL').",
				".($this->ultimoacceso!==null?"'".self::$bd->proteger($this->ultimoacceso)."'":'NULL').",
				".($this->ultimapagina!==null?"'".self::$bd->proteger($this->ultimapagina)."'":'NULL').",
				".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL').",
				".($this->apellido!==null?"'".self::$bd->proteger($this->apellido)."'":'NULL').",
				".($this->fechanacimiento!==null?"'".self::$bd->proteger($this->fechanacimiento)."'":'NULL').",
				".($this->lang!==null?"'".self::$bd->proteger($this->lang)."'":'NULL').",
				".($this->usuario!==null?"'".self::$bd->proteger($this->usuario)."'":'NULL').",
				".($this->activo!==null?"'".self::$bd->proteger($this->activo)."'":'NULL').",
				".($this->avatardata!==null?"'".self::$bd->proteger($this->avatardata)."'":'NULL').",
				".($this->avatarname!==null?"'".self::$bd->proteger($this->avatarname)."'":'NULL').",
				".($this->avatartype!==null?"'".self::$bd->proteger($this->avatartype)."'":'NULL').",
				".($this->avatarsize!==null?"'".self::$bd->proteger($this->avatarsize)."'":'NULL').",
				".($this->sucursal_id!==null?"'".self::$bd->proteger($this->sucursal_id)."'":'NULL').",
				".($this->cargo_id!==null?"'".self::$bd->proteger($this->cargo_id)."'":'NULL').",
				".($this->ingreso!==null?"'".self::$bd->proteger($this->ingreso)."'":'NULL').",
				".($this->contratoinicio!==null?"'".self::$bd->proteger($this->contratoinicio)."'":'NULL').",
				".($this->contratofin!==null?"'".self::$bd->proteger($this->contratofin)."'":'NULL').",
				".($this->cvdata!==null?"'".self::$bd->proteger($this->cvdata)."'":'NULL').",
				".($this->cvname!==null?"'".self::$bd->proteger($this->cvname)."'":'NULL').",
				".($this->cvtype!==null?"'".self::$bd->proteger($this->cvtype)."'":'NULL').",
				".($this->cvsize!==null?"'".self::$bd->proteger($this->cvsize)."'":'NULL').",
				".($this->email!==null?"'".self::$bd->proteger($this->email)."'":'NULL').",
				".($this->telefono1!==null?"'".self::$bd->proteger($this->telefono1)."'":'NULL').",
				".($this->telefono2!==null?"'".self::$bd->proteger($this->telefono2)."'":'NULL').",
				".($this->filasporpagina!==null?"'".self::$bd->proteger($this->filasporpagina)."'":'NULL').",
				".($this->remuneracion!==null?"'".self::$bd->proteger($this->remuneracion)."'":'NULL').",
				".($this->salud_id!==null?"'".self::$bd->proteger($this->salud_id)."'":'NULL').",
				".($this->afp_id!==null?"'".self::$bd->proteger($this->afp_id)."'":'NULL')."
				, '".AUDIT_PROGRAMA."'
				, '".AUDIT_USUARIO."'
				, NOW()
			)
		");
		if(!$this->afterInsert()) return false;
		return true;
	}

	/**
	 * Actualiza un objeto del tipo BaseUsuario de la tabla usuario
	 * No es posible actualizar la PK (id)
	 * Campos que no pueden ser null: id, nombre, apellido, fechanacimiento, lang, activo, sucursal_id, cargo_id, ingreso, remuneracion, audit_programa, audit_usuario, audit_fechahora
	 * @return =true en caso de éxito
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function update () {
		if(!$this->beforeUpdate()) return false;
		self::$bd->consulta("
			UPDATE usuario
			SET
				clave=".($this->clave!==null?"'".self::$bd->proteger($this->clave)."'":'NULL').",
				hash=".($this->hash!==null?"'".self::$bd->proteger($this->hash)."'":'NULL').",
				ultimoacceso=".($this->ultimoacceso!==null?"'".self::$bd->proteger($this->ultimoacceso)."'":'NULL').",
				ultimapagina=".($this->ultimapagina!==null?"'".self::$bd->proteger($this->ultimapagina)."'":'NULL').",
				nombre=".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL').",
				apellido=".($this->apellido!==null?"'".self::$bd->proteger($this->apellido)."'":'NULL').",
				fechanacimiento=".($this->fechanacimiento!==null?"'".self::$bd->proteger($this->fechanacimiento)."'":'NULL').",
				lang=".($this->lang!==null?"'".self::$bd->proteger($this->lang)."'":'NULL').",
				usuario=".($this->usuario!==null?"'".self::$bd->proteger($this->usuario)."'":'NULL').",
				activo=".($this->activo!==null?"'".self::$bd->proteger($this->activo)."'":'NULL').",
				avatardata=".($this->avatardata!==null?"'".self::$bd->proteger($this->avatardata)."'":'NULL').",
				avatarname=".($this->avatarname!==null?"'".self::$bd->proteger($this->avatarname)."'":'NULL').",
				avatartype=".($this->avatartype!==null?"'".self::$bd->proteger($this->avatartype)."'":'NULL').",
				avatarsize=".($this->avatarsize!==null?"'".self::$bd->proteger($this->avatarsize)."'":'NULL').",
				sucursal_id=".($this->sucursal_id!==null?"'".self::$bd->proteger($this->sucursal_id)."'":'NULL').",
				cargo_id=".($this->cargo_id!==null?"'".self::$bd->proteger($this->cargo_id)."'":'NULL').",
				ingreso=".($this->ingreso!==null?"'".self::$bd->proteger($this->ingreso)."'":'NULL').",
				contratoinicio=".($this->contratoinicio!==null?"'".self::$bd->proteger($this->contratoinicio)."'":'NULL').",
				contratofin=".($this->contratofin!==null?"'".self::$bd->proteger($this->contratofin)."'":'NULL').",
				cvdata=".($this->cvdata!==null?"'".self::$bd->proteger($this->cvdata)."'":'NULL').",
				cvname=".($this->cvname!==null?"'".self::$bd->proteger($this->cvname)."'":'NULL').",
				cvtype=".($this->cvtype!==null?"'".self::$bd->proteger($this->cvtype)."'":'NULL').",
				cvsize=".($this->cvsize!==null?"'".self::$bd->proteger($this->cvsize)."'":'NULL').",
				email=".($this->email!==null?"'".self::$bd->proteger($this->email)."'":'NULL').",
				telefono1=".($this->telefono1!==null?"'".self::$bd->proteger($this->telefono1)."'":'NULL').",
				telefono2=".($this->telefono2!==null?"'".self::$bd->proteger($this->telefono2)."'":'NULL').",
				filasporpagina=".($this->filasporpagina!==null?"'".self::$bd->proteger($this->filasporpagina)."'":'NULL').",
				remuneracion=".($this->remuneracion!==null?"'".self::$bd->proteger($this->remuneracion)."'":'NULL').",
				salud_id=".($this->salud_id!==null?"'".self::$bd->proteger($this->salud_id)."'":'NULL').",
				afp_id=".($this->afp_id!==null?"'".self::$bd->proteger($this->afp_id)."'":'NULL')."
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
	 * Recupera un objeto de tipo Sucursal asociado al objeto BaseUsuario
	 * Se requiere que ya se haya usado BaseUsuario->get()
	 * @return Sucursal Objeto de tipo Sucursal con datos seteados o null en caso de que no existe la asociación
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getSucursal () {
		require(DIR.'/class/db/final/Sucursal.class.php');
		$objSucursal = new Sucursal();
		$objSucursal->set(array('id'=>$this->sucursal_id));
		if($objSucursal->exist()) {
			$objSucursal->get();
			return $objSucursal;
		}
		return null;
	}

	/**
	 * Recupera un objeto de tipo Cargo asociado al objeto BaseUsuario
	 * Se requiere que ya se haya usado BaseUsuario->get()
	 * @return Cargo Objeto de tipo Cargo con datos seteados o null en caso de que no existe la asociación
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getCargo () {
		require(DIR.'/class/db/final/Cargo.class.php');
		$objCargo = new Cargo();
		$objCargo->set(array('id'=>$this->cargo_id));
		if($objCargo->exist()) {
			$objCargo->get();
			return $objCargo;
		}
		return null;
	}

	/**
	 * Recupera un objeto de tipo Salud asociado al objeto BaseUsuario
	 * Se requiere que ya se haya usado BaseUsuario->get()
	 * @return Salud Objeto de tipo Salud con datos seteados o null en caso de que no existe la asociación
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getSalud () {
		require(DIR.'/class/db/final/Salud.class.php');
		$objSalud = new Salud();
		$objSalud->set(array('id'=>$this->salud_id));
		if($objSalud->exist()) {
			$objSalud->get();
			return $objSalud;
		}
		return null;
	}

	/**
	 * Recupera un objeto de tipo Afp asociado al objeto BaseUsuario
	 * Se requiere que ya se haya usado BaseUsuario->get()
	 * @return Afp Objeto de tipo Afp con datos seteados o null en caso de que no existe la asociación
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getAfp () {
		require(DIR.'/class/db/final/Afp.class.php');
		$objAfp = new Afp();
		$objAfp->set(array('id'=>$this->afp_id));
		if($objAfp->exist()) {
			$objAfp->get();
			return $objAfp;
		}
		return null;
	}



}

/**
 * BaseUsuarios para trabajo con listado de objetos de la tabla usuario
 * Usuarios del sistema y personal de la empresa
 * Esta clase entrega los métodos básicos para ser extendida por Usuarios
 * Cualquier nuevo código debe ser colocado en la clase final NO en esta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-15
 */
abstract class BaseUsuarios implements Bases {

	public static $bd; ///< Objeto de acceso a la base de datos
	private $selectStatement; ///< Columnas a consultar
	private $whereStatement; ///< Condiciones para la consula
	private $groupByStatement; ///< Campos para agrupar
	private $havingStatement; ///< Condiciones de los campos agrupados
	private $orderByStatement; ///< Orden de los resultados
	private $limitStatementRecords; ///< registros que se seleccionaran
	private $limitStatementOffset; ///< desde que fila se seleccionaran

	/**
	 * Constructor de la clase BaseUsuarios
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __construct ($bd) {
		if(!self::$bd) self::$bd = $bd;
		unset($bd);
		$this->clear();
	}

	/**
	 * Destructor de la clase BaseUsuarios
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	protected function __destruct () {
	}

	/**
	 * Limpia los atributos del objeto BaseUsuarios, excepto $bd
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
	 * @param selectStatement Columna/s que se desea seleccionar de la tabla usuario
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
	 * @param groupByStatement Columna/s por la que se desea agrupar la tabla usuario
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
	 * @param orderByStatement Columna/s de la tabla usuario por la cual se ordenara y si es ASC o DESC
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
	 * Entrega la cantidad de objetos de tipo BaseUsuario desde la base de datos,
	 * hará uso del whereStatement si no es null también de groupByStatement
	 * y havingStatement
	 * @return integer Cantidad de objetos encontrados
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
        final public function count () {
		$query = 'SELECT COUNT(*) FROM usuario';
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
		$query = 'SELECT MAX('.self::$bd->proteger($campo).') FROM usuario';
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
		$query = 'SELECT MIN('.self::$bd->proteger($campo).') FROM usuario';
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
		$query = 'SELECT SUM('.self::$bd->proteger($campo).') FROM usuario';
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
		$query = 'SELECT AVG('.self::$bd->proteger($campo).') FROM usuario';
		if ($this->whereStatement) $query .= $this->whereStatement;
		return self::$bd->getValor($query);
	}
	
	/**
	 * Recupera objetos de tipo BaseUsuario desde la tabla usuario,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Arreglo o valor según lo solicitado
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final private function select ($solicitado) {

		// preparar consulta inicial
		if($this->selectStatement) $query = 'SELECT '.$this->selectStatement.' FROM usuario';
		else $query = 'SELECT * FROM usuario';
		
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
					$objUsuario = new Usuario();
					foreach($fila as $columna => $valor)
						$objUsuario->$columna = $valor;
					array_push($objetos, $objUsuario);
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
	 * Recupera objetos de tipo BaseUsuario desde la tabla usuario,
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con objetos de tipo BaseUsuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getObjetos () {
		return $this->select('objetos');
	}

	/**
	 * Recupera una tabla con las columnas y filas de la tabla usuario
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con filas y columnas de la tabla usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	final public function getTabla () {
		return $this->select('tabla');
	}

	/**
	 * Recupera una fila con las columnas de la tabla usuario
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con columnas de la tabla usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getFila() {
		return $this->select('fila');
	}
	
	/**
	 * Recupera una columna de la tabla usuario
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Array Arreglo con la columna de la tabla usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getColumna() {
		return $this->select('columna');
	}
	
	/**
	 * Recupera un valor de la tabla usuario
	 * hará uso del whereStatement si no es null, también de limitStatement,
	 * de orderbyStatement y de selectStatement
	 * @return Mixed Valor solicitado de la tabla usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-15
	 */
	public function getValor() {
		return $this->select('valor');
	}

}

?>
