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

// desactivar Strict Mode (errores con la clase de Mail)
ini_set('error_reporting', E_ALL);

// parametros de configuracion que se encuentran en la base de datos (tabla: parametro)
if(isset($Parametros)) $Parametros->getByModulo('email');

// clases PEAR
require('/usr/share/php/Mail.php');
require('/usr/share/php/Mail/mime.php');

/**
 * Permite el manejo de correo electrónico
 *
 * Esta clase permite realizar una conexión a un servidor SMTP
 * y realizar el envio de correo electrónico, tanto en formato HTML
 * como TXT, además de permitir enviar tantos archivos adjuntos como
 * deseemos.
 * Requiere PEAR Net/SMTP
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-07-18
 */
final class Email {

	private $debug; ///< Activa/desactiva modo de debugueo en la clase
	private $parametrosConexion; ///< Arreglo con parámetros para realizar la conexión al servidor
	private $desde; ///< Arreglo con la información del remitente
	private $para; ///< Arreglo de arreglos con la información de los destinatarios
	private $asunto; ///< Asunto del mensaje de correo electrónico
	private $mensajeTxt; ///< Cuerpo del mensaje en texto plano
	private $mensajeHtml; ///< Cuerpo del mensaje en html
	private $adjuntos; ///< Arreglo de archivos adjuntos

	/**
	 * Constructor
	 *
	 * Inicializa atributos: para como arreglo, debug con valor entregado al
	 * método, adjuntos como null.
	 * @param debug Permite activar el modo debug de la clase (por defecto false)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-06
	 */
	final public function __construct ($debug = false) {
		$this->para = array();
		$this->debug = $debug;
		$this->adjuntos = null;
		unset($debug);
		// si están los parámetros de email se cargar como datos
		// por defecto, si no fueran los datos por defecto los
		// que se requieran utilizar se deberá llamar a los
		// métodos datosConexion y desde de forma manual
		if(defined('EMAIL_HOST') && defined('EMAIL_USER') && defined('EMAIL_PASS') && EMAIL_HOST!='' && EMAIL_USER!='' && EMAIL_PASS!='') {
			$this->datosConexion(EMAIL_HOST, EMAIL_USER, EMAIL_PASS);
			if(defined('EMAIL_FROM_NAME') && EMAIL_FROM_NAME!='')
				$this->desde(EMAIL_FROM_NAME, EMAIL_USER);
			else
				$this->desde(EMAIL_USER, EMAIL_USER);
		}
	}

	/**
	 *  Crea parámetros para utilizar al momento de conectar
	 * @param servidor Url del servidor, puede incluir puerto (servidor:puerto)
	 * @param usuario Nombre de usuario para la cuenta
	 * @param clave Clave para la cuenta del usuario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-14
	 */
	final public function datosConexion($servidor, $usuario, $clave) {
		$aux = explode(':', $servidor);
		if(count($aux)==1) $aux[1] = '25';
		$this->parametrosConexion = array(
			'host'		=> $aux[0],
			'port'		=> $aux[1],
			'auth'		=> true,
			'username'	=> $usuario,
			'password'	=> $clave,
			'debug'		=> $this->debug
		);
		unset($servidor, $usuario, $clave, $aux);
	}

	/**
	 * Crea arreglo con datos del remitente
	 * @param fromname Nombre del remitente
	 * @param fromemail Correo electrónico del remitente
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-05-22
	 */
	final public function desde ($fromname, $fromemail) {
		$this->desde = array(
			'fromname'	=> $fromname,
			'fromemail'	=> $fromemail,
		);
		unset($fromname, $fromemail);
	}

	/**
	 * Crea arreglo con datos del destinatario
	 *
	 * Permite el envio a múltiples cuentas de destino, por lo cual
	 * este método puede ser llamado varias veces
	 * @param toname Nombre del destinatario
	 * @param toemail Correo electrónico del destinatario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-05-22
	 */
	final public function para ($toname, $toemail) {
		$paraAux = array(
			'toname'	=> $toname,
			'toemail'	=> $toemail,
		);
		array_push($this->para, $paraAux);
		unset($toname, $toemail, $paraAux);
	}

