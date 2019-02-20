<?php


class Module_Items extends GW_Common_Module_Tree_Data
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
	

	

}