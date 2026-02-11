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
		$cond=Array
		(
			'`title`=? AND id!=?',
			$this->get('title'), 
			(int)$this->get('id')
		);
		
		if($duplicate = $this->find($cond))
			$this->errors['title']='/G/VALIDATION/UNIQUE';
		
		
		return parent::validate();	
	}


	

	
}