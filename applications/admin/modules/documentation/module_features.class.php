<?php


class Module_Features extends GW_Common_Module_Tree_Data
{	

	
	function init()
	{	
		parent::init();
		
		$this->app->carry_params['clean']=1;		
		
	}

	
	function viewDefault()
	{
		$this->viewList();
	}
	

	
	function doDelete()
	{
		$do=$this->getDataObjectById();
		$do->set('active', 0);
		$do->update();
		
		$this->jump();
	}
    
	
	function doAjaxSave()
	{
		
		$vals = $_REQUEST['item'];	
		
		$item = $this->model->createNewObject($vals);
		
		if($item->id)
			$item->load();
			
		$item->setValues($vals);
		
		$item->save();
		
		
		exit;
	}
	
	function getFiltersConfig()
	{
		return [
			'text' => 1,
			'time' => 1
		];
	}
	
	
	function doGetTree()
	{
		//$list = $this->model->getFullTree();
		
		$list0 = $this->model->findAll(false, ['select'=>'id, title, active, parent_id, type, priority', 'key_field'=>'id', 'order'=>'priority']);
		
		foreach($list0 as $item){
			$vals=[
			    "text"=>$item->title, //. " ($item->id) ($item->priority)",debug
			    "parent"=>$item->parent_id==-1 ? '#' : $item->parent_id,
			    "id"=>$item->id,
			    //"state" => ["opened" => true, "selected" => true],
			    "type" => 't'.$item->type,
			    "position" => $item->priority
			];
			$list[] = $vals;
		}
		

		header('content-type: text/plain');
		echo json_encode($list, JSON_PRETTY_PRINT);
		exit;
	}
	
	function doRename()
	{
		if (!$item = $this->getDataObjectById())
			return false;

		
		$item->title = $_GET['title'];

		if($item->isChanged()){
			$item->updateChanged();
			$this->setMessage(["text"=>GW::l("/g/SAVE_SUCCESS"), "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}else{
			$this->setMessage(["text"=>GW::l("/g/NO_CHANGES"), "type"=>GW_MSG_INFO, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}

		

		//if($this->isPacketRequest())	
		//	$this->app->addPacket(['action'=>'delete_row','id'=>$item->id, 'context'=>get_class($this->model)]);
		
		if(!$this->sys_call)
			$this->jump();
	}
	
	function doMoveNode()
	{
		if (!$item = $this->getDataObjectById())
			return false;

		$oldparent = $item->parent_id;
		$newparent = (string)($_GET['parent']=='#' ? -1 : $_GET['parent']); // string nes jei tipai nesutaps bus nustatytas pokytis
		
		$item->set('parent_id', $newparent);
			
		if($item->isChanged() || $_GET['old_priority']!=$_GET['priority'] ){
			
			if(isset($item->changed_fields['parent_id'])){
				$item->priority = $_GET['priority'];
				$item->updateChanged();
				$item->fixOrder();
				$this->setMessage(["text"=>"Moved to new parent $oldparent -> {$item->parent_id}", "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
				
			}else{
				$inf = $item->updatePositions($_GET['old_priority'], $_GET['priority']);
				$this->setMessage(["text"=>'Positions updated', "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
			}
		}else{
			$this->setMessage(["text"=>GW::l("/g/NO_CHANGES"), "type"=>GW_MSG_INFO, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}

		

		//if($this->isPacketRequest())	
		//	$this->app->addPacket(['action'=>'delete_row','id'=>$item->id, 'context'=>get_class($this->model)]);
		
		if(!$this->sys_call)
			$this->jump();
	}

	function doCreateNode()
	{
		$item = $this->model->createNewObject();
		$item->parent_id = $_GET['parent'];
		$item->title = "Unnamed";
		$item->priority = 9999999;
		$item->insert();
		
		die(json_encode($item->toArray()));
	}

}