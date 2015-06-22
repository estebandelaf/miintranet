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
 * Clase para la conexión y consultas a una base de datos PostgreSQL,
 * permite realizar diversas formas para consultar la base de datos.
 * Notar que requiere la utilizacion del paquete (en debian) php5-pgsql
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-21
 */

final class PostgreSQL implements BD {

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
	public function __construct ($servidor = '', $basedatos = '', $usuario = '', $clave = '', $puerto = '5432', $charset = 'utf8') {
		$this->servidor = $servidor;
		$this->basedatos = $basedatos;
		$this->usuario = $usuario;
		$this->clave = $clave;
                $this->puerto = $puerto;
                $this->charset = $charset;
		$this->conexionId = null;
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
	 * @version 2010-07-30
	 */
	private function conectar () {
		// se realiza la conexión con el servidor
                $this->conexionId = pg_connect("host=$this->servidor port=$this->puerto user=$this->usuario password=$this->clave dbname=$this->basedatos options='--client_encoding=$this->charset'");
		if (!$this->conexionId) {
			MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.LANG_DB_CANTCONNECT);
		}
	}

	/**
	 * Cierra la conexión a MySQL
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-16
	 */
	private function cerrar () {
		if(isset($this->conexionId) && $this->conexionId!=null) {
			if(!pg_close($this->conexionId)) {
				//MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.LANG_DB_CANTCLOSE);
			}
		}
	}

