<?php


class Module_Items extends GW_Common_Module_Tree_Data
{	
	
	function viewDefault()
	{
		$this->viewList(Array('conditions'=>'type<2'));
		
	}
	
	
	function doSwitchState()
	{
		
		$item = $this->getDataObjectById();
		$item->state=$_GET['state'];
		$item->user_exec = $this->app->user->id;
			
		$item->update(Array('state','user_exec'));

		$this->jump();
	}
	
	function viewList()
	{
		$list = parent::viewList();
		
		$this->__attachLastComment($list);
	}
	
	function __attachLastComment($list)
	{
		$ids = array_keys($list);
		
		if(!$ids)
			return;
		
		$ids_cond = "parent_id IN (".implode(',', $ids).")";
		
		$comment_list = GW::getInstance('GW_Todo_Item')->findAll("type=2 AND ".$ids_cond,
			[
			    'select'=>'LEFT(description, 100) AS description, parent_id',
			    'order'=>'id DESC',
			    'group_by'=>'parent_id',
			    'key_field'=>'parent_id'
			]);
		
		foreach($comment_list as $pid => $comment)
			$list[$pid]->last_comment = $comment->description;		
	}
	

	
	function viewForm()
	{
		
		parent::viewForm();
		//d::dumpas($this->parent);
	}
	
	function init()
	{
		parent::init();
		
		$this->options['project_id'] = GW::getInstance('gw_todo_project')->getOptions();
		
	}

}