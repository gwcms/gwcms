<?php


class Module_Items extends GW_Common_Module_Tree_Data
{	
	

	function init()
	{
		parent::init();
		
		$this->options['project_id'] = GW::getInstance('gw_todo_project')->getOptions();
		
	}	
	
	function __eventBeforeListParams(&$params)
	{
		$params['conditions']='type<2';
	}
	
	
	function doSwitchState()
	{
		
		$item = $this->getDataObjectById();
		$item->state=$_GET['state'];
		$item->user_exec = $this->app->user->id;
			
		$item->update(Array('state','user_exec'));

		$this->jump();
	}
	
	function __eventAfterList(&$list)
	{				
		$this->__attachLastComment($list);
	}
	
	function __attachLastComment($list)
	{
		$ids = array_keys($list);
		
		if(!$ids)
			return;
				
		$comment_list = GW::getInstance('GW_Todo_Item')->findAll("type=2 AND ".GW_DB::inCondition('parent_id', $ids),
			[
			    'select'=>'LEFT(description, 100) AS description, parent_id',
			    'order'=>'id DESC',
			    'group_by'=>'parent_id',
			    'key_field'=>'parent_id'
			]);
		
		foreach($comment_list as $pid => $comment)
			$list[$pid]->last_comment = $comment->description;		
	}
	
	


}