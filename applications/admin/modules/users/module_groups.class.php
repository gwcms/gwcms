<?php

class Module_Groups extends GW_Common_Module
{	
	function init()
	{
		$this->model = new GW_Users_Group();
		
		parent::init();
	}
	
	function canBeAccessed($item, $die=true)
	{
		$result = $item->canBeAccessedByUser($this->app->user);
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump($this->app->page->path);
	}

	function viewDefault()
	{
		$this->viewList();
	}
	
	function viewList()
	{
		$list = $this->model->findAll();
		
		if(!$this->app->user->isRoot())
			foreach($list as $i => $item)
				if(!$item->canBeAccessedByUser($this->app->user))
					unset($list[$i]);
		
		return ['list'=>$list];
	}
	

	
	function viewPermissions()
	{
		if(!$item = $this->getDataObjectById())
			return;	
		
		$page0 = new GW_ADM_Page();
		$list = $page0->findAll('active', Array('order'=>'path'));
		
		//dont show pages which can not be accessed by current user
		if(!$this->app->user->isRoot())
		{
			foreach($list as $i => $page)
				if(!$page->canAccess() || $page->get('path')=='users' || $page->get('path')=='users/groups')
					unset($list[$i]);
		}
					
		$selected = GW_Permissions::getByGroupId($item->id);

		return compact(['item','list', 'selected']);
	}
	
	function doSavePermissions()
	{
		$vals = $_REQUEST['item'];		
		$item = $this->model->createNewObject($vals['id']);
		
		if(!$this->app->user->isRoot() && $this->app->user->inGroup($vals['id']))
		{
			$this->setErrors('/GENERAL/ACTION_RESTRICTED');
			$this->jump(dirname($this->app->path), array('id'=> $item->get('id')));	
		}
		
		//remove permissions which cant set current user
		if(!$this->app->user->isRoot())
		{
			unset($vals['paths']['users']);
			unset($vals['paths']['users/groups']);
			
			foreach($vals['paths'] as $path => $x)
				if(!GW_Permissions::canAccess($path, $this->app->user->group_ids))
					unset($vals['paths'][$path]);
		}
			
		$item->load();
		
		GW_Permissions::save($vals['id'], $vals['paths']);
		
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
		$this->jumpAfterSave($item);		
	}
	
	
	function doDelete()
	{
		if(! $item = $this->getDataObjectById())
			return false;

		//dont allow remove group which is set to current user
		if($this->app->user->inGroup($item->get('id')))
			return $this->setErrors('/GENERAL/ACTION_RESTRICTED');
			
		parent::doDelete();
	}
		
}

?>
