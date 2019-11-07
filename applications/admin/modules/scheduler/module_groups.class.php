<?php


class Module_Groups extends GW_Common_Module
{
	
	public $list_params=['page_by'=>100];	
	public $owner_key = false;
	
	function init()
	{	
		parent::init();
		//$this->filters['active']=1;
		$this->list_params['paging_enabled']=1;
		
		//$this->options['type1'] = GW_Sched_Type::singleton()->getOptions('grouptype1');
		//$this->options['type2'] = GW_Sched_Type::singleton()->getOptions('grouptype2');
		
		$this->app->carry_params=['owner_key'=>1, 'clean'=>1];
		
		if(isset($_GET['owner_key']))
			$this->owner_key = $_GET['owner_key'];
		
		if($this->owner_key)
			$this->filters['owner_key']=$this->owner_key;
		
		
	}


/*	
	function getMoveCondition($item)
	{
		$tmp = $this->filters;
		$tmp['type']=$item->get('type');
		
		return GW_SQL_Helper::condition_str($tmp);
	}
*/	

/*	
	function getListConfig()
	{

		$cfg = array('fields' => [
			'id' => 'Lof', 
			'title' => 'Lof', 
			'insert_time' => 'Lof', 
			'update_time' => 'Lof', 
			'job_type' => 'Lof', 
			'title' => 'Lof', 

			]
		);
		
			
		$users = GW_user::singleton()->getOptions(true, GW_DB::inCondition("id", $this->model->getDistinctVals('user_id') ));
				
		$cfg['filters']['user_id'] = ['type'=>'multiselect','options'=> $users];

			
			
		return $cfg;
	}
*/	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		//dont show at first time
		foreach(['insert_time','update_time','active'] as $field)
			$cfg['fields'][$field] = str_replace('L', 'l', $cfg['fields'][$field]);
		
		if($this->owner_key)
			unset($cfg['fields']['owner_key']);
		
		return $cfg;
	}

	function __eventAfterList($list)
	{	
		$this->attachFieldOptions($list, 'type1', 'GW_Sched_Type');	
		$this->attachFieldOptions($list, 'type2', 'GW_Sched_Type');	
	}	

	function getMoveCondition($item)
	{		
		return GW_SQL_Helper::condition_str([
		    'owner_key'=>$item->owner_key
		]);
	}
	
	function doCopySlots()
	{
		$item = $this->getDataObjectById();
		
		$source_id=(int)$_GET['source'];
		$source = GW_Sched_Group::singleton()->find(['id=?', $source_id]);
		
		if(!$source){
			$this->setError("Bad schedule id");
			$this->jump();
		}
		
		$slots = $source->getSlots();
		$slotscnt = count($slots);
		

		if(isset($_GET['confirm'])){
			foreach($slots as $slot)
			{
				$vals = $slot->toArray();
				unset($vals['id']);
				unset($vals['group_id']);
				unset($vals['insert_time']);
				unset($vals['update_time']);
				
				$vals['group_id'] = $item->id;
				
				$newslot = GW_Sched_Slot::singleton()->createNewObject($vals);
				$newslot->insert();
			}
			
			$this->setMessage("Nukopijuota: ".$slotscnt);
		}else{
				$accepturl = $this->app->buildUri(false,[
					'act'=>'doCopySlots',
					'id'=>$item->id, 
					'source'=>$source->id, 
					'confirm'=>1
				    ]+$_GET
				);
				
				$acceptbtn = "<a class='btn btn-primary' href='".$accepturl."' style='margin:10px'>Patvirtinu</a>";
				$this->setMessage("Kopijuoti iš \"<b>{$source->title}</b>\" į \"<b>{$item->title}</b>\". Rasta kopijuotinų eilučių: <b>$slotscnt</b>.  $acceptbtn");				
		}		
		
		$this->jump();
	}
	

}
