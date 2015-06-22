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
 * Archivo de idioma español
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-26
 */

// LOGIN
define('LANG_LOGIN_INDEX_TITLE', 'Bienvenido');
define('LANG_LOGIN_INDEX_MSG', 'Ingrese a continuación su usuario y contraseña');
define('LANG_LOGIN_USER', 'Usuario');
define('LANG_LOGIN_PASS', 'Contraseña');
define('LANG_LOGIN_BUTTON', 'Ingresar');
define('LANG_LOGIN_PASSHELP', 'Si ha olvidado su usuario o contraseña contáctese con '.ADMIN_NAME.' al email '.ADMIN_MAIL);
define('LANG_LOGIN_ERROR_USER_TITLE', 'Usuario incorrecto');
define('LANG_LOGIN_ERROR_USER_MSG', 'El usuario ingresado no se encuentra registrado en nuestra base de datos');
define('LANG_LOGIN_ERROR_PASS_TITLE', 'Clave incorrecta');
define('LANG_LOGIN_ERROR_PASS_MSG', 'La clave ingresada no corresponde con la clave registrada para el usuario');

// OFFLINE
define('LANG_ERROR_OFFLINE_TITLE', 'Sitio OffLine');
define('LANG_ERROR_OFFLINE_MSG', 'Estimado usuario, el sitio se encuentra temporalmente fuera de servicio pues nos encontramos trabajando en el.<br /><br />Usted verá este mensaje en todas las páginas.<br /><br />Por favor vuelva más tarde.');

// ERRORES
// pagina de error
define('LANG_ERROR_PAGE_TITLE', 'Página de error');
define('LANG_ERROR_PAGE_MSG', 'Por favor vaya a nuestra <a href="/">página principal</a> para seguir navegando o bien vuelva atrás para ver el contenido anterior');
define('LANG_ERROR_PAGE_TITLE_400', 'solicitud incorrecta');
define('LANG_ERROR_PAGE_TITLE_401', 'acceso no autorizado');
define('LANG_ERROR_PAGE_TITLE_403', 'acceso no permitido');
define('LANG_ERROR_PAGE_TITLE_404', 'página no encontrada');
define('LANG_ERROR_PAGE_TITLE_500', 'error interno');
define('LANG_ERROR_PAGE_TITLE_XXX', 'inesperado');
define('LANG_ERROR_PAGE_400', 'El navegador a enviado una solicitud que el servidor no puede comprender.');
define('LANG_ERROR_PAGE_401', 'Usted no dispone de permisos para acceder a la dirección solicitada.');
define('LANG_ERROR_PAGE_403', 'Usted no dispone de permisos para acceder a la dirección solicitada.');
define('LANG_ERROR_PAGE_404', 'La dirección solicitada no existe en nuestro servidor.');
define('LANG_ERROR_PAGE_500', 'Se ha producido un error durante la ejecución de la página en el servidor.');
define('LANG_ERROR_PAGE_XXX', 'Se ha producido un error inesperado durante la ejecución de la página.');
// descripciones de errores
define('LANG_ERROR_NEED_TITLE', 'Faltan campos');
define('LANG_ERROR_NEED_MSG', 'No se han ingresado todos los campos requeridos');
define('LANG_ERROR_NOAUTH_TITLE', 'Acción desautorizada');
define('LANG_ERROR_NOAUTH_MSG', 'Usted no dispone de permisos suficientes para realizar la acción solicitada');
define('LANG_ERROR_INPUT_TITLE', 'Faltan parámetros');
define('LANG_ERROR_INPUT_MSG', 'Falta algún parametro del campo del formulario');
define('LANG_ERROR_BD_TITLE', 'base de datos');
define('LANG_ERROR_ACCESSINCORRECT_TITLE', 'acceso incorrecto');
define('LANG_ERROR_ACCESSINCORRECT_MSG', 'No puede ejecutar la página de la manera que lo hizo.');
// errores de archivo subidos
define('LANG_ERROR_FILE_SIZE', 'Tamaño del archivo excedido, máximo');
define('LANG_ERROR_FILE_IMAGESIZE', 'La imágen es muy grande, máximo');
define('LANG_ERROR_FILE_TYPE', 'Tipo de archivo inválido');
define('LANG_ERROR_FILE_UPLOAD', 'Problemas al subir el archivo');
// error template
define('LANG_ERROR_TEMPLATE_TITLE', 'Plantilla');
define('LANG_ERROR_TEMPLATE_MSG', 'no se ha encontrado la plantilla');

// RESULT failure or success
define('LANG_RESULT_SUCCESS_MSG', 'La solicitud fue exitosa');
define('LANG_RESULT_FAILURE_MSG', 'La solicitud falló');
define('LANG_RESULT_REDIRECT', 'Será redirigido en ');

