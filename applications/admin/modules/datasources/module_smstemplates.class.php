<?php


class Module_SMSTemplates extends GW_Common_Module
{	

	public $admin = false;
	
	function init()
	{			
		
		$this->admin = $this->app->user->isRoot();
		
		//test
		$this->admin=true;
		
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

}
