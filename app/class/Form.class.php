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
 * Manejo de formularios
 *
 * Esta clase permite manejar los campos de formularios, los botones,
 * campos de entrada, selección, etc
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-05-23
 */
final class Form {

	/**
	 * Inicia el formulario, agrega tag form
	 * @param action Archivo que procesará el formulario
	 * @param onsubmit Función javascript a ejecutar al momento de enviar el formulario
	 * @param method Método que se usará, post o get
	 * @param upload True si el formulario se usa para subir archivos al servidor
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function bForm ($action = AKI, $onsubmit = false, $method = 'post', $upload = false) {
		$upload = $upload ? 'enctype="multipart/form-data"' : '';
                $onsubmit = $onsubmit ? 'onsubmit="return '.$onsubmit.'(this);"' : '';
		return MiSiTiO::generar('form/bForm.html', array('action'=>$action, 'method'=>$method, 'onsubmit'=>$onsubmit, 'upload'=>$upload));
	}

	/**
	 * Finaliza el formulario, tag /form
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function eForm () {
                return MiSiTiO::generar('form/eForm.html');
	}

	/**
	 * Inicia el formulario para formularios que suben archivos}
         * @param action Archivo que procesará el formulario
	 * @param onsubmit Función javascript a ejecutar al momento de enviar el formulario
	 * @param method Método que se usará, post o get
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2010-05-22
	 */
	public static function bFormUp ($action = AKI, $onsubmit = false, $method = 'post') {
		return self::bForm($action, $onsubmit, $method, true);
	}

	/**
	 * Agrega un botón submit con nombre add
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function addButton () {
                return self::submitButton(LANG_FORM_ADD, 'add');
	}

	/**
	 * Agrega un botón submit con nombre edit
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function editButton () {
                return self::submitButton(LANG_FORM_EDIT, 'edit');
	}

	/**
	 * Agrega un botón submit con nombre save
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function saveButton () {
                return self::submitButton(LANG_FORM_SAVE, 'save');
	}

	/**
	 * Agrega un botón submit con nombre next
	 * @param step Número del siguiente paso (por ejemplo un segundo formulario)
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function nextButton ($step = 2) {
                return self::submitButton(LANG_FORM_NEXT, 'next'.$step);
	}

	/**
	 * Agrega un botón submit con un valor cualquiera
	 * @param value Valor que se asignará al botón
         * @param name Atributo name del boton
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function submitButton ($value = LANG_FORM_SUBMIT, $name = 'submit') {
		return MiSiTiO::generar('form/submitButton.html', array('value'=>$value, 'name'=>$name));
	}

	/**
	 * Agrega un botón reset con un valor cualquiera
	 * @param value Valor que se asignará al botón
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function resetButton ($value = LANG_FORM_RESET) {
		return MiSiTiO::generar('form/resetButton.html', array('value'=>$value));
	}

	/**
	 * Agrega un input como campo de texto
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @param value Valor del elemento en el formulario
	 * @param help Cuadro de ayuda para el elemento
	 * @param atrib Atributos para el elemento
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function input ($label = '', $name = '', $value = '', $help = '', $atrib = '') {
		if(empty($label) || empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			if(!empty($help)) $help = self::helpbox($name, $help);
			return MiSiTiO::generar('form/input.html', array('label'=>$label, 'name'=>$name, 'value'=>$value, 'atrib'=>$atrib, 'help'=>$help));
		}
	}

	/**
	 * Agrega un input como campo de texto
	 * @param name Nombre del campo en el formulario
	 * @param value Valor del elemento en el formulario
	 * @param maxlength Cantidad de caracteres máximos para el elemento
	 * @param atrib Atributos para el elemento
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-31
	 */
	public static function input4table ($name = '', $value = '', $maxlength = '', $atrib = '') {
		if(empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			return MiSiTiO::generar('form/input4table.html', array('atrib'=>$atrib, 'name'=>$name, 'value'=>$value, 'maxlength'=>$maxlength));
		}		
	}
	
