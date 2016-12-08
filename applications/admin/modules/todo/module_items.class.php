<?php

class Module_Items extends GW_Common_Module_Tree_Data
{

	public $order_enabled_fields = [
		'id', 'title', 'priority', 'state', 'description', 'time_have', 'insert_time', 'update_time'
	];

	function init()
	{
		parent::init();

		$this->options['project_id'] = GW_Todo_Project::singleton()->getOptions('active=1');
		$this->options['project'] = GW::getInstance('gw_todo_project')->findAll(null, ['key_field' => 'id']);	
		
		$this->options['users'] = GW_User::singleton()->getOptions(true, 'is_admin=1');
		
	}

	function __eventBeforeListParams(&$params)
	{
		$params['conditions'] = 'type<2 ';

		//rodyti paskutini komentara
		$params['select'] = '*, (SELECT LEFT(description, 100) FROM gw_todo AS aaa WHERE aaa.parent_id=a.id ORDER BY `id` DESC LIMIT 1) AS last_comment, insert_time AS week';
	}

	function __eventAfterForm($item)
	{
		//d::dumpas($item->file1);
	}

	function doSwitchState()
	{

		$item = $this->getDataObjectById();
		$item->state = $_GET['state'];
		$item->user_exec = $this->app->user->id;

		$item->update(Array('state', 'user_exec'));

		$this->setMessage(sprintf(GW::l('/m/ITEM_STATUS_CHANGED'), $item->id));

		$this->jump();
	}

	function overrideFilterComments($value)
	{
		$value = GW_DB::escape($value);
		$cond = " (SELECT count(*) FROM `gw_todo` AS aab WHERE aab.parent_id=a.id AND description LIKE '%$value%')>0 ";


		return $cond;
	}
	
	function overrideFilterWeek($value, $comparetype)
	{
		//$value = (int)$value;
		
		return $this->buildCond('WEEK(`insert_time`)', $comparetype, (int)$value, false, false);
	}

	
	
	
	function getListConfig()
	{

		$cfg = array('fields' => [
			'id' => 'Lof', 
			'project_id' => 'Lof', 
			'user_create' => 'Lof', 
			'user_exec' => 'Lof', 
			'job_type' => 'Lof', 
			'title' => 'Lof', 
			'description' => 'Lof', 
			'state' => 'Lof', 
			'priority' => 'Lof', 
			'deadline' => 'Lof', 
			'time_have' => 'Lof', 
			'insert_time' => 'Lof', 
			'update_time' => 'Lof',
			'last_comment' =>'Lo',
			'comments'=>'f',
			'info'=>'L',
			'week'=>'lf'
			]
		);
		
		$cfg['filters']['project_id'] = ['type'=>'multiselect','options'=>$this->options['project_id']];
		
				
		$exec_users = GW_user::singleton()->getOptions(true, GW_DB::inCondition("id", $this->model->getDistinctVals('user_exec') ));
		$create_users= GW_user::singleton()->getOptions(true, GW_DB::inCondition("id", $this->model->getDistinctVals('user_create') ));
				
		$cfg['filters']['user_exec'] = ['type'=>'multiselect','options'=> $exec_users];
		$cfg['filters']['user_create'] = ['type'=>'multiselect','options'=> $create_users];
			
			
		return $cfg;
	}
	
	

}



