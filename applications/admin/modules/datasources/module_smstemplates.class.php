<?php


class Module_SMSTemplates extends GW_Common_Module
{	

	public $admin = false;
	
	function init()
	{			
		
		$this->admin = $this->app->user->isRoot();
		
		//test
		$this->admin=false;
		
		parent::init();
		
		$this->list_params['paging_enabled']=1;

		
		if($this->admin)
			$this->options['user_id'] = GW::getInstance('GW_User')->getOptions(false);
		
		if(!$this->admin)
			$this->filters['user_id'] = $this->app->user->id;
		
		

	}
	
	function doSend()
	{
		$template = $this->getDataObjectById(true);
		
		$this->app->sess['item']['message'] = $template->message;
		
		$this->jump('mis/outgoing/form');
	}

	function canBeAccessed($item, $die=true, $load = true)
	{	
		if($item)
			$item->load_if_not_loaded();
		
		if(!$item)
			return true;
				
		$result = ($item && $item->id == 0) || $this->admin || $item->user_id == $this->app->user->id;
		
		if(!$die || $result)
			return $result;

		$this->setError('/G/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump($this->app->page->path);
	}
	
	
	
	
}
