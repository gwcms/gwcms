<?php

//tikslas sukurti sasaja kuri atliktu visus veiksmus su susijusiu objektu taip tarsi jis pagrindinis butu
//1 duomenu uzsetinimas
//2 duomenu paemimas
//3 validacija
//4 saugojimas
//5 visi eventai

class GW_Composite_Linked extends GW_Data_Object implements GW_Composite_Slave {

	protected $params;
	protected $linkedobject;
	protected $masterobject;
	
	//paruosimas slave domenu operacijos
	public function setParams($params) {
		
		$this->linkedobject = new $params['object'];
		$this->params = $params;
		
	}

	public function setOwnerObject($master, $fieldname) {
		
		
	}

	public function getByOwnerObject($master, $fieldname) {
		
		$this->masterobject = $master;
		
		
		if($tmp=$this->linkedobject->find(["id=?", $master->get($this->params['relation_field'])]))
		{
			$this->linkedobject = $tmp;	
		}
		
		return $this;
		
	}

	//saugojimas
	public function save() {

		$this->linkedobject->save();
		
	}

	//pasalinimas
	public function deleteComposite($id='*') {
		
	}

	//duomenu paemimas
	public function getValue() 
	{

		return $this->linkedobject;
		
	
	}

}
