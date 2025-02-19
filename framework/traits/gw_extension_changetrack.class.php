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
	
	
	function prepareCountByField()
	{
		$changes = GW_Change_Track::singleton()->findAll(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->ownerkey]);;
		$change_count = [];
		
		foreach($changes as $change)
		{
			$new = $change->new;
			if($new){
				foreach($new as $field => $val)
					$change_count[$field] = ($change_count[$field] ?? 0) + 1;
			}
			$diff = $change->diff;
			if($diff){
				foreach($diff as $field => $val)
					$change_count[$field] = ($change_count[$field] ?? 0) + 1;
			}			
			
		}
		
		return $change_count;
	}
	
	function getChangesByField($field)
	{
		$changes0 = GW_Change_Track::singleton()->findAll(['owner_id=? AND owner_type=?',$this->parent->id, $this->parent->ownerkey]);;
		$changes=[];
		
		foreach($changes0 as $change)
		{
			$new = $change->new;
			
			if(isset($new->$field)){
				$old = $change->old;
				
				$changes[] = [$change, $new->$field, $old->$field];
			}
			
			$diff = $change->diff;
			
			if(isset($diff->$field))
				$changes[] = [$change, $diff->$field];
					
			
		}
		
		return $changes;		
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
		if($this->track_started)
			return false;
		
		$this->parent->copyOriginal();
		$this->additional_changes = [];
		$this->track_started = true;
	}
	
	public $additional_changes=[];
		
	function getChanges()
	{
		$changes=[];
		$itm = $this->parent;
		
		
		
		//
		
		foreach(array_keys($itm->getColumns()) as $field){
			if($field=='update_time' || isset($itm->ignored_change_track[$field]))
				continue;
			
			if($itm->_original->get($field) == $itm->get($field))
				continue;
			
			$new = $itm->get($field);
			$old = $itm->_original->get($field);
			
			if ($itm->change_track2[$field] ?? false) {
				$changes[$field]=['diff'=> GW_String_Helper::createDiff($new, $old) ];
			}else{
				$changes[$field]=['new'=>$new, 'old'=>$old];
			}
				
			
		}
		
		if($this->additional_changes)
			$changes = array_merge($changes, $this->additional_changes);
		
		
		
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
			$diff=[];
			
			foreach($changes as $field => $change)
			{
				if(isset($change['diff'])){
					$diff[$field] = $change['diff'];
				}else{
					$new[$field] = $change['new'];
					$old[$field] = $change['old'];					
				}
			}
			$keyfields = [
				'owner_type'=>$this->parent->ownerkey,
				'owner_id'=>$this->parent->id,
				'user_id'=>GW::$context->app->user->id ?? -1,			    
			];
			
			$itm = GW_Change_Track::singleton()->createNewObject();
			
			if($last = $itm->find(GW_DB::buidConditions($keyfields+['last'=>1,'undone'=>0]))){
				
				$last->last=false;
				$last->updateChanged();
			}
			//pasalinti po undo likusius veiksmus
			
			$vals = $keyfields+[
				'new'=>$new,
				'old'=>$old,
				'last'=>1
			];
			
			if($diff)
				$vals['diff'] = $diff;
			
			$itm->setValues($vals);
			
			$itm->insert();
		}
		
		//resetchanges
		$this->track_started = false;
		$this->trackChangesStart();
	}
	
	
	function canUndo()
	{
		$keyfields = [
			'owner_type'=>$this->parent->ownerkey,
			'owner_id'=>$this->parent->id,
			'user_id'=>GW::$context->app->user->id ?? -1,			    
		];
			
		return GW_Change_Track::singleton()->createNewObject()->count(GW_DB::buidConditions($keyfields+['undone'=>0]),['order'=>'id DESC']);
	}
	
	function canRedo()
	{
		$keyfields = [
			'owner_type'=>$this->parent->ownerkey,
			'owner_id'=>$this->parent->id,
			'user_id'=>GW::$context->app->user->id ?? -1,			    
		];
			
		$lastchange = GW_Change_Track::singleton()->createNewObject()->find(GW_DB::buidConditions($keyfields),['order'=>'id DESC']);
		return $lastchange ? $lastchange->undone : false;
		
	}
	
	function undo()
	{
		$keyfields = [
			'owner_type'=>$this->parent->ownerkey,
			'owner_id'=>$this->parent->id,
			'user_id'=>GW::$context->app->user->id ?? -1,			    
		];
			
		$itm = GW_Change_Track::singleton()->createNewObject();
			
		$list = $itm->findAll(GW_DB::buidConditions($keyfields),['order'=>'id DESC']);
		
		$last = false;
		
		foreach($list as $idx => $change){
			if(!$last && $change->last){
				$last=$change;
			}elseif($last){
				$nextafterlast = $change;
				break;
			}
		}
		
		if(!$last)
			return false;
		
		$currentval = $last->new;
		$prevval = $last->old;
		
		//todo patikrint ar current val sutampa su $this->parent vertemis
		//d::dumpas($last);
		
		
		$this->parent->setValues($prevval);
		$this->parent->updateChanged();
		
		
		if($nextafterlast){
			$nextafterlast->last = 1;
			$nextafterlast->updateChanged();
		}
		
		$last->undone = 1;
		$last->last = 0;
		$last->updateChanged();
	}
	
	function redo()
	{
		
	}

}
