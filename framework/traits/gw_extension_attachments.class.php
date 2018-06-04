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
				
				//d::dumpas([$this->parent->temp_id, $this->parent->id , $this->parent->attachments_owner]);
				
				GW_Attachment::singleton()->updateMultiple(
					['owner_temp_id=? AND owner_type=?', $this->parent->temp_id, $this->parent->attachments_owner], 
					['owner_id'=>$this->parent->id,'owner_temp_id'=>0]);
				
				//d::dumpas($this->parent->getDB()->last_query);
				
			break;
		
			case 'BEFORE_DELETE':
				$attachs = GW_Attachment::singleton()->findAll(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->attachments_owner]);
				foreach($attachs as $attach)
				{
					$attach->delete();
				}
			break;

			
		}
	}
	
	static function prepareList($list)
	{
		$ids = [];
		foreach($list as $item)
			$ids[] = $item->id;
		
		$result = GW_Attachment::singleton()->countGrouped('owner_id', GW_DB::inCondition('owner_id', $ids));
		
		foreach($list as $item)		
			$item->extensions['attachments']->prepare_count = $result[$item->id] ?? 0;
	}
	
	public $prepare_count=null;
	
	function count()
	{
		if($this->prepare_count!==null){
			return $this->prepare_count;
		}
		
		return GW_Attachment::singleton()->count(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->attachments_owner]);
	}

}
