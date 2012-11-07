<?

class Module_Groups extends GW_Common_Module
{	
	function init()
	{
		$this->model = new GW_ADM_Users_Group();
		
		parent::init();
	}
	
	function canBeAccessed($item, $die=true)
	{
		$result = $item->canBeAccessedByUser(GW::$user);
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump(GW::$request->page->path);
	}

	function viewDefault()
	{
		$this->viewList();
	}
	
	function viewList()
	{
		$list = $this->model->findAll();
		
		if(!GW::$user->isRoot())
			foreach($list as $i => $item)
				if(!$item->canBeAccessedByUser(GW::$user))
					unset($list[$i]);
		
		$this->smarty->assign('list', $list);
	}
	

	
	function viewPermissions()
	{
		if(!$item = $this->getDataObjectById())
			return;		
		
		$page0 = new GW_ADM_Page();
		$list = $page0->findAll('active', Array('order'=>'path'));
		
		//dont show pages which can not be accessed by current user
		if(!GW::$user->isRoot())
		{
			foreach($list as $i => $page)
				if(!$page->canAccess() || $page->get('path')=='users' || $page->get('path')=='users/groups')
					unset($list[$i]);
		}
					
		
		$selected = GW_ADM_Permissions::getByGroupId($item->id);

		
		$this->smarty->assign('item', $item);//group
		$this->smarty->assign('list', $list);//paths
		$this->smarty->assign('selected', $selected);
	}
	
	function doSavePermissions()
	{
		$vals = $_REQUEST['item'];		
		$item = $this->model->createNewObject($vals['id']);
		
		if(!GW::$user->isRoot() && GW::$user->inGroup($vals['id']))
		{
			$this->setErrors('/GENERAL/ACTION_RESTRICTED');
			$this->jump(dirname(GW::$request->path), array('id'=> $item->get('id')));	
		}
		
		//remove permissions which cant set current user
		if(!GW::$user->isRoot())
		{
			unset($vals['paths']['users']);
			unset($vals['paths']['users/groups']);
			
			foreach($vals['paths'] as $path => $x)
				if(!GW_ADM_Permissions::canAccess($path, GW::$user->group_ids))
					unset($vals['paths'][$path]);
		}
			
		$item->load();
		
		GW_ADM_Permissions::save($vals['id'], $vals['paths']);
		
		GW::$request->setMessage(GW::$lang['SAVE_SUCCESS']);
		$this->jumpAfterSave($item);		
	}
	
	
	function doDelete()
	{
		if(! $item = $this->getDataObjectById())
			return false;

		//dont allow remove group which is set to current user
		if(GW::$user->inGroup($item->get('id')))
			return $this->setErrors('/GENERAL/ACTION_RESTRICTED');
			
		parent::doDelete();
	}
		
}

?>
