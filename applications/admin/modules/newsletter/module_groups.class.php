<?php


class Module_Groups extends GW_Common_Module
{	

	public $default_view='viewList';

		
	function __eventAfterList(&$list)
	{
		#attach counts
		$counts = $this->model->getCountsByIds(array_keys($list));
		
		foreach($list as $id => $item)
			$item->subscribers_count = isset($counts[$id]) ? $counts[$id] : 0;
		
		
	}
}
