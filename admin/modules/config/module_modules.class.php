<?


class Module_Modules extends GW_Common_Module
{

	function init()
	{
		$this->model = new GW_ADM_Page();//nebutinas uzloadina per lang faila jei nurodyta
		parent::init();
	}
	
	function viewDefault()
	{	
		$list = $this->model->getChilds(Array('menu'=>false));
		$list1=Array();
		
		foreach($list as $item)
		{
			$list1[]=$item;
			$childs=$item->findAll("path LIKE '$item->path/%'");
			
			$list1=array_merge($list1, $childs);
		}
		
		$this->smarty->assign('list', $list1);	
	}
	
	function doMove($params=false)
	{
		if(! ($item = $this->getDataObjectById()))
			return $this->jump();
		
		$item->move($_REQUEST['where'], "parent_id=".(int)$item->get('parent_id'));
		
		$this->jump(false, Array('id'=>$item->get('id')));
	}
	
	function doGetNotes()
	{
		
		$item = $this->model->getByPath($_REQUEST['path']);
		
		$this->canBeAccessed($item, true);
		
		echo $item->notes;
		exit;
	}	
	
}

?>
