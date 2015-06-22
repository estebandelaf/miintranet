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
 * Conexión y trabajo con la base de datos
 *
 * Clase para la conexión y consultas a una base de datos Oracle,
 * permite realizar diversas formas para consultar la base de datos.
 * 
 * Se siguieron los pasos del siguiente tutorial para hacer funcionar oci8:
 * http://www.pensandoenred.com/2009/01/22/apache22-php5-oci-cliente-oracle-en-debian/
 * 
 * Para obtener SID de la base de datos desde comando sql utilizar:
 * select instance_name from v$instance;
 *
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-20
 */

final class Oracle implements BD {

	private $servidor; ///< Nombre del servidor
	private $basedatos; ///< Nombre de la base de datos
	private $usuario; ///< Nombre de usuario con permisos para la base de datos
	private $clave; ///< Clave del usuario
        private $puerto; ///< Puerto donde el servidor de base de datos escucha conexiones entrantes
        private $charset; ///< Juego de caracteres usado por las librerías cliente de la base de datos
	private $conexionId; ///< Identificador de la conexion
	public static $conexiones; ///< Almacena cuantos objetos se han creado a partir de esta clase y no se han destruido

	/**
	 * Constructor
	 *
	 * Inicializa atributos: servidor, basedatos, usuario y clave
	 * @param servidor servidor
	 * @param basedatos base de datos
	 * @param usuario usuario
	 * @param clave clave del usuario
         * @param puerto puerto donde escucha el servidor de base de datos
         * @param charset juego de caracteres a utilizar
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-02-17
	 */
	public function __construct ($servidor = '', $basedatos = '', $usuario = '', $clave = '', $puerto = '1521', $charset = 'utf8') {
		$this->servidor = $servidor;
		$this->basedatos = $basedatos;
		$this->usuario = $usuario;
		$this->clave = $clave;
                $this->puerto = $puerto;
                $this->charset = $charset;
		$this->conexionId = null;
		$this->db = null;
		self::$conexiones = (!isset(self::$conexiones) || self::$conexiones==null) ? 1 : ++self::$conexiones;
		unset($servidor, $basedatos, $usuario, $clave, $puerto, $charset);
	}

	/**
	 * Destructor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-15
	 */
	public function __destruct() {
		--self::$conexiones;
		if($this->conexionId) $this->cerrar();
	}

