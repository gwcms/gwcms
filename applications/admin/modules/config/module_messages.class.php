<?php


class Module_Messages extends GW_Common_Module
{	
	function init()
	{
		parent::init();
		$this->list_params['paging_enabled']=1;
		
		$this->filters['user_id']=$this->app->user->id;
	}
	
	function viewView()
	{
		$item = parent::viewForm();
		$item->seen=1;
		$item->update(['seen']);
	}
	
	function doInvertSeen()
	{
		if(! $item = $this->getDataObjectById())
			return false;
        
		$this->canBeAccessed($item, true);

		if(!$item->invert('seen')) 
			return $this->setErrors('/GENERAL/ACTION_FAIL'); 
	 	 
		$this->jump(); 		
	}
	
}

