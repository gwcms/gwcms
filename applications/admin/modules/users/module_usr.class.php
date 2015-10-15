<?php


class Module_Usr extends GW_Common_Module
{	
	function init()
	{
		$this->model = new GW_User();
		$this->group0 = new GW_Users_Group();
				
		$this->rootadmin = $this->app->user->isRoot();
		
		if(!$this->rootadmin){
			$this->filters['parent_user_id'] = $this->app->user->id;
		}
		
		$this->options['parent_user_id'] = GW::getInstance('GW_User')->getOptions(false);
		
		$this->__initGroupOptions();
		parent::init();
	}

	function __initGroupOptions()
	{
		$options = $this->group0->getAssoc(Array('id','title'));
		
		if(!$this->app->user->isRoot())
			unset($options[$this->group0->root_group_id]);
		
		$this->options['group_ids'] =$options;
	}
		

	function canBeAccessed($item, $die=true, $load = true)
	{	
		$item->load_if_not_loaded();
		
		$result = ($this->rootadmin) || ($item->parent_user_id == $this->app->user->id);
		
		
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/G/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump($this->app->page->path);
	}
	
	
	
	function doDelete()
	{	
		if(!$item = $this->getDataObjectById())
			return;
			
		$this->canBeAccessed($item, true);	
			
		if($item->get('id') == $this->app->user->get('id'))
			return $this->setErrors($this->lang['ERR_DELETE_SELF']);	
			

		$item->delete();
		$this->app->setMessage($this->app->lang['ITEM_REMOVE_SUCCESS']);
		
		$this->jump();
	}
	
	function doInvertActive()
	{
		if(!$item = $this->getDataObjectById())
			return;

		if($item->get('id') == $this->app->user->get('id'))
			return $this->setErrors($this->lang['ERR_DEACTIVATE_SELF']);
			
		parent::doInvertActive();
	}
	
	
	function doSwitchUser()
	{
		if(!$item = $this->getDataObjectById())
			return;
		
		$this->canBeAccessed($item, true);	
		
		// jei ne root tai neleisti pasikeisti i root
		
		// jei admin grupej iseiti
		
		
		$this->app->auth->switchUser($item->id);
		
		$this->jump();
	}
	

	
	
	function viewMessage()
	{
		//GW_Message//
		$user = $this->getDataObjectById();
				
		$this->tpl_vars['user']=$user;
		
	}
	
	function doMessage()
	{
	
		$vals = $_REQUEST['item'];
		
		GW::getInstance('GW_Message')->msg($vals['user_id'], $vals['subject'], $vals['message'], $this->app->user->id);
		
		$this->app->setMessage($this->lang['SENT']);
		
		$this->jumpAfterSave();
	}
		
}

?>
