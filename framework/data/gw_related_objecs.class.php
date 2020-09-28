<?php

//tikslas sukurti sasaja kuri atliktu visus veiksmus su susijusiu objektu taip tarsi jis pagrindinis butu
//1 duomenu uzsetinimas
//2 duomenu paemimas
//3 validacija
//4 saugojimas
//5 visi eventai

class GW_Related_Objecs extends GW_Data_Object implements GW_Composite_Slave {

	protected $params;
	protected $linkedobjects;
	protected $masterobject;
	static $cache_related;
	
	//paruosimas slave domenu operacijos
	public function setParams($params) {
		
		
		$this->params = $params;
		
	}

	public function setOwnerObject($master, $fieldname) {
		
		
	}

	
	//$cached=[5,6,12]; $request=[5,15,12,20]; $fetchfromdb=array_diff($request, $cached); print_r($fetchfromdb);
	
	
	public function getByOwnerObject($master, $fieldname) {
		
		$this->masterobject = $master;
		
		return $this;
	}

	//saugojimas
	public function save() {

		d::dumpas('why save?');
		
	}

	//pasalinimas
	public function deleteComposite($id='*') {
		
	}
	
	
	//nc - no cache
	
	public function getValueNC()
	{
		$class = $this->params['object'];
		
		$owner_field = $this->params['relation_field'];
		
		$opts = $this->params['opts'] ?? false;
		$conds = GW_DB::prepare_query([GW_DB::escapeField($owner_field).' = ?', $this->masterobject->id]);
		$extra_conds = $this->params['conds'] ?? false;
		
		if($extra_conds)
			$conds = GW_DB::buidConditions($conds, $extra_conds);
		


		return $class::singleton()->findAll($conds, $opts);
	}

	//duomenu paemimas
	public function getValue() 
	{		
		$masterclass = get_class($this->masterobject);
		$id = $this->masterobject->id;
		
		if(isset(self::$cache_related[$masterclass][$this->params['object']][$id])){
			
			return self::$cache_related[$masterclass][$this->params['object']][$id];
		}else{
			$list  = $this->getValueNC();
			
			self::$cache_related[$masterclass][$this->params['object']][$id] = $list;
			return $list;
		}
	}

}