	/**
	 * Realiza consultas a la base de datos
	 *
	 * Si no existe una conexión se conecta a la base de datos
	 * @param sql Consulta SQL
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-07
	 */
	public function consulta ($sql = '') {
		// se verifica que se ha pasado una sentencia sql
		if (empty($sql)) {
			$this->cerrar();
			MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.LANG_DB_EMPTY);
		}
		// se verifica que la base de datos este conectada
		if($this->conexionId==null) {
			$this->conectar();
		}
		// se realiza la consulta
		$queryId = pg_query($this->conexionId, $sql);
		if (!$queryId) {
			MiSiTiO::error(DB_ERROR, LANG_DB_ERROR.' '.pg_last_error($this->conexionId).'<br /><br />'.$sql);
		}
		unset($sql, $optimizar);
		return $queryId;
	}

	/**
	 * Devuelve los resultados de una consulta, filas y columnas
	 * @param sql Consulta SQL
	 * @return Arreglo con las filas y columnas de la consulta
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-07
	 */
	public function getTabla ($par1 = '') {
		$data = array();
		$parameters = func_get_args(); // obtener parametros pasados
                $par1 = array_shift($parameters); // quitar nombre de la funcion
		// ejecutar procedimiento almacenado
		if(substr($par1, 0, 3)=='sp_') {
			$procedure = $par1;
			$c_result = isset($parameters[0]) ? $parameters[0] : '';
			foreach($parameters as &$parameter) $parameter = $this->proteger($parameter); // proteger
			$parameters = isset($parameters[0]) ? "'".implode("', '", $parameters)."'" : ''; // armar lista de argumentos
			// si se paso el c_result significa que el procedimiento devuelve algo
			if($c_result=="c_result") {
				$queryId = $this->consulta("BEGIN; SELECT * FROM $procedure($parameters); FETCH ALL FROM c_result;");
				$data = pg_fetch_all($queryId);
				$this->consulta("END;");
			// sino hay c_result solo ejecutar el procedimiento
			} else {
				$this->consulta("SELECT $procedure($parameters)");
			}
		// ejecutar consulta sql
		} else {
			$sql = $par1;
			$queryId = $this->consulta($sql);
			while($row = pg_fetch_array($queryId, null, PGSQL_ASSOC)) {
				array_push($data, $row);
			}
			pg_free_result($queryId);
			unset($sql);
		}
		return $data;
	}

	/**
	 * Devuelve una fila desde una consula
	 * @param sql Consulta SQL
	 * @return Arreglo con la fila de la consulta o false si no encuentra nada
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-02-02
	 */
	public function getFila ($par1 = '') {
		$data = array();
		$parameters = func_get_args(); // obtener parametros pasados
                $par1 = array_shift($parameters); // quitar nombre de la funcion
		// ejecutar procedimiento almacenado
		if(substr($par1, 0, 3)=='sp_') {
			
		// ejecutar consulta sql
		} else {
			$sql = $par1;
			$data = pg_fetch_array($this->consulta($sql), null, PGSQL_ASSOC);
		}
		return $data;
	}

	/**
	 * Devuelde una columna desde una consulta
	 * @param sql Consulta SQL
	 * @return Arreglo con los valores de la columna seleccionada
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-02-02
	 */
	public function getColumna($par1 = '') {
		$data = array();
		$parameters = func_get_args(); // obtener parametros pasados
                $par1 = array_shift($parameters); // quitar nombre de la funcion
		// ejecutar procedimiento almacenado
		if(substr($par1, 0, 3)=='sp_') {
			
		// ejecutar consulta sql
		} else {
			$sql = $par1;
			$queryId = $this->consulta($sql);
			while($row = pg_fetch_array($queryId, null, PGSQL_ASSOC)) {
				array_push($data, array_pop($row));
			}
			pg_free_result($queryId);
			unset($sql);
		}
		return $data;
	}

	/**
	 * Devuelve un valor desde una consula
	 * @param sql Consulta SQL
	 * @return String con el valor devuelto por la consulta
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-02-02
	 */
	public function getValor($par1 = '') {
		$data = array();
		$parameters = func_get_args(); // obtener parametros pasados
                $par1 = array_shift($parameters); // quitar nombre de la funcion
		// ejecutar procedimiento almacenado
		if(substr($par1, 0, 3)=='sp_') {
			
		// ejecutar consulta sql
		} else {
			$sql = $par1;
			$row = pg_fetch_row($this->consulta($sql));
			$data = is_array($row) ? array_pop($row) : '';
			unset($sql);
		}
		return $data;
	}

	/**
	 * Método para parsear valores pasados a mysql y evitar sql injection
	 * @param txt Texto a parsear
	 * @return Texto parseado
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-05-22
	 */
	public function proteger($txt = '') {
		// se verifica que la base de datos este conectada
		if($this->conexionId==null) $this->conectar();
		// se proteje
		return pg_escape_string($this->conexionId, $txt);
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
		return $query.' LIMIT '.$this->proteger($records).' OFFSET '.$this->proteger($offset);
	}
	
	/**
	 * Concatena elemento 1 y 2, se pueden pasar n elementos
	 * Posibles elementos de union: ' ' ',' ', ' '-'
	 * @param par1 Elemento 1
	 * @param par2 Elemento 2
	 * @return String con la forma de concatenar especifica de la base de datos
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-07
	 */
	public function concat ($par1, $par2) {
		$concat = array();
		$parameters = func_get_args();
		foreach($parameters as &$parameter) {
			if($parameter==' ' || $parameter==',' || $parameter==', ' || $parameter=='-' || $parameter==' - ' || $parameter=='|')
				$parameter = "'".$parameter."'";
			array_push($concat, $parameter);
		}
		return implode(' || ', $concat);
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
		return "$col ILIKE '%".$this->proteger($value)."%'";
	}
	
	/**
	 * Recupera toda la información a partir del resultado de una consulta SQL
	 *
	 * Formatea el resultado de la consulta para ser utilizado en la generación automática
	 * de una tabla, incluyendo los títulos de las columnas.
	 * @param sql Consulta SQL
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-03
	 */
	public function select4table($sql = '') {
		$data = array();
		$keys = array();
		$queryId = $this->consulta($sql);
		$ncolumnas = pg_num_fields($queryId);
		for($i=0; $i<$ncolumnas; ++$i)
			array_push($keys, pg_field_name($queryId, $i));
		array_push($data, $keys);
		unset($keys);
		while($rows = pg_fetch_array($queryId)) {
			$row = array();
			for($i=0; $i<$ncolumnas; ++$i) {
				if(preg_match('/blob/i', pg_field_type($queryId, $i))) // si es un blob no se muestra el contenido en la web
					array_push($row, '['.pg_field_type($queryId, $i).']');
				else
					array_push($row, $rows[$i]);
			}
			array_push($data, $row);
		}
		pg_free_result($queryId);
		unset($sql, $nfilas, $i, $value, $row);
		return $data;
	}

	/**
	 * Exportar una consulta a un archivo csv y descargar
	 *
	 * La cantidad de campos seleccionados en la consulta debe ser igual
	 * al largo del arreglo de columnas
	 * @param consulta Consulta SQL
	 * @param columnas Arreglo con los nombres de las columnas a utilizar en la tabla
	 * @param archivo Nombre para el archivo que se descargara
         * @todo Probar que funcione
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-12-03
	 */
	public function tocsv ($consulta, $columnas, $archivo) {
		// realizar consulta
		$this->consulta('
			SELECT "'.implode('", "', $columnas).'"
			UNION
			'.$consulta.'
			INTO OUTFILE "'.TMP.'/'.$archivo.'.csv"
				FIELDS TERMINATED BY ";"
				LINES TERMINATED BY "\r\n"
		');
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
	 * @todo Programar método para Postgresql
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-23
	 */
	final public function tablas ($database = DB_NAME) {
		return $this->getTabla("
			SELECT t.table_name AS name, d.description AS comment
			FROM information_schema.tables AS t, pg_catalog.pg_description AS d, pg_catalog.pg_class AS c
			WHERE
				t.table_catalog = '$database'
				AND c.relname = t.table_name
				AND d.objoid = c.oid
				AND d.objsubid = 0
			ORDER BY t.table_name
		");
	}
	
	/**
	 * Busca la informacion de la tabla
	 * @param tabla Nombre de la tabla de la que se busca la informacion
	 * @param database Nombre de la base de datos en la que esta la tabla
	 * @return Arreglo con los indices: name, default, null, type, key, extra, comment, pk, fk_table y fk_column (primer indice del arreglo tiene la info de la tabla igual que la devuelta por $this->tablas()
	 * @todo Programar método para Postgresql
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-21
	 */
	final public function tablaInfo ($tabla, $database = DB_NAME) {
		$table = $this->getTabla("
			SELECT '$tabla' AS name, d.description AS comment
			FROM pg_catalog.pg_description AS d, pg_catalog.pg_class AS c
			WHERE
				c.relname = '$tabla'
				AND d.objoid = c.oid
				AND d.objsubid = 0
		");
		$pk = $this->getColumna("
			SELECT column_name
			FROM information_schema.constraint_column_usage
			WHERE constraint_name = (
				SELECT relname
				FROM pg_class
				WHERE oid = (
					SELECT indexrelid
					FROM pg_index, pg_class
					WHERE
						pg_class.relname='$tabla'
						AND pg_class.oid = pg_index.indrelid
						AND indisprimary = 't'
				)
			) AND table_catalog = '$database' AND table_name = '$tabla'
		");
		$fkAux = $this->getTabla("
			SELECT
				table_name || '_' || column_name AS name
				, table_name AS fk_table
				, column_name AS fk_column
			FROM information_schema.constraint_column_usage
			WHERE constraint_name IN (
				SELECT constraint_name
				FROM information_schema.table_constraints
				WHERE table_name = '$tabla' AND constraint_type = 'FOREIGN KEY'
			) AND table_catalog = '$database'
		");
		$fk = array();
		foreach($fkAux as &$aux) {
			$fk[array_shift($aux)] = $aux; 
		}
		unset($fkAux);
		$columns = $this->getTabla("
			SELECT
				c.column_name AS name
				, data_type as type
				, c.column_default AS default
				, c.is_nullable AS null
				, (CASE (SELECT c.character_maximum_length is null)
					WHEN true THEN c.numeric_precision
					ELSE c.character_maximum_length
				END) AS length
				, d.description AS comment
			FROM
				information_schema.columns as c
				, pg_description as d
				, pg_class as t
			WHERE
				c.table_catalog = '$database'
				AND c.table_name = '$tabla'
				AND t.relname = '$tabla'
				AND d.objoid = t.oid
				AND d.objsubid = c.ordinal_position
			ORDER BY c.ordinal_position ASC
		");
		// recorrer columnas para definir pk, fk y auto
		foreach($columns as &$column) {
			// definir columna extra para auto_increment (como en MySQL)
			if(substr($column['default'], 0, 7)=='nextval') $column['extra'] = 'auto_increment';
			else $column['extra'] = '';
			// definir null o not null
			if($column['null']=='YES') $column['null'] = 'NULL';
			else $column['null'] = 'NOT NULL';
			// definir default, quitar lo que viene despues de ::
			if($column['extra'] != 'auto_increment') {
				$aux = explode('::', $column['default']);
				$column['default'] = trim(array_shift($aux), '()');
				if($column['default']=='NULL') $column['default'] = '';
			}
			// definir pk
			if(array_search($column['name'], $pk)!==false) $column['pk'] = 'YES';
			else $column['pk'] = 'NO';
			// definir fk
			if(array_key_exists($column['name'], $fk)) {
				$column['fk_table'] = $fk[$column['name']]['fk_table'];
				$column['fk_column'] = $fk[$column['name']]['fk_column'];
			} else {
				$column['fk_table'] = '';
				$column['fk_column'] = '';
			}
			if($column['extra']=='auto_increment') $column['auto'] = 'YES';
			else $column['auto'] = 'NO';
		}
		return array_merge($table, $columns);
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
	 * @todo Programar método para Postgresql
         * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-13
         */
        final public function funcion ($function, $par1 = null) {
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

	final public function transaction () {
		$this->consulta('BEGIN');
	}
	final public function commit () {
		$this->consulta('COMMIT');
	}
	final public function rollback () {
		$this->consulta('ROLLBACK');
	}

}

?>
