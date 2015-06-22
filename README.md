MiInTrAnEt
==========

**DEPRECATED: esta aplicación derivó en [MiPaGiNa](https://github.com/estebandelaf/mipagina), la cual también está obsoleta ya que actualmente se hace el desarrollo de [SowerPHP](https://github.com/SowerPHP).**

**¡¡¡No utilizar este proyecto!!!**

**Se deja sólo como referencia del origen de MiPaGiNa.**

MiInTrAnEt es un framework para el desarrollo de aplicaciones web orientado a empresas. El nombre surgió puesto que la primera aplicación que se realizaría sería una Intranet, sin embargo puede ser utilizada para desarrollar cualquier aplicación web, ya que su principal ventaja es ser completamente modular.

Los módulos: admin, perfil, rrhh, servicios y exportar más cualquier directorio o archivo fuera de la carpeta *modulos* corresponde a la base de la aplicación. Esta es la que se distribuye de manera oficial mediante los enlaces de descarga, sin embargo este sitio o terceros podrán ofrecer módulos para la aplicación.

Intencionalmente se dejaron todas las clases y códigos de otros *vendors*, ya que de esta forma el proyecto está completo. Se hizó así para no tener que agregar composer ni modificar la aplicación para subirla (después de todo no se debe usar y está solo como referencia).

Snapshot generado el lun may 23 00:28:54 CLT 2011

Características
---------------

- Sistema multiusuario, con grupos de usuarios y permisos asociados a estos últimos.
- Exportación de datos a formatos CSV, PDF, ODS y XLS.
- Uso de atajos por teclado para el acceso a los módulos principales.
- Sistema permite tener módulos y submódulos (con n niveles).
- Se dispone de elementos gráficos como popup emergentes y bloques de texto para entregar ayuda a los usuarios.
- Sistema desarrollado utilizando lenguajes de programación PHP, XHTML, JavaScript, CSS y SQL.
- Utilización de base de datos MySQL, PostgreSQL u Oracle.
- Programación orientada a objetos donde se dispone de múltiples clases para ordenar, encapsular y estándarizar el código de la aplicación.
- Generación automática de clases para el trabajo con tablas de la base de datos.
- Generación automática de mantenedores para el trabajo con tablas de la base de datos.
- Existe un árbol de directorio estandarizado para el almacenamiento de los diferentes archivos (clases, funciones, paquetes de idioma, imágenes, etc).
- Soporte de internacionalización (por defecto sistema en español e inglés).
- Utiliza plantillas para separar el diseño de la lógica.

Módulos
-------

- **admin**: administración de usuarios (usuarios, grupos y permisos), empresa (sucursales, noticias y enlaces), parámetros del sistema e interacción con la base de datos.
- **perfil**: permite a los usuarios cambiar opciones que afecten el funcionamiento de la página, como su nombre, contraseña e idioma por defecto. Además tienen la posibilidad de asociar un avatar (imágen en miniatura) a su cuenta y establecer información de contacto como teléfono y correo electrónico. Los usuarios tendrán la posibilidad de crear un menú con sus propios enlaces, lo cuales pueden ser de la misma aplicación o de sitios externos.
- **rrhh**: sistema de personal (básico) que permite manejar datos del personal, áreas y cargos.
- **productos**: manejo de productos y stock.
- **servicios**: directorio de usuarios, enlaces de interés, mapa del sitio y utilidades.
- **exportar**: no es propiamente un módulo accesible por los usuarios, es usado indirectamente al solicitar la exportación de datos a los formatos antes mencionados.

Bibliotecas externas
--------------------

Esta aplicación utiliza una serie de bibliotecas externas, las cuales tienen sus propios autores y licencias, estas son:

- **[Libchart](http://naku.dohcrew.com/libchart)**: licencia [GPL3](http://www.gnu.org/licenses/gpl.txt), ubicación *class/other/libchart*
- **[Php Excel Reader](http://code.google.com/p/php-excel-reader)**: Vadim Tkachenko, licencia [PHP License 3.0](http://www.php.net/license/3_0.txt), ubicación *class/other/excel_reader.php*
- **[Spreadsheet Excel Writer](http://pear.php.net/package/Spreadsheet_Excel_Writer)**: licencia [LGPL](http://www.gnu.org/licenses/lgpl.html), ubicación *repositorio PEAR del sistema (se debe instalar aparte la biblioteca)
- **odsPhpGenerator**: Laurent VUIBERT, [LGPL](http://www.gnu.org/licenses/lgpl.html), ubicación *class/other/odsPhpGenerator*
- **ods-php**: Juan Lao Tebar y Jose Carlos Norte, licencia [LGPL3](http://www.gnu.org/licenses/lgpl-3.0.txt), ubicación *class/other/class.ods_jlt.php*
- **[PDF ROS](http://www.ros.co.nz/pdf)**: licencia [Public domain](http://en.wikipedia.org/wiki/Public_domain), ubicación *class/other/pdf-ros*
- **[Calendar](http://www.dynarch.com/projects/calendar)**: licencia [LGPL](http://www.gnu.org/licenses/lgpl.html), ubicación *js/calendar*
- **[JQuery](http://jquery.com)**: licencia [MIT and GPL](http://jquery.org/license), ubicación *js/jquery.js*
- **[Image Barcode](http://pear.php.net/package/Image_Barcode)**: licencia [PHP License](http://www.php.net/license/3_01.txt), ubicación repositorio PEAR del sistema (se debe instalar aparte la biblioteca)
