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
 * Archivo de idioma inglés
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-26
 */

// LOGIN
define('LANG_LOGIN_INDEX_TITLE', 'Welcome');
define('LANG_LOGIN_INDEX_MSG', 'Please insert your user and password');
define('LANG_LOGIN_USER', 'Username');
define('LANG_LOGIN_PASS', 'Password');
define('LANG_LOGIN_BUTTON', 'Login');
define('LANG_LOGIN_PASSHELP', 'If you forgot your user or password please contact with '.ADMIN_NAME.' to the email '.ADMIN_MAIL);
define('LANG_LOGIN_ERROR_USER_TITLE', 'Incorrect user');
define('LANG_LOGIN_ERROR_USER_MSG', 'The user name that you provide is not registered into our database');
define('LANG_LOGIN_ERROR_PASS_TITLE', 'Password incorrect');
define('LANG_LOGIN_ERROR_PASS_MSG', 'The password that you provide is not the user password');

// OFFLINE
define('LANG_ERROR_OFFLINE_TITLE', 'Site OffLine');
define('LANG_ERROR_OFFLINE_MSG', 'Dear user, the site is temporaly offline because we are working on this.<br /><br />You will see this message in all pages.<br /><br />Please come back later.');

// ERRORES
// pagina de error
define('LANG_ERROR_PAGE_TITLE', 'Error page');
define('LANG_ERROR_PAGE_MSG', 'Please go to our <a href="/">homepage</a> for continue navigation or <a href="javascript:history.back(1)">go back</a> to see the previous page');
define('LANG_ERROR_PAGE_TITLE_400', 'bad request');
define('LANG_ERROR_PAGE_TITLE_401', 'unauthorized');
define('LANG_ERROR_PAGE_TITLE_403', 'forbidden');
define('LANG_ERROR_PAGE_TITLE_404', 'page not found');
define('LANG_ERROR_PAGE_TITLE_500', 'internal server error');
define('LANG_ERROR_PAGE_TITLE_XXX', 'unexpected');
define('LANG_ERROR_PAGE_400', 'The request cannot be fulfilled due to bad syntax.');
define('LANG_ERROR_PAGE_401', 'Authentication has failed or you not been provide the valid credentials.');
define('LANG_ERROR_PAGE_403', 'You do not have permission for acces to the page requested.');
define('LANG_ERROR_PAGE_404', 'The requested page not exist into our server.');
define('LANG_ERROR_PAGE_500', 'An error has been produced when the page was generated into the server.');
define('LANG_ERROR_PAGE_XXX', 'An unexpected error has been produced when the page was generated into the server.');
// descripciones de errores
define('LANG_ERROR_NEED_TITLE', 'Parameters missing');
define('LANG_ERROR_NEED_MSG', 'You has not all the required fields for the form');
define('LANG_ERROR_NOAUTH_TITLE', 'Action not allowed');
define('LANG_ERROR_NOAUTH_MSG', 'You do not have enough privileges for the action requested');
define('LANG_ERROR_INPUT_TITLE', 'Parameters missing');
define('LANG_ERROR_INPUT_MSG', 'You has not all the required fields for the form');
define('LANG_ERROR_BD_TITLE', 'database');
define('LANG_ERROR_ACCESSINCORRECT_TITLE', 'access incorrect');
define('LANG_ERROR_ACCESSINCORRECT_MSG', 'You can not display the page in the way that you do.');
// errores de archivo subidos
define('LANG_ERROR_FILE_SIZE', 'Size of file exceeded, maximum');
define('LANG_ERROR_FILE_IMAGESIZE', 'Size of image exceeded, maximum');
define('LANG_ERROR_FILE_TYPE', 'Invalid type of file');
define('LANG_ERROR_FILE_UPLOAD', 'The file can not was upload');
// error template
define('LANG_ERROR_TEMPLATE_TITLE', 'Template');
define('LANG_ERROR_TEMPLATE_MSG', 'template not found');

// RESULT failure or success
define('LANG_RESULT_SUCCESS_MSG', 'The request was successful');
define('LANG_RESULT_FAILURE_MSG', 'The request fail');
define('LANG_RESULT_REDIRECT', 'You will be redirected in');

