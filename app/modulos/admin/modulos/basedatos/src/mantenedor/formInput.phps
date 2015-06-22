	$notNull = in_array('{col}', $arrNotNull);
	$isPK = in_array('{col}', $arrPK);
	if(isset($_GET['edit']) && $isPK) echo Form::text('{col}', $obj{class}->{col});
	else if(isset($_GET['new']) || (isset($_GET['edit']) && !$isPK)) echo Form::input(($notNull?MiSiTiO::generar('form/asterisco.html'):'').'{col}', '{col}', ($obj{class}->{col}===0 || $obj{class}->{col}==='0' || !empty($obj{class}->{col})) ? $obj{class}->{col} : '{default}', '{help}', ($notNull?MiSiTiO::generar('form/classObligatorio.html'):'').' maxlength="{length}"');
