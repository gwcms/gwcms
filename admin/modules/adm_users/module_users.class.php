<?php


class Module_Users extends GW_Common_Module
{	
	function init()
	{
		$this->model = new GW_ADM_User();
		$this->group0 = new GW_ADM_Users_Group();
		
		parent::init();
	}

	function canBeAccessed($item, $die=true, $load = true)
	{	
		$item->load_if_not_loaded();
		
		$result = $item->canBeAccessedByUser(GW::$user);
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump(GW::$request->page->path);
	}
	
	function viewDefault()
	{
		$this->assignGroupOptions();		
		$this->viewList();
	}
	
	function viewList()
	{
		$list = $this->model->findAll('! removed');	
		
		if(!GW::$user->isRoot())
			foreach($list as $i => $item)
			{
				if(!$item->canBeAccessedByUser(GW::$user) || $item->get('id') == GW::$user->get('id'))
					unset($list[$i]);
			}
		
		$this->smarty->assign('list', $list);
	}	
	
	function assignGroupOptions()
	{
		$options = $this->group0->getAssoc(Array('id','title'));
		
		if(!GW::$user->isRoot())
			unset($options[$this->group0->root_group_id]);
		
		$this->smarty->assign('groups_options', $options);		
	}
	
	function viewForm()
	{
		$this->assignGroupOptions();
		
		$item = parent::viewForm();
		
		if($item->id)
			$this->smarty->assign('group_options_selected', $item->get('link_groups')->getBinds());
		
	}
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		//do not allow assign to root group if user is not in root group
		if(!GW::$user->isRoot())
			$index = array_search($this->group0->root_group_id, (array)$vals['link_groups']);
			if($index!==false)
				unset($vals['link_groups'][$index]);
				
		$item = $this->model->createNewObject();
		
		if(!(int)$vals['id']){ // if insert	
			$item->setValidators('insert');
			//$vals['user_id']=GW::$user->get('id');
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
		
		GW::$request->setMessage(GW::$lang['SAVE_SUCCESS']);
		
		$this->jumpAfterSave($item);
	}
		
	function doDelete()
	{	
		if(!$item = $this->getDataObjectById())
			return;
			
		$this->canBeAccessed($item, true);	
			
		if($item->get('id') == GW::$user->get('id'))
			return $this->setErrors($this->lang['ERR_DELETE_SELF']);	
			

		$item->delete();
		GW::$request->setMessage(GW::$lang['ITEM_REMOVE_SUCCESS']);
		
		$this->jump();
	}
	
	function doInvertActive()
	{
		if(!$item = $this->getDataObjectById())
			return;

		if($item->get('id') == GW::$user->get('id'))
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
		
		
		GW::$auth->switchUser($item->id);
		
		$this->jump();
	}
	
	function doSwitchUserReturn()
	{
		GW::$auth->switchUserReturn();
		$this->jump();
	}
		
}

?>