// CLASE MYSQL
define('LANG_DB_ERROR', 'A problem with the database has been detected and the page can not be displayed.<br />The error was:');
define('LANG_DB_CANTCLOSE', 'Can not close the connection.');
define('LANG_DB_EMPTY', 'The sql query is empty.');
define('LANG_DB_TABLE4COUNT', 'You forgot the table in the sql query.');
define('LANG_DB_SEARCHMISSPARAM', 'You have left empty the table, what you need select, the columns or the words for search in the sql query.');

// CLASE FORM
define('LANG_FORM_ADD', 'Add');
define('LANG_FORM_EDIT', 'Edit');
define('LANG_FORM_SAVE', 'Save');
define('LANG_FORM_SEARCH', 'Search');
define('LANG_FORM_NEXT', 'Next &gt;&gt;');
define('LANG_FORM_LIST', 'List of');
define('LANG_FORM_SUBMIT', 'Send');
define('LANG_FORM_RESET', 'Reset');
define('LANG_FORM_FROMJS_ADD', 'add');
define('LANG_FORM_FROMJS_DELETE', 'delete');
define('LANG_FORM_SELECTOPTION', 'Select an option');
define('LANG_FORM_REQUIRED', 'This fields are required');

// CLASE ARCHIVO
define('LANG_ARCHIVO_NOTEXIST', 'The file was not found');
define('LANG_ARCHIVO_NOTVALID', 'is not a file or directory valid');

// CLASE EMAIL
define('LANG_EMAIL_MSGSENT', 'The message was sent to');

// CLASE TABLA
define('LANG_TABLA_EXPORT_XLS', 'Export to Microsoft Excel');
define('LANG_TABLA_EXPORT_ODS', 'Export to OpenDocument Spreadsheet');
define('LANG_TABLA_EXPORT_CSV', 'Export to plain text');
define('LANG_TABLA_EXPORT_PDF', 'Export to PDF');
define('LANG_TABLA_EXPORT_XML', 'Export to XML');
define('LANG_TABLA_SHOW', 'Show table');
define('LANG_TABLA_HIDE', 'Hide table');
define('LANG_TABLA_ACTIONS', 'Actions');
define('LANG_TABLA_NEW', 'New');
define('LANG_TABLA_EDIT', 'Edit');
define('LANG_TABLA_DELETE', 'Delete');
define('LANG_TABLA_NEXT', 'Next');

// MANTENEDORES
define('LANG_MAINTAINER_TITLE', 'Maintainer of');

// PAGINADOR
define('LANG_PAGINATOR_FIRSTPAGE', 'First page');
define('LANG_PAGINATOR_LASTPAGE', 'Last page');
define('LANG_PAGINATOR_NEXTGROUP', 'Next group of pages');
define('LANG_PAGINATOR_PREVGROUP', 'Previous group of pages');
define('LANG_PAGINATOR_SHOW', 'Show paginator');
define('LANG_PAGINATOR_HIDE', 'Hide paginator');

// PANEL INFORMACION
define('LANG_INFO_ID', 'ID');
define('LANG_INFO_USER', 'User');
define('LANG_INFO_IP', 'IP');
define('LANG_INFO_NEWS', 'News');
define('LANG_INFO_NEWS_READMORE', 'read more');
define('LANG_INFO_NEWS_VIEWALL', 'view all');

// PANEL INFERIOR
define('LANG_PANEL_START', 'Go to the homepage');
define('LANG_PANEL_UTILITIES', 'Utilities');
define('LANG_PANEL_DIRECTORY', 'Directory');
define('LANG_PANEL_LINKS', 'Links');
define('LANG_PANEL_MAP', 'Geoposition');
define('LANG_PANEL_PERM', 'Change permissions for this page');
define('LANG_PANEL_ADMIN', 'Module of administration');
define('LANG_PANEL_PROFILE', 'Profile of the user');
define('LANG_PANEL_PRINT', 'Print the page');
define('LANG_PANEL_RELOAD', 'Reload the page');
define('LANG_PANEL_RSS', 'Read news in RSS');
define('LANG_PANEL_SITEMAP', 'Sitemap');
define('LANG_PANEL_HELP', 'Show help about the application');
define('LANG_PANEL_INFO', 'Show info about the application');
define('LANG_PANEL_LOGOUT', 'Logout of aplication');

