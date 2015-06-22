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
 * Manejo de tablas html
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-04-07
 */
final class Tabla {

	public static $id = ''; ///< Identificador de la tabla
	public static $ordenar = true; ///< Permite indicar si la tabla se ordenará o no (además debe tener un id no vacio)
	public static $ancho = null; ///< Arreglo con los anchos de las columnas de la tabla
	public static $mostrar = true; ///< Permite indicar si por defecto la tabla será mostrata o permanecerá oculta en la página
	public static $mantenedor = false; ///< Indica si la tabla corresponde una tabla de mantenedor
	public static $paginator = ''; ///< Paginador para al tabla, viene de la clase MiSiTiO
	public static $nuevo = true; ///< Indica si se debe mostrar el boton de nuevo elemento en la tabla

	/**
	 * Permite reiniciar los atributos estáticos de la clase
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-07
	 */
	public static function reset () {
		self::$id = '';
		self::$ordenar = true;
		self::$ancho = null;
		self::$mostrar = true;
		self::$mantenedor = false;
		self::$paginator = '';
		self::$nuevo = true;
	}

	/**
	 * Generar una tabla a partir de un arreglo de datos
	 * @param data Arreglo con la tabla, incluyendo títulos de las columnas (en la primera fila)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-07
	 */
	public static function generar ($data) {
		// mostrar botones de acciones de la tabla
		if(!empty(self::$id)) {
			// generar iconos
                        $iconos = MiSiTiO::generar('tabla/iconos.html', array(
                                'id'=>self::$id
				, 'new'=>self::$mantenedor&&self::$nuevo?self::nuevo():''
				, 'paginator'=>self::$mantenedor?self::paginator():''
                                , 'xls'=>LANG_TABLA_EXPORT_XLS
                                , 'ods'=>LANG_TABLA_EXPORT_ODS
                                , 'csv'=>LANG_TABLA_EXPORT_CSV
                                , 'pdf'=>LANG_TABLA_EXPORT_PDF
				, 'xml'=>LANG_TABLA_EXPORT_XML
                                , 'show'=>LANG_TABLA_SHOW
                                , 'hide'=>LANG_TABLA_HIDE
                        ));
			// datos para la sesion y poder exportar
			$_SESSION['tabla']['id'] = self::$id;
			$_SESSION['tabla']['data'] = $data;
		} else $iconos = '';
                // si se solicito ordenar, llamar al codigo de tablesorter para tal accion
		if(!empty(self::$id) && self::$ordenar && !self::$mantenedor) {
			$tablesorter = MiSiTiO::generar('tabla/tablesorter.html', array('id'=>self::$id));
		} else $tablesorter = '';
                // definir la visibilidad por defecto de la tabla
                if(!empty(self::$id)) {
                        if(!self::$mostrar) $visibilidad = MiSiTiO::generar('tabla/visibilidadOcultar.js', array('id'=>self::$id));
                        else $visibilidad = MiSiTiO::generar('tabla/visibilidadMostrar.js', array('id'=>self::$id));
		} else $visibilidad = '';
                // fila con los titulos de las columnas
                $theadItem = '';
                $i=0;
		$row = array_shift($data);
		foreach($row as &$celda) {
                        if(isset(self::$ancho[$i]))
                                $theadItem .= MiSiTiO::generar('tabla/theadItemAncho.html', array('celda'=>$celda, 'ancho'=>self::$ancho[$i]));
                        else
                                $theadItem .= MiSiTiO::generar('tabla/theadItem.html', array('celda'=>$celda)); 
			++$i;
		}
                unset($row);
                // filas de datos
                $tbody = '';
		foreach($data as &$row) {
                        $tbodyItem = '';
                        foreach($row as &$celda)
                                $tbodyItem .= MiSiTiO::generar ('tabla/tbodyItem.html', array('celda'=>$celda));
                        $tbody .= MiSiTiO::generar ('tabla/tbody.html', array('tbodyItem'=>$tbodyItem));
                        unset($row);
                }
		// procesar tabla que se guardo en la sesion, esto para prepararla para las exportaciones
		if(!empty(self::$id)) {
			// quitar tag html del titulo de las columnas (si existiesen), se sacara el texto hasta el primer <
			$titulos = array_shift($_SESSION['tabla']['data']);
			foreach($titulos as &$titulo) {
				$tag = strpos($titulo, '<');
				if($tag)
					$titulo = substr($titulo, 0, $tag);
			}
			// solo se verifica si existen filas en la tabla
			if(count($_SESSION['tabla']['data'])) {
				// verificar que la primera fila no tenga tags html al inicio (caso de formulario de busqueda)
				// si los tiene esa fila se quita
				if(isset($_SESSION['tabla']['data'][0][0][0]) && $_SESSION['tabla']['data'][0][0][0]=='<') {
					array_shift($_SESSION['tabla']['data']);
				}
				// verificar que la ultima columna no sea de acciones, si lo es tendra tag html, quitarlos
				// solo se verifica si existen filas en la tabla
				if(count($_SESSION['tabla']['data'])) {
					$ultima = array_slice($_SESSION['tabla']['data'][0], -1);
					if(isset($ultima[0]) && isset($ultima[0][0]) && $ultima[0][0]=='<') {
						// quitar ultima columna de los titulos
						array_pop($titulos);
						// quitar ultima columna de las filas
						foreach($_SESSION['tabla']['data'] as &$fila) {
							array_pop($fila);
						}
					}
				}
			}
			// unir titulos al resto de la tabla nuevamente
			array_unshift($_SESSION['tabla']['data'], $titulos);
		}
		// eliminar variables usadas
                unset($data, $row, $celda, $tbodyItem);
                // dibujar tabla html con todos sus componentes
		return MiSiTiO::generar('tabla/tabla.html', array(
                    'id'=>self::$id
                    , 'iconos'=>$iconos
                    , 'tablesorter'=>$tablesorter
                    , 'visibilidad'=>$visibilidad
                    , 'theadItem'=>$theadItem
                    , 'tbody'=>$tbody
                ));
	}

