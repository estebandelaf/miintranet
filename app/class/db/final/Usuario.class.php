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

if(!defined('__Usuario__')) {
define('__Usuario__', true);

require(DIR.'/class/db/abstract/BaseUsuario.class.php');

/**
 * Usuario para trabajo con un objeto de la tabla usuario
 * Usuarios del sistema y personal de la empresa
 * Esta clase permite ampliar las funcionalidades provistas por BaseUsuario
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-17
 */
final class Usuario extends BaseUsuario {

	/**
	 * Constructor de la clase Usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-17
	 */
	final public function __construct () {
		// asignar objeto de acceso a la base de datos
		// si se quisiera utilizar otro objeto que permita el acceso
		// a otra base de datos que utiliza esta clase se cambia solo
		// en las lineas siguientes (ej: global $mysql o global $oracle)
		global $bd;
		parent::__construct ($bd);
		// definir lenguaje por defecto (para usuarios no logueados)
		$this->lang = LANG;
	}
	
	/**
	 * Destructor de la clase Usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-27
	 */
	final public function __destruct() {
		parent::__destruct ();
	}

	/**
	 * Asigna atributos del Usuario, solo si existe una sesión válida de usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function start () {
		// obtener estado de logueo y datos de la cuenta
		$this->id = (isset($_SESSION['usuario']['id'])) ? $_SESSION['usuario']['id'] : false;
                $hash = (isset($_SESSION['usuario']['hash'])) ? $_SESSION['usuario']['hash'] : false;
		// si existen datos de sesion de usuario se verifican
		if($this->id && $hash) {
			// obtener datos del usuario
			$this->get();
                        // revisar hash sea valido
			if($this->hash!=$hash) $this->logout();
			// actualizar información de acceso a la página
			self::$bd->consulta("UPDATE usuario SET ultimoacceso = NOW(), ultimapagina = '".AKI."'  WHERE id = ".$this->id);
		}
	}

	/**
	 * Loguea a un usuario en el sistema
	 * @param usuario Usuario a loguear
	 * @param clave Clave del usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function login ($usuario = '', $clave = '') {
		// verificar que los campos no se encuentres vacios
		if(empty($usuario) || empty($clave)) {
			MiSiTiO::error(LANG_ERROR_NEED_TITLE, LANG_ERROR_NEED_MSG);
		} else {
			// buscar el usuario en la base de datos
			$datos = self::$bd->getFila("SELECT id, clave FROM usuario WHERE usuario = '".self::$bd->proteger($usuario)."' AND activo = 1");
			// verificar que el usuario pasado por el formulario exista dentro de la base de datos
			if(!$datos) {
				// si el usuario no existe se muestra un mensaje de error
				MiSiTiO::error(LANG_LOGIN_ERROR_USER_TITLE, LANG_LOGIN_ERROR_USER_MSG);
			} else {
				// verificar que la clave pasada coincida con la clave encriptada guardada en la base de datos
				if($datos['clave'] != md5($clave)) { // para poder comparar se debe encriptar la clave del formulario
					// si las claves no coinciden se muestra un error indicando este problema
					MiSiTiO::error(LANG_LOGIN_ERROR_PASS_TITLE, LANG_LOGIN_ERROR_PASS_MSG);
				} else {
					unset($datos['clave']);
					// verificar que la clave no contenga ni el id ni el nombre de usuario
					if(strstr(strtolower($clave), strtolower($datos['id'])) || strstr(strtolower($clave), strtolower($datos['usuario']))) {
						ob_end_clean();
						header('location: /clave');
						exit();
					}
					// si el usuario existe y la clave es correcta entonces se loguea al usuario
					// seteando los valores de id, nombre y el hash a distintos de null
					$hash = md5($clave.$this->ip().date('U'));
					$_SESSION['usuario']['id'] = $datos['id'];
					$_SESSION['usuario']['usuario'] = $usuario;
					$_SESSION['usuario']['lang'] = $datos['lang'];
					$_SESSION['usuario']['hash'] = $hash;
					$_SESSION['usuario']['rowsPerPage'] = $datos['filasporpagina']>=1 ? $datos['filasporpagina'] : TABLE_SHOW_ROWS;
					self::$bd->consulta("UPDATE usuario SET hash = '".$hash."' WHERE id = ".$datos['id']);
					// se redirecciona a la página principal con la sesión ya creada
					ob_end_clean();
					header('location: /');
					exit();
				}
			}
		}
	}

	/**
	 * Destruye la sesión del usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-16
	 */
	final public function logout() {
		session_unset();
		session_destroy();
		ob_end_clean();
		header('location: /');
		exit();
	}

	/**
	 * Verifica si el usuario esta logueado de forma correcta
	 * @return True si esta logueado
	 * @todo Mejorar forma en que se verifica si esta logueado y retorna true
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function logueado () {
		// Primera verificación, no pueden estar estos atributos como false
		// VERIFICACION MULA... MEJORAR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		if($this->id!=false && $this->hash!=false)
			return true;
		// En cualquier otro caso retornar false
		return false;
	}

	/**
	 * Verifica que exista un usuario logueado y que tenga permisos para la pagina solicitada
	 * @param recurso Indica el recurso al que se quiere acceder, si es vacio se consulta por la pagina actual
	 * @param error Si es true se generará una página de error, si es false se puede usar para los if
	 * @return True si el usuario esta correctamente autorizado
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-17
	 */
	final public function autorizado ($recurso = '', $error = true) {
		// ciertos archivos no requieren logueo
		$recursosSinLogueo = explode(' ', RECURSOS_SIN_BLOQUEO);
		foreach($recursosSinLogueo as &$recursoSinLogueo)
			if($recursoSinLogueo==$recurso) return true;
		// script de error tampoco requiere logueo
		if($_SERVER['SCRIPT_NAME']=='/error.php') return true;
		// para el resto verificar permisos
                // primero verificar que el usuario este logueado
		if(!$this->logueado()) {
			if($error) MiSiTiO::error(ACCESS_DENIED);
			else return false;
		} else {
			// se debe verificar si el usuario tiene permisos para estar en el recurso solicitado
			if(!empty($recurso)) {
                                if(self::$bd->funcion('f_usuarioAutorizado', $this->id, $recurso)) return true;
			} else {
				return true; // si solo se quiere verificar login
			}
		}
		// si no se logro determinar si habia o no permiso antes
		// aqui por defecto de deniega (con error o false)
		if($error) MiSiTiO::error(ACCESS_DENIED);
		return false;
	}

	/**
	 * Retorna los grupos del usuario en un arreglo
	 * @return Arreglo con los grupos del usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function grupos() {
		return self::$bd->getColumna("
			SELECT G.id as id, G.glosa
			FROM grupo AS G, usuario_grupo AS AG
			WHERE AG.usuario_id = '".$this->id."' AND AG.grupo_id = G.id
			ORDER BY G.glosa ASC;
		");
	}

	/**
	 * Establecer ip del visitante
	 * @return Ip del visitante
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-05-22
	 */
	final public function ip () {
		if(isset($_SERVER)) {
			return $_SERVER['REMOTE_ADDR'];
		} else {
			if(getenv('HTTP_X_FORWARDED_FOR')) {
				return getenv('HTTP_X_FORWARDED_FOR');
			} elseif(getenv('HTTP_CLIENT_IP')) {
				return getenv('HTTP_CLIENT_IP' );
			} else {
				return getenv('REMOTE_ADDR');
			}
		}
	}

	/**
	 * Establecer host del visitante
	 * @return Host del visitante
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-06
	 */
	final public function host () {
		return gethostbyaddr($this->ip());
	}

	/**
         * Crea un menú personalizado para el usuario
	 * @return Menú del usuario
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-06
         */
        final public function nav () {
        	$links = self::$bd->getTabla("SELECT url, nombre FROM enlace_usuario WHERE usuario_id = '".$this->id."' ORDER BY nombre ASC");
                $menuUsuario = '';
                if(count($links)) {
                        $menuUsuarioItem = '';
        		foreach($links as &$link) {
        			$menuUsuarioItem .= MiSiTiO::generar('menuUsuarioItem.html', $link);
                        }
                        $menuUsuario = MiSiTiO::generar('menuUsuario.html', array('menuUsuarioItem'=>$menuUsuarioItem));
                }
                unset($links, $link, $menuUsuarioItem);
                return $menuUsuario;
        }
	
	/**
	 * Muestra el curriculim del usurio (si existe)
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-04
	 */
	final public function cv () {
		$this->get();
		if($this->cvsize) {
			$cv['size'] = $this->cvsize;
			$cv['data'] = $this->cvdata;
			if(DB_TYPE=='postgresql') $cv['data'] = pg_unescape_bytea($cv['data']);
			$cv['type'] = $this->cvtype;
			$cv['name'] = $this->cvname;
		} else {
			$cv['size'] = null;
			$cv['data'] = "No CV\n";
			$cv['type'] = 'text/plain';
			$cv['name'] = 'no-cv.txt';
		}
		// limpiar buffer salida
		ob_clean();
		// envio cabeceras
		header('Cache-control: private');
		header('Content-Disposition: attachment; filename='.$cv['name']);
		header('Content-type: '.$cv['type']);
		header('Content-length: '.$cv['size']);
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		// mostrar cv
		print $cv['data'];
	}
	
	/**
	 * Muestra la imágen del usuario
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function avatar () {
		$this->get();
		if($this->avatarsize) {
			$foto['size'] = $this->avatarsize;
			$foto['data'] = $this->avatardata;
			if(DB_TYPE=='postgresql') $foto['data'] = pg_unescape_bytea($foto['data']);
			$foto['type'] = $this->avatartype;
			$foto['name'] = $this->avatarname;
		} else {
			$gestor = fopen(DIR.'/modulos/perfil/img/avatar-default.gif', 'rb');
			$foto['size'] = filesize(DIR.'/modulos/perfil/img/avatar-default.gif');
			$foto['data'] = fread($gestor, $foto['size']);
			$foto['type'] = mime_content_type(DIR.'/modulos/perfil/img/avatar-default.gif');
			$foto['name'] = 'avatar-default.gif';
			fclose($gestor);
		}
		// limpiar buffer salida
		ob_clean();
		// envio cabeceras
		header('Cache-control: private');
		header('Content-Disposition: attachment; filename='.$foto['name']);
		header('Content-type: '.$foto['type']);
		header('Content-length: '.$foto['size']);
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		// mostrar foto
		print $foto['data'];
	}
	
	/**
	 * Muestra la tarjeta/credencial del usuario
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-04
	 */
	final public function tarjeta () {
		// obtener datos del usuario
		$this->get();
		// obtener cargo
		$objCargo = $this->getCargo();
		// obtener casa matriz
		require(DIR.'/class/db/final/Sucursal.class.php');
		$objSucursals = new Sucursals();
		$matriz = $objSucursals->matriz();
		// obtener nombre de la comuna donde esta la casa matriz
		$objComuna = $matriz->getComuna();
		// obtener sucursal del usuario
		$objSucursal = $this->getSucursal();
		echo MiSiTiO::generar('tarjeta.html', array(
			'id'=>$this->id,
			'nombre'=>$this->nombre,
			'apellido'=>$this->apellido,
			'run'=>rut($this->id.rutDV($this->id)),
			'cargo'=>$objCargo->glosa,
			'sucursal'=>$objSucursal->glosa,
			'empresa'=>EMPRESA_RAZON_SOCIAL,
			'fantasia'=>EMPRESA_NOMBRE_FANTASIA,
			'direccion'=>$matriz->direccion,
			'comuna'=>$objComuna->nombre,
			'email'=>$matriz->email,
			'telefono'=>$matriz->telefono
		));
	}
	
	/**
	 * Muestra la ficha del usuario
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-04
	 */
	final public function ficha () {

		$this->get();
		require(DIR.'/class/PDF.class.php');
		$objPDF = new PDF('ficha-'.$this->id);
		$objPDF->setTitulo('Ficha de personal');
		$objPDF->begin();
		
		// obtener objetos relacionados con el usuario
		$objSalud = $this->getSalud();
		$objAfp = $this->getAfp();
		$objSucursal = $this->getSucursal();
		$objCargo = $this->getCargo();
		
		// datos personales
		$objPDF->pdf->ezText(utf8_decode('<b>Datos personales</b>'), 14);
		$objPDF->pdf->ezText('', 10);
		$objPDF->pdf->ezText(utf8_decode('<b>RUN</b>: '.rut($this->id.rutDV($this->id))), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Nombre</b>: '.$this->nombre), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Apellidos</b>: '.$this->apellido), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Fecha nacimiento</b>: '.formatearFecha($this->fechanacimiento)), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Salud</b>: '.($objSalud?$objSalud->nombre:'sin')), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>AFP</b>: '.($objAfp?$objAfp->nombre:'sin')), 10);
		$objPDF->pdf->ezText('', 14);
		
		// datos de la empresa
		$objPDF->pdf->ezText(utf8_decode('<b>Datos empresa</b>'), 14);
		$objPDF->pdf->ezText('', 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Ingreso</b>: '.formatearFecha($this->ingreso)), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Contrato inicio</b>: '.($this->contratoinicio!='0000-00-00'?formatearFecha($this->contratoinicio):'sin contrato')), 10);
		if($this->contratofin!='0000-00-00') $objPDF->pdf->ezText(utf8_decode('<b>Contrato fin</b>: '.formatearFecha($this->contratofin)), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Sucursal</b>: '.$objSucursal->glosa), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Cargo</b>: '.$objCargo->glosa), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Remuneración</b>: '.($this->remuneracion?num($this->remuneracion):'honorarios')), 10);
		$objPDF->pdf->ezText('', 14);
		
		// datos de contacto
		$objPDF->pdf->ezText(utf8_decode('<b>Datos contacto</b>'), 14);
		$objPDF->pdf->ezText('', 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Email</b>: '.$this->email), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Teléfono 1</b>: '.$this->telefono1), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Teléfono 2</b>: '.$this->telefono2), 10);
		$objPDF->pdf->ezText('', 14);
		
		// datos intranet
		$objPDF->pdf->ezText(utf8_decode('<b>Datos intranet</b>'), 14);
		$objPDF->pdf->ezText('', 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Usuario</b>: '.$this->usuario), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Lenguaje</b>: '.$this->lang), 10);
		$objPDF->pdf->ezText(utf8_decode('<b>Grupos</b>: '.implode(', ', $this->grupos())), 10);
		$objPDF->pdf->ezText('', 14);

		$objPDF->end();

	}

	/**
	 * Guardar al personal}
	 * @param campos Arreglo con los campos a guardar
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-04
	 */
	final public function savePersonal ($campos) {
		// setear id del usuario
		$this->set($campos);
		// se suben los datos del usuario
		if(empty($this->contratoinicio)) $this->contratoinicio = null;
		if(empty($this->contratofin)) $this->contratofin = null;
		if($this->exist()) {
			self::$bd->consulta("
			UPDATE usuario
			SET
				nombre=".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL').",
				apellido=".($this->apellido!==null?"'".self::$bd->proteger($this->apellido)."'":'NULL').",
				fechanacimiento=".($this->fechanacimiento!==null?"'".self::$bd->proteger($this->fechanacimiento)."'":'NULL').",
				activo=".($this->activo!==null?"'".self::$bd->proteger($this->activo)."'":'NULL').",
				sucursal_id=".($this->sucursal_id!==null?"'".self::$bd->proteger($this->sucursal_id)."'":'NULL').",
				cargo_id=".($this->cargo_id!==null?"'".self::$bd->proteger($this->cargo_id)."'":'NULL').",
				ingreso=".($this->ingreso!==null?"'".self::$bd->proteger($this->ingreso)."'":'NULL').",
				contratoinicio=".($this->contratoinicio!==null?"'".self::$bd->proteger($this->contratoinicio)."'":'NULL').",
				contratofin=".($this->contratofin!==null?"'".self::$bd->proteger($this->contratofin)."'":'NULL').",
				email=".($this->email!==null?"'".self::$bd->proteger($this->email)."'":'NULL').",
				telefono1=".($this->telefono1!==null?"'".self::$bd->proteger($this->telefono1)."'":'NULL').",
				telefono2=".($this->telefono2!==null?"'".self::$bd->proteger($this->telefono2)."'":'NULL').",
				remuneracion=".($this->remuneracion!==null?"'".self::$bd->proteger($this->remuneracion)."'":'NULL').",
				salud_id=".($this->salud_id!==null?"'".self::$bd->proteger($this->salud_id)."'":'NULL').",
				afp_id=".($this->afp_id!==null?"'".self::$bd->proteger($this->afp_id)."'":'NULL')."
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."
			");
		} else {
			self::$bd->consulta("
			INSERT INTO usuario (
				id,			
				nombre,
				apellido,
				fechanacimiento,
				activo,
				sucursal_id,
				cargo_id,
				ingreso,
				contratoinicio,
				contratofin,
				email,
				telefono1,
				telefono2,
				remuneracion,
				salud_id,
				afp_id
				, audit_programa
				, audit_usuario
				, audit_fechahora
			) VALUES (
				".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL').",
				".($this->nombre!==null?"'".self::$bd->proteger($this->nombre)."'":'NULL').",
				".($this->apellido!==null?"'".self::$bd->proteger($this->apellido)."'":'NULL').",
				".($this->fechanacimiento!==null?"'".self::$bd->proteger($this->fechanacimiento)."'":'NULL').",
				".($this->activo!==null?"'".self::$bd->proteger($this->activo)."'":'NULL').",
				".($this->sucursal_id!==null?"'".self::$bd->proteger($this->sucursal_id)."'":'NULL').",
				".($this->cargo_id!==null?"'".self::$bd->proteger($this->cargo_id)."'":'NULL').",
				".($this->ingreso!==null?"'".self::$bd->proteger($this->ingreso)."'":'NULL').",
				".($this->contratoinicio!==null?"'".self::$bd->proteger($this->contratoinicio)."'":'NULL').",
				".($this->contratofin!==null?"'".self::$bd->proteger($this->contratofin)."'":'NULL').",
				".($this->email!==null?"'".self::$bd->proteger($this->email)."'":'NULL').",
				".($this->telefono1!==null?"'".self::$bd->proteger($this->telefono1)."'":'NULL').",
				".($this->telefono2!==null?"'".self::$bd->proteger($this->telefono2)."'":'NULL').",
				".($this->remuneracion!==null?"'".self::$bd->proteger($this->remuneracion)."'":'NULL').",
				".($this->salud_id!==null?"'".self::$bd->proteger($this->salud_id)."'":'NULL').",
				".($this->afp_id!==null?"'".self::$bd->proteger($this->afp_id)."'":'NULL')."
				, '".AUDIT_PROGRAMA."'
				, '".AUDIT_USUARIO."'
				, NOW()
			)
			");
		}
		// si se subio un avatar se guarda
		if($campos['avatar']!==null) $this->saveAvatar($campos['avatar']);
		// si se subio un curriculum se guarda
		if($campos['cv']!==null) $this->saveCv ($campos['cv']);
		// siempre se devuelve true
		return true;
	}

	/**
	 * 
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-04
	 */
	final public function saveUsuario ($campos) {
		// setear id del usuario
		$this->set($campos);
		// si la contraseña no existe se borrara
		if(empty($this->clave)) $this->clave = null;
		// actualizar campos
		self::$bd->consulta("
			UPDATE usuario
			SET
				usuario=".($this->usuario!==null?"'".self::$bd->proteger($this->usuario)."'":'NULL').",
				clave=".($this->clave!==null?"'".md5(self::$bd->proteger($this->clave))."'":'NULL')."
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE id=".($this->id!==null?"'".self::$bd->proteger($this->id)."'":'NULL')."
		");
		// siempre se devuelve true
		return true;
	}

	/**
	 * Guarda el perfil del usuario
	 * @param campos Arreglo con los valores de los campos (indices) a insertar
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function savePerfil ($campos) {
		self::$bd->consulta("
			UPDATE usuario
			SET
				nombre = '".self::$bd->proteger($campos['nombre'])."'
				, apellido = '".self::$bd->proteger($campos['apellido'])."'
				, lang = '".self::$bd->proteger($campos['lang'])."'
				, email = '".self::$bd->proteger($campos['email'])."'
				, telefono1 = '".self::$bd->proteger($campos['telefono1'])."'
				, telefono2 = '".self::$bd->proteger($campos['telefono2'])."'
				, filasporpagina = '".self::$bd->proteger($campos['filasporpagina'])."'
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE id = '".self::$bd->proteger($this->id)."'
		");
	}

	/**
	 * Método para guardar el avatar del usuario
	 * @param file Archivo subido por un formulario con la imagen
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function saveAvatar ($file) {
		// subir archivo
		require(DIR.'/class/Archivo.class.php');
		$foto = Archivo::upload(
			$file
			, explode(',', AVATAR_MIMETYPE)
			, AVATAR_SIZE_KB
			, AVATAR_SIZE_W
			, AVATAR_SIZE_H
		);
		// verificar en casos de errores
		if(!is_array($foto)) {
			if($foto==1) MiSiTiO::error(LANG_ERROR_FILE_SIZE.' '.AVATAR_SIZE_KB.'[KB]');
			else if($foto==2) MiSiTiO::error(LANG_ERROR_FILE_IMAGESIZE.' '.AVATAR_SIZE_W.'x'.AVATAR_SIZE_H.'[px]');
			else if($foto==3) MiSiTiO::error(LANG_ERROR_FILE_TYPE);
			else if($foto==4) MiSiTiO::error(LANG_ERROR_FILE_UPLOAD);
		}
		// si es un arreglo (o sea la foto si se subio) se guarda
		if(DB_TYPE=='postgresql') $foto['data'] = pg_escape_bytea($foto['data']);
		else $foto['data'] = self::$bd->proteger($foto['data']);
		self::$bd->consulta("
			UPDATE usuario
			SET
				avatardata = '".$foto['data']."'
				, avatarname = '".self::$bd->proteger($foto['name'])."'
				, avatartype = '".self::$bd->proteger($foto['type'])."'
				, avatarsize = '".self::$bd->proteger($foto['size'])."'
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE id = '".$this->id."'
		");
	}
	
	/**
	 * Método para guardar el curriculum del usuaro
	 * @param file Archivo subido por un formulario con el curriculum
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function saveCv ($file) {
		$cv = Archivo::upload(
			$file
			, explode(',', CV_MIMETYPE)
			, CV_SIZE_KB
		);
		// verificar en casos de errores
		if(!is_array($cv)) {
			if($cv==1) MiSiTiO::error(LANG_ERROR_FILE_SIZE.' '.CV_SIZE_KB.'[KB]');
			if($cv==3) MiSiTiO::error(LANG_ERROR_FILE_TYPE);
			if($cv==4) MiSiTiO::error(LANG_ERROR_FILE_UPLOAD);
		}
		// si es un arreglo (o sea la foto si se subio) se guarda
		if(DB_TYPE=='postgresql') $cv['data'] = pg_escape_bytea($cv['data']);
		else $cv['data'] = self::$bd->proteger($cv['data']);
		self::$bd->consulta("
			UPDATE usuario
			SET
				cvdata = '".$cv['data']."'
				, cvname = '".self::$bd->proteger($cv['name'])."'
				, cvtype = '".self::$bd->proteger($cv['type'])."'
				, cvsize = '".self::$bd->proteger($cv['size'])."'
				, audit_programa = '".AUDIT_PROGRAMA."'
				, audit_usuario = '".AUDIT_USUARIO."'
				, audit_fechahora = NOW()
			WHERE id = '".self::$bd->proteger($this->id)."'
		");
	}
	
	/**
	 * Cambia la clave a un usuario
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function saveClave ($claveNueva) {
		self::$bd->consulta("UPDATE usuario SET clave = MD5('".$claveNueva."') WHERE id = ".self::$bd->proteger($this->id));
	}
	
	/**
	 * Evitar que se borren usuarios que se encuentrn activos
	 * @return =true en caso que se pueda borrar el registro
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function beforeDelete() {
		return (boolean) !self::$bd->getValor("SELECT activo FROM usuario WHERE id =".self::$bd->proteger($this->id));
	}
	
}

/**
 * Usuarios para trabajo con listado de objetos de la tabla usuario
 * Usuarios del sistema y personal de la empresa
 * Esta clase permite ampliar las funcionalidades provistas por BaseUsuarios
 * Cualquier nuevo código debe ser colocado en esta clase NO en la abstracta
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-27
 */
final class Usuarios extends BaseUsuarios {

	/**
	 * Constructor de la clase Usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-27
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
	 * Destructor de la clase Usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-27
	 */
	final public function __destruct () {
		parent::__destruct ();
	}

	/**
	 * Devuelve una tabla de 2 columnas y n filas con la PK y una glosa
	 * de los elementos de la tabla usuario, esta servirá para ser utilizada
	 * en, por ejemplo, Form::select()
	 * @return Array Listado de elementos de la tabla usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-27
	 */
	final public function listado () {
		$this->clear();
		$this->setSelectStatement('id, '.self::$bd->concat('apellido', ', ', 'nombre').' as glosa');
		$this->setWhereStatement('activo = 1');
		$this->setOrderByStatement('apellido, nombre');
		return $this->getTabla();
	}
	
	/**
	 * Tabla con los datos de usuario, hora y última página visitada
	 * @return Arreglo con la tabla
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function ultimoAcceso () {
		$ultimoAcceso = self::$bd->getTabla("
			SELECT usuario, ultimoacceso, ultimapagina
			FROM usuario
			WHERE activo = 1 AND ultimoacceso >= '0001-01-01 00:00:00'
			ORDER BY ultimoacceso DESC, usuario ASC
		");
		// en postgresql la fecha/hora guarda mayor presicion que mysql
		// se debe quitar por lo tanto esta presicion (valor después del
		// punto en la hora)
		if(DB_TYPE=='postgresql') {
			foreach($ultimoAcceso as &$fila) {
				$aux = explode('.', $fila['ultimoacceso']);
				$fila['ultimoacceso'] = array_shift($aux);
			}
		}
		return $ultimoAcceso;
	}
	
	/**
	 * Directorio de usuarios con sus datos de contacto
	 * @return Arreglo con la tabla
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function directorio () {
		return self::$bd->getTabla("
			SELECT
				u.usuario
				, ".self::$bd->concat('u.nombre',"' '",'u.apellido')." AS nombre
				, s.glosa AS sucursal
				, c.glosa AS cargo
				, u.email
				, u.telefono1
				, u.telefono2
			FROM
				usuario AS u
				, cargo AS c
				, sucursal AS s
			WHERE
				u.activo = 1
				AND s.id = u.sucursal_id
				AND c.id = u.cargo_id
			ORDER BY u.apellido ASC, u.nombre ASC
		");
	}

	/**
	 * Devuelve una tabla con los usuarios activos y sus edades
	 * @return Arreglo con las edades y usuarios por cada edad
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function grupoEtario () {
		return self::$bd->getTabla("
			SELECT f_edad(fechanacimiento) AS edad, COUNT(*) as cantidad
			FROM usuario
			WHERE activo = 1
			GROUP BY edad
			ORDER BY edad
		");
	}
	
	/**
	 * Obtiene una lista con los próximos cumpleaños
	 * Esta realizado como SP ya que en las diferentes bases de datos la forma
	 * de convertir las fechas es diferente
	 * @return Arreglo con los cumpleanios
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-04-07
	 */
	final public function cumpleanios () {
		return self::$bd->getTabla('sp_cumpleanios', 'c_result');
	}
	
}

}

?>