	/**
	 * Realiza la conexion a la base de datos
	 *
	 * Actualiza $conexionId
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-01
	 */
	private function conectar () {
		// se crea objeto para conectar al servidor
		$this->conexionId = oci_connect($this->usuario, $this->clave, $this->servidor.':'.$this->puerto.'/'.$this->basedatos, $this->charset);
		// ser verifican posibles errores
		if (!$this->conexionId) {
			$error = oci_error();
			MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.LANG_DB_CANTCONNECT.'<br />'.$error['message']);
		}
	}

	/**
	 * Cierra la conexión a MySQL
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-01
	 */
	private function cerrar () {
		if(isset($this->conexionId) && $this->conexionId!=null) {
			if(!oci_close($this->conexionId)) {
				MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.LANG_DB_CANTCLOSE);
			}
		}
	}

	/**
	 * Realiza consultas a la base de datos
	 *
	 * Si no existe una conexión se conecta a la base de datos
	 * @param sql Consulta SQL
	 * @param optimizar Si es true se dará prioridad a los select y se encolarán los insert
	 * @return Result de la consulta
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-02
	 */
	public function consulta ($sql = '', $optimizar = false) {
		// se verifica que se ha pasado una sentencia sql
		if (empty($sql)) {
			$this->cerrar();
			MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.LANG_DB_EMPTY);
		}
		// se verifica que la base de datos este conectada
		if($this->conexionId==null) {
			$this->conectar();
		}
		// dar prioridad a los select y encolar los insert
		if($optimizar) {
                        // buscar algo como esto (de mysql) para oracle
			//$sql = str_replace('INSERT', 'INSERT LOW_PRIORITY', $sql);
			//$sql = str_replace('SELECT', 'SELECT HIGH_PRIORITY', $sql);
		}
		// se prepara la consulta
		$queryId = oci_parse($this->conexionId, $sql);
		// se verifican errores por oci_parse
		if (!$queryId) {
			$error = oci_error($this->conexionId);
			MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.$error['message'].'<br />'.$error['sqltext']);
		}
		// se realiza la consulta
		$queryRs = oci_execute($queryId);
		// se verifican errores por oci_execute
		if (!$queryRs) {
			$error = oci_error($queryId);
			MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.$error['message'].'<br />'.$error['sqltext']);
		}
		// se retorna el id de la consulta
		unset($sql, $optimizar);
		return $queryId;
	}

	/**
	 * Devuelve los resultados de una consulta, filas y columnas
	 * @param sql Consulta SQL
	 * @return Arreglo con las filas y columnas de la consulta
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-02-17
	 */
	public function getTabla ($sql = '') {
		$result = array();
		$queryId = $this->consulta($sql);
		while (($row = oci_fetch_array($queryId, OCI_ASSOC + OCI_RETURN_NULLS))) {
			array_push($result, $row);
		}
		oci_free_statement($queryId);
		unset($sql);
		return $result;
	}

	/**
	 * Devuelve una fila desde una consula
	 * @param sql Consulta SQL
	 * @return Arreglo con la fila de la consulta o false si no encuentra nada
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-02
	 */
	public function getFila ($sql = '') {
		return oci_fetch_array($this->consulta($sql), OCI_ASSOC + OCI_RETURN_NULLS);
	}

	/**
	 *  Devuelde una columna desde una consulta
	 * @param sql Consulta SQL
	 * @return Arreglo con los valores de la columna seleccionada
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-02
	 */
	public function getColumna($sql = '') {
		$result = array();
		$queryId = $this->consulta($sql);
		while($row = oci_fetch_array($queryId, OCI_ASSOC + OCI_RETURN_NULLS)) {
			array_push($result, array_pop($row));
		}
		oci_free_statement($queryId);
		unset($sql);
		return $result;
	}

	/**
	 *  Devuelve un valor desde una consula
	 * @param sql Consulta SQL
	 * @return String con el valor devuelto por la consulta
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-02
	 */
	public function getValor($sql = '') {
		$row = oci_fetch_row($this->consulta($sql));
		unset($sql);
		return is_array($row) ? array_pop($row) : '';
	}

	/**
	 *  Método para parsear valores pasados a mysql y evitar sql injection
	 * @param txt Texto a parsear
	 * @return Texto parseado
	 * @todo Método que protega las consultas
	 * @warning este método NO protege, ya que devuelve $txt tal cual
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-02
	 */
	public function proteger($txt = '') {
		// se verifica que la base de datos este conectada
		//if($this->conexionId==null) $this->conectar();
		// se proteje
		return $txt;
	}

	/**
	 * Asigna un límite para la obtención de filas en la consulta
	 * Se utiliza este método ya que la forma de hacer el limit depende de
	 * la base de datos que se este utilizando
	 * @param query Consulta a la que se le aplicará el limit
	 * @param records Registros que se desea retornar
	 * @param offset Desde que registro se partira
	 * @return String Consulta con el limit aplicado
	 * @todo Verificar que el método funcione y si offset por defecto no es 0 sumarselo dentro del metodo (se usara estilo mysql)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-27
	 */
	final public function setLimit ($query, $records, $offset = 0) {	
		return 'SELECT * FROM ('.$query.') WHERE ROWNUM >= '.$this->proteger($offset).' AND ROWNUM <= '.($this->proteger($offset)+$this->proteger($records));
	}

	/**
	 * Concatena elemento 1 y 2, se pueden pasar n elementos
	 * Posibles elementos de union: ' ' ',' ', ' '-'
	 * @param par1 Elemento 1
	 * @param par2 Elemento 2
	 * @return String con la forma de concatenar especifica de la base de datos
	 * @todo Programar metodo para Oracle y verificar que funcione
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-27
	 */
	public function concat($par1, $par2) {
		$concat = array();
		$parameters = func_get_args();
		foreach($parameters as &$parameter) {
			if($parameter==' ' || $parameter==',' || $parameter==', ' || $parameter=='-' || $parameter==' - ')
				$parameter = "'".$parameter."'";
			array_push($concat, $parameter);
		}
		return '';
	}
	
	/**
	 * Genera filtro para utilizar like en la consulta SQL
	 * @param col Columna por la que se filtrará
	 * @param value Valor a buscar mediante like
	 * @return String Filtro like
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-20
	 */
	public function like ($col, $value) {
		return "$col LIKE '%".$this->proteger($value)."%'";
	}
	
	/**
	 *  Recupera toda la información a partir del resultado de una consulta SQL
	 *
	 * Formatea el resultado de la consulta para ser utilizado en la generación automática
	 * de una tabla, incluyendo los títulos de las columnas.
	 * @param sql Consulta SQL
	 * @todo Verificar que efectivamente exista campo de tipo blob y sea ese el que no se muestra en la web
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-03
	 */
	public function select4table($sql = '') {
		$data = array();
		$keys = array();
		$queryId = $this->consulta($sql);
		$ncolumnas = oci_num_fields($queryId);
		for($i=1; $i<=$ncolumnas; ++$i)
			array_push($keys, oci_field_name($queryId, $i));
		array_push($data, $keys);
		unset($keys);
		while($rows = oci_fetch_array($queryId, OCI_NUM + OCI_RETURN_NULLS)) {
			$row = array();
			for($i=1; $i<=$ncolumnas; ++$i) {
				if(preg_match('/blob/i', oci_field_type($queryId, $i))) // si es un blob no se muestra el contenido en la web
					array_push($row, '['.oci_field_type($queryId, $i).']');
				else
					array_push($row, $rows[($i-1)]);
			}
			array_push($data, $row);
		}
		oci_free_statement($queryId);
		unset($sql, $nfilas, $i, $value, $row);
		return $data;
	}

	/**
	 *  Exportar una consulta a un archivo csv y descargar
	 *
	 * La cantidad de campos seleccionados en la consulta debe ser igual
	 * al largo del arreglo de columnas
	 * @param consulta Consulta SQL
	 * @param columnas Arreglo con los nombres de las columnas a utilizar en la tabla
	 * @param archivo Nombre para el archivo que se descargara
         * @todo Probar que funcione
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-30
	 */
	public function tocsv ($consulta, $columnas, $archivo) {
		// realizar consulta
		/*$this->consulta('
			SELECT "'.implode('", "', $columnas).'"
			UNION
			'.$consulta.'
			INTO OUTFILE "'.TMP.'/'.$archivo.'.csv"
				FIELDS TERMINATED BY ";"
				LINES TERMINATED BY "\r\n"
		');*/
		$this->consulta("
			begin
			set colsep ,
			set pagesize 0
			set trimspool on
			set headsep off
			set linesize 200
			spool ".TMP."/".$archivo.".csv
			".$this->proteger($consulta).";
			spool off
			end;
		");
		// enviar archivo
		ob_clean();
		header ('Content-Disposition: attachment; filename='.$archivo.'.csv');
		header ('Content-Type: text/csv');
		header ('Content-Length: '.filesize(TMP.'/'.$archivo.'.csv'));
		readfile(TMP.'/'.$archivo.'.csv');
	}

	/**
	 * Busca la lista de tabla de la base de datos
	 * @return Arreglo con los indices: name y comment
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-23
	 */
	public function tablas ($database = DB_NAME) {
		
	}

	/**
	 * Busca la informacion de la tabla
	 * @param tabla Nombre de la tabla de la que se busca la informacion
	 * @return Arreglo con los indices: 
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-23
	 */
	public function tablaInfo ($tabla, $database = DB_NAME) {
		
	}
	
        /**
         * Selecciona los datos desde una vista y los devuelve como tabla (idem self::getTabla)
         * @param view  Nombre de la vista que se utilizara
         * @param limit Columna desde donde se mostrara
         * @param offset Cantidad de columnas a mostrar
         * @todo Probar que funcione
         * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-09
         */
        public function view ($view, $limit = null, $offset = null) {
                if($limit!==null && $offset!=null) {
                        return $this->getTabla('
                                SELECT *
                                FROM '.$this->proteger($view).'
                                LIMIT '.$this->proteger($limit).','.$this->proteger($offset)
                        );
                } else {
                        return $this->getTabla('SELECT * FROM '.$this->proteger($view));
                }
        }

	/**
         * Ejecuta una funcion y recupera el valor devuelta por esta
         * @param function Nombre de la funcion a ejecutar
         * @param par1  Parametros pasados a la funcion: par1, par2, par3, etc
	 * @todo Probar que funcione
         * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-13
         */
        public function funcion ($function, $par1 = null) {
                $parameters = func_get_args(); // recoger todos los parametros de la funcion
                $funcion = $this->proteger(array_shift($parameters)); // quitar nombre de la funcion
                foreach($parameters as &$parameter) $parameter = $this->proteger($parameter); // proteger
                $parameters = $par1!==null ? "'".implode("', '", $parameters)."'" : ''; // armar lista de argumentos
                return $this->getValor("SELECT $funcion($parameters)"); // ejecutar funcion
        } 
	
        /**
         * Ejecuta un procedimiento almacenado en la base de datos
         * La función tiene 2 argumentos: nombre sp y parametro, pero pueden haber n
         * argumentos donde seran n-1 parametros para el sp (-1 ya que el primero es el sp)
         * @param procedure Nombre del procedimiento almacenado
         * @param par1 Parametros pasados al SP: par1, par2, par3, etc
         * @param Probar que funcione
         * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-10
         */
        public function procedure ($procedure, $par1 = null) {
                $parameters = func_get_args(); // recoger todos los parametros de la funcion
                $procedure = array_shift($parameters); // quitar nombre del sp
                foreach($parameters as &$parameter) $parameter = $this->proteger($parameter); // proteger
                $parameters = $par1!==null ? "'".implode("', '", $parameters)."'" : ''; // armar lista de argumentos
                $this->consulta("CALL $procedure($parameters)"); // ejecutar sp
        }

	public function transaction () {}
	public function commit () {}
	public function rollback () {}

}

?>
