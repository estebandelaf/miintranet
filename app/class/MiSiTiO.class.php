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
 * Clase para manejar diversos componentes del sitio, entre ellos la carga de las
 * plantillas que generaran el contenido html
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-12
 */
class MiSiTiO {

	/**
	 * Generador del paginador
	 * @param pagina Página que se esta viendo actualmente
	 * @param filas Filas totales que se deberán paginar
	 * @param link Enlace que se utilizará para acompañar al paginador
	 * @param rows Filas por página
	 * @param pages Páginas que se mostraran en un grupo del paginador
	 * @return String paginador ya creado 
	 */
	public static function paginador ($pagina, $filas, $link, $rows, $pages) {
		// determinar paginas totales
		$paginas = ceil($filas / $rows);
		if(!$paginas) $paginas = 1;
		// determinar grupo de paginas que se esta mostrando
		$grupo = ceil($pagina / $pages);
		$grupos = ceil($paginas / $pages);
		// inicializar el paginador
		$paginador = '';
		// agregar icono primera pagina
		if($pagina==1) $paginador .= MiSiTiO::generar ('paginador/firstpage_off.html');
		else $paginador .= MiSiTiO::generar ('paginador/firstpage_on.html', array('link'=>$link, 'firstpage'=>LANG_PAGINATOR_FIRSTPAGE));
		// agregar icono grupo previo
		$prevgroup = ($grupo-1)*$pages;
		if($grupo==1) $paginador .= MiSiTiO::generar ('paginador/prevgroup_off.html');
		else $paginador .= MiSiTiO::generar ('paginador/prevgroup_on.html', array('link'=>$link, 'page'=>$prevgroup, 'prevgroup'=>LANG_PAGINATOR_PREVGROUP));
		// agregar link a paginas
		$desde = ($grupo-1)*$pages+1;
		$hasta = $grupo*$pages;
		for($i=$desde; $i<=$hasta; ++$i) {
			if($i>$paginas) break;
			if($pagina==$i) $paginador .= MiSiTiO::generar('paginador/page_off.html', array('page'=>$i));
			else $paginador .= MiSiTiO::generar('paginador/page_on.html', array('link'=>$link, 'page'=>$i));
		}
		// agregar icono grupo siguiente
		$nextgroup = ($grupo*$pages)+1;
		if($grupo==$grupos) $paginador .= MiSiTiO::generar ('paginador/nextgroup_off.html');
		else $paginador .= MiSiTiO::generar ('paginador/nextgroup_on.html', array('link'=>$link, 'page'=>$nextgroup, 'nextgroup'=>LANG_PAGINATOR_NEXTGROUP));		
		// agregar icono ultima pagina
		if($pagina==$paginas) $paginador .= MiSiTiO::generar ('paginador/lastpage_off.html');
		else $paginador .= MiSiTiO::generar ('paginador/lastpage_on.html', array('link'=>$link, 'page'=>$paginas, 'lastpage'=>LANG_PAGINATOR_LASTPAGE));
		// retornar paginador
		return $paginador;
	}
	
	/**
	 * Obtiene el módulo en el que nos encontramos
	 * @return Nombre del módulo
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-24
	 */
	public static function modulo () {
		// preparar url y quitar elementos despues de ? en caso de haber
		// parametros pasados por get
		$get = strpos($_SERVER['REQUEST_URI'], '?');
		if($get) $modulo = substr($_SERVER['REQUEST_URI'], 1, $get-1);
		else $modulo = substr($_SERVER['REQUEST_URI'], 1);
		// verificar que se este viendo el menu del modulo o una pagina de este
		if($modulo[strlen($modulo)-1]=='/') return substr($modulo, 0, -1);
		else return substr($modulo, 0, strrpos($modulo, '/'));
	}
	
