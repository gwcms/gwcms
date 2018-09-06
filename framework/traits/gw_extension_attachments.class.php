<?php

class GW_Extension_Attachments
{
	private $parent;
	
	function __construct($parent, $name)
	{
		$this->parent = $parent;
		$parent->registerObserver(['extension', $name]);
	}
	
	function eventHandler($event, &$context_data = [])
	{			
		//d::ldump($event);
		
		switch ($event) {

			case 'AFTER_INSERT':
				
				//d::dumpas([$this->parent->temp_id, $this->parent->id , $this->parent->ownerkey]);
				if((int)$this->parent->temp_id)
					GW_Attachment::singleton()->updateMultiple(
						['owner_temp_id=? AND owner_type=?', $this->parent->temp_id, $this->parent->ownerkey], 
						['owner_id'=>$this->parent->id,'owner_temp_id'=>0]);
				
				//d::dumpas($this->parent->getDB()->last_query);
				
			break;
		
			case 'BEFORE_DELETE':
				$attachs = GW_Attachment::singleton()->findAll(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->ownerkey]);
				foreach($attachs as $attach)
				{
					$attach->delete();
				}
			break;

			
		}
	}
	
	
	//example use
	/*
	function __eventAfterList($list)
	{
		//to get first item
		foreach($list as $item)
			break;

		if($item)
			if($item->extensions['attachments'])
				$item->extensions['attachments']->prepareList($list);
	}
	 */
	
	function prepareList($list)
	{
		$ids = [];
		foreach($list as $item)
			$ids[] = $item->id;
		
		$cond = GW_DB::inCondition('owner_id', $ids).' AND '.GW_DB::prepare_query(['owner_type=?', $this->parent->ownerkey]);
				
		$result = GW_Attachment::singleton()->countGrouped('owner_id', $cond);
		
		foreach($list as $item)		
			$item->extensions['attachments']->prepare_count = $result[$item->id] ?? 0;
	}
	
	public $prepare_count=null;
	
	function count()
	{
		if($this->prepare_count!==null){
			return $this->prepare_count;
		}
		
		return GW_Attachment::singleton()->count(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->ownerkey]);
	}
	
	function getByTitle($title, $ln='lt')
	{
		return GW_Attachment::singleton()->find(["owner_id=? AND owner_type=? AND title_{$ln}",$this->parent->id, $this->parent->ownerkey, $title]);
	}
	


}
