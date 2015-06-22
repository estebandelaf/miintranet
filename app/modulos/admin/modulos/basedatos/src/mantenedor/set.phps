	if(!isset($arrSet['{col}'])) $arrSet['{col}'] = (in_array('{col}', $arrFK)&&empty($_POST['{col}'])) ? null : $_POST['{col}'];