	/**
	 * Setea el valor del atributo asunto
	 * @param asunto Asunto del correo electrónico
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-06
	 */
	final public function asunto ($asunto) {
		if(defined('EMAIL_SUBJECT_PREFIX') && EMAIL_SUBJECT_PREFIX!='')
			$this->asunto = EMAIL_SUBJECT_PREFIX.' ';
		else
			$this->asunto = '';
		$this->asunto .= $asunto;
		unset($asunto);
	}

	/**
	 * Setea el valor de los mensajes en texto plano y en html
	 *
	 * Si no se indica un texto en html se repetirá el texto plano
	 * @param txt Mensaje en texto plano
	 * @param html Mensaje en html
	 * @todo Procesar parámetro $txt al menos por saltos de linea
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-05
	 */
	final public function mensaje ($txt, $html = null) {
		$this->mensajeTxt = $txt;
		if($html) $this->mensajeHtml = $html;
		unset($txt, $html);
	}

	/**
	 * Adjunta un archivo al correo electrónico
	 *
	 * Permite adjuntar múltiples archivos, por lo cual
	 * este método puede ser llamado varias veces
	 * @param src Archivo adjunto, elemento del arreglo $_FILES
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-17
	 */
	final public function adjuntar (&$src) {
		$adjunto = array($src['tmp_name'], $src['type'], $src['name']);
		unset($src);
		if(!is_array($this->adjuntos)) $this->adjuntos = array();
		array_push($this->adjuntos, $adjunto);
		unset($adjunto);
	}

	/**
	 * Método que envía el mensaje
	 *
	 * Este método crea el objeto para enviar el mensaje, setea las variables necesarias
	 * adjunta archivos y envia el mensaje
	 * @param confirmacion Si es true mostrará un mensaje por cada email enviado (o no)
	 * @return Array Arreglo con índice los correos y valor si se envio o no (boolean)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-06
	 */
	final public function enviar ($confirmacion = false) {
		$mailer = Mail::factory('smtp', $this->parametrosConexion);
		$mail = new Mail_mime();
		// set parametros en mail
		$mail->setSubject($this->asunto);
		$mail->setFrom("{$this->desde['fromname']} <{$this->desde['fromemail']}>");
		$mail->setTXTBody($this->mensajeTxt);
		$mail->setHTMLBody($this->mensajeHtml);
		if(is_array($this->adjuntos)) {
			foreach($this->adjuntos as $adjunto) {
				$mail->addAttachment($adjunto[0], $adjunto[1], $adjunto[2]);
			}
		}
		// mensaje y cabecera
		$mail->_build_params['text_encoding'] = '8bit';
		$mail->_build_params['text_charset'] = 'UTF-8';
		$mail->_build_params['html_charset'] = 'UTF-8';
		$mail->_build_params['head_charset'] = 'UTF-8';
		$mail->_build_params['head_encoding'] = '8bit';
		$body = $mail->get(); // debe llamarse antes de headers
		$headers = $mail->headers();
		// enviar email
		$status = array();
		foreach($this->para as $dest) {
			$mailer->send($mail->encodeRecipients($dest['toemail']), $headers, $body);
			// verificar envio
			if (PEAR::isError($mailer)) {
				if($confirmacion) $status[$dest['toemail']] = false;
				echo MiSiTiO::generar('parrafo.html', array('txt'=>$mail->getMessage()));
			} else {
				if($confirmacion) echo MiSiTiO::generar('parrafo.html', array('txt'=>LANG_EMAIL_MSGSENT.' '.$dest['toemail']));
				$status[$dest['toemail']] = true;
			}
		}
		unset($confirmacion, $mailer, $mail, $_to, $_msg, $_head);
		return $status;
	}

}

?>