	/**
	 * Tabla horizontal, similar a lo usado en los formularios (2 columnas)
	 * @param label Título de la fila
	 * @param name Valor de la fila
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-10
	 */
	public static function h ($label, $name, $id = '') {
		return MiSiTiO::generar('tabla/h.html', array('label'=>$label, 'name'=>$name, 'id'=>$id));
	}

	/**
         * Link para icono nuevo en tablas
         * @return String con el link
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-30
         */
        private static function nuevo () {
                return MiSiTiO::generar('tabla/nuevo.html', array('new'=>LANG_TABLA_NEW));
        }
	
        /**
         * Link para icono editar en tablas
         * @param link Url donde se editará
         * @return String con el link
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function editar ($link) {
                return MiSiTiO::generar('tabla/editar.html', array('link'=>$link, 'edit'=>LANG_TABLA_EDIT));
        }

        /**
         * Link para icono eliminar en tablas
         * @param funcionJS Función JS que se utilizará para enviar a eliminar
         * @return String con el link
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function eliminar ($funcionJS) {
                return MiSiTiO::generar('tabla/eliminar.html', array('funcionJS'=>$funcionJS, 'delete'=>LANG_TABLA_DELETE));
        }
        
        /**
         * Link para ir a otra parte de la web
         * @param link Url donde se irá
         * @return String con el link
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function siguiente ($link) {
                return MiSiTiO::generar('tabla/siguiente.html', array('link'=>$link, 'next'=>LANG_TABLA_NEXT));
        }

	/**
         * Link para icono en una tabla
         * @param link Link del icono
	 * @param img Imágen del icono
	 * @param txt Descripción del icono
         * @return String con el link
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function icono ($link, $img = '', $txt = '') {
		if(empty($img)) $img = '/template/'.TEMPLATE.'/tabla/img/icono.png';
                return MiSiTiO::generar('tabla/icono.html', array('link'=>$link, 'img'=>$img, 'txt'=>$txt));
        }
	
	/**
	 * Genera el nombre de la columna, más los iconos ASC y DESC para ordenar por esta
	 * @param titulo Titulo para la columna
	 * @param link Enlace para el ordenamiento
	 * @return String
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
	 */
	public static function orderby ($titulo, $link = '') {
		if(empty($link)) return $titulo;
		return $titulo.MiSiTiO::generar('tabla/orderby.html', array('link'=>$link));
	}
	
	private static function paginator () {
		return self::$paginator;
	}
	
}

?>