// CLASE MYSQL
define('LANG_DB_ERROR', 'Se ha detectado un problema con la base de datos y la página no puede seguir siendo cargada.<br />El error fue:');
define('LANG_DB_CANTCLOSE', 'Ha habido un problema al cerrar la conexión con mysql.');
define('LANG_DB_EMPTY', 'No se ha indicado la sentencia SQL.');
define('LANG_DB_TABLE4COUNT', 'No se ha indicado una tabla en la que contar elementos.');
define('LANG_DB_SEARCHMISSPARAM', 'No se ha indicado una tabla, lo que se seleccionará, la(s) columna(s) o la(s) palabra(s) para poder buscar.');

// CLASE FORM
define('LANG_FORM_ADD', 'Agregar');
define('LANG_FORM_EDIT', 'Editar');
define('LANG_FORM_SAVE', 'Guardar');
define('LANG_FORM_SEARCH', 'Buscar');
define('LANG_FORM_NEXT', 'Siguiente &gt;&gt;');
define('LANG_FORM_LIST', 'Lista de');
define('LANG_FORM_SUBMIT', 'Enviar');
define('LANG_FORM_RESET', 'Limpiar');
define('LANG_FORM_FROMJS_ADD', 'agregar');
define('LANG_FORM_FROMJS_DELETE', 'eliminar');
define('LANG_FORM_SELECTOPTION', 'Seleccione una opción');
define('LANG_FORM_REQUIRED', 'Estos campos son obligatorios');

// CLASE ARCHIVO
define('LANG_ARCHIVO_NOTEXIST', 'El archivo especificado no existe');
define('LANG_ARCHIVO_NOTVALID', 'no es un fichero o directorio válido');

// CLASE EMAIL
define('LANG_EMAIL_MSGSENT', 'El mensaje ha sido enviado a');

// CLASE TABLA
define('LANG_TABLA_EXPORT_XLS', 'Exportar en formato Microsoft Excel');
define('LANG_TABLA_EXPORT_ODS', 'Exportar en formato OpenDocument Spreadsheet');
define('LANG_TABLA_EXPORT_CSV', 'Exportar en formato de texto plano');
define('LANG_TABLA_EXPORT_PDF', 'Exportar en formato PDF');
define('LANG_TABLA_EXPORT_XML', 'Exportar en formato XML');
define('LANG_TABLA_SHOW', 'Mostrar tabla');
define('LANG_TABLA_HIDE', 'Ocultar tabla');
define('LANG_TABLA_ACTIONS', 'Acciones');
define('LANG_TABLA_NEW', 'Nuevo');
define('LANG_TABLA_EDIT', 'Editar');
define('LANG_TABLA_DELETE', 'Eliminar');
define('LANG_TABLA_NEXT', 'Siguiente');

// MANTENEDORES
define('LANG_MAINTAINER_TITLE', 'Mantenedor de');

// PAGINADOR
define('LANG_PAGINATOR_FIRSTPAGE', 'Primera página');
define('LANG_PAGINATOR_LASTPAGE', 'Última página');
define('LANG_PAGINATOR_NEXTGROUP', 'Pŕoximo grupo de páginas');
define('LANG_PAGINATOR_PREVGROUP', 'Anterior grupo de páginas');
define('LANG_PAGINATOR_SHOW', 'Mostrar paginador');
define('LANG_PAGINATOR_HIDE', 'Ocultar paginador');

// PANEL INFORMACION
define('LANG_INFO_ID', 'ID');
define('LANG_INFO_USER', 'Usuario');
define('LANG_INFO_IP', 'IP');
define('LANG_INFO_NEWS', 'Noticias');
define('LANG_INFO_NEWS_READMORE', 'leer más');
define('LANG_INFO_NEWS_VIEWALL', 'ver todas');

// PANEL INFERIOR
define('LANG_PANEL_START', 'Ir a la página principal');
define('LANG_PANEL_UTILITIES', 'Utilidades');
define('LANG_PANEL_DIRECTORY', 'Directorio');
define('LANG_PANEL_LINKS', 'Enlaces');
define('LANG_PANEL_MAP', 'Geoposicionamiento');
define('LANG_PANEL_PERM', 'Cambiar permisos para esta página');
define('LANG_PANEL_ADMIN', 'Módulo de administración');
define('LANG_PANEL_PROFILE', 'Perfil del usuario');
define('LANG_PANEL_PRINT', 'Imprimir la página');
define('LANG_PANEL_RELOAD', 'Recargar la página');
define('LANG_PANEL_RSS', 'Leer noticias en RSS');
define('LANG_PANEL_SITEMAP', 'Mapa del sitio');
define('LANG_PANEL_HELP', 'Mostrar ayuda de la aplicación');
define('LANG_PANEL_INFO', 'Mostrar información de la aplicación');
define('LANG_PANEL_LOGOUT', 'Salir de la aplicación');

