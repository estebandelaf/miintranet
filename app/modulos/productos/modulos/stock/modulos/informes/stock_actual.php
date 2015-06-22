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

require('../../../../../../inc/web1.inc.php');

require(DIR.'/class/db/final/Producto_categoria.class.php');
require(DIR.'/class/db/final/Producto.class.php');
require(DIR.'/class/db/final/Sucursal.class.php');
require(DIR.'/class/db/final/Bodega.class.php');
require(DIR.'/class/db/final/Area.class.php');

$objProducto_categorias = new Producto_categorias();
$objProductos = new Productos();
$objSucursals = new Sucursals();
$objBodegas = new Bodegas();
$objAreas = new Areas();

// determinar campos
if(isset($_GET['campos'])) {
	$filtros = extraerCampos($_GET['campos']);
}

echo MiSiTiO::generar('titulo.html', array('title'=>'Stock actual'));

// mostrar formulario
echo Form::bForm(AKI, 'enviarPorUrl');
echo Form::select('Categoría producto', 'producto_categoria_id', $objProducto_categorias->listado(), !empty($filtros['producto_categoria_id'])?$filtros['producto_categoria_id']:'', 'Categoría producto y producto son mutuamente excluyentes', 'onchange="disableField(\'producto_id\', this.value)"'.(!empty($filtros['producto_id'])?' disabled="disabled"':''));
echo Form::select('Producto', 'producto_id', $objProductos->listado(), !empty($filtros['producto_id'])?$filtros['producto_id']:'', 'Categoría producto y producto son mutuamente excluyentes', 'onchange="disableField(\'producto_categoria_id\', this.value)"'.(!empty($filtros['producto_categoria_id'])?' disabled="disabled"':''));
echo Form::select('Sucursal', 'sucursal_id', $objSucursals->listado(), !empty($filtros['sucursal_id'])?$filtros['sucursal_id']:'', 'Sucursal y bodega son mutuamente excluyentes', 'onchange="disableField(\'bodega_id\', this.value)"'.(!empty($filtros['bodega_id'])?' disabled="disabled"':''));
echo Form::select('Bodega', 'bodega_id', $objBodegas->listado(), !empty($filtros['bodega_id'])?$filtros['bodega_id']:'', 'Sucursal y bodega son mutuamente excluyentes', 'onchange="disableField(\'sucursal_id\', this.value)"'.(!empty($filtros['sucursal_id'])?' disabled="disabled"':''));
echo Form::select('Área', 'area_id', $objAreas->listado(), !empty($filtros['area_id'])?$filtros['area_id']:'');
echo Form::submitButton();
echo Form::eForm();

// consultar stock
if(isset($_GET['campos'])) {
	
	// variable que guardara el stock para mostrarlo en una tabla posteriormente
	$stock = array(array('Productos'));
	
	// crear filtros
	$filtroProducto = !empty($filtros['producto_id']) ? "AND p.id='".$bd->proteger($filtros['producto_id'])."'" : '';
	$filtroBodega = !empty($filtros['bodega_id']) ? "AND b.id='".$bd->proteger($filtros['bodega_id'])."'" : '';
	$filtroArea = !empty($filtros['area_id']) ? "AND a.id='".$bd->proteger($filtros['area_id'])."'" : '';
	if(!empty($filtros['producto_categoria_id'])) {
		$familia = $objProducto_categorias->getFamilia($filtros['producto_categoria_id']);
		$filtroProducto_categoria = 'AND c.id IN ('.implode(',', $familia).')';
	} else $filtroProducto_categoria = '';
	$filtroSucursal = !empty($filtros['sucursal_id']) ? "AND b.sucursal_id = '".$bd->proteger($filtros['sucursal_id'])."'" : '';
	
	// buscar productos que tienen stock
	// en esta query deberan ir los filtros respectivos que vengan desde el formulario (excepto nivel para el nivel)
	$productos = $bd->getTabla("
		SELECT DISTINCT c.glosa AS categoria, p.id AS producto_id, p.nombre AS producto
		FROM producto AS p, producto_categoria AS c, stock AS s
		WHERE c.id = p.producto_categoria_id AND s.producto_id = p.id $filtroProducto $filtroProducto_categoria
		ORDER BY c.glosa, p.nombre
	");
	
	// procesar productos que tienen stock
	$categoriaAnterior = '';
	$fila = 1;
	foreach($productos as &$producto) {
		// colocar categoria
		if($producto['categoria']!=$categoriaAnterior) {
			array_push($stock, array('<div class="categoria">'.$producto['categoria'].'</div>'));
			$fila++;
		}
		$categoriaAnterior = $producto['categoria'];
		// colocar producto
		array_push($stock, array($producto['producto']));
		// colocar numero de fila para el stock (esto es necesario porque van las categorias tb en las filas)
		$producto['fila'] = $fila++;
		// limpiar arreglo de categoria y producto
		unset($producto['categoria'], $producto['producto']);
	}
	
	// buscar sucursales, bodegas y areas
	$bodegas = $bd->getTabla("
		SELECT DISTINCT sucursal.glosa AS sucursal, b.glosa AS bodega, s.bodega_id, a.glosa AS area, s.area_id
		FROM bodega AS b, area AS a, stock AS s, sucursal
		WHERE b.id = s.bodega_id AND a.id = s.area_id AND sucursal.id = b.sucursal_id $filtroBodega $filtroArea $filtroSucursal
		ORDER BY sucursal, bodega, area		
	");
	foreach($bodegas as &$bodega) {
		array_push($stock[0], $bodega['sucursal'].'<br />'.$bodega['bodega'].'<br />'.$bodega['area']);
		unset($bodega['sucursal'], $bodega['bodega'], $bodega['area']);
	}
	
	// buscar stock
	require(DIR.'/class/db/final/Stock.class.php');
	foreach($productos as &$producto) {
		foreach($bodegas as &$bodega) {
			// obtener stock
			$objStock = new Stock();
			$objStock->set(array(
				'producto_id'=>$producto['producto_id'],
				'bodega_id'=>$bodega['bodega_id'],
				'area_id'=>$bodega['area_id']
			));
			if($objStock->exist()) {
				$objStock->get();
				$txt = '<div class="'.$objStock->getNivel().'">'.$objStock->nivel.'</div>';
			} else $txt = '';
			// agregar a la celda respectiva
			array_push($stock[$producto['fila']], $txt);
		}
	}
	
	// mostrar stock en tabla
	echo '<div id="stock">',"\n";
	echo Tabla::generar($stock);
	echo '</div>',"\n";

	// mover la página hacia el stock
	echo '<script type="text/javascript">$().ready(function(){document.location.href="#stock";});</script>',"\n";
	
	// mostrar redireccionar
	if(STOCK_REFRESH) {
		echo '<script type="text/javascript">$().ready(function(){redirect("'.AKI.'", '.STOCK_REFRESH.');});</script>',"\n";
		echo '<div class="center">Stock se actualizará en <span id="seconds"></span></div>',"\n";
	}
	
	// mostrar significado de colores
	echo MiSiTiO::generar('colores.html');
}

require(DIR.'/inc/web2.inc.php');

?>
