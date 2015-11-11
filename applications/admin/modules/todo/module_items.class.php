<?php


class Module_Items extends GW_Common_Module_Tree_Data
{	
	

	function init()
	{
		parent::init();
		
		$this->options['project_id'] = GW::getInstance('gw_todo_project')->getOptions();
		$this->options['project'] = GW::getInstance('gw_todo_project')->findAll(null, ['key_field'=>'id']);;
		
	}	
	
	function __eventBeforeListParams(&$params)
	{
		$params['conditions']='type<2 ';
		
		//rodyti paskutini komentara
		$params['select']='*, (SELECT LEFT(description, 100) FROM gw_todo AS aaa WHERE aaa.parent_id=a.id ORDER BY `id` DESC LIMIT 1) AS last_comment, insert_time AS week';
	}
	
	function __eventAfterForm($item)
	{
		//d::dumpas($item->file1);
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
	
	function overrideFilterComments($value)
	{
		$value = GW_DB::escape($value);
		$cond = " (SELECT count(*) FROM `gw_todo` AS aab WHERE aab.parent_id=a.id AND description LIKE '%$value%')>0 ";
		
		
		return $cond;
	}	
	

	
	


}