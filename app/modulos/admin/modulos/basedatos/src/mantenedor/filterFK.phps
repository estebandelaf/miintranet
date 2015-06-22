		if(isset($columnas['{col}'])&&$columnas['{col}']!='') {
			array_push($filtros, "{col} = '".{class}s::$bd->proteger($columnas['{col}'])."'");
			array_push($linkWhere, '{col}|'.$columnas['{col}']);
		}
