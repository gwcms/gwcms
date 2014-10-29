<?php


class Module_Items extends GW_Common_Module_Tree_Data
{	
	
	function viewDefault()
	{
		$this->viewList(Array('conditions'=>'type<2'));
		
	}
	
	
	function doExecute()
	{
		
		$item = $this->getDataObjectById();
		$item->state=15;
		$item->user_exec = $this->app->user->id;
			
		$item->update(Array('state','user_exec'));

		$this->jump();
	}
	
	function doComplete()
	{
		$item = $this->getDataObjectById();
		
		if($item->user_exec == $this->app->user->id)
		{
			$item->state=100;
			$item->update(Array('state'));
		}
		$this->jump();
	}

}