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
 * Manejar archivos y directorios
 *
 * Esta clase permite realizar diversas acciones sobre archivos y directorios
 * que se encuentren en el servidor donde se ejecuta la aplicación. Tales como:
 * listar contenido de directorios, subir archivos al servidor, extraer archivos
 * comprimidos, etc.
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-24
 */
final class Archivo {

	/**
	 * Recupera los archivos/directorios desde una carpeta
	 * @param dir Nombre del directorio a examinar
	 * @return Arreglo con los nombres de los archivos y/o directorios
	 * @todo Selección de sólo algunos archivos de la carpeta
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-05-22
	 */
	public static function examinarDirectorio ($dir) {
		$archivos = array(); // arreglo para retornar archivos
		if ($gestor = opendir($dir)) { // abrir directorio
			while (($archivo = readdir($gestor)) != false) { // leer directorio
				if($archivo!='.' && $archivo!='..') { // no considerar . ni ..
					array_push($archivos, $archivo); // guardar nombre del archivo
				}
			}
			closedir($gestor); // cerrar gestor
		}
		unset($dir, $gestor, $archivo);
		sort($archivos); // ordenar resultado alfabéticamente
		return $archivos; // devolver archivos
	}

	/**
	 * Obtiene el tamaño de un fichero o directorio (método basado en función encontrada en Internet)
	 * @param filepath Nombre del archivo/directorio a consultar tamaño
         * @param mostrarUnidad =true mostrara la unidad (KB, MB, etc)
	 * @return Tamaño del archivo/directorio o bien descripción del error ocurrido
	 * @author Desconocido, http://www.blasten.com/contenidos/?id=Tama?o_de_archivo_en_byte,_Kb,_Mb,_y_Gb
	 * @version 2010-07-16
	 */
	public static function getSize ($filepath, $mostrarUnidad = true) {
		$method = array('B','KB','MB','GB', 'TB');
		$size = 0;
		if (!file_exists($filepath)) { // verificar que el archivo exista
			return LANG_ARCHIVO_NOTEXIST;
		} elseif (!is_file($filepath) && !is_dir($filepath)) { // verificar que sea un fichero o directorio
			return '"'.$file.'" ' . LANG_ARCHIVO_NOTVALID;
		} else {
			if ($dir = opendir($filepath)) { // abrir el directorio
				while ($file = readdir($dir)) {
					if (is_dir($filepath.'/'.$file)) { // si el archivo es un directorio lo recorre recursivamente
						if ($file != '.' && $file != '..') { // no recorre el dir padre ni el mismo recursivamente
							$size += self::getSize($filepath.'/'.$file, false); // llamada recursiva sin unidad
						}
					} else {
						$size += filesize ($filepath.'/'.$file); // si no es directorio se retorna el tamaño del archivo
					}
				}
				closedir($dir);
			} else { // si no es directorio se retorna el tamaño del archivo
				$size += filesize($filepath);
			}
		}
		clearstatcache();
		// dependiendo del tamaño del archivo se le coloca la unidad
		if(!$mostrarUnidad) return $size;
		if ($size <= 1024) // B
			return $size.' '.$method[0];
		elseif ($size >= pow(1024, 4)) // TB
			return round($size/pow(1024, 4), 2).' '.$method[4];
		elseif ($size >= pow(1024, 3)) // GB
			return round($size/pow(1024, 3), 2).' '.$method[3];
		elseif ($size >= pow(1024, 2)) // MB
			return round($size/pow(1024, 2), 2).' '.$method[2];
		else // KB
			return round($size/1024, 2).' '.$method[1];
	}

