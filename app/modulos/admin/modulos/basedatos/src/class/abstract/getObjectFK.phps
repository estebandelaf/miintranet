	/**
	 * Recupera un objeto de tipo {fk_class} asociado al objeto Base{class}
	 * Se requiere que ya se haya usado Base{class}->get()
	 * @return {fk_class} Objeto de tipo {fk_class} con datos seteados o null en caso de que no existe la asociación
	 * @author {author}
	 * @version {date}
	 */
	public function get{fk_class} () {
		require(DIR.'/class/db/final/{fk_class}.class.php');
		$obj{fk_class} = new {fk_class}();
		$obj{fk_class}->set(array('{column}'=>$this->{table}_{column}));
		if($obj{fk_class}->exist()) {
			$obj{fk_class}->get();
			return $obj{fk_class};
		}
		return null;
	}

