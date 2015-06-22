		if(isset($columnas['{col}'])&&$columnas['{col}']!='') {
			array_push($filtros, {class}s::$bd->like('{col}', $columnas['{col}']));
			array_push($linkWhere, '{col}|'.$columnas['{col}']);
		}