        /**
         * Incluye código css por módulo, agregara el archivo screen si existe,
         * si existe screen.css y print.css agregará ambos, si solo existe screen.css
         * para el media="print" utilizara el screen.css
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function cssMod () {
                $cssMod = '';
                if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/template/'.TEMPLATE.'/css/screen.css')) {
                        $cssMod .= self::generar ('cssMod.html', array(
                            'modulo'=>MODULO_DIR
                            , 'template'=>TEMPLATE
                            , 'css'=>'screen'
                            , 'media'=>'screen'
                        ));
                        if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/template/'.TEMPLATE.'/css/print.css')) {
                                $cssMod .= self::generar ('cssMod.html', array(
                                    'modulo'=>MODULO_DIR
                                    , 'template'=>TEMPLATE
                                    , 'css'=>'print'
                                    , 'media'=>'print'
                                ));
                        } else {
                                $cssMod .= self::generar ('cssMod.html', array(
                                    'modulo'=>MODULO_DIR
                                    , 'template'=>TEMPLATE
                                    , 'css'=>'screen'
                                    , 'media'=>'print'
                                ));
                        }
                }
                return $cssMod;
        }

        /**
         * Incluye código javascript por módulo, de existir el archivo de idioma
         * para JS también lo incluirá
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-12
         */
        public static function jsMod () {
		global $Usuario;
                $jsMod = '';
                if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/js/js.js')) {
                        if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/js/lang/'.$Usuario->lang.'.js')) {
                                $jsMod .= MiSiTiO::generar ('jsMod.html', array(
                                    'modulo'=>MODULO_DIR
                                    , 'js'=>'lang/'.$Usuario->lang
                                ));
                        } else if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/js/lang/'.LANG.'.js')) {
                                $jsMod .= MiSiTiO::generar ('jsMod.html', array(
                                    'modulo'=>MODULO_DIR
                                    , 'js'=>'lang/'.LANG
                                ));
                        } 
                        $jsMod .= MiSiTiO::generar('jsMod.html', array('modulo'=>MODULO_DIR, 'js'=>'js'));
                }
                return $jsMod;
        }
  
        /**
         * Crea menú principal de navegación
         * @param nav Arreglo con las opciones para el menú
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function nav (&$nav) {
		global $Usuario;
                $navItem = '';
		// se usa auxiliar porque solo variables debiesen ser pasadas por referencia (Strict Mode)
		$aux = explode('/', MODULO);
        	$modulo = array_shift($aux); 
		unset($aux);
        	foreach($nav as &$opcion) { // realizar acción para cada elemento del menú
                        // solo se muestra el menú si el usuario puede accederlo
        		if($Usuario->autorizado($opcion['link'], false)) {
                                $navItem .= MiSiTiO::generar('nav/navItem.html', array(
                                    'on'=>$opcion['link']=='/'.$modulo.'/' ? '_on' : ''
                                    , 'link'=>$opcion['link']
                                    , 'desc'=>$opcion['desc']
                                    , 'akey'=>$opcion['akey']
                                    , 'name'=>$opcion['name']
                                ));
                	}
                }
                unset($nav, $tab, $modulo, $opcion);
                return MiSiTiO::generar('nav/nav.html', array('navItem'=>$navItem));
        }

        /**
         * Genera los iconos para el panel de iconos de los modulos
         * @param navModulo Arreglo con la información de los iconos para el menú
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-05-16
         */
        public static function iconos ($titulo, &$navModulo) {
		global $Usuario;
                $iconosItem = '';
        	foreach($navModulo as $nav) { // realizar acción por cada elemento del menú
			$pagina = $nav['link'][0]=='/' ? $nav['link'] : '/'.MODULO.'/'.$nav['link'];
        		if($Usuario->autorizado($pagina,false)) {
        			// determinar ubicacion de imagen
        			if(empty($nav['imag'])) $nav['imag'] = '/img/iconos/icono.png';
        			if(file_exists(DIR.'/img/iconos/'.$nav['imag'])) {
        				$nav['imag'] = '/img/iconos/'.$nav['imag'];
        			} else if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/img/iconos/'.$nav['imag'])) {
        				$nav['imag'] = '/modulos/'.MODULO_DIR.'/img/iconos/'.$nav['imag'];
				} else if(file_exists(DIR.'/template/'.TEMPLATE.'/img/iconos/'.$nav['imag'])) {
					$nav['imag'] = '/template/'.TEMPLATE.'/img/iconos/'.$nav['imag'];
        			} else $nav['imag'] = '/template/'.TEMPLATE.'/img/iconos/icono.png';
                                $iconosItem .= MiSiTiO::generar('iconos/iconosItem.html', $nav);
        		}
        	}
                unset($nav);
                return MiSiTiO::generar('iconos/iconos.html', array('titulo'=>$titulo, 'iconosItem'=>$iconosItem));
        }

        /**
         * Crea un menú a partir de las opciones del modulo en el que se encuentra el usuario
         * @param navModulo Arreglo con la información de los iconos para el menú
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-30
         */
        public static function navMod(&$navModulo) {
		global $Usuario;
                $navMod = '';
        	if(MODULO!='' && count($navModulo)) {
                        $navModItem = '';
        		foreach($navModulo as &$opcion) { // realizar acción para cada elemento del menú
        			if($Usuario->autorizado($opcion['link'], false)) { // solo se muestra el menú si el usuario puede accederlo
        				$navModItem .= MiSiTiO::generar('nav/navModItem.html', array(
                                            'link'=>$opcion['link']
                                            , 'name'=>$opcion['name']
                                            , 'desc'=>$opcion['desc']
                                        ));
                                }
                        }
                        $navMod = MiSiTiO::generar('nav/navMod.html', array('modulo'=>MODULO, 'navModItem'=>$navModItem));
                }
                unset($navModItem);
                return $navMod;
        }

        /**
         * Crea un menú para ser usado como pestañas
         * @param opciones Arreglo con un arreglo por cada opción del menu de pestañas
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function tabmenu($opciones) {
                $tabmenuItem = '';
                $class = ' class="on"';
        	foreach($opciones as &$opcion) {
                        $tabmenuItem .= MiSiTiO::generar('tabmenu/tabmenuItem.html', array(
                                'id'=>array_shift($opcion)
                                , 'name'=>array_shift($opcion)
                                , 'class'=>$class
                        ));
        		$class = '';
                }
                unset($opciones, $opcion);
                return MiSiTiO::generar('tabmenu/tabmenu.html', array('tabmenuItem'=>$tabmenuItem));
        }

        /**
         * Crea un cuadro de ayuda visible todo el tiempo (diferente al helpBox)
         * @param titulo Título del cuadro de ayuda
         * @param txt Texto del cuadro de ayuda
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-08
         */
        public static function textbox($titulo, $txt) {
                return self::generar('textbox/textbox.html', array('titulo'=>$titulo, 'txt'=>$txt));
        }

        /**
         * Muestra un mensaje al usuario
         * @param txt Texto/mensaje a mostrar
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-08
         */
        public static function mensaje ($txt) {
        	return self::generar('mensaje.html', array('txt'=>$txt));
        }

	/**
	 * Genera un cuadro de solicitud exitosa
	 * @param url Url a la que se deberá redirigir la página
	 * @return String Cuadro con el mensaje
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-30
	 */
	public static function success ($url) {
		return self::generar('result/result.html', array('result'=>'success', 'url'=>$url, 'secs'=>REDIRECT_SECS, 'msg'=>LANG_RESULT_SUCCESS_MSG, 'redirect'=>LANG_RESULT_REDIRECT));
	}

	/**
	 * Genera un cuadro de solicitud fallida
	 * @param url Url a la que se deberá redirigir la página
	 * @return String Cuadro con el mensaje
	 * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-30
	 */
	public static function failure ($url) {
		return self::generar('result/result.html', array('result'=>'failure', 'url'=>$url, 'secs'=>REDIRECT_SECS, 'msg'=>LANG_RESULT_FAILURE_MSG, 'redirect'=>LANG_RESULT_REDIRECT));
	}
	
        /**
         * Genera página de error
         *
         * Función que limpia el buffer de salida, generá página de error y detiene la ejecución del script
         * @param error Identificador del error
         * @param descripcion Descripción del error
         * @author DeLaF, esteban[at]delaf.cl
         * @version 2011-03-10
         */
        public static function error ($error = '', $descripcion = '') {
        	switch ($error) {
        		case DB_ERROR: {
        			$error = LANG_ERROR_BD_TITLE;
        			$img = 'database.png';
        			break;
        		}
        		case ACCESS_DENIED: {
        			$error = LANG_ERROR_PAGE_TITLE_401;
        			$descripcion = LANG_ERROR_PAGE_401;
        			$img = 'restricted-area.jpg';
        			break;
        		}
        		case ACCESS_INCORRECT: {
        			$error = LANG_ERROR_ACCESSINCORRECT_TITLE;
        			$descripcion = LANG_ERROR_ACCESSINCORRECT_MSG;
        			$img = 'restricted-area.jpg';
        			break;
        		}
        		case SITE_OFFLINE: {
        			$error = LANG_ERROR_OFFLINE_TITLE;
        			$descripcion = LANG_ERROR_OFFLINE_MSG;
        			$img = 'site-offline.jpg';
        			break;
        		}
        		case TEMPLATE_ERROR: {
        			$error = LANG_ERROR_TEMPLATE_TITLE;
        			$descripcion .= ': '.LANG_ERROR_TEMPLATE_MSG;
        			$img = 'error.jpg';
        			break;
        		}
                        case 400: {
                                $error = '400 '.LANG_ERROR_PAGE_TITLE_400;
                                $descripcion = LANG_ERROR_PAGE_400;
                                $img = 'apache.jpg';
                                break;
                        }
                        case 401: {
                                $error = '401 '.LANG_ERROR_PAGE_TITLE_401;
                                $descripcion = LANG_ERROR_PAGE_401;
                                $img = 'apache.jpg';
                                break;
                        }
                        case 403: {
                                $error = '403 '.LANG_ERROR_PAGE_TITLE_403;
                                $descripcion = LANG_ERROR_PAGE_403;
                                $img = 'apache.jpg';
                                break;
                        }
                        case 404: {
                                $error = '404 '.LANG_ERROR_PAGE_TITLE_404;
                                $descripcion = LANG_ERROR_PAGE_404;
                                $img = 'apache.jpg';
                                break;
                        }
                        case 500: {
                                $error = '500 '.LANG_ERROR_PAGE_TITLE_500;
                                $descripcion = LANG_ERROR_PAGE_500;
                                $img = 'apache.jpg';
                                break;
                        }
                        case '': {
                                $error = LANG_ERROR_PAGE_TITLE_XXX;
                                $descripcion = LANG_ERROR_PAGE_XXX;
                                $img = 'error.jpg';
                        }
        		default: {
                                if(empty($descripcion)) $descripcion = LANG_ERROR_PAGE_XXX;
        			$img = 'error.jpg';
        		}
        	}
        	ob_end_clean();
        	echo self::generar('error/error.html', array(
        	    'lang' => LANG
        	    , 'title' => LANG_ERROR_PAGE_TITLE
        	    , 'error' => $error
        	    , 'img' => $img
        	    , 'description' => $descripcion
        	    , 'msg' => LANG_ERROR_PAGE_MSG
        	));
        	exit();
        }

        /**
	 * Esta método permite utilizar plantillas en la aplicacion, estas deberán
	 * estar ubicadas en la carpeta template del directorio raiz (de la app) o bien
	 * en la carpeta template de los modulos/submodulos
	 * @param nombrePlantilla Nombre del archivo que se utilizara como plantilla (con extension)
	 * @param variables Arreglo con las variables a reemplazar en la plantilla
	 * @param tab Si es que se deberán añadir tabuladores al inicio de cada linea de la plantilla
	 * @return String Plantilla ya formateada con las variables correspondientes
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-04
	 */
	public static function generar ($nombrePlantilla, $variables = null, $tab = 0) {

		// definir donde se encuentra la plantilla
		if(file_exists(DIR.'/template/'.TEMPLATE.'/'.$nombrePlantilla)) {
			$archivoPlantilla = DIR.'/template/'.TEMPLATE.'/'.$nombrePlantilla;
		} else if(file_exists(DIR.'/modulos/'.MODULO_DIR.'/template/'.TEMPLATE.'/'.$nombrePlantilla)) {
			$archivoPlantilla = DIR.'/modulos/'.MODULO_DIR.'/template/'.TEMPLATE.'/'.$nombrePlantilla;
		} else if($nombrePlantilla!='error.html') {
			self::error(TEMPLATE_ERROR, $nombrePlantilla);
		}

		// cargar plantilla
		$plantilla = file_get_contents($archivoPlantilla);

		// añadir tabuladores delante de cada linea
		if($tab) {
			$lineas = explode("\n", $plantilla);
			foreach($lineas as &$linea) {
				if(!empty($linea)) $linea = constant('TAB'.$tab).$linea;
			}
			$plantilla = implode("\n", $lineas);
			unset($lineas, $linea);
		}

		// reemplazar variables en la plantilla
		if($variables) {
			foreach($variables as $key => $valor)
				$plantilla = str_replace('{'.$key.'}', $valor, $plantilla);
		}

		// retornar plantilla ya procesada
		return $plantilla;

        }

}