	/**
	 * Recibe un archivo subido por post desde un formulario
	 *
	 * Procesa el archivo enviado y lo devuelve en un arreglo,
	 * el arreglo contiene los datos, nombre, tipo y tamaño, si w y h
	 * se indican como 0 se asume que el archivo NO es una foto.
	 * @param src Arreglo con la información del archivo recibido mediante $_FILES
	 * @param mimetype Arreglo con los tipos mime válidos
	 * @param size Tamaño máximo permitido en KB (0 para cualquier tamaño)
	 * @param w Ancho máximo de una foto (0 para cualquier archivo)
	 * @param h Alto maximo de una foto (0 para cualquier archivo)
	 * @return Arreglo con los datos del archivo (índices: data, name, type y size),
	 * en caso de error se retorna un entero (=1 tamaño, =2 imagen muy grande, =3 mimetype, =4 upload fallo)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-17
	 */
	public static function upload (&$src, $mimetype = null, $size = 0, $w = 0, $h = 0) {
		// verificar que exista el archivo y haya sido subido
		if (!empty($src['name'])) {
			// verificar el formato del archivo
			if (!$mimetype || in_array($src['type'],$mimetype)) { // verificar tipo de archivo si es que se indico
				if($w&&$h) { // verificar el tamaño de la imagen
					$tam = getimagesize($src['tmp_name']);
					$ok = (bool)($tam[0]<=$w && $tam[1]<=$h);
				} else $ok = true; // si no se ha especificado un tamaño maximo, se acepta cualquier tamaño
				if($ok) {
					if(!$size || $src['size']<=($size*1024)) { // se verifica si hay limitacion de tamaño
						$file['data'] = fread( // se lee el contenido del archivo
							fopen($src['tmp_name'], 'rb'),
							filesize($src['tmp_name'])
						);
						$file['name'] = $src['name'];
						$file['type'] = $src['type'];
						$file['size'] = $src['size'];
						unset($src, $mimetype, $size, $w, $h, $tam, $ok);
						return $file; // se retorna un arreglo con los datos del archivo
					} else {
						// tamaño excedido
						return 1;
					}
				} else {
					// imagen muy grande
					return 2;
				}
			} else {
				// tipo de archivo inválido
				return 3;
			}
		} else {
			// no se ha enviado correctamente el archivo
			return 4;
		}
	}

	/**
	 * Extrae un archivo de un fichero comprimido zip
	 * @param archivoZip Nombre del archivo comprimido
	 * @param archivoBuscado Nombre del archivo que se busca extraer
	 * @return Arreglo con índices name, type (no definido), size y data (idem self::upload)
	 * @warning Sólo extrae un archivo del primer nivel (fuera de directorios)
	 * @todo Extracción de un fichero que este en subdirectorios
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-07-15
	 */
	public static function ezip ($archivoZip, $archivoBuscado) {
		$zip = zip_open($archivoZip);
		if(is_resource($zip)) {
			// buscar contenido
			do {
				$entry = zip_read($zip);
				$name = zip_entry_name($entry);
			} while ($entry && $name != $archivoBuscado);
			// abrir contenido
			zip_entry_open($zip, $entry, 'r');
			$size = zip_entry_filesize($entry);
			$entry_content = zip_entry_read($entry, $size);
			// pasar datos del archivo
			$archivo['name'] = $name;
			$archivo['type'] = null;
			$archivo['size'] = $size;
			$archivo['data'] = $entry_content;
		} else {
			$archivo = false;
		}
		unset($archivoZip, $archivoBuscado, $zip, $entry, $name, $size, $entry_content);
		return $archivo;
	}

	/**
	 * Empaqueta un directorio (o un archivo), lo comprime y descarga
	 * @param filepath Directorio (o archivo) que se desea bajar
	 * @param delete =true se intentara borrar filepath
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-24
	 */
	public static function targz ($filepath, $delete = false) {
		// obtener directorio que contiene al archivo/directorio y el nombre de este
		$dir = dirname($filepath);
		$file = basename($filepath);
		// empaquetar directorio/archivo
		exec('cd '.$dir.'; tar czf '.$file.'.tar.gz '.$file);
		// enviar archivo
		ob_clean();
		header ('Content-Disposition: attachment; filename='.$file.'.tar.gz');
		header ('Content-Type: application/x-gtar');
		header ('Content-Length: '.filesize($dir.'/'.$file.'.tar.gz'));
		readfile($dir.'/'.$file.'.tar.gz');
		// borrar archivo generado
		unlink($dir.'/'.$file.'.tar.gz');
		// borrar filepath
		if($delete) {
			if(is_dir($filepath)) self::rmdir_recursive($filepath);
			else unlink($filepath);
		}
	}

	/**
	 * Borra recursivamente un directorio
	 * @param dir Directorio a borrar
	 * @author http://en.kioskea.net/faq/793-warning-rmdir-directory-not-empty
	 * @version 2011-03-24
	 */
	public static function rmdir_recursive ($dir) {
		// List the contents of the directory table
		$dir_content = scandir ($dir);
		// Is it a directory?
		if($dir_content!==false) {
			// For each directory entry
			foreach ($dir_content as &$entry) {
				// Unix symbolic shortcuts, we go
				if (!in_array ($entry, array ('.','..'))) {
					// We find the path from the beginning
					$entry = $dir. '/'. $entry;
					// This entry is not an issue: it clears
					if (!is_dir($entry)) {
						unlink ($entry);
					} else { // This entry is a folder, it again on this issue
						self::rmdir_recursive ($entry);
					}
				}
			}
		}
		// It has erased all entries in the folder, we can now erase
		rmdir ($dir);
	}
}

?>
