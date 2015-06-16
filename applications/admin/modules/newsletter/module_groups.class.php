<?php


class Module_Groups extends GW_Common_Module
{	

	function init()
	{
		$this->model = new GW_NL_Groups();
		
		parent::init();
	}

	
	function viewDefault()
	{
		$list=$this->viewList();
		
		
		#attach counts
		$counts = $this->model->getCountsByIds(array_keys($list));
		foreach($list as $id => $item)
			$item->subscribers_count = isset($counts[$id]) ? $counts[$id] : 0;
		
		
	}
}