	/**
	 * Agrega un área de texto
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @param value Valor del elemento en el formulario
	 * @param help Cuadro de ayuda para el elemento
         * @param atrib Atributos para el elemento
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function textarea ($label = '', $name = '', $value = '', $help = '', $atrib = '') {
		if(empty($label) || empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			if(!empty($help)) $help = self::helpbox($name, $help);
                        return MiSiTiO::generar('form/textarea.html', array('label'=>$label, 'name'=>$name, 'value'=>$value, 'atrib'=>$atrib, 'help'=>$help));
		}
	}

	/**
	 * Agrega campo browse, para cargar un archivo
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function file ($label = '', $name = '') {
		if(empty($label) || empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			return MiSiTiO::generar('form/file.html', array('label'=>$label, 'name'=>$name));
		}
	}

	/**
	 * Agrega un campo para cargar una fecha utilizando javascript
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @param value Valor del elemento en el formulario
         * @param readonly =true el campo sera de solo lectura
	 * @param help Cuadro de ayuda para el elemento
         * @param atrib Atributos para el elemento
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function inputDate ($label = '', $name = '', $value = '', $readonly = true, $help = '', $atrib = '') {
		if(empty($label) || empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			if(!empty($help)) $help = self::helpbox($name, $help);
                        $readonly = $readonly ? 'readonly="readonly" ' : '';
			return MiSiTiO::generar('form/inputDate.html', array('label'=>$label, 'name'=>$name, 'value'=>$value, 'readonly'=>$readonly, 'help'=>$help, 'atrib'=>$atrib));
		}
	}

	/**
	 * Agrega un campo para cargar una fecha utilizando javascript
	 * @param name Nombre del campo en el formulario
	 * @param value Valor del elemento en el formulario
	 * @param atrib Atributos para el elemento
         * @param readonly =true el campo sera de solo lectura
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-05-23
	 */
	public static function inputDate4table ($name = '', $value = '', $atrib = '', $readonly = true) {
		if(empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
                        $readonly = $readonly ? 'readonly="readonly" ' : '';
			return MiSiTiO::generar('form/inputDate4table.html', array('name'=>$name, 'value'=>$value, 'readonly'=>$readonly, 'atrib'=>$atrib));
		}
	}
	
	/**
	 * Agrega un campo de contraseña
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
         * @param help Cuadro de ayuda para el elemento
         * @param atrib Atributos para el elemento
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-04-20
	 */
	public static function pass ($label = '', $name = '', $help = '', $atrib = '') {
		if(empty($label) || empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
                        if(!empty($help)) $help = self::helpbox($name, $help);
			return MiSiTiO::generar('form/pass.html', array('label'=>$label, 'name'=>$name, 'help'=>$help, 'atrib'=>$atrib));
		}
	}

	/**
	 * Mediante valores de un arreglo crea una lista desplegable
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @param opciones Arreglo de arreglos de las opciones de la lista desplegable
	 * @param selected Valor del elemento en el formulario
	 * @param help Cuadro de ayuda para el elemento
	 * @param atrib Atributos para el elemento
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function select ($label, $name, $opciones, $selected = '', $help = '', $atrib = '') {
		if(empty($label) || empty($name) || !is_array($opciones)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			if(!empty($help)) $help = self::helpbox($name, $help);
                        $selectItem = '';
			foreach($opciones as $opcion) {
				$valor = array_shift($opcion);
                                $descripcion = array_shift($opcion);
				$seleccionado = (!strcmp($selected, $valor)) ? 'selected="selected"' : '';
				$selectItem .= MiSiTiO::generar('form/selectItem.html', array('valor'=>$valor, 'descripcion'=>$descripcion, 'seleccionado'=>$seleccionado));
			}
                        return MiSiTiO::generar('form/select.html', array('label'=>$label, 'name'=>$name, 'atrib'=>$atrib, 'selectoption'=>LANG_FORM_SELECTOPTION, 'selectItem'=>$selectItem, 'help'=>$help));
		}
	}

	/**
	 * Mediante valores de un arreglo crea una lista desplegable
	 * @param name Nombre del campo en el formulario
	 * @param opciones Arreglo de arreglos de las opciones de la lista desplegable
	 * @param selected Valor del elemento en el formulario
	 * @param atrib Atributos para el elemento
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-31
	 */
	public static function select4table ($name, $opciones, $selected = '', $atrib = '') {
		if(empty($name) || !is_array($opciones)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
                        $selectItem = '';
			foreach($opciones as $opcion) {
				$valor = array_shift($opcion);
                                $descripcion = array_shift($opcion);
				$seleccionado = (!strcmp($selected, $valor)) ? 'selected="selected"' : '';
				$selectItem .= MiSiTiO::generar('form/selectItem.html', array('valor'=>$valor, 'descripcion'=>$descripcion, 'seleccionado'=>$seleccionado));
			}
                        return MiSiTiO::generar('form/select4table.html', array('name'=>$name, 'selectoption'=>LANG_FORM_SELECTOPTION, 'selectItem'=>$selectItem, 'atrib'=>$atrib));
		}
	}
	
	/**
	 * Crea un checkbox
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @param selected Valor del elemento en el formulario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function checkbox ($label, $name, $selected = false) {
		if(empty($label) || empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			if($selected) $selected = 'checked="checked"';
			return MiSiTiO::generar('form/checkbox.html', array('label'=>$label, 'name'=>$name, 'selected'=>$selected));
		}
	}

	/**
	 * Crea un checkbox para seleccionar/deseleccionar todo un grupo de checkbox
	 * @param class nombre del atributo class de los input checkbox
	 * @param selected Valor del elemento en el formulario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-23
	 */
	public static function checkboxSwitch ($class, $selected = false) {
		if(empty($class)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			if($selected) $selected = 'checked="checked"';
			return MiSiTiO::generar('form/checkboxSwitch.html', array('class'=>$class, 'selected'=>$selected));
		}
	}
	
