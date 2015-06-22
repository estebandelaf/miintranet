<?php

// determinar si el usuario puede cambiar los permisos de las paginas de la aplicacion
if($Usuario->autorizado('/admin/permisos',false))
        $permisos = MiSiTiO::generar('panel/permisos.html', array('url'=>urlencode(RECURSO), 'permiso'=>LANG_PANEL_PERM));
else $permisos = '';

// determinar si el usuario puede tener acceso al modulo admin
if($Usuario->autorizado('/admin/',false))
        $admin = MiSiTiO::generar('panel/admin.html', array('admin'=>LANG_PANEL_ADMIN));
else $admin = '';

// determinar si el usuario puede tener acceso al modulo mapa
if($Usuario->autorizado('/mapa/',false))
        $mapa = MiSiTiO::generar('panel/mapa.html', array('map'=>LANG_PANEL_MAP));
else $mapa = '';

// si el usuario esta conectado se muestra un panel de usuario, sino se muestra el footer
if($Usuario->autorizado('', false)) {
        $panel = MiSiTiO::generar('panel/panel.html', array(
                'inicio'=>LANG_PANEL_START
		, 'utilidades'=>LANG_PANEL_UTILITIES
		, 'directorio'=>LANG_PANEL_DIRECTORY
		, 'enlaces'=>LANG_PANEL_LINKS
		, 'mapa'=>$mapa
                , 'navUser'=>$Usuario->nav()
                , 'navMod'=>MiSiTiO::navMod($navModulo)
                , 'permisos'=>$permisos
		, 'admin'=>$admin
		, 'perfil'=>LANG_PANEL_PROFILE
                , 'imprimir'=>LANG_PANEL_PRINT
                , 'recargar'=>LANG_PANEL_RELOAD
                , 'rss'=>LANG_PANEL_RSS
		, 'mapasitio'=>LANG_PANEL_SITEMAP
                , 'ayuda'=>LANG_PANEL_HELP
                , 'info'=>LANG_PANEL_INFO
                , 'salir'=>LANG_PANEL_LOGOUT
        ));
        $footer = '';
} else {
        $panel = '';
        $footer = MiSiTiO::generar('footer.html', array('title'=>SITE_TITLE, 'server'=>$_SERVER['SERVER_NAME'], 'date'=>date('Y'), 'by'=>LANG_FOOTER_BY, 'version'=>  file_get_contents(DIR.'/version')));
}

// generar web2
echo MiSiTiO::generar('web2.html', array(
        'panel'=>$panel
        , 'footer'=> $footer
        , 'tiempo'=>num(microtime(true)-INICIO,3)
        , 'memoria'=>num(memory_get_usage(true)/1024)
));

?>
