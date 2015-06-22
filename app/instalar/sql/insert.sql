-- insertar sucursal del usuario inicial (casa matriz)
INSERT INTO sucursal VALUES ('{sucursal_id}', '{sucursal_glosa}', 1, '{sucursal_direccion}', {sucursal_comuna_id}, '{sucursal_email}', '{sucursal_telefono}', NULL, NULL);

-- insertar area de usuario inicial 
INSERT INTO area VALUES (1, '{area_glosa}');

-- insertar cargo del usuario inicial
INSERT INTO cargo VALUES (1, '{cargo_glosa}', 1, 0);

-- insertar grupo inicial (siempre root)
INSERT INTO grupo VALUES (1, 'root');

-- insertar usuario inicial
INSERT INTO usuario VALUES ({usuario_id}, '{usuario_clave}', NULL, NULL, NULL, '{usuario_nombre}', '{usuario_apellido}', '{usuario_fechanacimiento}', '{usuario_lang}', '{usuario_usuario}', 1, NULL, NULL, NULL, NULL, '{sucursal_id}', 1, '{usuario_ingreso}', NULL, NULL, NULL, NULL, NULL, NULL, '{usuario_email}', NULL, NULL, 20, 0, NULL, NULL);

-- insertar grupo del usuario inicial (siempre root)
INSERT INTO usuario_grupo VALUES ({usuario_id}, 1);

-- insertar modulos de la aplicacion
INSERT INTO modulo VALUES ('base', 'Base de la aplicación');
INSERT INTO modulo VALUES ('empresa', 'Opciones específicas para la empresa que ejecuta la app');
INSERT INTO modulo VALUES ('pdf', 'Módulo que genera los PDF');
INSERT INTO modulo VALUES ('perfil', 'Perfil de usuario');
INSERT INTO modulo VALUES ('rrhh', 'Recursos humanos');
insert into modulo values ('email', 'Parámetros para la clase Email');
insert into modulo values ('stock', 'Submódulo de productos');

