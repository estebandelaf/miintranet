	$notNull = in_array('{col}', $arrNotNull);
	$isPK = in_array('{col}', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('{col}', $obj{class}->{col});
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::inputDate(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'{col}', '{col}', !empty($obj{class}->{col}) ? $obj{class}->{col} : '{default}', true, '{help}', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):''));