// PAGINA NOTICIAS
define('LANG_NEWS_TITLE', 'News');
define('LANG_INFO_BIRTHDAY', 'Birthdays');

// VARIOS
define('LANG_AND', 'and');

// CAMBIO DE CLAVE OBLIGATORIO
define('LANG_NEWPASS_TITLE', 'Change password');
define('LANG_NEWPASS_MSG', 'Remember that you can not use your username or your id into your password.');
define('LANG_NEWPASS_SUCESS', 'Your password was been updated, go to the <a href="/">home page</a> to login.');
define('LANG_NEWPASS_FORM_USER', 'Username');
define('LANG_NEWPASS_FORM_PASS', 'Current password');
define('LANG_NEWPASS_FORM_PASS1', 'New password');
define('LANG_NEWPASS_FORM_PASS2', 'Repeat password');
define('LANG_PASSRECOVERY_RECOVERY', 'recover password or user?');
define('LANG_PASSRECOVERY_TITLE', 'Password recovery');
define('LANG_PASSRECOVERY_FORM_ID', 'ID');
define('LANG_PASSRECOVERY_MSG1', 'For recovery you need a email associated to your account, please write your ID (run without dots and dv).');
define('LANG_PASSRECOVERY_EMAILSENT', 'Email sent to {email} with the instructions for the recovery.');
define('LANG_PASSRECOVERY_EMAILNOTSENT', 'Email not sent.');
define('LANG_PASSRECOVERY_MSG2', 'Please write your password.');
define('LANG_PASSRECOVERY_ERROR_USER', 'The user not exist in the database or is not active.');
define('LANG_PASSRECOVERY_ERROR_EMAIL', 'The user do not have a email associated to the account, please contact to the administrator.');
define('LANG_PASSRECOVERY_ERROR_KEY', 'Hash/key used is invalid.');

// MESES
define('LANG_MONTH_JANUARY', 'January');
define('LANG_MONTH_FEBRUARY', 'February');
define('LANG_MONTH_MARCH', 'March');
define('LANG_MONTH_APRIL', 'April');
define('LANG_MONTH_MAY', 'May');
define('LANG_MONTH_JUNE', 'June');
define('LANG_MONTH_JULY', 'July');
define('LANG_MONTH_AUGUST', 'August');
define('LANG_MONTH_SEPTEMBER', 'September');
define('LANG_MONTH_OCTOBER', 'October');
define('LANG_MONTH_NOVEMBER', 'November');
define('LANG_MONTH_DECEMBER', 'December');
define('LANG_MONTH_JANUARY_SHORT', 'Jan');
define('LANG_MONTH_FEBRUARY_SHORT', 'Feb');
define('LANG_MONTH_MARCH_SHORT', 'Mar');
define('LANG_MONTH_APRIL_SHORT', 'Apr');
define('LANG_MONTH_MAY_SHORT', 'May');
define('LANG_MONTH_JUNE_SHORT', 'Jun');
define('LANG_MONTH_JULY_SHORT', 'Jul');
define('LANG_MONTH_AUGUST_SHORT', 'Aug');
define('LANG_MONTH_SEPTEMBER_SHORT', 'Sep');
define('LANG_MONTH_OCTOBER_SHORT', 'Oct');
define('LANG_MONTH_NOVEMBER_SHORT', 'Nov');
define('LANG_MONTH_DECEMBER_SHORT', 'Dec');

// DIAS
define('LANG_DAY_MONDAY', 'Monday');
define('LANG_DAY_TUESDAY', 'Tuesday');
define('LANG_DAY_WEDNESDAY', 'Wednesday');
define('LANG_DAY_THURSDAY', 'Thursday');
define('LANG_DAY_FRIDAY', 'Friday');
define('LANG_DAY_SATURDAY', 'Saturday');
define('LANG_DAY_SUNDAY', 'Sunday');

// FOOTR
define('LANG_FOOTER_BY', 'Developed by');

// PDF
define('LANG_PDF_RUT', 'RUT');
define('LANG_PDF_MAINOFFICE', 'Main office');
define('LANG_PDF_EMAIL', 'Email');
define('LANG_PDF_TELEPHONE', 'Telephone');
define('LANG_PDF_FAX', 'Fax');
define('LANG_PDF_FILE', 'File');
define('LANG_PDF_FILEBY', 'generated by');

?>
