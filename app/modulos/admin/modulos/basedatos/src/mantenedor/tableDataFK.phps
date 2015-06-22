		// agregar {column} a la fila
		if($obj{class}->{column}!='') {
			$objFK = $obj{class}->get{fk_class}();
			$glosaFK = $objFK->{fk_column}; // FIXME: cambiar atributo por la glosa/descripcion/nombre/etc que corresponda
		} else $glosaFK = '';
		array_push($fila, $glosaFK);
