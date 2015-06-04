<?php


class Module_Items extends GW_Common_Module_Tree_Data
{	
	
	function viewDefault()
	{
		$this->viewList(Array('conditions'=>'type<2'));
		
	}
	
	
	function doSwitchState()
	{
		
		$item = $this->getDataObjectById();
		$item->state=$_GET['state'];
		$item->user_exec = $this->app->user->id;
			
		$item->update(Array('state','user_exec'));

		$this->jump();
	}
	

	
	function viewForm()
	{
		
		parent::viewForm();
		//d::dumpas($this->parent);
	}
	
	function init()
	{
		parent::init();
		
		$this->options['project_id'] = GW::getInstance('gw_todo_project')->getOptions();
		
	}

}