	/**
	 * Crea un checkbox para ser utilizado en una tabla
	 * @param name Nombre del campo en el formulario
	 * @value value Valor que identifica a la fila de la tabla
	 * @param selected Valor del elemento en el formulario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-23
	 */
	public static function checkbox4table ($name, $value, $selected = false) {
		if(empty($name) || empty($value)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			if($selected) $selected = 'checked="checked"';
			return MiSiTiO::generar('form/checkbox4table.html', array('name'=>$name, 'value'=>$value, 'selected'=>$selected));
		}
	}
	
	/**
	 * Crea una lista de campos checklist
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @param opciones Arreglo de arreglos de las opciones de la lista de checklist
	 * @param chequeados Elementos checklist que están seleccionados
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function checkList ($label, $name, $opciones, $chequeados = array()) {
		if(empty($label) || empty($name) || !is_array($opciones)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
                        $checkListItem = '';
			foreach($opciones as &$opcion) {
				$valor = array_shift($opcion);
                                $descripcion = array_shift($opcion);
				$checked = in_array($valor, $chequeados) ? $checked = ' checked="checked"' : '';
                                $checkListItem .= MiSiTiO::generar('form/checkListItem.html', array('name'=>$name, 'valor'=>$valor, 'descripcion'=>$descripcion, 'checked'=>$checked));
			}
			return MiSiTiO::generar('form/checkList.html', array('label'=>$label, 'checkListItem'=>$checkListItem));
		}
	}

	/**
	 * Crea una lista de radios
	 * @param label Nombre del elemento en el formulario
	 * @param name Nombre del campo en el formulario
	 * @param opciones Arreglo de arreglos de las opciones de la lista de radios
	 * @param selected Key del radio seleccionado
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function radio ($label, $name, $opciones, $selected = null) {
		if(empty($label) || empty($name) || !is_array($opciones)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
                        $radioItem = '';
			foreach($opciones as &$opcion) {
                                $valor = array_shift($opcion);
                                $descripcion = array_shift($opcion);
                                $seleccionado = $valor==$selected ? 'checked="checked"' : '';
                                $radioItem .= MiSiTiO::generar('form/radioItem.html', array('name'=>$name, 'valor'=>$valor, 'descripcion'=>$descripcion, 'selected'=>$seleccionado));
                        }
                        return MiSiTiO::generar('form/radio.html', array('label'=>$label, 'radioItem'=>$radioItem));
		}
	}

	/**
	 * Agrega un campo oculto
	 * @param name Nombre del campo en el formulario
	 * @param value Valor del elemento en el formulario
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function hidden ($name = '', $value = '') {
		if(empty($name)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
			return MiSiTiO::generar('form/hidden.html', array('name'=>$name, 'value'=>$value));
		}
	}

	/**
	 * Agrega un texto
	 * @param label Nombre del elemento en el formulario
	 * @param txt Texto a colocar
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	public static function text ($label = '', $txt = '') {
		if(empty($label)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
                        return MiSiTiO::generar('form/texto.html', array('label'=>$label, 'txt'=>$txt));
		}
	}

	/**
	 * Agrega campos para agregar campos desde javascript
	 * @param label Nombre del elemento en el formulario
	 * @param div Tag div donde irán los elementos generados mediante javascript
	 * @param js Nombre de la función javascript usada para cargar los campos dinámicamente
	 * @param campos Campos en caso que ya existan, si no al menos un campo vacio
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	*/
	public static function fromJS ($label = '', $div = '', $js = '', $campos = '') {
		if(empty($label) || empty($div) || empty($js) || empty($campos)) {
			MiSiTiO::error(LANG_ERROR_INPUT_TITLE, LANG_ERROR_INPUT_MSG);
		} else {
                        return MiSiTiO::generar('form/fromJS.html', array('label'=>$label, 'div'=>$div, 'js'=>$js, 'campos'=>$campos, 'add'=>LANG_FORM_FROMJS_ADD));
		}
	}

	/**
	 * Genera cuadro de ayuda
	 * @param div Identificador del div donde se desea colocar al cuadro de ayuda
	 * @param txt Texto que irá en el cuadro de ayuda
	 * @return Link al cuadro de ayuda
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-08
	 */
	private static function helpbox ($div, $txt) {
        	return MiSiTiO::generar('form/helpbox.html', array('id'=>$div.'Helpbox', 'txt'=>$txt));
	}

	/**
	 * Generar el enlace con la imagen para lanzar la búsqueda
	 * @return String Enlace para buscar 
	 * @author DeLaF, esteban[at]delaf.cl
	 * @version 2011-03-29
	 */
	public static function buscar () {
		return MiSiTiO::generar('form/buscar.html');
	}
	
}

?>
