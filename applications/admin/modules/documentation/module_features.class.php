<?php


class Module_Features extends GW_Common_Module_Tree_Data
{	

	
	function init()
	{	
		parent::init();
		
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
			    "text"=>$item->title,
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

		
		$item->parent_id = $_GET['parent']=='#' ? -1 : $_GET['parent'];
		$item->priority = $_GET['priority'];
			
		if($item->isChanged()){
			

			
			$item->updateChanged();
			
			if(isset($item->changed_fields['priority'])){
				//$item->fixOrder();
				//d::dumpas($item->getDB()->last_query);
			}			
			
			
			$this->setMessage(["text"=>GW::l("/g/SAVE_SUCCESS"), "type"=>GW_MSG_SUCC, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}else{
			$this->setMessage(["text"=>GW::l("/g/NO_CHANGES"), "type"=>GW_MSG_INFO, "title"=>$item->title, "obj_id"=>$item->id,'float'=>1]);
		}

		

		//if($this->isPacketRequest())	
		//	$this->app->addPacket(['action'=>'delete_row','id'=>$item->id, 'context'=>get_class($this->model)]);
		
		if(!$this->sys_call)
			$this->jump();
	}	

}