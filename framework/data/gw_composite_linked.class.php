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
		
		if($params['object'])
			$this->linkedobject = new $params['object'];
		
		$this->params = $params;
		
	}

	public function setOwnerObject($master, $fieldname) {
		
		
	}

	public function getByOwnerObject($master, $fieldname) {
		
		$this->masterobject = $master;
		
		$id = $master->get($this->params['relation_field']);
		$class = $this->params['object'];;
		
		
		//pakraudavo kruvas uzklausu WHERE id=0
		if(!$id){
			$this->linkedobject=$this->createNewObject();
			return $this;
		}

		if(isset(GW_Composite_Data_Object::$linked_cache[$class][$id])){
			//echo "--$class--$id--cached--";
			$this->linkedobject = GW_Composite_Data_Object::$linked_cache[$class][$id];
			
			return $this;
		}
		
		
		if($this->linkedobject && ($tmp=$this->linkedobject->find(["id=?", $id])))
		{
			//echo "--$class--$id--asked--";
			GW_Composite_Data_Object::$linked_cache[$class][$id] = $tmp;
			
			$this->linkedobject = $tmp;	
		}
		
		return $this;
		
	}

	//saugojimas
	public function save() {

		if($this->linkedobject->id && method_exists($this->linkedobject, 'updateChanged')){
			$this->linkedobject->updateChanged();
		}elseif(is_object($this->linkedobject) && method_exists($this->linkedobject, 'save')){
			$this->linkedobject->save();
		}		
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
