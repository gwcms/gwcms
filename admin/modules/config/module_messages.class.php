<?


class Module_Messages extends GW_Common_Module
{	

	function init()
	{
		parent::init();
		$this->list_params['paging_enabled']=1;
		
		$this->filters['user_id']=GW::$user->id;
	}

	
	function viewDefault()
	{
		$this->viewList(Array('order'=>$this->model->default_order));
	}
	
	function viewView()
	{
		$item = parent::viewForm();
		$item->seen=1;
		$item->update(Array('seen'));
	}
	
}