-- insertar parametros de la aplicacion (algunos con valores por defecto para hacer mas simple el instalador)
INSERT INTO parametro VALUES ('ADMIN_IP', '{usuario_ip}', 'IP desde donde se conecta el admin de la app', 'base');
INSERT INTO parametro VALUES ('ADMIN_MAIL', '{usuario_email}', 'Correo del webmaster de la app', 'base');
INSERT INTO parametro VALUES ('ADMIN_NAME', '{usuario_nombre} {usuario_apellido}', 'Nombre del webmaster de la app', 'base');
INSERT INTO parametro VALUES ('AVATAR_SIZE_H', '100', 'Alto máximo del avatar del usuario', 'perfil');
INSERT INTO parametro VALUES ('AVATAR_SIZE_KB', '50', 'Tamaño máximo del avatar del usuario en KB', 'perfil');
INSERT INTO parametro VALUES ('AVATAR_SIZE_W', '100', 'Ancho máximo del avatar del usuario', 'perfil');
INSERT INTO parametro VALUES ('BARCODE_TYPE', 'code128', 'Tipo de código de barras a utilizar (ej: code128)', 'base');
INSERT INTO parametro VALUES ('CV_SIZE_KB', '200', 'Tamaño máximo del CV en KB', 'rrhh');
INSERT INTO parametro VALUES ('DATE_FORMAT', 'd/m/Y', 'Formato de la fecha (estilo PHP) para formatear las fechas', 'base');
INSERT INTO parametro VALUES ('DATE_FORMAT_SHORT', 'm/Y', 'Formato corto de la fecha (estilo PHP) para formatear las fechas', 'base');
INSERT INTO parametro VALUES ('DEBUG', '0', 'Permite activar los errores de PHP y mostrarlos, 0 en producción', 'base');
INSERT INTO parametro VALUES ('EMPRESA_ACTECO', '{empresa_acteco}', 'Código de actividad económica', 'empresa');
INSERT INTO parametro VALUES ('EMPRESA_NOMBRE_FANTASIA', '{empresa_nombre_fantasia}', 'Nombre de fantasía de la empresa', 'empresa');
INSERT INTO parametro VALUES ('EMPRESA_RAZON_SOCIAL', '{empresa_razon_social}', 'Razón social de la empresa', 'empresa');
INSERT INTO parametro VALUES ('EMPRESA_RLEGAL', '{empresa_rlegal}', 'Representante legal de la empresa', 'empresa');
INSERT INTO parametro VALUES ('EMPRESA_RUT', '{empresa_rut}', 'Rut de la empresa', 'empresa');
INSERT INTO parametro VALUES ('LANG', '{usuario_lang}', 'Lenguaje por defecto de la app', 'base');
INSERT INTO parametro VALUES ('NEWS_BODY', '70', 'Máximo número de caracteres a mostrar del cuerpo de las noticias', 'base');
INSERT INTO parametro VALUES ('NEWS_LIMIT', '3', 'Máximo número de noticias a mostrar', 'base');
INSERT INTO parametro VALUES ('NEWS_RSS_TTL', '15', 'TTL para el generador de noticias mediante RSS', 'base');
INSERT INTO parametro VALUES ('OFFLINE', '0', 'Permite desactivar el sitio, solo se podrá ingresar desde la IP del admin de la app', 'base');
INSERT INTO parametro VALUES ('PDF_MARGIN', '20,90,30,30', 'Margenes del documento: top,bottom,left,right', 'pdf');
INSERT INTO parametro VALUES ('PDF_ORIENTATION', 'portrait', 'Orientación de la pagina: portrait o landscape', 'pdf');
INSERT INTO parametro VALUES ('PDF_PAGE_HEIGHT', '790', 'Altura de la página (depende del tipo de hoja)', 'pdf');
INSERT INTO parametro VALUES ('PDF_PAGE_TYPE', 'LETTER', 'Formato/tipo de hoja a utilizar', 'pdf');
INSERT INTO parametro VALUES ('PDF_PAGE_WIDTH', '615', 'Ancho de la página (depende del tipo de hoja)', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_COLGAP', '5', 'Espacio entre texto y columnas de la tabla', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_FONTSIZE', '7', 'Tamaño del texto en la tabla', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_SHADECOL', '0.9,0.9,0.9', 'Color en formato RGB para el color de fondo de la línea par', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_SHADECOL2', '0.8,0.8,0.8', 'Color en formato RGB para el color de fondo de la línea impar', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_SHADED', '1', 'Color par las filas, =1 línea por medio, =0 sin color, =2 dos colores intercalados', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_SHOWLINES', '1', '=1 bordes, =0 sin bordes, =2 bordes y líneas entre filas', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_TEXTCOL', '0,0,0', 'Color en formato RGB para el color del texto de la tabla', 'pdf');
INSERT INTO parametro VALUES ('PDF_TABLE_ROWGAP', '2', 'Espacio entre texto y filas de la tabla', 'pdf');
INSERT INTO parametro VALUES ('BIRTHDAY_LIMIT', '5', 'Cantidad de personas que se mostrarán en la lista de cumpleaños', 'base');
INSERT INTO parametro VALUES ('PDF_TABLE_TITLEFONTSIZE', '10', 'Tamaño del título de la tabla del pdf', 'pdf');
INSERT INTO parametro VALUES ('PDF_TITLE_SIZE', '25', 'Tamaño del título del pdf', 'pdf');
INSERT INTO parametro VALUES ('REDIRECT_SECS', '3', 'Segundos antes de redireccionar a otra página', 'base');
INSERT INTO parametro VALUES ('SITE_TITLE', '{site_title}', 'Título de la aplicación', 'base');
INSERT INTO parametro VALUES ('TAB', '4', 'Tabs por defecto (según diseño css)', 'base');
INSERT INTO parametro VALUES ('TABLE_SHOW_PAGES', '10', 'Cantidad de páginas que se mostrarán en el paginador de tablas', 'base');
INSERT INTO parametro VALUES ('TABLE_SHOW_ROWS', '20', 'Cantidad de filas que se mostrarán en el paginador de tablas', 'base');
INSERT INTO parametro VALUES ('TEMPLATE', 'default', 'Plantilla por defecto de la app', 'base');
INSERT INTO parametro VALUES ('TMP', '{tmp}', 'Directorio temporal con permisos de escritura para el usuario que corre la app', 'base');
INSERT INTO parametro VALUES ('ZONA_HORARIA', '{zona_horaria}', 'Zona horaria a ser utilizada por la función date() de PHP', 'base');
INSERT INTO parametro VALUES ('AVATAR_MIMETYPE', 'image/gif,image/jpeg,image/png', 'Mimetype permitidos para las imágenes del avatar', 'perfil');
INSERT INTO parametro VALUES ('CV_MIMETYPE', 'application/pdf,application/msword,text/rtf', 'Mimetype permitidos para el curriculum', 'rrhh');
INSERT INTO parametro VALUES ('STOCK_REFRESH', '300', 'Segundos para recargar el informe de stock actual (=0 no se recarga)', 'stock');
insert into parametro values ('EMAIL_HOST', 'smtp.intranet.dev', 'Servidor smtp remoto, ej: smtp.intranet.dev o smtp.intranet.dev:25', 'email');
insert into parametro values ('EMAIL_USER', 'user@intranet.dev', 'Usuario para enviar el correo', 'email');
insert into parametro values ('EMAIL_PASS', 'pass', 'Clave del usuario smtp', 'email');
insert into parametro values ('EMAIL_FROM_NAME', '', 'Nombre del remitente, si esta en blanco se utiliza EMAIL_USER', 'email');
insert into parametro values ('EMAIL_SUBJECT_PREFIX', '', 'Prefijo para incluir en el asunto de los correos', 'email');

-- insertar permisos para recursos que solo requieren estar logueado como usuario
INSERT INTO permiso_login VALUES ('/exportar/barcode');
INSERT INTO permiso_login VALUES ('/exportar/csv');
INSERT INTO permiso_login VALUES ('/exportar/grafico');
INSERT INTO permiso_login VALUES ('/exportar/graficoTorta');
INSERT INTO permiso_login VALUES ('/exportar/ods');
INSERT INTO permiso_login VALUES ('/exportar/pdf');
INSERT INTO permiso_login VALUES ('/exportar/xls');
INSERT INTO permiso_login VALUES ('/exportar/xml');
INSERT INTO permiso_login VALUES ('/perfil/');
INSERT INTO permiso_login VALUES ('/perfil/avatar');
INSERT INTO permiso_login VALUES ('/perfil/avatar-view');
INSERT INTO permiso_login VALUES ('/perfil/clave');
INSERT INTO permiso_login VALUES ('/perfil/enlace_usuario');
INSERT INTO permiso_login VALUES ('/perfil/usuario');
INSERT INTO permiso_login VALUES ('/rrhh/avatar');
INSERT INTO permiso_login VALUES ('/servicios/ayuda');
INSERT INTO permiso_login VALUES ('/servicios/directorio');
INSERT INTO permiso_login VALUES ('/servicios/enlaces');
INSERT INTO permiso_login VALUES ('/servicios/mapasitio');
INSERT INTO permiso_login VALUES ('/servicios/utilidades/');
INSERT INTO permiso_login VALUES ('/servicios/utilidades/extraer');
INSERT INTO permiso_login VALUES ('/servicios/utilidades/formatearRut');
INSERT INTO permiso_login VALUES ('/servicios/utilidades/repartir');
