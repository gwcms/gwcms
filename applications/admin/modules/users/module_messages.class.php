<?php


class Module_Messages extends GW_Common_Module
{	
	function init()
	{
		if(!$this->app->user)
			die('no access');
		
		parent::init();
		$this->list_params['paging_enabled']=1;
		
		$this->filters['user_id']=$this->app->user->id;
		
		
		
		$this->admin = $this->app->user->isRoot();
		
		$other_cond =  !$this->admin ? 'id='.(int)$this->app->user->parent_user_id : '';
		
		$this->options['user_id'] = GW::getInstance('GW_User')->getOptions(false, $other_cond);
	}
	
	function viewView()
	{
		$item = $this->getDataObjectById();
		$item->seen=1;
		$item->update(['seen']);
		
		return ['item'=>$item, 'sender'=>GW::getInstance('GW_User')->find(['id=?',$item->sender])];
	}
	
	function doSetSeen()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		$item->saveValues(['seen'=>1]);
		
		$this->jump(implode('/',$this->module_path).'/new');
	}
	
	function viewNew()
	{
		
		$messages=$this->model->findAll(['user_id=? AND seen=0',$this->app->user->id],['order'=>'insert_time ASC','limit'=>'1']);
		
		if(!$messages)
			return false;
		
		$item=array_shift($messages);
		//$item->saveValues(['seen'=>1]);
		
		
		$this->tpl_vars['item'] = $item;
		$this->tpl_vars['sender']=GW::getInstance('GW_User')->find(['id=?',$item->sender]);
		//$this->tpl_vars['more_messages'] = count($messages);
	}
	
	function doInvertSeen()
	{
		if(! $item = $this->getDataObjectById())
			return false;
        


		if(!$item->invert('seen')) 
			return $this->setErrors('/G/GENERAL/ACTION_FAIL'); 
	 	 
		$this->jump(); 		
	}
	
	
	function canBeAccessed($item, $die=true, $load = true)
	{	
		$item->load_if_not_loaded();
				
		$result = $item->user_id == $this->app->user->id || $item->sender == $this->app->user->id;
		
		
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/G/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump($this->app->page->path);
	}	
	
	
	function __eventBeforeSave0($context)
	{
		
		$item=$context;
		$item->sender = $this->app->user->id;
		
	}
	
}

