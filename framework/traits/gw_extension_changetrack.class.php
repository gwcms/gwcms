<?php

class GW_Extension_ChangeTrack
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
				
				//GW_Change_Track::singleton()->updateMultiple(
				//	['owner_temp_id=? AND owner_type=?', $this->parent->temp_id, $this->parent->ownerkey], 
				//	['owner_id'=>$this->parent->id,'owner_temp_id'=>0]);
				
				//d::dumpas($this->parent->getDB()->last_query);
				
			break;
		
			case 'BEFORE_DELETE':
				$items = GW_Change_Track::singleton()->findAll(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->ownerkey]);
				foreach($items as $item)
				{
					$item->delete();
				}
				
				
			break;
			
			case 'BEFORE_CHANGES':
				$this->trackChangesStart();
				
			break;
		
			case 'AFTER_UPDATE':
				$this->storeChanges();
			
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
				
		$result = GW_Change_Track::singleton()->countGrouped('owner_id', $cond);
		
		foreach($list as $item)		
			$item->extensions['changetrack']->prepare_count = $result[$item->id] ?? 0;
	}
	
	public $prepare_count=null;
	
	function count()
	{
		if($this->prepare_count!==null){
			return $this->prepare_count;
		}
		
		return GW_Change_Track::singleton()->count(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->ownerkey]);
	}
	
	public $track_started=false;
		
	function trackChangesStart()
	{
		$this->parent->copyOriginal();
		$this->track_started = true;
	}
	
		
	function getChanges()
	{
		$changes=[];
		$itm = $this->parent;
		
		
		
		foreach(array_keys($itm->getColumns()) as $field){
			if(!isset($itm->ignored_change_track[$field]) && $itm->_original->get($field) != $itm->get($field))
			{
				$changes[$field]=['new'=>$itm->get($field), 'old'=>$itm->_original->get($field)];
			}
		}
		
		return $changes;
	}

	function storeChanges()
	{
		if(!$this->track_started)
			return false;
			
		$changes = $this->getChanges();
				
		if($changes){
			$new=[];
			$old=[];
			
			foreach($changes as $field => $change)
			{
				$new[$field] = $change['new'];
				$old[$field] = $change['old'];
			}
			
			$itm = GW_Change_Track::singleton()->createNewObject();
			$itm->setValues([
				'owner_type'=>$this->parent->ownerkey,
				'owner_id'=>$this->parent->id,
				'user_id'=>GW::$context->app->user->id ?? -1,
				'new'=>$new,
				'old'=>$old
			]);
			
			$itm->insert();
		}
		
		//resetchanges
		$this->trackChangesStart();
	}

}
