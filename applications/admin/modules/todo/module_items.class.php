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
		$params['conditions']='type<2 ';
		
		//rodyti paskutini komentara
		$params['select']='*, (SELECT LEFT(description, 100) FROM gw_todo AS aaa WHERE aaa.parent_id=a.id ORDER BY `id` DESC LIMIT 1) AS last_comment, insert_time AS week';
	}
	
	
	public $allowed_order_columns=['last_comment'=>1, 'week'=>1];
	
	function doSwitchState()
	{
		
		$item = $this->getDataObjectById();
		$item->state=$_GET['state'];
		$item->user_exec = $this->app->user->id;
			
		$item->update(Array('state','user_exec'));

		$this->jump();
	}
	

	
	


}