<?php


class GW_Doc_Type extends GW_Data_Object
{
	public $table = 'gw_doc_types';
	
	public $validators = ['title' => ['gw_string', [ 'required'=>1 ]]];	
	
	public $i18n_fields = Array(
		'title' => 1
	);
		
	
	
	function getOptions($conds)
	{
		//args $type, $conds=null, $lang='lt'
		//$cond = $active ? 'active!=0 AND removed=0' : '';
		//$typecond = GW_DB::prepare_query(['`type`=?', $type]);
		//$conds = $conds ? $conds .' AND '.$typecond : $typecond;
		
		
		return $this->getAssoc(['id','title'], $conds);
	}	
	
	function validate()
	{
		if(!parent::validate())
			return false;		
			
		/*
		$this->set('key', preg_replace('/[^a-z-_0-9]/','_', strtolower($this->get('key')) ));
		
		
		$cond=Array
		(
			'group_id=? AND `key`=? AND id!=?',
			$this->get('group_id'),
			$this->get('key'), 
			(int)$this->get('id')
		);
		
		if($duplicate = $this->find($cond))
			$this->errors['key']='/G/VALIDATION/UNIQUE';
		*/
			
		return !(bool)count($this->errors);
	}

	function getBySlots($slotslist)
	{
		return $this->attachAssocRecs($slotslist, 'type_id', 'GW_Sched_Type');
	}
	

	
}