// PAGINA NOTICIAS
define('LANG_NEWS_TITLE', 'Noticias');
define('LANG_INFO_BIRTHDAY', 'Próximos cumpleaños');

// VARIOS
define('LANG_AND', 'y');

// CAMBIO DE CLAVE OBLIGATORIO
define('LANG_NEWPASS_TITLE', 'Cambio de contraseña');
define('LANG_NEWPASS_MSG', 'Recuerde que su contraseña no puede contener ni el nombre de su usuario ni su id en ella.');
define('LANG_NEWPASS_SUCCESS', 'Su clave ha sido actualizada, vaya a la <a href="/">página principal</a> para entrar al sistema.');
define('LANG_NEWPASS_FORM_USER', 'Usuario');
define('LANG_NEWPASS_FORM_PASS', 'Contraseña actual');
define('LANG_NEWPASS_FORM_PASS1', 'Contraseña nueva');
define('LANG_NEWPASS_FORM_PASS2', 'Repetir contraseña');
define('LANG_PASSRECOVERY_RECOVERY', '¿olvido su usuario o contraseña?');
define('LANG_PASSRECOVERY_TITLE', 'Recuperación de contraseña');
define('LANG_PASSRECOVERY_FORM_ID', 'ID');
define('LANG_PASSRECOVERY_MSG1', 'Para recuperar su contraseña debe tener previamente asociado un correo electrónico a su cuenta, si es así ingrese a continuación su ID (run sin puntos ni dígito verificador) y un correo con las instrucciones le será enviado.');
define('LANG_PASSRECOVERY_EMAILSENT', 'Correo electrónico enviado a {email} con las instrucciones para la recuperación de su contraseña.');
define('LANG_PASSRECOVERY_EMAILNOTSENT', 'Correo no enviado.');
define('LANG_PASSRECOVERY_MSG2', 'Por favor ingrese su nueva contraseña');
define('LANG_PASSRECOVERY_ERROR_USER', 'El usuario ingresado no se encuentra registrado en nuestra base de datos o bien no está activo.');
define('LANG_PASSRECOVERY_ERROR_EMAIL', 'No existe correo asociado al usuario, deberá contactar al administrador del sistema.');
define('LANG_PASSRECOVERY_ERROR_KEY', 'Hash/key utilizado es inválido.');

// MESES
define('LANG_MONTH_JANUARY', 'Enero');
define('LANG_MONTH_FEBRUARY', 'Febrero');
define('LANG_MONTH_MARCH', 'Marzo');
define('LANG_MONTH_APRIL', 'Abril');
define('LANG_MONTH_MAY', 'Mayo');
define('LANG_MONTH_JUNE', 'Junio');
define('LANG_MONTH_JULY', 'Julio');
define('LANG_MONTH_AUGUST', 'Agosto');
define('LANG_MONTH_SEPTEMBER', 'Septiembre');
define('LANG_MONTH_OCTOBER', 'Octumbre');
define('LANG_MONTH_NOVEMBER', 'Noviembre');
define('LANG_MONTH_DECEMBER', 'Diciembre');
define('LANG_MONTH_JANUARY_SHORT', 'Ene');
define('LANG_MONTH_FEBRUARY_SHORT', 'Feb');
define('LANG_MONTH_MARCH_SHORT', 'Mar');
define('LANG_MONTH_APRIL_SHORT', 'Abr');
define('LANG_MONTH_MAY_SHORT', 'May');
define('LANG_MONTH_JUNE_SHORT', 'Jun');
define('LANG_MONTH_JULY_SHORT', 'Jul');
define('LANG_MONTH_AUGUST_SHORT', 'Ago');
define('LANG_MONTH_SEPTEMBER_SHORT', 'Sep');
define('LANG_MONTH_OCTOBER_SHORT', 'Oct');
define('LANG_MONTH_NOVEMBER_SHORT', 'Nov');
define('LANG_MONTH_DECEMBER_SHORT', 'Dic');

// DIAS
define('LANG_DAY_MONDAY', 'Lunes');
define('LANG_DAY_TUESDAY', 'Martes');
define('LANG_DAY_WEDNESDAY', 'Miércoles');
define('LANG_DAY_THURSDAY', 'Jueves');
define('LANG_DAY_FRIDAY', 'Viernes');
define('LANG_DAY_SATURDAY', 'Sábado');
define('LANG_DAY_SUNDAY', 'Domingo');

// footer
define('LANG_FOOTER_BY', 'Desarrollado por');

// PDF
define('LANG_PDF_RUT', 'RUT');
define('LANG_PDF_MAINOFFICE', 'Casa matriz');
define('LANG_PDF_EMAIL', 'Email');
define('LANG_PDF_TELEPHONE', 'Teléfono');
define('LANG_PDF_FAX', 'Fax');
define('LANG_PDF_FILE', 'Archivo');
define('LANG_PDF_FILEBY', 'generado por');

?>
