<?php


class Module_Usr extends GW_Common_Module
{	
	function init()
	{
		$this->model = new GW_User();
		$this->group0 = new GW_Users_Group();
		
		
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
		
		$result = $item->canBeAccessedByUser($this->app->user);
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump($this->app->page->path);
	}
	
	
	function viewList()
	{		
		$list = $this->model->findAll('removed=0');	
		
		if(!$this->app->user->isRoot())
			foreach($list as $i => $item)
			{
				if(!$item->canBeAccessedByUser($this->app->user) || $item->get('id') == $this->app->user->get('id'))
					unset($list[$i]);
			}
		
		return ['list'=>$list];
	}	

	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		//do not allow assign to root group if user is not in root group
		if(!$this->app->user->isRoot())
			$index = array_search($this->group0->root_group_id, (array)$vals['link_groups']);
			if(isset($index) && $index!==false)
				unset($vals['link_groups'][$index]);
				
		$item = $this->model->createNewObject();
		
		if(!(int)$vals['id']){ // if insert	
			$item->setValidators('insert');
			//$vals['user_id']=$this->app->user->get('id');
		}else{ //if update
			$item->setValidators('update');
		}
		
		$item->setValues($vals);
		$this->canBeAccessed($item, true);
		$item->setValues($vals);
		
		if(!$item->validate())
		{
			$this->setErrors($item->errors);
			$this->processView('form');
			exit;
		}
		
		$item->setValidators(false); //remove validators
		$item->save();
		
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
		
		$this->jumpAfterSave($item);
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
	
	function doSwitchUserReturn()
	{
		$this->app->auth->switchUserReturn();
		$this->jump();
	}
		
}

?>
