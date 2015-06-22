	$notNull = in_array('{col}', $arrNotNull);
	$isPK = in_array('{col}', $arrPK);
	if(isset($_GET['edit']) && $isPK) {
		if(in_array('{col}', $arrFK)) {
			$objFK = $obj{class}->get{fk_class}();
			$text = $objFK->{fk_column}; // FIXME: cambiar atributo por la glosa/descripcion/nombre/etc que corresponda
		} else $text = $obj{class}->{col};
		echo Form::text('{col}', $text);
	} else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::select(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'{col}', '{col}', $obj{fk_class}s->listado(), $obj{class}->{col}, '{help}', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
