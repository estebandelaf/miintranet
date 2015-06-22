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

require('inc.php');

echo MiSiTiO::generar('web1.html', array(
    'lang'=>$Usuario->lang
    , 'title'=>SITE_TITLE
    , 'url'=>AKI
    , 'jsMod'=>MiSiTiO::jsMod()
    , 'cssMod'=>MiSiTiO::cssMod()
    , 'calendar_lang'=>file_exists(DIR.'/js/calendar/calendar-'.$Usuario->lang.'.js') ? $Usuario->lang : LANG
    , 'nav'=>MiSiTiO::nav($nav)
));

?>
